<?php
/*
 * 海外仓备货单数据同步功能
 */
class SyncPreGoodsOrderAct {
    public static $errCode    = 0;
    public static $errMsg     = '';
    
    /*
     * 接受备货单数据
     */
    public function act_syncPreGoodsOrderInfo(){
        $returnData = array('code'=>'fail', 'msg'=>'');                         //返回的信息
        $orderSn    = isset($_POST['orderSn']) ? trim($_POST['orderSn']) : '';    //备货单号
        $owner      = isset($_POST['owner']) ? trim($_POST['owner']) : '';        //申请人
        $createTime = isset($_POST['createtime']) ? trim($_POST['createtime']) : '';//备货单生成时间
//         print_r($_POST);
        if (empty($orderSn) || empty($owner) || empty($createTime)) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = '缺少参数';
            return $returnData;
        }
        
        $skuList    = json_decode($_POST['data'], TRUE);                             //备货单sku列表

        $preGoods_obj   = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGoods_obj->getOrderInfo($orderSn);
        if (FALSE !== $orderInfo) {                                             //备货单已经存在
        	$returnData['code']    = 'success';
        	$returnData['msg']     = 'order has exist';
        	return $returnData;
        }
        
        $orderInfo  = array();
        $orderInfo['orderSn']       = $orderSn;
        $orderInfo['createTime']    = $createTime;
        $orderInfo['owner']         = $owner;
        
        $skuarray   = array();
        foreach ($skuList as $key=>$row){
            $skuarray[]    = array('sku'=>$key, 'amount'=>$row);
        }
//         print_r($skuarray);exit;
        
        $addResult  = $preGoods_obj->addNewPreGoodsOrder($orderInfo, $skuarray);
        if (FALSE === $addResult) {
        	$returnData['code']    = 'fail';
        	$returnData['msg']     = PreGoodsOrdderManageModel::$errMsg;
        	return $returnData;
        } else {
            $returnData['code']    = 'success';
            $returnData['msg']     = '';
            return $returnData;
        }
    }
    
    /*
     * 海外仓备货单数据修改
     */
    public function act_modifyPreGoodsSku(){
        $returnData = array('code'=>'fial', 'msg'=>'', 'num'=>0);
        $orderSn    = isset($_GET['ordersn']) ? trim($_GET['ordersn']) : '';    //备货单号
        $sku        = isset($_GET['sku']) ? trim($_GET['sku']) : '';            //sku
        $amount     = isset($_GET['amount']) ? trim($_GET['amount']) : '';      //修改数据
        $operator   = isset($_GET['operator']) ? intval($_GET['operator']) : '';//修改人Id
//         print_r($_GET);exit;
        if (empty($orderSn) || empty($sku) || empty($operator)) {
        	$returnData['code']    = 'fail';
        	$returnData['msg']     = '缺少参数';
        	return $returnData;
        }
        
        $preGoods_obj   = new PreGoodsOrdderManageModel();
        
        $orderInfo      = $preGoods_obj->getOrderInfo($orderSn);
        if (FALSE === $orderInfo) {
        	$returnData['code']    = 'fail';
        	$returnData['msg']     = '不存在的备货单号!';
        	return $returnData;
        }
        
        $skuInfo    = $preGoods_obj->getSKUinfo($orderInfo['id'], $sku);
        if (FALSE === $skuInfo) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = 'sku不存在!';
            return $returnData;
        }
        
        if (intval($skuInfo['scantnum']) > $amount ) {                                  //如果修改的数量小于已经配货的数量 则报错
        	$returnData['code']    = 'fail';
            $returnData['msg']     = '已配货数量大于修改数量！';
            return $returnData;
        }
        
        $reuslt = $preGoods_obj->updateSkuAmount($orderInfo['id'], $sku, $amount, $orderSn, $operator);
        if (FALSE === $reuslt) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = PreGoodsOrdderManageModel::$errMsg;
            return $returnData;
        } else {
        	$preGoods_obj->updPreOrderStatus($orderSn);//修改数量后验证备货单号是否已配货完成
            $returnData['code']    = 'success';
            $returnData['msg']     = '';
            $returnData['num']     = $amount;
            return $returnData;
        }
    }
}
