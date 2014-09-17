<?php
include_once	WEB_PATH."model/picture.model.php";
class PictureAct extends Auth{
		
	static $errCode	=	0;
	static $errMsg	=	"";

	public function act_login(){
		$auth	=	new Auth();
		$auth->login();
	}

	public function act_gettype(){
		$ret	=	parent::checkAccess("picture","gettype");
		if(!$ret){
			self::$errCode	=	Auth::$errCode;
			self::$errMsg	=	Auth::$errMsg;
			return false;
		}
		$ret	=	PictureModel::getPictureTypeList();
		if($ret){
			return $ret;
		}else{
			self::$errCode	=	PictureModel::$errCode;
			self::$errMsg	=	PictureModel::$errMsg;
			return false;
		}
	}
}
?>