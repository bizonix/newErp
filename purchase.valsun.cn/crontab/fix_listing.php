<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
Core::getInstance();
global $dbConn,$dbconn;
$curl = new CURL();
error_reporting(-1);

function publish($data){
	$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
	$ch = $conn->channel();
	//$ch->queue_declare("oversold_queue", false, true, false, false);
	$ch->queue_declare("oversold_reshelf", false, true, false, false);
	$ch->queue_bind("oversold_reshelf", "oversold_reshelf");
	$msg = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
	$rtn = $ch->basic_publish($msg, "oversold_reshelf");
}

function check_sz_online(){
	global $dbConn,$dbconn,$argv;
	$sql = "select * from ph_sku_statistics where  out_mark=1";
	$page = $argv[1]*10000;
//	$sql = "select * from ph_sku_statistics limit {$page} ,10000";
	$sql = $dbConn->execute($sql);
	$info = $dbConn->getResultArray($sql);
	foreach($info as $item){
			//$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['interceptnums'] - $item['autointerceptnums'] - $item['salensend'] - $item['auditingnums'];
			$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['autointerceptnums'] - $item['salensend'] ;
			if($item['everyday_sale'] == 0){//如果均量为0 默认一个值，让库存天数放到10倍
				$item['everyday_sale'] = 0.1;
			}
			$availableInventoryDays = ceil($availableStock/$item['everyday_sale']);
			//$outOfStockDays = $availableInventoryDays - $item['reach_days'];
			if(isset($item['stockoutDays']) && $item['stockoutDays'] != 0){
				$stockoutDays = $item['stockoutDays'];
			}else{
				$stockoutDays = 10;
			}

			$outOfStockDays = 0;
			if($availableInventoryDays > $stockoutDays){
				$days = floor((time() - $item['addReachtime']) / (24*60*60));
				$arrivalGoodsDays = $item['reach_days'] - $days; 
				$sendData = array(
					"sku" => $item['sku'],
					"availableStock" => $availableStock,
					"location" => "CN",
					"availableInventoryDays" => $availableInventoryDays,
					"outOfStockDays" => $outOfStockDays,
					"arrivalGoodsDays" => $arrivalGoodsDays,
					"everyday_sale" => $item['everyday_sale'],
					"action" => "online"
				);

				print_r($sendData);
				publish($sendData);
			}
	}
}


function check_us_online(){
	global $dbConn,$dbconn;
	$sql = "select * from ow_stock where out_mark=1";
	//$sql = "select * from ow_stock ";
	$sql = $dbConn->execute($sql);
	$skuInfo = $dbConn->getResultArray($sql);
	foreach($skuInfo as $item){
		if($item['count'] <= 0){
			continue;
		}
		$availableStock =  $item['count'] - $item['salensend'];
		if($item['everyday_sale'] == 0 && $availableStock > 10){
			//$availableInventoryDays = $availableStock;
			$item['everyday_sale'] = 0.001;
		}
		$availableInventoryDays = ceil($availableStock/$item['everyday_sale']);
		//$outOfStockDays = $availableInventoryDays - $item['reach_days'];
		if($availableInventoryDays > 5 && $availableStock > 10){
			//$days = floor((time() - $item['addReachtime']) / (24*60*60));
			$sendData = array(
				"sku" => $item['sku'],
				"availableStock" => $availableStock,
				"location" => "US",
				"availableInventoryDays" => $availableInventoryDays,
				"outOfStockDays" => 0,
				"arrivalGoodsDays" => 0,
				"everyday_sale" => $item['everyday_sale'],
				"action" => "online"
			);
			print_r($sendData);
			publish($sendData);
		}
	}
}
check_sz_online();
check_us_online();



?>
