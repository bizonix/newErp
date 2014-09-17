<?php
if (!defined('WEB_PATH')) exit();
define("WEB_URL","");
define("WEB_API","http://api.198track.valsun.cn/");
define("WEB_PIC_URL","http://img.tran.valsun.cn/images/");
//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	    =>	WEB_PATH."/log/log",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb
	"DB_PREFIX"     =>	"tracks_", 	//数据库表前缀


	//mysql db	配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("localhost","root","123456","3306","valsun_track")			//主DB
		//"slave1"	=>	array("localhost","root","","3306")		//从DB
	),
	
	
	"CACHE_CONFIG"	=>	array(
		array("192.168.200.198","11211"),
		//array("192.168.200.122","11211")
	),
	
	"LANG"	=>	"zh",	//语言版本开关  zh , en
    
    "CACHEGROUP" => 'tracks_system_userinfo',   //memcache上的保存组名
    "CACHELIFETIME" => 7200,     //memcache 过期时间默认为 两小时
	//跟踪号系统状态码说明
	'TRACK_STATUS_DETAIL'=> array(
		'0'	=> '未发送','1'	=> '已发送','2'	=> '转运中',
		'3'	=> '妥投','4'	=> '超时','5'	=> '扣关',
		'6'	=> '地址错误','7'	=> '快件丢失','8'	=> '退件',
		'9'	=> '其它异常','10'	=> '销毁'),
	//运输方式英文信息
	'TRACK_NAME'=> array(
		'运德物流'	=> 'WeDo','EMS'	=> 'EMS/ePacket','中国邮政'	=> 'China Post','DHL'	=> 'DHL',
		'FEDEX'	=> 'FEDEX','美国邮政'	=> 'USPS','UPS'	=> 'UPS',
		'德国邮政'	=> 'Deutsche Post','新加坡邮政'	=> 'Sing Post',
		'瑞士邮政' => 'Swiss Post'),	
	//开放系统配置		 
	'OPEN_SYS_URL_LOCAL' => 'http://gw.open.valsun.cn:88/router/rest?',//开放系统内网地址
	'OPEN_SYS_URL' 		 => 'http://idc.gw.open.valsun.cn/router/rest?',//开放系统外网地址
	'OPEN_SYS_USER'		 => 'Purchase',//开放系统用户名
	'OPEN_SYS_TOKEN' 	 => 'a6c94667ab1820b43c0b8a559b4bc909',//开放系统用户token	
	//鉴权系统相关配置
	'AUTH_SYSNAME' 		 => 'wedoExpress',//系统名称
	'AUTH_SYSTOKEN' 	 => '4ab79afe173aeed3517274595116f38d',//系统token
	//用户管理DEBUG
	'IS_DEBUG' 			 => true,
	'IS_AUTH_ON' 		 => true,	// 是否开启验证
	'SYSTEM_VERSION'	 => "V2.1 Release",	// 系统版本号
	'USER_AUTH_TYPE'	 => 2,		// 验证模式1为登录时验证，2为实时验证
	'USER_AUTH_ID'		 => 'userId', // 储存分系统SESSION 的 key
	'USER_AUTH_SYS_ID'	 => 'sysUserId', // 储存统一用户的SESSION 的 key
	'USER_COM_ID'		 => 'companyId', // 储存companyId的SESSION 的 key
	'USER_MAX_COUNT'	 => 300, // 查询过期时间内能最大查询多少次
	'USER_EXPIRES_TIME'	 => 86400, // 查询过期时间
	'AUTH_SYSTEM_ID'	 => '24',   //鉴权系统ID
	'AUTH_COMPANY_ID'	 => '1',   //鉴权公司ID
	'USER_AUTH_KEY'		 => 'userpowers',
	'USER_GO_URL'		 => 'index.php?mod=trackInquiry&act=index',
	'NOT_AUTH_NODE' 	 => 'public-userLogin,public-login,public-logout,trackInquiry-index,trackInquiry-track,webSinglePage-view,trackShipFee-index',	// 默认无需认证模块
	//用户、权限、岗位表配置
	'TABLE_USER_SESSION' => 'power_session',
	'TABLE_USER_INFO'	 => 'power_user',
	'TABLE_GLOBAL_USER_INFO'	 => 'power_global_user',
	'TABLE_USER_ONLINE'	 => '',
	'TABLE_DEPT_INFO'	 => 'power_dept',
	'TABLE_JOB_INFO'	 => 'power_job',
	'TABLE_JOB_POWER'	 => 'power_jobpower',
	'TABLE_ACTION_INFO'	 => 'power_action',
	'TABLE_ACTION_GROUP' => 'power_action_group',
	'TABLE_COMPANY_INFO' => 'power_company',
);
?>