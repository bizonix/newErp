<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/PHPExcel.php";

error_reporting(-1);
$idlist = isset($_GET["data"]) ? $_GET['data'] : '';
$data   = array();
if(!empty($idlist)){
	$data   = explode(',',$idlist);
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
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','配货数量');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','发货数量');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','采购员');
	$dataArr = exportOrder($data);
	$row = 2;
	$rownum = count($dataArr);
	for($ii = 0; $ii < $rownum; $ii++){
		$list = $dataArr[$ii];
		foreach($list as $v){
			$addtime 		= $v['addtime'];
			$recordnumber 	= $v['recordnumber'];
			$parname		= $v['parname'];
			$purname 		= $v['purname'];
			$sku 			= $v['sku'];
			$name 			= $v['name'];
			$price 			= $v['price'];
			$count 			= $v['count'];
			$totalmoney 	= $v['totalmoney'];
			$stockqty       = $v['stockqty'];
			$sendqty        = $v['sendqty'];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, date("Y/m/d", $addtime));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $recordnumber);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $name);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $count);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $price);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $totalmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $stockqty);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $sendqty);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, $purname);
			$row++;
		}
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
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(10);
	$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(10);

	$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
	$title		= "Files_purchase".date('Y-m-d');
	$titlename	= $title.".xls";

	$objPHPExcel->getActiveSheet()->setTitle($title);
	$objPHPExcel->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename={$titlename}");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}

function exportOrder($data){
	global $dbConn;
		$num 		= 0;
		$dataArr 	= array();
		foreach($data as $id){
			$sql 	    = "SELECT a.addtime, a.recordnumber, a.purchaseuser_id, b.sku, b.price, b.count, b.parid, b.stockqty, b.sendqty  FROM ph_ow_order as a ";
			$sql       .= " JOIN ph_ow_order_detail as b ON a.id = b.po_id WHERE a.id = '{$id}' AND a.is_delete = 0 AND b.is_delete = 0 ";
			$query  	= $dbConn->query($sql);
			$datalist 	= array();
			if($query){
				$rtnData = $dbConn->fetch_array_all($query);
				if(!empty($rtnData)){
					$ii       = 0;
					foreach($rtnData as $k => $v){
						$addtime      = $v['addtime'];
						$recordnumber = $v['recordnumber'];
						$parid        = $v['parid'];
						//$purid        = $v['purchaseuser_id'];
						$price        = $v['price'];
						$count        = $v['count'];
						$sku          = $v['sku'];
						$stockqty     = $v['stockqty'];
						$sendqty      = $v['sendqty'];
						$skuinfo      = getSkuById($sku);
						$name         = $skuinfo[0]['goodsName'];
						$parname      = getParNameById($parid);
						$purid        = getOverCguser($sku);
						$purname      = getNameById($purid);
						$datalist[$ii]['addtime'] 		= $addtime;
						$datalist[$ii]['recordnumber'] 	= $recordnumber;
						$datalist[$ii]['parname'] 		= $parname;
						$datalist[$ii]['purname'] 		= $purname;
						$datalist[$ii]['stockqty']      = $stockqty;
						$datalist[$ii]['sendqty']       = $sendqty;
						$datalist[$ii]['sku'] 			= $sku;
						$datalist[$ii]['name'] 			= $name;
						$datalist[$ii]['price'] 		= $price;
						$datalist[$ii]['count'] 		= $count;
						$datalist[$ii]['totalmoney'] 	= $price * $count;
						$ii++;
					}
					$dataArr[$num] = $datalist;
					$num++;
				}
			}
		}
		return $dataArr;
}


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

//根据料号获取海外采购员
function getOverCguser($sku){
	global $dbConn;
	$sql 	= "SELECT OverSeaSkuCharger FROM pc_goods WHERE sku = '{$sku}'";
	$query	= $dbConn->query($sql);
	if ($query) {
		$data = $dbConn->fetch_array_all($query);
		if(!empty($data)){
			return $data[0]['OverSeaSkuCharger'];
		}else{
			return false;
		}
	}else{
		return false;
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
