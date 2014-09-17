<?php
/*
 * 仓库基础信息管理(action)
 * ADD BY chenwei 2013.8.13
 */
class WarehouseManagementAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	

	/*
     * 分页总数
     */
	function act_getPageNum($where){
		//调用model层获取数据
		$list =	WarehouseManagementModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = WarehouseManagementModel::$errCode;
			self::$errMsg  = WarehouseManagementModel::$errMsg;
			return false;
		}
	}

	/*
     * 仓库名称管理数据查询
     */
	function  act_warehouseManagementList($where){
		$list =	WarehouseManagementModel::warehouseManagementModelList($where);		
		if($list){
			return $list;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 验证 $table			=	"wh_store"
     */
	function  act_existAct(){
		$whData = trim($_POST['whData']);
		$name	= trim($_POST['name']);
		$where = "WHERE ".$name."='".$whData."'";
		$ret = WarehouseManagementModel::existModel($where);
		if(empty($ret)){
			self::$errCode = "200";
			self::$errMsg  = "可以使用";
		}else{
			self::$errCode = "1111";
			return $ret;
		}		
	}
	
	/*
     * 仓库名称（提交）
     */
	function  act_warehouseSubmit($where,$type){
		$list =	WarehouseManagementModel::warehouseSubmit($where,$type);		
		if($list){
			return true;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 是否启用
     */
	function  act_isEnabled(){
		$whData = trim($_POST['whData']);
		$name	= trim($_POST['name']);
		$whereId = $_POST['whereId'];
		$where = "set ".$name."='".$whData."' where id =".$whereId;
		$ret = WarehouseManagementModel::warehouseSubmit($where,"edit");
		if($ret){
			self::$errCode = "200";
		}else{
			self::$errCode = "4444";
		}		
	}
	
	/*
     * 出入库类型管理数据查询
     */
	function  act_whIoTypeList($where){
		$list =	WarehouseManagementModel::whIoTypeModelList($where);		
		if($list){
			return $list;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 验证 $table2			=	"wh_iotype"
     */
	function  act_whIoTypeExistAct(){
		$whData = trim($_POST['whData']);
		$name	= trim($_POST['name']);
		$where = "WHERE ".$name."='".$whData."'";
		$ret = WarehouseManagementModel::whIoTypeExistModel($where);
		if(empty($ret)){
			self::$errCode = "200";
			self::$errMsg  = "可以使用";
		}else{
			self::$errCode = "1111";
			return $ret;
		}		
	}
	
	/*
     * 出入库类型 添加、编辑
     */
	function  act_whIoTypeSubmit($where,$type){
		$list =	WarehouseManagementModel::whIoTypeSubmit($where,$type);		
		if($list){
			return true;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 删除
     */
	function  act_whIoTypeDel($where){
		$list =	WarehouseManagementModel::whIoTypeDel($where);		
		if($list){
			return true;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 出入库单据类型管理数据查询
     */
	function  act_whIoInvoicesTypeList($where = ''){
		$list =	WarehouseManagementModel::whIoInvoicesTypeModelList($where);		
		if($list){
			return $list;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 出入库单据类型 添加、编辑
     */
	function  act_whIoInvoicesTypeSubmit($where,$type){
		$list =	WarehouseManagementModel::whIoInvoicesTypeSubmit($where,$type);		
		if($list){
			return true;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 单据验证 
     */
	function  act_whIoInvoicesTypeExistAct(){
		$whData = trim($_POST['whData']);
		$name	= trim($_POST['name']);
		$where = "WHERE ".$name."='".$whData."'";
		$ret = WarehouseManagementModel::whIoInvoicesTypeExistModel($where);
		if(empty($ret)){
			self::$errCode = "200";
			self::$errMsg  = "可以使用";
		}else{
			self::$errCode = "1111";
			return $ret;
		}		
	}
	
	/*
     * 单据删除
     */
	function  act_whIoInvoicesTypeDel($where){
		$list =	WarehouseManagementModel::whIoInvoicesTypeDel($where);		
		if($list){
			return true;
		}else{
			self :: $errCode = WarehouseManagementModel :: $errCode;
			self :: $errMsg = WarehouseManagementModel :: $errMsg;
			return false;
		}
	}
	
}
?>
