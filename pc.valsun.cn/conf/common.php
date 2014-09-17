<?php
if (!defined('WEB_PATH')) exit();
define("WEB_URL","http://pc.valsun.cn/");
define("WEB_API","http://api.pc.valsun.cn/");
//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	=>	"/data/web/pc.valsun.cn/log/",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb


	//mysql db	配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("localhost","root","123456","3306","valsun_pc")			//主DB
		//"slave1"	=>	array("localhost","root","","3306")		//从DB
	),


	"CACHE_CONFIG"	=>	array(
		array("192.168.200.222","11211"),
		//array("192.168.200.122","11211")
	),

	"LANG"	=>	"zh",	//语言版本开关  zh , en

	"CACHEGROUP" => 'pc_system_info',   //memcache上的保存组名
    "CACHELIFETIME" => 7200,     //memcache 过期时间默认为 两小时
	    'DB_PREFIX'=>'pc_',
    
    //MQ相关配置
    "MQSWITH" => 'NO',//是否开启MQ发布信息
    "MQSERVERADDRESS"=>'192.168.200.222'
    //"MQSERVERADDRESS"=>'115.29.188.246'
    ,
    "MQUSER"=>'xiaojinhua',
    "MQPSW"=>'jinhua',
    "MQVHOST"=>'/',
    //这里是 pc_goods, pc_goods_combine,pc_sku_combine_relation 这3个表的队列同步的交换机名称
    "MQ_EXCHANGE"=>'rabbitmq_goods_exchange',//发布队列的交换器名称
    
    "MQ_CATEGROY_EXCHANGE"=>'rabbitmq_category_exchange',//类别的
    "MQ_SKUCONVERSION_EXCHANGE"=>'rabbitmq_skuConversion_exchange',//料号转换的
    "MQ_GOODSPARTNER_EXCHANGE"=>'rabbitmq_goodsPartner_exchange',//sku供应商关系的
	//鉴权系统相关配置
	//'AUTH_HTML_EXT' 	 => '.htm',//模版后缀
	'AUTH_SYSNAME' 		 => 'ProductCenter',//系统名称
	'AUTH_SYSTOKEN' 	 => '507c44e32a5d14cf25de1d53ddccb0f0',//系统token
	//用户管理DEBUG
	'IS_DEBUG' 			 => true,
	'IS_AUTH_ON' 		 => true,	// 是否开启验证
	'USER_AUTH_TYPE'	 => 2,		// 验证模式1为登录时验证，2为实时验证
	'USER_AUTH_ID'		 => 'sysUserId', // 储存userId的SESSION 的 key
	'USER_COM_ID'		 => 'companyId', // 储存companyId的SESSION 的 key
	'USER_AUTH_KEY'		 => 'userpowers',
	'USER_GO_URL'		 => 'index.php?mod=goods&act=getGoodsList',
	'NOT_AUTH_NODE' 	 => 'public-userLogin,public-login,public-logout',	// 默认无需认证模块

	//用户、权限、岗位表配置
	'TABLE_USER_SESSION' => 'power_session',
	'TABLE_USER_INFO'	 => 'power_user',
	'TABLE_USER_ONLINE'	 => '',
	'TABLE_DEPT_INFO'	 => 'power_dept',
	'TABLE_JOB_INFO'	 => 'power_job',
	'TABLE_JOB_POWER'	 => 'power_jobpower',
	'TABLE_ACTION_INFO'	 => 'power_action',
	'TABLE_ACTION_GROUP' => 'power_action_group',
	'TABLE_COMPANY_INFO' => 'power_company',
);

?>