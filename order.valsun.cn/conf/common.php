<?php
if (!defined('WEB_PATH')) exit();
define("WEB_URL","http://order.valsun.cn/");
define("WEB_API","http://api.order.valsun.cn/");
//全局配置信息
return  array(
	//运行相关
	"RUN_LEVEL"		=>	"DEV",		//	运行模式。 DEV(开发)，GAMMA(测试)，IDC(生产)

	//日志相关
	"LOG_RECORD"	=>	true,		//	开启日志记录
	"LOG_TYPE"		=>	3,			//	1.mail  2.file 3.api
	"LOG_PATH"	=>	"E:/erpNew/order.valsun.cn/log/",	//文件日志目录
	"LOG_FILE_SIZE"	=>	2097152,
	"LOG_DEST"		=>	"",			//	日志记录目标
	"LOG_EXTRA"		=>	"",			//	日志记录额外信息

	//数据接口相关
	"DATAGATE"		=>	"db",		//	数据接口层 cache, db, socket
	"DB_TYPE"		=>	"mysql",	//	mysql	mssql	postsql	mongodb		
	
	//mysql db	配置
	"DB_CONFIG"		=>	array(
		"master1"	=>	array("192.168.200.122","cerp","123456","3306","valsun_order")			//主DB
		//"slave1"	=>	array("localhost","root","","3306")		//从DB
	),
	
	"CACHE_CONFIG"	=>	array(
		//array("192.168.200.222","11211"),
		array("112.124.41.121","11211")
	),
	
	"LANG"	=>	"zh",	//语言版本开关  zh , en
	
	"CACHEGROUP" => 'order_system_info',   //memcache上的保存组名
    "CACHELIFETIME" => 7200,     //memcache 过期时间默认为 两小时
	'DB_PREFIX'=>'om_',
	
	//mysql db	配置
	"RMQ_CONFIG"		=>	array(
		//"fetchorder"	=>	array("localhost",'xiaojinhua','jinhua',"5672","mq_vhost1"),			                //测试机RabbitMQ
		"fetchOrder"	=>	array("112.124.41.121","valsun_order","order%123","5672","order"),			            //生产环境RabbitMQ 获取订单
		"fetchPower"	=>	array("115.29.188.246","valsun_power","power%123","5672","power"),			            //生产环境RabbitMQ 获取权限
		"sendOrder"     =>	array("112.124.41.121","valsun_sendOrder","sendOrder%123","5672","sendOrder")			//生产环境RabbitMQ 获取权限
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
	
	//用户管理DEBUG
	'IS_DEBUG' 			 => true,
	//'IS_AUTH_ON' 		 => false,	// 是否开启验证
	'IS_AUTH_ON' 		 => false,	// 是否开启验证
	'USER_AUTH_TYPE'	 => 2,		// 验证模式1为登录时验证，2为实时验证
	'USER_AUTH_ID'		 => 'userId', // 储存userId的SESSION 的 keyy
	'USER_COM_ID'		 => 'companyId', // 储存companyId的SESSION 的 key
	'USER_AUTH_KEY'		 => 'userpowers',
	'USER_GO_URL'		 => 'index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=101',
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
		"waitingsend" => array(101,102,103,104,105), //待发货
		"interceptsend" => array(),//自动拦截
		"waitingaudit" =>array(), //等待审核
		"partpackage" =>array(), //部分包货的数量
		"bigOrder" => array(200,201,202,203,204,205)
	),
	
	'ERP_USER_MAPPING'	=>	array(
	        "3ACYBER"		=>	"cn1000268236",
	        "szsunweb"		=>	"cn1000421358",
	        "E-Global"		=>	"cn1000616054",
	        "beauty365"		=>	"cn1000960806",
	        "caracc"		=>	"cn1000983412",
	        "Bagfashion"	=>	"cn1000983826",
	        "prettyhair"	=>	"cn1000999030",
	        "LovelyBaby"	=>	"cn1001428059",
	        "Finejo"		=>	"cn1001392417",
	        "5season"		=>	"cn1001424576",
	        "fashiondeal"	=>	"cn1001656836",
	        "Sunshine"		=>	"cn1001711574",
	        "fashionqueen"	=>	"cn1001718610",
	        "shiningstar"	=>	"cn1001739224",
	        "babyhouse"		=>	"cn1500053764",
	        "fashionzone"	=>	"cn1500152370",
	        "shoesacc"		=>	"cn1500226033",
	        "superdeal"		=>	"cn1500293467",
	        "istore"		=>	"cn1500439756",
	        "ladyzone"		=>	"cn1500514645",
	        "beautywomen"	=>	"cn1500688776",
	        "womensworld"	=>	"cn1501288533",
	        "myzone"		=>	"cn1501287427",
	        "homestyle"		=>	"cn1501540493",	//2013-08-01
	        "championacc"	=>	"cn1501578304",	//2013-08-01
	        "digitallife"	=>	"cn1501595926",	//2013-08-01
	        "Etime"			=>	"cn1501638006",	//2013-08-20
	        "citymiss"		=>	"cn1510304665",
	        "zeagoo360"		=>	"cn1500440054",
	
	        //taotaoAccount
	        "taotaocart"	=>	"cn1501642501",
	        "arttao"		=>	"cn1501654678",
	        "taochains"		=>	"cn1501654797",
	        "etaosky"		=>	"cn1501655651",
	        "tmallbasket"	=>	"cn1501656206",
	        "mucheer"		=>	"cn1501656494",
	        "lantao"		=>	"cn1501657160",
	        "direttao"		=>	"cn1501657334",
	        "hitao"			=>	"cn1501657572",
	        "taolink"		=>	"cn1501686293",
	
	        //----------
	
	        //surfaceAccount
	        "acitylife"		=> "cn1510515579",
	        "etrademart"	=> "cn1510509503",
	        "centermall"	=> "cn1510509429",
	        "viphouse"		=> "cn1510514024",
	
	        //---------
	
	)
	
);

?>
