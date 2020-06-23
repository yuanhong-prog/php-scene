<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/6/23
 * Time: 2:22 PM
 */
function get_mysql() {
    $host = '127.0.0.1';
    $user='root';
    $password='123456';
    $db_name='test';

    $mysql = new mysqli($host,$user,$password,$db_name);

    if ($mysql->connect_error) {
        exit('mysql 连接失败: ' . $mysql->connect_error);
    }

    return $mysql;
}