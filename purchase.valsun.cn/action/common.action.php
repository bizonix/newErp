<?php
 
class CommonAct{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * CommonAct::ajaxAccess()
	 * 同域ajax异步调用权限控制
	 * @return bool
	 */
	public static function ajaxAccess() {
		$act	= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod	= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		return AuthUser::checkLogin($mod, $act);	
	}
	
	/**
	 * CommonAct::actGetSkuInfo()
	 * 获取某个sku的代发货，实际库存等详细数据
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function actGetSkuInfo($sku){
		$sku	= isset($sku) ? post_check($sku): "";
		if (empty($sku)) {
			self::$errCode	= 10000;
			self::$errMsg	= "sku参数错误";
			return false;
		}
		$res			= CommonModel::getSkuInfo($sku);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $res;
	}
	
	/**
	 * CommonAct::actGetPurchaseList()
	 * 获取公司采购列表
	 * @param int $all 是否全部获取 true全部获取
	 * @return  array
	 */
	public static function actGetPurchaseList($all=false){
		$cacheName 		= md5("purchase_list");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$purchaseInfo 	= $memc_obj->get_extral($cacheName);
		if (!empty($purchaseInfo)) {
			$purchaseInfo= unserialize($purchaseInfo);
		} else {
			$purchaseInfo  = CommonModel::getPurchaseList();
			$isok 		   = $memc_obj->set_extral($cacheName, serialize($purchaseInfo));
			if (!$isok) {
				self::$errCode = 0;
				self::$errMsg = 'memcache缓存出错!';
				//return false;
			}
		}
		if ($all) {
			return $purchaseInfo;
			exit;
		}
		//获取当前用户可见采购帐号
		$res		= CommonAct::actGetPurchaseAccess();
		if	(empty($res['power_ids'])) {
			$uids	= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		} else {
			$uids	= $res['power_ids'];
		}
		if (empty($uids)) {
			self::$errCode = 10001;
			self::$errMsg = '您还尚未登录!';
			return false;
		}
		//获取可见采购的帐号信息
		$uidArr		= explode(",",$uids);
		$realArr	= array();
		foreach ($uidArr as $v) {
			foreach ($purchaseInfo as $key=>$val) {
				if ($val['userId']==$v) {
					array_push($realArr,array("userId"=>$val['userId'],"userName"=>$val['userName']));
					break;
				}
			}
		}
        return $realArr;		
	}
	
	/**
	 * CommonAct::actGetPurchaseAccess()
	 * 获取某个采购用户的细颗粒权限
	 * @param int $uid 统一用户ID 
	 * @return  array
	 */
	public static function actGetPurchaseAccess(){
		$uid	= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		if (empty($uid)) {
			self::$errCode	= 10000;
			self::$errMsg	= "统一用户ID参数有误！";
			//return false;
		}
		$res			= CommonModel::getPurchaseAccess($uid);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $res;		
	}
	
	/**
	 * CommonAct::actGetPartnerList()
	 * 获取采购供应商列表
	 * @param string $uids 采购们统一ID
	 * @return  array
	 */
	public static function actGetPartnerList(){
		$uid			= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		if (empty($uid)) {
			self::$errCode	= 10000;
			self::$errMsg	= "统一用户ID参数有误！";
			return false;
		}
		$res			= CommonAct::actGetPurchaseAccess();
		$uids			= isset($res['power_ids']) ? $res['power_ids'] : $uid;
		$res			= CommonModel::getPartnerList($uids);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $res;		
	}
	
	/**
	 * CommonAct::act_GetSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @return string
	 */
	public static function getSkuImg() {
		$skuArr = $_REQUEST["skuArr"];
		$size   = $_REQUEST['size'];
		$skuJson = json_encode($skuArr);
		$paramArr= array(
			'method'	=> 'datacenter.picture.getPicBySkuArr',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			'sku'		=> $skuJson,  //主料号
			'size'      =>  $size, 
			'picType'	=> 'G', 
		);
		$data 	= callOpenSystem($paramArr);
		return $data;
	}



