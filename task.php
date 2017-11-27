<?php
/**
 * 分发任务
 */

$exchangeName = 'task';
$queueName = 'worker';
$routeKey = 'worker';
$message = empty($argv[1]) ? 'Hello World!' : $argv[1];

// 建立TCP连接
$connection = new AMQPConnection([
    'host' => 'localhost',
    'port' => '5672',
    'vhost' => '/',
    'login' => 'guest',
    'password' => 'guest'
]);
$connection->connect() or die("Cannot connect to the broker!\n");

try {
    $channel = new AMQPChannel($connection);

    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    // $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->setType(AMQP_EX_TYPE_FANOUT);
    $exchange->declareExchange();

    // echo 'Send Message: ' . $exchange->publish($message);//, $routeKey) . "\n";
    echo 'Send Message: ' . $exchange->publish($message, $routeKey, 1) . "\n";
    echo "Message Is Sent: " . $message . "\n";
} catch (AMQPConnectionException $e) {
    var_dump($e);
}

// 断开连接
$connection->disconnect();