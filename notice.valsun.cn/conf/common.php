<?php
if (!defined('WEB_PATH')) exit();
define("WEB_URL","http://198notice.valsun.cn/");
define("WEB_API","http://198api.notice.valsun.cn/");

//全局配置信息
return  array(
	"PREFIX"	 	=>	'nt_', 					//  表前缀
	//运行相关
	"RUN_LEVEL"		=>	"DEV",					//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	false,					//	开启日志记录
	"LOG_TYPE"		=>	3,						//	1.mail  2.file 3.api
	"LOG_PATH"		=>	WEB_PATH."log/log",		//  文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",						//	日志记录目标
	"LOG_EXTRA"		=>	"",						//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",					//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",				//	mysql	mssql	postsql	mongodb

	//mysql db配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("192.168.200.198", "root", "123456", "3306", "notice")	//主DB
		//"slave1"	=>	array("localhost","root","","3306")								//从DB
	),

	"CACHE_CONFIG"	=>	array(
		array("127.0.0.1","11211"),
		//array("192.168.200.122","11211")
	),
	"CACHEGROUP" 	=>	'notice_system_info',   	//memcache上的保存组名
	"CACHELIFETIME" =>	7200,     					//memcache 过期时间默认为 两小时

	"LANG"			=>	"zh",						//语言版本开关  zh , en

	//短信发送配置
	"SMS_SEND"		=>  true,

	//开放系统配置
	"OPEN_SYS_USER" 		=>	'Notice',
	"OPEN_SYSTOKEN"			=>	'f8d9aa94589f2e8fe1b5f82ba1eaa16b',
	'IS_LOCAL' 				=>	true, 												//是否使用内网开放系统地址
	'OPEN_SYS_URL_LOCAL' 	=>	'http://gw.open.valsun.cn:88/router/rest?',			//开放系统内网地址
	'OPEN_SYS_URL' 		 	=>	'http://idc.gw.open.valsun.cn/router/rest?',		//开放系统外网地址

	//鉴权系统相关配置
	//'AUTH_HTML_EXT' 	 	=>	'.htm',												//模版后缀
	'AUTH_SYSNAME' 		 	=>	'notice',											//系统名称
	'AUTH_SYSTOKEN' 	 	=>	'66ddc8ed0b81f080749311f494a39fe1',					//系统token

	//用户管理DEBUG
	'IS_DEBUG' 			 	=>	true,
	'IS_AUTH_ON' 		 	=>	true,												// 是否开启验证
	'USER_AUTH_TYPE'	 	=>	2,													// 验证模式1为登录时验证，2为实时验证
	'USER_AUTH_ID'		 	=>	'sysUserId', 										// 储存通用userId的SESSION 的 key
	'USER_AUTH_SYS_ID'	 	=>	'userId', 											// 储存分系统用户userId的SESSION 的 key
	'USER_COM_ID'		 	=>	'companyId',										// 储存companyId的SESSION 的 key
	'AUTH_SYSTEM_ID'	 	=>	'9',												// 鉴权系统ID
	'USER_AUTH_KEY'		 	=>	'userpowers',
	'USER_GO_URL'		 	=>	'index.php?mod=user&act=userList',					// 登录成功后跳转
	'NOT_AUTH_NODE' 	 	=>	'public-userLogin,public-login,public-logout',		// 默认无需认证模块

	//用户、权限、岗位表配置
	'TABLE_USER_SESSION' 			=>	'power_session',
	'TABLE_USER_INFO'	 			=>	'power_user',
	'TABLE_GLOBAL_USER_INFO'	 	=>	'power_global_user',
	'TABLE_USER_ONLINE'	 			=>	'',
	'TABLE_DEPT_INFO'	 			=>	'power_dept',
	'TABLE_JOB_INFO'	 			=>	'power_job',
	'TABLE_JOB_POWER'	 			=>	'power_jobpower',
	'TABLE_ACTION_INFO'	 			=>	'power_action',
	'TABLE_ACTION_GROUP' 			=>	'power_action_group',
	'TABLE_COMPANY_INFO' 			=>	'power_company',
);
?>
