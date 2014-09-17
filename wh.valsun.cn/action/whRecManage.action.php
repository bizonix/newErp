<?php


/*
 * 收货信息表action
 * ADD BY zqt 2013.8.21
 */
class WhRecManageAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = WhIoStoreModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = WhIoStoreModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_addTNameRow($tName, $set) {
		$ret = WhIoStoreModel :: addTNameRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_updateTNameRow($tName, $set, $where) {
		$ret = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_exportRecManageExcel() {
		$eStartTime = isset ($_GET['eStartTime']) ? post_check($_GET['eStartTime']) : '';
		$eEndTime = isset ($_GET['eEndTime']) ? post_check($_GET['eEndTime']) : '';
		$where = "WHERE 1=1 ";
		if (!empty ($eStartTime)) {
			$startTime = strtotime($eStartTime . '00:00:00');
			$where .= "AND orderDate >='$startTime' ";
		}
		if (!empty ($eEndTime)) {
			$endTime = strtotime($eEndTime . '23:59:59');
			$where .= "AND orderDate <='$endTime' ";
		}
		require_once '../lib/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0)->getCell('A1')->setValueExplicit('订货日期', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('B1')->setValueExplicit('订单号', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('C1')->setValueExplicit('供应商ID', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('D1')->setValueExplicit('料号', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('E1')->setValueExplicit('产品描述', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('F1')->setValueExplicit('订货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('G1')->setValueExplicit('订货价格', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('H1')->setValueExplicit('订货金额', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('I1')->setValueExplicit('采购员ID', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('J1')->setValueExplicit('订货备注', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('K1')->setValueExplicit('首次到货日期', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('L1')->setValueExplicit('首次到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('M1')->setValueExplicit('到货日期_2', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('N1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('O1')->setValueExplicit('到货日期_3', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('P1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('Q1')->setValueExplicit('到货日期_4', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('R1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('S1')->setValueExplicit('到货日期_5', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('T1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('U1')->setValueExplicit('到货日期_6', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('V1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('W1')->setValueExplicit('到货日期_7', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('X1')->setValueExplicit('到货数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('Y1')->setValueExplicit('实收数量', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('Z1')->setValueExplicit('数量核对', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AA1')->setValueExplicit('到货状态', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AB1')->setValueExplicit('出货单价格', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AC1')->setValueExplicit('价格核对', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AD1')->setValueExplicit('价格确认', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AE1')->setValueExplicit('收货备注', PHPExcel_Cell_DataType :: TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->getCell('AF1')->setValueExplicit('实际应付货款', PHPExcel_Cell_DataType :: TYPE_STRING);

		$row = 2;
		$tName = 'wh_receipt_management';
		$select = '*';
		$where .= "ORDER BY orderDate";
		$whRecManageList = WhRecManageModel :: getTNameList($tName, $select, $where);

		foreach ($whRecManageList as $value) {
			$id = $value['id']; //收货管理表记录id
			$ordersn = $value['ordersn']; //订单号
			$sku = $value['sku']; //料号
			//产品描述
			$amount = $value['amount']; //订货数量
			$reStatus = $value['reStatus']; //单据状态，0未完成，1已完成
			$arrivedNums = $value['arrivedNums']; //已到货数量
			$unitPrice = $value['unitPrice']; //单价
			$total = $value['total']; //总金额，=单价*订货数量
			$actualPayment = $value['actualPayment']; //实际应付款
			$partnerId = $value['partnerId']; //供应商id
			$purchaseId = $value['purchaseId']; //采购员id
			$orderDate = $value['orderDate']; //订货日期
			$orderNotes = $value['orderNotes']; //订货备注
			$storeId = $value['storeId']; //仓库id
			$companyId = $value['companyId']; //公司id
			$createdTime = $value['createdTime']; //单据生成时间

			$objPHPExcel->setActiveSheetIndex(0)->getCell('A' . $row)->setValueExplicit(date('Y-m-d', $orderDate), PHPExcel_Cell_DataType :: TYPE_STRING); //订货日期
			$objPHPExcel->setActiveSheetIndex(0)->getCell('B' . $row)->setValueExplicit($ordersn, PHPExcel_Cell_DataType :: TYPE_STRING); //订单号
			$objPHPExcel->setActiveSheetIndex(0)->getCell('C' . $row)->setValueExplicit($partnerId, PHPExcel_Cell_DataType :: TYPE_STRING); //供应商id
			$objPHPExcel->setActiveSheetIndex(0)->getCell('D' . $row)->setValueExplicit($sku, PHPExcel_Cell_DataType :: TYPE_STRING); //料号
			$objPHPExcel->setActiveSheetIndex(0)->getCell('E' . $row)->setValueExplicit('', PHPExcel_Cell_DataType :: TYPE_STRING); //产品描述
			$objPHPExcel->setActiveSheetIndex(0)->getCell('F' . $row)->setValueExplicit($amount, PHPExcel_Cell_DataType :: TYPE_STRING); //订货数量
			$objPHPExcel->setActiveSheetIndex(0)->getCell('G' . $row)->setValueExplicit($unitPrice, PHPExcel_Cell_DataType :: TYPE_STRING); //单价
			$objPHPExcel->setActiveSheetIndex(0)->getCell('H' . $row)->setValueExplicit($total, PHPExcel_Cell_DataType :: TYPE_STRING); //订货总金额
			$objPHPExcel->setActiveSheetIndex(0)->getCell('I' . $row)->setValueExplicit($purchaseId, PHPExcel_Cell_DataType :: TYPE_STRING); //采购员id
			$objPHPExcel->setActiveSheetIndex(0)->getCell('J' . $row)->setValueExplicit($orderNotes, PHPExcel_Cell_DataType :: TYPE_STRING); //订货备注
			$objPHPExcel->setActiveSheetIndex(0)->getCell('Y' . $row)->setValueExplicit($arrivedNums, PHPExcel_Cell_DataType :: TYPE_STRING); //实收数量
			$objPHPExcel->setActiveSheetIndex(0)->getCell('Z' . $row)->setValueExplicit($arrivedNums - $amount, PHPExcel_Cell_DataType :: TYPE_STRING); //数量核对
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AA' . $row)->setValueExplicit($reStatus ? 'OK' : '-', PHPExcel_Cell_DataType :: TYPE_STRING); //到货状态
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AB' . $row)->setValueExplicit($reStatus ? $unitPrice : '', PHPExcel_Cell_DataType :: TYPE_STRING); //出货单价格
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AC' . $row)->setValueExplicit($reStatus ? '0' : -1 * $unitPrice, PHPExcel_Cell_DataType :: TYPE_STRING); //价格核对
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AD' . $row)->setValueExplicit($reStatus ? 'OK' : '需确认', PHPExcel_Cell_DataType :: TYPE_STRING); //价格确认
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AE' . $row)->setValueExplicit('', PHPExcel_Cell_DataType :: TYPE_STRING); //收货备注
			$objPHPExcel->setActiveSheetIndex(0)->getCell('AF' . $row)->setValueExplicit('', PHPExcel_Cell_DataType :: TYPE_STRING); //实际应付货款

			$tName = 'wh_receipt_management_details';
			$select = '*';
			$where = "WHERE rmId='$id' ORDER BY insertTime";
			$whRecManageDetailList = WhRecManageModel :: getTNameList($tName, $select, $where);

			if (isset ($whRecManageDetailList[0])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('K' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[0]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('L' . $row)->setValueExplicit($whRecManageDetailList[0]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[1])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('M' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[1]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('N' . $row)->setValueExplicit($whRecManageDetailList[1]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[2])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('O' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[2]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('P' . $row)->setValueExplicit($whRecManageDetailList[2]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[3])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('Q' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[3]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('R' . $row)->setValueExplicit($whRecManageDetailList[3]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[4])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('S' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[4]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('T' . $row)->setValueExplicit($whRecManageDetailList[4]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[5])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('U' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[5]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('V' . $row)->setValueExplicit($whRecManageDetailList[5]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			if (isset ($whRecManageDetailList[6])) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('W' . $row)->setValueExplicit(date('Y-m-d', $whRecManageDetailList[6]['insertTime']), PHPExcel_Cell_DataType :: TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('X' . $row)->setValueExplicit($whRecManageDetailList[6]['nums'], PHPExcel_Cell_DataType :: TYPE_STRING);
			}
			$row++;
		}

		//    $objPHPExcel->getActiveSheet(0)->getStyle('A1:Y'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		//
		//    $objPHPExcel->getActiveSheet(0)->getStyle('A1:Y'.$row)->getAlignment()->setWrapText(true);
		$title = '收货记录';
		$titlename = "exportWhRecManage" . date('Y-m-d') . ".xls";
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory :: createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	//对外接口，在收货管理表中添加一条记录(由采购订单在途订单添加进来)
	function act_addWhRecManage() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串(客户端要先json然后再base64))
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$ordersn = $jsonArr['ordersn']; //单据编码（采购订单编码）
		$sku = $jsonArr['sku']; //sku
		$amount = $jsonArr['amount']; //数量
		$unitPrice = $jsonArr['unitPrice']; //单价
		$total = $jsonArr['total']; //金额
		$partnerId = $jsonArr['partnerId']; //供应商Id
		$purchaseId = $jsonArr['purchaseId']; //采购员Id
		$orderDate = $jsonArr['orderDate']; //订货日期
		$orderNotes = isset ($jsonArr['orderNotes']) ? $jsonArr['orderNotes'] : ''; //订货备注
		$companyId = isset ($jsonArr['companyId']) ? $jsonArr['companyId'] : 1; //公司Id,默认为1
		$storeId = isset ($jsonArr['storeId']) ? $jsonArr['storeId'] : 1; //仓库Id,默认为1

		if (empty ($ordersn)) { //出入库类型表中的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty ordersn';
			return 0;
		}
		if (empty ($sku)) { //出或者入库
			self :: $errCode = 0401;
			self :: $errMsg = 'error sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		if (empty ($unitPrice)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty unitPrice';
			return 0;
		}
		if (empty ($total)) {
			self :: $errCode = 0701;
			self :: $errMsg = 'empty total';
			return 0;
		}
		if (empty ($partnerId)) {
			self :: $errCode = 0801;
			self :: $errMsg = 'empty partnerId';
			return 0;
		}
		if (empty ($purchaseId)) {
			self :: $errCode = 0901;
			self :: $errMsg = 'empty purchaseId';
			return 0;
		}
		if (empty ($orderDate)) {
			self :: $errCode = 0901;
			self :: $errMsg = 'empty orderDate';
			return 0;
		}
		$now = time();
		$tName = 'wh_receipt_management';
		$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',unitPrice='$unitPrice',total='$total',partnerId='$partnerId',purchaseId='$purchaseId',orderDate='$orderDate',orderNotes='$orderNotes',companyId='$companyId',storeId='$storeId',createdTime='$now' ";
		$affectRows = WhRecManageModel :: addTNameRow($tName, $set);
		if (!$affectRows) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//对内接口，在收货管理详细表中添加一条记录(由上架人员操作添加进来)
	function act_addWhRecManageDetail($jsonArr) {
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$sku = $jsonArr['sku']; //sku
		$nums = $jsonArr['nums']; //到货数量
		$userId = $jsonArr['userId']; //添加人id
		$batchNum = $jsonArr['batchNum']; //到货批次
		$now = time();
		$flag = 0; //标识变量，用来判断是否要进行采购未订单；
		try {
			TransactionBaseModel :: begin();
			if (empty ($sku)) { //sku不能为空
				self :: $errCode = 0101;
				self :: $errMsg = 'empty sku';
				throw new Exception('empty sku');
			}
			if (empty ($nums)) { //到货数量不能为空
				self :: $errCode = 0201;
				self :: $errMsg = 'empty nums';
				throw new Exception('empty nums');
			}
			if (intval($nums) == 0) { //数量不能为0
				self :: $errCode = 0311;
				self :: $errMsg = 'error nums';
				throw new Exception('error nums');
			}
			if (empty ($userId)) { //操作人id不能为空
				self :: $errCode = 0301;
				self :: $errMsg = 'empty userId';
				throw new Exception('empty userId');
			}
			//            if (empty ($batchNum)) { //批次不能为空
			//				self :: $errCode = 0302;
			//				self :: $errMsg = 'empty $atchNum';
			//				throw new Exception('empty batchNum');
			//			}

			$tName = 'wh_receipt_management';
			$select = '*';
			$where = "WHERE reStatus=0 AND sku='$sku' ORDER BY createdTime"; //reStatus为0，且sku符合的收货表记录
			$whRecManageList = WhRecManageModel :: getTNameList($tName, $select, $where);
			if (empty ($whRecManageList)) { //没有找到该sku对应的记录时，直接全部未订单
				self :: $errCode = 222;
				self :: $errMsg = 'success';
				$resupplyOrder = array (); //采购未订单数组
				$resupplyOrder['sku'] = $sku;
				$resupplyOrder['nums'] = $nums;
				return $resupplyOrder;
			}
			$diffNums = $whRecManageList[0]['amount'] - $whRecManageList[0]['arrivedNums']; //该记录中能插入的最大到货数量
			if ($nums <= $diffNums) { //nums小于本条记录能支持的最大到货数量
				$tName = 'wh_receipt_management_details';
				$set = "SET rmId='{$whRecManageList[0]['id']}',nums='$nums',userId='$userId',batchNum='$batchNum',insertTime='$now'";
				$affectRowAdd = WhRecManageModel :: addTNameRow($tName, $set);
				if (!$affectRowAdd) { //未插入成功报错
					self :: $errCode = 0501;
					self :: $errMsg = 'affectRowAdd error';
					throw new Exception('affectRowAdd error');
				}
				//同时更新匹配记录的字段，arrivedNums和reStatus
				$tName = 'wh_receipt_management';
				$set = "SET arrivedNums=arrivedNums+'$nums' ";
				if ($nums == $diffNums) {
					$set .= ",reStatus=1 ";
				}
				$where = "WHERE id='{$whRecManageList[0]['id']}'";
				$affectRowUp = WhRecManageModel :: updateTNameRow($tName, $set, $where);
				if (!$affectRowUp) { //未插入成功报错
					self :: $errCode = 0501;
					self :: $errMsg = 'affectRowUp error';
					throw new Exception('affectRowUp error');
				}
				$flag = 1;
			} else { //如果nums大于本条记录能支持的最大到货数量
				foreach ($whRecManageList as $value) {
					$diffNums = $value['amount'] - $value['arrivedNums']; //该记录中能插入的最大到货数量，此时第一次循环是，$num一定是>diffnums的
					if ($nums <= $diffNums) { //本次到货数量小于本条记录能支持的最大到货数量
						$tName = 'wh_receipt_management_details';
						$set = "SET rmId='{$value['id']}',nums='$nums',userId='$userId',batchNum='$batchNum',insertTime='$now'";
						$affectRowAdd = WhRecManageModel :: addTNameRow($tName, $set);
						if (!$affectRowAdd) { //未插入成功报错
							self :: $errCode = 0501;
							self :: $errMsg = 'affectRowAdd error';
							throw new Exception('affectRowAdd error');
						}
						//同时更新匹配记录的字段，arrivedNums和reStatus
						$tName = 'wh_receipt_management';
						$set = "SET arrivedNums=arrivedNums+'$nums' ";
						if ($nums == $diffNums) {
							$set .= ",reStatus=1 ";
						}
						$where = "WHERE id='{$value['id']}'";
						$affectRowUp = WhRecManageModel :: updateTNameRow($tName, $set, $where);
						if (!$affectRowUp) { //未插入成功报错
							self :: $errCode = 0501;
							self :: $errMsg = 'affectRowUp error';
							throw new Exception('affectRowUp error');
						}
						$flag = 1;
						break; //$nums <= $diffNums时，退出循环
					} else { //直接先将第一条匹配记录的最大差值填入
						$tName = 'wh_receipt_management_details';
						$set = "SET rmId='{$value['id']}',nums='$diffNums',userId='$userId',batchNum='$batchNum',insertTime='$now'";
						$affectRowAdd = WhRecManageModel :: addTNameRow($tName, $set);
						if (!$affectRowAdd) { //未插入成功报错
							self :: $errCode = 0501;
							self :: $errMsg = 'affectRowAdd error';
							throw new Exception('affectRowAdd error');
						}
						//同时更新匹配记录的字段，arrivedNums和reStatus
						$tName = 'wh_receipt_management';
						$set = "SET arrivedNums=arrivedNums+'$diffNums',reStatus=1 ";
						$where = "WHERE id='{$value['id']}'";
						$affectRowUp = WhRecManageModel :: updateTNameRow($tName, $set, $where);
						if (!$affectRowUp) { //未插入成功报错
							self :: $errCode = 0501;
							self :: $errMsg = 'affectRowUp error';
							throw new Exception('affectRowUp error');
						}
						$nums = $nums - $diffNums; //减去本次插入的数量，下次循环继续比对
					}
				}

			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			if ($flag == 1) { //不需要采购未订单
				self :: $errCode = 200;
				self :: $errMsg = 'success';
				return 1;
			} else { //需要采购未订单
				self :: $errCode = 222;
				self :: $errMsg = 'success';
				$resupplyOrder = array (); //采购未订单数组
				$resupplyOrder['sku'] = $sku;
				$resupplyOrder['nums'] = $nums;
				return $resupplyOrder;
			}

		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			TransactionBaseModel :: autoCommit();
			return 0;
		}

	}
}
?>
