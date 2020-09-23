#!/bin/bash
set -e

# docker启动相应的配套服务, 如果已经存在，可不运行
sh ../docker/mysql/run.sh
sh ../docker/redis/run.sh
sh ../docker/rabbitmq/run.sh