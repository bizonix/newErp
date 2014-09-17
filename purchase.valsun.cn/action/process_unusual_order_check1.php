<?php
include "include/config.php";
include_once 'include/function_action.php';
include_once 'include/function_purchase.php';

include_once 'include/config_row/config_database_row_master.php';
unset($dbcon);
$dbcon	= new DBClass();

$log_data 	= "\r\n";
$ebay_id	= $_POST['ebay_id'];
$detail_id	= $_POST['detail_id'];
$sku		= $_POST['sku'];
$type		= $_POST['type'];
$check_status = $_POST['check_status'];
$notecontent = isset($_POST['pcontent']) ? $_POST['pcontent'] : '';

//强制拦截时输入拦截理由 add by guanyongjun 2013-09-17
if($check_status=='2'){
	$notecontent	= ",notecontent='{$notecontent}'";
}

//审核检查 add by rdh 2013-11-04
if($check_status=='1'){
	$realStore		= 0;
	$needSend		= 0;
	$orderSkuNum	= 0;
	$sql = "select goods_count from ebay_onhandle where goods_sn='{$sku}' and store_id='76' ";
	$sql = $dbcon->execute($sql);
	$sql = $dbcon->getResultArray($sql);
	$realStore = $sql[0]['goods_count']; //实际库存

	$needSend  = getpartsaleandnosendall($sku, '76');//待发货

	$sql2 = "select ebay_amount from ebay_orderdetail where ebay_id='{$detail_id}' ";
	$sql2 = $dbcon->execute($sql2);
	$sql2 = $dbcon->getResultArray($sql2);
	$orderSkuNum = $sql2[0]['ebay_amount'];//订单数量
	$diffNum  = $realStore - $needSend - $orderSkuNum;
	if($diffNum < 0 ) {
		$res['code'] = '002';
		$res['data']['msg'] = "该料号的实际库存$realStore - 待发货$needSend 小于 订购数量$orderSkuNum 审核不通过!";
		echo json_encode($res);
		return $res;
		exit;
	}
}


$res = array();

$c_sql = "select * from ebay_unusual_order_check where ebay_id={$ebay_id} and ebaydetail_id='{$detail_id}' and sku='{$sku}'";
$c_sql = $dbcon->execute($c_sql);
$c_sql = $dbcon->getResultArray($c_sql);

