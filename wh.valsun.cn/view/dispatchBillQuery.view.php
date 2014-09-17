<?php
/**
 * 发货单信息查询
 * @author 涂兴隆
 */
class dispatchBillQueryView extends CommonView {
    /*
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
    }

    /*
     * 显示查询页面
     */
    public function view_showForm() {
        global $memc_obj;
        $pagesize = 200;    //页面大小

        $whereSql = $this->buildWhereSql();
        //echo $whereSql;exit;
        $packorder_obj = new PackingOrderModel();      
	    $rownumber = $packorder_obj->getRowAllNumber($whereSql.' group by po.id ');       //获得所有的行数

	    $pager = new Page($rownumber, $pagesize);
        $billlist = $packorder_obj->getBillList($whereSql.' group by po.id order by pd.pName '.$pager->limit);             //更具条件获得发货单
	
		$packorder_obj->buildOrderinfo($billlist);
		 
		/*
        //去除重复的
        $currentid = NULL;
        $prekey = 0;
        foreach ($billlist as $key=>$valbill){
            if ($currentid == $valbill['id']) {
            	$billlist[$prekey]['originOrderId'] .= ', '.$valbill['originOrderId'];
            	unset($billlist[$key]);
            }else {
                $prekey = $key;
                $currentid = $valbill['id'];
            }
        }
		*/
        $materInfo          =   CommonModel::getMaterInfoAll(); //获取包材信息
        $materInfo          =   reverse_array($materInfo, 'pmName', 'id');
        
        $shipingtyplist     =   CommonModel::getShipingTypeList();      //运输方式列表
        $shipingtyplist     =   reverse_array($shipingtyplist, 'carrierNameCn', 'id');
        
        $salesaccountlist   =   CommonModel::getSalesaccountList(); //获取销售帐号
        $salesaccountlist   =   reverse_array($salesaccountlist, 'account', 'id');
        //print_r($salesaccountlist);exit;
        $platformList       =   CommonModel::getPlatformInfo(); //获取平台帐号
        $platformList       =   reverse_array($platformList, 'platform', 'id');
        
        $this->smarty->assign('shipingtypelist', $shipingtyplist);
        $this->smarty->assign('salesaccountlist', $salesaccountlist);
        $this->smarty->assign('platformList', $platformList);
        
        
		foreach($billlist as $key=>$valbil){
			$tracknumber = '';
			$str_info = OmAvailableModel::getTNameList("wh_order_tracknumber","tracknumber","where shipOrderId='{$valbil['id']}' and is_delete=0");
			if(!empty($str_info)){
				$tracknumber = $str_info[0]['tracknumber'];
			}
			$billlist[$key]['tracknumber'] = $tracknumber;
			
			$originOrder_arr = array();
			$originOrder_str = '';
			$originOrder_info = OmAvailableModel::getTNameList("wh_shipping_order_relation","originOrderId","where shipOrderId='{$valbil['id']}'");
			
			if(!empty($originOrder_info)){
				foreach($originOrder_info as $originOrder){
					$originOrder_arr[] = $originOrder['originOrderId'];
				}	
				$originOrder_str = implode(',',$originOrder_arr);
			}
			$billlist[$key]['originOrder']   =   $originOrder_str;
			
			//运输方式
            $billlist[$key]['shipingname']  =   $shipingtyplist[$valbil['transportId']];

			//包材
            $billlist[$key]['materName']    =   $materInfo[$valbil['pmId']];
			
			//平台
            $billlist[$key]['platformName'] =   $platformList[$valbil['platformId']];
		
			//销售账号
            $billlist[$key]['salesaccountinfo'] =   $salesaccountlist[$valbil['accountId']];
		}

		if ($rownumber > $pagesize) {       //分页
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);

        $this->smarty->assign('billlist', $billlist);          //发货单列表

        $this->smarty->assign('secnev', 3);
        $libstu_obj = new LibraryStatusModel();                 //出库状态类型
        $libstatuslist = $libstu_obj->getAllLibStatusList(' and groupId in (4 ,5)');
        $this->smarty->assign('outstatuslist', $libstatuslist);

        $toptitle = '订单查询';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$storeId = isset($_GET['storeId']) ? intval($_GET['storeId']) :0; 
		if($storeId==1){
			$navlist = array(//面包屑
				array('url' => '', 'title' => '出库'),
				array('url' => '', 'title' => '发货单查询'),
				array('url' => '', 'title' => 'A仓发货单')
			);
		}elseif($storeId==2){
			$navlist = array(//面包屑
				array('url' => '', 'title' => '出库'),
				array('url' => '', 'title' => '发货单查询'),
				array('url' => '', 'title' => 'B仓发货单')
			);
		}else{
			$navlist = array(//面包屑
				array('url' => '', 'title' => '出库'),
				array('url' => '', 'title' => '发货单查询'),
			);
		}
        $this->smarty->assign('navlist', $navlist);

        $toplevel = 2;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
		$secondlevel = isset($_GET['secondlevel']) ? trim($_GET['secondlevel']) : '';
		if(empty($secondlevel)){
			$secondlevel = '21';   //当前的二级菜单
		}
        $this->smarty->assign('secondlevel', $secondlevel);

		$this->smarty->assign('platLists', $_SESSION['platformList']);
		$this->smarty->assign('accounts', $_SESSION['accountList']);
		$this->smarty->assign('shippingList', $_SESSION['shippingList']);
        $this->smarty->display('dispatchbillquery.htm');
    }