	/*
		获取超大订单
	*/
	public static function getBigOrders($purid){
		$paramArr= array(
			'method'	 => 'om.showSuperOrder',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'purchaseId' => $purid
		);

		$data 	= callOpenSystem($paramArr);
		$data 	= json_decode($data, true);
        return $data;
	}	


	/*
		获取旧系统ERP超大订单
	*/
	public static function getBigOrders_old($pursename){
		$paramArr= array(
			'method'	 => 'erp.get.bigOrders',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'type'       => "get",
			'pusername' => $pursename
		);

		$data 	= callOpenSystem($paramArr,"local");
        return $data;
	}	

	/*
		获取旧系统ERP超大订单
	*/
	public static function dealBigOrder($pursename='vipchen'){
		$data = $_POST['data'];
		$type = $data['type'];
		$data = json_encode($data);
		$paramArr= array(
			'method'	 => 'erp.get.bigOrders',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'type'       => $type,
			'pusername' => $pursename,
			'data'       => $data
		);

		$data 	= callOpenSystem($paramArr,"local");
        return $data;
	}	

	/*
		获取旧系统sku 数据 
	*/
	public  function getSkuData(){
		$data = $_POST['data'];
		$data = json_encode($data);
		$pursename = $_POST['purchaseUser'];
		if(empty($pursename)){
			$pursename = $_SESSION['userCnName'];
		}

		$paramArr= array(
			'method'	 => 'erp.get.skuData',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'pusername' => $pursename,
			'data' => $data 
		);

		$data 	= callOpenSystem($paramArr,"local");
		$rtn = $this->updateSkuData($data);
        return $rtn;
	}	

	// 更新旧系统sku 数据
	public  function updateSkuData($data){
		global $dbConn;
		$dataArr = json_decode($data,true);
		foreach($dataArr as $item){
			$set = array2sql($item);
			$sql = "SELECT count(*) as totalNum from ph_sku_statistics where sku='{$item['sku']}'";
			//echo $sql;
			$sql = $dbConn->execute($sql);
			$number = $dbConn->fetch_one($sql);
			//var_dump($number);
			if($number['totalNum'] > 0){
				$sql = "update ph_sku_statistics set {$set} where sku='{$item['sku']}' ";
			}else{
				$sql = "insert into ph_sku_statistics set {$set} ";
			}
			//echo "{$sql}\n";
			$dbConn->execute($sql);
			//$this->calcAlert($item['sku']);
		}
		return 1;
	}

	public function updateCache(){
		$pursename = $_SESSION['userCnName'];
		$data = $_POST['data'];
		if(isset($_REQUEST['purchaseUser'])){
			$pursename = $_REQUEST['purchaseUser'];
		}else{
			$pursename = $_SESSION['userCnName'];
		}
		$paramArr= array(
			'method'	 => 'erp.update.cache',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'pusername' => $pursename,
			'data' => $data 
		);
		$data 	= callOpenSystem($paramArr,"local");
		$rtn = $this->updateSkuData($data);
	}


