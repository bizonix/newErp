<?php


/*
 * 开放api接口查询
 */

class OpenapiAct {
	public static $errCode = 000;
	public static $errMsg = '未初始化';

	/*
	 * 取得指定的表的列表
	 */
	public function act_getTNameProductsList() {
		$tName = isset ($_GET['tName']) ? post_check($_GET['tName']) : '';
		$select = isset ($_GET['select']) ? post_check($_GET['select']) : '';
		$where = isset ($_GET['where']) ? post_check($_GET['where']) : '';
		$where = base64_decode($where);
		if (empty ($select) || empty ($tName)) { //参数不完整
			self :: $errCode = 301;
			self :: $errMsg = 'select or tName is empty';
			return false;
		} else {
			$data = OpenapiModel :: getTNameProductsList($tName, $select, $where);
			if ($data) {
				self :: $errCode = 200;
				self :: $errMsg = 'Success';
				return $data;
			} else {
				self :: $errCode = OpenapiModel :: $errCode;
				self :: $errMsg = OpenapiModel :: $errMsg;
			}
		}
	}

	/*
	 * 添加指定记录
	 */
	public function act_addTNameProducts() {
		$tName = isset ($_GET['tName']) ? post_check($_GET['tName']) : '';
		$set = isset ($_GET['set']) ? post_check($_GET['set']) : '';
		if (empty ($tName) || empty ($set)) { //参数不完整
			self :: $errCode = 301;
			self :: $errMsg = 'tName or set is empty';
			return false;
		} else {
			$data = OpenapiModel :: addTNameProducts($tName, $set);
			if ($data) {
				self :: $errCode = 200;
				self :: $errMsg = 'Success';
				return $data;
			} else {
				self :: $errCode = OpenapiModel :: $errCode;
				self :: $errMsg = OpenapiModel :: $errMsg;
			}
		}
	}

	/*
	* 添加指定表记录
	*/
	public function act_updateTNameProducts() {
		$tName = isset ($_GET['tName']) ? post_check($_GET['tName']) : '';
		$set = isset ($_GET['set']) ? post_check($_GET['set']) : '';
		$where = base64_decode($where);
		if (empty ($tName) || empty ($set)) { //参数不完整
			self :: $errCode = 301;
			self :: $errMsg = 'tName or set is empty';
			return false;
		} else {
			$data = OpenapiModel :: updateTNameProducts($tName, $set, $where);
			if ($data) {
				self :: $errCode = 200;
				self :: $errMsg = 'Success';
				return $data;
			} else {
				self :: $errCode = OpenapiModel :: $errCode;
				self :: $errMsg = OpenapiModel :: $errMsg;
			}
		}
	}

