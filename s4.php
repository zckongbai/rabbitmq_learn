<?php
/**
 * push diff type log
 * type in message info error
 */

$exchangeName = 'log-exchange';
$queueName = 'log';

$type = isset($argv[1]) ? $argv[1] :  'message';
$message = "this is a {$type} log";

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
    $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->declareExchange();    

    echo 'Send Message: ' . $exchange->publish($message, $type, 1) . "\n";
    echo "Message Is Sent: " . $message . "\n";
} catch (AMQPConnectionException $e) {
    var_dump($e);
}

// 断开连接
$connection->disconnect();