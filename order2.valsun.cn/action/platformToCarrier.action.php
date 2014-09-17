<?php 
/**
 * 运输方式管理
 * @add by yxd ,date 2014/07/08
 */
class PlatformToCarrierAct extends CheckAct{
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
		
	}
	/**
	 * 分页获取
	 * return array
	 */
	public function act_getPlatformToCarrier(){
		return M('PlatformToCarrier')->getPlatformToCarrier($this->act_getPlatformToCarrierCondition());
	}
	
	/**
	 * 运输方式id获取运输方式信息
	 * param id
	 * return array
	 */
	public function act_getPlatformToCarrierByid(){
		$id           = isset($_GET['id']) ? $_GET['id'] : '';
		$PlatformToCarrier    = M('PlatformToCarrier')->getPlatformToCarrierByid($id);
		return $PlatformToCarrier;
	}
	/**
	 * 获取运输方式数量
	 * return int
	 */
	public function act_getPlatformToCarrierCount(){
		
		return M('PlatformToCarrier')->getPlatformToCarrierCount($this->act_getPlatformToCarrierCondition());
	}
	
	/**
	 * 插入获取运输方式
	 * @param array data
	 * return boolean
	 */
	public function act_insert(){
		$data                                 = array();
		$data['platformId']                   = isset($_POST['platformId']) ?$_POST['platformId']:"";
		$data['platformCarrierName']          = isset($_POST['carrierName']) ?$_POST['carrierName']:"";
		$data['platformReturnCarrierName']    = isset($_POST['returnCarrierName']) ?$_POST['returnCarrierName']:"";
	    $carrierIds0                          = isset($_POST['carriers0']) ?$_POST['carriers0']:"";
		$carrierIds1                          = isset($_POST['carriers1']) ?$_POST['carriers1']:"";
		$nkd                                  = isset($_POST['ckall0']) ? $_POST['ckall0'] : "";
		$kd                                   = isset($_POST['ckall1']) ? $_POST['ckall1'] : "";
		if($nkd==="0"){
			$is_k    = 0;
		}
		if($kd==="1"){
			$is_k    = 1;
		}
		$data['isExpressDelivery']            = $is_k;
		$data['createdtime']	              = time();
		$data['creatorId']                    = get_userid();
	    $carrierIdsStr                        = NULL;
	    if($carrierIds0){
		    foreach($carrierIds0 as $value){
		    	$carrierIdsStr   .= $value.",";
		    }
	    }
	    if($carrierIds1){
		    foreach($carrierIds1 as $value){
		    	$carrierIdsStr   .= $value.",";
		    }
	    }
	    $carrierIdsStr         = substr($carrierIdsStr, 0,strlen($carrierIdsStr)-1);
	    $data['channelIds']    = $carrierIdsStr;
        return M('PlatformToCarrier')->insertData($data);
	}
	/**
	 * 检查是否存在该平台下的运输方式名称、
	 * @param platformId carrierName
	 * @return bool
	 * @author yxd
	 */
	public function act_checkExit(){
		$platformId     = $_POST['platformId'];
		$carrierName    = $_POST['carrierName'];
		$data['platformId']             = array('$e'=>$platformId);
		$data['platformCarrierName']    = array('$e'=>$carrierName);
		$data['is_delete']              = array('$e'=>0);
		$ret                            = M('PlatformToCarrier')->checkIsExist($data);
		if($ret){
			self::$errMsg['201']    = "该平台运输方式已存在";
			return false;
		}else{
			self::$errMsg['200']    = "success";
			return true;
		}
	}
	/**
	 * 获取运输方式列表 0
	 * @param $type 0非快递 1快递 2全部 （8、20之前逻辑弄反了）
	 * return array
	 */
	public function act_getCarrierFromApi($type){
		$carriers     = M('InterfaceTran')->getCarrierList($type);
		return   $carriers;
	}
	
	/**
	 * 删除信息
	 * @param id
	 * return boolean
	 */
	public function act_delete(){
		$id    = isset($_GET['id']) ?$_GET['id']:"";
		return M('PlatformToCarrier')->deleteData($id);
	}
	/**
	 * 更新黑名单信息
	 * @param int id ,array data
	 * retrun boolean
	 */
	public function act_update(){
		$id                                   = isset($_POST['id']) ?$_POST['id']:"";
		$data                                 = array();
		$data['platformId']                   = isset($_POST['platformId']) ?$_POST['platformId']:"";
		$data['platformCarrierName']          = isset($_POST['carrierName']) ?$_POST['carrierName']:"";
		$data['platformReturnCarrierName']    = isset($_POST['returnCarrierName']) ?$_POST['returnCarrierName']:"";
		$carrierIds0                          = isset($_POST['carriers0']) ?$_POST['carriers0']:"";
		$carrierIds1                          = isset($_POST['carriers1']) ?$_POST['carriers1']:"";
		$data['createdtime']	              = time();
		$data['creatorId']                    = get_userid();
		$nkd                                  = isset($_POST['ckall0']) ? $_POST['ckall0'] : "";
		$kd                                   = isset($_POST['ckall1']) ? $_POST['ckall1'] : "";
		if($nkd==="0"){
			$is_k    = 0;
		}
		if($kd==="1"){
			$is_k    = 1;
		}
		$data['isExpressDelivery']            = $is_k;
		$carrierIdsStr                        = NULL;
	    if($carrierIds0){
		    foreach($carrierIds0 as $value){
		    	$carrierIdsStr   .= $value.",";
		    }
	    }
	    if($carrierIds1){
		    foreach($carrierIds1 as $value){
		    	$carrierIdsStr   .= $value.",";
		    }
	    }
	    $carrierIdsStr         = substr($carrierIdsStr, 0,strlen($carrierIdsStr)-1);
	    $data['channelIds']    = $carrierIdsStr;
		return M('PlatformToCarrier')->updateData($id,$data);
	}
	private function act_getPlatformToCarrierCondition(){
		$data['platformId']          = isset($_POST['platformName']) ? $_POST['platformName'] : '';
		foreach($data as $key=>$value){
			if($value){
				$data["$key"]    = array('$e'=>"$value");
			}
		}
		$data['is_delete']       = array('$e'=>0);
		return $data;
	} 
}
?>