	//不良品存在后，要对其进行操作的接口（报废，内部处理及待退回）
	function act_afterAuditDefectiveProducts() { //QC对不良品列表中进行对报废和内部处理
		//$scrappedStatus表示处理方向，1为报废，2为内部处理，3为待退回
		try {
			$defectiveId = isset ($_GET['defectiveId']) ? post_check($_GET['defectiveId']) : '';
			$infoId = isset ($_GET['infoId']) ? post_check($_GET['infoId']) : '';
			$num = isset ($_GET['num']) ? post_check($_GET['num']) : '';
			$note = isset ($_GET['note']) ? post_check($_GET['note']) : '';
			$scrappedStatus = isset ($_GET['scrappedStatus']) ? post_check($_GET['scrappedStatus']) : '';
			if (empty ($defectiveId) || empty ($infoId) || empty ($num) || empty ($scrappedStatus)) {
				throw new Exception("empty fields");
			}
			TransactionBaseModel :: begin();
			$now = time();
			$select = 'startTime';
			$where = "WHERE id='$defectiveId'";
			$defectiveProductsList = DefectiveProductsModel :: getDefectiveProductsList($select, $where); //修改记录前看是否是第一次插入
			$set = "SET processedNum=processedNum+'$num' ";
			if (empty ($defectiveProductsList[0]['startTime'])) { //如果是第一次插入则加入首次处理时间
				$set .= ",startTime='$now' ";
			}
			DefectiveProductsModel :: updateDefectiveProducts($set, $where); //先将该不良品记录的相关字段修改

			$select = 'sku,defectiveNum,processedNum';
			$defectiveProductsList = DefectiveProductsModel :: getDefectiveProductsList($select, $where);

// 			$spu = $defectiveProductsList[0]['spu'];
			$sku = $defectiveProductsList[0]['sku'];
			$defectiveNum = $defectiveProductsList[0]['defectiveNum']; //不良品记录的总数量
			$processedNum = $defectiveProductsList[0]['processedNum']; //已处理数量

			if ($scrappedStatus == 1 || $scrappedStatus == 2) {
				$set = "SET infoId='$infoId',sku='$sku',scrappedNum='$num',processTypeId='$scrappedStatus',note='$note' ";
				ScrappedProductsModel :: addScrappedProducts($set); //在报废，内部处理表中添加记录；
			}
			elseif ($scrappedStatus == 3) {
				$set = "SET infoId='$infoId',sku='$sku',returnNum='$num',note='$note' ";
				ReturnProductsModel :: addReturnProducts($set); //在退回表中表中添加记录；
			} else {
				throw new Exception("error status");
			}

			if ($defectiveNum == $processedNum) { //检测该不良品记录是否处理完成
				$set = "SET defectiveStatus='2',lastModified='$now' ";
				$where = "WHERE id='$defectiveId'";
				DefectiveProductsModel :: updateDefectiveProducts($set, $where); //先将该不良品记录的相关字段修改
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			self :: $errCode = 200;
			self :: $errMsg = 'Success';
			return 1;
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			self :: $errCode = 301;
			self :: $errMsg = $e->getMessage();
			return 0;
		}
	}

	//对待定商品记录进行修改图片，正常回测，待退回
	function act_operatePendingProducts() { //QC对待定列表中进行修改图片，正常回测，待退回（全部，不分部分）
		//$pendingId表示待定表中的记录ID,$status表示修改图片，正常回测或待退回
		try {
			$cTime1 = time() + microtime();
			$pendingId = isset ($_GET['pendingId']) ? post_check($_GET['pendingId']) : '';
			$status = isset ($_GET['status']) ? post_check($_GET['status']) : '';
			$tracktime = isset ($_GET['tracktime']) ? post_check($_GET['tracktime']) : '';
			//echo $pendingId.'   '.$status;
			$select = '*';
			$where = "WHERE id='$pendingId'";
			$pendingProductsList = PendingProductsModel :: getPendingProductsList($select, $where);
			if (empty ($pendingProductsList)) {
				throw new Exception("error");
			}
			TransactionBaseModel :: begin();
			$selectStatus = $pendingProductsList[0]['pendingStatus'];
			if ($selectStatus != 0 && $selectStatus != 2) { //如果该条待定记录中的状态不是0或2（待处理或者是图片修改完成时，报错）
				throw new Exception("error");
			}
			$startTime = $pendingProductsList[0]['startTime'];
			$pendingNum = $pendingProductsList[0]['pendingNum'];
			$pendingId = $pendingProductsList[0]['id'];
			$infoId = $pendingProductsList[0]['infoId'];
// 			$spu = $pendingProductsList[0]['spu'];
			$sku = $pendingProductsList[0]['sku'];
			$now = time();
			if ($status == 1) { //修改图片
				$set = "SET pendingStatus='1',processedNum='$pendingNum',startTime='$now' "; //状态改为需要修改图片
				$where = "WHERE id='$pendingId'";
				PendingProductsModel :: updatePendingProducts($set, $where); //先将该待定记录的相关字段修改
				//这里还要调用API在产品中心待修改列表中添加一条记录//
				//先略过，到时处理
			}
			elseif ($status == 2) { //正常回测
				$set = "SET pendingStatus='3',processedNum='$pendingNum',lastModified='$now' "; //状态改为已处理pendingStatus='3'
				if (empty ($startTime)) { //如果不是从修改完图片进行回测的话(即开始处理时间为空)，要加上开始处理时间
					$set .= ",startTime='$now' ";
				}
				$where = "WHERE id='$pendingId'";
				//echo $set;
				PendingProductsModel :: updatePendingProducts($set, $where); //先将该待定记录的相关字段修改
				//这里还要在wh_sample_info中将$infoId这条记录的detectStatus状态改为1（待检测），pid=$pendingId
				$select = 'id';
				$where = "WHERE id='$infoId'";
				$whInfoList = PendingProductsModel :: getWhInfo($select, "WHERE id='$infoId'"); //取出wh_info表中关联pendingId的记录
				if (empty ($whInfoList)) { //如果该infoid在whInfo中找不到记录的话
					throw new Exception("error infoId");
				}
				$set = "set detectStatus='1',pid='$pendingId' ";
				$affectRow = PendingProductsModel :: updateWhInfo($set, $where); //更新原来的wh_info表中的该记录
				if ($affectRow != 1) { //如果更新记录数不是唯一的，也报错
					throw new Exception("update error");
				}
			}
			elseif ($status == 3) { //待退回
				$set = "SET pendingStatus='3',processedNum='$pendingNum',startTime='$now',lastModified='$now' ";
				$where = "WHERE id='$pendingId'";
				PendingProductsModel :: updatePendingProducts($set, $where); //先将该待定记录的相关字段修改
				$note = '待定移至待退回';
				$set = "SET infoId='$infoId',sku='$sku',note='$note',returnNum='$pendingNum'";
				ReturnProductsModel :: addReturnProducts($set); //在退回表中表中添加记录；
			} else {
				throw new Exception("error status");
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			$cTime2 = time() + microtime();
			self :: $errCode = $cTime2.'    '.$cTime1;
			self :: $errMsg = 'Success';
			return 1;

		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			$cTime2 = time() + microtime();
			self :: $errCode = $cTime2.'    '.$cTime1;
			self :: $errMsg = $e->getMessage();
			return 0;
		}
	}

	//待退回审核
	public function act_auditReturnProducts() {
		$flag = 1; //标识是否操作成功
		$returnId = isset ($_GET['returnId']) ? post_check($_GET['returnId']) : '';
		if (empty ($returnId)) { //为空时，跳转到列表页面，输出错误信息
			$flag = 0;
		}
		$now = time();
		$set = "SET returnStatus='1',auditTime='$now' ";
		$where = "WHERE id='$returnId' ";
		$affectRow = ReturnProductsModel :: updateReturnProducts($set, $where);
		if (!$affectRow) {
			$flag = 0;
		}
		if ($flag) {
			self :: $errCode = 200;
			self :: $errMsg = 'Success';
			return 1;
		} else {
			self :: $errCode = 301;
			self :: $errMsg = 'error';
			return 0;
		}

	}
	public function  act_adjustPrintNum(){
		$printBatch = isset($_POST['printBatch'])?$_POST['printBatch']:"";
		$num = isset($_POST['num'])?$_POST['num']:"";
		if($printBatch==""){
			self :: $errCode = 101;
			self :: $errMsg = '批次号不能为空！';
			return false;
		}
		if(!is_numeric($num)||$num){
			self :: $errCode = 101;
			self :: $errMsg = '参数num异常！';
			return false;
		}
		$where = "where printBatch='{$printBatch}'";
		$record = OmAvailableModel::getTNameList("qc_sample_info","*",$where);
		if($record[0]['detectStatus']==3){
			$set = "set printNum=printNum+{$num},ichibanNum=ichibanNum+{$num}";
		}
		$set = "set printNum=printNum+{$num}";
		$info = OmAvailableModel::updateTNameRow("qc_sample_info",$set,$where);
		if(!$info){
			self :: $errCode = 101;
			self :: $errMsg = '修改失败！';
			return false;
		}else{
			self :: $errCode = 200;
			self :: $errMsg = '修改成功！';
			return true;
		}
	}

}