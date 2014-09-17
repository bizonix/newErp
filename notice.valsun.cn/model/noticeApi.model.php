<?php
/**
 * 名称：NoticeApiModel
 * 功能： 对外提供的发送短信和邮件API
 * 版本：v1.0
 * 日期：2013/10/09
 * 作者：wxb
 * */
class NoticeApiModel{
	public static $dbConn;
	static $errCode				= 0;
 	static $errMsg 				= "";
 	static $prefix				= "nt_";

	private static $tabemail 	= "email";
	private static $tabsms		= "sms";
	private static $tabsmspower = "sms_power";
	private static $table 		= "power_global_user";

	public static function initDB() {
		global $dbConn;
		self::$dbConn	= $dbConn;
	}

	/*
	 * 功能：查询可发送短信数量
	 * 日期：2014/08/19
	 * 作者：张志强
	 */
	public static function modSmsSurNum($from) {
		self::initDB();
		$table 		= "`power_global_user`";															//获取用户的global_user_id
		$filed 		= "global_user_id";
		$where 		= " global_user_status = 1 AND global_user_is_delete = 0 AND (global_user_login_name = '{$from}' OR global_user_name = '{$from}')  LIMIT 1 ";
		$ret 		= self::selectOneTable($table, $filed, $where);
		if(!$ret[0]['global_user_id']) {
			self::$errCode	=	"1057";
			self::$errMsg	=	"获取用户资料失败";
			return false;
		}
		$from 		= $ret[0]['global_user_id'];

		$field	= "num";
		$sql	= "SELECT {$field} FROM `" . C('PREFIX') . self::$tabsmspower . "` WHERE 1 AND `global_user_id` = '{$from}' AND `is_delete` = 0 LIMIT 1 ";
		$query	= self::$dbConn->query($sql);
		$sumnum	= self::$dbConn->fetch_array_all($query);

		if($sumnum) {																					//如果在表中查询到每天可发送短信的最大数
			$sumnum = $sumnum[0]['num'];																//每日可发送短信数量
			if($sumnum > 0) {
				$y 			= date("Y");
				$m 			= date("m");
				$d 			= date("d");
				$day_start 	= mktime(0, 0, 0, $m, $d, $y);												//获取今天的开始时间戳
				$nowtime	= time();																	//获取现在的时间戳
				$field 		= "count(*)";
				$sql		= "SELECT {$field} FROM `" . C('PREFIX') . self::$tabsms ."` WHERE addtime > {$day_start} AND addtime < {$nowtime} AND is_delete = 0 ";
				$query		= self::$dbConn->query($sql);
				$smsnum		= self::$dbConn->fetch_array_all($query);									//已发送短信数
				$smsnum		= $smsnum[0]['count(*)'];
				$surnum		= $sumnum - $smsnum;														//目前可发送短信数
				if($surnum > 0){
					return array("ret" => "ok");
				} else {
					return array("ret" => "no");
				}
			} elseif($sumnum == 0) {
				return array("ret" => "ok");															//当设置sms_num为0的时候可以无限发送短信
			} else {
				self::$errCode	=	"1000";
				self::$errMsg	=	"数据错误！";
				return array("ret" => "no");
			}
		} else {																						//若没有在表中查询到每日可发短信最大数，则添加默认
			$data 		= array(
							"global_user_id"	=>	$from,
							"num"			=>	"5",
							"is_delete"		=>	"0"
			);
			$def		= array2sql($data);																//将数组转为SET后面的sql语句
			$def 		= "INSERT INTO `" . C('PREFIX') . self::$tabsmspower . "` SET " . $def;
			$query 		= self::$dbConn->query($def);													//插入到nt_sms_power表中，作为默认值
			if($query) {
				return array("ret" => "ok");
			} else {
				self::$errCode	=	"1060";
				self::$errMsg	=	"获取数据失败";
				return array("ret" => "no");
			}
		}
	}

