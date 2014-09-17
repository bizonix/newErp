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

$objPHPExcel->getActiveSheet()->setCellValue('A1', "料号");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "aliexpress");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "cndirect");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "DHgate");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "dresslink");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "ebay");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "newegg");
$objPHPExcel->getActiveSheet()->setCellValue('H1', "amazon");
$objPHPExcel->getActiveSheet()->setCellValue('I1', "出口通");
$objPHPExcel->getActiveSheet()->setCellValue('J1', "国内销售部");
$objPHPExcel->getActiveSheet()->setCellValue('K1', "天猫zegoo");
$objPHPExcel->getActiveSheet()->setCellValue('L1', "天猫fenjo");
$objPHPExcel->getActiveSheet()->setCellValue('M1', "海外仓");
$objPHPExcel->getActiveSheet()->setCellValue('N1', "线下交易");
$objPHPExcel->getActiveSheet()->setCellValue('O1', "每日总发货数");
$objPHPExcel->getActiveSheet()->setCellValue('P1', "采购");


$datetime = time() -24*60*60;
$dateformat = date("Y-m-d",$datetime);
echo $dateformat;
$cursor = $m->daysale->$dateformat->find();
$k = 2;
foreach ($cursor as $item) {
    // do something to each document
	$platformdata = $item['platformnums'];
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$k, $item['sku']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$k, $platformdata['aliexpress'] );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$k, $platformdata['cndirect']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$k, $platformdata["DHgate"]);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$k, $platformdata["dresslink"]);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$k, $platformdata["ebay"]);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$k, $platformdata["Newegg"]);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$k, $platformdata["amazon"]);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$k, $platformdata["chukoutong"]);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$k, $platformdata["guonei"]);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$k, $platformdata["zegoo"]);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$k, $platformdata["fenjo"]);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$k, $platformdata["oversea"]);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$k, $platformdata["offline"]);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$k, $item['totalnums']);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$k, $item['cguser']);
	$k++;
}


            
// Set page orientation and size
echo date('H:i:s') , " Set page orientation and size" , EOL;
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('每日各平台发货报表');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
$filepath = "/data/web/data/download/daysale_{$dateformat}.xlsx";
echo $filepath."\n";
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($filepath);

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
?>
