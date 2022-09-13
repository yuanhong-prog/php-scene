<?php
/**
 * redis + rabbitmq 实现分布式锁, 防止超卖
 *
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2019/12/3
 * Time: 2:57 PM
 */

require_once '../vendor/autoload.php';
require_once '../utils/common.php';

use PhpAmqpLib\Message\AMQPMessage;

$start_time = microtime(true);

// 模拟下单的sku_id = 1
$sku_id = 1;

// redis client
try {
    $redis = get_redis();
} catch (RedisException $e) {
    exit('redis 连接失败: ' . $e->getMessage());
}

// 分布式锁 redis key
$lock_key  = 'LOCK_SKU_' . $sku_id;
// 存储库存值的redis key
$stock_key = 'SKU_STOCK_' . $sku_id;

$stock = $redis->get($stock_key);
if ($stock > 0) {
    // 使用 setnx 并且设置过期时间为 1000ms (设置过期时间是防止死锁的发生)
    $res = $redis->set($lock_key, 1, ['nx', 'px' => 1000]);

    // 此次请求拿不到锁，则关闭请求(说明当前存在正在下单的请求)
    if (!$res) {
        done($start_time,-1);
    }

    // 延时器(锁延时器，防止下单逻辑未执行完毕，锁已过期，导致其他请求进入)
    // 这里的1000毫秒 表示一般情况下 整个下单业务所耗费的时长(根据具体的时间来设置)
    pttl_monitor($lock_key,1000);

    // 模拟业务代码耗时 (1s到2秒)
    usleep(rand(1000, 2000) * 1000);

    // 下面就是正常的下单逻辑, 记住，下面的下单流程无论成功还是失败，必须要释放 锁
    $order_code = random_code(32);
    $mysql = get_mysql();
    $mysql->autocommit(false);

    $insert_order_tpl = 'insert into `order` (order_code, sku_id) values ("%s", %d)';
    $insert_order = sprintf($insert_order_tpl,$order_code, $sku_id);

    $update_sku_tpl = 'update sku set stock = stock - 1 where id = %d and status = %d and stock = %d';
    $update_sku = sprintf($update_sku_tpl,$sku_id, 1, $stock);

    $mysql->begin_transaction();

    $insert_order_res = $mysql->query($insert_order);
    $insert_order_rows = $mysql->affected_rows;

    $update_sku_res  = $mysql->query($update_sku);
    $update_sku_rows = $mysql->affected_rows;

    if ($insert_order_res &&
        $insert_order_rows == 1 &&
        $update_sku_res &&
        $update_sku_rows == 1) {
        $stock -= 1;
        $is_set_redis_stock_success = $redis->set($stock_key, $stock);
        if ($is_set_redis_stock_success) {
            $mysql->commit();
            $mysql->autocommit(true);
        } else {
            $mysql->rollback();
            // 下单失败，释放锁
            $redis->del($lock_key);
            done($start_time,-2);
        }
    } else {
        $mysql->rollback();
        // 下单失败，释放锁
        $redis->del($lock_key);
        done($start_time,-3);
    }

    // 下单成功，释放锁
    $redis->del($lock_key);

    done($start_time,1, $stock);
}

done($start_time);

/**
 * 延时器
 *
 * @param $key string redis key
 * @param $pttl int 原始key的有效期 单位: 毫秒
 */
function pttl_monitor($key, $pttl) {
    $mq = get_rabbitmq();

    $ch = $mq->channel();
    $ch->queue_declare('ttl_monitor', false, false, false, false);
    $msg_json = json_encode(array('redis_key' => $key, 'pttl' => $pttl));
    $msg = new AMQPMessage($msg_json);
    $ch->basic_publish($msg, '', 'ttl_monitor');

    $ch->close();
    try {
        $mq->close();
    } catch (Exception $e) {
        print_r($e->getMessage());
    }


}

function random_code($len = 16) {
    $ori = sha1(uniqid() . microtime(true));
    return substr(str_shuffle($ori), 0, $len);
}

function done($start_time, $status = 0, $stock = 0) {
    switch ($status) {
        case 1:
            $msg = "扣减成功, 当前剩余库存: " . $stock;
            break;
        case -1:
            $msg = "系统繁忙, 请稍后再试";
            break;
        case -2:
            $msg = "redis库存删减失败，回滚";
            break;
        case -3:
            $msg = "mysql事务执行失败，回滚";
            break;
        default:
            $msg = "库存不足";
    }
    error_log($msg);
    error_log("运行时间: " . (microtime(true) - $start_time) * 1000 . '毫秒');
    echo $msg;
    exit();
}

