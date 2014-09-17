<?php
/*
 * 开放api接口查询
 */
class openapiModel {
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	//static  $table			=	"trans_platform";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	/*
	*用小语种国家名取得英文国家名
	*/
	public static function selectCountryNameEnBySmall($smallCountry){
		self::initDB();

		$sql	 =	"SELECT countryName FROM trans_countries_small_comparison where small_country='{$smallCountry}'";
	
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
	
	/*
	*用国家名简称取得英文国家名
	*/
	public static function selectCountryNameEnBySn($countrySn){
		self::initDB();

		$sql	 =	"SELECT countryNameEn FROM trans_countries_standard where countrySn='{$countrySn}'";
	
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
}
?>