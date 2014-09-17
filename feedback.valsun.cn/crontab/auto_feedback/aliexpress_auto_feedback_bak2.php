<?php
    //脚本参数检验
    /*if($argc!=3){
    	exit("Usage: /usr/bin/php	$argv[0] eBayAccount minutes_ago \n");
    }*/

	if(!defined('WEB_PATH')){
		define("WEB_PATH","/data/scripts/ebay_order_cron_job/aliexpress/auto_feedback/");
	}	
	set_time_limit(0);   
	require_once WEB_PATH."Aliexpress.class.php";
	require_once WEB_PATH."config/common.php";

    $aliexpress_user   =   trim($argv[1]);  
    $logfile   =   WEB_PATH."logs/sync_order_".$aliexpress_user."_".date("Y-m-d").".log";	
    if(!array_key_exists($aliexpress_user, $erp_user_mapping)){
    	$log   = "\n\n\nDate: ".date("Y-m-d H:i:s")."error：账号不存在: ".$aliexpress_user."\n";
    	@file_put_contents($logfile, $log, FILE_APPEND);
        echo $log;
        exit;
    }
    $log	=	"\n\n\nDate: ".date("Y-m-d H:i:s"). " 开始速卖通账号".$aliexpress_user."的订单评价\n";
    @file_put_contents($logfile, $log, FILE_APPEND);
     
    //加载个性化配置信息
    $configFile = WEB_PATH."config/config_{$aliexpress_user}.php";
    if (file_exists($configFile)){
    	include_once $configFile;
    }else{
	    $log	=	"error：未找到".$aliexpress_user."对应的config文件!\n";
	    @file_put_contents($logfile, $log, FILE_APPEND);
        echo $log;
	    exit;
    }        
    $aliexpress = new Aliexpress();
    $aliexpress->setConfig($appKey,$appSecret,$refresh_token);
    $aliexpress->doInit();   

    $currentPage 	= 1;
    $pageSize		= 50;
    $score 		 	= "5";
    $content		= "Good buyer!It is our great honor that you will visit our company for the next time.";
    $orderList      = array();
    
    while(1){
    	$dataList = $aliexpress->getUnEvaluatedOrderlList($currentPage++, $pageSize, '', '', '');
    	if ( !$dataList['success'] || $dataList['totalItem'] == 0) {
    		$log = "Date: ".date("Y-m-d H:i:s"). " 账号".$aliexpress_user."的待评价订单拉取完成\n";
    		@file_put_contents($logfile, $log, FILE_APPEND);
    		break;
    	}
    	foreach ($dataList['listResult'] as $key => $data) {
    		$orderList[] = $data['orderId'];
    	}
    }
    
    foreach ($orderList as $order) {
    	$orderId = $order;
    	$result  = $aliexpress->setOrderEvaluation($orderId, $score, $content);
    	if ($result['success']) {
    		$log = "Date: ".date("Y-m-d H:i:s")." 订单".$orderId."评价成功\n";
    		@file_put_contents($logfile, $log, FILE_APPEND);
    		echo "---账号--$aliexpress_user-----订单--$orderId--------评价成功!\n";
    	} else {
    		$log = "Date: ".date("Y-m-d H:i:s")." 订单".$orderId."评价失败，错误信息:".$result['errorMessage']."\n";
    		@file_put_contents($logfile, $log, FILE_APPEND);
    		echo "---账号--$aliexpress_user-----订单--$orderId--------评价失败!--{$result['errorMessage']}\n";    		
        }
    }    
    exit; 
?>