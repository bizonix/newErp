<?php
date_default_timezone_set('Asia/Shanghai');

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

/*
 * 链接数据库
 */
$db_conn = mysql_connect('192.168.200.222', 'cerp', '123456');
mysql_select_db('valsun_warehouse', $db_conn);
mysql_query('set names utf8');


$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua','mq_vhost1');
$channel = $connection->channel();

/* 接收队列 */
$channel->exchange_declare('order', 'topic', false, false, false);

list($queue_name, ,) = $channel->queue_declare("whgetorder", false, true, false,  false);

$channel->queue_bind($queue_name, 'order');
/* --------- 接收队列  -------------*/

/* 发送队列  */
$queue = 'whack';
$exchange = 'whexchange';
$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_declare($queue, false, true, false, false);
$channel->queue_bind($queue, $exchange, 'orderack');

/* --------- 发送队列 -------------*/

// echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
  global $channel, $exchange;
  echo ' [x] ', $msg->body, "\n";
//   return ;
  $result = json_decode($msg->body, TRUE);
  if ($result == FALSE) {
  	echo '解析数据失败';
  	return FALSE;
  }
  
  
  
  //print_r($result);
  $info = $result[0];
  $isexist = checkExist($info['id']);       //验证数据是否已经提交过
  if (TRUE == $isexist) {
    echo "duplicate massage!";    
  	return FALSE;
  }
  
  $username = mysql_real_escape_string($info['username']);
  $platformUsername = mysql_real_escape_string($info['platformUsername']);
  $email = mysql_real_escape_string($info['email']);
  $countryName = mysql_real_escape_string($info['countryName']);
  $countrySn = mysql_real_escape_string($info['countrySn']);
  $state = mysql_real_escape_string($info['state']);
  $city = mysql_real_escape_string($info['city']);
  $street = mysql_real_escape_string($info['street']);
  $address2 = mysql_real_escape_string($info['address2']);
  $address3 = mysql_real_escape_string($info['address3']);
  $currency = mysql_real_escape_string($info['currency']);
  $landline = mysql_real_escape_string($info['landline']);
  $phone = mysql_real_escape_string($info['phone']);
  $landline = mysql_real_escape_string($info['landline']);
  $zipCode = mysql_real_escape_string($info['zipCode']);
  $transportId = mysql_real_escape_string($info['transportId']);
  $account = mysql_real_escape_string($info['account']);
  $transportId = mysql_real_escape_string($info['transportId']);
  $transportId = mysql_real_escape_string($info['transportId']);
  $onesku = count($info['data']) > 1 ? 2 : 1;
  $pmid = $info['pmId'];
  $isFixed = $info['isFixed'];
  $total = $info['actualTotal'];
  $channelId = $info['channelId'];
  $calcWeight = $info['calcWeight'];
  $calcShipping = $info['calcShipping'];
  $createtime = time();
  $orderTypeId = $info['flag'] ? 1 : 2;
  $recordNumber = mysql_real_escape_string($info['platformUsername']);
  
  /* 事务处理 */
  mysql_query("SET AUTOCOMMIT=0");
  mysql_query("BEGIN");
  
  $order_sql = "
      insert into wh_shipping_order values (null, '$username', '$platformUsername', '$email',
        '$countryName', '$countrySn', '$state', '$city', '$street', '$address2', '$$address3',
        '$currency', '$landline', '$phone', '$zipCode', '$transportId', '$account', 400, $onesku,
        $pmid, $isFixed, $total, $channelId, $calcWeight, $calcShipping, $createtime, $orderTypeId,
        1, 1, 0)
      ";
  //echo $order_sql;exit;
  $result = mysql_query($order_sql);
  if ($result == FALSE) {   //插入数据失败
    mysql_query("ROLLBACK");
    mysql_query('SET AUTOCOMMIT=1');
  	return FALSE;
  }
  $newid = mysql_insert_id();   //获取新生成的id
  
  $sqlar = array();
  foreach ($info['id'] as $id){
      $sqlar[] = "(null, $id, $newid, '$recordNumber', 1)";
  }
  $sqlstr = implode(',', $sqlar);
  $relation_sql = "
      insert into wh_shipping_order_relation values $sqlstr
      ";
//   echo $relation_sql;exit;
  $relation = mysql_query($relation_sql);
  if ($relation == FALSE) { //插入收失败
  	mysql_query("ROLLBACK");
  	mysql_query('SET AUTOCOMMIT=1');
  	return  FALSE;
  }
  
  $skuar = array();
  foreach ($info['data'] as $skuv){
     $skulist = getRealSkuAndNums($skuv['sku']);
//      var_dump($skulist);exit;
     $amount = intval($skuv['amount']);
     if($skulist == FALSE && ($skulist['isCombine'] == 0)) {
     	$skuar[] = "(null, $newid, '', '', '$skuv[sku]', $amount, 1)";
     } else {
         foreach ($skulist['sku'] as $k=>$v){
             $num = $v * $amount;
             $skuar[] = "(null, $newid, '$skuv[sku]', '$amount', '$k', $v, 1)";
         }
     }
  }
  $detailsql = implode(',', $skuar);
  $detailinsert = "insert into wh_shipping_orderdetail values $detailsql";
//   echo $detailinsert;exit;
  $dtailresult = mysql_query($detailinsert);
  if (FALSE == $dtailresult) {  //插入失败
  	mysql_query("ROLLBACK");
  	mysql_query('SET AUTOCOMMIT=1');
  	return  FALSE;
  }
  
  mysql_query("COMMIT");
  mysql_query('SET AUTOCOMMIT=1');
  $ackmsg = array('id'=>$info['id'], 'result'=>1);
  $ackmsg = json_encode($ackmsg);
  $ackmsg = new AMQPMessage($ackmsg, array('content_type' => 'text/plain', 'delivery_mode' => 2));
  $channel->basic_publish($ackmsg,  $exchange,'orderack');
  echo 'success: '.implode(',', $info['id']), "\n";
};


$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

/*
 * 组合料号转换关系
 */
function getRealSkuAndNums($sku){
    $mem = new Memcache();
    $mem->addserver('192.168.200.222');
//     var_dump($mem);
    $skuArr = array('sku'=>array($sku=>1),'isCombine'=>0);//默认为单料号
   // echo "sku_info_" . $sku;exit;
    $skuInfo = $mem->get("sku_info_" . $sku);
//     print_r($skuInfo);
    if(empty($skuInfo)){
        return false;
    }
    if(isset($skuInfo['sku']) && is_array($skuInfo['sku'])){  //如果为组合料号时
    	$tmpArr = array();
		foreach ($skuInfo['sku'] as $key => $value) { //循环$skuInfo下的sku的键，找出所有真实料号及对应数量,$key为组合料号下对应的真实单料号，value为对应数量
			$tmpArr[$key] = $value;
		}
		$skuArr['sku'] = $tmpArr;
		$skuArr['isCombine'] = 1;
	}
    return $skuArr;
}

/*
 *验证订单是否已经推送过 
 *存在返回true 不存在返回false
 **/
function checkExist($idar){
    $ids = implode(',', $idar);
    $sql = "
        select so.id from wh_shipping_order as so join wh_shipping_order_relation as sor on so.id=sor.shipOrderId
        where sor.originOrderId in ($ids) and sor.storeId =1 and so.is_delete = 0
        ";
    $qre = mysql_query($sql);
    if (FALSE == $qre) {
    	return FALSE;
    }
    $rownum = mysql_num_rows($qre);
    if ($rownum < 1) {
    	return FALSE;
    } else {
        return TRUE;
    }
}
