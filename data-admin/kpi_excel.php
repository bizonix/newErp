<?php
/**
 * ebay 毛利
 * xiaojinhua
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/excel/Classes/PHPExcel.php';
include "dbconnect.php";
include "function_purchase.php";
$m = new MongoClient('mongodb://localhost:20000/');


date_default_timezone_set ("Asia/Chongqing");
ini_set('memory_limit', '-1');
$dbcon	= new DBClass();




// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Add some data, we will use printing features
echo date('H:i:s') , " Add some data" , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('A1', "日期");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "ebay账号");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "客户Id");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "订单编号");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "Record Number");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "分sku");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "主sku");
$objPHPExcel->getActiveSheet()->setCellValue('H1', "组合料号");
$objPHPExcel->getActiveSheet()->setCellValue('I1', "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue('J1', "币种");
$objPHPExcel->getActiveSheet()->setCellValue('K1', "产品售价");
$objPHPExcel->getActiveSheet()->setCellValue('L1', "Shipping Fee");
$objPHPExcel->getActiveSheet()->setCellValue('M1', "国家");
$objPHPExcel->getActiveSheet()->setCellValue('N1', "订单数量");
$objPHPExcel->getActiveSheet()->setCellValue('O1', "总收入(USD)");
$objPHPExcel->getActiveSheet()->setCellValue('P1', "真实料号总收入");
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "组合料号总收入");
$objPHPExcel->getActiveSheet()->setCellValue('R1', "PP Fee(USD)");
$objPHPExcel->getActiveSheet()->setCellValue('S1', "折算RMB总收入(扣除pp)");
$objPHPExcel->getActiveSheet()->setCellValue('T1', "是否挂号");
$objPHPExcel->getActiveSheet()->setCellValue('U1', "实际过秤重量(kg)");
$objPHPExcel->getActiveSheet()->setCellValue('V1', "重量表重量(kg)");
$objPHPExcel->getActiveSheet()->setCellValue('W1', "100%运费(RMB)");
$objPHPExcel->getActiveSheet()->setCellValue('X1', "运输方式");
$objPHPExcel->getActiveSheet()->setCellValue('Y1', "发货分区");
$objPHPExcel->getActiveSheet()->setCellValue('Z1', "折扣后运费(RMB)");
$objPHPExcel->getActiveSheet()->setCellValue('AA1', "包材成本(RMB)");
$objPHPExcel->getActiveSheet()->setCellValue('AB1', "产品成本(RMB)");
$objPHPExcel->getActiveSheet()->setCellValue('AC1', "订单处理成本");
$objPHPExcel->getActiveSheet()->setCellValue('AD1', "毛利");
$objPHPExcel->getActiveSheet()->setCellValue('AE1', "真实料号毛利");
$objPHPExcel->getActiveSheet()->setCellValue('AF1', "组合料号毛利");
$objPHPExcel->getActiveSheet()->setCellValue('AG1', "毛利率");
$objPHPExcel->getActiveSheet()->setCellValue('AH1', "采购负责人");
$objPHPExcel->getActiveSheet()->setCellValue('AI1', "真实料号销售负责人");
$objPHPExcel->getActiveSheet()->setCellValue('AJ1', "虚拟料号销售负责人");
$objPHPExcel->getActiveSheet()->setCellValue('AK1', "采购团队");
$objPHPExcel->getActiveSheet()->setCellValue('AL1', "销售团队-1");
$objPHPExcel->getActiveSheet()->setCellValue('AM1', "销售团队-2");
$objPHPExcel->getActiveSheet()->setCellValue('AN1', "是否复制订单");
$objPHPExcel->getActiveSheet()->setCellValue('AO1', "是否拆分订单");
$objPHPExcel->getActiveSheet()->setCellValue('AP1', "是否异常订单");
$objPHPExcel->getActiveSheet()->setCellValue('AQ1', "是否补寄");
$objPHPExcel->getActiveSheet()->setCellValue('AR1', "是否异常数据");



$datetime = time() -24*60*60;
$dateformat = date("Y-m-d",$datetime);
echo $dateformat;
$where = array(
	"order_platform"=>"ebay",
	"order_scantime" => array('$gte'=>"$datetime")
	//"order_scantime" => array('$gte'=>$datetime)
	//'order_scantime' => '1406000259'
);

$cursor = $m->bigdata->ebay->find($where);
$k = 2;
foreach ($cursor as $item) {
    // do something to each document
	print_r($item);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$k,date("Y-m-d",$item['order_scantime']) );
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$k,$item['send_account'] );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$k,$item['sale_userid']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$k,$item['order_id']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$k,$item['recordnumber']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$k,$item['sku']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$k,$item['spu']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$k,$item['csku']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$k,$item['sell_count']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$k,$item['order_currency']);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$k,$item['order_total']);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$k,$item['order_shipfee']);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$k,$item['order_countryname']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$k,$item['order_number']);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$k,$item['order_usdtotal']);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$k,$item['sell_skuprice']);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$k,$item['sell_cskuprice']);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$k,$item['order_ppfee']);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$k,$item['order_cnytotal']);
	if($item['is_register'] == 1){
		$register = "是";
	}else{
		$register = "否";
	}
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$k,$register);
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$k,$item['order_weight']);
	$objPHPExcel->getActiveSheet()->setCellValue('V'.$k,$item['sku_weight']);
	$objPHPExcel->getActiveSheet()->setCellValue('W'.$k,$item['send_allshipfee']);
	$objPHPExcel->getActiveSheet()->setCellValue('X'.$k,$item['send_carrier']);
	$objPHPExcel->getActiveSheet()->setCellValue('Y'.$k,$item['order_sendZone'] );
	$objPHPExcel->getActiveSheet()->setCellValue('Z'.$k,$item['send_rebateshipfee'] );
	$objPHPExcel->getActiveSheet()->setCellValue('AA'.$k,$item['sku_packingcost'] );
	$objPHPExcel->getActiveSheet()->setCellValue('AB'.$k, $item['sku_cost']);
	$objPHPExcel->getActiveSheet()->setCellValue('AC'.$k, $item['sku_processingcost']);
	$objPHPExcel->getActiveSheet()->setCellValue('AD'.$k, $item['order_grossrate']);
	$objPHPExcel->getActiveSheet()->setCellValue('AE'.$k, $item['order_skugrossrate']);
	$objPHPExcel->getActiveSheet()->setCellValue('AF'.$k, $item['order_cskugrossrate']);
	$objPHPExcel->getActiveSheet()->setCellValue('AG'.$k, $item['order_grossmarginrate']);
	$objPHPExcel->getActiveSheet()->setCellValue('AH'.$k, $item['sku_purchase']);
	$objPHPExcel->getActiveSheet()->setCellValue('AI'.$k, $item['salemember']);
	$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$k, $item['csalemember']);
	$objPHPExcel->getActiveSheet()->setCellValue('AK'.$k, $item['caigou_team']);
	$objPHPExcel->getActiveSheet()->setCellValue('AL'.$k, $item['sale_team']);
	$objPHPExcel->getActiveSheet()->setCellValue('AM'.$k, $item['csale_team']);

	if($item['is_copyorder'] == 1 ){
		$is_copyorder = "是";
	}else{
		$is_copyorder = "否";
	}

	if($item['is_splitorder'] == 1 ){
		$is_splitorder = "是";
	}else{
		$is_splitorder = "否";
	}

	if($item['is_suppleorder'] == 1 ){
		$is_suppleorder = "是";
	}else{
		$is_suppleorder = "否";
	}
	if($item['is_effective'] == 1 ){
		$is_effective = "是";
	}else{
		$is_effective = "否";
	}

	if($item['is_effectiveorder'] == 1 ){
		$is_effectiveorder = "是";
	}else{
		$is_effectiveorder = "否";
	}

	$objPHPExcel->getActiveSheet()->setCellValue('AN'.$k, $is_copyorder);
	$objPHPExcel->getActiveSheet()->setCellValue('AO'.$k, $is_splitorder);
	$objPHPExcel->getActiveSheet()->setCellValue('AP'.$k, $is_effectiveorder);
	$objPHPExcel->getActiveSheet()->setCellValue('AQ'.$k, $is_suppleorder);
	$objPHPExcel->getActiveSheet()->setCellValue('AR'.$k, $is_effective);
	$k++;                                         
}                                                 


            
// Set page orientation and size
echo date('H:i:s') , " Set page orientation and size" , EOL;
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('ebay kpi 报表');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
$filepath = "/data/web/data/download/ebay_excel_{$dateformat}.xlsx";
echo $filepath."\n";
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($filepath);

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
?>