    /*
     * 构造sql搜索条件语句
     * 返回 sql条件语句字符串
     */
    private function buildWhereSql() {
		$sql_where_storeId = '';
        $storeId = isset($_GET['storeId']) ? intval($_GET['storeId']) :0; 
		if ($storeId != 0) { 
            $sql_where_storeId = " and po.storeId=$storeId";
        }
		$this->smarty->assign('storeId', $storeId);
		
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';                                         //关键字
        $sql_where_keywords = '';
        $keytype = isset($_GET['keytype']) ? intval($_GET['keytype']) :'';      //关键字类型
        //echo $keytype;exit;
        if (!empty($keywords) && ($keytype != 0)) {    //有关键字搜索条件
            $keywords = mysql_real_escape_string($keywords);
            if ($keytype == 1) {  //按订单号搜索
                $sql_where_keywords = " and por.originOrderId='$keywords'";
            } elseif ($keytype == 2) {     //按配货单号搜索
                $sql_where_keywords = " and po.id='$keywords'";
            } elseif ($keytype == 3) {     //按sku搜索
                $sql_where_keywords = " and pd.sku='$keywords'";
            } elseif ($keytype == 4) {
				$sql_where_keywords = " and ot.tracknumber='$keywords'";
			}else if ($keytype == 5){ //按配货单号搜索
			     $wave   =   WhBaseModel::number_decode($keywords); //获取配货单ID
                 $shipOrderIds  =   WhWaveShippingRelationModel::getShippingOrderIdsByWaveId($wave); //获取发货单ID
                 $orderIds      =   array();
                 if(!empty($shipOrderIds)){
                        $orderIds   =   get_filed_array('shipOrderId', $shipOrderIds);
                 }else{
                    $orderIds       =   array(0);
                 }
                 $orderIds          =   implode(',', $orderIds);
                 $sql_where_keywords    =   " and po.id in ($orderIds)";
			}
        }
        
        $this->smarty->assign('keywords', $keywords);
        $this->smarty->assign('keytype', $keytype);

        $sql_where_status = '';
        $status = intval($_GET['status']);                                                                          //订单状态
        if(!isset($_GET['status'])){
			$status = PKS_WGETGOODS;
		}
		if ($status != 0) {           //状态
            $sql_where_status = " and po.orderStatus=$status";
        }
        $this->smarty->assign('status', $status);

        $sql_where_orderstarttime = '';
        $ordertimestart = isset($_GET['startdate']) ? trim($_GET['startdate']) : '';                       //下单日期 开始
        if ($ordertimestart != 0) {           //开始时间
            $ordertimestart_int = strtotime($ordertimestart);
            $sql_where_orderstarttime = " and po.createdTime >= $ordertimestart_int";
        }
        $this->smarty->assign('ordertimestart', $ordertimestart);

        $sql_where_orderendtime = '';
        $ordertimeend = isset($_GET['enddate']) ? trim($_GET['enddate']) : '';                             //下单日期 结束
        if ($ordertimeend != 0) {             //结束时间
            $ordertimeend_int = strtotime($ordertimeend);
            //$ordertimeend_int += 86400;
            $sql_where_orderendtime = " and po.createdTime < $ordertimeend_int";
        }
        $this->smarty->assign('ordertimeend', $ordertimeend);

        $sql_where_goodsoutstarttime = '';
        $goodsouttimestart = isset($_GET['goodsouttimestart']) ? trim($_GET['goodsouttimestart']) : '';              //出库日期 开始
        if ($goodsouttimestart != 0) {        //出库开始时间
            $goodsouttimestart_int = strtotime($goodsouttimestart);
            $sql_where_goodsoutstarttime = " and po.weighTime >= $goodsouttimestart_int";
        }
        $this->smarty->assign('goodsouttimestart', $goodsouttimestart);

        $sql_where_goodsoutendtime = '';
        $goodsouttimeend = isset($_GET['goodsouttimeend']) ? trim($_GET['goodsouttimeend']) : '';                    //出库日期 结束
        if ($goodsouttimeend != 0) {          //出库结束时间
            $goodsouttimeend_int = strtotime($goodsouttimeend);
            //$goodsouttimeend += 86400;
            $sql_where_goodsoutstarttime = " and po.weighTime < $goodsouttimeend_int";
        }
        $this->smarty->assign('goodsouttimeend', $goodsouttimeend);
		
		$sql_where_isnote = '';
        $isNote = intval($_GET['isNote']);
		if($isNote){
            //echo $isNote;exit;
            switch ($isNote){
                case 1: //有留言
                    $sql_where_isnote = ' and po.isNote=1';
                    break;
                case 2: //没留言
                    $sql_where_isnote = ' and po.isNote=0';
                    break;
            }
		}
        $this->smarty->assign('isNote', $isNote);
		
		$sql_where_orderTypeId = '';
    /*    $orderTypeId = intval($_GET['orderTypeId']);
		if(!isset($_GET['orderTypeId'])){
			$orderTypeId = 1;
		}
        switch ($orderTypeId){
            case 1: //有留言
                $sql_where_orderTypeId = ' and po.orderTypeId=1';
                break;
            case 2: //没留言
                $sql_where_orderTypeId = ' and po.orderTypeId=2';
                break;
        }
        $this->smarty->assign('orderTypeId', $orderTypeId);*/
		
        $sql_where_shiptype = '';
        $shiptype = trim($_GET['shiptype']);                                                                       //运输方式
        if ($shiptype != 0) {                 //运输方式
			if($shiptype==200){
				$nshiptype = "1,2,3";
			}else if($shiptype==300){
				$nshiptype = "6,10,52,53";
			}else{
				$nshiptype = $shiptype;
			}
            $sql_where_shiptype = " and po.transportId in($nshiptype)";
        }else{
			if(!empty($_SESSION['shippingList'])){
				$nshiptype  = implode(',',$_SESSION['shippingList']);
				$sql_where_shiptype = " and po.transportId in($nshiptype)";
			}
		}
        $this->smarty->assign('shiptype', $shiptype);
 
        $sql_where_clientname = '';
        $client_name = trim($_GET['clientname']);
        if ($client_name != '') {   //按客户id搜索
        	$sql_where_clientname = " and po.platformUsername='$client_name'";
        }
        $this->smarty->assign('clientname', $client_name);
        
        $sql_where_salesaccount = '';
        $salesaccount= trim($_GET['salesaccount']);
        if ($salesaccount != '') {   //按客户id搜索
            $sql_where_salesaccount = " and po.accountId='$salesaccount'";
        }else{
			if(!empty($_SESSION['accountList'])){
				$accountInfo  = implode(',',$_SESSION['accountList']);
				$sql_where_salesaccount = " and po.accountId in($accountInfo)";
			}	
		}
        $this->smarty->assign('salesaccount', $salesaccount);
        
        $sql_where_hunhe = '';
        $hunhe = intval($_GET['hunhe']);
        //echo $hunhe;exit;
        switch ($hunhe){
            case 2: //单料号
                $sql_where_hunhe = ' and po.orderAttributes='.SOA_SINGLE;
                break;
            case 1: //多料号
                $sql_where_hunhe = ' and po.orderAttributes='.SOA_MULTIY;
                break;
            case 3: //组合订单
                $sql_where_hunhe = ' and po.orderAttributes='.SOA_COMBIN;
                break;
        }
        $this->smarty->assign('hunhe', $hunhe);
		
		$sql_where_platformName = '';
        $platformName = trim($_GET['platformName']);
        if ($platformName != '') {                 //平台
            $sql_where_platformName = " and po.platformId= $platformName";
        }else{
			if(!empty($_SESSION['platformList'])){
				$platformInfo  = implode(',',$_SESSION['platformList']);
				$sql_where_platformName = " and po.platformId in($platformInfo)";
			}	
		}
        $this->smarty->assign('platformName', $platformName);

        return $sql_where_storeId . $sql_where_keywords . $sql_where_status . $sql_where_orderstarttime . $sql_where_orderendtime .
                $sql_where_goodsoutstarttime . $sql_where_goodsoutendtime . $sql_where_isnote . $sql_where_orderTypeId.  $sql_where_shiptype.$sql_where_clientname.
        $sql_where_salesaccount.$sql_where_hunhe.$sql_where_platformName;
    }

	//http://www.localhost.cc/zhang/code/wh.valsun.cn/html/index.php?mod=dispatchBillQuery&act=getExpressRemark&id=1
	public function view_getExpressRemark() {
		$id	=	isset($_GET['id']) ? $_GET['id'] : '';
		if(empty($id)) {
			return false;
		}

		$data	=	CommonModel::getExpressRemark($id);
		if(empty($data)){
			echo '查询不到数据!';exit;
		}
		$total	=	0;
		foreach($data as $k => $v){
			$total	+=	$v['price'] * $v['amount'];
			$type	=	$v['type'];
		}
		$this->smarty->assign('data', $data);
		$this->smarty->assign('total', $total);
		$this->smarty->assign('type', $type);
        $this->smarty->display('expressRemark.htm');
	}
}