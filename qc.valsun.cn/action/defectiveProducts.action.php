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
	
	function act_apiGetDefectiveProductsList() {
		$page       = isset($_GET['page']) ? $_GET['page'] : '1';
		$timetype	= isset($_GET['timetype']) ? $_GET['timetype'] : '';
		$starttime	= isset($_GET['startTime']) ? $_GET['startTime'] : '';//,'1354294861');
		$endtime	= isset($_GET['endTime']) ? $_GET['endTime'] : '';//,'1375290061');
		$sku 		= isset($_GET['sku']) ? $_GET['sku'] : '';
		$purid 		= isset($_GET['purid']) ? $_GET['purid'] : '1';
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		
		/*$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}*/
       $select = '*';
	   $where = "";
	   $where_arr = array();
	   if(!empty($sku)){
		   $where_arr[] = "and sku = '{$sku}' "; 
	   }
	   if($timetype != '' && (empty($starttime) || empty($endtime))){
		   /*self :: $errCode = 153;
		   self :: $errMsg = '选择时间种类，需要完善时间！';
		   return false;*/
	   }
	   
	   if($starttime > $endtime){
		   self :: $errCode = 156;
		   self :: $errMsg = '开始时间大于结束时间！';
		   return false;
	   }
	   
	   if($timetype == 1){
		   $where_arr[] = "and startTime between '{$starttime}' and '{$endtime}' ";
	   }else if($timetype == 2){
		  $where_arr[] = "and lastModified between '{$starttime}' and '{$endtime}' ";
	   }
	   
	  if(!in_array($status,array(0,2))){
		   self :: $errCode = 157;
		   self :: $errMsg = '请传入正确的状态码！';
		   return false;
	   }
	  if($status != ''){
			$where_arr[] = "and defectiveStatus = '{$status}' ";
	   }
	   
	   if(!is_numeric($page)){
		   self :: $errCode = 158;
		   self :: $errMsg = 'page 需要传数字！';
		   return false;
	   }
	   if(!empty($where_arr)){
			$where .= "where 1 ".join(' ', $where_arr);
	   }
	    $listNum = DefectiveProductsModel :: getDefectiveProductsCount($where);
	   $where .= " order by id";
		$list = DefectiveProductsModel :: getDefectiveProductsList($select, $where);
		if ($list) {
			return json_encode(array('total'=>$listNum,'data'=>$list));
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

	function act_apiUpdateDefectiveProductsAudit($id, $type, $userId) {
		//采购对不良品列表中记录的审核接口
		/*
		1,退回
		2，报废
		3，内部处理
		*/
		if(empty($id)){
			self :: $errCode = "001";
			self :: $errMsg = "have not id";
			return false;	
		}else if(empty($type)){
			self :: $errCode = "002";
			self :: $errMsg = "have not type";
			return false;	
		}else if(empty($userId)){
			self :: $errCode = "002";
			self :: $errMsg = "have not userId";
			return false;	
		}
		$set = " SET defectiveStatus = 1,completeStatus = ".$type.",detectorId= ".$userId.", auditTime=".time().",lastModified=".time();
		$where = " where id = ".$id;
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