<?php
/*
 * 对外提供对应邮件收件人Api接口
 */
class MailApiAct {
	public static $errCode	=	0;
	public static $errMsg	=	"";
	static $debug	  		= false;
	
	
	
	public function act_getUserList() {
		$list_english_id	= addslashes($_GET['english_id']);
		$list_english_id	= trim($list_english_id);
// 		print_r($list_english_id);
		if ($list_english_id === ''){
			self::$errCode 	= '5506';
			self::$errMsg  	= 'Mail english_id is null,please input again!';
			return array();
		} else {
			$getData			= new MailApiModel();
			$getUserList		= $getData->checkPower($list_english_id);
			return $this->_checkReturnData($getUserList, array());
		}
	}
	
	private function _checkReturnData($data, $errreturn){
		if ($data === false){
			self::$errCode = UserModel::$errCode;
			self::$errMsg  = UserModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = '5506';
			self::$errMsg  = 'The english_id is not exists or there is no person subscribe to this mail right now,please check out.';
			if (self::$debug===true){
				self::$errMsg .= 'The SQL is '.UserModel::$errMsg;
			}				
			return $errreturn;
		}else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
}