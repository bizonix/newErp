<?php
/*
 * 订单拆分相关格式化
 * @add by : zqt ,date : 20140806
 */

class SplitOrder{
	
	private $errMsg = array();//装载拦截过程中的异常信息，异常信息需要提交到数据库统一管理
	private $orderData = array();
	
	public function __construct(){

	}
	
	/**
	 * 赋值订单变量
	 * @param array $orderData
	 * @author zqt
	 */
	public function setOrder($orderData){
		$this->orderData = $orderData;
	}
	
	/**
	 * 获取错误信息
	 * @eturn array 错误信息数据需要打到订单相关表中，记录错误编号用于订单查询
	 * @author lzx 
	 */
	public function getErrMsg(){
		return $this->errMsg;
	}
	
	/**
	 * 仓库订单拆分逻辑,调用该方法前必须调用FormatOrder中的MarkOverSeaOrder标记订单仓库类型并插入到数据库中，然后再进行拆分
	 */
	public function splitOrderForStore(){	    
	    $orderData = $this->orderData;
		if(empty($orderData) || empty($orderData['order']['id'])){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
        $orderStore = $orderData['order']['orderStore'];
        if(empty($orderStore)){
            $this->errMsg[10126] = get_promptmsg(10126);//异常仓库订单
            return false;
        }elseif(in_array($orderStore, array(2))){//判断该订单是否处于包含海外料号和国内料号订单(orderStore=2),后面可能会扩展多个仓混合订单，所以这里用in_array
            $storeIdArr = array();//定义一个数组存放orderData中sku所在仓库id;
            $orderDetail = $orderData['orderDetail'];
            if(!empty($orderDetail)){
                $returnArr = array();//要返回的订单数组信息，可能返回多个（拆分后）也可能只返回一个（不拆分）
                $detailArr = array();//这个是模拟orderManage发货单拆分的格式（将storeId当成是虚拟发货单id），按照sku及数量对订单进行拆分
                foreach($orderDetail as $value){
                    $storeIdArr[] = intval($value['orderDetail']['storeId'])>0?$value['orderDetail']['storeId']:0;
                }
                $storeIdArr = array_unique($storeIdArr);
                foreach($storeIdArr as $storeId){
                    foreach($orderDetail as $value){
                        if($storeId == $value['orderDetail']['storeId']){
                            $detailArr[$storeId] = array($value['orderDetail']['sku']=>$value['orderDetail']['amount']);
                        }                        
                    }
                }
                $newOrderDataArr = M('OrderManage')->splitOrderWithOrderDetail($orderData['order']['id'],$detailArr);
                return 'SPLITTED';
            }else{
                $this->errMsg[10075] = get_promptmsg(10075);//明细为空，不能拆
                return false;
            }
        }else{
            $this->errMsg[10127] = get_promptmsg(10127);//不是混合仓订单，不需要拆分
            return false;
        }
	}
}
?>