<?php
/**
*类名：AMQPMsg
*功能：用户发送和接收消息
*作者：肖金华
*版本：V1
*开发时间：2013-9-11
*修改人：管拥军
*修改时间：2013-9-20
*/

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
include_once (__DIR__."/vendor/autoload.php");

class AMQPMsg
{
	private static $connection;
	private static $channel;
	
	//功能：初始化消息服务器信息
	public static function AMQPCon()
	{
		//self::$connection = new AMQPConnection('192.168.13.56', 5672, 'guest', 'guest');
		self::$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua','mq_vhost1');
		self::$channel = self::$connection->channel();//声明通道
		self::$channel->exchange_declare('order', 'topic', false, false, false);
	}
	
	//发送消息
	public static function sendMes($mes)//发送
	{	//echo $mes;		
		self::AMQPCon();
		$msg = new AMQPMessage($mes);
		self::$channel->basic_publish($msg, 'order');//发送消息
		self::AMQPClose();
	}
	
	//接收消息
	public static function receiveMes()
	{		
		self::AMQPCon();
		list($queue_name, ,) = self::$channel->queue_declare("", false, false, true, false);		
		self::$channel->queue_bind($queue_name, 'order');//绑定消息队列	
		$callback = function($msg)	
		{
		  echo $msg->body;
		};		
		self::$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
		while(count(self::$channel->callbacks)) 
		{
			self::$channel->wait();
		}
		self::AMQPClose();		
	}
	
	//关闭服务器连接
	public static function AMQPClose()
	{
		self::$channel->close();
		self::$connection->close();
	}
}
?>