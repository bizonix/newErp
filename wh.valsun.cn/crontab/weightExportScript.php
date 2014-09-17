<?php
/*
 * 脚本总汇-重量报表导出 weightExportScript.php
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

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '物品总编号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '信封');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '信封重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '信封容量---1个信封能装几个产品？');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '产品实际重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'CN邮政-重量特别修正');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '修正后产品重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '实际总重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '总重量－修正后');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '备注包装类型');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '不带包装大小(cm)');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '带包装大小(cm)');

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '是否带包装');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '港版汇率：');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '信封价格');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', '包材规格');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', '包材重量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', '包材规格说明');

$row  = 2;
mysql_query('SET NAMES UTF8');
//LIMIT 1,10
$spuSql	     = "SELECT spu FROM  `pc_goods` GROUP BY spu ORDER BY id ASC";
$spuSql      = $dbConn->query($spuSql);
$spuSql      = $dbConn->fetch_array_all($spuSql);
/*
 *1. a.ebay_packingmaterial 包材
 *2. a.ispacking 0:不带包装 1：带包装  这个不确定，先空着，没接口。   目前是用pmCapacity对应包材容量判断  为 0 不带包装  非空 带包装
 */ 
foreach($spuSql as $spuArr){
	if(empty($spuArr['spu'])){
		
		$nullSpuSql      = "SELECT a.sku,a.pmId,a.pmCapacity,a.goodsWeight,a.goodsNote,a.goodsLength,a.goodsWidth,a.goodsHeight FROM  `pc_goods` as a WHERE  a.spu = '' ORDER BY a.sku ASC";
		$nullSpuSql      = $dbConn->query($nullSpuSql);
		$nullSpuSql      = $dbConn->fetch_array_all($nullSpuSql);
		foreach($nullSpuSql as $goodsArr){
				$aa	= 'A'.$row;
				$bb	= 'B'.$row;
				$cc	= 'C'.$row;
				$dd	= 'D'.$row;
				$ee	= 'E'.$row;
				$ff	= 'F'.$row;
				$gg	= 'G'.$row;
				$hh	= 'H'.$row;
				$ii	= 'I'.$row;
				$jj	= 'J'.$row;
				$kk	= 'K'.$row;
				$ll	= 'L'.$row;
				$mm	= 'M'.$row;
				$nn	= 'N'.$row;
				$oo	= 'O'.$row;
				$pp	= 'P'.$row;
				
				
				$sku					  = $goodsArr['sku'];//料号
				
				$pmId				      = $goodsArr['pmId'];//信封ID
				if($pmId == 0){
					$pmNameStr	 = "无";//信封
					$pmWeight	 = "无";//信封重量
					$pmCost		 = "无";//信封价格
				}else{
					//包材信息API接口调用方法
					$para_path['method']      = 'pc.getPmInfoById';  //API名称
					$para_path['id']          = $pmId;
					$cate_info 		  	      = UserCacheModel::callOpenSystem($para_path);
					$pmNameStr				  = $cate_info['data']['pmName'];//信封
					$pmWeight				  = $cate_info['data']['pmWeight'];//信封重量
					$pmCost					  = $cate_info['data']['pmCost'];
				}
				
				if($goodsArr['pmCapacity'] == 0 && $pmId == 0){
					$pmCapacity = "无";//信封容量
					$ispacking  = "否";////0:不带包装 1：带包装
				}else{
					$pmCapacity = $goodsArr['pmCapacity'];//信封容量
					$ispacking  = "是";////0:不带包装 1：带包装
				}
				
				$goodsWeight			= $goodsArr['goodsWeight'];//SKU实际重量（不带包装）
				$goodsNote			    = $goodsArr['goodsNote'];//备注包装类型
				
				if(empty($goodsArr['goodsLength']) || empty($goodsArr['goodsWidth']) || empty($goodsArr['goodsHeight'])){
					$skuRules = "";
				}else{
					$skuRules			= $goodsArr['goodsLength']."*".$goodsArr['goodsWidth']."*".$goodsArr['goodsHeight'];//SKU不带包装大小(cm)
				}
				
				$objPHPExcel->setActiveSheetIndex(0)->getCell($aa)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);//物品总编号
				$objPHPExcel->setActiveSheetIndex(0)->getCell($bb)->setValueExplicit($sku, PHPExcel_Cell_DataType::TYPE_STRING);//料号
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cc, $pmNameStr);//信封
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($dd, $pmWeight);//信封重量				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ee, $pmCapacity);//信封容量				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ff, $goodsWeight);//SKU实际重量（不带包装）				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($gg, '0.000');//CN邮政-重量特别修正(没数据)				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($hh, $goodsWeight);//修正后产品重量				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ii, $pmWeight + $goodsWeight);//实际总重量
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($jj, $pmWeight + $goodsWeight);//总重量－修正后				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($kk, $goodsNote);//备注包装类型				
				if($ispacking == '是'){//0:不带包装 1：带包装
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ll, "");//不带包装大小(cm)
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($mm, $skuRules);//带包装大小(cm)				
				}else{
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ll, $skuRules);//不带包装大小(cm)
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($mm, "");//带包装大小(cm)					
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($nn, $ispacking);//是否带包装
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($oo, "");//港币汇率
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pp, $pmCost);//信封价格
				$row++;
		}
		
	}else{
		$mainSql     = "SELECT a.spu,a.sku,a.pmId,a.pmCapacity,a.goodsWeight,a.goodsNote,a.goodsLength,a.goodsWidth,a.goodsHeight FROM  `pc_goods` as a WHERE  a.spu = '".$spuArr['spu']."' ORDER BY a.goodsWeight DESC";
		$mainSql     = $dbConn->query($mainSql);
		$mainSql     = $dbConn->fetch_array_all($mainSql);
		foreach($mainSql as $keyId => $mainValArr){
			$aa	= 'A'.$row;
			$bb	= 'B'.$row;
			$cc	= 'C'.$row;
			$dd	= 'D'.$row;
			$ee	= 'E'.$row;
			$ff	= 'F'.$row;
			$gg	= 'G'.$row;
			$hh	= 'H'.$row;
			$ii	= 'I'.$row;
			$jj	= 'J'.$row;
			$kk	= 'K'.$row;
			$ll	= 'L'.$row;
			$mm	= 'M'.$row;
			$nn	= 'N'.$row;
			$oo	= 'O'.$row;
			$pp	= 'P'.$row;
			
			$spu					= $mainValArr['spu'];
			$sku				    = $mainValArr['sku'];//料号				
			$pmId				    = $mainValArr['pmId'];//信封ID
			if($pmId == 0){
				$pmNameStr	 = "无";//信封
				$pmWeight	 = "无";//信封重量
				$pmCost		 = "无";//信封价格
			}else{
				//包材信息API接口调用方法
				$para_path['method']      = 'pc.getPmInfoById';  //API名称
				$para_path['id']          = $pmId;
				$cate_info 		  	      = UserCacheModel::callOpenSystem($para_path);
				$pmNameStr				  = $cate_info['data']['pmName'];//信封
				$pmWeight				  = $cate_info['data']['pmWeight'];//信封重量
				$pmCost					  = $cate_info['data']['pmCost'];
			}
			
			if($mainValArr['pmCapacity'] == 0 && $pmId == 0){
				$pmCapacity = "无";//信封容量
				$ispacking  = "否";////0:不带包装 1：带包装
			}else{
				$pmCapacity = $mainValArr['pmCapacity'];//信封容量
				$ispacking  = "是";////0:不带包装 1：带包装
			}
			
			$goodsWeight			= $mainValArr['goodsWeight'];//SKU实际重量（不带包装）
			$goodsNote			    = $mainValArr['goodsNote'];//备注包装类型
			
			if(empty($mainValArr['goodsLength']) || empty($mainValArr['goodsWidth']) || empty($mainValArr['goodsHeight'])){
				$skuRules = "";
			}else{
				$skuRules			= $mainValArr['goodsLength']."*".$mainValArr['goodsWidth']."*".$mainValArr['goodsHeight'];//SKU不带包装大小(cm)
			}
			
			if($keyId == 0){
				$objPHPExcel->setActiveSheetIndex(0)->getCell($aa)->setValueExplicit($spu, PHPExcel_Cell_DataType::TYPE_STRING);//物品总编号
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->getCell($aa)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);//物品总编号
			}
			
			$objPHPExcel->setActiveSheetIndex(0)->getCell($bb)->setValueExplicit($sku, PHPExcel_Cell_DataType::TYPE_STRING);//料号
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cc, $pmNameStr);//信封
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($dd, $pmWeight);//信封重量				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ee, $pmCapacity);//信封容量				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ff, $goodsWeight);//SKU实际重量（不带包装）				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($gg, '0.000');//CN邮政-重量特别修正(没数据)				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($hh, $goodsWeight);//修正后产品重量				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ii, $pmWeight + $goodsWeight);//实际总重量
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($jj, $pmWeight + $goodsWeight);//总重量－修正后				
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($kk, $goodsNote);//备注包装类型				
			if($ispacking == '是'){//0:不带包装 1：带包装
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ll, "");//不带包装大小(cm)
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($mm, $skuRules);//带包装大小(cm)				
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ll, $skuRules);//不带包装大小(cm)
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($mm, "");//带包装大小(cm)					
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($nn, $ispacking);//是否带包装
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($oo, "");//港币汇率
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pp, $pmCost);//信封价格
			$row++;				
		}		
	}
}

$packingmaterialRow  = 2;
//包材信息API接口调用方法
$para_path['method']      = 'pc.getPmInfoAll';  //API名称
$cate_info 		  	      = UserCacheModel::callOpenSystem($para_path);

foreach($cate_info['data'] as $keyName => $packingmaterial){

	$qq	= 'Q'.$packingmaterialRow;
	$rr	= 'R'.$packingmaterialRow;
	$ss = 'S'.$packingmaterialRow;

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($qq, $packingmaterial['pmName']);//包材规格
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($rr, $packingmaterial['pmWeight']);//包材重量
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ss, $packingmaterial['pmNotes']);//包材规格说明	
	$packingmaterialRow++;	
}



$start			= strtotime(date("Y-m-d")." 00:00:01");
$end			= strtotime(date("Y-m-d")." 23:59:59");
$title 			= "weightExport_".date('Y-n-j', $end);
$titlename      = "/home/exportFile/everyday_weightExport/weightExport_".date('Y-n-j', $start)."_".date('Y-n-j', $end).".xls";

$objPHPExcel->getActiveSheet(0)->getStyle('A1:S'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(15);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(15);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:S'.($row-1))->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($titlename);
exit;
?>
