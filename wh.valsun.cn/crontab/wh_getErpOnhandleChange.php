<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(-1);
date_default_timezone_set('Asia/Shanghai');

if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace(array("\\", 'crontab'), array('/', ''), __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}
//var_dump($web_path);exit;
var_dump(class_exists('Core'));exit;
//require "../framework.php";
Core::getInstance();

//require_once __DIR__ . '/data/web/wh.valsun.cn/lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('112.124.41.121', 5672, 'purchase', 'purchase123','valsun_purchase');
$exchange_name = 'wh_status_exchange';
//$exchange_name  =   'warehouse-out';

$queue_name = 'getErpOnhandle_change';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

//echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    $log_file   =   'wh_getErpOnhandleChange/'.date('Y-m-d-H').'.txt';
    $date       =   date('Y-m-d H:i:s');
    //$msg        =   '{"sku":"10878","amount":1,"reason":"\u9500\u552e\u8ba2\u5355\u51fa\u5e93","ioTypeId":"\u9500\u552e\u8ba2\u5355","ioType":1,"userId":"vipchen","ordersn":"2014-04-16-101430994"}';
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
	$mctime = time();
	//$msg_array = json_decode($msg->body,true);
    $msg_array  =   json_decode($msg->body, true);
	//echo ' [x] ', $msg->body, "\n";
    
	//echo "\n\n";
    write_log($log_file, json_encode($msg_array)."\r\n");

	if(!empty($msg_array)){
	    $sku        =   $msg_array['sku'];
        $ioTypeId   =   WarehouseManagementModel::whIoTypeModelList(" where typeName='{$msg_array['ioTypeId']}'");
	    $msg_array['ioTypeId']   =   empty($ioTypeId) ? 0 : $ioTypeId[0]['id'];        //获取出入库类型id
        
        $skuinfo        =   whShelfModel::selectSku("where sku='$sku'");
        if(empty($skuinfo)){
            $errCode = 409;
			$errMsg = "没有该料号";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s\r\n", $sku, $date, $errMsg, json_encode($skuinfo));
            write_log($log_file, $log_info);
			OmAvailableModel :: rollback();
			continue;
        }
        $nums           =   ($msg_array['ioType'] == 1 ? '-' : '').$msg_array['amount'];  //1出库 2入库
        //print_r($nums);exit;
		
		$positioninfo  = whShelfModel::selectRelation("where pId={$skuinfo['id']}");  //获取料号仓位关系
        //print_r($positioninfo);exit;

        OmAvailableModel::begin();
        
        /**** 更新仓位库存 ****/
		$update_position = whShelfModel::updateProductPositionRelation($nums, "where pId={$skuinfo['id']} and positionId != 8290 and is_delete = 0");
		if(!$update_position){
			$errCode = 410;
			$errMsg = "更新仓位库存失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, $errMsg,
                                        $update_position, $nums);
            write_log($log_file, $log_info);
			OmAvailableModel :: rollback();
			continue;
		}
        write_log($log_file, date('Y-m-d H:i:s').'更新仓位库存成功！'."{$sku}\r\n");
        
        /**** 更新总库存 *****/
		$where = "where sku='{$sku}'";
		$info  = whShelfModel::updateStoreNum($nums,$where);
		if(!$info){
			$errCode = 412;
			$errMsg = "更新总库存失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
                                        $info, $nums, $where);
            write_log($log_file, $log_info);               
			OmAvailableModel :: rollback();
			continue;
			
		}
        write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
        
        $user_id                    =   $msg_array['userId'];
        /**** 插入出入库记录 *****/
        $msg_array['positionId']    =   $positioninfo[0]['positionId'];
        $userId                     =   preg_match("/^\d+$/", $user_id) ? $user_id : getUserIdByName($msg_array['userId']);
        $msg_array['userId']        =   $userId ? $userId : 0;
        $msg_array['purchaseId']    =   $skuinfo['purchaseId'];
        //print_r($msg_array);exit;
		$record = CommonModel::addIoRecores($msg_array);     //出库记录
		if(!$record){
			$errCode = 413;
			$errMsg = "插入出入库记录失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, $errMsg,
                                            $record, json_encode($paraArr));
            write_log($log_file, $log_info);
			OmAvailableModel :: rollback();
			continue;
		}
        write_log($log_file, date('Y-m-d H:i:s').'插入入库记录成功！'."{$sku}\r\n");
        OmAvailableModel::commit();
	}
    
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>