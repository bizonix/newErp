<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30); //session有效时间为30分钟

$system_name = "产品中心";
$system_url = "http://pc.valsun.cn/";
$css_height = "line-heigh:180%";
$title = "【问题料号简报】" . ' ' . date('Y-m-d', time()) . ' ' . $system_name;
$table = '<p style="' . $css_height . '"><b>大家好:</b><br/>以下为按照添加时间排序的<b>部分问题料号简表</b>，<span style="color:red">请相关人员对问题料号进行信息完善</span></p>';

$startTime = strtotime(date('Y-m-d', (time() - 86400 * $days)) . " 00:00:01");
$endTime = strtotime(date('Y-m-d', time()) . " 23:59:59");
$type = "email"; //消息发送类型
$from = "朱清庭"; //发送人
//$from	= "温小彬"; //发送人
$to = "陈文平,王绪成,陈前,王晓华,廖海英,杨飞,曾帅,陈赟士,王奇,陈燕云,陈小霞,覃云云,郑凤娇,李美琴,潘旭东,罗莹,陈波,林正祥,席慧超,肖金华,钟衍台,周聪,韩庆新,仝召燕,陈智兴,曹莉,孙学轩,朱清庭"; //接收者
//$to	= "朱清庭"; //接收者
$cc = "";
//$cc = "陈文平";//抄送
echo "开始时间" . date('Y-m-d H:i:s', time()) . "\n";
$table .= '<table border="1" cellpadding="0" cellspacing="0" width="791">';
$table .= "<tr><th>序号</th><th>SKU</th><th>重量</th><th>包材</th><th>采购</th><th>类别</th></tr>";

$limitCount = 50;
$tName = 'pc_goods';
$select = '*';
$where = "WHERE is_delete=0 and left(sku,2)<>'MT' and isNew=0 and goodsStatus=1 and (goodsWeight=0 OR pmId=0) order by goodsCreatedTime limit $limitCount";
$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
$count = count($skuList);
if($count < $limitCount){
    $where = "WHERE is_delete=0 and left(sku,2)<>'MT' and isNew=0 and goodsStatus=1 and (goodsWeight=0 OR pmId=0 OR purchaseId=0 OR goodsCategory='') order by goodsCreatedTime limit $limitCount";
    $skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
}
//print_r($skuList);
//exit;
foreach ($skuList as $index => $value) {
	$sku = $value['sku'];
	$goodsWeight = $value['goodsWeight'];
	$pmId = $value['pmId'];
	$purchaseId = $value['purchaseId'];
	$goodsCategory = $value['goodsCategory'];
	if (!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/", $sku)) {
		$sku = "<span style='color:red'>$sku</span>";
	}

	if ($goodsWeight == 0) {
		$goodsWeight = "<span style='color:red'>无</span>";
	}

	if (empty ($pmId)) {
		$pmId = "<span style='color:red'>无</span>";
	} else {
		$tName = 'pc_packing_material';
		$select = 'pmName';
		$where = "WHERE id='$pmId'";
		$pmList = OmAvailableModel :: getTNameList($tName, $select, $where);
		$pmId = $pmList[0]['pmName'];
	}

	if (empty ($purchaseId)) {
		$purchaseId = "<span style='color:red'>无</span>";
	} else {
		$purchaseId = getPersonNameById($purchaseId);
	}

	if (empty ($goodsCategory)) {
		$goodsCategory = "<span style='color:red'>无</span>";
	} else {
		$goodsCategory = getAllCateNameByPath($goodsCategory);
	}
    $tdIndex = $index + 1;
	$table .= "<tr><td>$tdIndex</td><td>$sku</td><td>$goodsWeight</td><td>$pmId</td><td>$purchaseId</td><td>$goodsCategory</td></tr>";
}
$table .= "</table>";
$table .= '<p style="' . $css_height . '">欲知所有问题料号，请联系产品中心负责人。详情请登录：<a href="' . $system_url . '" target="_blank">' . $system_name . '</a><br/></br>' . date('Y-m-d', $endTime) . '<br/>' . $system_name . '</p>';
//$table = '1111111';
echo $table."\n";
echo 'table length = '.strlen($table)."\n";
echo 'table urlencode length = '.strlen(urlencode($table))."\n";
$paramArr = array (
    "sysName" => 'ProductCenter',
	"from" => $from,
	"to" => $to,
    "cc"=> $cc,
	"type" => $type,
	"title" => $title,
    "content" => $table
);
$message = UserCacheModel :: getOpenSysApiPost('notice.send.message', $paramArr, '');//get
//$message = UserCacheModel :: getOpenSysApiPost('notice.send.message', $paramArr,'gw88');//post
echo 'message = ';
var_dump($message);
echo "\n";
//echo $message, "\n";
echo "完成时间" . date('Y-m-d H:i:s', time()) . "\n";
exit;
?>
