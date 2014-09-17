<?php
/*
 * 仓库内部销售出入库管管理 InternalIoSellManagement.action.php
 * ADD BY chenwei 2013.8.23
 */
class InternalIoSellManagementAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/*
     * 分页总数
     */
	function act_getPageNum($where = ""){
		//调用model层获取数据
		$list =	InternalIoSellManagementModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = InternalIoSellManagementModel::$errCode;
			self::$errMsg  = InternalIoSellManagementModel::$errMsg;
			return false;
		}
	}

	/*
     * 内部使用组的 单据类型 数据显示
     */
	function  act_invoiceTypeList($where = ""){
		$listArr =	InternalIoSellManagementModel::invoiceTypeList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = InternalIoSellManagementModel :: $errCode;
			self :: $errMsg = InternalIoSellManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 单据类型付款方式联动 
     */
	function  act_changeCategoriesSkip(){
		$ret = InternalIoSellManagementModel::changeCategoriesSkip();
		if(!empty($ret)){
			self::$errCode = "200";
			return $ret;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:error";
			return false;
		}		
	}
	
	/*
     * SKU信息搜索与显示
     */
	function  act_skuInfoSearch(){
		$sku = trim($_POST['sku']);
		$ret = InternalIoSellManagementModel::getSkuInfo($sku);
		if(!empty($ret)){
			self::$errCode = "200";
			return $ret;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "getSkuInfo:error";
			return false;
		}		
	}
	
	/*
     * 库存是否足够验证
     */
	function  act_skuInventoryVerdict(){
		$sku    = $_POST['sku'];
		$where  = " WHERE sku = '{$sku}' and is_delete != 1";
		$retTwo = InternalIoSellManagementModel::skuInventoryVerdict($where);
		if(!empty($retTwo)){
			self::$errCode = "200";
			return $retTwo;
		}else{
			self :: $errCode = "4444";
			return false;
		}		
	}
	
	/*
     * wh_iostore 出入库单
     */
	function  act_iostoreList($where = ""){
		$listArr =	InternalIoSellManagementModel::iostoreList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = InternalIoSellManagementModel :: $errCode;
			self :: $errMsg = InternalIoSellManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * wh_iostoredetail  出入库单据明细
     */
	function  act_iostoredetailList($where = ""){
		$listArr =	InternalIoSellManagementModel::iostoredetailList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = InternalIoSellManagementModel :: $errCode;
			self :: $errMsg = InternalIoSellManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 审核通过
     */
	function  act_internalIoSellApproved($where){
		$list =	InternalIoSellManagementModel::internalIoSellApproved($where);		
		if($list){
			return true;
		}else{
			self :: $errCode = InternalIoSellManagementModel :: $errCode;
			self :: $errMsg = InternalIoSellManagementModel :: $errMsg;
			return false;
		}
		
	}
	
	/*
     * 弃用
     */
	function  act_internalIoSellAbandon($where){
		$list =	InternalIoSellManagementModel::internalIoSellAbandon($where);		
		if($list){
			return true;
		}else{
			self :: $errCode = InternalIoSellManagementModel :: $errCode;
			self :: $errMsg = InternalIoSellManagementModel :: $errMsg;
			return false;
		}
		
	}
	
	/*
     * 验证SKU是否存在
     */
	function  act_skuVerify(){
		$sku = $_POST['sku'];
		$where = " WHERE sku = '{$sku}' and is_delete != 1";
		$ret = InternalIoSellManagementModel::skuVerify($where);
		if(!empty($ret)){
			self::$errCode = "200";
			return $ret;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:error";
			return false;
		}		
	}
	
	/*
     * 仓位ID转换
     */
	function  act_positionIdToName($whereStr){
		$ret = InternalIoSellManagementModel::positionIdToName($whereStr);
		if(!empty($ret)){
			return $ret;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:error";
			return false;
		}		
	}
	
}
?>
