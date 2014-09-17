<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
error_reporting(-1);
//echo "#####";
$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua','mq_vhost1');
//echo __DIR__.'/vendor/autoload.php';
//exit;
$channel = $connection->channel();
//demo 通道
$channel->exchange_declare('order', 'topic', false, false, false);

//$data = implode(' ', array_slice($argv, 1));
$data = 'abc';
//if(empty($data)) $data = array("update"=>"info: sku ");
$msg = new AMQPMessage($data);
//var_dump($msg);

$channel->basic_publish($msg, 'order');

echo " [x] Sent from order ", $data, "\n";

$channel->close();
$connection->close();
?>