$ordercheck		= "select ebay_ordersn,ebay_account,ebay_carrier,orderweight,ebay_status from ebay_order where ebay_id={$ebay_id}";	
$ordercheck		= $dbcon->execute($ordercheck);
$ordercheck		= $dbcon->getResultArray($ordercheck);
if(empty($ordercheck)){
	$res['code'] = '502';
	$res['data']['msg'] = '未找到对应的订单!';
}else{
	if($type=='check'){
		if(empty($c_sql)){
			$sql = "INSERT INTO ebay_unusual_order_check SET ebay_id={$ebay_id}".$notecontent.",ebay_ordersn='{$ordercheck[0]['ebay_ordersn']}',ebaydetail_id='{$detail_id}',sku='{$sku}',ebay_account='{$ordercheck[0]['ebay_account']}',check_user='{$truename}',checktime=".time().",modtime=".time().",check_status='{$check_status}'";
		}else{
			$sql = "UPDATE ebay_unusual_order_check SET modtime=".time().",check_status='{$check_status}'".$notecontent." WHERE ebay_id={$ebay_id} and ebaydetail_id='{$detail_id}' AND sku='{$sku}'";
		}
		$dbcon->execute($sql);
		$ch_sql = "SELECT a.ebay_ordersn,a.sku,a.ebay_id as detail_id FROM	ebay_orderdetail AS a 
					WHERE a.ebay_ordersn='{$ordercheck[0]['ebay_ordersn']}'";
		$ch_sql = $dbcon->execute($ch_sql);
		$check_array = $dbcon->getResultArray($ch_sql);
		$isend     = true;
		$status    = array();
		$array_sku = array();
		foreach($check_array AS $check_sku){
			$array_sku = get_realskuinfo($check_sku['sku']);
			foreach($array_sku as $key_sku=>$num){
				$compare_sql = "SELECT ebay_ordersn,sku,check_status FROM ebay_unusual_order_check WHERE sku='{$key_sku}' and ebaydetail_id='{$check_sku['detail_id']}' AND ebay_ordersn='{$ordercheck[0]['ebay_ordersn']}'";
				$compare_sql = $dbcon->execute($compare_sql);
				$compare_sql = $dbcon->getResultArray($compare_sql);
				if (empty($compare_sql)){
					$isend = false;
					break;
				}else if (!in_array($compare_sql[0]['check_status'],$status)){
					array_push($status,$compare_sql[0]['check_status']);
				}
			}
		}
		if ($isend===true){
			$changes = array();
			if (in_array(1, $status)&&in_array(2, $status)){
				if (in_array($ordercheck[0]['ebay_carrier'], array('中国邮政平邮','香港小包平邮','中国邮政挂号','香港小包挂号','Global Mail'))){
					$sql = "update ebay_order set ebay_status=653 where ebay_id={$ebay_id} AND ebay_status IN (640,641,642,652,653)";
					$log_data .= $sql."\n";
					$dbcon->execute($sql);
					//$changes['ebay_status'] = 653;
					//$order_statistics->updateFieldData($ebay_id, $changes);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t--订单{$ebay_id}审核前状态为{$ordercheck[0]['ebay_status']}--审核后状态为653--\n\n";
				}else{
					$sql = "update ebay_order set ebay_status=652 where ebay_id={$ebay_id} AND ebay_status IN (640,641,642,652,653)";
					$log_data .= $sql."\n";
					$dbcon->execute($sql);
					//$changes['ebay_status'] = 652;
					//$order_statistics->updateFieldData($ebay_id, $changes);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t--订单{$ebay_id}审核前状态为{$ordercheck[0]['ebay_status']}--审核后状态为652--\n\n";
				}
			}else if (in_array(1, $status)){
				if($ordercheck[0]['orderweight'] > 2 && in_array($ordercheck[0]['ebay_carrier'], array('中国邮政平邮','香港小包平邮','中国邮政挂号','香港小包挂号','Global Mail'))){
					$sql = "update ebay_order set ebay_status=608 where ebay_id={$ebay_id} AND ebay_status IN (640,641,642,652,653)";
					$log_data .= $sql."\n";
					if($dbcon->execute($sql)){
						//$changes['ebay_status'] = 608;
						//$order_statistics->updateFieldData($ebay_id, $changes);
						$log_data .= "[".date("Y-m-d H:i:s")."]\t--订单{$ebay_id}审核前状态为{$ordercheck[0]['ebay_status']}--审核后状态为608--\n\n";
						now_order_status_log($ebay_id);
						mark_shipping($ebay_id, 608);		
					}
				}else{
					$sql = "update ebay_order set ebay_status=641 where ebay_id={$ebay_id} AND ebay_status IN (640,641,642,652,653)";
					$log_data .= $sql."\n";
					if($dbcon->execute($sql)){
						//$changes['ebay_status'] = 641;
						//$order_statistics->updateFieldData($ebay_id, $changes);
						$log_data .= "[".date("Y-m-d H:i:s")."]\t--订单{$ebay_id}审核前状态为{$ordercheck[0]['ebay_status']}--审核后状态为641--\n\n";
						now_order_status_log($ebay_id);
						mark_shipping($ebay_id, 641);					
					}	
				}
			}else if (in_array(2, $status)){
				$sql = "update ebay_order set ebay_status=642 where ebay_id={$ebay_id} AND ebay_status IN (640,641,642,652,653)";
				$log_data .= $sql."\n";
				$dbcon->execute($sql);
				//$changes['ebay_status'] = 642;
				//$order_statistics->updateFieldData($ebay_id, $changes);
			}else{
				$log_data .= var_dump($status)."\n";
			}
		}else{
			$log_data .= "isend is false!\n";
		}
		//write_log('move_unusual_order_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
		write_log('move_order_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
		$res['code'] = '200';
		$res['data']['msg'] = '审核完成!';
		$res['data']['status'] = $check_status;
		$res['data']['isend'] = $isend;
	}else if($type=='add_reason'){
		if(empty($c_sql)){
			//$sql = "INSERT INTO ebay_unusual_order_check SET ebay_id={$ebay_id},ebay_ordersn='{$ordercheck[0]['ebay_ordersn']}',sku='{$sku}',ebay_account='{$ordercheck[0]['ebay_account']}',check_user='{$truename}',checktime=".time().",modtime=".time().",notecontent='{$notecontent}'";
			$res['code'] = '001';
			$res['data']['msg'] = '请先拦截或者审核!';
		}else{
			$sql = "UPDATE ebay_unusual_order_check SET modtime=".time().",notecontent='{$notecontent}' WHERE ebay_id={$ebay_id} and ebaydetail_id='{$detail_id}' AND sku='{$sku}'";
			if($dbcon->execute($sql)){
				$res['code'] = '200';
				$res['data']['msg'] = '添加备注成功!';
			}else{
				$res['code'] = '001';
				$res['data']['msg'] = '添加备注失败!';
			}
		}
	}
}

$dbcon->close();
echo json_encode($res);
return $res;
exit;
?>
