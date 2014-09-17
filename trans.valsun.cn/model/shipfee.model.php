<?php
class shipfeeModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	//static  $table			=	"trans_platform";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}

	public static function cprg_fujian($where=""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_cprg_fujian {$where}";
	
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
	public static function modify_cprg_fujian($id,$groupName,$countries,$unitPrice,$handlefee){
		self::initDB();
		$sql = "update trans_freight_cprg_fujian set groupName='{$groupName}',countries='{$countries}',unitPrice={$unitPrice},handlefee={$handlefee},lastmodified=".time()." where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	public static function cpsf_fujian($where =""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_cpsf_fujian {$where}";
	
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
	public static function modify_cpsf_fujian($id,$groupName,$countries,$unitPrice,$handlefee){
		self::initDB();
		$sql = "update trans_freight_cpsf_fujian set name='{$groupName}',countries='{$countries}',unitPrice={$unitPrice},handlefee={$handlefee} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	public static function cpsf_shenzhen($where=""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_cpsf_shenzheng {$where}";
	
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
	public static function modify_cpsf_shenzhen($id,$groupName,$countries,$firstweight){
		self::initDB();
		$sql = "update trans_freight_cpsf_shenzheng set name='{$groupName}',countries='{$countries}',firstweight={$firstweight} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
    public static function dhl_shenzheng($where =""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_dhl_shenzheng {$where}";
	
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
	public static function modify_dhl_shenzhen1($weightfreight,$partition,$mode){
		self::initDB();
		$sql = "update trans_freight_dhl_shenzhen set weight_freight='{$weightfreight}',modifiedtime=".time()." where partition={$partition} AND mode={$mode}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	public static function modify_dhl_shenzhen2($countries,$partition,$mode){
		self::initDB();
		$sql = "update trans_freight_dhl_shenzhen set country='{$countries}',modifiedtime=".time()." where partition={$partition} AND mode={$mode}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
    public static function eub_shenzheng($where=""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_eub_shenzheng {$where}";
	
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
	public static function modify_eub_shenzhen($id,$groupName,$countries,$unitprice,$handlefee){
		self::initDB();
		$sql = "update trans_freight_eub_shenzheng set name='{$groupName}',countrys='{$countries}',unitprice={$unitprice},handlefee={$handlefee} where id={$id}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}



	
    public static function ems_shenzheng(){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_ems_shenzheng ";
	
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
    public static function modify_ems_shenzhen($id,$groupName,$countries,$firstweight,$firstweight0,$nextweight,$files,$declared_value){
		self::initDB();
		$sql = "update trans_freight_eub_shenzheng set groupName='{$groupName}',countries='{$countries}',firstweight={$firstweight},firstweight0={$firstweight0},nextweight={$nextweight},files={$files},declared_value={$declared_value} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	public static function globalmail_shenzheng(){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_globalmail_shenzheng ";
	
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
	public static function modify_golbalmail_shenzhen($country,$freight_str,$fuel_str){
		self::initDB();
		$sql = "update trans_freight_globalmail_shenzheng set weight_freight='{$freight_str}',fuelcosts='{$fuel_str}',modifytime=".time()." where country='{$country}'";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	public static function hkpostrg_hk($where){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_hkpostrg_hk {$where}";
	
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
	public static function modify_hkpostrg_hk($id,$groupName,$countries,$firstweight,$nextweight,$handlefee){
		self::initDB();
		$sql = "update trans_freight_hkpostrg_hk set name='{$groupName}',countrys='{$countries}',firstweight={$firstweight},nextweight={$nextweight},handlefee={$handlefee} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	
	public static function hkpostsf_hk(){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_hkpostsf_hk ";
	
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
    public static function modify_hkpostsf_hk($id,$groupName,$countries,$firstweight,$nextweight,$handlefee){
		self::initDB();
		$sql = "update trans_freight_hkpostsf_hk set name='{$groupName}',countrys='{$countries}',firstweight={$firstweight},nextweight={$nextweight},handlefee={$handlefee} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
	
	
	
	public static function fedex_shenzhen($where=""){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_fedex_shenzhen {$where}";
	    
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
	public static function fedex_shenzhen_nums(){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_freight_fedex_shenzhen ";
	
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}		
    public static function modify_fedex_shenzhen($type,$fuel,$country,$weight,$fee){
		self::initDB();
		$sql = "update trans_freight_fedex_shenzhen set unitprice={$fee},baf={$fuel} where weightinterval ='{$weight}' AND countrylist='{$country}' AND type='{$type}'";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}
	}
	
}

?>