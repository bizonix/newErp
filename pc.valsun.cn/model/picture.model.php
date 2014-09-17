<?php
class PictureModel{
	public static $dbConn;
	static $errCode	=	0;
	static $errMsg	=	"";

	public  function	initDB(){
		global $dbConn;
		self::$dbConn	=	$dbConn;
	}
	
	public static function getPictureTypeList(){
		self::initDB();
		$sql	=	"select * from type order by id desc";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
}
?>