<?php
/**
 * 类名：UserCompetenceAct
 * 功能：用户权限颗粒处理层
 * 版本：1.0
 * 日期：2013/9/12
 * 作者：管拥军
 */
class  UserCompetenceAct{
    public static $errCode = 0;
    public static $errMsg = '';
	/**
	 * UserCompetenceAct::act_Competence()
	 * 添加修改用户颗粒权限
	 * @return array 
	 */
	public function act_Competence(){
		$userid	= isset($_GET["userid"]) ? intval($_GET["userid"]) : 0;
		$visacc	= isset($_GET["visacc"]) ? post_check(trim($_GET["visacc"])) : '';
		if(!$userid){
			self::$errCode  = "1001";
			self::$errMsg   = "用户ID参数非法";
			return false;
		}
		if(empty($visacc)){
			self::$errCode  = "1002";
			self::$errMsg   = "可见帐号内容非法";
			return false;
		}		
		$data	= array(
				"global_user_id"	=> $userid,
				"visible_account"	=> $visacc,
				);
        $result			= UserCompetenceModel::Competence($data);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	/**
	 * UserCompetenceAct::act_show()
	 * 查看用户颗粒权限
	 * @return array 
	 */
	public function act_show(){
		$userid	= isset($_GET["userid"]) ? intval($_GET["userid"]) : 0;
		if(!$userid){
			self::$errCode  = "1001";
			self::$errMsg   = "用户ID参数非法";
			return false;
		}
        $result			= UserCompetenceModel::showCompetence($userid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	public function act_showGlobalUser(){
		$userid	= $_SESSION['sysUserId'];
		//echo($userid); echo "<br>";
		if(!$userid){
			self::$errCode  = "1001";
			self::$errMsg   = "用户ID参数过期";
			return false;
		}
        $result			= UserCompetenceModel::showCompetenceVisibleAccount($userid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	public function act_showGlobalPlatform(){
		$userid	= $_SESSION['sysUserId'];
		if(!$userid){
			self::$errCode  = "1001";
			self::$errMsg   = "用户ID参数非法";
			return false;
		}
        $result			= UserCompetenceModel::showCompetenceVisiblePlatform($userid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	/**
	 * UserCompetenceAct::act_listPf()
	 * 列出所有平台名称
	 * @return array 
	 */
	public function act_listPf(){
        $result			= UserCompetenceModel::listPlatform();
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	/**
	 * UserCompetenceAct::act_listAcc()
	 * 列出某个平台或所有平台帐号
	 * @return array 
	 */
	public function act_listAcc(){
		if(isset($_GET["pfid"])){
			$pfid = intval($_GET["pfid"]);
		}else {
			self::$errCode  = "1001";
			self::$errMsg   = "平台ID参数非法";
			return false;
		}
        $result			= UserCompetenceModel::listAccount($pfid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $result;
    }
	
	/*
	 * 根据文件夹出ID获取
	 */
	function act_getInStatusIds($statusId = '', $uid = '') {
		//global $memc_obj; //调用memcache获取sku信息
		$statusId = isset($_POST['statusId']) ? $_POST['statusId'] : $statusId;
		$addUser = isset($_POST['uid']) ? $_POST['uid'] : $uid;
		//$addUser = $_SESSION['sysUserId'];
		$OmAccountAct = new OmAccountAct();
		$UserCompenseList = $OmAccountAct->act_getUserCompenseList($addUser);
		//echo "<pre>";
		//var_dump($UserCompenseList);
		//$visible_platform = $UserCompenseList['visible_platform'];
		//$visible_account = $UserCompenseList['visible_account'];
		$visible_movefolder = json_decode($UserCompenseList['visible_movefolder'],true);
		//var_dump($visible_movefolder);
		$rtn = array();
		if(isset($visible_movefolder[$statusId])){
			$rtn = $visible_movefolder[$statusId];
		}
		//var_dump($rtn);
		//self :: $errCode = OmAccountModel :: $errCode;
		//self :: $errMsg = OmAccountModel :: $errMsg;
		return $rtn;
	}
	
	/*
	 * 根据所有文件夹出ID获取
	 */
	function act_getAllInStatusIds() {
		//global $memc_obj; //调用memcache获取sku信息
		//$statusId = isset($_POST['statusId']) ? $_POST['statusId'] : $statusId;
		$addUser = $_SESSION['sysUserId'];
		$OmAccountAct = new OmAccountAct();
		$UserCompenseList = $OmAccountAct->act_getUserCompenseList($addUser);
		//echo "<pre>";
		//var_dump($UserCompenseList);
		//$visible_platform = $UserCompenseList['visible_platform'];
		//$visible_account = $UserCompenseList['visible_account'];
		$visible_movefolder = json_decode($UserCompenseList['visible_movefolder'],true);
		if(!empty($visible_movefolder)){
			return $visible_movefolder;	
		}else{
			return array();	
		}
	}
	
	/*
	 * 添加ID获取
	 */
	function act_addInStatusIds() {
		//global $memc_obj; //调用memcache获取sku信息
		$outid = $_POST['outid'];
		$idArr = $_POST['idArr'];
		$uid = $_SESSION['sysUserId'];
		$data = array();
		$data['visible_movefolder'] = $this->act_getAllInStatusIds();
		//var_dump($data);
		$data['visible_movefolder'][$outid] = $idArr;
		$data['visible_movefolder'] = json_encode($data['visible_movefolder'], JSON_UNESCAPED_UNICODE);
		//var_dump($data);
		$rtn = OmAccountModel :: addUserCompense($uid,$data);
		$visible_movefolder = omAccountModel::idTransferName($data['visible_movefolder']);
		//调用老系统接口，同步老系统文件夹
		$rtnInfo = OldsystemModel::erpSyncMovefolders($visible_movefolder,$_SESSION['userCnName']);
		if($rtnInfo['res_code'] == 200){
			self :: $errCode = OmAccountModel :: $errCode;
			self :: $errMsg = OmAccountModel :: $errMsg;
			return $rtn;
		}else{
			self :: $errCode = $rtnInfo['res_code'];
			self :: $errMsg =  '同步老系统文件夹移动权限失败';
			return false;
		}
		
	}	

	/*
	 * 添加显示文件夹权限 ShowFolder
	 */
	function act_addShowFolderInStatusIds() {
		//global $memc_obj; //调用memcache获取sku信息
		$idArr = $_POST['idArr'];		
		$uid   = $_POST['uid'];	
		$data  = array();
		$idStr = implode(',', $idArr);
		$data['visible_showfolder'] = $idStr;
		$rtn = OmAccountModel :: addUserCompense($uid,$data);		
		self :: $errCode = OmAccountModel :: $errCode;
		self :: $errMsg = OmAccountModel :: $errMsg;
		return $rtn;
	}

	/*
	 * 添加编辑订单权限 orderEditOptions
	 */
	function act_updateOrderEditOptions() {
		//global $memc_obj; //调用memcache获取sku信息
		$idArr = $_POST['idArr'];		
		$uid   = $_POST['uid'];	
		$data  = array();
		$idStr = implode(',', $idArr);
		$data['visible_editorder'] = $idStr;
		$rtn = OmAccountModel :: addUserCompense($uid,$data);		
		self :: $errCode = OmAccountModel :: $errCode;
		self :: $errMsg = OmAccountModel :: $errMsg;
		return $rtn;
	}	
}

?>