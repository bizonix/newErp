<?php
define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/wh.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

require_once  '/data/web/erpNew/wh.valsun.cn/lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection    = new AMQPConnection('112.124.41.121', 5672, 'valsun_sendOrder', 'sendOrder%123','sendOrder');
$exchange_name = 'send_order_exchange';
$queue_name    = 'rabbitmq_wh_getOrderInfo_queue';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$messageCount = $channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

//echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$i = 0;
$max = 2000;
while ($i<$messageCount[1] && $i<$max) {
	$mctime = time();
	$msg = $channel->basic_get($queue);
	//$channel->basic_ack($msg->delivery_info['delivery_tag']);
	//var_dump($msg->body); $i++; continue;
	$msg_array = json_decode($msg->body,true);
	if(empty($msg_array)){
		
	}else
	if($msg_array == 'quit'){
		echo "最后一条数据为退出命令！\n";
		$channel->basic_ack($msg->delivery_info['delivery_tag']);
		break;
	}else{
		foreach($msg_array as $order_infos){
			$sku_info = array();
			$true_sku = get_realskuinfo($order_infos['sku']);
			foreach($true_sku as $sku=>$num){
				if(isset($sku_info[$sku])){
					$sku_info[$sku] = $sku_info[$sku]+$num;
				}else{
					$sku_info[$sku] = $num;
				}
			}
			
			$order_detail = array();
			$sku_blag = false;
			foreach($sku_info as $sku=>$nums){
				$position_arr = getPositionBySku($sku,$nums);
				if(empty($position_arr)){
					Log::write('料号'.$sku.'找不到仓位', Log::ERR);
					$sku_blag = true;
					break;
				}
				foreach($position_arr as $position){
					$order_detail[] = array(
						'sku' 	 	 => $sku,
						'amount' 	 => $position['amount'],
						'positionId' => $position['positionId'],
						'pName' 	 => $position['position']
					);
				}
			}
			
			if($sku_blag){
				continue;
			}
			
			if(count($order_detail) == 1){
				$orderAttributes = 1;
			}else{
				$orderAttributes = 2;
			}
			
			OmAvailableModel::begin();
			$insert_arr[] = "username='{$order_infos['ebay_username']}'";
			$insert_arr[] = "platformId='{$order_infos['platformId']}'";
			$insert_arr[] = "platformUsername='{$order_infos['ebay_userid']}'";
			$insert_arr[] = "email='{$order_infos['ebay_usermail']}'";
			$insert_arr[] = "countryName='{$order_infos['ebay_countryname']}'";
			$insert_arr[] = "countrySn='{$order_infos['ebay_couny']}'";
			$insert_arr[] = "state='{$order_infos['ebay_state']}'";
			$insert_arr[] = "city='{$order_infos['ebay_city']}'";
			$insert_arr[] = "street='{$order_infos['ebay_street']}'";
			$insert_arr[] = "address2='{$order_infos['ebay_street1']}'";
			$insert_arr[] = "currency='{$order_infos['ebay_currency']}'";
			$insert_arr[] = "landline='{$order_infos['ebay_phone']}'";
			$insert_arr[] = "phone='{$order_infos['ebay_phone1']}'";
			$insert_arr[] = "zipCode='{$order_infos['ebay_postcode']}'";
			$insert_arr[] = "transportId='{$flip_carrier_arr[$order_infos['ebay_carrier']]}'";
			$insert_arr[] = "account='{$order_infos['ebay_account']}'";
			$insert_arr[] = "orderStatus='400'";
			$insert_arr[] = "orderAttributes={$orderAttributes}";
			$insert_arr[] = "pmId={$order_infos['pmId']}";
			$insert_arr[] = "isFixed=1";
			$insert_arr[] = "channelId='{1}'";
			$insert_arr[] = "total='{$order_infos['ebay_total']}'";
			$insert_arr[] = "calcWeight={$order_infos['orderweight']}";
			$insert_arr[] = "calcShipping={$order_infos['ordershipfee']}";
			$insert_arr[] = "createdTime='{$mctime}'";
			
			$order_tname = "wh_shipping_order";
			$order_set 	 = "set ".implode(",", $insert_arr);
			$shipOrderId = OmAvailableModel::addTNameRow($order_tname,$order_set);
			if(!$shipOrderId){
				Log::write("INSERT INTO ".$order_tname.$order_set, Log::ERR);
				continue;
			}
			
			$insert_relation_arr = array();
			$insert_relation_arr[] = "originOrderId = '{$order_infos['ebay_id']}'";
			$insert_relation_arr[] = "shipOrderId = '{$shipOrderId}'";
			$insert_relation_arr[] = "recordNumber = '{$order_infos['recordnumber']}'";
			$insert_relation_sql = "INSERT INTO wh_shipping_order_relation SET ".implode(",", $insert_relation_arr);
			$relation_tname = "wh_shipping_order_relation";
			$relation_set 	= "set ".implode(",", $insert_relation_arr);
			$relationId = OmAvailableModel::addTNameRow($relation_tname,$relation_set);
			if(!$relationId){
				Log::write("INSERT INTO ".$relation_tname.$relation_set, Log::ERR);
				OmAvailableModel::rollback();
				continue;
			}
			
			if(!empty($order_infos['ebay_tracknumber'])){
				$insert_tracknumber_arr = array();
				$insert_tracknumber_arr[] = "tracknumber = '{$order_infos['ebay_tracknumber']}'";
				$insert_tracknumber_arr[] = "shipOrderId = '{$shipOrderId}'";
				$insert_tracknumber_arr[] = "createdTime = '{$mctime}'";
				$tracknumber_tname = "wh_order_tracknumber";
				$tracknumber_set   = "set ".implode(",", $insert_tracknumber_arr);
				$tracknumberId = OmAvailableModel::addTNameRow($tracknumber_tname,$tracknumber_set);
				if(!$tracknumberId){
					Log::write("INSERT INTO ".$tracknumber_tname.$tracknumber_set, Log::ERR);
					OmAvailableModel::rollback();
					continue;
				}
			}
			
			$detail_blag = false;
			foreach($order_detail as $detail){
				$insert_detail_arr = array();
				$insert_detail_arr[] = "shipOrderId = '{$shipOrderId}'";
				$insert_detail_arr[] = "sku = '{$detail['sku']}'";
				$insert_detail_arr[] = "amount = '{$detail['amount']}'";
				$insert_detail_arr[] = "positionId = '{$detail['positionId']}'";
				$insert_detail_arr[] = "pName = '{$detail['pName']}'";
				$detail_tname = "wh_shipping_orderdetail";
				$detail_set   = "set ".implode(",", $insert_tracknumber_arr);
				$detailId 	  = OmAvailableModel::addTNameRow($detail_tname,$detail_set);
				if(!$detailId){
					Log::write("INSERT INTO ".$detail_tname.$detail_set, Log::ERR);
					OmAvailableModel::rollback();
					$detail_blag = true;
					break;
				}
			}
			if($detail_blag){
				continue;
			}
			OmAvailableModel::commit();
			$channel->basic_ack($msg->delivery_info['delivery_tag']);
		}
	}
	$i++;
}

$channel->close();
$connection->close();
?>