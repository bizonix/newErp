<?php
class addNewCarrierModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	//static  $table			=	"trans_platform";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}


	

	public 	static function insertTransName($carrierId,$carrierName,$platformId){
		self::initDB();

		$sql	 =	"INSERT INTO trans_carrierName(carrierId,carrierName,platformId) values ({$carrierId},'{$carrierName}',{$platformId})";
		
		$query	 =	self::$dbConn->query($sql);
		
        
		if($query){
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	public static function insertCarrier($transnamec,$transnamee,$weightmin,$weightmax,$timecount){
		self::initDB();

		$sql	 =	"INSERT INTO trans_carrier(carrierNameCn,carrierNameEn,weightMin,weightMax,timecount,createdTime,is_delete) values ('{$transnamec}','{$transnamee}',{$weightmin},{$weightmax},{$timecount},".time().",0)";
		
		$query	 =	self::$dbConn->query($sql);
		if($query){
			return mysql_insert_id();	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	
	}
	public static function insertChannels($carrierId,$carrierName){
		self::initDB();

		$sql	 =	"INSERT INTO trans_channels(carrierId,channelName,channelAlias,enable,createdTime,is_delete) values ('{$carrierId}','{$carrierName}','',1,".time().",0)";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			return mysql_insert_id();	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	
	}
}

?>