<?php
/**
 * 库存异常问题View页面
 * @author herman.xi
 * @20140116
 */
class AbnormalStockView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 显示查询页面
     */
    public function view_abnormalStockList() {
        global $memc_obj;
        $pagesize = 200;    //页面大小
		
		//var_dump($AbOrderList); exit;
		$omAvailableAct = new OmAvailableAct();
		$TransAPIAct = new TransAPIAct();
		$OrderindexAct = new OrderindexAct();
		$GoodsAct = new GoodsAct();
		$OrderRefundAct = new OrderRefundAct();
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
		//var_dump($AbOrderListArr); exit;
		$platform	=  $OmAccountAct->act_getPlatformListByPower();
		$this->smarty->assign('platform', $platform);
		$account = $OmAccountAct->act_accountAllListById();//账号信息
		
		/**导航 start**/
		$ostatus = isset($_GET['ostatus']) ? $_GET['ostatus'] : 0;
		$this->smarty->assign('ostatus', 770);
		
		$StatusMenuAct = new StatusMenuAct();
		$ostatusList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = 0 AND is_delete=0');
		//var_dump($ostatusList);
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
        $where = " WHERE is_delete=0 ";
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
		    //$where .= " AND 1=2 ";
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
		    //$where .= " AND 1=2 ";
		}
		//if($where){
		//$refund_total = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0 ");
		$refund_total = $OrderRefundAct->act_getRecordNums();
		//}else{
			//$refund_total = 0;	
		//}
		$this->smarty->assign('refund_total', $refund_total);
		
		//三级目录
		/*$refund_one = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0 and status=0");
		$this->smarty->assign('refund_one', $refund_one);
		$refund_two = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0 and status=1");
		$this->smarty->assign('refund_two', $refund_two);
		$refund_three = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0 and status=2");
		$this->smarty->assign('refund_three', $refund_three);*/
		/**导航 end**/
		
        $toptitle = '异常缺货统计页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 0);
		$threelevel = '1';   //当前的三级菜单
        $this->smarty->assign('threelevel', $threelevel);

		$statusMenu	=  $omAvailableAct->act_getTNameList('om_status_menu',' * ','WHERE is_delete=0');
		$this->smarty->assign('statusMenu', $statusMenu);
        
       /* $where =	' WHERE is_delete = 0 '.$where;
        $total = $omAvailableAct->act_getTNameCount('om_order_refund', $where);*/
		$parameterArr	=	array();
		$parameterArr['AbOrderList'] = $AbOrderids;
		$total = $OrderindexAct->act_showABOrder($ostatus, $otype, '', $parameterArr);
		$this->smarty->assign('abnormal_total', $total);
		//echo $total; exit;
		$num = $pagesize; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		//$limit	=	$page->limit;
		//var_dump($parameterArr);
		$omOrderList = $OrderindexAct->act_showABOrder($ostatus, $otype, $page->limit, $parameterArr);
        
       	$this->smarty->assign('omOrderList', $omOrderList);
		
		$pm = $GoodsAct->act_getMaterInfoByList();
		
		$transportationList = $TransAPIAct->act_getCarrierListById();
		$this->smarty->assign('transportationList', $transportationList);
		
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
		//$statusList = copyOrderModel::selectStatusList();
		//var_dump($statusList); exit;
		//$this->smarty->assign('statusList', $statusList);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('account', $account);
		//$this->smarty->assign('sku', $sku);
		$this->smarty->assign('pm', $pm);
		$this->smarty->assign('omOrderList', $omOrderList);
        $this->smarty->display('orderindex.htm');
    }
}   