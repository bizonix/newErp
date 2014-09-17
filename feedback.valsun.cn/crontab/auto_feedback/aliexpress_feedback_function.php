<?php

    set_time_limit(0); 
	if(!defined('WEB_PATH')){
		define("WEB_PATH","/data/scripts/ebay_order_cron_job/aliexpress/auto_feedback/");
	}	  
	require_once WEB_PATH."Aliexpress.class.php";
	require_once WEB_PATH."config/common.php";

    function aliexpress_setEvaluation($account,$orderIdArr,$score,$content) {

        $aliexpress_user    =   $account;        
        if (!in_array($account, $erp_user_mapping)) {
            $log   = "\n\n\nDate: ".date("Y-m-d H:i:s")."error：账号不存在: ".$account."\n";
            @file_put_contents($logfile, $log, FILE_APPEND);
            return false;
        }
        $aliexpress_account_mapping  =  array_flip($erp_user_mapping);
        $aliexpress_account_login    =  $aliexpress_account_mapping[$account];
        echo "---------------$aliexpress_account_login---------------";exit;
        $logfile   =   WEB_PATH."logs/sync_order_".$aliexpress_account_login."_".date("Y-m-d").".log";   
        if(!array_key_exists($aliexpress_account_login, $erp_user_mapping)){
            $log   = "\n\n\nDate: ".date("Y-m-d H:i:s")."error：账号不存在: ".$aliexpress_user."\n";
            @file_put_contents($logfile, $log, FILE_APPEND);
            return false;
        }

        $log   =   "\n\n\nDate: ".date("Y-m-d H:i:s"). " 开始速卖通账号".$aliexpress_user."的订单评价\n";
        @file_put_contents($logfile, $log, FILE_APPEND);
         
        //加载个性化配置信息
        $configFile = WEB_PATH."config/config_{$aliexpress_user}.php";
        if (file_exists($configFile)){
            include_once $configFile;
        }else{
            $log    =   "error：未找到".$aliexpress_user."对应的config文件!\n";
            @file_put_contents($logfile, $log, FILE_APPEND);
            return false;
        }        
        $aliexpress = new Aliexpress();
        $aliexpress->setConfig($appKey,$appSecret,$refresh_token);
        $aliexpress->doInit();  

        foreach ($orderIdArr as $order) {
            $orderId = $order;
            $result  = $aliexpress->setOrderEvaluation($orderId, $score, $content);
            if ($result['success']) {
                $log = "Date: ".date("Y-m-d H:i:s")." 订单".$orderId."评价成功\n";
                //@file_put_contents($logfile, $log, FILE_APPEND);
                echo "---账号--$aliexpress_user-----订单--$orderId--------评价成功!\n";
            } else {
                $log = "Date: ".date("Y-m-d H:i:s")." 订单".$orderId."评价失败，错误信息:".$result['errorMessage']."\n";
                //@file_put_contents($logfile, $log, FILE_APPEND);
                echo "---账号--$aliexpress_user-----订单--$orderId--------评价失败!--{$result['errorMessage']}\n";          
            }
        }         
        return true;
    }  
   
?>