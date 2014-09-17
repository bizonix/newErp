<?php
/**
 * 订单信息查询
 * @author herman.xi
 */
class orderindexView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 显示查询页面(包括搜索功能)
	 * zyp
     */
    public function view_getOrderList() {
        global $memc_obj;
        $pagesize = isset($_GET['pnum'])&&intval($_GET['pnum'])>0&&intval($_GET['pnum'])<101 ? intval($_GET['pnum']) : 20;				//页面大小

        if ($_GET['debug']==1){
			$starttime = time()+microtime();
			echo microtime().'-----'.$starttime."\n";
        }
		
		//菜单
		$status	=	'';
		$search	=	isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
		//搜索时使用的数据
		//order表
		$searchPlatformId			=	isset($_GET['platformId']) ? $_GET['platformId'] : '';				//搜索平台
		$searchAccountId			=	isset($_GET['accountId']) ? $_GET['accountId'] : '';				//搜索账号
		$searchIsNote				=	isset($_GET['isNote']) ? $_GET['isNote'] : '';						//是否有留言
		$searchTransportationType	=	isset($_GET['transportationType']) ? $_GET['transportationType'] : '';//运输类型
		$searchTransportation		=	isset($_GET['transportation']) ? $_GET['transportation'] : '';		//运输方式
		$searchIsBuji				=	isset($_GET['isBuji']) ? $_GET['isBuji'] : '';						//是否补寄订单
		$searchIsLock				=	isset($_GET['isLock']) ? $_GET['isLock'] : '';						//是否锁定
		$searchOrderTime1			=	isset($_GET['OrderTime1']) ? $_GET['OrderTime1'] : '';				//搜索下单初始时间
		$searchOrderTime2			=	isset($_GET['OrderTime2']) ? $_GET['OrderTime2'] : '';				//搜索下单结束时间
		//order_detail表
		$searchReviews				=	isset($_GET['reviews']) ? $_GET['reviews'] : '';					//是否评价
		$searchSku					=	isset($_GET['sku']) ? $_GET['sku'] : '';							//sku
		//$searchOmOrderId			=	'';																	//订单编号
		$searchOrderType			=	isset($_GET['selectOrderType']) ? $_GET['selectOrderType'] : '';	//订单种类
		//order_userInfo表
		//$searchUsername				=	'';																	//买家名
		//$searchEmail				=	'';
		$countryName				=   isset($_GET['country'])? $_GET['country']:"";
		$state						=   isset($_GET['state'])? $_GET['state']:"";
		$city						=   isset($_GET['city'])? $_GET['city']:"";
		$zipCode					=   isset($_GET['zipCode'])? $_GET['zipCode']:"";
		
		//order_warehouse表
		//$weighTimeStart				= 	isset($_GET['searchTimeStart'])? $_GET['searchTimeStart']:"";
		//$weighTimeEnd				= 	isset($_GET['searchTimeEnd'])? $_GET['searchTimeEnd']:"";
		$searchTimeType				=	isset($_GET['searchTimeType']) ? $_GET['searchTimeType'] : '';
		
		//order_tracknumbe表
		//$searchTracknumber			=	'';																	//跟踪号
		//order_extension_ebay表
		//$searchTransId				=	'';																	//交易ID
		$searchKeywordsType			=	isset($_GET['KeywordsType']) ? $_GET['KeywordsType'] : '';			//搜索关键字类型
		$searchKeywords				=	isset($_GET['Keywords']) ? $_GET['Keywords'] : '';					//搜索关键字
		
		$this->smarty->assign('searchPlatformId', $searchPlatformId);
		$this->smarty->assign('searchAccountId', $searchAccountId);
		$this->smarty->assign('searchIsNote', $searchIsNote);
		$this->smarty->assign('searchTransportationType', $searchTransportationType);
		$this->smarty->assign('searchTransportation', $searchTransportation);
		$this->smarty->assign('searchIsBuji', $searchIsBuji);
		$this->smarty->assign('searchIsLock', $searchIsLock);
		$this->smarty->assign('searchOrderTime1', $searchOrderTime1);
		$this->smarty->assign('searchOrderTime2', $searchOrderTime2);
		$this->smarty->assign('searchReviews', $searchReviews);
		$this->smarty->assign('searchSku', $searchSku);
		$this->smarty->assign('searchOrderType', $searchOrderType);
		//$this->smarty->assign('searchEmail', $searchEmail);
		$this->smarty->assign('searchKeywordsType', $searchKeywordsType);
		$this->smarty->assign('searchKeywords', $searchKeywords);
		$this->smarty->assign('searchCountry', $countryName);
		$this->smarty->assign('searchState', $state);
		$this->smarty->assign('searchCity', $city);
		$this->smarty->assign('searchZipCode', $zipCode);
		//$this->smarty->assign('searchTimeStart', $weighTimeStart);
		//$this->smarty->assign('searchTimeEnd', $weighTimeEnd);
		$this->smarty->assign('searchTimeType', $searchTimeType);

		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "firsttime ==={$dotime}=== ".($dotime-$starttime)."\n\n";
		}
		
		$OrderRefundAct = new OrderRefundAct();
		$omAvailableAct = new OmAvailableAct();
		$OrderindexAct = new OrderindexAct();
		//平台信息
		$OmAccountAct = new OmAccountAct();
		$WarehouseAPIAct = new WarehouseAPIAct();
		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "secondtime === ".($dotime1-$dotime)."\n\n";
		}
		
		$AbOrderList = $WarehouseAPIAct->act_getAbOrderList();
		//var_dump($AbOrderList); exit;
		
		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "thirdtime === ".($dotime-$dotime1)."\n\n";
		}

		$AbOrderListArr = array();
		$AbOrderids = array();
		$AbOrderShow = array();
		foreach($AbOrderList as $orderId){
			$AbOrderInfo = $WarehouseAPIAct->act_getAbOrderInfo($orderId['id']);
			$AbOrderListArr[$orderId['originOrderId']] = $AbOrderInfo;
			$AbOrderids[] = $orderId['originOrderId'];
			$AbOrderShow[$orderId['originOrderId']] = $orderId['id'];
		}

		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "forthtime === ".($dotime1-$dotime)."\n\n";
		}

		//var_dump($AbOrderListArr); exit;
		$this->smarty->assign('AbOrderListArr', $AbOrderListArr);
		$this->smarty->assign('AbOrderShow', $AbOrderShow);
		
		//$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
		$platform	=  $OmAccountAct->act_getPlatformListByPower();
		//var_dump($platform);
		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "fiftytime === ".($dotime-$dotime1)."\n\n";
		}

		$this->smarty->assign('platform', $platform);
		
		/**导航 start**/
		$default_ostatus = isset($_GET['ostatus']) ? $_GET['ostatus'] : C('STATEPENDING');
		/*if(isset($_GET['ostatus']) && !empty($_GET['ostatus'])){
			$ostatus = $_GET['ostatus'];
		}else{
			$ostatus = C('STATEPENDING');
		}*/
		$ostatus = $_GET['ostatus'];
		//echo C('STATEPENDING');
		$this->smarty->assign('ostatus', $ostatus);
		$otype   = isset($_GET['otype']) ? $_GET['otype'] : '';
		$this->smarty->assign('otype', $otype);
		//二级目录
		
		$StatusMenuAct = new StatusMenuAct();
		$ostatusList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = 0 AND is_delete=0');
		//var_dump($ostatusList);
		$this->smarty->assign('ostatusList', $ostatusList);
		
		if($ostatus){
			$otypeList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$ostatus.'" AND is_delete=0');
		}else{
			$otypeList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$default_ostatus.'" AND is_delete=0');
		}
		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "sixtytime === ".($dotime1-$dotime)."\n\n";
		}

		//var_dump($otypeList);
		$this->smarty->assign('otypeList', $otypeList);
		
		/*$o_secondlevel =  $omAvailableAct->act_getTNameList('om_status_menu','*','WHERE is_delete=0 and groupId=0 order by sort asc');
		$this->smarty->assign('o_secondlevel', $o_secondlevel);*/
		
		$second_count = array();
		$second_type = array();
		$accountacc = $_SESSION['accountacc'];
		//var_dump($ostatusList); echo "<br>";
		foreach($ostatusList as $o_secondinfo){
			$orderStatus = $o_secondinfo['statusCode'];
			//echo $orderStatus."============"; echo "<br>";
			$s_total = 0;//$OrderindexAct->act_showSearchOrderNum($orderStatus);
			//echo $orderStatus."==".$s_total; echo "<br>";
			$second_count[$o_secondinfo['statusCode']] = $s_total;
			
			//$s_type =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$orderStatus' order by sort asc");
			$s_type =  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$orderStatus.'" AND is_delete=0 order by sort asc');
			$second_type[$orderStatus] = $s_type[0]['statusCode'];
		}
		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "seventime === ".($dotime-$dotime1)."\n\n";
		}

		//var_dump($second_count);
		$this->smarty->assign('second_count', $second_count);
		$this->smarty->assign('second_type', $second_type);
		//var_dump($second_type);
		
		//退款数量
        $accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
        $where = " WHERE is_delete=0 ";
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
		//echo $where;
		//$refund_total = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0 ");
		$refund_total = $OrderRefundAct->act_getRecordNums();
		$this->smarty->assign('refund_total', $refund_total);
		
		$parameterArr	=	array();
		//var_dump($AbOrderids);
		$parameterArr['AbOrderList'] = $AbOrderids;
		$total = $OrderindexAct->act_showABOrder($ostatus, $otype, '', $parameterArr);
		$this->smarty->assign('abnormal_total', $total);
		
		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "8time === ".($dotime1-$dotime)."\n\n";
		}

		//三级目录
		$three_count = array();
		if($ostatus){
			$o_threelevel =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$ostatus' order by sort asc");
			foreach($o_threelevel as $o_threeinfo){
				$orderType = $o_threeinfo['statusCode'];
				/*$or_where = " where orderStatus='$ostatus' and orderType='$orderType' ";
				if($accountacc){
					$or_where .= ' AND ('.$accountacc.') ';
				}*/
				//$s_total = $OrderindexAct->act_showSearchOrderNum($ostatus, $orderType);
				$s_total = $default_ostatus==900&&$orderType==21 ? 'n' : $OrderindexAct->act_showSearchOrderNum($default_ostatus, $orderType);
				//$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order", $or_where);
				$three_count[$o_threeinfo['statusCode']] = $s_total;
			}
		}else{
			$or_where = "WHERE is_delete=0 and groupId='$default_ostatus' ";
			if($accountacc){
				$or_where .= ' AND ('.$accountacc.') ';
			}
			$or_where .= " order by sort asc";
			$o_threelevel =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$default_ostatus' order by sort asc");
			foreach($o_threelevel as $o_threeinfo){
				$orderType = $o_threeinfo['statusCode'];
				/*$or_where = " where orderStatus='$ostatus' and orderType='$orderType' ";
				if($accountacc){
					$or_where .= ' AND ('.$accountacc.') ';
				}*/

				$s_total = $default_ostatus==900&&$orderType==21 ? 'n' : $OrderindexAct->act_showSearchOrderNum($default_ostatus, $orderType);

				//$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order", $or_where);
				$three_count[$o_threeinfo['statusCode']] = $s_total;
			}
		}
		//
		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "9time === ".($dotime-$dotime1)."\n\n";
		}

		$this->smarty->assign('o_threelevel', $o_threelevel);
		$this->smarty->assign('three_count', $three_count);
		
        $toptitle = '订单显示页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 0);
		$threelevel = '1';   //当前的三级菜单
        $this->smarty->assign('threelevel', $threelevel);

		$statusMenu	=  $omAvailableAct->act_getTNameList('om_status_menu',' * ','WHERE is_delete=0 ');
		$this->smarty->assign('statusMenu', $statusMenu);
		
		$value	= '';
		$where  = '';
		
		switch($searchTransportationType){
			case '1':
				$transportation = CommonModel::getCarrierList(1);		//快递
				break;
			case '2':
				$transportation = CommonModel::getCarrierList(0);	//平邮
				break;
			default:
				$transportation = CommonModel::getCarrierList();   //所有的
				break;
		}
		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "10time === ".($dotime1-$dotime)."\n\n";
		}

		//var_dump($transportation); exit;
		$transportationList = array();
		foreach($transportation as $tranValue){
			$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
		}
		//var_dump($transportationList); exit;
		$this->smarty->assign('transportation', $transportation);
		$this->smarty->assign('transportationList', $transportationList);
		
		if($search == ''){
			/*$where	=	' WHERE is_delete = 0 AND storeId = 1 AND orderStatus = '.$ostatus;
			if($otype	!=	''){
				$where	.=	' AND orderType	=	'.$otype;
			}*/
			//$total = $omAvailableAct->act_getTNameCount($orderForm, $where);
			$total = $OrderindexAct->act_showOrder($ostatus, $otype);
			//echo $total; exit;
			$num = $pagesize; //每页显示的个数
			$page = new Page($total, $num, '', 'CN');
			//$where .= " ORDER BY ordersTime " . $page->limit;
			//echo "========订单系统升级中========="; echo "<br>";
			$omOrderList = $OrderindexAct->act_showOrder($ostatus, $otype, $page->limit);
			//echo "<pre>"; var_dump($omOrderList); exit;
			//$omOrderList = $omAvailableAct->act_getTNameList($orderForm, '*', $where);
		} else {
			$parameterArr	=	array();
			$parameterArr['searchPlatformId']	=	$searchPlatformId;
			$parameterArr['searchAccountId']	=	$searchAccountId;
			$parameterArr['searchIsNote']	=	$searchIsNote;
			$parameterArr['searchTransportationType']	=	$searchTransportationType;
			$parameterArr['searchTransportation']	=	$searchTransportation;
			$parameterArr['searchIsBuji']	=	$searchIsBuji;
			$parameterArr['searchIsLock']	=	$searchIsLock;
			$parameterArr['searchOrderTime1']	=	$searchOrderTime1;
			$parameterArr['searchOrderTime2']	=	$searchOrderTime2;
			$parameterArr['searchReviews']	=	$searchReviews;
			$parameterArr['searchSku']	=	trim($searchSku);
			$parameterArr['searchOrderType']	=	$searchOrderType;
			$parameterArr['searchKeywordsType']	=	$searchKeywordsType;
			$parameterArr['countryName']	=	trim($countryName);
			$parameterArr['state']	=	trim($state);
			$parameterArr['city']	=	trim($city);
			$parameterArr['zipCode']	=	trim($zipCode);
			$parameterArr['searchTimeType']	=	trim($searchTimeType);
			$parameterArr['searchKeywords']	=	trim($searchKeywords);
			//$parameterArr['searchKeywords']	=	trim($searchKeywords);
			/*if($_SESSION['sysUserId'] == 8){
				var_dump($ostatus.'---'.$otype);
			}*/
			//echo "订单系统升级中。。。。。。。<br>";
			//var_dump($parameterArr); echo "<br>";
			//$total = $OrderindexAct->act_index($parameterArr,$searchKeywordsType,$searchKeywords);
			$total = $OrderindexAct->act_showOrder($ostatus, $otype, '', $parameterArr);
			//echo $total; exit;
			$num = $pagesize; //每页显示的个数
			$page = new Page($total, $num, '', 'CN');
			//$limit	=	$page->limit;
			//var_dump($parameterArr);
			$omOrderList = $OrderindexAct->act_showOrder($ostatus, $otype, $page->limit, $parameterArr);
			//var_dump($omOrderList);
			//$omOrderList = OrderindexAct::act_index($parameterArr,$searchKeywordsType,$searchKeywords,$limit,$ostatus);
		}
		if ($_GET['debug']==1){
			$dotime = time()+microtime();
			echo "11time ={$dotime}== ".($dotime-$dotime1)."\n\n";
		}
		
		//$sku	=	array();
		$account_where = ' WHERE is_delete = 0 ';
		if($searchPlatformId){
			$account_where .= ' AND platformId = '.$searchPlatformId;
		}
		$UserCompetenceAct = new UserCompetenceAct();
		$accountList = $UserCompetenceAct->act_showGlobalUser();
		if($accountList){
			$account_where .= ' AND id in ( '.join(',', $accountList).' ) ';	
		}
		//帐号信息
		$accountList = $omAvailableAct->act_getTNameList('om_account', '*', $account_where);
		//var_dump($accountList); exit;
		$account = array();
		foreach($accountList as $v){
			$account[$v['id']] = $v['account'];
		}
		
		//包材信息
		$pm = GoodsModel::getMaterInfoByList();
		
		if (!empty ($_GET['page'])) {
			if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
				$n = 1;
			} else {
				$n = (intval($_GET['page']) - 1) * $num +1;
			}
		} else {
			$n = 1;
		}
		//var_dump($page);
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
		//echo $show_page;
		//获取系统所有状态
		$statusList = copyOrderModel::selectStatusList();

		if ($_GET['debug']==1){
			$dotime1 = time()+microtime();
			echo "12time ={$dotime1}== ".($dotime1-$dotime)."\n\n";
		}
		//echo $show_page;
		$this->smarty->assign('statusList', $statusList);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('account', $account);
		$this->smarty->assign('accountList', $accountList);
		$this->smarty->assign('pm', $pm);
		$this->smarty->assign('omOrderList', $omOrderList);
        $this->smarty->display('orderindex.htm');
    }
}   