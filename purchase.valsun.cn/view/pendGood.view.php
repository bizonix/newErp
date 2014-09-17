<?php
/**
 * 类名： PendGoodView
 * 功能： 待定列表模板输出
 * 版本： 1.0
 * 日期： 2013/08/11
 * 作者： 王民伟
 */
class PendGoodView extends BaseView{	
	public function view_index(){
	 	global $mod,$act;
		session_start(); 
		$this->smarty->assign('title','待定列表');
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('web_api',WEB_API);//API 接口地址 
		
		$timetype	= isset($_GET['timetype']) ? $_GET['timetype'] : '';
		$starttime	= isset($_GET['startTime']) ? $_GET['startTime'] : '';//,'1354294861');
		$endtime	= isset($_GET['endTime']) ? $_GET['endTime'] : '';//,'1375290061');
		$sku 		= isset($_GET['sku']) ? $_GET['sku'] : '';
		$purid 		= isset($_GET['purid']) ? $_GET['purid'] : '1';
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$page       = isset($_GET['page']) ? $_GET['page'] : '1';
		//可见权限
		$res = commonAct::actGetPurchaseAccess();
		$pids = $res['power_ids'];
		if(!empty($pids)){
			$condition 	= " WHERE pendingStatus!=5 AND purchaseId in ({$pids}) ";
		}else{
			$condition 	= " WHERE pendingStatus!=5 AND purchaseId = {$_SESSION['sysUserId']} ";
		}
		if (!empty($sku)){
			$condition  .= " AND sku = '{$sku}'";
		}
		if ($status !== ''){
			$condition  .= " AND pendingStatus = '{$status}'";
		}
		if($timetype!=0){
			if (!empty($starttime) && $endtime >= $starttime){
				$serstart = strpos($starttime, ':')!==false ? strtotime($starttime) : strtotime($starttime." 00:00:00");
				$serend   = strpos($endtime, ':')!==false ? strtotime($endtime) : strtotime($endtime." 23:59:59");
				if($timetype == '1'){
					$condition  .= " AND startTime BETWEEN "."'{$serstart}'"." AND "."'{$serend}'";
				}else if($timetype == '2'){
					$condition  .= " AND lastModified BETWEEN "."'{$serstart}'"." AND "."'{$serend}'";
				}
			}
		}

		$qc = new RtnQcDataAct();
		$rtndata = $qc->act_QcData($purid, $condition, $page, 'pendgood');
		//var_dump($rtndata); exit;
		$data = $rtndata['data'];
		//echo "<pre>"; var_dump($data); exit;
		if($data[1]){
			//$data = $data[1];
			//获取采购id
			//var_dump($res); exit;
			/*$purid = $res['power_ids'];
			//转换成sku
			$pur_sku = array();
			if(!empty($purid)){
				$pur_sku = ApiModel::getSkuByPurids($purid);
			}
			$pur_sku_arr = array();
			foreach($pur_sku as $pur_sku_val){
				$pur_sku_arr[] = $pur_sku_val['sku'];
			}
			if(!empty($data) && !empty($pur_sku_arr)){
				foreach($data as $key=>$val){ 
					if(!in_array($val['sku'],$pur_sku_arr)){
						unset($data[$key]);	//去除不是自己的sku
						continue;
					}
				}
			}
			$perNum = 100;
			/*$pageArr = pageForArr($data, $perNum);
			$pageStr = $pageArr[1];*/
			$list = $data[1];
		}else{
			$pageStr = '暂无数据';
		}
		$this->smarty->assign('pageStr', $pageStr);//分页输出 
		$this->smarty->assign('ser_sku', $sku);//选中SKU
		$this->smarty->assign('ser_timetype', $timetype);//选中时间类型
		$this->smarty->assign('ser_startTime', $starttime);//选中开始时间 
		$this->smarty->assign('ser_endTime', $endtime);//选中结束时间
		$this->smarty->assign('ser_status', $status);//选中状态
		$this->smarty->assign('userid', $_SESSION['userId']);//登录用户userid
		$this->smarty->assign('list', $list);//循环赋值   
		$this->smarty->display('pendGood.htm');
		
	}
	
}
?>
