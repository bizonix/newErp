<?php
/**
 * 类名：OrderWarehouseRecordView
 * 功能：订单仓库操作记录展示
 * 版本：2013-12-27
 * 作者：贺明华
 */
class OrderWarehouseRecordView extends BaseView {
	 /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }
	public function view_orderWarehouseRecordList(){
		$this->smarty->assign('toptitle', '订单仓库操作记录查询');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '41');
		if(isset($_POST)&&$_POST['action']=="scanRecord"){
			$orderid = isset($_POST['omOrderId'])?$_POST['omOrderId']:"";
			$where = "where omOrderId={$orderid}";
			$orderDetail = OmAvailableModel::getTNameList("om_unshipped_order_detail","*",$where);
			$scanRecord = array();
			$scanRecords = array();
			foreach($orderDetail as $key=>$value){
				$method = "wh.getOrderSkuPickingRecords";
				$dataArr['orderId'] = $orderid;
				$dataArr['sku'] = $value['sku'];
				$data = OmAvailableModel::callOpenSystemByMethod($method,$dataArr);
				$data = json_decode($data,true);
				//echo "<pre>";print_r($data);
				$scanRecord['omOrderId'] = $orderid;
				$scanRecord['sku'] = $value['sku'];
				$scanRecord['numyes'] = $data['amount'];
				$scanRecord['numno'] = $data['totalNums']-$data['amount'];

				$scanRecord['operatorId'] = $data['scanUserId'];
				$scanRecord['createdTime'] = $data['scanTime'];

				$scanRecords[] = $scanRecord;
			}
			//print_r($scanRecords);
			$this->smarty->assign("RecordArr",$scanRecords);
			
			$action = isset($_POST['action'])?$_POST['action']:"";
			
			$this->smarty->assign("action",$action);
			
		}
		if(!empty($_POST)&&$_POST['action']!="scanRecord"){
			//print_r($_POST);
			$orderid = isset($_POST['omOrderId'])?$_POST['omOrderId']:"";
			$where = "where omOrderId={$orderid}";
			$orderRecord = OmAvailableModel::getTNameList("om_unshipped_order_warehouse","*",$where);
			$record = array();
			$records = array();
			
			foreach($orderRecord as $key=>$value){
				if($_POST['action']=="reviewRecord"){
					$operatorId = $value['reviewerId'];
					$createdTime = $value['reviewTime'];
				}
				if($_POST['action']=="packageRecord"){
					$operatorId = $value['packersId'];
					$createdTime = $value['packingTime'];
				}
				if($_POST['action']=="weighRecord"){
					$operatorId = $value['weighStaffId'];
					$createdTime = $value['weighTime'];
				}
				if($_POST['action']=="partionRecord"){
					$operatorId = $value['districtStaffId'];
					$createdTime = $value['districtTime'];
				}
				$record['omOrderId'] = $orderid;
				$record['operatorId'] = $operatorId;
				$record['createdTime'] = $createdTime;
				$records[] = $record;
			}
			
			$action = isset($_POST['action'])?$_POST['action']:"";
			$this->smarty->assign("action",$action);
			$this->smarty->assign("RecordArr",$records);
		}
		$this->smarty->display("orderWarehouseRecord.htm");
	} 
}