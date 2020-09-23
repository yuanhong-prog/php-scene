<?php
require_once '../vendor/autoload.php';
require_once '../utils/common.php';

$sku_id = 1;
$stock  = 5;

$mysql = get_mysql();

try {
    $redis = get_redis();
} catch (RedisException $e) {
    exit('redis 连接失败: ' . $e->getMessage());
}

$select_tpl = 'select * from sku where id = %d';
$select_sku = sprintf($select_tpl, $sku_id);
$select_sku_res = $mysql->query($select_sku);
if ($select_sku_res->num_rows) {
    $update_sku_tpl = 'update sku set stock = %d where id = %d';
    $update_sku = sprintf($update_sku_tpl, $stock, $sku_id);
    $update_sku_res  = $mysql->query($update_sku);
} else {
    $insert_sku_tpl = 'insert into `sku` (id, stock) values (%d, %d)';
    $insert_sku = sprintf($insert_sku_tpl, $sku_id, $stock);
    $mysql->query($insert_sku);
}

// 存储库存值的redis key
$stock_key = 'SKU_STOCK_' . $sku_id;

$stock = $redis->set($stock_key, $stock);
echo 'init sku success! sku_id = ' . $sku_id . ', stock = ' . $stock . PHP_EOL;




