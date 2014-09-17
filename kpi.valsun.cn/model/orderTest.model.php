<?php
/********************************************************
 * model层   
 * 对数据的操作(操作表： order_test)
 * 
 * 小驼峰式命名法，例如：firstName、lastName
 * 大驼峰式命名法，FirstName、LastName、CamelCase
 * 
 * 文件命名： 	xxxAxxCxx.model.php
 * 类命名:		XxxAxxCxxModel(即文件命名大驼峰+Model)
 * 
 */
class OrderTestModel{
	
	//静态数据dbConn(db操作类)
	public 	static $dbConn;
	//错误码, 每个函数里面都需要定义对应的错误码
	public	static $errCode	=	0;
	//错误信息, 
	public	static $errMsg	=	"";

	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn	=	$dbConn;
	}
	
	
	/*********************************************
	 * 方法数据返回要求， 及错误码，错误信息设置
	 * 失败： 返回false
	 * 成功： 返回数据(非空)
	 * 
	 */
	public 	static function getOrderTestList(){
		self::initDB();
		$sql	=	"select * from order_test order by id desc";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret	=	self::$dbConn->fetch_row($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode	=	"8001";
			self::$errMsg	=	"query error";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	
	/*********************************************
	 * 通过id拉取orderTest的信息
	 * 失败： 返回false
	 * 成功： 返回数据(非空)
	 * 
	 */
	public 	static function getOrderTestById($id){
		self::initDB();
		if(empty($id)){
			self::$errCode	=	"8002";		//设置另外一个错误码
			self::$errMsg	=	"id不能为空";
			return false;
		}
		$sql	=	"select * from order_testorder where id = '$id'";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret	=	self::$dbConn->fetch_array($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode	=	"8003";
			self::$errMsg	=	"query error";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
}
?>