	//查询用户名字列表
	public static function showNameList() {
		self::initDB();
		$field	= "global_user_name as name ,global_user_id as id ";
		$sql 	= "SELECT {$field} FROM `" . self::$table . "` WHERE 1 AND `global_user_is_delete` = 0 AND global_user_status = 1  ";
		$sql .= " ORDER BY global_user_id DESC " ;

		$query 	= self::$dbConn->query($sql);
		if($query) {
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode 	= "003";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	//向表中插入消息记录
	public static function modInsert($data, $table="nt_email") {
		self::initDB();
		$sql = array2sql($data);
		if($table == 'nt_email') {
			$sql = "INSERT INTO `". C('PREFIX').self::$tabemail."` SET ".$sql;
		} else {
			$sql = "INSERT INTO `". C('PREFIX').self::$tabsms."` SET ".$sql;
		}
		$query	=	self::$dbConn->query($sql);
		if($query) {
			$affectedrows = self::$dbConn->affected_rows();
			return $affectedrows;
		} else {
			self::$errCode	=	"1060";
			self::$errMsg	=	"获取数据失败";
			return false;
		}
	}

	/**
	 * 功能：     拉取某个用户最近消息发送记录
	 * @param  str $from 传入的为登入拼音名 或者中文名
	 * @param  str $table
	 * @param  str $page 第几页
	 * */
	public static function modDetailList($from, $table="nt_email", $page) {
		self::initDB();
		//查询用户的中文名字
		$tableTmp 	= "`power_global_user`";
		$filed 		= "global_user_name";
		$where 		= " global_user_status = 1 AND global_user_is_delete = 0 AND (global_user_login_name = '{$from}' OR global_user_name = '{$from}')  LIMIT 1 ";
		$ret 		= self::selectOneTable($tableTmp, $filed, $where);
		if(!$ret[0]['global_user_name']) {
			self::$errCode	=	"1057";
			self::$errMsg	=	"获取用户资料失败";
			return false;
		}
		$from 		= $ret[0]['global_user_name'];										//$from为用户中文名字
		if(empty($page)) {																//默认当前页数
			$page 	= 1;
		}
		$pageSize 	= 10;																//每页显示数量
		$start 		= $pageSize * ($page - 1);
		$limit 		= "LIMIT {$start},{$pageSize}";

		$sql_email 	= "SELECT * FROM ". C('PREFIX').self::$tabemail." WHERE   from_name='{$from}'  AND is_delete = 0 ORDER BY id DESC {$limit}";
		$sql_sms 	= "SELECT * FROM ". C('PREFIX').self::$tabsms." WHERE from_name='{$from}' AND is_delete = 0  ORDER BY id DESC {$limit}";
		$query_email	 	= self::$dbConn->query($sql_email);
		$query_sms	 		= self::$dbConn->query($sql_sms);
		$result_email	 	= self::$dbConn->fetch_array_all($query_email);
		$result_sms	 		= self::$dbConn->fetch_array_all($query_sms);
		$query				= array_merge_recursive($result_email, $result_sms);

		if($query) {
			$sql_email 		= "SELECT count(*) as total FROM ". C('PREFIX').self::$tabemail." WHERE   from_name='{$from}'  AND is_delete = 0 ";
			$sql_sms 		= "SELECT count(*) as total FROM ". C('PREFIX').self::$tabsms." WHERE from_name='{$from}' AND is_delete = 0  ";
			$query_email	= self::$dbConn->query($sql_email);
			$query_sms		= self::$dbConn->query($sql_sms);
			$res_email		= self::$dbConn->fetch_array($query_email);
			$res_sms		= self::$dbConn->fetch_array($query_sms);
			$allNum 		= $res_email['total'] + $res_sms['total'];												//发出消息总数

			$totalPage 		= ceil($allNum/$pageSize);
			return array('totalPage'=>$totalPage, 'nowPage'=>$query);
		} else {
			self::$errCode	=	"1060";
			self::$errMsg	=	"获取数据失败";
			return false;
		}
	}

	//从数据库中获取某表信息 不默念添加is_delete = 0 条件
	public static function selectOneTable($table, $filed, $where, $countRec='') {
		self::initDB();
		if(!empty($where)) {
			$where = " AND ".$where;
		}
		$where = "WHERE  1  ".$where;

		if(empty($countRec)) {
			$sql  = "SELECT {$filed} FROM {$table}  {$where} ";
		} else {
			$sql  = "SELECT count(*) AS total  FROM {$table}  {$where} ";
		}
		//var_dump($sql);exit;
		$query	=	self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}

	/**
	 * NoticApiModel::getAuthCompanyList()
	 * 获取鉴权公司列表
	 * @return  array
	 */
	public static function getAuthCompanyList() {
		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' => 'power.user.getApiCompany.get',  //API名称
				'format' => 'json',  //返回格式
				'v' => '1.0',   //API版本号
				'username'	 => C('OPEN_SYS_USER'),
				/* API系统级参数 End */
				/* API应用级输入参数 Start*/
				'sysName' 	=> C('AUTH_SYSNAME'),
				'sysToken' 	=> C('AUTH_SYSTOKEN')

				/* API应用级输入参数 End*/
		);
		$companyInfo	= callOpenSystem($paramArr);
		$companyInfo	= json_decode($companyInfo, true);
		$companyInfo	= is_array($companyInfo) ? $companyInfo : array();
		unset($paramArr);
		return $companyInfo;
	}

	/**
	 *功能：搜索用用户名
	 *@package str $name
	 *@author wxb
	 *@date 2013/12/3
	 */
	public static function searchUser($name) {
		self::initDB();
		$sql 	= "SELECT global_user_name FROM power_global_user WHERE global_user_status = 1 ";
		$sql   .= " AND (global_user_name LIKE '%{$name}%' OR global_user_login_name LIKE '%{$name}%') ";
		$query 	= self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			if(count($res) > 0) {
				return $res;
			} else {
				self::$errMsg = 'res empty '.$sql;
				return false;
			}
		}
		self::$errMsg = 'query error '.$sql;
		return false;
	}

