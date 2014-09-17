<?php
/**
 * 类名： BadGoodView
 * 功能： 不良品模板输出
 * 版本： 1.0
 * 日期： 2013/08/11
 * 作者： 王民伟
 */
include_once WEB_PATH.'lib/page.php';
include_once WEB_PATH.'action/rtnQcData.action.php';
class BadGoodView extends BaseView{	
	//不良品列表显示
	public function view_index(){
	 	global $mod,$act;
		session_start(); 
        $this->smarty->assign('title','不良品列表');
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('web_api',WEB_API);//API 接口地址
		$timetype	= isset($_GET['timetype']) ? $_GET['timetype'] : '';
		$starttime	= isset($_GET['startTime']) ? $_GET['startTime'] : '';//,'1354294861');
		$endtime	= isset($_GET['endTime']) ? $_GET['endTime'] : '';//,'1375290061');
		$sku 		= isset($_GET['sku']) ? $_GET['sku'] : '';
		$purid 		= isset($_GET['purid']) ? $_GET['purid'] : '1';
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$page       = isset($_GET['page']) ? $_GET['page'] : '1';
		
		$condition 	= '';
		if (!empty($sku)){
			$condition  .= " AND sku = '{$sku}'";
		}
		if ($status !== ''){
			$condition  .= " AND defectiveStatus = '{$status}'";
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
		if(empty($condition)){
			$condition = ' AND 1 = 1';
		}
        $qc = new ApiAct();
        $data = $qc->act_getBadGoodsList();
        $data = json_decode($data, true);
        $data = json_decode($data['data'],true);
        $list = '';
        $pageStr = '暂无数据';
        if($data){
// 		$totalrow = $data['total'];
	        $data = $data['data'];
	        $spu = '';
	        $good = new GoodsAct();
        	//获取采购id
			$res = commonAct::actGetPurchaseAccess();
			$purid = $res['power_ids'];
			//转换成sku 
			$pur_sku = ApiModel::getSkuByPurids($purid);	
			$pur_sku_arr = array();
			foreach($pur_sku as $pur_sku_val){
				$pur_sku_arr[] = $pur_sku_val['sku'];
			}
	        if(!empty($data)){
	        	foreach($data as $key=>$val){ //补充没有的信息
	        		if(!in_array($val['sku'],$pur_sku_arr)){
	        				unset($data[$key]);	//去除不是自己的sku
	        				continue;
	        		}
	        		$spu = $good->getSpuBySku($val['sku']);
	        		$data[$key]['spu'] = $spu;
	        	}
	        }
	       if(!empty($data)){ 
		       	$perNum = 100;
		       	$pageArr = pageForArr($data, $perNum);
		       	$pageStr = $pageArr[1];
		       	$list = $pageArr[0];
	       }
	 
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
		$this->smarty->display('badGood.htm');
		
	}
	
	//处理不良品页面
	public function view_handleGood(){
		global $mod,$act;
		$condition = " WHERE 1 ";
		$type		= isset($_GET['type']) ? $_GET['type'] : '';
		$id			= isset($_GET['id']) ? $_GET['id'] : '';
		
		$qc 		= new RtnQcDataAct();
		$condition  .= "AND id = {$id}";
		$rtndata    = $qc->act_QcData('1', $condition, '1', 'badgood');
		$data 		= $rtndata['data'][1];
		$data[0]['spu'] = GoodsAct::getSpuBySku($data[0]['sku']);
		if($type=='scrapped'){
			$title = '不良品报废处理页面';
			$sign  = '报废处理';
			$category = 1;
		}else if($type=='return'){
			$title = '不良品退货处理页面';
			$sign  = '退货处理';
			$category = 3;
		}else if($type=='interhandle'){
			$title = '不良品内部处理页面';
			$sign  = '内部处理';
			$category = 2;
		}
		$this->smarty->assign('title',$title);
		$this->smarty->assign('sign',$sign);
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('web_api',WEB_API);//API 接口地址
		
		$hasNum = $data[0]['defectiveNum'] - $data[0]['processedNum'];
		$this->smarty->assign('numid', $id);
		$this->smarty->assign('data', $data[0]);
		$this->smarty->assign('infoId', $data[0]['infoId']);
		$this->smarty->assign('spu', $data[0]['spu']);
		$this->smarty->assign('sku', $data[0]['sku']);
		$this->smarty->assign('defectiveNum', $data[0]['defectiveNum']); 
		$this->smarty->assign('processedNum', $data[0]['processedNum']);
		$this->smarty->assign('category', $category);
		$this->smarty->assign('hasNum', $hasNum);
		$this->smarty->display('handleBadGood.htm');
	}
}
?>