<?php
class UserAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//测试分文件夹的action
	function  act_index(){
		return array("user1","user2");
	}
}