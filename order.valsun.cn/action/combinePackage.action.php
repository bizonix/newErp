<?php
/*
* 合并包裹功能
* @author by heminghua
* @last modified by Herman.Xi @20131213
*/
class combinePackageAct{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
		
    }
	
	public function act_combinePackage(){
		//global $memc_obj;
		
		$str = isset($_POST['str'])?$_POST['str']:"";
		//var_dump($str); exit;
		$id_array = explode(",",$str);
		$id_array = array_filter($id_array);
		
		//$lists = $memc_obj->get_extral('trans_system_carrier');
		$lists = CommonModel::getCarrierList(0);
		$carrierId = '';
		$carrierIds = array();
		foreach($lists as $list){
			if(in_array($list['carrierNameCn'], array('中国邮政平邮','中国邮政挂号'))){
				$carrierIds[] = $list['id'];
			}
		}
		$plateform_arr = array(1,8,10,12,13);  //允许的平台ebay,淘宝，DL,CN
		$tableName = 'om_unshipped_order';
		$rtn = combinePackageModel::combinePackage($tableName,$plateform_arr,$carrierIds,$id_array);
		self :: $errCode = combinePackageModel::$errCode;
		self :: $errMsg = combinePackageModel::$errMsg;
		return $rtn;
		/***平台拦截***/
	}
}
?>