<?php
require_once '../utils/mq_util.php';

try {
    $redis = get_redis();
} catch (RedisException $e) {
    exit('redis 连接失败: ' . $e->getMessage());
}

// 模拟一个抽奖的场景，5秒钟内最多可以支持20个人抽奖，超过这个数量，提示 活动太火爆了，请稍后再试

