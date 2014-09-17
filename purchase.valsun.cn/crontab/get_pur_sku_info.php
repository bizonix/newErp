<?php
function get_PurSkuInfo($skulist){
	for ($i=0; $i < count($skulist); $i++) { 
		$sku .= $skulist[$i].',';
	}
	$sku = substr($sku, 0,strlen($sku) - 1);
	$paramArr = array(
		'method' 	=> 'purchase.erp.purskuinfo',  //API名称
		'format' 	=> 'json',  //返回格式
		'v' 		=> '1.0',   //API版本号
		'type' 		=> 'pursku',
		'username'	=> 'purchase',
		'sku' 		=> $sku//sku数组
	);
	$rtn = callOpenSystem($paramArr);
}

//取SKU第一次售出时间
function get_firstSaleTime($sku){
	$paramArr = array(
		'method' 	=> 'purchase.erp.getfirstsaletime',  //API名称
		'format' 	=> 'json',  //返回格式
		'v' 		=> '1.0',   //API版本号
		'type' 		=> 'firstSaleTime',
		'username'	=> 'purchase',
		'sku' 		=> $sku
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//取SKU最近一次售出时间
function get_lastSaleTime($sku){
	$paramArr = array(
		'method' 	=> 'purchase.erp.getlastsaletime',  //API名称
		'format' 	=> 'json',  //返回格式
		'v' 		=> '1.0',   //API版本号
		'type' 		=> 'lastSaleTime',
		'username'	=> 'purchase',
		'sku' 		=> $sku//sku数组
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//取SKU时间段内销售数量
function get_saleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale){
	$paramArr = array(
		'method' 		=> 'purchase.erp.getsalenum',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'saleNum',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'startTime'     => $start1,
		'endTime'		=> $end1,
		'warehouseid' 	=> $warehouse_id,
		'everydaysale'	=> $everyday_sale 
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//待发货数量
function get_waitSendNum($sku, $warehouse_id){
	$paramArr = array(
		'method' 		=> 'purchase.erp.getwaitsendnum',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'waitSendNum',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'warehouseid' 	=> $warehouse_id
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//自动拦截数量
function get_autointerceptNum($sku, $warehouse_id){
	$paramArr = array(
		'method' 		=> 'purchase.erp.getautointerceptnum',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'autointercetpNum',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'warehouseid' 	=> $warehouse_id
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//取拦截数量
function get_interceptallNum($sku, $warehouse_id){
	$paramArr = array(
		'method' 		=> 'purchase.erp.getinterceptallnum',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'interceptallNum',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'warehouseid' 	=> $warehouse_id
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}
//待审核数量
function get_auditingallNum($sku, $warehouse_id){
	$paramArr = array(
		'method' 		=> 'purchase.erp.getauditingallnum',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'auditingallNum',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'warehouseid' 	=> $warehouse_id
	);
	$rtn = callOpenSystem($paramArr);
	return $rtn;
}

//从仓库系统获取异常到货订单
function get_unusualOrder($purid, $condition, $page){
	$paramArr = array(
		'method' 		=> 'wh.erp.getunusualorder',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'unusualOrder',
		'username'	 	=> 'purchase',
		'where'			=> base64_encode($condition),
		'purid' 		=> $purid,//采购员
		'page'          => $page //申请页面
	);
	$rtn = callOpenSystem($paramArr);
	$rtndata = json_decode($rtn,true);
	return $rtndata;
}
//调用API更新异常订单更新状态
function update_unusualOrderSataus($purid, $oid, $category, $recordnumber){
	$paramArr = array(
		'method' 		=> 'wh.erp.updateunusualordersatatus',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'unusualOrderSataus',
		'username'	 	=> 'purchase',
		'purid' 		=> $purid,//采购员
		'oid'           => base64_encode($oid), //编号
		'category'		=> $category,//处理类型
		'recordnumber'	=> $recordnumber //跟踪号
	);
	$rtn = callOpenSystem($paramArr);
	$rtndata = json_decode($rtn,true);
	return $rtn;
}
//获取QC系统相关数据==不良品、待定、待退回列表
function get_qcData($purid, $condition, $page, $type){
	$paramArr = array(
		'method' 		=> 'qc.erp.get'.$type,  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'username'	 	=> 'purchase',
		'where'			=> base64_encode($condition),
		'purid' 		=> $purid,//采购员
		'page'          => $page //申请页面
	);
	$rtn = callOpenSystem($paramArr);
	$rtndata = json_decode($rtn,true);
	return $rtndata;
}
//处理不良品API操作更新到QC系统==scrappedStatus 1为报废，2为内部处理，3为待退回
function update_qcBadGoodData($defectiveId, $infoId, $num, $note, $scrappedStatus){
	$paramArr = array(
		'method' 		 => 'qc.afterAuditDefPros',  //API名称
		'format' 		 => 'json',  //返回格式
		'v' 			 => '1.0',  //API版本号
		'username'	 	 => 'purchase',
		'defectiveId'	 => $defectiveId,//编号
		'infoId' 		 => $infoId,//记录号
		'num'            => $num,//处理数量
		'note'			 => $note,//备注
		'scrappedStatus' => $scrappedStatus//状态
	);
	$rtn = callOpenSystem($paramArr);
	$rtndata = json_decode($rtn,true);
	return $rtndata;
}

//处理退回列表API操作更新到QC系统 采购审核
function update_qcReturnGoodData($id){
	$paramArr = array(
		'method' 		 => 'qc.auditRetPros',  //API名称
		'format' 		 => 'json',  //返回格式
		'v' 			 => '1.0',  //API版本号
		'username'	 	 => 'purchase',
		'returnId'		 => $id,//编号
	);
	$rtn = callOpenSystem($paramArr,'local');
	$rtndata = json_decode($rtn,true);
	return $rtndata;
}

//处理待定列表API操作更新到QC系统==type==>修改图片、回测、退回
function update_qcPendGoodData($id, $type, $sysUserId,$note){
	$paramArr = array(
		'method' 		 => 'qc.operatePenPros',  //API名称
		'format' 		 => 'json',  //返回格式
		'v' 			 => '1.0',  //API版本号
		'username'	 	 => 'purchase',
		'pendingId'		 => $id,//编号
		'status' 	     => $type,//状态
		'sysUserId'		 => $sysUserId, //审核人员ID
		'note'			 => $note
	);
	$rtn = callOpenSystem($paramArr);
	$rtndata = json_decode($rtn,true);
	return $rtndata['data'];
}

//模拟调用采购系统到货入库API返回仓库系统=====仓库系统调用入库方法
function get_stockIn($sku, $amount){
	$paramArr = array(
		'method' 		=> 'wh.erp.getstockin',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'type' 			=> 'stockIn',
		'username'	 	=> 'purchase',
		'sku' 			=> $sku,
		'amount' 		=> $amount
	);
	$rtn 	 = callOpenSystem($paramArr);
	return $rtn;
}
//推送需要下单的订单编号到芬哲ERP add by wangminwei 2013.11.23
function put_orderIdToFinejo($orderid){
	$paramArr = array(
		'method' 		=> 'purchase.erp.downOrder',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'username'	 	=> 'purchase',
		'orderid' 		=> $orderid
	);
	$rtn 	 = callOpenSystem($paramArr,'local');
	return $rtn;
}
//审核超大订单
function auitSupperOrder($data){
	echo "############";
	$paramArr = array(
		'method' 		=> 'order.system.auitSuperOrder',  //API名称
		'format' 		=> 'json',  //返回格式
		'v' 			=> '1.0',  //API版本号
		'username'	 	=> 'purchase',
		'orderid' 		=> $data['orderid'],
		'sku' 		    => $data['sku'],
		'type'          => $data['type'],
		'status' 		=> $data['status'],
		'pcontent'      => $data['content'],
		'purchaseId'    => $data['purid'],
		'storeId'       => '1'
	);
	$rtn 	 = callOpenSystem($paramArr);
	return $rtn;
}
?>