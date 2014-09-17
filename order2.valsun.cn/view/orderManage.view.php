<?php
/*
 * 订单通用操作类视图
 * @author herman.xi
 * @modify by lzx ,date 20140603
 */
class OrderManageView extends BaseView {
	
    public function __construct() {
    	parent::__construct();
    }

    /**
     * 合并订单
     */
    public function view_combineOrder(){
    	return $this->ajaxReturn(A('OrderManage')->act_combineOrder(), A('OrderManage')->act_getErrorMsg());
    }
    
	/**
     * 合并包裹
     */
    public function view_combineOrderPackage(){
    	return $this->ajaxReturn(A('OrderManage')->act_combineOrderPackage(), A('OrderManage')->act_getErrorMsg());
    }
    
    /**
     * 获取手动拆分订单信息
     */
    public function view_getSplitOrder(){
    	 return $this->ajaxReturn(A('Order')->act_getSplitOrder(), A('Order')->act_getErrorMsg());
    }
    
	/**
     * 手动拆分订单
     */
    public function view_handSplitOrder(){
    	return $this->ajaxReturn(A('OrderManage')->act_handSplitOrder(),A('OrderManage')->act_getErrorMsg());
    }
    
    /**
     * 超重拆分
     */
    public function view_overWeightSplit(){
    	return $this->ajaxReturn(A('OrderManage')->act_overWeightSplit(), A('OrderManage')->act_getErrorMsg());
    }
	/**
     * 复制订单补寄，只针对已完结订单!!!
     */
    public function view_copyOrderForResend(){
    	$this->ajaxReturn(A('OrderManage')->act_copyOrderForResend(), A('OrderManage')->act_getErrorMsg());
    }
    
    /**
     * 复制订
     */
    public function view_copyOrder(){
    	$this->ajaxReturn(A('OrderManage')->act_copyOrder(), A('OrderManage')->act_getErrorMsg());
    }
    /**
     * 获取复制订单补寄信息
     */
    public function view_getsendReplacement(){
    	$this->ajaxReturn(A('OrderManage')->act_getSendReplacement(), array('200'=>get_promptmsg(200,'获取补寄信息')));
    }
    
    /**
     * 获取一级状态下的对应的文件列表
     */
    public function view_getStatusMenu(){
    	$this->ajaxReturn(A('OrderManage')->act_getStatusMenu(), array('200'=>get_promptmsg(200,'获取状态信息')));
    }
    
	/**
     * 申请打印订单(打印整个文件夹)
     */
    public function view_applyAllPrintOrder(){
    	$this->ajaxReturn($data, $prompt);
    }
    
    /**
     * 获取取消的合并订单
     */
    public function view_findCombineOrder(){
    	$this->ajaxReturn(A('OrderManage')->act_findCombineOrder(), array('200'=>get_promptmsg(200,'获取合并订单')));
    }
	/**
     * 取消合并包裹关系
     */
    public function view_cancelOrderPackageRelation(){
   		$this->ajaxReturn(A('OrderManage')->act_cancelOrderPackageRelation(), A('OrderManage')->act_getErrorMsg());
    }
    
    /**
     * 取消交易  （取消交易，暂不寄，废弃订单，异常处理） 
     */
    public function view_cancelDeal(){
    	$this->ajaxReturn(A('OrderManage')->act_cancelDeal(), A('OrderManage')->act_getErrorMsg());
    }
	/**
     * 移动订单
     */
    public function view_batchMove(){
    	$this->ajaxReturn(A('OrderManage')->act_batchMove(), A('OrderManage')->act_getErrorMsg());
    }
    /**
     * 计算运费
     */
    /* public function view_calshippingfee(){
    	$this->ajaxReturn(A('OrderManage')->act_calshippingfee(), A('OrderManage')->act_getErrorMsg());
    } */
    /**
     * 申请退款
     */
    public function view_applyRefund(){
    	$this->ajaxReturn(A('OrderManage')->act_applyRefund(), A('OrderManage')->act_getErrorMsg());
    }
    /**
     * 确认添加申请退款信息
     */
    public function view_addRefundInfo(){
    	$this->ajaxReturn(A('OrderManage')->act_addRefundInfo(), A('OrderManage')->act_getErrorMsg());
    }
    /**
     * 启动发货
     */
    public function view_doshipping(){
    	$this->ajaxReturn(A('OrderManage')->act_doshipping(), A('OrderManage')->act_getErrorMsg());
    }
}   