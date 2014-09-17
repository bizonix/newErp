<?php
/*
 * 快递描述action
 * ADD BY Herman.Xi 2013.12.23
 */
class ExpressRemarkAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_getExpressRemarkList($orderid) { //
		global $memc_obj; //调用memcache获取sku信息
		//$addUser = $_SESSION['sysUserId'];
		
		$remarkList = ExpressRemarkModel :: getExpressRemarkList($orderid);
		self :: $errCode = ExpressRemarkModel :: $errCode;
		self :: $errMsg = ExpressRemarkModel :: $errMsg;
		return $remarkList;
	}
	
	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_getExpressRemarkListAPI() { //
		global $memc_obj; //调用memcache获取sku信息
		
		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : '';
		$remarkList = $this->act_getExpressRemarkList($orderid);
		return $remarkList;
	}
	
	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_addExpressRemark($omOrderId,$post) {
		global $memc_obj; //调用memcache获取sku信息
		$addUser = $_SESSION['sysUserId'];
		$data = array();
		//var_dump($post); exit;
		$action = $post['action'];
		
		switch($action){
			case 'adddhl':
				if(isset($post['price2'])){
					foreach($post['price2'] as $key => $priceValue){
						$data[$key]['omOrderId'] = $omOrderId;
						$data[$key]['price'] = trim(round($post['price2'][$key],2));
						$data[$key]['amount'] = trim($post['amount2'][$key]);
						$data[$key]['description'] = trim(mysql_real_escape_string($post['description2'][$key]));
						$data[$key]['creatorId'] = $addUser;
						$data[$key]['createdTime'] = time();
					}
				}
				break;
			case 'addfedex':
				if(isset($post['price'])){
					//var_dump($post);
					foreach ($post['description'] as $key => $tempDesc) {
						$ret = preg_match('/^(.|\n|\r)*\((.|\n|\r){3,}\)(.|\n|\r)*$/', $tempDesc);				
						if (!$ret) {
							self :: $errCode = 005;
							self :: $errMsg  = " -[<font color='#FF0000'>操作记录: 数据保存失败,描述一栏必须包含\"(材质)\"字样! 且必须全为英文字符(包括标点符号)，<br>&nbsp;&nbsp;不要有回车或换行，建议先在记事本里编辑好再拷贝到此描述栏!</font>]";
							return false;
						}
					}
					foreach($post['price'] as $key => $priceValue){
						$branddescrips = trim(mysql_real_escape_string($post['branddescrip'][$key]));	
						if(empty($branddescrips)){
							$isBrand = 2;
							$descriptions = "[No Brand]".trim(mysql_real_escape_string($post['description'][$key]));
						}else{
							$isBrand = 1;
							$descriptions = "[".$branddescrips."]".trim(mysql_real_escape_string($post['description'][$key]));
						}
						$data[$key]['omOrderId'] = $omOrderId;
						$data[$key]['price'] = trim(round($post['price'][$key],2));
						$data[$key]['amount'] = trim($post['amount'][$key]);
						$data[$key]['hamcodes'] = trim($post['hamcodes'][$key]);
						$data[$key]['isBrand'] = $isBrand;
						//$data[$key]['branddescrip'] = $branddescrips;
						$data[$key]['description'] = $descriptions;
						$data[$key]['creatorId'] = $addUser;
						$data[$key]['createdTime'] = time();
					}
					//var_dump($data);
				}
				break;
			default:
				
		}
		$rtn = ExpressRemarkModel :: addExpressRemark($omOrderId,$data);
		self :: $errCode = ExpressRemarkModel :: $errCode;
		self :: $errMsg = ExpressRemarkModel :: $errMsg;
		return $rtn;
	}
	
}
?>
