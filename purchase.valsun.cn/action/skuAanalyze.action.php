<?php
/*
 *分析sku 销售 和库存 数据
 * */
class SkuAanalyzeAct {
	public function index(){
		global $dbconn;
		$type = $_GET['type'];
		$cguserid 	= isset($_GET['pcid']) ? $_GET['pcid'] : ''; //采购id
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$partnerId	= isset($_GET['pid']) ? $_GET['pid'] : '';
		$page       = isset($_GET['page']) ? ($_GET['page']-1) : 0;
		$is_warning = $_GET['is_warn'];
		$key = $_GET['keyword']; 
		$dailyNum = $_GET['dailyNum']; //根据均量来排序
		$condition 	= '';
		if (!empty($type) && $type == "sku" && isset($key)){
			$condition  .= " AND a.sku like '%{$key}%'";
		}
		if (!empty($type) && $type == "spu" && isset($key)){
			$condition  .= " AND a.spu='{$key}'";
		}
		if ($status != '' && $status != -1){
			//$condition  .= " AND a.status = '{$status}'";
			$condition  .= " AND a.goodsStatus = '{$status}'";
		}

		if($cguserid != ""){
			$condition  .= " AND a.purchaseId = {$cguserid}";
		}else{
			$condition  .= " AND a.purchaseId = {$_SESSION['sysUserId']}";
		}

		if($partnerId != ""){
			$skuArr = $this->getSkuFromPartner($partnerId);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}

		if(isset($is_warning)){
			$condition  .= " AND b.is_warning={$is_warning}";
		}

		//$condition .= " and b.sku!='' ";

		//$condition .= " and a.purchaseId in ({$_SESSION['access_id']})";
		$orderby = '';
		if(isset($dailyNum)){
			if($dailyNum == 2){
				$orderby  .= " ,b.averageDailyCount asc ";
			}else{
				$orderby  .= " ,b.averageDailyCount desc";
			}
		}
		$page = $page * 100;
		$limit = " limit {$page},100";


		$sqlStr = "SELECT a.sku as gsku,a.goodsName,a.goodsCost,a.goodsWeight, e.global_user_name,a.purchaseId,b.*,c.purchasedays,c.goodsdays,c.outrates,a.goodsStatus FROM pc_goods as a           left join 
			om_sku_daily_status as b on a.sku=b.sku
			left join ph_goods_calc as c on b.sku=c.sku	   
			left join ph_sku_status_change as d on a.sku=d.sku
			left join power_global_user as e on a.purchaseId=e.global_user_id
			where 1 {$condition}
			order by a.purchaseId ASC 
			{$orderby}
			";
		if($_GET['debug'] == 1){
			echo $sqlStr;
		}
		$sql = $dbconn->execute($sqlStr);
		$totalNum = $dbconn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbconn->execute($sql);
		$skuInfo = $dbconn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"skuInfo"=>$skuInfo);
		return $data;

	}

	//获取包材 料号 预警信息
	public function get_materia_list(){
		global $dbconn;
		$type = $_GET['type'];
		$cguserid 	= isset($_GET['pcid']) ? $_GET['pcid'] : ''; //采购id
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$partnerId	= isset($_GET['pid']) ? $_GET['pid'] : '';
		$page       = isset($_GET['page']) ? ($_GET['page']-1) : 0;
		$is_warning = $_GET['is_warn'];
		$key = $_GET['keyword']; 
		$dailyNum = $_GET['dailyNum']; //根据均量来排序
		$condition 	= '';
		if (!empty($type) && $type == "sku" && isset($key)){
			$condition  .= " AND a.sku like '%{$key}%'";
		}
		if (!empty($type) && $type == "spu" && isset($key)){
			$condition  .= " AND a.spu='{$key}'";
		}
		if ($status != '' && $status != -1){
			//$condition  .= " AND a.status = '{$status}'";
			$condition  .= " AND a.goodsStatus = '{$status}'";
		}


		if($partnerId != ""){
			$skuArr = $this->getSkuFromPartner($partnerId);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}

		if(isset($is_warning)){
			$condition  .= " AND b.is_warning={$is_warning}";
		}
		$orderby = '';
		$page = $page * 100;
		$limit = " limit {$page},100";

		$sqlStr = "SELECT a.sku as gsku,a.goodsName,a.goodsCost,a.goodsWeight, e.global_user_name,a.purchaseId,b.*,c.purchasedays,c.goodsdays,c.outrates,a.goodsStatus FROM ebay_materia_statistics as b left join 
			pc_goods as a on a.sku=b.sku
			left join ph_goods_calc as c on b.sku=c.sku	   
			left join power_global_user as e on a.purchaseId=e.global_user_id
			where 1 {$condition}
			order by a.purchaseId ASC 
			{$orderby}";
			
		$sql = $dbconn->execute($sqlStr);
		$totalNum = $dbconn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		//echo $sql;
		$sql = $dbconn->execute($sql);
		$skuInfo = $dbconn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"skuInfo"=>$skuInfo);
		return $data;
	}

	// 获取海外仓sku 统计信息
	function overseaAlertInfo(){
		global $dbconn;
		$type 		= $_GET['type'];
		$cguserid 	= isset($_GET['pcid']) ? $_GET['pcid'] : ''; //采购id
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$partnerId	= isset($_GET['pid']) ? $_GET['pid'] : '';
		$page       = isset($_GET['page']) ? ($_GET['page']-1) : 0;
		$is_warning = $_GET['is_warn'];
		$key 		= $_GET['keyword']; 
		$dailyNum 	= $_GET['dailyNum']; //根据均量来排序
		$condition 	= '';
		if (!empty($type) && $type == "sku" && isset($key)){
			$condition  .= " AND a.sku like '%{$key}%'";
		}
		if (!empty($type) && $type == "spu" && isset($key)){
			$condition  .= " AND a.spu='{$key}'";
		}
		if($type == -1 && !empty($key)){
			$rtnParArr   = $this->getPartnerArrId($key);//获取可能匹配的供应商编号
			$parArr      = '';
			if(!empty($rtnParArr)){
				foreach($rtnParArr as $k => $v){
					$parArr .= $v['partnerId'].',';
				}
				$parArr = "(".substr($parArr, 0, strlen($parArr) - 1).")";
			}
			if($parArr != ''){
				$condition  .= "AND (a.sku LIKE '%{$key}%' OR a.goodsName LIKE '%{$key}%' OR z.partnerId IN {$parArr})";
			}else{
				$condition  .= "AND (a.sku LIKE '%{$key}%' OR a.goodsName LIKE '%{$key}%')";
			}
		}
		if ($status != '' && $status != -1){
			$condition  .= " AND c.oversea_status = '{$status}'";
		}
		if($cguserid != ''){
        	$condition  .= " AND a.OverSeaSkuCharger = {$cguserid}";
        }else{
			$overCguserArr 	= array('龚永喜', '陈珠艺', '陈剑锋', '郑珍', '王芳', '陈奕宏', '汤东东', '胡威');
	        $loginName      = $_SESSION['userCnName'];
	        $userIdArr      = '';
	        foreach($overCguserArr AS $cguser){
	        	$userId 	= getUserIdByTrueName($cguser);
	        	if(!empty($userId)){
	        		$userIdArr .= $userId.',';
	        	}
	        }
	        $userIdArr = "(".substr($userIdArr, 0, strlen($userIdArr) - 1).")";
	        if(in_array($loginName, $overCguserArr)){//如果登录人为海外仓采购员
	        	if($loginName == '龚永喜'){
	        		$condition  .= " AND a.OverSeaSkuCharger IN {$userIdArr}";
	        	}else{
		        	$aloneUserId = getUserIdByTrueName($loginName);
		        	$condition  .= " AND a.OverSeaSkuCharger = {$aloneUserId}";
	        	}
	        }else{
	        	$condition  .= " AND a.OverSeaSkuCharger IN {$userIdArr}";
	        }
	    }

		if($partnerId != ""){
			$skuArr = $this->getSkuFromPartner($partnerId);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}

		if(isset($is_warning)){
			$condition  .= " AND b.is_alert={$is_warning}";
		}
		$orderby 	= '';
		$page 		= $page * 100;
		$limit 		= " limit {$page},100";

		$sqlStr = "SELECT a.sku as gsku,a.goodsName,a.goodsCost,a.goodsWeight,e.global_user_name,f.global_user_name as OverSeaSkuCharger,a.purchaseId,b.*,a.goodsStatus,c.* FROM ow_stock as b left join 
			pc_goods as a on a.sku=b.sku
			left join ph_sku_status_change as c on a.sku=c.sku
			left join power_global_user as e on a.purchaseId=e.global_user_id
			left join power_global_user as f on a.OverSeaSkuCharger=f.global_user_id
			JOIN ph_user_partner_relation AS z ON z.sku = a.sku
			where 1=1 {$condition}
			order by b.everyday_sale desc
			{$orderby}";
		$sql 			= $dbconn->execute($sqlStr);
		$totalNum 		= $dbconn->num_rows($sql);
		$sql 			= $sqlStr."{$limit}";
		$sql 			= $dbconn->execute($sql);
		$skuInfo 		= $dbconn->getResultArray($sql);
		$data 			= array("totalNum"=>$totalNum,"skuInfo"=>$skuInfo);
		return $data;
	}
	/**
	 * 根据关键字获取可能匹配到的供应商编号
	 */
	function getPartnerArrId($name){
		global $dbconn;
		$sql 		= "SELECT partnerId FROM ph_user_partner_relation WHERE companyname LIKE '%{$name}%'";
		$sql 		= $dbconn->execute($sql);
		$parArr 	= $dbconn->getResultArray($sql);
		return $parArr;
	}

	//海外仓超卖控制
	function overseaOverControl(){
		global $dbconn;
		$type = $_GET['type'];
		$cguserid 	= isset($_GET['pcid']) ? $_GET['pcid'] : ''; //采购id
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$partnerId	= isset($_GET['pid']) ? $_GET['pid'] : '';
		$page       = isset($_GET['page']) ? ($_GET['page']-1) : 0;
		$is_warning = $_GET['is_warn'];
		$key = $_GET['keyword']; 
		$dailyNum = $_GET['dailyNum']; //根据均量来排序
		$condition 	= '';
		if (!empty($type) && $type == "sku" && isset($key)){
			$condition  .= " AND a.sku like '%{$key}%'";
		}
		if (!empty($type) && $type == "spu" && isset($key)){
			$condition  .= " AND a.spu='{$key}'";
		}
		if(empty($type)){
			$condition  .= " AND a.sku like '%{$key}%'";
		}
		if ($status != '' && $status != -1){
			$condition  .= " AND c.oversea_status = '{$status}'";
			//$condition  .= " AND a.goodsStatus = '{$status}'";
		}

		if(empty($key)){
			if($cguserid != ""){
				$condition  .= " AND a.OverSeaSkuCharger = {$cguserid}";
			}else{
				$condition  .= " AND a.OverSeaSkuCharger = {$_SESSION['sysUserId']}";
			}
		}


		if($partnerId != ""){
			$skuArr = $this->getSkuFromPartner($partnerId);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}

		if(isset($is_warning)){
			$condition  .= " AND b.is_alert={$is_warning}";
		}

		$condition  .= " AND b.out_alert=1";
		$orderby = '';
		$page = $page * 100;
		$limit = " limit {$page},100";

		$sqlStr = "SELECT a.sku as gsku,a.goodsName,a.goodsCost,a.goodsWeight,e.global_user_name,f.global_user_name as OverSeaSkuCharger,a.purchaseId,b.*,a.goodsStatus,c.* FROM ow_stock as b left join 
			pc_goods as a on a.sku=b.sku
			left join ph_sku_status_change as c on a.sku=c.sku
			left join power_global_user as e on a.purchaseId=e.global_user_id
			left join power_global_user as f on a.OverSeaSkuCharger=f.global_user_id
			where a.sku!='' {$condition}
			order by b.everyday_sale desc
			{$orderby}";
		//echo $sqlStr;
			
		$sql = $dbconn->execute($sqlStr);
		$totalNum = $dbconn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		//echo $sql;
		$sql = $dbconn->execute($sql);
		$skuInfo = $dbconn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"skuInfo"=>$skuInfo);
		return $data;
	}
	

	// 通过供应商id 获取sku
	function getSkuFromPartner($partnerId){
		global $dbconn;
		$sql = "select sku from ph_user_partner_relation where partnerId={$partnerId}"; 
		$sql = $dbconn->execute($sql);
		$skuArr = $dbconn->getResultArray($sql);
		//$skuArr = array_values($skuArr); 	
		$skuArrVal = array();
		foreach($skuArr as $item){
			$skuArrVal[] = $item['sku'];
		}
		return $skuArrVal;
	}

	// 获取销售平台信息
	function getSalePlatform(){
		global $dbConn;
		$sql = "select * from ph_sale_platform ";
		$sql = $dbConn->execute($sql);
		$platformInfo = $dbConn->getResultArray($sql);
		return $platformInfo;
	}
} 
?>