	public function getNewData(){
		global $dbConn;
		$skuArr = $_POST["skuArr"];
		$url = "http://order.valsun.cn/data_api.php?";
		$jsonArr['skuArr'] = $skuArr;
		$jsonArr['type'] = "getData";
		$updateSkuArr = curl($url,$jsonArr);
		$updateObj = json_decode($updateSkuArr,true);
		//var_dump($updateObj);

		$rtnArr = array();
		foreach($updateObj as $sku=> $data){
			$bookNum = $this->getOrderSkuNum($sku);
			$sql = "SELECT count(*) as totalNum from om_sku_daily_status where sku='{$sku}'";
			$sql = $dbConn->execute($sql);
			$skuData = $dbConn->fetch_one($sql);
			if($skuData['totalNum'] > 0){
				$sql = "select * from ph_goods_calc where sku='{$sku}'";
				$sql = $dbConn->execute($sql);
				$daysInfo = $dbConn->fetch_one($sql);
				if(empty($daysInfo['goodsdays'])){
					continue;
				}
				$stockNum = $data['number1'] + $data['number2'];
				$alertNum = $stockNum - $data['waitingsend'] - $data['shortageSendCount'] - $data['interceptSendCount'] + $bookNum;
				if($data['averageDailyCount'] <= 0){
						$is_warning = 0;
				}else{
					$alertdays = floor($alertNum / $data['averageDailyCount']);
					if($alertdays < $daysInfo['goodsdays'] && $data['averageDailyCount'] > 0){
						$is_warning = 1;
					}else{
						$is_warning = 0;
					}
				}
				$waitingSendCount = $stockNum - $data['waitingsend'];
				$sql = "update om_sku_daily_status set booknums={$bookNum},actualStockCount='{$stockNum}',averageDailyCount='{$data['averageDailyCount']}',
					waitingSendCount='{$data['waitingsend']}',interceptSendCount='{$data['interceptsendCount']}',
					shortageSendCount='{$data['shortagesendCount']}',availableStockCount='{$waitingSendCount}',
					waitingAuditCount='{$data['waitingauditCount']}',is_warning='{$is_warning}' where sku='{$sku}'";
			}else{
				$sql = "insert into om_sku_daily_status set booknums={$bookNum},actualStockCount='{$stockNum}',averageDailyCount='{$data['averageDailyCount']}',waitingSendCount='{$data['waitingsend']}',interceptSendCount='{$data['interceptsendCount']}',shortageSendCount='{$data['shortagesendCount']}',availableStockCount='{$waitingSendCount}',waitingAuditCount='{$data['waitingauditCount']}',is_warning='0',sku='{$sku}'";

			}
			//echo $sql;
			$itemFlag = array();
			$itemFlag['msg'] = $sku;
			if($dbConn->execute($sql)){
				$itemFlag['code'] = 1;
			}else{
				$itemFlag['code'] = 0;
			}
			$rtnArr[] = $itemFlag;
		}
		return json_encode($rtnArr);
	}


	public function getUsername(){
		global $dbConn;
		$userid = $_GET['userId'];
		$sql = "select global_user_name from power_global_user where global_user_id={$userid}";
		$sql = $dbConn->execute($sql);
		$name = $dbConn->fetch_one($sql);
		return $name['global_user_name'];
	}


	//计算是否预警
	public function calcAlert($sku,$type="default"){
		global $dbConn,$rmqObj; 
		$pursename = $_SESSION['userCnName'];
		$dataArr = $_POST['data'];

		if(isset($sku)){
			$condition = " a.sku = '{$sku}'";
		}else{
			if(isset($dataArr)){
				$dataStr = implode("','",$dataArr);
				$condition = " a.sku in ('{$dataStr}')";
			}else{
				$condition = " a.purchaseuser='{$pursename}' order by rand() limit 500 "; // 随机取500个料号先更新着
			}
		}

		$now = time();
		$beforetime = $now - 60*30;

		if($type == "auto"){
			$sql = "SELECT a.* FROM  `ph_sku_statistics` as a left join pc_goods as b on a.sku=b.sku where {$condition} and lastupdate<{$beforetime} ";
		}else{
			$sql = "SELECT a.* FROM  `ph_sku_statistics` as a left join pc_goods as b on a.sku=b.sku where {$condition} ";
		}

		$sql = $dbConn->execute($sql);
		$skuData = $dbConn->getResultArray($sql);
		$exchange = "purchase_info_exchange";
		foreach($skuData as $item){
			$publish_data = array();
			$publish_data['type'] = "updateSku";
			$publish_data['sku'] = $item['sku'];
			$rmqObj->single_queue_publish($exchange,json_encode($publish_data));
			$bookNum = $this->getOrderSkuNum($item['sku']);
			$alertNum = $item['stock_qty'] + $item['ow_stock']+ $bookNum - $item['salensend'] - $item['interceptnums'] - $item['autointerceptnums'] - $item['auditingnums'];
			if($item['everyday_sale'] != 0){
				$canUseDay = ($alertNum / $item['everyday_sale']);
				if($canUseDay < $item['alertDays']){
					$isAlert = 1;
				}else{
					$isAlert = 0;
				}
			}else{
					$isAlert = 0;
			}
			$sql = "UPDATE `ph_sku_statistics` SET is_alert={$isAlert},newBookNum={$bookNum} WHERE sku='{$item['sku']}'";
			$dbConn->execute($sql);
		}
		return 1;
	}


