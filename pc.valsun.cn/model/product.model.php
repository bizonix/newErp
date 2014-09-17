<?php
class ProductModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"pc_goods";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}


	public 	static function getProductList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql.'<br>';
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"0101";
			self::$errMsg  =	"getProductList";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}

	public 	static function getProductListNum(){
		self::initDB();
		$sql	 =	"select * from ".self::$table." order by id asc ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"0201";
			self::$errMsg  =	"getProductListNum";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}

	public 	static function getNewGoodsListNum($where){//统计新品数量分页
		self::initDB();
		$sql	 =	"select id from ".self::$table." $where";
		//echo $sql.'<br>';
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"0301";
			self::$errMsg  =	"getNewGoodsListNum";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}

	public 	static function getNewGoodsList($select, $where){
		self::initDB();
		$info = array();
		$sql	 =	"select $select from ".self::$table." $where";//isNew = 1  新品
//		echo $sql.'<br>';
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$info =self::$dbConn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"0401";
			self::$errMsg  =	"getNewGoodsList";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
}
?>