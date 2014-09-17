<?php
/**
 * 类名：UserCompetenceAct
 * 功能：用户权限颗粒处理层
 * 版本：1.0
 * 日期：2013/11/13
 * 作者：管拥军
 */
 
class  UserCompetenceAct{
    public static $errCode = 0;
    public static $errMsg = '';
	/**
	 * UserCompetenceAct::competence()
	 * 添加修改用户颗粒权限
	 * @return array 
	 */

	public function competence(){
		global $dbConn;
		//$userid = $_SESSION["sysUserId"];
		$userid = $_POST["userid"];
		$access_id = $_POST["visacc"];
		$_SESSION["access_id"] = $userid.",".$access_id;
		$sql = "SELECT count(*) as totalNum FROM  `ph_purchases_access` where user_id={$userid}";
		$sql = $dbConn->execute($sql);
		$userInfo = $dbConn->fetch_one($sql);
		if($userInfo['totalNum'] > 0){
			$sql = "update ph_purchases_access set power_ids='{$_SESSION['access_id']}' where user_id={$userid}";
		}else{
			$sql = "INSERT INTO `ph_purchases_access`(`user_id`, `power_ids`) VALUES ({$userid},'{$_SESSION['access_id']}')";
		}
		if($dbConn->execute($sql)){
			$data['errCode'] = 0;
			$data['msg'] = "success...";
		}else{
			$data['errCode'] = 1;
			$data['msg'] = "failer...";
		}
		return json_encode($data);
	}

	public function competence1(){
		$ajaxAcc	= commonAct::ajaxAccess();
		if (!$ajaxAcc) {
			self::$errCode  = "1003";
			self::$errMsg   = "您无用户颗粒（添加、修改）权限！";
			return false;
		}		
		$userid		= isset($_POST["userid"]) ? post_check($_POST["userid"]) : '';
		$visacc		= isset($_POST["visacc"]) ? post_check($_POST["visacc"]) : '';
		if (empty($userid)) {
			self::$errCode  = "1001";
			self::$errMsg   = "用户参数非法";
			return false;
		}
		if (empty($visacc)) {
			self::$errCode  = "1002";
			self::$errMsg   = "可见帐号内容非法";
			return false;
		}
		$userarr = explode(",",$userid);
		foreach ($userarr as $userid) {
			$userid = is_numeric($userid) ? $userid : 0;
			if (empty($userid)) {
				self::$errCode  = "1000";
				self::$errMsg   = "单个用户ID参数非法";
				return false;
			}
			$data	= array(
					"user_id"		=> $userid,
					"power_ids"		=> $visacc,
				);
			$res			= UserCompetenceModel::competence($data);
			self::$errCode  = UserCompetenceModel::$errCode;
			self::$errMsg   = UserCompetenceModel::$errMsg;
		}
		return $res;
    }
	
	/**
	 * UserCompetenceAct::show()
	 * 查看用户颗粒权限
	 * @return array 
	 */
	public function show(){
		$ajaxAcc	= commonAct::ajaxAccess();
		if (!$ajaxAcc) {
			self::$errCode  = "1002";
			self::$errMsg   = "您无用户颗粒查看权限！";
			return false;
		}
		$userid	= isset($_POST["userid"]) ? intval($_POST["userid"]) : 0;
		if (empty($userid)) {
			self::$errCode  = "1001";
			self::$errMsg   = "用户ID参数非法";
			return false;
		}
        $res			= UserCompetenceModel::showCompetence($userid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $res;
    }

	/**
	 * UserCompetenceAct::listAcc()
	 * 查看所有采购帐号
	 * @return array 
	 */
	public function listAcc(){
		global $dbConn;
        $res			= CommonAct::actGetPurchaseList(true);
		self::$errCode  = CommonAct::$errCode;
        self::$errMsg   = CommonAct::$errMsg;
		$type = $_POST["type"];
		$userIdArr = $_POST['userIdArr'];
		if($type == "all" && count($userIdArr) > 1){//批量添加
			$access_id = 0;
		}else{
			$sql = "SELECT power_ids from ph_purchases_access where user_id={$userIdArr[0]}"; 
			$sql = $dbConn->execute($sql);
			$powerInfo = $dbConn->fetch_one($sql);
			$access_id = $powerInfo['power_ids'];
		}
		$data = array("access_id"=>$access_id,"data"=>$res);
		return json_encode($data);
    }	
}
?>
