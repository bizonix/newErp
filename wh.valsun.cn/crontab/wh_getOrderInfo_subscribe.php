<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/wh.valsun.cn/framework.php";
Core::getInstance();

//require_once __DIR__ . '/data/web/wh.valsun.cn/lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('112.124.41.121', 5672, 'valsun_sendOrder', 'sendOrder%123','sendOrder');
$exchange_name = 'send_order_exchange';

$queue_name = 'wh_rabbitmq_getOrderInfo_queue';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    //error_reporting(-1);
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
	$mctime = time();
    
	//var_dump($msg->body); echo"=======\n";
	$msg_array = json_decode($msg->body,true);
    Log::write("订单\r\n".$msg->body, Log::ERR);
	//echo ' [x] ', $msg->body, "\n";
	//var_dump($msg_array);exit;
	//echo "\n\n";
	//print_r($msg_array['orderDetail']);exit;
	if(!empty($msg_array)){
		//$sameorder = false;
 		$orderTypeId = $msg_array['flag']==1? 1 : 2;  //1发货单 2配货单  默认发货单，配货单取消
		$isNotIn 	 = false;
		if($orderTypeId==1){
			$isExist = OmAvailableModel::getTNameList("wh_shipping_order","*","where id='{$msg_array['orderData']['id']}'");
			if(!empty($isExist)){
				foreach($isExist as $exit){
					//$isExistOrder = OmAvailableModel::getTNameList("wh_shipping_order","*","where id='{$exit['shipOrderId']}'");
					if($exit['orderStatus']!=900 && $exit['orderTypeId']==1){
						$isNotIn = true;
					}
				}
				if($isNotIn){
					Log::write('订单'.$msg_array['orderData']['id'].'仓库系统已经存在', Log::ERR);
					//continue;
					
				}
			}
		}
		if(!$isNotIn){
			$isNote = empty($msg_array['notes'])? 0 : 1;
			$sku_info = array();
			foreach($msg_array['orderDetail'] as $orderDetail){
				$is_combine = get_skuIsCombine($orderDetail['orderDetailData']['sku']);
				//var_dump($is_combine); exit;
				$itemTitle = !empty($orderDetail['orderDetailExtenData']['0']['itemTitle'])? mysql_real_escape_string($orderDetail['orderDetailExtenData']['0']['itemTitle']) : '';
				$itemPrice = $orderDetail['orderDetailData']['itemPrice'];
				if($is_combine){
					$true_sku = get_realskuinfo($orderDetail['orderDetailData']['sku']);
					foreach($true_sku as $sku=>$num){
						if(isset($sku_info[$orderDetail['orderDetailData']['sku']][$sku])){
							$sku_info[$orderDetail['orderDetailData']['sku']][$sku] = $sku_info[$orderDetail['orderDetailData']['sku']][$sku]+$num*$orderDetail['orderDetailData']['amount'];
						}else{
							$sku_info[$orderDetail['orderDetailData']['sku']][$sku] = $num*$orderDetail['orderDetailData']['amount'];
						}
					}
					if(isset($sku_info[$orderDetail['orderDetailData']['sku']]['amount'])){
						$sku_info[$orderDetail['orderDetailData']['sku']]['amount'] = $sku_info[$orderDetail['orderDetailData']['sku']]['amount']+$orderDetail['orderDetailData']['amount'];
					}else{
						$sku_info[$orderDetail['orderDetailData']['sku']]['amount'] = $orderDetail['orderDetailData']['amount'];
					}
					$sku_info[$orderDetail['orderDetailData']['sku']]['itemTitle'] = $itemTitle;
					$sku_info[$orderDetail['orderDetailData']['sku']]['itemPrice'] = $itemPrice;
				}else{
					if(isset($sku_info[$orderDetail['orderDetailData']['sku']])){
						$sku_info[$orderDetail['orderDetailData']['sku']]['amount'] = $sku_info[$orderDetail['orderDetailData']['sku']]+$orderDetail['orderDetailData']['amount'];
						$sku_info[$orderDetail['orderDetailData']['sku']]['itemTitle'] = $itemTitle;
						$sku_info[$orderDetail['orderDetailData']['sku']]['itemPrice'] = $itemPrice;
					}else{
						$sku_info[$orderDetail['orderDetailData']['sku']]['amount'] = $orderDetail['orderDetailData']['amount'];
						$sku_info[$orderDetail['orderDetailData']['sku']]['itemTitle'] = $itemTitle;
						$sku_info[$orderDetail['orderDetailData']['sku']]['itemPrice'] = $itemPrice;
					}
				}
			}
			$order_detail = array();
			$sku_flag     = true;
			$storeId      = 1;
            
            OmAvailableModel::begin();
            
			foreach($sku_info as $key=>$infos){
				$count = count($infos);
				if($count>3){
					$combine_amount = $infos['amount'];
					unset($infos['amount']);
					$itemTitle = $infos['itemTitle'];
					unset($infos['itemTitle']);
					$itemPrice = $infos['itemPrice'];
					unset($infos['itemPrice']);
					foreach($infos as $sku=>$nums){
						$position_arr = getPositionBySku($sku,$nums);
						if(empty($position_arr)){
							Log::write('订单'.$msg_array['orderData']['id'].' 料号'.$sku.'库存不足', Log::ERR);
							CommonModel::callbackOrderSys($msg_array['orderData']['id'],"料号{$sku}库存不足");
							$sku_flag = false;
							break;
						}
						foreach($position_arr as $position){
							if($position['storeId']!=1){
								$storeId = $position['storeId'];
							}
							$order_detail[] = array(
								'itemTitle'  => $itemTitle,
								'itemPrice'  => $itemPrice,
								'combineSku' => $key,
								'combineNum' => $combine_amount,
								'sku' 	 	 => $sku,
								'amount' 	 => $position['amount'],
								'positionId' => $position['positionId'],
								'pName' 	 => $position['position'],
								'storeId' 	 => $position['storeId'],
							);                   
                            $info   =   WhProductPositionRelationModel::updateSkuScanNums($position['pId'], $position['positionId'], $position['amount'], 3);
                            if(!$info){
                                Log::write('update wh_product_position_relation :FAIL', Log::ERR);
        						OmAvailableModel::rollback();
        						$sku_flag = false;
                            }
						}
					}
				}else{
					$position_arr = getPositionBySku($key,$infos['amount']);
					if(empty($position_arr)){
						Log::write('订单'.$msg_array['orderData']['id'].' 料号'.$key.'库存不足', Log::ERR);
						CommonModel::callbackOrderSys($msg_array['orderData']['id'],"料号{$key}库存不足");
						$sku_flag = false;
						//break;
					}
					foreach($position_arr as $position){
						if($position['storeId']!=1){
							$storeId = $position['storeId'];
						}
						$order_detail[] = array(
							'itemTitle'  => $infos['itemTitle'],
							'itemPrice'  => $infos['itemPrice'],
							'combineSku' => '',
							'combineNum' => '',
							'sku' 	 	 => $key,
							'amount' 	 => $position['amount'],
							'positionId' => $position['positionId'],
							'pName' 	 => $position['position'],
							'storeId' 	 => $position['storeId'],
						);
                        $info   =   WhProductPositionRelationModel::updateSkuScanNums($position['pId'], $position['positionId'], $position['amount'], 3);
                        if(!$info){
                            Log::write('update wh_product_position_relation :FAIL', Log::ERR);
    						OmAvailableModel::rollback();
    						$sku_flag = false;
                        }
					}
				}
			}
			
			if($sku_flag){
				if(count($order_detail) == 1){
					$orderAttributes = 1;
				}else{
					$orderAttributes = 2;
				}
				
                /** 发货单号等于订单系统订单号**/
				$insert_arr[] = "recordNumber = '{$msg_array['orderData']['recordNumber']}'";
				$insert_arr[] = "username='{$msg_array['orderUserInfoData']['username']}'";
				$insert_arr[] = "platformId='{$msg_array['orderData']['platformId']}'";
				$insert_arr[] = "platformUsername='{$msg_array['orderUserInfoData']['platformUsername']}'";
				$insert_arr[] = "email='{$msg_array['orderUserInfoData']['email']}'";
				$insert_arr[] = "countryName='{$msg_array['orderUserInfoData']['countryName']}'";
				$insert_arr[] = "countrySn='{$msg_array['orderUserInfoData']['countrySn']}'";
				$insert_arr[] = "state='{$msg_array['orderUserInfoData']['state']}'";
				$insert_arr[] = "city='{$msg_array['orderUserInfoData']['city']}'";
				$insert_arr[] = "street='{$msg_array['orderUserInfoData']['street']}'";
				$insert_arr[] = "address2='{$msg_array['orderUserInfoData']['address2']}'";
				$insert_arr[] = "address3='{$msg_array['orderUserInfoData']['address3']}'";
				$insert_arr[] = "currency='{$msg_array['orderUserInfoData']['currency']}'";
				$insert_arr[] = "landline='{$msg_array['orderUserInfoData']['landline']}'";
				$insert_arr[] = "phone='{$msg_array['orderUserInfoData']['phone']}'";
				$insert_arr[] = "zipCode='{$msg_array['orderUserInfoData']['zipCode']}'";
				$insert_arr[] = "transportId='{$msg_array['orderData']['transportId']}'";
				$insert_arr[] = "accountId='{$msg_array['orderData']['accountId']}'";
				$insert_arr[] = "orderStatus='400'";
				$insert_arr[] = "orderAttributes={$orderAttributes}";
				$insert_arr[] = "pmId={$msg_array['orderData']['pmId']}";
				$insert_arr[] = "isFixed='{$msg_array['orderData']['isFixed']}'";
				$insert_arr[] = "channelId='{$msg_array['orderData']['channelId']}'";
				$insert_arr[] = "total='{$msg_array['orderData']['actualTotal']}'";
				$insert_arr[] = "calcWeight='{$msg_array['orderData']['calcWeight']}'";
				$insert_arr[] = "calcShipping='{$msg_array['orderData']['calcShipping']}'";
				$insert_arr[] = "createdTime='{$mctime}'";
				$insert_arr[] = "orderTypeId='{$orderTypeId}'";
				$insert_arr[] = "isNote='{$isNote}'";
				$insert_arr[] = "storeId='{$storeId}'";
				
				$order_tname = "wh_shipping_order";
				$order_set 	 = "set ".implode(",", $insert_arr);
				$shipOrderId = OmAvailableModel::addTNameRow($order_tname,$order_set);
				if(!$shipOrderId){
					Log::write("INSERT INTO ".$order_tname.$order_set, Log::ERR);
					$sku_flag = false;
				}
				
				if($sku_flag){
					$insert_relation_arr = array();
					$insert_relation_arr[] = "originOrderId = '{$msg_array['orderData']['id']}'";
					$insert_relation_arr[] = "shipOrderId = '{$shipOrderId}'";
					$insert_relation_arr[] = "recordNumber = '{$msg_array['orderData']['recordNumber']}'";
					$insert_relation_sql = "INSERT INTO wh_shipping_order_relation SET ".implode(",", $insert_relation_arr);
					$relation_tname = "wh_shipping_order_relation";
					$relation_set 	= "set ".implode(",", $insert_relation_arr);
					$relationId = OmAvailableModel::addTNameRow($relation_tname,$relation_set);
					if(!$relationId){
						Log::write("INSERT INTO ".$relation_tname.$relation_set, Log::ERR);
						OmAvailableModel::rollback();
						$sku_flag = false;
					}
					
					if($sku_flag){
						if(!empty($msg_array['tracknumbers'])){
							$insert_tracknumber_arr = array();
							$insert_tracknumber_arr[] = "tracknumber = '{$msg_array['tracknumbers']['tracknumber']}'";
							$insert_tracknumber_arr[] = "shipOrderId = '{$shipOrderId}'";
							$insert_tracknumber_arr[] = "createdTime = '{$mctime}'";
							$tracknumber_tname = "wh_order_tracknumber";
							$tracknumber_set   = "set ".implode(",", $insert_tracknumber_arr);
							$tracknumberId = OmAvailableModel::addTNameRow1($tracknumber_tname,$tracknumber_set);
							if(!$tracknumberId){
								Log::write("INSERT INTO ".$tracknumber_tname.$tracknumber_set, Log::ERR);
								OmAvailableModel::rollback();
								$sku_flag = false;
							}
						}
						if($sku_flag){
							if(!empty($msg_array['notes'])){
								$insert_notes_arr = array();
								$insert_notes_arr[] = "shipOrderId = '{$shipOrderId}'";
								$content = mysql_real_escape_string($msg_array['notes'][0]['content']);
								$insert_notes_arr[] = "content = '{$content}'";
								$insert_notes_arr[] = "userId = '{$msg_array['notes'][0]['userId']}'";
								$insert_notes_arr[] = "createdTime = '{$msg_array['notes'][0]['createdTime']}'";
								$insert_notes_arr[] = "storeId = '{$msg_array['notes'][0]['storeId']}'";
								$notes_tname = "wh_order_notes";
								$notes_set   = "set ".implode(",", $insert_notes_arr);
								$noteId = OmAvailableModel::addTNameRow($notes_tname,$notes_set);
								if(!$noteId){
									Log::write("INSERT INTO ".$notes_tname.$notes_set, Log::ERR);
									OmAvailableModel::rollback();
									$sku_flag = false;
								}
							}
							if($sku_flag){
								$detail_flag = true;
								foreach($order_detail as $detail){
									$insert_detail_arr = array();
									$insert_detail_arr[] = "shipOrderId = '{$shipOrderId}'";
									$insert_detail_arr[] = "itemTitle = '{$detail['itemTitle']}'";
									$insert_detail_arr[] = "itemPrice = '{$detail['itemPrice']}'";
									$insert_detail_arr[] = "combineSku = '{$detail['combineSku']}'";
									$insert_detail_arr[] = "combineNum = '{$detail['combineNum']}'";
									$insert_detail_arr[] = "sku = '{$detail['sku']}'";
									$insert_detail_arr[] = "amount = '{$detail['amount']}'";
									$insert_detail_arr[] = "positionId = '{$detail['positionId']}'";
									$insert_detail_arr[] = "pName = '{$detail['pName']}'";
									$insert_detail_arr[] = "storeId = '{$detail['storeId']}'";
									$detail_tname = "wh_shipping_orderdetail";
									$detail_set   = "set ".implode(",", $insert_detail_arr);
									$detailId 	  = OmAvailableModel::addTNameRow($detail_tname,$detail_set);
									if(!$detailId){
										Log::write("INSERT INTO ".$detail_tname.$detail_set, Log::ERR);
										OmAvailableModel::rollback();
										$detail_flag = false;
										break;
									}
								}
								if($detail_flag){
									echo "发货单[{$shipOrderId}]入库成功.\n\n";
									$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
									OmAvailableModel::commit();
								}
							}
						}
					}
				}
			}
          if($sku_flag){
                $waveBuild  =   new WaveBuildAct();
                $waveBuild->waveBuild($shipOrderId);
          }
		}
	}
	
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>