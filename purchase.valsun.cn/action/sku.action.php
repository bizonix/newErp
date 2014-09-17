<?php
/*
 *sku 相关的处理
 * add by xiaojinhua
 * */

class SkuAct{

	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	/*
	 *采购处理料号的销售状态
	 * */

	public function changeSkuStatus(){
		global $dbConn;
		$skuArr = $_POST['skuArr'];
		$data = $_POST['data'];
		$flag = array();
		foreach($skuArr as $item){
			$sql = "select count(*) as totalNum from ph_sku_status_change where sku='{$item}'";
			$sql = $dbConn->execute($sql);
			$number = $dbConn->fetch_one($sql);
			if($number['totalNum'] > 0){
				$sql = "update ph_sku_status_change set ebay_status='{$data['ebay']}',`b2b_status`='{$data['B2B']}',`amazon_status`='{$data['Amazon']}',`gongxiaoshan_status`='{$data['gongxiaoshan']}',`oversea_status`='{$data['oversea']}',`guonei_status`='{$data['guonei']}' where sku='{$item}'";
			}else{
				$sql = "insert into ph_sku_status_change set ebay_status='{$data['ebay']}',`b2b_status`='{$data['B2B']}',`amazon_status`='{$data['Amazon']}',`gongxiaoshan_status`='{$data['gongxiaoshan']}',`oversea_status`='{$data['oversea']}',`guonei_status`='{$data['guonei']}' , sku='{$item}'";
			}
			$this->batchUpdOverSeaSkuReachDay($sku, $data['oversea']);//add wangminwei
			if($dbConn->execute($sql)){
				$flag[] = 1;
			}else{
				$flag[] = 0;
			}
		}
		return json_encode($flag);
	}

	/*
	 *都能找到正常的采购订单
	 * 记录仓库点货记录
	 * xiaojinhua
	 * */
	public function  tallySkuRecord($sku,$amount,$type){
		global $dbConn;
		$sql = "select count(*) as totalNum from ph_tallySku_record where sku='{$sku}'";
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		$now = time();
		if($number["totalNum"] >= 1){//存在记录
			if($type == 1){//增加的
				$sql = "update ph_tallySku_record set tallyAmout=tallyAmout+{$amount},updatetime={$now} where sku='{$sku}'";
			}else if($type == 2){//取消的点货记录
				$tallyAmount = $this->getTallySkuNum($sku);
				$setNumber = $tallyAmount - $amount;
				if($setNumber < 0){
					$setContion = " tallyAmout=0 ";
				}else{
					$setContion = " tallyAmout=tallyAmout - {$amount} ";
				}
				$sql = "update ph_tallySku_record set {$setContion},updatetime={$now}	 where sku='{$sku}'";
			}else if($type == 3){
				$sql = "update ph_tallySku_record set tallyAmout={$amount},updatetime={$now} where sku='{$sku}'";
			}
		}else{
			$sql = "insert into ph_tallySku_record 	(`sku`, `tallyAmout`, `updatetime`) VALUES ('{$sku}',{$amount},{$now})";

		}
		$rtn = array();
		if($dbConn->execute($sql)){//记录成功
			return 1;
		}else{
			return 0;
		}
	}



	public function checkSkuInStock(){
		global $dbConn;
		$sku = $_REQUEST["sku"];
		$amount = $_REQUEST["amount"];
		$type = $_REQUEST["type"];
		$skuOrder = new PurchaseOrderAct();
		$onWayNum = $skuOrder->checkSkuOnWayNum($sku); 
		$data['msg'] = "在途数量";
		$data['num'] = $onWayNum;
		return json_encode($data);
	}

	function write_test_log($str){
		$fp = fopen('order.txt', 'a+');
		$time = date('Y-m-d H:i:s', time());
		$str = "[--$time--] === $str \n\n";
		fwrite($fp, $str);
	}



