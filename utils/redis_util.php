<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/5/23
 * Time: 10:34 AM
 */

function get_redis() {
    $host = '127.0.0.1';
    $auth = 'redis';
    $redis = new Redis();
    $redis->connect($host);
    $redis->auth($auth);
    $result = $redis->ping();
    if ($result != '+PONG') {
        exit('redis 连接失败: ' . $redis->getLastError());
    }
    return $redis;
}