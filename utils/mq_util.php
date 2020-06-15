<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/6/1
 * Time: 10:42 AM
 */

require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function get_rabbitmq_conn() {
    $host = '127.0.0.1';
    $port = 5672;
    $username = 'admin';
    $password = 'admin';
    $vhost    = 'my_vhost';

    return new AMQPStreamConnection($host, $port, $username, $password, $vhost);
}

