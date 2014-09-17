<?php
/*********************************************
 * action层   
 * 对逻辑数据的操作
 * 
 * 小驼峰式命名法，例如：firstName、lastName
 * 大驼峰式命名法，FirstName、LastName、CamelCase
 * 
 * 文件命名： 	xxxAxxCxx.action.php
 * 类命名:		XxxAxxCxxAct(即文件命名大驼峰+Act)
 */
class OrderAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	/*************************************************
	 * 无任何参数的demo
	 */
	function  act_getOrderTestList(){
		//调用model层获取数据
		$list	=	OrderTestModel::getOrderTestList();
		if($list){
			return $list;
		}else{
			self::$errCode	=	OrderTestModel::$errCode;
			self::$errMsg	=	OrderTestModel::$errMsg;
			return false;
		}
	}
	
	
	/********************************************************
	 * 有参数的($_POST 或者  $_GET)
	 * 参数通过POST或GET传递,  主要用于API调用
	 */
	function  act_getOrderTestById(){
		//获取传递ID参数
		$id	=	isset($_GET['id']) ?  $_GET['id']: "";
		if (empty($id)){
			self::$errCode	=	"5001";		//action层的错误码
			self::$errMsg	=	"id不能为空";
			return false;
		}
		$list	=	OrderTestModel::getOrderTestById($id);
		if($list){
			return $list;
		}else{
			self::$errCode	=	OrderTestModel::$errCode;
			self::$errMsg	=	OrderTestModel::$errMsg;
			return false;
		}
	}
	
}


?>