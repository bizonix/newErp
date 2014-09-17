<?php
if (!defined('WEB_PATH')) exit();

//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	=>	"/data/web/erp.valsun.cn/kpi.valsun.cn/log/",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb		
	

	//mysql db	配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("192.168.200.188","cerp","123456","3306","cerp")			//主DB
		//"slave1"	=>	array("localhost","root","","3306")		//从DB
	),
	
	
	"CACHE_CONFIG"	=>	array(
		array("192.168.200.222","11211"),
		array("192.168.200.122","11211")
	),
	
	"LANG"	=>	"zh",	//语言版本开关  zh , en
	
);

?>