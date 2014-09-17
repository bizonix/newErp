<?php
class PurToWhAct{
	public static $dbConn;
	public static $errCode	= 0;
	public static $errMsg	= "";
	
	//返回仓库重点 add by wangminwei 2014-04-03
	public static function unusualSkuReturnWh(){
		$unOrderIdArr = $_POST["unOrderIdArr"];
		$unOrderId    = implode(',',$unOrderIdArr);
		$paramArr = array(
			'method' 		=> 'purchase.unusualSkuReturnWh',
			'format' 		=> 'json',
			'v' 			=> '1.0',
			'username'		=> C('OPEN_SYS_USER'),
			'unOrderIdArr' 	=> $unOrderId
		);
		$rtnData = callOpenSystem($paramArr);
		$rtn	 = json_decode($rtnData,true);
		if($rtn['errCode'] == 200){//仓库系统更新成功
			$rtnResult  = PurToWhModel::delUnusualSku($unOrderIdArr);
			if($rtnResult){
				$result['code'] = '1';
				$result['msg'] 	= '返回仓库重点成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '仓库状态更新成功,采购系统状态更新失败';
			}
		}else{
			$result['code'] = '3';
			$result['msg'] 	= '返回仓库重点失败';
		}
		return json_encode($result);
	}
	
	//判断异常到货记录是否已存在采购系统 add by wangminwei 2014-04-03
	public static function isExistUnusualSku($unOrderId){
		$rtnData  	= PurToWhModel::isExistUnusualSku($unOrderId);
		return $rtnData;
	}

	//显示收货管理列表 add by wangminwei 2014-04-08
	public static function getReceiptGoods($condition, $page){
		$rtnData  	= PurToWhModel::getReceiptGoods($condition, $page);
		return $rtnData;
	}

	//根据id获取信息 add by wangminwei 2014-04-08
	public static function getReceiptGoodsById($id){
		$rtnData  	= PurToWhModel::getReceiptGoodsById($id);
		return $rtnData;
	}

