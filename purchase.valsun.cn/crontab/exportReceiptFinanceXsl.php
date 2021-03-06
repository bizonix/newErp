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
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','订货价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','订货金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','采购员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','订货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','首次到货日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','首次到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1','到货日期-2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1','到货日期-3');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1','到货日期-4');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1','到货日期-5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1','到货日期-6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1','到货日期-7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1','到货日期-8');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA1','到货日期-9');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC1','实收数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD1','数量核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE1','到货状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF1','出货单价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG1','价格核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH1','价格确认');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI1','实收产品货款金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ1','未到产品货款金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK1','收货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM1','已付货款全额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO1','已付款货订金');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ1','已付货款余额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS1','已付部份货款-1');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU1','已付部份货款-2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW1','已付部份货款-3');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY1','已付部份货款-4');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA1','已付部份货款-5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC1','已付部份货款-6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE1','已付部份货款-7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF1','支付方式');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG1','已付货款总额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH1','实际应付货款');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI1','实际已付货款差额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ1','付款状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK1','运费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL1','付款备注');
		$dataArr 	= exportData();
		$row 		= 2;
		$tmpordersn = '';
		$tmpfee     = '';
		$totalrow   = count($dataArr) + 1;
		foreach($dataArr as $k => $v){
			$id             = $v['id'];
			$addtime 		= date('Y/m/d', $v['purtime']);
			$ordersn     	= $v['ordersn'];
			$parnter		= $v['parnter'];
			$sku 			= $v['sku'];
			$nameinfo       = getSkuName($sku);
			$skuname        = $nameinfo[0]['goodsName'];
			$purcount 		= $v['purcount'];
			$purprice 		= $v['purprice'];
			$purmoney 		= $v['purcount'] * $v['purprice'];//订货金额
			$cguser         = $v['cguser'];
			$purnote 		= $v['purnote'];
			$actualcount    = $v['actualcount'];
			$paymoney       = $v['purmoney'];//已付款金额
			$recmoney       = $actualcount * $purprice;//实收货款金额
			$unrecmoney     = $purmoney - $recmoney;//未收货款金额
			$paytime        = !empty($v['paytime']) ? date('Y/m/d', $v['paytime']) : '';
			$paymethod      = $v['paymethod'];
			$fee            = $v['fee'];
			$diffmoney      = $paymoney - $recmoney;//实际已付货款差额
			$diffcount      = $actualcount - $purcount;
			$payresult      = '';
			if($actualcount >= $purcount){
				$stu = 'OK';
			}else{
				$stu = '-';
			}
			if($diffmoney == 0){
				$payresult = '结清';
			}else{
				$payresult = '应付';
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $addtime);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $ordersn);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parnter);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $skuname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $purcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $purprice);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $purmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $cguser);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $paymethod);
			$detailArr 	= getReceiptGoodsDetailById($id);
			if(!empty($detailArr)){
				$signnum    = 0;
				foreach($detailArr as $kk => $vv){
					$_intime 	= $vv['intime'];
					$_incount 	= $vv['incount'] ;
					$_innote    = $vv['innote'];
					$_intime    = date('Y/m/d', $_intime);
					switch($signnum){
						case '1':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$row, $_incount);
							break;
						case '2':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$row, $_incount);
							break;
						case '3':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$row, $_incount);
							break;
						case '4':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.$row, $_incount);
							break;
						case '5':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$row, $_incount);
							break;
						case '6':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.$row, $_incount);
							break;
						case '7':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.$row, $_incount);
							break;
						case '8':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.$row, $_incount);
							break;
						default:
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$row, $_incount);
							break;
					}
					$signnum++;
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.$row, $actualcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD'.$row, $diffcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.$row, $stu);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF'.$row, $purprice);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.$row, '0');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH'.$row, 'OK');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI'.$row, $recmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ'.$row, $unrecmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL'.$row, $paytime);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM'.$row, $paymoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF'.$row, $paymethod);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG'.$row, $paymoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH'.$row, $recmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI'.$row, $diffmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ'.$row, $payresult);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$row, '');
			if($tmpordersn != $ordersn){
				$tmpordersn = $ordersn;
				if($row != 2){
					$tmprow = $row - 1;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$tmprow, $tmpfee);
				}
				$tmpfee     = $fee;
			}
			if($row == $totalrow){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$totalrow, $fee);
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL'.$row, '');
			$row++;
		}
		

		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(60);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Z')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AA')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AB')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AC')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AD')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AE')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AF')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AG')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AH')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AI')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AJ')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AK')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AL')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AM')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AN')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AO')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AP')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AQ')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AR')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AS')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AT')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AU')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AV')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AW')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AX')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AY')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AZ')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BA')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BB')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BC')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BD')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BE')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BF')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BG')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BH')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BI')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BJ')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BK')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BL')->setWidth(15);
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);

		$path 		= "/data/web/purchase.valsun.cn/html/download/receiptExcel/";
		$title		= "finance_".date('Y-m-d');
		$titlename	= $path.$title.".xls";

		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);


		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($titlename);
		
		function exportData(){
			global $dbConn;
			$orderby    = " ORDER BY purtime ASC ,ordersn ASC";
			$sql 		= "SELECT * FROM ph_receipt_goods ";
			$query 		= $dbConn->execute($sql);
			$dataInfo   = array();
			if($query){
				$dataInfo = $dbConn->getResultArray($query);
			}
			return $dataInfo;
		}
		
		function getReceiptGoodsDetailById($id){
			global $dbConn;
			$sql    	= "SELECT * FROM  `ph_receipt_goods_detail` WHERE rid = '{$id}' ORDER BY id ASC";
			$query    	= $dbConn->query($sql);
			$dataInfo   = array();
			if($query){
				$dataInfo 	= $dbConn->getResultArray($query);
			}
			return $dataInfo;
		}
		
		function getSkuName($sku){
			global $dbConn;
			$sql 		= "SELECT goodsName FROM pc_goods WHERE sku = '{$sku}' ";
			$query    	= $dbConn->query($sql);
			$dataInfo   = array();
			if($query){
				$dataInfo 	= $dbConn->getResultArray($query);
			}
			return $dataInfo;
		}
?>
