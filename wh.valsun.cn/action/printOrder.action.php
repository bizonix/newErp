<?php

/** 
 * @author 涂兴隆
 * 发货单打印
 */
class PrintOrderAct
{

    public static $errCode = 0;
    public static $errMsg = '';
    /**
     * 构造函数
     */
    function __construct ()
    {
    	
    }
    
    /*
     * 申请配货
     */
    public function act_applyfor(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if($pid == ''){
            self::$errCode = 0;
            self::$errMsg  = '输入非法值!';
            return ;
        }
        
        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }
        $o_count = count($idar);
        $orderist = OrderPrintListModel::getPrintList('*', ' where id in ('.implode(',', $idar).') and status!=1001 and storeId=1');
        $n_count = count($orderist);
		if($o_count!=$n_count){
			self::$errCode = 0;
            self::$errMsg  = '申请配货不能包含未打印地址条订单或包含B仓订单';
            return ;
		}

        $oidar = array();   //发货单id数组
        foreach ($orderist as $orlval){ //验证合法性
            if ($orlval['is_delete']==1) {
                $data = array('data'=>array('包含已经删除单号!','单号id为'.$orlval['id']),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
                goErrMsgPage($data);
                exit;
            }
            $tempar = explode(',', $orlval['orderIds']);
            $oidar = array_merge($oidar, $tempar);
        }
        
        $po_obj = new PackingOrderModel();
        $qresult = $po_obj->changeStatusToWaitGetGoods(implode(',', $oidar));
        
        OrderPrintListModel::deleteAsetOfPrint(implode(',', $idar));
		printLabelModel::inserRecords($oidar,$_SESSION['userId']);
        if ($qresult) {
			$time = time();
			foreach($oidar as $oid){
				WhPushModel::pushOrderStatus($oid,'STATESHIPPED_BEPICKING',$_SESSION['userId'],$time);
			}
        	self::$errCode = 1;
        	self::$errMsg = '申请成功';
        	return ;
        } else {
            self::$errCode = 0;
            self::$errMsg = '失败';
            return FALSE;
        }
    }
    
    /*
     * 解锁打印单
     * $id 要解锁的id
     */
    public function act_unlockPrint(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if($pid == ''){
            self::$errCode = 0;
            self::$errMsg = '输入非法值!';
            return ;
        }
        
        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }
        
        $orderist = OrderPrintListModel::getPrintList('*', ' where id in ('.implode(',', $idar).')');
        if(empty($orderist)){   //订单不存在
            $data = array('data'=>array('单号不合法!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
        //print_r( $idar);exit;
        $ulock = OrderPrintListModel::unlockAsetOfPrint(implode(',', $idar));
        
        if ($ulock) {
        	self::$errCode =1;
        	self::$errMsg='解锁成功';
        	return ;
        } else {
            self::$errCode = 0;
            self::$errMsg = '解锁失败！';
            return ;
        }
    }
	
	/*
     * 退回待处理
     */
    public function act_backwait(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if($pid == ''){
            self::$errCode = 0;
            self::$errMsg  = '输入非法值!';
            return ;
        }
        
        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }
		
        $orderist = OrderPrintListModel::getPrintList('*', ' where id in ('.implode(',', $idar).')');
		if(empty($orderist)){   //订单不存在
            $data = array('data'=>array('单号不合法!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }

        $oidar = array();   //发货单id数组
        foreach ($orderist as $key=>$orlval){ //验证合法性
            if ($orlval['is_delete']==1) {
                unset($orderist[$key]);
            }
            $tempar = explode(',', $orlval['orderIds']);
            $oidar = array_merge($oidar, $tempar);
        }
        $po_obj = new PackingOrderModel();
        $qresult = $po_obj->changeStatusToWaiting(implode(',', $oidar));
        
        OrderPrintListModel::deleteAsetOfPrint(implode(',', $idar));
        if ($qresult) {
        	self::$errCode = 1;
        	self::$errMsg  = '退回成功';
        	return ;
        } else {
            self::$errCode = 0;
            self::$errMsg  = '退回失败';
            return FALSE;
        }
    }
	
	/*
     * 订单锁定
     */
    public function act_lockPrin(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if($pid == ''){
            self::$errCode = 0;
            self::$errMsg  = '输入非法值!';
            return ;
        }
        
        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }
		$lockresult = OrderPrintListModel::lockPrint($idar);         //加锁
        if($lockresult == false){
            self::$errCode = 0;
            self::$errMsg  = '加锁失败!';
            return ;
        }else{
			self::$errCode = 1;
            self::$errMsg  = '加锁成功!';
            return ;
		}
        
    }
    
	/*
     * B仓申请提货
     */
    public function act_applyforB(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if($pid == ''){
            self::$errCode = 0;
            self::$errMsg  = '输入非法值!';
            return ;
        }
        
        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }
        $o_count = count($idar);
        $orderist = OrderPrintListModel::getPrintList('*', ' where id in ('.implode(',', $idar).') and status!=1001 and storeId=2');
        $n_count = count($orderist);
		if($o_count!=$n_count){
			self::$errCode = 0;
            self::$errMsg  = '申请提货不能包含未打印地址条订单或包含A仓订单';
            return ;
		}

        $oidar = array();   //发货单id数组
        foreach ($orderist as $orlval){ //验证合法性
            if ($orlval['is_delete']==1) {
                $data = array('data'=>array('包含已经删除单号!','单号id为'.$orlval['id']),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
                goErrMsgPage($data);
                exit;
            }
            $tempar = explode(',', $orlval['orderIds']);
            $oidar = array_merge($oidar, $tempar);
        }
        
        $po_obj = new PackingOrderModel();
        $qresult = $po_obj->changeStatusToWaitGetGoodsB(implode(',', $oidar));
        
        OrderPrintListModel::deleteAsetOfPrint(implode(',', $idar));
		printLabelModel::inserRecords($oidar,$_SESSION['userId']);
        if ($qresult) {
			$time = time();
        	self::$errCode = 1;
        	self::$errMsg = '申请成功';
        	return ;
        } else {
            self::$errCode = 0;
            self::$errMsg = '失败';
            return FALSE;
        }
    }
	
}

?>