<?php
class DefectiveProductsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getDefectiveProductsList($select, $where) {
		$list = DefectiveProductsModel :: getDefectiveProductsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = DefectiveProductsModel :: $errCode;
			self :: $errMsg = DefectiveProductsModel :: $errMsg;
			return false;
		}
	}

	function act_updateDefectiveProducts($defectiveId, $infoId, $num, $note, $scrappedStatus) { //QC对不良品列表中进行对报废和内部处理
		//$scrappedStatus表示处理方向，1为报废，2为内部处理，3为待退回
		try {
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

			$select = 'spu,sku,defectiveNum,processedNum';
			$defectiveProductsList = DefectiveProductsModel :: getDefectiveProductsList($select, $where);

			$spu = $defectiveProductsList[0]['spu'];
			$sku = $defectiveProductsList[0]['sku'];
			$defectiveNum = $defectiveProductsList[0]['defectiveNum']; //不良品记录的总数量
			$processedNum = $defectiveProductsList[0]['processedNum']; //已处理数量

			if ($scrappedStatus == 1 || $scrappedStatus == 2) {
				$set = "SET infoId='$infoId',spu='$spu',sku='$sku',scrappedNum='$num',processTypeId='$scrappedStatus',note='$note' ";
				ScrappedProductsModel :: addScrappedProducts($set); //在报废，内部处理表中添加记录；
			}
			elseif ($scrappedStatus == 3) {
				$set = "SET infoId='$infoId',spu='$spu',sku='$sku',returnNum='$num',note='$note' ";
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
			return 1;
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			self :: $errCode = '0000';
			self :: $errMsg = $e;
			return 0;
		}
	}

	function act_updateDefectiveProducts2($set, $where) { //采购对不良品列表中记录的审核
		$affectRow = DefectiveProductsModel :: updateDefectiveProducts($set, $where);
		if ($affectRow) {
			return $affectRow;
		} else {
			self :: $errCode = DefectiveProductsModel :: $errCode;
			self :: $errMsg = DefectiveProductsModel :: $errMsg;
			return false;
		}
	}

	function act_getDefectiveProductsCount($where) { //根据条件，取得记录总数
		$list = DefectiveProductsModel :: getDefectiveProductsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = DefectiveProductsModel :: $errCode;
			self :: $errMsg = DefectiveProductsModel :: $errMsg;
			return false;
		}
	}

	function act_addDefectiveProducts($set) {
		$list = DefectiveProductsModel :: updateDefectiveProducts($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = DefectiveProductsModel :: $errCode;
			self :: $errMsg = DefectiveProductsModel :: $errMsg;
			return false;
		}
	}
}
?>