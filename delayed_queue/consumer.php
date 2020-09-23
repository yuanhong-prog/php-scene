<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/6/1
 * Time: 12:22 PM
 */

require_once '../utils/mq_util.php';

$mq = get_rabbitmq_conn();
$channel = $mq->channel();

$channel->exchange_declare('delay_exchange', 'direct',false,false,false);


$channel->queue_declare('delay_queue',false,true,false,false,false);
$channel->queue_bind('delay_queue', 'delay_exchange', 'delay_exchange');

echo ' [*] Waiting for message. To exit press CTRL+C '.PHP_EOL;

$callback = function ($msg){
    echo date('Y-m-d H:i:s')." [x] Received '". $msg->body. "'". PHP_EOL;
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// 流量管控
$channel->basic_qos(null, 1, null);
$channel->basic_consume('delay_queue','', false, false, false, false, $callback);


while (count($channel->callbacks)) {
    try {
        $channel->wait();
    } catch (Exception $e) {
        echo 'wait message fail!' . PHP_EOL;
        print_r($e->getMessage());
        $channel->close();
        try {
            $mq->close();
        } catch (Exception $e) {
            echo 'close mq fail' . PHP_EOL;
            print_r($e->getMessage());
        }
    }
}

