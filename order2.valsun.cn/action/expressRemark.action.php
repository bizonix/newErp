<?php
/**
 * Created by dy.
 * Date: 14-8-15
 */
class ExpressRemarkAct extends CheckAct{
    public function __construct(){
        parent::__construct();
    }

    public function act_getRemark(){
        $rfData         = array();
        $spuList        = array();
        $id             = $_POST['id'];
        $remarkData     = array();
        $transportName  = '';
        //$transportId    = $_POST['transportId'];        //最终运输方式的Id
        F('Order');
        if(empty($id)){
            self::$errMsg['104']  = '没有正确的获取订单ID';
        }else{
            $orderList = M('Order')->getFullUnshippedOrderById(array($id));
            foreach($orderList as $v){
                $order       = $v['order'];
                $orderDetail = $v['orderDetail'];
                $rfData['actualTotal'] = $order['currency'].$order['actualTotal'];
                $rfData['transport']   = $transportName;
                foreach($orderDetail as $skuList){
                    $sku            = $skuList['orderDetail']['sku'];
                    $orderskulist   = get_orderskulist($sku);
                    if(!empty($orderskulist)){
                        $realsku        = $orderskulist['realsku'];
                        foreach($realsku as $v){
                            $spu = $v['spu'];
                        }
                        if(!empty($spu)){
                            $spuList[]  = $spu;
                        }
                    }
                }
            }

            $spuList  = array_unique($spuList);
            $orderRemarkList = M('ExpressRemark')->getRemarkById($id);
            if(is_array($orderRemarkList)){
                foreach($orderRemarkList as $v){
                    $remarkSku              = $v['spu'];
                    $remarkData[$remarkSku] = $v;
                }
            }
            foreach($spuList as $spu){
                $rfData['skuList'][$spu]= $remarkData[$spu];
            }
        }
        return $rfData;
    }

    public function act_editExpressRemark(){
        $data        = $_POST;
        $id          = $data['id'];
        $spuList     = $_POST['spu'];
        $num         = sizeof($spuList);
        $declaration = array();
        for($i=0;$i<$num;$i++){
            $declaration[$i]['spu']         = $_POST['spu'][$i];
            $declaration[$i]['cnTitle']     = $_POST['cnTitle'][$i];
            $declaration[$i]['enTitle']     = $_POST['enTitle'][$i];
            $declaration[$i]['brand']       = $_POST['brand'][$i];
            $declaration[$i]['material']    = $_POST['material'][$i];
            $declaration[$i]['cnMaterial']  = $_POST['cnMaterial'][$i];
            $declaration[$i]['hamcodes']    = $_POST['hamcodes'][$i];
            $declaration[$i]['unit']        = $_POST['unit'][$i];
            $declaration[$i]['amount']      = $_POST['amount'][$i];
            $declaration[$i]['price']       = $_POST['price'][$i];
        }

        if(is_array($declaration)){
            foreach($declaration as $k=>$list){
                $spu = $list['spu'];
                if(M('ExpressRemark')->isExistOmOrderId($id,$spu)){
                    if(M('ExpressRemark')->updateRemark($id,$list,$spu)){
                        self::$errMsg[200] = '更新成功';
                    }
                }else{
                    $inserData = array();
                    $inserData[0] = $list;
                    M('ExpressRemark')->setInsertOrderId($id);
                    if(M('ExpressRemark')->insertOrderDeclarationContent($inserData)){
                        self::$errMsg[200] = '添加成功';
                    }
                }
            }
        }

    }
}