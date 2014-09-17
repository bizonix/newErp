<?php
/*
 * 脚本总汇--组合产品价格信息表导出 zuHePriceInfoExportScript.php
 * add by chenwei 2013.11.12
 */
error_reporting(-1);
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
Core::getInstance();

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
					 ->setLastModifiedBy("Maarten Balliauw")
					 ->setTitle("Office 2007 XLSX Test Document")
					 ->setSubject("Office 2007 XLSX Test Document")
					 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					 ->setKeywords("office 2007 openxml php")
					 ->setCategory("Test result file");

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','组合料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','产品描述');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','可用库存数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','现行单价');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','采购');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','产品重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','仓位号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','实际库存数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','产品类别');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','存货位');

$row  = 2;
mysql_query('SET NAMES UTF8');
//组合产品导出 LIMIT 1,10
$exportSql = "SELECT id,combineSpu,combineSku,combineNote,combinePrice,combineUserId,combineWeight FROM `pc_goods_combine` WHERE is_delete != 1 ORDER BY id ASC";
$exportSql = $dbConn->query($exportSql);
$exportSql = $dbConn->fetch_array_all($exportSql);
foreach($exportSql as $zhpriceInfo){
	$combineSpu      = $zhpriceInfo['combineSpu'];//组合主料号
	$combineSku      = $zhpriceInfo['combineSku'];//组合SKU
	$combineNote     = $zhpriceInfo['combineNote'];//组合描述
	$combinePrice 	 = $zhpriceInfo['combinePrice'];//组合产品单价
	
	$combineUserId   = $zhpriceInfo['combineUserId']; //采购ID
	if(empty($combineUserId)){
		$combineUserId = "无";
	}else{
		//获取采购人名称
		$usermodel       = UserModel::getInstance();
		$whereStr	     = "where a.global_user_id=".$combineUserId;         
		$cgUser	         = $usermodel->getGlobalUserLists('global_user_name',$whereStr,'','');//$cgUser[0]['global_user_name'];
		$combineUserId   =  $cgUser[0]['global_user_name'];		
	}
	$combineWeight 	 = $zhpriceInfo['combineWeight'];//组合产品重量
	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$row)->setValueExplicit($combineSpu, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$row)->setValueExplicit($combineSku, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$row)->setValueExplicit(@trim($combineNote), PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);			
	$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$row)->setValueExplicit($combinePrice, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$row)->setValueExplicit($combineUserId, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$row)->setValueExplicit($combineWeight, PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING); 		 
	$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$row++;		
}





$start			= strtotime(date("Y-m-d")." 00:00:01");
$end			= strtotime(date("Y-m-d")." 23:59:59");
$title 			= "priceInfo_".date('Y-n-j', $end);
$titlename = "/home/exportFile/everyday_priceInfo_zuHeSku/zuHeSkuPriceInfo_".date('Y-n-j', $start)."_".date('Y-n-j', $end).".xls";

$objPHPExcel->getActiveSheet(0)->getStyle('A1:K'.($row-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(10);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(100);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:K'.($row-1))->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($titlename);
exit;
?>
