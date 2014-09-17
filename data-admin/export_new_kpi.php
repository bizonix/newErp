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

$user	= 'vipchen';
$departments = array(1=>'采购部',2=>'芬哲',3=>'哲果');
$fielnames = array(1=>'sw',2=>'fz',3=>'zg');
require_once '/data/scripts/ebay_order_cron_job/script_root_path.php';

require_once SCRIPT_ROOT.'function_purchase.php';
require_once SCRIPT_ROOT."script_root_path.php";
require_once SCRIPT_ROOT."ebay_order_cron_config.php";

date_default_timezone_set ("Asia/Chongqing");

$dbcon	= new DBClass();


$sql = "SELECT ebay_account,ebay_platform FROM ebay_account WHERE ebay_platform!='' ORDER BY ebay_platform ASC";
$sql  = $dbcon->execute($sql);
$eaccounts = $dbcon->getResultArray($sql);
$accounts = array();
foreach ($eaccounts AS $eaccount){
	$accounts[$eaccount['ebay_platform']][] = $eaccount['ebay_account'];
}


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

$objPHPExcel->getActiveSheet()->setCellValue('A1', "主料号");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "料号");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "产品描述");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "仓位号");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "入库记录");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "库存天数");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "总库存");
$objPHPExcel->getActiveSheet()->setCellValue('H1', "A仓实际库存");
$objPHPExcel->getActiveSheet()->setCellValue('I1', "B仓实际库存");
$objPHPExcel->getActiveSheet()->setCellValue('J1', "缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue('K1', "待发货");
$objPHPExcel->getActiveSheet()->setCellValue('L1', "已拦截");
$objPHPExcel->getActiveSheet()->setCellValue('M1', "待审核");
$objPHPExcel->getActiveSheet()->setCellValue('N1', "单价");
$objPHPExcel->getActiveSheet()->setCellValue('O1', "成本核算价");
$objPHPExcel->getActiveSheet()->setCellValue('P1', "存货金额");
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "采购负责人");
$objPHPExcel->getActiveSheet()->setCellValue('R1', "助理");
$objPHPExcel->getActiveSheet()->setCellValue('S1', "月度发货金额");
$objPHPExcel->getActiveSheet()->setCellValue('T1', "本月发货");
$objPHPExcel->getActiveSheet()->setCellValue('U1', "每日均量");
$objPHPExcel->getActiveSheet()->setCellValue('V1', "自动拦截");
$objPHPExcel->getActiveSheet()->setCellValue('W1', "虚拟库存");
$objPHPExcel->getActiveSheet()->setCellValue('X1', "商品分类");
$objPHPExcel->getActiveSheet()->setCellValue('Y1', "商品状态");
$objPHPExcel->getActiveSheet()->setCellValue('Z1', "商品状态变更日期");

$titleArr = array(
	"A",
	"B",
	"C",
	"D",
	"E",
	"F",
	"G",
	"H",
	"I",
	"J",
	"K",
	"L",
	"M",
	"N",
	"O",
	"P",
	"Q",
	"R",
	"S",
	"T",
	"U",
	"V",
	"W",
	"X",
	"Y",
	"Z"
);          

//$accounts_value = array_values($accounts);
$accounts_value = array_keys($accounts);
foreach ($accounts_value AS $key=>$account){
	//echo $titleArr[$key].$account,EOL;
	$objPHPExcel->getActiveSheet()->setCellValue("A{$titleArr[$key]}1", "{$account}");
}

$accounts_length = count($accounts);

$objPHPExcel->getActiveSheet()->setCellValue("A{$titleArr[$accounts_length+1]}1", "海外仓每月发货");
$objPHPExcel->getActiveSheet()->setCellValue("A{$titleArr[$accounts_length+2]}1", "海外仓库存");
$objPHPExcel->getActiveSheet()->setCellValue("AQ", "aliexpress缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AR", "cndirect缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AS", "DHgate缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AT", "dresslink缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AU", "ebay平台缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AV", "Newegg缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AW", "亚马逊缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AX", "出口通缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AY", "国内销售部缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("AZ", "天猫哲果缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("BA", "天猫芬哲缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("BB", "海外仓缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("BC", "海外销售平台缺货库存");
$objPHPExcel->getActiveSheet()->setCellValue("BD", "线下结算客户缺货库存");

	
$sql  = "SELECT a.add_time,a.check_cost,a.isuse,a.update_status_time,a.goods_sn, a.goods_name, a.goods_cost, a.cguser, a.goods_category, a.mainsku, a.goods_location, a.goods_weight,
					a.cguser, b.goods_count,b.second_stock_count, c.everyday_sale, c.salensend, c.interceptnums, c.auditingnums, d.company_name,
					c.platformautointerceptnums,c.platformsenddaynums,c.platformsendmonthnums,c.totalmonthnum,c.autointerceptnums
				FROM ebay_goods as a 
				LEFT JOIN ebay_onhandle AS b ON a.goods_sn = b.goods_sn
				LEFT JOIN ebay_sku_statistics AS c ON a.goods_sn=c.sku
				LEFT JOIN ebay_partner AS d ON a.factory=d.id
				WHERE a.mainsku>0 AND a.cguser!='' {$where} 
	 			ORDER BY a.mainsku ASC, a.goods_price DESC, a.goods_id ASC {$limit}" ;
$sql  = $dbcon->execute($sql);
$infos = $dbcon->getResultArray($sql);
$temsku = '';
$temname = array();
$sku_infos = array();

            
// Set page orientation and size
echo date('H:i:s') , " Set page orientation and size" , EOL;
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('库存管理报表');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Save Excel 95 file
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
