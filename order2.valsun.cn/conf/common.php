<?php
if (!defined('WEB_PATH')) exit();
//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	=>	"/data/web/re.order.valsun.cn/log/",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb		
	
	//mysql db	配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("192.168.200.198", "root", "123456", "3306", "test_order")			//主DB
			
		//"slave1"	=>	array("localhost", "root", "", "3306")		//从DB
	),
	
	"CACHE_CONFIG"	=>	array(
		//array("192.168.200.222", "11211"),
		array("192.168.200.198", "11211")
	),
	
	"LANG"	=>	"zh",	//语言版本开关  zh , en
	
	"CACHEGROUP" => 'order_system_info',   //memcache上的保存组名
    "CACHELIFETIME" => 7200,     //memcache 过期时间默认为 两小时
	'DB_PREFIX'=>'om_',
	
	//mysql db	配置
	"RMQ_CONFIG"		=>	array(
		"fetchorder"	=>	array("192.168.200.198",'xiaojinhua','jinhua',"5672","mq_vhost1"),			                //测试机RabbitMQ
		//"fetchOrder"	=>	array("112.124.41.121","valsun_order","order%123","5672","order"),			            //生产环境RabbitMQ 获取订单
		//"fetchPower"	=>	array("115.29.188.246","valsun_power","power%123","5672","power"),			            //生产环境RabbitMQ 获取权限
		//"sendOrder"     =>	array("112.124.41.121","valsun_sendOrder","sendOrder%123","5672","sendOrder")			//生产环境RabbitMQ 获取权限
	),
	
	//开放系统配置		 
	'OPEN_SYS_URL_LOCAL' => 'http://gw.open.valsun.cn:88/router/rest?',//开放系统内网地址
	'OPEN_SYS_URL' 		 => 'http://idc.gw.open.valsun.cn/router/rest?',//开放系统外网地址
	'OPEN_SYS_USER'		 => 'Purchase',//开放系统用户名
	'OPEN_SYS_TOKEN' 	 => 'a6c94667ab1820b43c0b8a559b4bc909',//开放系统用户token
	
	//鉴权系统相关配置
	//'AUTH_HTML_EXT' 	 => '.htm',//模版后缀
	'AUTH_SYSNAME' 		 => 'Ordermanage',//系统名称
	'AUTH_SYSTOKEN' 	 => 'eccd25ddf4cddea9c46cf77fb6d78fa4',//系统token
	
	//自动加载文件目录配置--关联F函数
	'AUTO_DIR' 		 => array('functions'=>'file', 'class'=>'object', 'api'=>'object'),//文件目录
	
	//用户管理DEBUG
	'IS_DEBUG' 			 => true,
	'IS_AUTH_ON' 		 => true,	// 是否开启验证
	'USER_AUTH_TYPE'	 => 2,		// 验证模式1为登录时验证，2为实时验证
	'USER_AUTH_ID'		 => 'userId', // 储存userId的SESSION 的 keyy
	'USER_COM_ID'		 => 'companyId', // 储存companyId的SESSION 的 key
	'USER_AUTH_KEY'		 => 'userpowers',
	'USER_GO_URL'		 => 'index.php?mod=Order&act=index&ostatus=100&otype=101',
	'NOT_AUTH_NODE' 	 => 'public-userLogin,public-login,public-logout',	// 默认无需认证模块
	'AUTH_COMPANY_ID'    => 1,
	
	//用户、权限、岗位表配置
	'TABLE_USER_SESSION' => 'power_session',
	'TABLE_USER_INFO'	 => 'power_user',
	'TABLE_GLOBAL_USER_INFO' => 'power_global_user',
	'TABLE_USER_ONLINE'	 => '',
	'TABLE_DEPT_INFO'	 => 'power_dept',
	'TABLE_JOB_INFO'	 => 'power_job',
	'TABLE_JOB_POWER'	 => 'power_jobpower',
	'TABLE_ACTION_INFO'	 => 'power_action',
	'TABLE_ACTION_GROUP' => 'power_action_group',
	'TABLE_COMPANY_INFO' => 'power_company',
	
	//订单状态
	'ORDER_STATUS' => array(
		"waitingsend" => array(220,221,223), //小包缺货、快递缺货、待发货状态
		"outofStock" => array('220','221'),////(快递缺货、小包缺货)
		"waitingaudit" =>array('214'), //等待审核
		"partsalensend" =>array(), //部分包货的数量
		"intercepted" => array('210'),//自动拦截
		"superLargeBlocked" => array('219'),//超大拦截订单
    ),
    
    "ENABLE_AMAZON_GET_ORDER_CACHE"	=> 2,	//抓取amazon订单时候，使用启用缓存方便调试：0：不启用; 1：正常使用缓存; 2：更新缓存中的数据

);

?>
