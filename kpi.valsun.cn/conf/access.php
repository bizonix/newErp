<?php
if (!defined('WEB_PATH')) exit();

//后期可以自动推送

//白名单式的权限角色控制
return  array(
    //系统所支持的ACTION
	'SYSTEM_ACTIONS'	=>	array(
		"login",	//登录
		"picture"	//图片操作
	),													

	//钟衍台的权限配置
	"zhongyantai"	=>	array(
		"login"	=>	"all",			//支持login Action的所有操作
		"picture"	=>	"query"		//只支持picture的query操作
	),


	//zengxianghong的权限配置
	"zengxianghong"	=>	array(
		"login"		=>	"all",		//所有操作
		"picture"	=>	"all"		//所有操作
	),

);

?>