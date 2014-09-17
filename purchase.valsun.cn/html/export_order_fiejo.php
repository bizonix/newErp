<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/PHPExcel.php";

error_reporting(-1);
$idlist = isset($_GET["data"]) ? $_GET['data'] : '';
$sql = "SELECT id FROM `ph_partner` WHERE `company_name` LIKE '芬哲制衣%'";
$sql = $dbConn->execute($sql);
$idarr = $dbconn->getResultArray($sql);
$idtmp = array();
foreach($idarr as $iditem){
	$idtmp[] = $iditem['id'];
}
$idstr = implode(",",$idtmp);

$sql = "SELECT id from ph_order where addtime>1385856001 and partner_id in({$idstr})";
$sql = $dbConn->execute($sql);
$idArr = $dbconn->getResultArray($sql);

$data   = array();
foreach($idArr as $iditem){
	$data[] = $iditem['id'];
}
if(!empty($idlist) || true){
	//$data   = explode(',',$idlist);
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
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','到货数量');
	//$dataArr = $PO->actExportOrder($data);
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
			$qty 	= $v['stockqty'];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, date("Y/m/d", $addtime));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $recordnumber);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $name);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $count);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $price);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $totalmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $purname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $qty);
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

	$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
	$title		= "Files_purchase".date('Y-m-d');
	$titlename	= $title.".xls";

	$objPHPExcel->getActiveSheet()->setTitle($title);
	$objPHPExcel->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename={$titlename}");
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($titlename);
}

function exportOrder($data){
	global $dbConn;
		$num = 0;
		foreach($data as $id){
			$sql 	    = "SELECT a.addtime, a.recordnumber, a.partner_id, a.purchaseuser_id, b.sku, b.price, b.count,b.stockqty  FROM ph_order as a ";
			$sql       .= " JOIN ph_order_detail as b ON a.id = b.po_id WHERE a.id = '{$id}' AND a.is_delete = 0 AND b.is_delete = 0 ";
			$query  	= $dbConn->query($sql);
			$datalist 	= array();
			if($query){
				$rtnData = $dbConn->fetch_array_all($query);
				if(!empty($rtnData)){
					$ii       = 0;
					foreach($rtnData as $k => $v){
						$addtime      = $v['addtime'];
						$recordnumber = $v['recordnumber'];
						$parid        = $v['partner_id'];
						$purid        = $v['purchaseuser_id'];
						$price        = $v['price'];
						$count        = $v['count'];
						$sku          = $v['sku'];
						$stockqty     = $v['stockqty'];
						$skuinfo      = getSkuById($sku);
						$name         = $skuinfo[0]['goodsName'];
						//$parname      = getParNameById($parid);
						$parname      = getPartnerBySku($sku);
						$purname      = getNameById($purid);
						$datalist[$ii]['addtime'] 		= $addtime;
						$datalist[$ii]['recordnumber'] 	= $recordnumber;
						$datalist[$ii]['parname'] 		= $parname;
						$datalist[$ii]['purname'] 		= $purname;
						$datalist[$ii]['sku'] 			= $sku;
						$datalist[$ii]['name'] 			= $name;
						$datalist[$ii]['price'] 		= $price;
						$datalist[$ii]['count'] 		= $count;
						$datalist[$ii]['stockqty'] 		= $stockqty;
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

function getPartnerBySku($sku){
	global $dbconn;
	$sql = "select b.company_name from ph_user_partner_relation  as a left join ph_partner as b on a.partnerId=b.id where a.sku='{$sku}'";
	$sql = $dbconn->execute($sql);
	$partner = $dbconn->fetch_one($sql);
	return $partner["company_name"];
}

?>
