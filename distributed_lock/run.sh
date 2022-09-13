#!/bin/bash
set -e

cpath=`pwd`
# 初始化 库存
php ./init_sku.php

# 启动下单接口服务
php -S localhost:8701 >> "${cpath}"/run.log 2>&1 &

# 启动延时器mq服务
php ./monitor.php >> "${cpath}"/monitor.log 2>&1 &

bash -c "tail -f ${cpath}/*.log"