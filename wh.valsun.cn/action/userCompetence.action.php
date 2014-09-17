<?php

/** 
 * @author 涂兴隆
 * 打印操作
 */
class UserCompetenceAct
{

    public static $errCode = 0;
    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct ()
    {
    	
    }
    
    /*
     * 运输方式权限设置
     */
    public function act_addShippingIds(){
        $shippingids = isset($_POST['shippingids']) ? $_POST['shippingids'] : '';
		$uid  	     = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        if (empty($shippingids)) {
            self::$errCode = 401;
            self::$errMsg = '请选择运输运输方式!';
            return;
        }
		if (empty($uid)) {
            self::$errCode = 402;
            self::$errMsg = '被修改人id有误!';
            return;
        }
        if(UserCompetenceModel::insertCarrierData($uid,$shippingids)) {
			self::$errCode = 0;
			self::$errMsg = '权限设置成功';
			return true;
        }else{
			self::$errCode = 403;
            self::$errMsg = '权限设置失败';
            return false;
		}
    }
	
	/*
     * 账号权限设置
     */
    public function act_addPlatIds(){
        $infos = isset($_POST['infos']) ? $_POST['infos'] : '';
		$uid  	     = isset($_POST['uid']) ? intval($_POST['uid']) : 0;

		if (empty($uid)) {
            self::$errCode = 402;
            self::$errMsg = '被修改人id有误!';
            return;
        }
		
		$insertData = array();
		if(!empty($infos)){
			$datas = explode(',',$infos);
			foreach($datas as $data){
				$idInfo = explode('*',$data);
				$insertData[$idInfo[1]][] = $idInfo[0];
			}
		}
		if(empty($insertData)){
			$data = '';
		}else{
			$data = json_encode($insertData);
		}

        if(UserCompetenceModel::insertPlatData($uid,$data)) {
			self::$errCode = 0;
			self::$errMsg = '权限设置成功';
			return true;
        }else{
			self::$errCode = 403;
            self::$errMsg = '权限设置失败';
            return false;
		}
    }

}

?>