	/**
	 *功能：检查用户是否存在
	 *@param str $userEmail
	 *@author wxb
	 *@date 2014/01/08
	 */
	function userExistByEmail($userEmail){
		self::initDB();
		$sql = "SELECT global_user_id FROM power_global_user WHERE global_user_email = '{$userEmail}' LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			if(empty($res['0']['global_user_id'])){
				return false;
			}
			return true;
		}
			return false;
	}

	public static function insert($data,$table){
		self::initDB();
		$sql = array2sql($data);
		$sql = "INSERT INTO `". $table."` SET ".$sql;
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();
			return $affectedrows;
		}else{
			self::$errCode	=	"1060";
			self::$errMsg	=	"获取数据失败";
			return false;
		}
	}
	/**
	 * 提取power_global_user 表里的用户信息
	 * @param str $fileds
	 * @param str $where
	 * @author wxb
	 * date 2014/01/13
	 */
	public static function oneGlobalUser($fileds,$where){
		self::initDB();
		if(!empty($where)){
			$where = " AND ".$where;
		}
		$where = "WHERE  1  ".$where;
		$sql  = "SELECT {$fileds} FROM `power_global_user`   {$where} ";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}
	/**
	 * 向表 `nt_page_token`  插入数据
	 * @param array $set
	 * @author wxb
	 * date 2013/01/13
	 */
	public static function insertPageToken($set){
		self::initDB();
		$set  = array2sql($set);
		$sql = "INSERT INTO nt_page_token SET {$set}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$num = mysql_affected_rows(self::$dbConn->link);
			if($num ==1){
				return true;
			}
			return false;
		}
		return false;
	}
	/**
	 * 向表 `nt_email_detail`  插入数据
	 * @param array $set
	 * @author wxb
	 * date 2013/01/13
	 */
	public static function insertEmialDetail($set){
		self::initDB();
		$set  = array2sql($set);
		$sql = "INSERT INTO nt_email_detail SET {$set}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$num = mysql_affected_rows(self::$dbConn->link);
			if($num ==1){
				return true;
			}
			return false;
		}
		return false;
	}
	/**
	 * 提取`nt_page_token`  表里的信息
	 * @param str $fileds
	 * @param str $where
	 * @author wxb
	 * date 2014/01/13
	 */
	public static function onePageToken($fileds,$where){
		self::initDB();
		if(!empty($where)){
			$where = " AND ".$where;
		}
		$where = "WHERE  1  ".$where;
		$sql  = "SELECT {$fileds} FROM `nt_page_token`   {$where} ";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}
	/**
	 * 提取`nt_email_detail`  表里的信息
	 * @param str $fileds
	 * @param str $where
	 * @author wxb
	 * date 2014/01/13
	 */
	public static function onEmailDetail($fileds,$where){
		self::initDB();
		if(!empty($where)){
			$where = " AND ".$where;
		}
		$where = "WHERE  1  ".$where;
		$sql  = "SELECT {$fileds} FROM `nt_email_detail`   {$where} ";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}
	/**
	 * 更新 nt_page_token 表
	 * @param array $set
	 * @param str $where
	 */
	public static  function updatePageToken($set,$where = ''){
		self::initDB();
		if(!empty($where)){
			$where = "WHERE    ".$where;
		}
		$set = array2sql($set);
		$sql  = "UPDATE `nt_page_token`  SET  {$set}   {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$num = mysql_affected_rows(self::$dbConn->link);
			if($num == 1){
				return true;
			}
			return false;
		}
		return false;
	}
	/**
	 * 从邮件分表中获取某条完全的内容
	 * @param str $where
	 * @author wxb
	 * 2104/01/20
	 */
	function getEmailDetail($fields,$where = ''){
		self::initDB();
		if(!empty($where)){
			$where = ' AND '.$where;
		}
		$sql = "SELECT {$fields} FROM ".self::$prefix."email_detail WHERE 1 {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}
}
?>