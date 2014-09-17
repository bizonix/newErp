<?php
class PendingProductsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getPendingProductsList($select, $where) {
		$list = PendingProductsModel :: getPendingProductsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = PendingProductsModel :: $errCode;
			self :: $errMsg = PendingProductsModel :: $errMsg;
			return false;
		}
	}

	//修改图片，正常回测，待退回
	function act_updatePendingProducts($pendingProductsList, $status) { //QC对待定列表中进行修改图片，正常回测，待退回（全部，不分部分）
		//$pendingProductsList表示待定表中的指定记录
		try {
			TransactionBaseModel :: begin();
			$selectStatus = $pendingProductsList[0]['pendingStatus'];
			if ($selectStatus != 0 && $selectStatus != 2) { //如果该条待定记录中的状态不是0或2（待处理或者是图片修改完成时，报错）
				throw new Exception("error");
			}
			$startTime = $pendingProductsList[0]['startTime'];
			$pendingNum = $pendingProductsList[0]['pendingNum'];
			$pendingId = $pendingProductsList[0]['id'];
			$infoId = $pendingProductsList[0]['infoId'];
			$spu = $pendingProductsList[0]['spu'];
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
				$set = "SET infoId='$infoId',spu='$spu',sku='$sku',note='$note',returnNum='$pendingNum'";
				ReturnProductsModel :: addReturnProducts($set); //在退回表中表中添加记录；
			} else {
				throw new Exception("error status");
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			return 1;

		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			self :: $errCode = '0000';
			self :: $errMsg = $e;
			return 0;
		}
	}
	
	//修改图片，正常回测，待退回
	function act_apiUpdatePendingProducts($pendingId,$infoId,$pendingNum,$userId,$status) { //QC对待定列表中进行修改图片，正常回测，待退回（全部，不分部分）
		//$pendingProductsList表示待定表中的指定记录
		try {
			TransactionBaseModel :: begin();
			$now = time();
			if ($status == 2) { //修改图片
				$set = "SET pendingStatus='2',auditId=$userId,processedNum='$pendingNum',lastModified='$now' "; //状态改为需要修改图片
				$where = "WHERE id='$pendingId'";
				PendingProductsModel :: updatePendingProducts($set, $where); //先将该待定记录的相关字段修改
				//这里还要调用API在产品中心待修改列表中添加一条记录//
				//先略过，到时处理
			}else if ($status == 5) { //正常回测
				$set = "SET pendingStatus='5',auditId=$userId,processedNum='$pendingNum',lastModified='$now' "; //状态改为已处理pendingStatus='3'
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
			}else if ($status == 4) { //待退回
				$set = "SET pendingStatus='4',auditId=$userId,processedNum='$pendingNum',startTime='$now',lastModified='$now' ";
				$where = "WHERE id='$pendingId'";
				PendingProductsModel :: updatePendingProducts($set, $where); //先将该待定记录的相关字段修改
                $note = '待定移至待退回';
				$set = "SET infoId='$infoId',spu='$spu',sku='$sku',auditId=$userId,note='$note',returnNum='$pendingNum'";
				ReturnProductsModel :: addReturnProducts($set); //在退回表中表中添加记录；
			} else {
				throw new Exception("error status");
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			return "申请成功";

		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			self :: $errCode = '0000';
			self :: $errMsg = $e;
			return "申请失败";
		}
	}

	function act_updatePendingProducts2($set, $where) { //采购对不良品列表中记录的审核
		$affectRow = PendingProductsModel :: updatePendingProducts($set, $where);
		if ($affectRow) {
			return $affectRow;
		} else {
			self :: $errCode = PendingProductsModel :: $errCode;
			self :: $errMsg = PendingProductsModel :: $errMsg;
			return false;
		}
	}

	function act_getPendingProductsCount($where) { //根据条件，取得记录总数
		$list = PendingProductsModel :: getPendingProductsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = PendingProductsModel :: $errCode;
			self :: $errMsg = PendingProductsModel :: $errMsg;
			return false;
		}
	}

	function act_addPendingProducts($set) {
		$list = PendingProductsModel :: updatePendingProducts($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = PendingProductsModel :: $errCode;
			self :: $errMsg = PendingProductsModel :: $errMsg;
			return false;
		}
	}
}
?>