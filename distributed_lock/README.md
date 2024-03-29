## 概述
使用redis + rabbitmq 实现的分布式锁，尽可能的防止超卖现象的发生

## 原理
通过 redis [set](http://redisdoc.com/string/set.html) 来实现锁(redis本身是单进程，使用set来实现setnx, 多个请求同时加锁，也只会有一个加锁成功), 该锁存在一个默认的过期时间来防止死锁的出现。加锁成功的同时会发送一个异步请求来为锁延长过期时间(这里主要是考虑到业务运行的时间有可能会大于锁的过期时间，导致业务未运行完成，另一个请求也能获取到锁)

## 优点
能更大程度上防止超卖(当然肯定是不能完全防止)

## 缺点
执行效率低下(不是并行处理请求)

## 可改进的想法
可以讲库存分段缓存到redis。
50个库存，分5个key进行存储，每个里面放10个库存，那么可以对这5个key进行加锁，效率提高5倍

[面试官：每秒上千订单的场景下，如何对分布式锁进行高并发优化？](https://blog.csdn.net/qq_42046105/article/details/102577610)

## 依赖服务
- mysql
- redis
- rabbitmq

如果没有上述依赖，可运行 sh docker/run.sh

## 依赖 mysql DDL
- ddl.sql(如果使用了上述docker搭建的mysql, 则不需要运行DDL)

## 启动
```
# 启动服务
sh run.sh 

# 下单服务
curl localhost:8888/create_order.php

```

## 效果
![jmeter](https://github.com/yuanhong-prog/php-scene/blob/master/images/WX20200923-182620%402x.png)

![consolu](https://github.com/yuanhong-prog/php-scene/blob/master/images/WX20200923-182513%402x.png)
