<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/PHPExcel.php";

error_reporting(-1);
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','订货日期');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','订单号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','供应商');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','产品描述');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','订货数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','单价');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','金额');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','采购员');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','订单备注');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','到货日期');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','到货数量');
//$dataArr = $PO->actExportOrder($data);
$now = time();
$lastDay = $now - 24*60*60;
//$lastDay = $now - 3*24*60*60;//测试
//$lastDay = $now ;//测试
$dateFarmat = date("Y-m-d",$lastDay);
$lastStart = strtotime($dateFarmat."00:00:00");
$lastEnd = strtotime($dateFarmat."23:59:59");
var_dump($lastStart,$lastEnd);
$sql = "select distinct a.id, a.*,b.*,c.price,c.count from ph_order_arrive_log as a left join ph_order as b 
		on a.ordersn=b.recordnumber left join ph_order_detail as c
		on b.id=c.po_id 
		where a.sku=c.sku
		and a.arrive_time>{$lastStart} and a.arrive_time<{$lastEnd}
  	";
echo $sql;
$sql = $dbConn->execute($sql);
$skuReachInfo = $dbConn->getResultArray($sql);
print_r($skuReachInfo);
$row = 2;
foreach($skuReachInfo as $itemSku){
	$addtime 		= $itemSku['addtime'];
	$recordnumber 	= $itemSku['recordnumber'];
	$partnerName	= getParterNameById($itemSku['partner_id']);
	$purchaseName 	= getNameById($itemSku['purchaseuser_id']);
	$sku 			= $itemSku['sku'];
	$goodsName 		= getSkuById($sku);
	$price 			= $itemSku['price'];
	$count 			= $itemSku['count'];
	$totalmoney 	= $price*$count;
	$arrive_time = $itemSku['arrive_time'];
	$amount = $itemSku['amount']; //到货数量
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, date("Y/m/d", $addtime));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $recordnumber);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $partnerName);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $goodsName); //产品名称
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $count); //
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $price);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $totalmoney);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $purchaseName);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $purchaseName);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, date("Y/m/d",$arrive_time));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$row, $amount);
	$row++;
}

$objPHPExcel->getActiveSheet(0)->getStyle('A1:N'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(80);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(25);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(25);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(25);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
$path = "/data/web/purchase.valsun.cn/html/download/";
$title		= "receipt_info_".date('Y-m-d');
$titlename	= $path.$title.".xls";

$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');
$objWriter->save($titlename);


function getSkuById($sku){
	global $dbConn;
	$sql = "SELECT spu, sku, goodsName FROM pc_goods where sku= '{$sku}' AND is_delete = 0";
	$query = $dbConn->query($sql);
	$rtn_data = $dbConn->fetch_one($query);
	return $rtn_data['goodsName'];
}

function getParterNameById($id){
	global $dbConn;
	$sql 	= "SELECT company_name FROM ph_partner WHERE id = '{$id}' AND is_delete = 0";
	$query	= $dbConn->query($sql);
	$data = $dbConn->fetch_one($query);
	return $data['company_name'];
}

function getNameById($id){
	global $dbConn;
	$sql 	= "SELECT global_user_name FROM power_global_user WHERE global_user_id = '{$id}'";
	$query	= $dbConn->query($sql);
	if ($query) {
		$data = $dbConn->fetch_array_all($query);
		if(!empty($data)){
			return $data[0]['global_user_name'];
		}else{
			return false;
		}
	}else{
		return false;
	}
}

?>
