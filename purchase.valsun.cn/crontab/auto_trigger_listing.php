<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
Core::getInstance();
global $dbConn,$dbconn;
error_reporting(-1);
$consumer_tag = 'consumer';

function publish($data){
	$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
	$ch = $conn->channel();
	$ch->queue_declare("oversold_queue", false, true, false, false);
	$ch->queue_bind("oversold_queue", "oversold");
	$msg = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
	$rtn = $ch->basic_publish($msg, "oversold");
}

function check_offine_ts(){ //检查停售的料号
	global $dbConn,$dbconn;
	//$sql = "select a.*,b.goodsStatus from ph_sku_statistics as a left join pc_goods as b on a.sku=b.sku  where out_mark=0 and b.goodsStatus=2";
	$sql = "select a.*,b.goodsStatus from ph_sku_statistics as a left join pc_goods as b on a.sku=b.sku  where out_mark=0 and b.goodsStatus=2";
	$sql = $dbConn->execute($sql);
	$info = $dbConn->getResultArray($sql);
	foreach($info as $item){
		//$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['interceptnums'] - $item['autointerceptnums'] - $item['salensend'] - $item['auditingnums'];
		//$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['autointerceptnums'] - $item['salensend'] - $item['auditingnums'];
		$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['autointerceptnums'] - $item['salensend'] ;
		$outOfStockDays = $availableInventoryDays - $arrivalGoodsDays;
		if($availableStock <= 0){
			$sendData = array(
				"sku" => $item['sku'],
				"availableStock"=>$availableStock,
				"location" => "CN",
				"availableInventoryDays" => 0,
				"outOfStockDays" => 1,
				"arrivalGoodsDays" => 0,
				"everyday_sale" => $item['everyday_sale'],
				"action" => "offline",
				"status" => $item['goodsStatus']
			);
			print_r($sendData);
			$log = json_encode($sendData)."\n";
			file_put_contents("cn.ts_offline.txt",$log,FILE_APPEND);
			publish($sendData);
		}

	}
}

function check_offine_sz(){
	global $dbConn,$dbconn;
	$sql = "select a.* ,b.goodsStatus from ph_sku_statistics as a left join pc_goods as b on a.sku=b.sku  where out_mark=0  and b.goodsStatus!=2 and a.sku!='' ";
	$sql = $dbConn->execute($sql);
	$info = $dbConn->getResultArray($sql);
	foreach($info as $item){
		//$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['interceptnums'] - $item['autointerceptnums'] - $item['salensend'] - $item['auditingnums'];
		//$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['autointerceptnums'] - $item['salensend'] - $item['auditingnums'];
		$availableStock = $item['stock_qty'] + $item['ow_stock'] - $item['autointerceptnums'] - $item['salensend'] ;
		if($item['everyday_sale'] == 0){
			$item['everyday_sale'] = 0.001;
		}
		$availableInventoryDays = ceil($availableStock/$item['everyday_sale']);
		$days = floor((time() - $item['addReachtime']) / (24*60*60));
		if(isset($item['addReachtime'])){
			$days = floor((time() - $item['addReachtime']) / (24*60*60));
			$arrivalGoodsDays = $item['reach_days'] - $days; // 可能到货天数 
		}else{
			$arrivalGoodsDays = 5;
		}

		$outOfStockDays = $availableInventoryDays - $arrivalGoodsDays;
		if($outOfStockDays < 0 || $availableInventoryDays <= 0){
			$outOfStockDays = (-1)*$outOfStockDays;
			$sendData = array(
				"sku" => $item['sku'],
				"availableStock"=>$availableStock,
				"location" => "CN",
				"availableInventoryDays" => $availableInventoryDays,
				"outOfStockDays" => $outOfStockDays,
				"arrivalGoodsDays" => $arrivalGoodsDays,
				"everyday_sale" => $item['everyday_sale'],
				"action" => "offline",
				"status" => $item['goodsStatus']
			);
			print_r($sendData);
			$log = json_encode($sendData);
			file_put_contents("cn.offline.txt",$log,FILE_APPEND);
			publish($sendData);
		}

	}
} 



// 海外仓超卖触发
function check_offine_us(){
	global $dbConn,$dbconn;
	//$sql = "select * from ow_stock where  out_alert=1 and out_mark=0";
	$sql = "select * from ow_stock where   out_mark=0";
	//$sql = "select * from ow_stock ";
	$sql = $dbConn->execute($sql);
	$info = $dbConn->getResultArray($sql);
	foreach($info as $item){
		$availableStock = $item['count'] - $item['salensend'];
		if($item['everyday_sale'] == 0){
			$item['everyday_sale'] = 0.001;
		}
		$availableInventoryDays = ceil($availableStock/$item['everyday_sale']);

		if(isset($item['addReachtime']) && $item['reach_days'] != 0 && $item['addReachtime'] != 0){
			$days = floor((time() - $item['addReachtime']) / (24*60*60));
			$arrivalGoodsDays = $item['reach_days'] - $days; // 可能到货天数 
		}else{
			$arrivalGoodsDays = 5;
		}

		//var_dump($availableInventoryDays,$arrivalGoodsDays,$item['reach_days'],$item['addReachtime']);
		$outOfStockDays = $availableInventoryDays - $arrivalGoodsDays;
		if($outOfStockDays < 0){
			$outOfStockDays = (-1)*$outOfStockDays;
			$sendData = array(
				"sku" => $item['sku'],
				"location" => "US",
				"availableStock"=>$availableStock,
				"availableInventoryDays" => $availableInventoryDays,
				"outOfStockDays" => $outOfStockDays,
				"arrivalGoodsDays" => $arrivalGoodsDays,
				"everyday_sale" => $item['everyday_sale'],
				"action" => "offline"
			);
			//$log = $item['sku']."\n";
			$log = json_encode($sendData);
			file_put_contents("us.offline.txt",$log,FILE_APPEND);
			print_r($sendData);
			publish($sendData);
		}
	}
}

check_offine_sz();
check_offine_ts();
check_offine_us();

?>
