## 概述
rabbitmq实现延迟队列

## 原理
rabbitmq 消息有效期(message ttl)+死信队列(dead queue)实现。通过设置消息在队列中的有效期，过期后通过交换机(exchange)自动转入到对应的死信(dead queue)中

## 图解