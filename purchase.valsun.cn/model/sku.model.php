<?php

class SkuModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public function	__construct(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}

	public function changeSkuStatus(){
		$skuArr = $_POST["skuArr"];
		$status = $_POST["status"];
		$flag = array(); //标记 是否成功
		foreach($skuArr as $sku){
			$rtn = $this->getSkuStatus($sku);
			if(isset($rtn["sku"])){
				if($status != $rtn['status']){
					$flagNow = $this->updateStatus($sku,$status);
				}
			}else{
				$flagNow = $this->addSkuStatus($sku);
			}

			$flag[] = $flagNow;
		}
		return $flag;
	}

	public function getSkuStatus($sku){//获取sku 现在的销售状态
		global $dbConn;
		$sql = "select sku,status from ph_sku_status_change where sku='{$sku}' limit 1";
		$sql = $dbConn->execute($sql);
		$rtn = $dbConn->fetch_one($sql);
		return $rtn;
	}

	public function addSkuStatus($sku){ 
		global $dbConn;
		$now = time();
		$user = $_SESSION['userName'];
		$sql = "insert into ph_sku_status_change (sku,status,start_time,start_user) values ('{$sku}',1,{$now},'{$user}')";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}

	public function updateStatus($sku,$status){
		global $dbConn;
		$now = time();
		$user = $_SESSION['userName'];
		if($status == 1 ){ // 设置领域
			$setfield = "start_time={$now},start_user='{$user}'";
		}else{
			$setfield = "stop_time={$now},stop_user='{$user}'";
		}
		$sql = "UPDATE `ph_sku_status_change` SET ".$setfield." ,status={$status} WHERE sku='{$sku}'";
		//echo $sql;
		if($dbConn->execute($sql)){
			$this->updateSkuName($sku,$status);
			return 1;
		}else{
			return 0;
		}
	}

	public function updateSkuName($sku,$status){
		global $dbConn;
		$sql = "select goodsName  from pc_goods where sku='{$sku}'";
		$sql = $dbConn->execute($sql);
		$goodsName = $dbConn->fetch_one($sql);
		if($status == 1){// 在线
			$search = array("停售","--暂时停售","--永久停售");
			$replace = array("","","");
			$nowName = str_replace($search,"",$goodsName['goodsName']);
		}else if($status == 2){//暂时停售
			$nowName = $goodsName['goodsName']."--暂时停售";
		}else if($status == 3){//永久停售
			$nowName = $goodsName['goodsName']."--永久停售";
		}
		$sql = "UPDATE pc_goods set goodsName='{$nowName}' where sku='{$sku}'";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}

	}

}
?>
