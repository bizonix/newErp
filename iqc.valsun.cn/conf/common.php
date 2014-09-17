<?php
if (!defined('WEB_PATH')) exit();

//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	=>	"E:/xampp/htdocs/erpNew/iqc.valsun.cn/log/",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb		
	

	//mysql db	配置
	"DB_CONFIG"		=>	array(
		//"master1"	=>	array("192.168.200.222","cerp","123456","3306","valsun_qccenter")			//主DB
		"master1"	=>	array("127.0.0.1","root","","3306","valsun_qccenter")			//主DB
		//"slave1"	=>	array("localhost","root","","3306")		//从DB
	),
	/* smarty 模板路径和缓存路径 */
	//"DIR_WS_TEMPLATES" =>"/data/web/erpNew/iqc.valsun.cn/html/v1/",
	//"DIR_WS_TEMPLATES_C" =>"/data/web/erpNew/iqc.valsun.cn/html/v1/templates_c/",
	"DIR_WS_TEMPLATES" =>"E:/xampp/htdocs/erpNew/iqc.valsun.cn/html/v1/",
	"DIR_WS_TEMPLATES_C" =>"E:/xampp/htdocs/erpNew/iqc.valsun.cn/html/v1/templates_c/",
	
	"CACHE_CONFIG"	=>	array(
		array("192.168.200.222","11211"),
		array("192.168.200.122","11211")
	),
	
	"LANG"	=>	"zh",	//语言版本开关  zh , en
    
        "CACHEGROUP" => 'transport_system_userinfo',   //memcache上的保存组名
        "CACHELIFETIME" => 7200,     //memcache 过期时间默认为 两小时
	
);

?>