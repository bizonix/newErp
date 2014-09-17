<?php
error_reporting(E_ALL);
ini_set('max_execution_time', 1800);
if(!defined('WEB_PATH')){
	define("WEB_PATH","/data/web/order.valsun.cn/");
	//define("WEB_PATH","E:/www/zhang/code/order.valsun.cn/");
}
require_once WEB_PATH."crontab/scripts.comm.php";
require_once WEB_PATH_CONF_SCRIPTS."script.ebay.config.php";
/*include_once "/data/scripts/ebay_order_cron_job/ebay_order_cron_config.php";
include_once "/data/scripts/ebay_order_cron_job/function_purchase.php";*/

$tNameUnShipped = 'om_shipped_order';


/*$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end,' 1=1',900);

$orderIdArr = array ();
foreach ($tNameOrderIdList as $value) {
	$orderIdArr[] = $value['id'];
}
$orderIdStr = implode(',', $orderIdArr);
if (empty ($orderIdStr)) {
	$orderIdStr = 0;
}*/

//$where = "WHERE id in($orderIdStr) AND channelId = '0' ";
$where = "WHERE channelId=0 ";
$count = OmAvailableModel::getTNameCount($tNameUnShipped, $where);
$start = 0;
$per = 5000;
$page = ceil($count/$per);
echo "totalcount = $count";
echo "\n";
echo "totalpage = $page";
echo "\n";

for($i=0;$i<$page;$i++){
    echo "currentPage = $i";
    echo "\n";
    $start = $per*$i;
    $where = "WHERE channelId=0 ORDER BY id desc ";
    $where .= " limit $start,$per";
    $shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);
    foreach($shipOrderList as $key => $value){
    	$id			=	$value['orderData']['id'];
    	$weight		=	$value['orderWhInfoData']['actualWeight'];
    	$country	=	$value['orderUserInfoData']['countryName'];
    	$transportId =  $value['orderData']['transportId'];
    	$shaddr		=	1;
    	echo "id = $id";
    	echo "\n";
    	if(empty($weight) || empty($country) || empty($transportId)){
    		echo 'weight OR countryName or transportId is Empty!';
    		echo "\n";
    		continue;
    	}
    	$ifee		=	calcshippingfee($shaddr,$weight,$country,$transportId);
    	$channelId	=	'';
    	if(isset($ifee['channelId'])){
    		$channelId	=	$ifee['channelId'];
    	}
    	if(!empty($channelId) && !empty($id)){
    		error_log(date()."id: ".$id." channelId: ".$channelId." \r\n",3,'/data/web/order.valsun.cn/log/update_channelId.log');
    		updateChannelId($id,$channelId,$tNameUnShipped);
    	}else{
    		echo 'channelId OR id is Empty!';
    		echo "\n";
    		echo "channelId=$channelId,weight=$weight,country=$country,transportId=$transportId \n";
    	}
    }
}


function calcshippingfee($shaddr, $weight, $country, $transportId){
	require_once WEB_PATH."api/include/functions.php";

	$method = 'trans.carrier.fix.get';
	$paramArr= array(
		/* API系统级输入参数 Start */
		'method'	=> $method,  //API名称
		'format'	=> 'json',  //返回格式
		'v'			=> '1.0',   //API版本号
		'username'	=> C('OPEN_SYS_USER'),
		/* API系统级参数 End */
	);

	$paramArr['country'] = $country;
	$paramArr['weight'] = $weight;
	$paramArr['shaddr'] = $shaddr;
	$paramArr['carrier'] = $transportId;
	$result = callOpenSystem($paramArr);

	$data = json_decode($result,true);
	if(empty($data['data'])){
		return false;
	}
	return $data['data']['fee'];
}

function updateChannelId($id,$channelId,$tNameUnShipped) {
	global $dbConn;
	$sql	=	'UPDATE `'.$tNameUnShipped.'` SET channelId = "'.$channelId.'" WHERE id = "'.$id.'"';
	echo $sql; echo "\n";
	$sql	=	$dbConn->query($sql);
}


?>