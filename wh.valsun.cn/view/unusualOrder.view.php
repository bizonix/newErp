<?php

/** 
 * @author 涂兴隆
 * 异常发货单处理
 */
class UnusualOrderView extends CommonView
{

    /**
     * 构造函数
     */
    function __construct ()
    {
    	parent::__construct();
    }
    
    /*
     * 异常发货单列表
     */
    public function view_unusualOrderList(){
        $pagesize = 100;    //页面大小
        
        $statusar = array(PKS_UNUSUAL);
        $statusstr = implode(',', $statusar);
        
        $packing_obj = new PackingOrderModel();
        $count = $packing_obj->getRecordsNumByStatus($statusar);      //获得当前状态为待复核的发货单总数量
        
        $pager = new Page($count, $pagesize);    //分页对象
        
        $billlist = $packing_obj->getBillList(' and orderStatus in ('.$statusstr.') order by po.id '.$pager->limit);
        $this->smarty->assign('billlist', $billlist);
        
		$ShipingTypeList = CommonModel::getShipingTypeListKeyId();
		$count = count($billlist);
		for($i=0;$i<$count;$i++){
			$billlist[$i]['shipingname'] = isset($ShipingTypeList[$billlist[$i]['transportId']])?$ShipingTypeList[$billlist[$i]['transportId']]:'';
		}
		
		$acc_id_arr = array();
		foreach($billlist as $key=>$valbil){
			if(!in_array($valbil['accountId'],$acc_id_arr)){
				array_push($acc_id_arr,$valbil['accountId']);
			}
		}
		$salesaccountinfo = CommonModel::getAccountInfo($acc_id_arr);
		$this->smarty->assign('salesaccountinfo', $salesaccountinfo);
		
        if ($count > $pagesize) {       //分页链接
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '异常订单'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '异常订单处理';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '29';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        $this->smarty->display('unusualorderlist.htm');
    }
    
    /*
     * 处理异常订单
     */
    public function view_handelUnusalOrder(){
        $orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
        if ($orderid<1) {
        	$data = array('data'=>array('请指定订单号！'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
        	goErrMsgPage($data);
        	exit;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){
            $data = array('data'=>array('不存在的单号！'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goErrMsgPage($data);
            exit;
        }
        
        if ($orderinfo['orderStatus'] != PKS_UNUSUAL) { // 该订单不不属于异常订单
            $data = array('data'=>array('该发货单不属于异常单！'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goErrMsgPage($data);
            exit;
        }
        
        $sod_obj = new ShipingOrderDetailModel();
        $skulist = $sod_obj->getSkuListByOrderId($orderid);     //该订单下的sku列表
        
        $sku_have = $sod_obj->getSkuHavedone($orderid);     //已经配货的sku信息
        
        foreach ($sku_have as $skuhval){
            if(array_key_exists($skuhval['shipOrderdetailId'], $skulist)){
                $skulist[$skuhval['shipOrderdetailId']]['picknum']  = $skuhval['amount'];
            }
        }
        
        $sellorder = $po_obj->getSellOrderidByOrderid($orderid);
        $sellorderidstr = '';           //与改发货单关联的订单号
        foreach ($sellorder as $soval){
            $sellorderidstr .= $soval['originOrderId'].'&nbsp&nbsp&nbsp';
        }
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => 'index.php?mod=unusualOrder&act=unusualOrderList', 'title' => '异常订单'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '异常订单处理';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '29';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        
        $this->smarty->assign('orderid', $orderinfo['id']);     //发货单id
        $this->smarty->assign('sellorderid', $sellorderidstr);  //关联的订单号
        $this->smarty->assign('sellaccount', $orderinfo['account']); //销售账号
        $this->smarty->assign('type', ShipingTypeModel::typeIdTostr($orderinfo['orderTypeId']));
        $this->smarty->assign('skulist', $skulist);
        
        $this->smarty->display('unusralorderhandel.htm');
    }
    
    /*
     * 异常订单 回滚库存
     */
    public function view_rollBackStock(){
        $orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
        if($orderid <1){    //传参不合法
            $data = array('data'=>array('参数不合法!'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goErrMsgPage($data);
            exit;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){
            $data = array('data'=>array('指定发货单不存在!'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goErrMsgPage($data);
            exit;
        }
        
        if($orderinfo['orderStatus'] != PKS_UNUSUAL){  //该订单不在待复核状态
            $data = array('data'=>array('该订单不是异常订单!'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goErrMsgPage($data);
            exit;
        }
        
        $sod_obj = new ShipingOrderDetailModel();
        $sku_havegot = $sod_obj->getSkuHavedone($orderid);  //获得已经配货的sku列表
        
        $rollbackresult = $sod_obj->rollBackStock($sku_havegot);
        
        if($rollbackresult){
            $data = array('data'=>array('回滚成功!'), 'link'=>'index.php?mod=unusualOrder&act=unusualOrderList');
            goOkMsgPage($data);
            exit;
        } else {
            $data = array('data'=>array('该订单不是异常订单!'), 'link'=>'index.php?mod=dispatchBillQuery&act=showForm');
            goErrMsgPage($data);
            exit;
        }
    }
}


?>