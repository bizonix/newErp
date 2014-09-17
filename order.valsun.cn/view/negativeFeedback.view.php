<?php
/**
 * 类名：negativeFeedbackView
 * 功能：B2B中差评
 * 版本：2013-01-08
 * 作者：贺明华
 */
class negativeFeedbackView extends BaseView {
	 /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }
	public function view_index(){
		/*$OmAccountAct = new OmAccountAct();

    	$ebayAccountList = $OmAccountAct->act_getEbayAccountList();
		print_r($ebayAccountList);*/
		//print_r($_SESSION);
		$orderid = isset($_GET['orderid'])?$_GET['orderid']:"";
		if(isset($_POST['orderid'])){
			$orderid = $_POST['orderid'];
			$where = " where omOrderId ={$orderid}"; 
			$details = OmAvailableModel::getTNameList("om_unshipped_order_detail","*",$where);
			$order = OmAvailableModel::getTNameList("om_unshipped_order","*","where id={$orderid}");
			$accountId = $order[0]['accountId'];
			$message = "";
			foreach($details as $detail){
				$sql_arr = array();
				
				$sql_arr['omOrderId'] = $orderid;
				$sql_arr['sku']		  = $detail['sku'];
				$sql_arr['amount'] 	  = $detail['amount'];
				$sql_arr['accountId'] = $accountId;
				$app = $detail['sku']."*app";
			
				$reason1 = $detail['sku']."*reason1";
				$reason2 = $detail['sku']."*reason2";
				$remark = $detail['sku']."*remark";
				$sql_arr['type'] 	  = $_POST[$app];
				$sql_arr['reason1']   = $_POST[$reason1];
				$sql_arr['reason2']   = $_POST[$reason2];
				$sql_arr['remark']    = $_POST[$remark];
				$sql_arr['userId'] 	  = $_SESSION['sysUserId'];
				$sql_arr['createdTime']= time();
				if($sql_arr['type']==""&&$sql_arr['reason1']==""&&$sql_arr['reason2']==""&&$sql_arr['remark']==""){
					
					continue;
				}elseif($sql_arr['type']!=""&&($sql_arr['reason1']!=""||$sql_arr['reason2']!="")){
					$set = array2sql($sql_arr);
					$where = " where omOrderId={$orderid} and sku='{$detail['sku']}'";
					$order_appraise = OmAvailableModel::getTNameList("om_order_detail_appraisal","*",$where);
					if($order_appraise){
						$msg = OmAvailableModel::updateTNameRow("om_order_detail_appraisal"," set ".$set,$where);
						if(!$msg){
							$message .= "<font color='red'>料号{$detail['sku']}数据保存失败！</font><br>";
						}else{
							$message .= "<font color='green'>料号{$detail['sku']}数据保存成功！</font><br>";
						}
					}else{
						$msg = OmAvailableModel::insertRow("om_order_detail_appraisal"," set ".$set);
						if(!$msg){
							$message .= "<font color='red'>料号{$detail['sku']}数据保存失败！</font><br>";
						}else{
							$message .= "<font color='green'>料号{$detail['sku']}数据保存成功！</font><br>";
						}
					}
				}else{
					$message .= "<font color='red'>请将料号{$detail['sku']}的数据填写完整！</font><br>";
				}
			}
			$this->smarty->assign("message",$message);
		}
		$where = " where omOrderId ={$orderid}"; 
		$details = OmAvailableModel::getTNameList("om_unshipped_order_detail","*",$where);
		$reasons = OmAvailableModel::getTNameList("om_order_refund_reason","*"," where typeId=3");
		$reason = array();
		foreach($reasons as $value){
			$reason[$value['id']] = $value['reason'];
		}
		$appraise = array();
		$i = 0;
		foreach($details as $detail){
			$where = " where omOrderId={$orderid} and sku='{$detail['sku']}'";
			$order_appraise = OmAvailableModel::getTNameList("om_order_detail_appraisal","*",$where);
			if($order_appraise){
				foreach($order_appraise as $key=>$value){
					$appraise[$i]['reason1'] = $value['reason1'];
					$appraise[$i]['reason2'] = $value['reason2'];
					$appraise[$i]['type']	 = $value['type'];
					$appraise[$i]['sku']	 = $detail['sku'];
					$appraise[$i]['remark']  = $value['remark'];
				}
			}else{
				$appraise[$i]['reason1'] = "";
				$appraise[$i]['reason2'] = "";
				$appraise[$i]['type']	 = "";
				$appraise[$i]['sku']	 = $detail['sku'];
				$appraise[$i]['remark']  = "";
			}
			$i++;
		}
		//print_r($appraise);
		$this->smarty->assign("skuinfoList",$appraise);
		$this->smarty->assign("reasonList",$reason);
		$this->smarty->assign("orderid",$orderid);
		
		$this->smarty->assign("ebayAccountList", $ebayAccountList);
		$this->smarty->assign('toptitle', '添加B2B中差评');	
		$this->smarty->display("negativeFeedback.htm");
	}
}