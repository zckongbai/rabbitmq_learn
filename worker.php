<?php
/**
 * 处理任务
 */

$exchangeName = 'task';
$queueName = 'worker';
$routeKey = 'worker';

// 建立TCP连接
$connection = new AMQPConnection([
    'host' => 'localhost',
    'port' => '5672',
    'vhost' => '/',
    'login' => 'guest',
    'password' => 'guest'
]);
$connection->connect() or die("Cannot connect to the broker!\n");

$channel = new AMQPChannel($connection);

$exchange = new AMQPExchange($channel);

$exchange->setName($exchangeName);
// $exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setType(AMQP_EX_TYPE_FANOUT);

echo 'Exchange Status: ' . $exchange->declareExchange() . "\n";

$queue = new AMQPQueue($channel);
$queue->setName($queueName);

echo 'Message Total: ' . $queue->declareQueue() . "\n";

echo 'Queue Bind: ' . $queue->bind($exchangeName, $routeKey) . "\n";

var_dump("Waiting for message...");

// 消费队列消息
while(TRUE) {
    $queue->consume('processMessage');
}

// 断开连接
$connection->disconnect();

function processMessage($envelope, $queue) {
    $msg = $envelope->getBody();
    var_dump("Received: " . $msg);
    sleep(substr_count($msg, '.')); // 为每一个点号模拟1秒钟操作
    $queue->ack($envelope->getDeliveryTag()); // 手动发送ACK应答
}