	public function getOrderSkuNum($sku){
		global $dbConn;
		$sql = "select b.count ,b.stockqty from ph_order_detail as b left join ph_order as a on b.po_id=a.id where a.is_delete=0 and a.status in(1,2,3) and b.sku='{$sku}' and b.count>0 and b.is_delete=0";
		$sql = $dbConn->execute($sql);
		$skuNum = $dbConn->getResultArray($sql);
		$totalnum = 0;
		$totalqty = 0;
		$total = 0;
		if(count($skuNum) != 0){
			foreach($skuNum as $itemNum){
				$totalnum += $itemNum["count"];
				$totalqty += $itemNum["stockqty"];
			}
			$total = $totalnum - $totalqty;
		}
		return $total;
	}

	//设置超卖sku 的状态
	public function setSkuOverSale(){
		global $dbConn;
		$sku = $_REQUEST['sku'];
		$status = $_REQUEST['status']; // 0 是上架成功，1 是下架成功
		$location = $_REQUEST['location'];
		$now = time();
		if($location == "US"){
			$sql = "UPDATE `ow_stock` SET `out_mark`='{$status}' ,mark_time='{$now}' WHERE sku='{$sku}'";
		}else{
			$sql = "UPDATE `ph_sku_statistics` SET `out_mark`='{$status}' ,mark_time='{$now}' WHERE sku='{$sku}'";
		}
		if($dbConn->execute($sql)){
			$data['errCode'] = 0;
			$data['msg'] = "成功标记";
		}else{
			$data['errCode'] = 500;
			$data['msg'] = "标记失败";
		}
		return json_encode($data);
	}


	public function setSkuOverSaleManual(){
		global $dbConn;
		$skuArr = $_REQUEST['skuArr'];
		$status = $_REQUEST['status']; // 0 是上架成功，1 是下架成功
		$location = $_REQUEST['location'];
		$skuStr = implode("','",$skuArr);
		$now = time();
		if($location == "US"){
			$sql = "UPDATE `ow_stock` SET `out_mark`='{$status}' ,mark_time='{$now}' WHERE sku='{$sku}'";
		}else{
			$sql = "UPDATE `ph_sku_statistics` SET `out_mark`='{$status}' ,mark_time='{$now}' WHERE sku in ('{$skuStr}')";
		}
		if($dbConn->execute($sql)){
			$data['errCode'] = 0;
			$data['msg'] = "成功标记";
		}else{
			$data['errCode'] = 500;
			$data['msg'] = "标记失败";
		}
		return json_encode($data);
	}

	/*
	 *调用异常点货数量
	 * */
	public function setTallyIsUse($orderArr){
		$orderJson = json_encode($orderArr);
		$paramArr= array(
			'method'	 => 'wh.updateAbnormalOrder',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'orderArr' => $orderJson
		);

		$data 	= callOpenSystem($paramArr);
        return $data;
	}

	public function secondComfirm(){
		error_reporting(-1);
		global $dbConn;
		$dataArr = $_POST['data'];
		$orderIdArr = array();
		$skuOrder = new PurchaseOrderAct();
		$tallyObj = new SkuAct(); 
		foreach($dataArr as $item){
			$onWayNum = $skuOrder->checkSkuOnWayNum($item['sku']); 
			$tallyNum = $tallyObj->getTallySkuNum($item['sku']);
			//$nowNum = $onWayNum - $tallyNum;
			$nowNum = $onWayNum;
			if($nowNum >= $item['qty']){
			//var_dump($onWayNum,$item['qty'],$item['id']);
			//if($onWayNum >= $item['qty']){
				$orderIdArr[] = $item['unorderid'];
				$tallyObj->tallySkuRecord($item['sku'],$item['qty'],1);
				$sql = "UPDATE `ph_sku_reach_record` SET status=2 where id={$item['id']}";
				$dbConn->execute($sql);
			}
		}
		$rtn = $this->setTallyIsUse($orderIdArr);
		$rtn = json_decode($rtn,true);
		if($rtn['errCode'] == 0){
			return 1;
		}else{
			return 0;
		}
	}

