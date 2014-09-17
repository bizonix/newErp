<?php
/**
 * 类名: PurToOrderAPIAct
 * 功能：采购系统推送数据到订单系统交互业务逻辑类,推送数据到订单系统
 * 版本：1.0
 * 日期：2014-09-02
 * 作者：杨世辉
 */
class PurToOrderAPIAct {

	public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * 将审核超大订单的结果更新到订单系统
	 */
	public static function pushBigOrder(){
		$id 	= $_POST['id'];
		$status = $_POST['status'];
		$note 	= $_POST['note'];
		if (empty($id) || empty($status)) {
			$arr = array('code'=>'2', 'msg'=>'参数有误');
			return json_encode($arr);
		}
		$where 	= "id='{$id}'";
		$row 	= SuperorderAuditModel::getOne('*', $where);
		if (empty($row) || $row['status'] == 1) {
			$arr = array('code'=>'3', 'msg'=>'已经审核');
			return json_encode($arr);
		}
		$paramArr = array(
			/* API系统级输入参数 Start */
			'method' 	=> 'order.updateOrderAuditFromPh',  //API名称
			'format' 	=> 'json',  //返回格式
			'v' 		=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
		);
		/* API应用级输入参数 Start*/
		$paramArr['omOrderId'] 			= $row['omOrderId'];
		$paramArr['omOrderdetailId'] 	= $row['omOrderdetailId'];
		$paramArr['sku'] 				= $row['sku'];
		$paramArr['auditStatus'] 		= $status;
		$paramArr['auditUser'] 			= $_SESSION['sysUserId'];
		$paramArr['note'] 				= $note;
		/* API应用级输入参数 End*/

		$result = callOpenSystem($paramArr,'local');
		$result = json_decode($result, true);
		//add log
		//$filename = C("LOG_PATH").'/pushbigorder.txt';
		//write_log($filename, $result);
		if($result['data'] == true){
			$res['code'] = '1';
			$res['msg']  = 'success';
		}else{
			$res['code'] = '2';
			$res['msg']  = $result['errMsg'];
		}
		return json_encode($res);
	}


}