	//采购员添加收货产体信息
	public static function add(){
		$data 		= $_POST['data'];
		if(!empty($data)){
			$rtnData  	= PurToWhModel::add($data);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '收货订单信息添加成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '收货订单信息添加失败';
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}

	//采购员添加收货主体信息,订单号自动填充
	public static function autoAdd(){
		$data 		= $_POST['ordersn'];
		if(!empty($data)){
			$rtnData  	= PurToWhModel::autoAdd($data);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '收货订单信息添加成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '收货订单信息添加失败';
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}

	//仓库员添加收货信息
	public static function addDetail(){
		$data 		= $_POST['data'];
		if(!empty($data)){
			$rtnData  	= PurToWhModel::addDetail($data);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '收货料号信息添加成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '收货料号信息添加失败';
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}
	//删除收货信息记录
	public static function delete(){
		$data 		= $_POST['data'];
		if(!empty($data)){
			$rtnData  	= PurToWhModel::delete($data);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '删除收货记录成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '删除收货记录失败';
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}

	//财务审核收货信息记录
	public static function auit(){
		$data 		= $_POST['data'];
		$paytime    = $_POST['paytime'];
		$payaway    = $_POST['paymethod'];
		$fee        = $_POST['fee'];
		if(!empty($data)){
			$rtnData  	= PurToWhModel::auit($data, $paytime, $payaway, $fee);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '审核收货成功';
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '审核收货失败';
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}

	//获取采购订单详情
	public static function getOrderInfo(){
		$ordersn 	= $_POST['ordersn'];
		$rtnData  	= PurToWhModel::getOrderInfo($ordersn);
		return json_encode($rtnData);
	}

	//更新到货入库数量记录
	public static function editDetail(){
		$mainid 		= $_POST['mainid'];
		$detailid 		= $_POST['detailid'];
		$beforecount 	= $_POST['beforecount'];
		$aftercount  	= $_POST['aftercount'];
		$rtnData  		= PurToWhModel::editDetail($mainid, $detailid, $beforecount, $aftercount);
		if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '更新收货记录成功';
		}else{
			$result['code'] = '2';
			$result['msg'] 	= '更新收货记录失败';
		}
		return json_encode($result);
	}

	//收货管理报表导出
	public static function exportData($condition){
		$rtnData  	= PurToWhModel::exportData($condition);
		return $rtnData;
	}

	//根据id获取收货明细信息
	public static function getReceiptGoodsDetailById($id){
		$rtnData  	= PurToWhModel::getReceiptGoodsDetailById($id);
		return $rtnData;
	}

	//根据料号返回料号描述
	public static function getSkuName($sku){
		$rtnData  	= PurToWhModel::getSkuName($sku);
		return $rtnData;
	}

	//获取旧ERP系统超大订单
	public static function getBigOrder($cguser){
		$paramArr= array(
			'method'	=> 'erp.getBigOrders', //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号/
			'username'  => C('OPEN_SYS_USER'),
			'cguser'    => $cguser,
			'type'      => 'getBigOrder'
		);
		$data 	= callOpenSystem($paramArr, 'local');
		$data 	= json_decode($data, true);
        return $data;
	}

	//审核拦截超大订单(旧ERP系统)
	public static function operBigOrder(){
		$data 		= $_POST['data'];
		$note       = !empty($data[0]['note']) ? $data[0]['note'] : 'erp';
		$paramArr 	= array(
			'method'		=> 'pur.AuitOrInterceptBigOrders', //API名称
			'format'		=> 'json',  //返回格式
			'v'				=> '1.0',   //API版本号/
			'username'  	=> C('OPEN_SYS_USER'),
			'ebay_id'   	=> $data[0]['ebayid'],
			'detail_id' 	=> $data[0]['detailid'],
			'sku'			=> $data[0]['sku'],
			'check_status' 	=> $data[0]['status'],
			'cguser'		=> $data[0]['cguser'],
			'pcontent'      => $note
		);
		$data 		= callOpenSystem($paramArr, 'local');
		$rtnData 	= json_decode($data, true);
        $code 		= $rtnData['code'];
        $msg        = $rtnData['msg'];
        if($code == 200){
				$result['code'] = '1';
				$result['msg'] 	= '操作成功';
		}else{
			$result['code'] = '2';
			$result['msg'] 	= $msg;
		}
		return json_encode($result);
	}

	//异常到货记录页面加载时重新计算待确认数量,仅限于状态为未处理 add by wangminwei 2014-04-17
	public function updUnusualSkuConfirmQty($data){
		$skuact 	= new SkuAct();
		$purorder   = new PurchaseOrderAct();
		if(!empty($data)){
			foreach($data as $k => $v){
				$id          		= $v['id'];
				$sku		 		= $v['sku'];
				$status      		= $v['status'];
				$totalAmount 		= $v['totalAmount'];//总共到货数量
				$onWayAmount    	= $purorder->checkSkuOnWayNum($sku);//在途数量
				$waitOnAmount  		= $skuact->getTallySkuNum($sku);//等待上架数量
				$waitOnAmount  		= !empty($waitOnAmount) ? $waitOnAmount : 0;
				$confirmAmount   	= $totalAmount + $waitOnAmount - $onWayAmount;
				if($status == 0){
					PurToWhModel::updUnusualSkuConfirmQty($id, $confirmAmount);//重新计算待确认数量
				}
			}
		}
	}

	//返回符合条件迁入已到货数量的订单料号信息
	public function getMoveOrderSkuInfo(){
		$ordersn = $_POST['ordersn'];
		$sku     = $_POST['sku'];
		$rtnData = array();
		$mark    = PurToWhModel::isExistOrderSku($ordersn, $sku);
		if($mark == 0){
			$result['code'] = '404';
			$result['msg'] 	= '订单料号信息不存在';
		}else{
			$info 		= PurToWhModel::rtnOrderSkuStockQty($ordersn, $sku);//返回已入库数量
			$data     	= PurToWhModel::getMoveOrdeSkuInfo($ordersn, $sku);//返回符合条件的信息
			if(empty($data)){
				$result['code'] = '403';
				$result['msg'] 	= '没有满足迁入的订单料号信息';
			}else{
				$rtnData[0] 	= $info;
				$rtnData[1] 	= $data;
				$result['code'] = '200';
				$result['msg'] 	= $rtnData;
			}
			
		}
		return json_encode($result);
	}

	//订单料号到货数量跨订单迁移
	public static function moveOrderSku(){
		$ordersn  = $_POST['ordersn'];
		$sku      = $_POST['sku'];
		$stockqty = $_POST['stockqty'];
		$amount   = $_POST['outtotalamount'];//迁出数量
		$dataArr  = $_POST['dataArr'];//迁入明细
		$rtn      = PurToWhModel::updMoveOrderStatusAndInfo($dataArr, $ordersn, $sku, $stockqty, $amount);
		if($rtn){
			$result['code'] = '200';
			$result['msg'] 	= '跨订单迁移成功';
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '跨订单迁移失败';
		}
		return json_encode($result);
	}
	
	/**
	 * 获取海外料号新品迁移记录数
	 */
	public static function getOverSkuMoveLogCount(){
		$totalnum 			= PurToWhModel::getOverSkuMoveLogCount();
		$result['totalnum'] = $totalnum;
		return json_encode($result);
	}
	
	/**
	 * 获取海外料号新品迁移记录信息返回更新到旧ERP系统海外采购人
	 */
	public function getOverSkuMoveLogInfo(){
		$page    = isset($_GET['page']) ? $_GET['page'] : 1;
		$pagenum = isset($_GET['pagenum']) ? $_GET['pagenum'] : 200;
		$rtnData = PurToWhModel::getOverSkuMoveLogInfo($page, $pagenum);
		if(!empty($rtnData)){
			$result['code'] = '200';
			$result['data'] = $rtnData;
		}else{
			$result['code'] = '404';
		}
		return json_encode($result);
	}
	
	//更新收货管理表订单料号状态
	public static function updReceiptStatus(){
		$orderId 	= isset($_POST['orderId']) ? $_POST['orderId'] : NULL;
		$orderstu  	= isset($_POST['orderstu']) ? $_POST['orderstu'] : NULL;
		if(empty($orderId) || empty($orderstu)){
			$result['code'] = '404';
			$result['msg']  = '参数有误';
		}
		$rtn = PurToWhModel::updReceiptStatus($orderId, $orderstu);
		if($rtn){
			$result['code'] = '200';
			$result['msg'] 	= '状态更新成功';
		}else{
			$result['code'] = '502';
			$result['msg'] 	= '状态更新失败';
		}
		return json_encode($result);
	}
	
	/**
	 * 批量更新收货管理表订单状态
	 */
	public static function batchUpdate(){
		$dataArr 	= isset($_POST['data']) ? $_POST['data'] : NULL;
		$status 	= isset($_POST['status']) ? $_POST['status'] : NULL;
		if(empty($dataArr) || empty($status)){
			$result['code'] = '404';
			$result['msg']  = '参数有误';
			return json_encode($result);
			exit();
		}
		$idStr  = '';
		foreach($dataArr AS $k => $v){
			$idStr .= $v['id'].',';
		}
		$idList = '('.substr($idStr, 0, strlen($idStr) - 1).')';
		$rtn 	= PurToWhModel::batchUpdStatus($idList, $status);
		if($rtn){
			$result['code'] = '200';
			$result['msg'] 	= '状态更新成功';
		}else{
			$result['code'] = '502';
			$result['msg'] 	= '状态更新失败';
		}
		return json_encode($result); 
	}
	
	/**
	 * 新品迁移海外仓预警
	 *
	 */
	public static function moveOverSeaSku(){
		$skuArr = isset($_POST['skuArr']) ? $_POST['skuArr'] : NULL;
		if(empty($skuArr)){
			$result['code'] = '404';
			$result['msg']  = '参数有误';
			echo json_encode($result);
		}
		$rtn = PurToWhModel::moveOverSeaSku($skuArr);
		if($rtn){
			$result['code'] = '200';
			$result['msg']  = '料号迁移成功';
		}else{
			$result['code'] = '404';
			$result['msg']  = '料号迁移失败';
		}
		return json_encode($result);
	}
	
	/**
	 * 接收仓库系统海外仓备货单复核==>同步数量到采购系统备货
	 */
	public static function receiptOverSeaUpdBOrderAmount(){
		$ordersn  = isset($_GET['ordersn']) ? $_GET['ordersn'] : '';
		$sku      = isset($_GET['sku']) ? $_GET['sku'] : '';
		$amount   = isset($_GET['amount']) ? $_GET['amount'] : '';
		if(empty($ordersn) || empty($sku) || empty($amount)){
			$result['code'] = '404';
			$result['msg']  = '参数传递有误';
			return json_encode($result);
		}
		$rtn = PurToWhModel::receiptOverSeaUpdBOrderAmount($ordersn, $sku, $amount);
		if($rtn){
			$result['code'] = '200';
			$result['msg']  = '同步更新采购系统备货单成功';
			return json_encode($result);
		}else{
			$result['code'] = '502';
			$result['msg']  = '同步更新采购系统备货单失败';
			return json_encode($result);
		}
	}
	
	/*
	 * 根据订单号获取订单明细
	 */
	public static function getPurOrderDetailByOrdersn(){
		$orderSn  = isset($_GET['orderSn']) ? $_GET['orderSn'] : '';
		$rtnData  = array();
		if(!empty($orderSn)){
			$data = PurToWhModel::getPurOrderDetailByOrdersn($orderSn);
			if(!empty($data)){
				$rtnData = $data;
			}
		}
		return json_encode($rtnData);
	}
	
	/**
	 * 批量删除B仓备货单
	 */
	public static function batchDelOwOrder(){
		$pid = !empty($_POST['idArr']) ? $_POST['idArr'] : NULL;
		if(empty($pid)) {
			$result['code'] = '404';
			$result['msg']  = '参数有误';
			return json_encode($result);
			exit();
		}
		$rtn = PurToWhModel::batchDelOwOrder($pid);
		if($rtn){
			$result['code'] = '200';
			$result['msg']  = '删除备货单成功';
			return json_encode($result);
		}else{
			$result['code'] = '502';
			$result['msg']  = '删除备货单失败';
			return json_encode($result);
		}
	}
	/**
	 * 采购员-供应商下单月度搜索
	 * Enter description here ...
	 */
	public function mothIndex(){
		$cguser			= isset($_POST['cguser']) ? $_POST['cguser'] : '';
		$parnter        = isset($_POST['parnter']) ? $_POST['parnter'] : '';
		$startTime		= isset($_POST['startTime']) ? $_POST['startTime'] : '';
		$endTime		= isset($_POST['endTime']) ? $_POST['endTime'] : '';
		$loginname          = $_SESSION['userCnName'];
		$condition 			= '';
		if(empty($cguser) || empty($parnter) || empty($startTime) || empty($endTime)){
			$result['code'] = '202';
			$result['msg']  = '参数有误';
			return json_encode($result);
			exit();
		}
		if (!empty($cguser)){
			$condition  .= " AND cguser = '{$cguser}'";
		}
		if (!empty($parnter)){
			$condition  .= " AND parnter = '{$parnter}'";
		}
		if (!empty($startTime) && $endTime >= $startTime){
			$serstart = strpos($startTime, ':')!==false ? strtotime($startTime) : strtotime($startTime." 00:00:00");
			$serend   = strpos($endTime, ':')!==false ? strtotime($endTime) : strtotime($endTime." 23:59:59");
			$condition  .= " AND purtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		if(strtotime($startTime) > strtotime($endTime)){
			$result['code'] = '405';
			$result['msg']  = '时间范围有误，请选择正确的时间范围';
			return json_encode($result);
		}
		$rtn = PurToWhModel::getReceiptMothInfo($condition);
		if($rtn){
			$result['code'] = '200';
			$result['msg']  = '获取数据成功';
			$result['data'] = $rtn;
			return json_encode($result);
		}else{
			$result['code'] = '404';
			$result['msg']  = '没有结款订单数据';
			return json_encode($result);
		}
	}
	
	/*
	 * 批量处理周、月结订单
	 */
	public function batchAuit(){
		$data 		= !empty($_POST['dataArr']) ? $_POST['dataArr'] : '';
		$paytime    = !empty($_POST['paytime']) ? $_POST['paytime'] : '';
		$payaway    = !empty($_POST['paymethod']) ? $_POST['paymethod'] : '';
		$fee        = !empty($_POST['fee']) ? $_POST['fee'] : '';
		if(empty($payaway) || empty($paytime) || empty($fee)){
			$result['code'] = '402';
			$result['msg'] 	= '参数有误';
			return json_encode($result);
		}
		if(!empty($data)){
			$orderArr = '';
			foreach($data as $k => $v){
				$ordersn 	= $v['ordersn'];
				$orderArr  .= "'".$ordersn."',";
			}
			$orderArr = "(".substr($orderArr, 0, strlen($orderArr) - 1).")";
			$rtnData  = PurToWhModel::batchAuit($orderArr, $paytime, $payaway, $fee);
			if($rtnData){
				$result['code'] = '1';
				$result['msg'] 	= '审核成功';
				BaseModel::commit();
			}else{
				$result['code'] = '2';
				$result['msg'] 	= '审核失败';
				BaseModel::rollback();
			}
		}else{
			$result['code'] = '404';
			$result['msg'] 	= '没有数据';
		}
		return json_encode($result);
	}
}

?>
