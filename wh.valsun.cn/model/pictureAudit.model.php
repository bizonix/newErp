<?php
class PictureAuditModel {
	public 	static $dbConn;
	public	static $errCode		=	0;
	public	static $errMsg		=	"";
	static  $table				=	"wh_picture_audit";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	public static function insertstatus(){
		self::initDB();
		
	}
	public static function isaudit($ordersn,$stype){
		self::initDB();
		$sql		=	"select audit_status from ".self::$table." where ebay_ordersn='$ordersn' and picture_type='$stype'";
		$query		=    self::$dbConn->query($sql);
		$res		=	 self::$dbConn->fetch_row($query);
		//echo $sql;
		//var_dump($res);exit;
		if($res){
			//var_dump($res);exit;
			return   $res[0];
		}else{
			return 2;  //audit_status=2表示未评分，没有查询到在记录中也是为评分状态
		}		
	}
	
	public static function insertAudit($ordersn,$status	,$audituser,$scanuser,$time,$scantime,$stype){
		self::initDB();
		$table 		=	self::$table;
		$sql		=	"insert into $table (id,ebay_ordersn,picture_type,audit_status,scanuser,audituser,audit_time,scantime)";
		$sql		.=" values ('0', $ordersn,'$stype',$status,'$scanuser','$audituser','$time','$scantime')";
		//echo $sql;exit;
		$query		=	self::$dbConn->query($sql);
		if($query){
			$res		=	self::$dbConn->insert_id();
		}else{
			self::$errCode	=201;
			self::$errMsg	="insert error";
			return false;
		}
	}
	
	public static function updateAudit($ordersn,$status	,$audituser,$scanuser,$time){
		self::initDB();
		$sql	=	"update ".self::$table." set audit_status=$status,scanuser='$scanuser',audit_time='$time' where ebay_ordersn=$ordersn and scanuser='$scanuser'";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	= 202;
			self::$errMsg	= 'update error';
			return false;
		}
	}
	
	public static function getAuditPicture($ordersn,$starttime,$endtime,$scanuser,$pictype,$pic_status,$limit){
		self::initDB();
		$table  = self::$table;
		$sql	=	"select  ebay_ordersn,scantime,scanuser,audit_status,picture_type from $table WHERE 1=1";
		if(!empty($ordersn) ){
			$sql	.=" AND ebay_ordersn='$ordersn'";
		}else{
			if(!empty($pic_status) || $pic_status==="0"){
				$sql	.=" AND audit_status=$pic_status";
			}
			if(!empty($starttime)){
				$sql	.=" AND scantime>='$starttime'";
			}
			if(!empty($endtime)){
				$sql	.=" AND scantime<='$endtime'";
			}
			if(!empty($scanuser)){
				$sql	.=" AND scanuser='$scanuser'";
			}
			if(!empty($pictype)){
				$sql	.=" AND picture_type='$pictype'";
			}else{
				$sql	.=" AND picture_type='fh' OR picture_type='cz'";
			}
		}
		if(!empty($limit)){
			$sql		.=	" limit $limit";
		}
		//echo $sql;exit;
		$query	=	self::$dbConn->query($sql);
		$res	=	self::$dbConn->fetch_array_all($query);
		if(count($res)>0){
			return $res;
		}else{
			self::$errCode	= 203;
			self::$errMsg	= 'select error';
			return false;
		}
	}
	
	public static function exceloutput($starttime,$endtime,$scanuser,$pictype,$pic_status){
		self::initDB();
		$table  = self::$table;
		$sql	=	"select  ebay_ordersn,scanuser,picture_type,audit_status,audituser,scantime from $table WHERE audit_status=$pic_status";
		if(!empty($starttime)){
			$sql	.=" AND scantime>='$starttime'";
		}
		if(!empty($endtime)){
			$sql	.=" AND scantime<='$endtime'";
		}
		if(!empty($scanuser)){
			$sql	.=" AND scanuser='$scanuser'";
		}
		if(!empty($pictype)){
			$sql	.=" AND picture_type='$pictype'";
		}else{
			$sql	.=" AND picture_type='fh' OR picture_type='cz'";
		}
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		$res	=	self::$dbConn->fetch_array_all($query);
		if(count($res)>0){
			return $res;
		}else{
			self::$errCode	= 203;
			self::$errMsg	= 'select error';
			return false;
		}
	}
	
}
?>