<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2019/12/5
 * Time: 10:52 PM
 */

require_once '../vendor/autoload.php';
require_once '../utils/common.php';

$mq = get_rabbitmq();

$ch = $mq->channel();
$ch->queue_declare('ttl_monitor', false, false, false, false);

$ch->basic_consume('ttl_monitor', '', false, true, false, false, function ($msg) {
    $msg = json_decode($msg->body, true);
    $redis_key = $msg['redis_key'];
    $ori_pttl = $msg['pttl'];

    $redis = get_redis();
    $extension_times = 0;
    while (true) {
        $extension_times ++;
        $current_pttl = $redis->pttl($redis_key);
        // 已过期或者延长次数大于3次，则不再进行延长，这里的时间根据业务运行的时间来把控，主要是防止下单系统崩溃导致未释放锁，发生死锁
        if ($current_pttl < 0 || $extension_times >= 3) {
            break;
        }

        if (($ori_pttl - $current_pttl) >= $ori_pttl / 3) {
            $pttl = $ori_pttl + $current_pttl;
            echo "key = $redis_key 延长过期时间, 当前剩余过期时间 = $current_pttl 毫秒, 延长后的剩余过期时间 = $pttl 毫秒" . PHP_EOL;
            $redis->pExpire($redis_key, $pttl);
        }
    }

});

echo ' [*] ttl monitor running. To exit press CTRL+C', "\n";

while(count($ch->callbacks)) {
    $ch->wait();
}