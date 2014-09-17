<?php
/**
 * 黑名单管理
 * 
 * @add by yxd ,date 2014/06/10
 */
class test_formatView extends BaseView{
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	
public function view_overweightorder(){
		$order_id = 1998850;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            
            
		$info = M('orderManage')->handleOverWeightOrder($order);
		var_dump($info);
	}
	public function view_isfulfillByAmazon(){
		$order_id = 1998850;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isfulfillByAmazon();    
		var_dump($info);
	}
public function view_isValidPaypalAccount(){
		$order_id = 1573652;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isValidPaypalAccount();    
		var_dump($info);
	}
public function view_isInBlackList(){
		$order_id = 1573652;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isInBlackList();  
        //$arr = M('order')->getAllRunSql();  
		var_dump($info);
	}
public function view_isMissingInfoOrder(){
		$order_id = 1573652;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isMissingInfoOrder();  
        //$arr = M('order')->getAllRunSql();  
		var_dump($info);
	}
public function view_isContainInvalidSkuOrder(){
		$order_id = 1573652;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isContainInvalidSkuOrder();  
        //$arr = M('order')->getAllRunSql();  
		var_dump($info);
	}
public function view_isLeaveWordOrder(){
		$order_id = 1998850;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isLeaveWordOrder();  
        //$arr = M('order')->getAllRunSql();  
		var_dump($info);
	}
public function view_isSuperLargeOrder(){
		$order_id = 1998850;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
        $format = F('FormatOrder');
        $format->setOrder($order);    
        $info = $format->isSuperLargeOrder();  
        //$arr = M('order')->getAllRunSql();  
		var_dump($info);
	}
	
}
?>