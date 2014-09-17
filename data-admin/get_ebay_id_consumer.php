<?php
include('/data/web/data/php-amqplib/demo/config.php');
include_once "sync_data.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
error_reporting(0);


global $dbcon;
$exchange = 'ebay_id_send';
$queue = 'ebay_ids_queue';
$consumer_tag = 'consumer';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

/*
    name: $queue
    passive: false
    durable: true // the queue will survive server restarts
    exclusive: false // the queue can be accessed in other channels
    auto_delete: false //the queue won't be deleted once the channel is closed.
*/
$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'fanout', false, true, false);
$ch->queue_bind($queue, $exchange);


function process_message($msg){
	$dataStr = $msg->body;

	if(!empty($dataStr)){
		$ebay_id = json_decode($dataStr,true);
		$rtn = calc_order($ebay_id,"ebay");
		if($rtn){
			$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		}
	}
       
}




/*
    queue: Queue from where to get the messages
    consumer_tag: Consumer identifier
    no_local: Don't receive messages published by this consumer.
    no_ack: Tells the server if the consumer will acknowledge the messages.
    exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
    nowait:
    callback: A PHP Callback
*/

$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

/*
function shutdown($ch, $conn){
    $ch->close();
    $conn->close();
}
register_shutdown_function('shutdown', $ch, $conn);
 */



// Loop as long as the channel has callbacks registered
while (count($ch->callbacks)) {
    $ch->wait();
}


?>
