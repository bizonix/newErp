<?php
/*
 * 提供对接运输方式管理系统のACTION
 * ADD BY Herman.Xi @20140120
 */
class TransAPIAct{
	static $errCode = 0;
	static $errMsg = "";
	
	/*
     *获取运输方式列表信息
	 *填写正确的运输方式参数类型（0非快递，1快递，2全部）
     */
	public function act_getCarrierInfoById($id = 2){
		$data = TransAPIModel::getCarrierList($id);
		//var_dump($data);
		$ret = array();
		foreach($data as $value){
			$ret[] = $value['id'];
		}
		return $ret;
    }
	
	/*
     *获取运输方式列表信息
	 *填写正确的运输方式参数类型（0非快递，1快递，2全部）
     */
	public function act_getCarrierListById($id = 2){
		$data = TransAPIModel::getCarrierList($id);
		//var_dump($data);
		$ret = array();
		foreach($data as $value){
			$ret[$value['id']] = trim($value['carrierNameCn']);
		}
		return $ret;
    }
	
	//获取所有的账号信息BY id
	function act_accountAllListById() {
		$res = omAccountModel::accountAllListById();
		self::$errCode = omAccountModel::$errCode;
		self::$errMsg = omAccountModel::$errMsg;
		return $res;
	}

	function act_getChannelistByApi(){
		$data = TransAPIModel::getChannelistByApi();
		$ret = array();
		foreach($data as $value){
			$ret[$value['id']] = trim($value['channelName']);
		}
		return $ret;
	}
}
?>	