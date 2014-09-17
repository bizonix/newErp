<?php
/*
*提供账号和平台列表接口
*ADD BY heminghua
*/
class omAccountModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	public  static $UserCompenseTable = "om_userCompetence";
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取平台信息列表
	public 	static function platformList($id=''){
		self::initDB();
		$where = '';
		if($id){
			$where = ' and id = '.$id;	
		}
		$sql	= "SELECT * FROM om_platform WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取平台信息列表
	public 	static function platformListById($id=''){
		self::initDB();
		$ret =array();
		$where = '';
		if($id){
			$where = ' and id = '.$id;	
		}
		$sql	= "SELECT * FROM om_platform WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[$row['id']] = $row['platform'];
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取平台信息列表权限
	public 	static function platformListPowerById(){
		self::initDB();
		$ret =array();
		$platformList = $_SESSION['platformList'];
		if(!$platformList){
			return array();	
		}
		$where = '';
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= "id='".$platformList[$i]."'";
		}
		if($platformsee){
			$where .= ' AND ('.join(" or ", $platformsee).') ';	
		}else{
			return array();	
		}
		$sql	= "SELECT * FROM om_platform WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[] = $row['id'];
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取平台信息列表
	public 	static function platformListPower(){
		self::initDB();
		$ret =array();
		$platformList = $_SESSION['platformList'];
		$where = '';
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= "id='".$platformList[$i]."'";
		}
		if($platformsee){
			$where .= ' AND ('.join(" or ", $platformsee).') ';	
		}else{
			return array();	
		}
		$sql	= "SELECT * FROM om_platform WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[$row['id']] = $row;
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取账号信息列表
	public 	static function accountListPower(){
		self::initDB();
		$ret =array();
		$accountList = $_SESSION['accountList'];
		if(!$accountList){
			return array();	
		}
		$sql	= "SELECT `id`,`account`,`platformId` FROM om_account WHERE id IN (".join(',', $accountList).") AND is_delete = 0 ";
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[$row['id']] = $row;
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取账号信息列表2
	public 	static function accountListPlatform($Platform){
		self::initDB();
		$ret =array();
		if(!$Platform){
			return array();
		}
		$plat = array_shift($Platform);
		//var_dump($plat);
		$accountList = $_SESSION['accountList'];
		if(!$accountList){
			return array();	
		}
		$sql	= "SELECT `id`,`account`,`platformId` FROM om_account WHERE platformId = ".$plat." AND id IN (".join(',', $accountList).") AND is_delete = 0 ";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[$row['id']] = $row;
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取平台信息列表
	public 	static function accountAllList($id=''){
		self::initDB();
		$where = '';
		if($id){
			$where = ' and id = '.$id;	
		}
		$sql	= "SELECT * FROM om_account WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取平台信息列表
	public 	static function ebayaccountAllList(){
		self::initDB();
		$accountList = $_SESSION['accountList'];
		if(!$accountList){
			return array();	
		}
		$sql	= "SELECT `id`,`account`,`platformId` FROM om_account WHERE platformId=1 AND id IN (".join(',', $accountList).") AND is_delete = 0 ";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}

	//获取平台信息列表
	public 	static function accountAllListById($id=''){
		self::initDB();
		$ret =array();
		$where = '';
		if($id){
			$where = ' and id = '.$id;
		}
		$sql	= "SELECT * FROM om_account WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$ret[$row['id']] = $row['account'];
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $ret;
	}
	
	//获取平台信息列表
	public static function getPlatformSuffixById($id){
		self::initDB();
		$where = '';
		if($id){
			$where = ' and id = '.$id;
		}
		$sql	= "SELECT suffix FROM om_platform WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取平台信息列表
	public 	static function getPlatformSuffixByAccountId($id){
		self::initDB();
		$where = '';
		if($id){
			$where = ' and id = '.$id;	
		}
		$sql	= "SELECT * FROM om_account WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			$ssql = "SELECT suffix FROM om_platform WHERE is_delete = 0 and and id = ".$ret['platformId'];
			$result	= self::$dbConn->query($ssql);
			$ret2 =self::$dbConn->fetch_array($result);
			return $ret2;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取平台信息列表
	public 	static function accountInfo($accountId){
		self::initDB();
		$where = '';
		if($accountId){
			$where = ' and id = '.$accountId;	
		}
		$sql	= "SELECT * FROM om_account WHERE is_delete = 0 {$where} ";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取客户信息列表
	public 	static function accountList(){
		self::initDB();
		$sql	= "SELECT b.id,b.account,a.id platformId,a.platform,b.appname,b.email,b.suffix,b.charger FROM om_platform as a LEFT JOIN om_account as b on a.id=b.platformId WHERE 1=1";
		$query	= self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/*
	* 获取平台账号信息列表
	* last modified by Herman.Xi @20140220
	*/
	public 	static function accountListByCompense(){
		self::initDB();
		$sql	= "SELECT a.id as pid, b.id as aid, b.account, a.platform, b.appname, b.email, b.suffix, b.charger FROM om_platform as a LEFT JOIN om_account as b on a.id=b.platformId WHERE 1=1";
		$query	= self::$dbConn->query($sql);
		$arr = array();
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return $arr;
		}
		foreach($ret as $value){
			$arr[$value['pid']]['platform'] = $value['platform'];
			$arr[$value['pid']]['acountlists'][$value['aid']] = $value['account'];
		}
		return $arr;
	}
	
	//获取客户信息列表（可见账号限制）
	public 	static function accountListAcc(){
		self::initDB();
		$accountList = $_SESSION['accountList'];
		$where = '';
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= "b.id='".$accountList[$i]."'";
		}
		if($accountsee){
			$where .= ' AND ('.join(" or ", $accountsee).') ';	
		}
		$sql	= "SELECT b.id,b.account,a.platform,b.appname,b.email,b.suffix,b.charger FROM om_platform as a LEFT JOIN om_account as b on a.id=b.platformId WHERE 1=1".$where;
		$query	= self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if(!empty($ret)){
				foreach($ret as $k => $v){
					if($v['id'] == ''){
						unset($ret[$k]);
					}	
				}
			}
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取销售账号列表
	public 	static function getAccountByPlatform($platform){
		self::initDB();
		if(empty($platform)){
			return array();
		}
		$where = "platformId in (".join(',', $platform).")";
		$accountList = $_SESSION['accountList'];
		if(empty($accountList)){
			return array();
		}
		$where .= ' AND id IN ('.join(",", $accountList).') AND is_delete = 0 ';	
		$sql	= "SELECT id,account FROM om_account WHERE ".$where;
		$query	= self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//通过平台id获取账户信息列表
	public 	static function accountListByPid($pid){
		self::initDB();
		$accountList = $_SESSION['accountList'];
		$where = '';
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= "id='".$accountList[$i]."'";
		}
		if($accountsee){
			$where .= ' AND ('.join(" or ", $accountsee).') ';	
		}
		$sql	= "SELECT id,account FROM om_account WHERE platformId = ".$pid.$where;
		$UserCompetenceAct = new UserCompetenceAct();
		$accountList = $UserCompetenceAct->act_showGlobalUser();
		if($accountList){
			$sql .= ' AND id in ( '.join(',', $accountList).' ) ';	
		}
		$query	= self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			$arr = array();
			foreach($ret as $value){
				$arr[$value['id']] = $value['account'];	
			}
			return $arr;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getUserCompenseInfo($addUser, $type){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from ".self::$UserCompenseTable." where global_user_id = {$addUser} and type = {$type} ";
		//echo $sql; exit;
		$query	= self::$dbConn->query($sql);
		$tinfo = self::$dbConn->fetch_array($query);
		if($tinfo){
			self :: $errCode = "200";
			self :: $errMsg =  " 获取数据成功！ ";
		}else{
			self :: $errCode = "001";
			self :: $errMsg  =  " 获取数据为空 ";
		}
		$visible_platform = $tinfo['visible_platform'];
		$list_visible_platform = array_filter(explode(',', $visible_platform));
		/*$arr_visible_platform = array();
		if(!empty($list_visible_platform)){
			//var_dump($list_visible_platform);
			foreach($list_visible_platform as $pid){
				$platform = self::platformList($pid);
				$arr_visible_platform[$pid] = $platform[0]['platform'];	
			}
		}*/
		$visible_account = $tinfo['visible_account'];
		$list_visible_account = array_filter(explode(',', $visible_account));		
		$visible_showfolder = $tinfo['visible_showfolder'];
		$list_visible_showfolder = array_filter(explode(',', $visible_showfolder));
		$visible_editorder = $tinfo['visible_editorder'];
		$list_visible_editorder = array_filter(explode(',', $visible_editorder));
		/*$arr_visible_account = array();
		if(!empty($list_visible_account)){
			foreach($list_visible_account as $aid){
				$omaccount = self::accountInfo($aid);
				$arr_visible_account[$aid] = $omaccount['account'];
			}
		}*/
		//$arr_all_platform = self::platformListById();
		//$arr_all_account = self::accountAllListById();
		$arr_all_platform_account = self::accountListByCompense();
		$tinfo['visible_platform'] = $list_visible_platform;
		$tinfo['visible_account'] = $list_visible_account;
		$tinfo['visible_showfolder'] = $list_visible_showfolder;
		$tinfo['visible_editorder'] = $list_visible_editorder;
		$tinfo['arr_all_platform_account'] = $arr_all_platform_account;
		//$tinfo['all_platform'] = $arr_all_platform;
		//$tinfo['all_account'] = $arr_all_account;
		//$tinfo['compense'] = $accountListByCompense;
		//var_dump($tinfo); exit;
		return $tinfo; //失败则设置错误码和错误信息， 返回false
	}
	
	/*
	 * 搜索快递描述信息(最新版)
	 * last modified by Herman.Xi @20131223
	 */
	public static function selectUserCompense($uid){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT * FROM ".self::$UserCompenseTable." WHERE global_user_id = ".$uid;
		$query = self::$dbConn->query($sql);
		$tinfo = self::$dbConn->fetch_array_all($query);
		if(!$tinfo){
			self :: $errCode = "002";
			self :: $errMsg =  " 插入数据失败！ ";
			return false; //失败则设置错误码和错误信息， 返回false	
		}else{
			self :: $errCode = "200";
			self :: $errMsg =  " 插入数据成功！ ";
			return $tinfo; //失败则设置错误码和错误信息， 返回true
		}
	}
	
	/*
	 * 插入快递描述信息(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function addUserCompense($uid,$data){
		!self::$dbConn ? self::initDB() : null;
		BaseModel :: begin(); //开始事务
		//var_dump($data);
		$string = array2sql_extral($data);
		if(self::selectUserCompense($uid)){
			$sql = "UPDATE ".self::$UserCompenseTable." SET {$string} WHERE global_user_id = ".$uid;
			//echo $sql;
			if(!self::$dbConn->query($sql)){
				BaseModel :: rollback();
				self :: $errCode = "002";
				self :: $errMsg =  " 插入数据失败！";
				return false; //失败则设置错误码和错误信息， 返回false	
			}
		}else{
			$data['global_user_id'] = $uid;
			//$data['type'] = 1;
			$string = array2sql($data);
			$sql = "INSERT INTO ".self::$UserCompenseTable." SET {$string} ";
			//echo $sql;
			if(!self::$dbConn->query($sql)){
				BaseModel :: rollback();
				self :: $errCode = "002";
				self :: $errMsg =  " 插入数据失败！";
				return false; //失败则设置错误码和错误信息， 返回false	
			}	
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self :: $errCode = "200";
		self :: $errMsg =  " 插入数据成功！";
		return true; //失败则设置错误码和错误信息， 返回false	
	}
	
	/*
	 * 删除快递描述信息(最新版)
	 * last modified by Herman.Xi @20131223
	 */
	public static function deleteUserCompense($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		$sql = "DELETE FROM ".self::$UserCompenseTable." WHERE global_user_id = ".$omOrderId;
		//echo $sql;
		if(!self::$dbConn->query($sql)){
			self :: $errCode = "002";
			self :: $errMsg  =  " 插入数据失败！ ";
			return false; //失败则设置错误码和错误信息， 返回false	
		}else{
			self :: $errCode = "200";
			self :: $errMsg  = " 插入数据成功！ ";
			return true; //失败则设置错误码和错误信息， 返回true
		}
	}
	
	/*
	 * 通过账户名和平台id获取账号信息
	 * @param	string	$account	账号名
	 * @param	int		$platformId	平台id
	 */
	public static function getAccountInfoByName($account, $platformId=""){
		
		!self::$dbConn ? self::initDB() : null;
		$where	=	" ";
		if (!empty($platformId))	$where	=	" AND platformId = '$platformId'";
		$sql	=	"SELECT * FROM om_account WHERE account	=	'$account'".$where;
		$query	=	self::$dbConn->query($sql);
		$ret	=	self::$dbConn->fetch_array($query);
		return $ret;
		
	}
	
}
?>	
	