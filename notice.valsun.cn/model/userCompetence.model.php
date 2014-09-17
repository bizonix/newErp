<?php
/**
 * 功能：修改用户每日可发短信action
 * 作者：张志强
 * 时间：2014/08/20
 */
Class UserCompetenceModel{
	public static $dbConn;
	static $errCode				= 0;
	static $errMsg 				= "";

	private static $tabsms		= "sms";
	private static $tabsmspower = "sms_power";
	private static $table 		= "power_global_user";

	public static function initDB() {
		global $dbConn;
		self::$dbConn	= $dbConn;
	}

	public static function updateUserCompetence($nameList, $smsnum) {
		self::initDB();
	 	$successnum = 0;
	 	$success 	= array();
	 	$error		= array();															//设置批量修改记录标志变量
		foreach($nameList as $from) {
			$table 		= "`power_global_user`";										//获取用户的global_user_id
			$filed 		= "global_user_id";
			$where 		= " global_user_status = 1 AND global_user_is_delete = 0 AND (global_user_login_name = '{$from}' OR global_user_name = '{$from}')  LIMIT 1 ";
			$ret 		= self::selectOneTable($table, $filed, $where);
			if(!$ret[0]['global_user_id']) {
				self::$errCode	=	"1057";
				self::$errMsg	=	"获取用户资料失败";
				return false;
			}
			$from 		= $ret[0]['global_user_id'];
			$username	= $ret[0]['global_user_name'];

			$field	= "num";
			$sql	= "SELECT {$field} FROM `" . C('PREFIX') . self::$tabsmspower . "` WHERE 1 AND `global_user_id` = '{$from}' AND `is_delete` = 0 LIMIT 1 ";
			$query	= self::$dbConn->query($sql);
			$sumnum	= self::$dbConn->fetch_array_all($query);							//查询在nt_sms_power中是否有sms_num记录

			if($sumnum) {																//存在记录则更新
				$field		= "num";
				$sql		= "UPDATE " .C('PREFIX'). self::$tabsmspower . " SET num = '{$smsnum}' WHERE global_user_id = '{$from}'";
				$query		= self::$dbConn->query($sql);
				if($query) {
					$affectedrows = self::$dbConn->affected_rows();
					$successnum   ++;
					array_push($success, $username);
				} else {
					array_push($error, $username);
				}
			} else {																	//不存在记录则插入
				$data 		= array(
								"global_user_id"	=>	$from,
								"num"				=>	$smsnum,
								"is_delete"			=>	"0"
				);
				$def		= array2sql($data);											//将数组转为SET后面的sql语句
				$def 		= "INSERT INTO `" . C('PREFIX') . self::$tabsmspower . "` SET " . $def;
				$query 		= self::$dbConn->query($def);
				if($query) {
					$successnum ++;
					array_push($success, $username);
				} else {
					array_push($error, $username);
				}
			}
		}

 		if($successnum === count($nameList)) {
			return array("ret" => "ok","success" => $success);
		} else {
			return array("ret" => "no", "errorUser" => $error, "success" => $success);
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

		$query	  =	self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		}
		return false;
	}
}
?>