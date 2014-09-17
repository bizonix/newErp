<?php
/*
 * 备货单管理
 */
class OwGoodsReplenishManageView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct ();
    }
    
    /*
     * 显示备货单列表
     */
    public function view_showOrderList(){
        $pageSize   = 50;                                                                    //一页显示多少个备货单
        $where      = '';                                                                   //where条件语句
        
        $status     = isset($_GET['status'])    ? trim($_GET['status']) : NULL;           //备货单状态
        if (!empty($status) && $status!=='NULL') {
        	$where .= " AND a.status='$status' ";
        }
        $orderSn    = isset($_GET['ordersn'])   ? trim($_GET['ordersn']) : NULL;            //备货单号
        $orderSn    = mysql_real_escape_string($orderSn);
        if (!empty($orderSn)) {
        	$where .= " AND  a.ordersn='$orderSn' ";
        }
    	$sku    = isset($_GET['sku'])   ? trim($_GET['sku']) : NULL;            //料号
        $sku    = mysql_real_escape_string($sku);
        if (!empty($sku)) {
        	$where .= " AND b.sku = '$sku' ";
        }
        $con  		= '';
    	$skustatus  = isset($_GET['skustatus']) ? trim($_GET['skustatus']) : NULL;            //料号
        if (!empty($skustatus)) {
        	if($skustatus == 1){
        		$con   .= " AND scantime = 0 ";
        		$where .= " AND a.status NOT IN(3,4) ";
        	}else if($skustatus == 2){
        		$con .= " AND scantime != 0 ";	
        	}
        }
        $startTime  = isset($_GET['startTime']) ? trim($_GET['startTime']) : NULL;          //开始时间
        if (!empty($startTime)) {
            $startTimeStamp = strtotime($startTime);
            $where  .= " and a.createtime>$startTimeStamp ";
        }
        
        $endTime    = isset($_GET['endTime'])   ? trim($_GET['endTime'])  : NULL;           //结束时间
        if (!empty($endTime)) {
        	$endTimeStamp  = strtotime($endTime);
        	$where .= " and a.createtime<$endTimeStamp ";
        }
        
        $preGoodObj = new PreGoodsOrdderManageModel();
        $count      = $preGoodObj->getBackOrderInfoCount($where);//culOrderCount($where);
        if (FALSE === $count) {                                                            //计算数量出错
            header('location:index.php?mod=owGoodsReplenishManage&act=showOrderList');
            exit;
        }
        
        $pageObj        = new Page($count, $pageSize);
        $orderInfoList  = $preGoodObj->getBackOrderInfo($where.$pageObj->limit);//getOrderListInfo($where.$pageObj->limit);
        foreach ($orderInfoList as &$order){
            $order['createTimeStr'] = empty($order['createtime']) ? '' : date('Y-m-d H:i:s', $order['createtime']);
            $order['synctimeStr']   = empty($order['synctime']) ? '' : date('Y-m-d H:i:s', $order['synctime']);
            $order['statusStr']     = PreGoodsOrdderManageModel::statusCodeToStr($order['status']);
            $skuDetail              = $preGoodObj->getSKUDetail($order['id'], $con);
            $order['ownerName']     = getUserNameById($order['owner']);
            foreach ($skuDetail as &$row){
                $row['scantime']    = empty($row['scantime']) ? '未配货' : date('Y-m-d H:i:s', $row['scantime']);
                $row['userName']    = empty($row['scanuser']) ? '' : getUserNameById($row['scanuser']);
            }
            $order['skulist']    = $skuDetail;
        }
        
        if ($count > $pageSize) {
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'海外仓备货单'),
        );
        
        $toplevel = 2;                                                                      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '214';                                                                //当前的二级菜单
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('orderSn', $orderSn);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('sku', $sku);
        $this->smarty->assign('skustatus', $skustatus);
        $this->smarty->assign('third', 1);
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('orderList', $orderInfoList);
        $this->smarty->display('pregoodsorderlist.htm');
    }
    
    /*
     * 显示备货单列表
    */
    public function view_editOrder(){
        $orderId    = isset($_GET['orderId']) ? intval($_GET['orderId']) : FALSE;
        if (empty($orderId)) {
        	goErrMsgPage(array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=showOrderList'));
        	exit;
        }
        
        $preGoods   = new PreGoodsOrdderManageModel();
        $orderInfo  = $preGoods->getOrderInfroByid($orderId);
        if (empty($orderInfo)) {
            goErrMsgPage(array('data'=>array('不存在的订单!'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=showOrderList'));
            exit;
        }
        
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓备货'),
                array('url'=>'','title'=>'编辑备货单'),
        );
    
        $toplevel = 2;                                                                      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
    
        $secondlevel = '214';                                                                //当前的二级菜单
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('orderInfo', $orderInfo);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->display('editpregoodsorder.htm');
    }
    
    /*
     * 补货单管理
     */
    public function view_preOrderList(){
        $status     = isset($_GET['status']) ? intval($_GET['status']) : FALSE;                   //状态
        $boxId      = isset($_GET['orderid'])  ? trim($_GET['orderid']): FALSE;                   //箱号
        $startTime  = isset($_GET['startTime']) ? trim($_GET['startTime']) : FALSE;             //开始时间
        $endTime    = isset($_GET['endTime'])   ? trim($_GET['endTime']) : FALSE;                  //结束时间
        
        $whereSql   = '';
        if (!empty($status)) {
            $whereSql .= " and status='$status' ";
        }
        if (!empty($boxId)) {
            $bxid   = intval($boxId);
            $whereSql  .= " and id='$bxid' ";
        }
        if (!empty($startTime)) {
            $startTimeStamp = strtotime($startTime);
            $whereSql  .= " and createTime>$startTimeStamp ";
        }
        
        if (!empty($endTime)) {
            $endTimeStamp = strtotime($endTime);
            $whereSql  .= " and createTime<$endTimeStamp ";
        }
        //         echo $whereSql;exit;
        $pageSize   = 100;
        
        //         print_r($boxinfoList);exit;
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'补货单管理'),
        );
        
        $pre_obj    = new PreplenshOrderModel();
        $count      = $pre_obj->culPreshOrder($whereSql);
        
        $pageObj    = new Page($count, $pageSize);
        
        $orderList  = $pre_obj->culPershOrder($whereSql.$pageObj->limit);
        foreach ($orderList as &$list){
            $list['timestr']    = date('Y-m-d H:i:s', $list['createTime']);
            $list['statusstr']  = PreplenshOrderModel::status2Str($list['status']);
            $list['userName']   = empty($list['createUser'])    ? '' : getUserNameById($list['createUser']);
        }
//         print_r($orderList);exit;
        if ($count > $pageSize) {
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        
        $toplevel = 2;                                                                      //顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '214';                                                                //当前的二级菜单
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('orderlist', $orderList);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('third', 4);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('boxid', $boxId);
        $this->smarty->assign('starttime', $startTime);
        $this->smarty->assign('endtime', $endTime);
        $this->smarty->display('owPreOrder.htm');
    }
    
    /*
     * 显示补货单详情
     */
    public function view_showPreOrderDetail(){
        
        $orderId    = isset($_GET['orderid']) ? $_GET['orderid'] : '';
        $order_obj  = new  PreplenshOrderModel();
        $orderInf   = $order_obj->getPrePlenshOrderInfo($orderId);
        if (FALSE === $orderInf) {
        	goErrMsgPage(array('data'=>array('不存在的补货单'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=preOrderList'));
        	exit;
        }
        
        $box_obj    = new BoxManageModel();
        $pageSize   = 100;
        $count      = $box_obj->culBoxList($orderId);
        
        $pageObj    = new Page($count, $pageSize);
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'补货单详情'),
        );
        if ($count > $pageSize) {
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        $detailList = $box_obj->getListBoxInfo(" and replenshId='$orderId' ".$pageObj->limit);
		foreach ($detailList as &$box){
            $box['timestr'] = date('Y-m-d H:i:s', $box['addtime']);
            $box['statusstr']   = BoxManageModel::status2Name($box['status']);
            $box['userName']    = empty($box['sendScanUser'])   ? '' : getUserNameById($box['sendScanUser']);
        }
        
        $toplevel = 2;                                                                      //顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '214';                                                                //当前的二级菜单
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('detailList', $detailList);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('third', 4);
        $this->smarty->display('PreOrderDetail.htm');
    }
    
    /*
     * 退箱扫描
     */
    public function view_returnBxoScanPage(){
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'退箱扫描'),
        );
        $secondlevel = '214';
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('third', 4);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('returnBoxScan.htm');
    }
    
    /*
     * 修改补货单状态页面
     */
    public function view_changePreOrderStatusPage(){
        
        $orderId    = isset($_GET['orderid']) ? $_GET['orderid'] : FALSE;           //订单号
        if (empty($orderId)) {
        	goErrMsgPage(array('data'=>array('缺少订单号!'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=preOrderList'));
        	exit;
        }
        
        $pre_obj    = new PreplenshOrderModel();
        $orderInfo  = $pre_obj->getPrePlenshOrderInfo($orderId);
        if (FALSE === $orderInfo) {
        	goErrMsgPage(array('data'=>array('不存在的单号!'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=preOrderList'));
        	exit;
        }
        
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'修改补货单状态'),
        );
        $secondlevel = '214';
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('orderInfo', $orderInfo);
        $this->smarty->assign('third', 4);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('changePreOrderStatuspage.htm');
    }
    
    /*
     *修改补货单状态 
     */
    public function view_changePreOrderStatus(){
        $returnData = array('code'=>0, 'msg'=>'');
        $orderId    = isset($_GET['orderid']) ? intval($_GET['orderid']) : FALSE;           //订单号
        $arriveday  = isset($_GET['arriveday']) ? intval($_GET['arriveday']) : 0;
        if (FALSE === $orderId) {
        	$returnData['msg'] = '不存在的订单号!';
        	echo json_encode($returnData);
        	exit;
        }
        
        $status = isset($_GET['status']) ? intval($_GET['status']) : '';
        $pre_obj    = new PreplenshOrderModel();
        if (FALSE === PreplenshOrderModel::isValidStatusCode($status)) {
            $returnData['msg'] = '不正确的状态码!';
            echo json_encode($returnData);
            exit;
        }
        
        $change = $pre_obj->changePreOrderStatus($orderId, $status, $arriveday);
        if (FALSE === $change) {
        	$returnData['msg'] = '不正确的状态码!';
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code'] = 1;
            echo json_encode($returnData);
            exit;
        }
    }
}