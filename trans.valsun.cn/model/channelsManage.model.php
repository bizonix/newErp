<?php
class channelsManageModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		ob_clean();
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

		$sql	 =	"INSERT INTO trans_channels(carrierId,channelName,channelAlias,enable,createdTime,is_delete) values ({$carrierId},'{$carrierName}','',1,".time().",0)";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			return mysql_insert_id();	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	
	}
	public static function insertChannelsall($carrierId,$channelName,$channelAlias,$discount=0.0000,$enable=0){
		self::initDB();

		$sql	 =	"INSERT INTO trans_channels(carrierId,channelName,channelAlias,discount,enable,createdTime,is_delete) values ({$carrierId},'{$channelName}','{$channelAlias}',{$discount},1,".time().",{$enable})";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			return self::$dbConn->insert_id();
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	
	}
	public static function modifychannel($id,$channelName,$channelAlias,$discount,$enable){
		self::initDB();

		$sql	 =	"update trans_channels set channelName='{$channelName}',channelAlias='{$channelAlias}',enable={$enable},discount={$discount} where id={$id}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	
	public static function channelsShow(){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_channels where is_delete=0";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function transnametoid($transname){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_carrier where is_delete=0 and carrierNameCn='{$transname}'";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function transById($id){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_carrier where is_delete=0 and id={$id}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function channelShowById($id){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_channels where is_delete=0 and id ={$id}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function channelShowByWhere($where){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_channels {$where}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function carrierShowById($id){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_carrier where id={$id}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
}
?>