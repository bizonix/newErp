<?php
/*
 * 类名：UserCompetenceAct
 * 功能：用户权限颗粒处理层
 * 版本：1.0
 * 日期：2013/9/12
 * 作者：管拥军
 *@add by : linzhengxiang ,date : 20140525
 */
class UserCompetenceAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 提供账号和平台列表接口
	 * @return array
	 * @author lzx
	 */
	public function act_getCompetenceByUserId($uid) {
		return  M('UserCompetence')->getCompetenceByUserId($uid);
	}
    
    /**
	 * 获取对应平台的账号，连接权限
	 */
    //获取用户信息
	public function  act_getAccountListByPlatform($uid, $platformId){
	    $accountList = M('UserCompetence')->getAcountPowerByUserId($uid, $platformId);
        $returnArr = array();
        if(!empty($accountList)){
            foreach($accountList as $accountId){
                $accountInfo = M('Account')->getAccountById($accountId);
                $returnArr[$accountId] = $accountInfo['account'];
            }
            self::$errMsg[200] = get_promptmsg(200, '获取账号信息');
        }else{
            self::$errMsg[10040] = get_promptmsg(10040);
        }
        return $returnArr;
	}
	
	/**
	 * 使用replace来对用户的权限赋值和编辑
	 * @return bool
	 * @author lzx
	 */
	public function act_replace(){
		$data = array();
		$uid = $_POST['uid'];
		$visible_platform_account = array();
		foreach($_POST as $key => $valueArray){
			if(strpos($key, 'checkboxes_account_')!==false){
				$strarr=explode('checkboxes_account_',$key);
				$pid=$strarr[1];
				$visible_platform_account[$pid]    = $valueArray;
			}
		}
		$data['visible_platform_account']    = json_encode($visible_platform_account);
		return M('UserCompetence')->replaceData($uid, $data, 'global_user_id');
	}
	/**
	 * 保存文件夹显示权限
	 * 
	 * @return bool
	 * @author yxd
	 */
	public function act_saveShowfolder(){
		$data          = array();
		$uid           = $_POST['uid'];
		$folder0       = $_POST['checkboxes_showfolder0'];
		$folder        = $_POST['checkboxes_showfolder'];
        $showfolder    = '';
        if(!empty($folder0)){
            $showfolder    = implode(",",$folder0);
        }
        if(!empty($folder)){
            $showfolder    .= ",".implode(",",$folder);;
        }
        $showfolder = trim($showfolder,',');
		$data['visible_showfolder']    = $showfolder;
		return M('UserCompetence')->replaceData($uid, $data, 'global_user_id');
	}
	/**
	 * 保存文件夹移动权限
	 * @return bool
	 * @author yxd
	 */
	public function act_saveMovefolder(){
		
		$data                    = array();
		$uid                     = $_POST['uid'];
		$userCompetence          = M('UserCompetence')->getCompetenceByUserId($uid);
		$moveout                 = $_POST['moveout'];//键值{10:[1,2,3,]}
		$movein                  = $_POST['movein'];
		$hasmove                 = json_decode($userCompetence['visible_movefolder'],true);//已有权限
	    $hasmove[$moveout[0]]    = $movein;//新增或修改权限
		$data['visible_movefolder']    = json_encode($hasmove);
		return M('UserCompetence')->replaceData($uid, $data, 'global_user_id');
	}
	/**
	 * 更新订单编辑权限
	 * @return bool
	 * @author yxd
	 */
	public function act_saveEditorder(){
		$data         = array();
		$uid          = $_POST['uid'];
		$vsbeditor    = $_POST['checkboxes_orderoptions'];
		$data['visible_editorder']    = implode(",", $vsbeditor);
		return  M('UserCompetence')->replaceData($uid, $data, 'global_user_id');
	}
	
	/**
	 * 保存运输方式显示权限
	 * @return bool
	 * @author yxd
	 */
	public function act_saveCarrier(){
		$data         = array();
		$uid          = $_POST['uid'];
		$carrierk     = $_POST['carrierListk'];
		$carriernk    = $_POST['carrierListnk'];
		$json_carr    = array();
		if($carriernk){
			$json_carr[1]    = $carriernk;
		}
		if($carrierk){
			$json_carr[0]    = $carrierk;
		}
		$json         = json_encode($json_carr);
		$data['visible_carrier']    = $json;
		return M('UserCompetence')->replaceData($uid, $data, 'global_user_id');
	}
}
?>