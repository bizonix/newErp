<?php
define('AMQP_PASSIVE', true);
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPReader;

class RabbitMQClass{
	private $ServerAddress = '';
	private $Port = '';
	private $mquser = '';
	private $mqpassw = '';
	private $vhost = '';
	private $msg_body = '';
	
	public function __construct($mquser,$mqpassw,$vhost,$ServerAddress = '192.168.200.222'){
	//	$this->ServerAddress = $ServerAddress;
		$this->ServerAddress = "112.124.41.121";
		$this->Port = '5672';
		$this->mquser = $mquser;
		$this->mqpassw = $mqpassw;
		$this->vhost = $vhost;
	}
	
	public function queue_publish($exchange,$msg_body,$mqtype = 'fanout'){
		//发布
		
		$conn = new AMQPConnection($this->ServerAddress, $this->Port, $this->mquser, $this->mqpassw, $this->vhost);
		$ch = $conn->channel();
		
		/*
			name: $exchange
			type: fanout
			passive: false // don't check is an exchange with the same name exists
			durable: false // the exchange won't survive server restarts
			auto_delete: true //the exchange will be deleted once the channel is closed.
		*/
		
		$ch->exchange_declare($exchange, $mqtype, false, false, false);

		$ch->queue_declare('purchase_queue', false, true, false, false);
		
		//$msg_body = implode(' ', array_slice($argv, 1));
		$msg = new AMQPMessage(json_encode($msg_body),array('content_type' => 'text/plain'));
		//$ch->basic_publish($msg, $exchange);
		$ch->basic_publish($msg, $exchange,"purchase_queue");
		
		$ch->close();
		$conn->close();
	}

	public function single_queue_publish($exchange,$msg_body){
		$conn = new AMQPConnection($this->ServerAddress, $this->Port, $this->mquser, $this->mqpassw, $this->vhost);
		$ch = $conn->channel();
		$queue = "purchase_queue";
		$ch->queue_declare($queue, false, true, false, false);
		$ch->exchange_declare($exchange, 'direct', false, true, false);
		$ch->queue_bind($queue, $exchange);
		$msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
		$ch->basic_publish($msg, $exchange);
		$ch->close();
		$conn->close();
	}
	
	public function fanout_publish($exchange,$msg_body){
		$conn = new AMQPConnection($this->ServerAddress, $this->Port, $this->mquser, $this->mqpassw, $this->vhost);
		$ch = $conn->channel();
		$ch->exchange_declare($exchange, 'fanout', false, true, false);
		$msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
		$ch->basic_publish($msg, $exchange);
		$ch->close();
		$conn->close();
	}


	public function queue_subscribe($exchange,$queue, $extral=true, $callback=array('RabbitMQClass','process_message'),$mqtype = 'fanout'){
		//订阅
		
		$consumer_tag = 'consumer'. getmypid ();
		
		$conn = new AMQPConnection($this->ServerAddress, $this->Port, $this->mquser, $this->mqpassw, $this->vhost);
		$ch = $conn->channel();
		
		/*
			name: $queue    // should be unique in fanout exchange.
			passive: false  // don't check if a queue with the same name exists
			durable: false // the queue will not survive server restarts
			exclusive: false // the queue might be accessed by other channels
			auto_delete: true //the queue will be deleted once the channel is closed.
		*/
		
		$messageCount = $ch->queue_declare($queue, false, false, false, false);
		//var_dump($messageCount);
		
		/*
			name: $exchange
			type: direct
			passive: false // don't check if a exchange with the same name exists
			durable: false // the exchange will not survive server restarts
			auto_delete: true //the exchange will be deleted once the channel is closed.
		*/
		
		$ch->exchange_declare($exchange, $mqtype, false, false, false);
		
		$ch->queue_bind($queue, $exchange);
		
		/*
			queue: Queue from where to get the messages
			consumer_tag: Consumer identifier
			no_local: Don't receive messages published by this consumer.
			no_ack: Tells the server if the consumer will acknowledge the messages.
			exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
			nowait: don't wait for a server response. In case of error the server will raise a channel
					exception
			callback: A PHP Callback
		*/
		
		//$ch->basic_consume($queue, $consumer_tag, false, false, false, false, $callback);
		
		/*function shutdown($ch, $conn)
		{
			$ch->close();
			$conn->close();
		}
		register_shutdown_function('shutdown', $ch, $conn);*/
		
		// Loop as long as the channel has callbacks registered
		/*while(count($ch->callbacks)) {
			$ch->wait();
		}*/
		$i = 0;
		$max = 200;
		$orderidlists = array();
		while ($i<$messageCount[1] && $i<$max) {
			$msg = $ch->basic_get($queue);
			$ch->basic_ack($msg->delivery_info['delivery_tag']);
			//var_dump($msg->body);
			if($extral === false){
				array_push($orderidlists, $msg->body);
			}else{
				array_push($orderidlists, json_decode($msg->body));		
			}
			$i++;
		}
		$ch->close();
		$conn->close();
		//echo "\n----------------end----------------\n";
		return $orderidlists;
	}
	
	function process_message($msg)
	{
		//回调函数，已经废弃使用
		echo "\n--------\n";
		echo $msg->body;
		echo "\n--------\n";
		
	   $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	
		// Send a message with the string "quit" to cancel the consumer.
		if ($msg->body === 'quit') {
			$msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
		}
	}
}
