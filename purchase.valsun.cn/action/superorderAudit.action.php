<?php
/**
 * 类名: SuperorderAuditAct
 * 功能：超大订单审核处理类
 * 版本：1.0
 * 日期：2014-09-02
 * 作者：杨世辉
 */
class SuperorderAuditAct {

	public static $errCode	= 0;
	public static $errMsg	= "";


	/**
	 * 获取列表数据
	 */
	public function getList() {
		global $dbConn;
		$where = " a.is_delete=0 ";
		//关键词搜索
		$type = isset($_GET['type']) ? $_GET['type'] : NULL;
		$keyWord = isset($_GET['keyWord']) ? trim($_GET['keyWord']) : NULL;
		if ((!is_null($type) && $type != -1) && $keyWord != '') {
			$fieldarr = array('orderid'=>'omOrderId','sku'=>'sku','note'=>'auditNote');
			if ($type == 'note') {
				$where .= " AND a.{$fieldarr[$type]} like '%{$keyWord}%'";
			} else {
				$where .= " AND a.{$fieldarr[$type]}='{$keyWord}'";
			}
		}

		//审核状态
		$auditStatus = isset($_GET['auditStatus']) ? $_GET['auditStatus'] : NULL;
		if (!is_null($auditStatus) && $auditStatus != -1) {
			$where .= ' AND a.status=\''. $auditStatus .'\'';
		}

		//时间
		$timeType = isset($_GET['timeType']) ? $_GET['timeType'] : NULL;
		$startTime = isset($_GET['startTime']) ? $_GET['startTime'] : NULL;
		$endTime = isset($_GET['endTime']) ? $_GET['endTime'] : NULL;
		if ((!is_null($timeType) && $timeType != -1) && $startTime !='' && $endTime != '') {
			$fieldarr = array('addtime'=>'addTime','audittime'=>'auditTime');
			$startTime = strtotime($startTime);
			$endTime = strtotime($endTime.' 23:59:59');
			$where .= " AND a.{$fieldarr[$timeType]} BETWEEN '{$startTime}' AND '{$endTime}' ";
		}

		$page = isset($_GET['page']) ? $_GET['page'] : 0;
		if($page > 0){
			$page = ($page-1) * 100;
		}
		$limit = " limit {$page},100";

		$sqlStr = 'SELECT a.*,b.goodsName,b.goodsCost,c.everyday_sale,c.salensend,(c.stock_qty+c.ow_stock) as real_stock,c.newBookNum '
				. 'FROM ph_superorder_audit a '
				. 'LEFT JOIN pc_goods b ON a.sku=b.sku '
				. 'LEFT JOIN ph_sku_statistics c ON a.sku=c.sku '
				. "WHERE {$where} "
				. 'ORDER BY a.addtime DESC ';

		$sql = $dbConn->execute($sqlStr);
		$totalNum = $dbConn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbConn->execute($sql);
		$orderInfo = $dbConn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"listData"=>$orderInfo);
		return $data;
	}

	/**
	 * 保存审核结果
	 */
	public function saveAuditResult() {
		$id 	= $_POST['id'];
		$status = $_POST['status'];
		$note 	= $_POST['note'];
		if (empty($id) || empty($status)) {
			$arr = array('code'=>'2', 'msg'=>'参数有误');
			return json_encode($arr);
		}
		$where 	= "id='{$id}'";
		$row 	= SuperorderAuditModel::getOne('id,status', $where);
		if (empty($row) || $row['status'] ==1) {
			$arr = array('code'=>'3', 'msg'=>'已经审核');
			return json_encode($arr);
		}
		$data 					= array();
		$data['status'] 		= $status;
		$data['auditUserId'] 	= $_SESSION['sysUserId'];
		$data['auditNote']		= $note;
		$data['auditTime'] 		= time();
		$isOk = SuperorderAuditModel::update($data, $where);
		if ($isOk) {
			$arr = array('code'=>'1', 'msg'=>'success');
		} else {
			$arr = array('code'=>'2', 'msg'=>'更新出错');
		}
		return json_encode($arr);
	}

}