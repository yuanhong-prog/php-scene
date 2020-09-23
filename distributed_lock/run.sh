#!/bin/bash
set -e

# 初始化 库存
php ./init_sku.php

# 启动下单接口服务
php -S localhost:8888 > `pwd`/run.log 2>&1 &

# 启动延时器mq服务
php ./monitor.php > `pwd`/monitor.log 2>&1 &

tail -f `pwd`/*.log