	public function deleteUnorder(){
		global $dbConn;
		$dataArr = $_POST['data'];
		$idArr = array();
		foreach($dataArr as $item){
			$idArr[] = $item['id'];
		}
		$idStr = implode(",",$idArr);
		$sql = "DELETE FROM `ph_sku_reach_record` WHERE id in ({$idStr})";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}
	public function adjustNum(){
		global $dbConn;
		$dataArr = $_POST['data'];
		$number = $_POST['number'];
		$skuArr = array();
		foreach($dataArr as $item){
			$skuArr[] = $item['sku'];
		}
		$skuStr = implode("','",$skuArr);
		$sql = "UPDATE `ph_tallySku_record` SET `tallyAmout`={$number} WHERE sku in ('{$skuStr}')";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}




	/**
	 * CommonAct::actgetPartnerIdBySku()
	 * 根据SKU获取供应商ID
	 * @param string $sku 料号
	 * @return 供应商ID
	 * add by wangminwei 2013-11-13
	 */
	public static function actgetPartnerIdBySku($sku){
		$rtn			= CommonModel::getPartnerIdBySku($sku);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $rtn;
	}
	
    /**
	 * CommonAct::act_categoryName()
	 * 获取产品类别名
	 * @param string $path 类别路径
	 * @return string
	 *  @author wxb 
	 * @date 2013/11/13
	 */
	public static function act_categoryName($path) {
		$ret = CommonModel::categoryName($path);
		if($ret == false){
			self::$errMsg = CommonModel::$errMsg;
			return false;			
		}
		return $ret;
	}
	
	public static function act_getCategoryInfo($pid='') {
		if(empty($pid) && $pid !== 0){//支持前端js调用,优先直接传参
			$pid = $_GET['pid'];
		}
		$ret = CommonModel::getCategoryInfo($pid);
		if($ret == false){
			self::$errMsg = CommonModel::$errMsg;
			return false;
		}
		self::$errCode = 200;
		return $ret;
	}

	//移交料号
	public function changeSku(){
		$skuArr = $_POST["skuArr"];
		$purchaseId = $_POST["receiveUserId"];
		$addUserId = $_SESSION["sysUserId"];
		$skuJson = json_encode($skuArr);

		$paramArr= array(
			'method'	 => 'pc.updatePurchaseIdBySkuArrGet',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'skuArr' => $skuJson,
			'purchaseId' => $purchaseId,
			'addUserId' => $addUserId
		);
		$data 	= callOpenSystem($paramArr);
		return $data;
	} 

	//提供登录的接口

	public function checkLogin(){
		global $dbConn;
		$username = $_GET['username'];
		$passwd = md5(md5(post_check($_GET['passwd'])));
		$sql = "SELECT global_user_id FROM `power_global_user` where global_user_login_name='{$username}' and global_user_pwd='{$passwd}'";
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		if(empty($number['global_user_id'])){
			$rtn['code'] = 404;
			$rtn['msg'] = "不存在这个用户";
		}else{
			$sql = "SELECT a.* ,b.job_name,c.dept_name FROM `power_global_user` as a left join power_job as b on a.global_user_job_path=b.job_path left join power_dept as c on b.job_dept_id=c.dept_id where a.global_user_id={$number['global_user_id']}";
			$sql = $dbConn->execute($sql);
			$userInfo = $dbConn->fetch_one($sql);
			$rtn['code'] = 0;
			$rtn['msg'] = "success....";
			$rtn['data'] = $userInfo;
		}
		return json_encode($rtn);
	}


}
?>
