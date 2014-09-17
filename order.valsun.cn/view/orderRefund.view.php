<?php
/**
 * 订单信息查询
 * @author herman.xi
 */
class orderRefundView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 显示查询页面
     */
    public function view_orderRefundList() {
        
        global $memc_obj;
        $pagesize = 200;    //页面大小

		//菜单
		$status	=	'';
        //print_r($_GET);
		
		//搜索时使用的数据
		//order表
		$searchPlatformId			=	isset($_GET['platformId']) ? $_GET['platformId'] : '';				//搜索平台
		$searchRefundType		    =	isset($_GET['refundType']) ? $_GET['refundType'] : '';				//退款类型
		$searchRefundStatus	        =	isset($_GET['refundStatus']) ? $_GET['refundStatus'] : '';          //
		$searchOmOrderId			=	isset($_GET['omOrderId']) ? $_GET['omOrderId'] : '';				//订单编号
        $searchTransId				=	isset($_GET['transId']) ? $_GET['transId'] : '';					//交易ID        
        $searchApplyTime1		    =	isset($_GET['applyTime1']) ? $_GET['applyTime1'] : '';		       //初始时间
		$searchApplyTime2			=	isset($_GET['applyTime2']) ? $_GET['applyTime2'] : '';			   //结束时间
        $status                     =   isset($_GET['status']) ? $_GET['status'] : 0;                      //操作状态
		$orderType                  =   isset($_GET['orderType']) ? $_GET['orderType'] : 1;                 //单据种类
		$ostatus 					=   isset($_GET['ostatus']) ? $_GET['ostatus'] : 0;                      //一级分类

//echo "------------------$searchOmOrderId----------------";
        $where = ' where is_delete=0 ';
        if($searchPlatformId != '') {
            $where .= ' AND platformId = '.$searchPlatformId;
        }
        if($searchRefundType != '') {
            $where .= ' AND refundType = '.$searchRefundType;
        }
        if($searchTransId != '') {
            $where .= ' AND transId = '.$searchTransId;
        }
        if($searchOmOrderId != '') {
            $where .= ' AND omOrderId = '.$searchOmOrderId;
        }
        if($status != '') {
            $where .= ' AND status = '.$status;
        }
        if(($searchApplyTime1 != '') && ($searchApplyTime2 != '')) {
            $where .= ' AND addTime >= "'.strtotime($searchApplyTime1).'" AND addTime <= "'.strtotime($searchApplyTime2).'" ';
        }
		
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId IN ('.join(",", $platformsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}

		$this->smarty->assign('searchPlatformId', $searchPlatformId);
		$this->smarty->assign('searchRefundType', $searchRefundType);
		$this->smarty->assign('searchRefundStatus', $searchRefundStatus);
		$this->smarty->assign('searchOmOrderId', $searchOmOrderId);
		$this->smarty->assign('searchTransId', $searchTransId);
		$this->smarty->assign('searchApplyTime1', $searchApplyTime1);
		$this->smarty->assign('searchApplyTime2', $searchApplyTime2);

		$omAvailableAct = new OrderRefundAct();
		$OrderindexAct = new OrderindexAct();
		//平台信息
		$OmAccountAct = new OmAccountAct();
		$WarehouseAPIAct = new WarehouseAPIAct();
		
		$AbOrderList = $WarehouseAPIAct->act_getAbOrderList();
		//var_dump($AbOrderList); exit;
		$AbOrderListArr = array();
		$AbOrderids = array();
		$AbOrderShow = array();
		foreach($AbOrderList as $orderId){
			$AbOrderInfo = $WarehouseAPIAct->act_getAbOrderInfo($orderId['id']);
			$AbOrderListArr[$orderId['originOrderId']] = $AbOrderInfo;
			$AbOrderids[] = $orderId['originOrderId'];
			$AbOrderShow[$orderId['originOrderId']] = $orderId['id'];
		}
		//var_dump($AbOrderListArr); exit;
		$this->smarty->assign('AbOrderListArr', $AbOrderListArr);
		$this->smarty->assign('AbOrderShow', $AbOrderShow);
		
		//$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
		$platform	=  $OmAccountAct->act_getPlatformListByPower();
		$this->smarty->assign('platform', $platform);
		
		/**导航 start**/
		$this->smarty->assign('ostatus', $ostatus);       
		$this->smarty->assign('status', $status);   
        $this->smarty->assign('refund_status', $status);
		
		$StatusMenuAct = new StatusMenuAct();
		$ostatusList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = 0 AND is_delete=0');
		/*var_dump($ostatusList);*/
		$this->smarty->assign('ostatusList', $ostatusList);
        
        //二级目录
		/*$o_secondlevel =  $omAvailableAct->act_getTNameList('om_status_menu','*','WHERE is_delete=0 and groupId=0 order by sort asc');
		$this->smarty->assign('o_secondlevel', $o_secondlevel);*/
		$second_count = array();
		$second_type = array();
		foreach($ostatusList as $o_secondinfo){
			
			$orderStatus = $o_secondinfo['statusCode'];
			//echo $orderStatus."============"; echo "<br>";
			$s_total = $OrderindexAct->act_showSearchOrderNum($orderStatus);
			//echo $orderStatus."==".$s_total; echo "<br>";
			$second_count[$o_secondinfo['statusCode']] = $s_total;
			
			$s_type =  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$orderStatus.'" AND is_delete=0 order by sort asc');
			$second_type[$orderStatus] = $s_type[0]['statusCode'];
		}
		
		$this->smarty->assign('second_count', $second_count);
		$this->smarty->assign('second_type', $second_type);
		
		//退款数量
		//echo $where;
		
		//$refund_total = $omAvailableAct->act_getTNameCount("om_order_refund", " where is_delete=0 ");
		$refund_total = $omAvailableAct->act_getRecordNums();
		$this->smarty->assign('refund_total', $refund_total);
		
		$parameterArr	=	array();
		//var_dump($AbOrderids);
		$parameterArr['AbOrderList'] = $AbOrderids;
		$total = $OrderindexAct->act_showABOrder($ostatus, $otype, '', $parameterArr);
		$this->smarty->assign('abnormal_total', $total);
		
		//三级目录
		/*$refund_one = $omAvailableAct->act_getTNameCount("om_order_refund",$where." and status=0");
		$this->smarty->assign('refund_one', $refund_one);
		$refund_two = $omAvailableAct->act_getTNameCount("om_order_refund",$where." and status=1");
		$this->smarty->assign('refund_two', $refund_two);
		$refund_three = $omAvailableAct->act_getTNameCount("om_order_refund",$where." and status=2");
		$this->smarty->assign('refund_three', $refund_three);*/
		/**导航 end**/
		$refund_status = array('671'=>array(1,0),'673'=>array(1,1),'674'=>array(1,2),'675'=>array(2,0),'676'=>array(2,1),'677'=>array(2,2),'678'=>array(3,0),'679'=>array(3,1));
		$o_threelevel =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$ostatus' order by sort asc");
		//var_dump($o_threelevel);
		$extral_str = array();
		foreach($o_threelevel as $o_threeinfo){
			$statusCode = $o_threeinfo['statusCode'];
			$torderType = $refund_status[$statusCode][0];
			$tostatus = $refund_status[$statusCode][1];
			$where = " WHERE orderType = '{$torderType}' and status = '{$tostatus}' and is_delete = 0 ";
			//echo $where; echo "<br>";
			$s_total = $omAvailableAct->act_getRecordNums($where);
			//$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order", $or_where);
			$three_count[$o_threeinfo['statusCode']] = $s_total;
			$extral_str[$o_threeinfo['statusCode']] = "orderType={$torderType}&status={$tostatus}";
		}
		$this->smarty->assign('extral_str', $extral_str);
		$this->smarty->assign('o_threelevel', $o_threelevel);
		$this->smarty->assign('three_count', $three_count);
		
        $toptitle = '退款操作页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 0);
		$threelevel = '1';   //当前的三级菜单
        $this->smarty->assign('threelevel', $threelevel);

		$statusMenu	=  $omAvailableAct->act_getTNameList('om_status_menu',' * ','WHERE is_delete=0');
		$this->smarty->assign('statusMenu', $statusMenu);
		
        //print_r($orderRefundList);//exit;
        
        //$where =	' WHERE is_delete = 0 '.$where;
		$where = " WHERE orderType = '{$orderType}' and status = '{$status}' and is_delete = 0 ";
		//echo $where;
        //$total = $omAvailableAct->act_getTNameCount('om_order_refund', $where);
		$total = $omAvailableAct->act_getRecordNums($where);
        $num   = 100; //每页显示的个数
        $page  = new Page($total, $num, '', 'CN');
        //$where .= " ORDER BY id " . $page->limit;           
        //$orderRefundList = $omAvailableAct->act_getTNameList('om_order_refund', '*', $where);
		$orderRefundList = $omAvailableAct->act_getRefundList($where);
        
        //echo $where;
        if(empty($orderRefundList)) {
            $orderRefundList = array();
        }       
        
       	$this->smarty->assign('orderRefundList', $orderRefundList);
		
        $where = ' WHERE is_delete = 0 ';
        //$where = ' WHERE is_delete = 0 AND orderRefundId in('.$value.') '.$where; 
        if(!empty($orderRefundList)) {
            foreach($orderRefundList as $k => $v){
    			if($value == ''){
    				$value	=	$v['id'];
    			} else {
    				$value	.=	','.$v['id'];
    			}
    		}
            $where = ' WHERE is_delete = 0 AND orderRefundId in('.$value.') ';  
            
        }else{
            $where .= " AND 1=2 ";
        }               
        $orderRefundDetailList	=  $omAvailableAct->act_getTNameList('om_order_refund_detail','*',$where);	
		
        /*if(empty($orderRefundDetailList)){
			$status	=	'无符合条件的订单';
			$this->smarty->assign('status', $status);
			$this->smarty->display('orderRefund.htm.htm');
			exit;
		}*/
        //print_r($orderRefundDetailList);
        
        if(empty($orderRefundDetailList)) {
            $orderRefundDetailList = array();
        }          
    
      	$this->smarty->assign('orderRefundDetailList', $orderRefundDetailList);

		if (!empty ($_GET['page'])) {
			if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
				$n = 1;
			} else {
				$n = (intval($_GET['page']) - 1) * $num +1;
			}
		} else {
			$n = 1;
		}
		if ($total > $num) {
			//输出分页显示
			$show_page = $page->fpage(array (
				0,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9
			));
		} else {
			$show_page = $page->fpage(array (
				0,
				2,
				3
			));
		}
		
		//获取系统所有状态
		$statusList = copyOrderModel::selectStatusList();
		
		$this->smarty->assign('status', $status);
		$this->smarty->assign('statusList', $statusList);
		$this->smarty->assign('show_page', $show_page);
		
		$this->smarty->assign('account', $account);
		$this->smarty->assign('sku', $sku);
		$this->smarty->assign('pm', $pm);
		$this->smarty->assign('orderdEtailExtensionEbay', $orderdEtailExtensionEbay);
		$this->smarty->assign('omOrderList', $omOrderList);
		$this->smarty->assign('omOrderDetail', $omOrderDetail);
		//$this->smarty->assign('orderTracknumber', $orderTracknumber);
		$this->smarty->assign('omOrderExtensionEbay', $omOrderExtensionEbay);
		$this->smarty->assign('omOrderUserInfo', $omOrderUserInfo);
        $this->smarty->display('orderRefund.htm');
    }
}   