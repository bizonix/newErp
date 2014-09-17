<?php
/*
* paypal邮箱管理
* @author by  heminghua
*/
class paypalEmailModel extends BaseModel{
	public static function selectList($where){
		self::initDB();
		$sql = "SELECT * FROM om_paypal_email {$where}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	
	public static function get_account_paypalemails($accountId){
		self::initDB();
		$ret = self::selectList("where accountId = ".$accountId);
		if($ret){
			$returnArr = array();
			foreach($ret as $row){
				$returnArr[] = strtolower(trim($row['email']));
			}
			return $returnArr;
		}else{
			return array();
		}
	}

	public static function selectMsg($accountId){
		self::initDB();
		$sql = "SELECT a.account,b.platform FROM om_account AS a LEFT JOIN om_platform AS  b ON a.platformId=b.id WHERE a.id={$accountId}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectAccount(){
		self::initDB();
		$sql = "SELECT id,account FROM om_account order by account ";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function insertRecord($email,$accountId,$userId){
		self::initDB();
		$sql = "INSERT INTO om_paypal_email(email,accountId,createTime,creatorId) VALUES('{$email}',{$accountId},".time().",{$userId})";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
	public static function updateRecord($email,$accountId,$enable,$id){
		self::initDB();
		$sql = "UPDATE om_paypal_email SET email='{$email}',accountId={$accountId},status={$enable},modefyTime=".time()." WHERE id={$id}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
	public static function delRecord($id){
		self::initDB();
		$sql = "delete from  om_paypal_email  WHERE id={$id}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
}
?>