	/*
	 * 异常到货记录
	 * */
	public function addSkuReach(){
		global $dbconn;
		$orderArr 	= $_POST["orderArr"];
		$orderArr 	= json_decode($orderArr,true);
		$now 		= time();
		$sql 		= "INSERT INTO ph_sku_reach_record(sku,purchaseId, amount, totalAmount,tallymanId,note,addtime,partnerName,unOrderId) VALUES ";
		$sqlarr 	= array();
		$rollback 		= false;
		$purchaseOrder 	= new PurchaseOrderAct();
		foreach($orderArr as $orderItem){
			$sku 			= $orderItem["sku"];
			$amount 		= $orderItem["amount"];
			$unOrderId 		= $orderItem["orderid"];
			$user 			= getUserIdBySku($sku);
			$purchaseId 	= $user["purchaseId"];
			$partnerName 	= getPartnerBySku($sku);
			$tallymanId 	= $orderItem["tallymanId"];
			$onWayNum 		= $purchaseOrder->checkSkuOnWayNum($sku); //在途数量
			//$tallyAmount 	= $this->getTallySkuNum($sku); //已经点货的数量
			//$nowUnReach 	= $onWayNum - $tallyAmount;
			$unSkuNum = $amount - $onWayNum;
			$note = "当时的在途数量是{$onWayNum}个，总共到货数量{$amount}个，异常到货数量{$unSkuNum}个";
			$sqlarr[] = " ('{$sku}','{$purchaseId}',{$unSkuNum},{$amount},'{$tallymanId}','{$note}',{$now},'{$partnerName}',$unOrderId) ";
		}
		$sqlStr = implode(",",$sqlarr);
		$sql   .= $sqlStr;
		if($dbconn->execute($sql)){
			$rtn["msg"] 		= "添加数据成功";
			$rtn["errCode"] 	= 0;
		}else{
			$rtn["msg"] 		= "添加数据成功";
			$rtn["errCode"] 	= 0;
		}

		return json_encode($rtn);
	}

	/*
	 * 获取已经点货但没入库的sku数量*/

	public function getTallySkuNum($sku){
		global $dbConn;
		$sql = "SELECT tallyAmout FROM  `ph_tallySku_record` where sku='{$sku}'";
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		return $number["tallyAmout"];
	}

	//入库查询列表
	public function show_reach_list(){
		global $dbConn;
		$sql = " SELECT * FROM  `ph_order_arrive_log` LIMIT 0 , 30";
		$sql = $dbConn->execute($sql);
		$skuInfo = $dbConn->getResultArray($sql);
	}

	public function changeDays(){
		global $dbConn,$rmqObj;
		$skuArr = $_POST['skuArr'];
		$purchasedays = $_POST['purchasedays'];
		$goodsdays = $_POST['goodsdays'];
		$stockoutDays = $_POST['stockoutdays'];
		$powerUsers = array("李美琴","潘旭东","陈月葵","陈小霞","郑凤娇","覃云云","肖金华","周聪","曹莉");
		$returnInfo = array();
		$userCnName = $_SESSION['userCnName'];
		foreach($skuArr as $sku){
			$dataArr["type"] = "changeDays";
			$dataArr["data"] = array(
				"sku" => $sku,
				"purchasedays" => $purchasedays,
				"goodsdays" => $goodsdays
			);
			$flag = array();
			$sql = "select count(*) as number from ph_goods_calc where sku='{$sku}' ";
			$sql = $dbConn->execute($sql);
			$number = $dbConn->fetch_one($sql);
			if($number['number'] > 0 ){
				if(in_array($userCnName,$powerUsers)){
					$sql = "update ph_goods_calc set purchasedays={$purchasedays},goodsdays={$goodsdays} where sku='{$sku}'";
					if($dbConn->execute($sql)){
						$sql = "update ph_sku_statistics set purchaseDays={$purchasedays},alertDays={$goodsdays},stockoutDays={$stockoutDays} where sku='{$sku}'";
						$dbConn->execute($sql);
						$rmqObj->fanout_publish("changeDays",json_encode($dataArr));
						$flag['code'] = 1;
						$flag['msg'] = "更新天数成功。。。";
					}else{
						$flag['code'] = 0;
						$flag['msg'] = "更新天数失败。。。";
					}
				}else{
						$sql = "update ph_sku_statistics set stockoutDays={$stockoutDays} where sku='{$sku}'";
						$dbConn->execute($sql);
						$flag['code'] = 0;
						$flag['msg'] = "没有权限更新，请找部门经理。。。";
				}
			}else{
				$sql = "insert into ph_goods_calc (`sku`, `purchasedays`, `goodsdays`) VALUES ('{$sku}','{$purchasedays}','{$goodsdays}')";
				if($dbConn->execute($sql)){
						$rmqObj->fanout_publish("changeDays",json_encode($dataArr));
						$flag['code'] = 1;
						$flag['msg'] = "更新天数成功。。。";
				}else{
					$flag['code'] = 0;
					$flag['msg'] = "写入天数失败。。。";
				}
			}
			$flagArr[] = $flag;
		}
		return json_encode($flagArr);
	}

