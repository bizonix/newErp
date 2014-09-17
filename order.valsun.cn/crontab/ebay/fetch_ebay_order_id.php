<?php
	//脚本参数检验
	if($argc!=3){
		exit("Usage: /usr/bin/php	$argv[0] eBayAccount minute_range \n");
	}
	$__ebayaccount=trim($argv[1]);
	$__minute_range=trim($argv[2]);
	
	#########全局变量设置########	
	//date_default_timezone_set('Asia/Chongqing');   
    $detailLevel = 0;	
	$Sordersn	= "eBay";
	$pagesize	= 20;//每页显示的数据条目数
	$mctime		= time();                    	
	$cc			= $mctime;
	$nowtime	= date("Y-m-d H:i:s",$cc);
	$nowd		= date("Y-m-d",$cc);
	#################以下时间范围用于测试#############
	if(!defined('WEB_PATH')){
		define("WEB_PATH","/data/web/order.valsun.cn/");
	}
	require_once WEB_PATH."crontab/scripts.comm.php";
	require_once WEB_PATH_LIB_SCRIPTS_EBAY."ebay_order_cron_func.php";
	
	$rmq_config	=	C("RMQ_CONFIG");
	//echo "<pre>"; var_dump($rmq_config); exit;
	$rabbitMQClass= new RabbitMQClass($rmq_config['fetchOrder'][1],$rmq_config['fetchOrder'][2],$rmq_config['fetchOrder'][4],$rmq_config['fetchOrder'][0]);//队列对象
	$omAvailableAct = new OmAvailableAct();
	$where = 'WHERE is_delete=0 ';
	$where .= 'AND platformId in(1,5) ';
	$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', $where);
	
	//echo "<pre>"; var_dump($GLOBAL_EBAY_ACCOUNT); exit;
	
	if(!preg_match('#^[\da-zA-Z]+$#i',$__ebayaccount)){
		exit("Invalid ebay account: $__ebayaccount!");
	}
	if(!in_array($__ebayaccount,$GLOBAL_EBAY_ACCOUNT)){
		exit("$__ebayaccount is not support now !");
	}
	if(!preg_match('#^[1-9]\d{1,3}$#i',$__minute_range)){
		exit("Invalid minute range [$__minute_range]\n");
	}
	
	//预先判断ebaytoken文件
	$__token_file = WEB_PATH_CONF_SCRIPTS_KEYS_EBAY.'keys_'.$__ebayaccount.'.php';
	if(!file_exists($__token_file)){
		exit($__token_file." does not exists!!!");
	}
	
	/*$_SESSION['user']	= 'vipchen';
	$user	= $_SESSION['user'];*/	
	//require_once "ebay_order_cron_config.php";
	
	require_once WEB_PATH_LIB_SDK_EBAY."GetOrdersID.php";
	#############类或API 实例化##############
	$api_goi		=new GetOrdersIDAPI($__ebayaccount);
	
	#########加载lost_order_id########
	$relost_order_ids = '';
	$push_order_ids = '';
	$lost_orderid_path = EBAY_RAW_DATA_PATH.'lost_ebay_orderid/'.$__ebayaccount.'/lost_sql.txt';			
	$orderid_content = read_and_empty_lost_sql($lost_orderid_path);
	$orderid_lists = sql_str2array($orderid_content);
	if(!empty($orderid_lists)){
		foreach($orderid_lists AS $orderid_sql){
			if(!$dbConn->execute($orderid_sql)){
				$relost_order_ids .= $orderid_sql."\n";
			}else{
				$push_order_ids .= $orderid_sql."\n";
			}
		}
		if(!empty($relost_order_ids)){
			write_lost_sql($lost_orderid_path, $relost_order_ids);
		}
		if(!empty($push_order_ids)){
			write_lost_sql(str_replace('lost_sql.txt', 'push_success_sql.txt', $lost_orderid_path), $push_order_ids);
		}
	}
	
	$account		= $__ebayaccount;
	$ebay_start		= get_ebay_timestamp($cc-(60*$__minute_range));//specific minutes agao
	$ebay_end		= get_ebay_timestamp($cc);
	$ebay_start		=date('Y-m-d\TH:i:s',$ebay_start);
	$ebay_end		=date('Y-m-d\TH:i:s',$ebay_end);
	
	//程序计时器
	$time_start=$cc;
	echo "\n=====[".date('Y-m-d H:i:s',$time_start)."]系统【开始】同步账号【 $account 】订单 ====>\n";
	echo "同步订单范围From: $ebay_start To $ebay_end \n";
	
	$api_goi->GetSellerOrdersID($ebay_start,$ebay_end,$account);
	
	$time_end=time();
	echo "\n=====[耗时:".ceil(($time_end-$time_start)/60)."分钟]====\n";
	echo "\n<====[".date('Y-m-d H:i:s',$time_end)."]系统【结束】同步账号【 $account 】订单\n";
	exit;
?>