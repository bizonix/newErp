<?php
	/**
	*插入扫描SKU、订单编号、平邮、挂号等条码信息到pda_scan_temporaryrecord表中，最后导出并清空导出数据。
	*ADD BY 陈伟 2013.5.10
	**/
class pda_scanRecordAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	public function act_pda_scanRecord(){
		$data_id    = $_POST['data_id'];//SKU条码转换 add by chenwei 2013.7.6
		if(empty($data_id)){
			$data_id = $_POST['data_id'];
		}

		$scan_user  = $_POST['scan_user'];
		//$sku = isset($data_id) ? str_pad(trim($data_id), 3, '0', STR_PAD_LEFT) : '';	
		$res=array();


		//插入PDA扫描临时数据表
		$insert_sql = "INSERT INTO pda_scan_temporaryrecord(`scan_id`,`insert_user`,`scantime`) values('$data_id','{$scan_user}','$mctime')";				
		if($dbcon->execute($insert_sql)){
			$res['res_code']='200';
			$res['res_msg']="该信息[{$data_id}]扫描成功，已插入！";
			echo json_encode($res);exit;
		}else{
			$res['res_code']='001';
			$res['res_msg'] ="该信息[{$data_id}]扫描失败！请重试！";
			echo json_encode($res);exit;
		}
	}
}	
?>