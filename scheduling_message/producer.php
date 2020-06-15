<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/6/1
 * Time: 12:22 PM
 */

use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;

require_once '../utils/mq_util.php';

try {
    $mq = get_rabbitmq_conn();
} catch (Exception $e) {
    echo 'connect mq fail!' . PHP_EOL;
    print_r($e->getMessage());
    exit();
}

$channel = $mq->channel();

$channel->exchange_declare('delay_exchange', 'direct', false, false, false);
$channel->exchange_declare('cache_exchange', 'direct', false, false, false);

// 设置缓存消息队列的参数
$tale = new AMQPTable();
// 指定死信交换机
$tale->set('x-dead-letter-exchange', 'delay_exchange');
// 指定死信交换机路由
$tale->set('x-dead-letter-routing-key','delay_exchange');
// 指定缓存消息队列消息的有效期，毫秒单位
$tale->set('x-message-ttl', 10000);

// 缓存消息的queue
$channel->queue_declare('cache_queue', false, true, false, false, false, $tale);
$channel->queue_bind('cache_queue', 'cache_exchange');

// 消息过期后的处理queue
$channel->queue_declare('delay_queue', false, true, false, false, false);
$channel->queue_bind('delay_queue', 'delay_exchange');

// 模拟消息发送
for ($i = 1000; $i < 10000; $i++) {
    $msg_txt = 'message-' . $i;
    $msg = new AMQPMessage($msg_txt, array(
        'expiration' => $i, //  消息过期时间, 与上述有效期作用一致，如果全部设置，则取最短的为准
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT

    ));

    $channel->basic_publish($msg, 'cache_exchange');
    echo date('Y-m-d H:i:s') . " [x] Sent '$msg_txt' ".PHP_EOL;
    sleep(1);
}