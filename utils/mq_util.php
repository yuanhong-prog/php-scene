<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/6/1
 * Time: 10:42 AM
 */

require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

function get_rabbitmq() {
    $host = '127.0.0.1';
    $port = 5672;
    $username = 'admin';
    $password = 'admin';
    $vhost    = 'my_vhost';

    try {
        $conn = new AMQPStreamConnection($host, $port, $username, $password, $vhost);
    } catch (Exception $e) {
        echo 'connect rabbitmq fail!';
        print_r($e);
        exit();
    }
    return $conn;
}

