<?php
/**
* @name order_constrast_intercept.php
* @author Herman.Xi (席慧超)
* @version 1.0
* @modify 2012-09-18 09:44:00
* @last modified Herman.Xi
* @last modified date 2012-12-15
* 定时同步发货单打印信息到仓库系统中
**/
//orderStatus = ".C("STATESHIPPED").", orderType = ".C("STATESHIPPED_PRINTPEND")
set_time_limit(0);
//脚本参数检验
#########全局变量设置########
//date_default_timezone_set('Asia/Chongqing');
$detailLevel = 0;
$storeId	= "1";
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
require_once WEB_PATH_CONF_SCRIPTS."script.ebay.config.php";

$tableName = "om_unshipped_order";
$ordersql 	 =  'SELECT         COUNT(*) AS total_num
				FROM 			'.$tableName.' AS a 
				LEFT JOIN       '.$tableName.'_detail AS b 
				ON 			    b.omOrderId = a.id
				WHERE			a.orderStatus = '.C('STATESHIPPED').'
				AND				a.orderType = '.C('STATESHIPPED_APPLYPRINT').' 
				AND 			a.is_delete = 0
				AND 			a.storeId= '.$storeId;

$order_sql	= $dbConn->query($ordersql);
$orders_count = $dbConn->fetch_array($order_sql);
//var_dump($orders_count); echo "\n";
$page = 1;
$perpage = 2000;
$totalpage = ceil($orders_count['total_num']/$perpage);
$time_start=time();
echo "\n=====[".date('Y-m-d H:i:s',$time_start)."]系统【推送打印数据给仓库系统】共有（".$orders_count['total_num']."）个订单需要处理\n";
$CommonAct = new CommonAct();
while ($page<=$totalpage){
	echo "总共{$totalpage}页---现在是第{$page}页\n";
	//$start_num  = ($page-1)*$perpage;
	$start_num  = 0;
	$limit = " GROUP BY a.id ORDER BY a.id LIMIT {$start_num}, {$perpage}";
	$ordersql 	 =  'SELECT         a.id
					FROM 			'.$tableName.' AS a 
					LEFT JOIN       '.$tableName.'_detail AS b 
					ON 			    b.omOrderId = a.id
					WHERE			a.orderStatus = '.C('STATESHIPPED').'
					AND				a.orderType = '.C('STATESHIPPED_APPLYPRINT').' 
					AND 			a.is_delete = 0
					AND 			a.storeId= '.$storeId.$limit;

	//echo $ordersql; echo "<br>";
	//exit;
	$query	     		=	$dbConn->query($ordersql);
	$orders     		=	$dbConn->fetch_array_all($query);
	
	if(!empty($orders)){
		foreach($orders as $value){
			$omOrderId = $value['id'];
			if(OrderPushModel::listPushMessage($omOrderId)){
				echo "=====[".date('Y-m-d H:i:s',$time_start)."]订单{$omOrderId}同步成功======\n";
			}else{
				echo "=====[".date('Y-m-d H:i:s',$time_start)."]订单{$omOrderId}同步失败,原因：".OrderPushModel::$errMsg."======\n";
				$CommonAct->act_ApplicationException($omOrderId,OrderPushModel::$errMsg);
			}
		}
	}else{
		echo "=====没有同步的订单======\n";
	}
	$page++;
}
$time_end=time();
echo "\n=====[耗时:".ceil(($time_end-$time_start)/60)."分钟]====\n";
echo "\n=====[".date('Y-m-d H:i:s',$time_end)."]系统【推送打印数据给仓库系统】订单结束\n";
exit;
//sleep(10);//执行完操作之后开始休眠10秒钟,同步数据。。。。。