	public function changeSetting(){
		global $dbConn;
		$onseadays = $_POST["onseadays"];
		$stockreaddays = $_POST['stockreaddays'];
		$shipredaydays = $_POST['shipredaydays'];
		$reshelfdays = $_POST['reshelfdays'];
		$data = array(
			"onseadays" => $onseadays,
			"stockreaddays" => $stockreaddays,
			"shipredaydays" => $shipredaydays,
			"reshelfdays" => $reshelfdays
		);
		$dataJson = json_encode($data);
		$userCnName = $_SESSION['userCnName'];
		$now = time();
		$sql = "replace  INTO `ow_setting`(id,`settingJson`, `addtime`, `adduser`) VALUES (1,'{$dataJson}','{$now}','{$userCnName}')" ; 
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}




	public function getSetting(){
		global $dbConn;
		$sql = "select settingJson from ow_setting where id=1" ; 
		$sql = $dbConn->execute($sql);
		$setContion = $dbConn->fetch_one($sql);
		return json_decode($setContion['settingJson'],true);
	}

	public function changeOwdays(){
		global $dbConn;
		$safetystockdays = $_POST['safetystockdays'];
		$cycle_days = $_POST['cycle_days'];
		$purchasedays = $_POST['purchasedays'];
		$skuArr = $_POST['skuArr'];
		$flag = array();
		foreach($skuArr as $sku){
			$sql = "update ow_stock set safeStockDays='{$safetystockdays}', cycle_days='{$cycle_days}',purchasedays='{$purchasedays}' where sku='{$sku}' ";
			if($dbConn->execute($sql)){
				$flag[] = 1;
			}else{
				$flag[] = 0;
			}
		}
		return json_encode($flag);
	}

	//修改到货天数
	public function changeSkuReachDays(){
		global $dbConn;
		$skulist = $_POST['skulist'];
		$flag = array();
		$now = time();
		$type = $_POST['type'];
		if($type == "same"){
			$skuArr = $_POST['skuArr'];
			$skuStr = implode("','",$skuArr);
			$samedays = $_POST['samedays'];
			$sql = "update ph_sku_statistics set addReachtime={$now},reach_days='{$samedays}' where sku in ('{$skuStr}')";
			if($dbConn->execute($sql)){
				$flag[] = 1;
			}else{
				$flag[] = 0;
			}
		}else{
			foreach($skulist as $item){
				$sql = "update ph_sku_statistics set addReachtime={$now},reach_days='{$item['reach_days']}' where sku='{$item['sku']}'";
				if($dbConn->execute($sql)){
					$flag[] = 1;
				}else{
					$flag[] = 0;
				}
			}
		}
		return json_encode($flag);
	}


	//海外仓修改到货天数
	public function changeOwSkuReachDays(){
		global $dbConn;
		$skulist = $_POST['skulist'];
		$type = $_POST['type'];
		$flag = array();
		$now = time();
		if($type == "same"){
			$skuArr = $_POST['skuArr'];
			$skuStr = implode("','",$skuArr);
			$samedays = $_POST['samedays'];
			$sql = "update ow_stock set addReachtime={$now},reach_days='{$samedays}' where sku in ('{$skuStr}')";
			if($dbConn->execute($sql)){
				$flag[] = 1;
			}else{
				$flag[] = 0;
			}
		}else{
			foreach($skulist as $item){
				$sql = "update ow_stock set addReachtime={$now},reach_days='{$item['reach_days']}' where sku='{$item['sku']}'";
				if($dbConn->execute($sql)){
					$flag[] = 1;
				}else{
					$flag[] = 0;
				}
			}
		}
		return json_encode($flag);
	}
	
	/**
	 * 海外仓预警料号批量修改状态成平台停售，海外仓超卖系统中此料号天数自动设置为100天
	 * name:wangminwei
	 * time:2014-07-29
	 */
	public  function batchUpdOverSeaSkuReachDay($sku, $status){
		global $dbConn;
		if($status == 3){
			$sql 		= "SELECT COUNT(*) AS totalNum FROM ow_stock WHERE sku = '{$sku}'";
			$query 		= $dbConn->execute($sql);
			$rtn	 	= $dbConn->fetch_one($query);
			$totalNum   = $rtn['totalNum'];
			if($totalNum != 0){
				$upd = "UPDATE ow_stock SET reach_days = 100 WHERE sku = '{$sku}'";
				$dbConn->execute($upd);
			}
		}
	}

}

?>
