<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/PHPExcel.php";
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','SKU');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','到货数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','异常数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','采购');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','供应商');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','到货时间');

$row = 2;

$sql = "SELECT * from ph_sku_reach_record  where status=0 ";
$sql = $dbConn->execute($sql);
$skuInfo = $dbConn->getResultArray($sql);
foreach($skuInfo as $item){
	$sku = $item['sku'];
	$totalAmount = $item['totalAmount'];
	$amount = $item['amount'];
	$purname = getNameById($item['purchaseId']);
	$partner = $item['partnerName'];
	$addtime = date("Y-m-d",$item['addtime']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $sku);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $totalAmount);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $amount);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $purname);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $partner);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $addtime);
	$row++;
}

$objPHPExcel->getActiveSheet(0)->getStyle('A1:N'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(80);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
$title		= "unusualOrder".date('Y-m-d');
$titlename	= $title.".xls";

$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename={$titlename}");
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($titlename);


function getSkuById($sku){
	global $dbConn;
	$sql = "SELECT spu, sku, goodsName FROM pc_goods where sku= '{$sku}' AND is_delete = 0";
	$query = $dbConn->query($sql);
	if($query){
		$rtn_data = $dbConn->fetch_array_all($query);
		if(!empty($rtn_data)){
			return $rtn_data;
		}
	}else{
		return false;
	}
}

function getParNameById($id){
	global $dbConn;
	$sql 	= "SELECT company_name FROM ph_partner WHERE id = '{$id}' AND is_delete = 0";
	$query	= $dbConn->query($sql);
		if ($query) {
			$data = $dbConn->fetch_array_all($query);
			if(!empty($data)){
				return $data[0]['company_name'];
			}else{
				return '';
			}
		}else{
			return '';
		}
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
