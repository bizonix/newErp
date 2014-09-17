<?php
class OrderRefundDetailModel  extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	public function getRefundDetailList($orderRefundid){
		return $this->sql(" SELECT * FROM ".$this->getTableName()." WHERE orderRefundId=$orderRefundid AND is_delete=0 ")->limit("*")->select(array('mysql'), 1800);
	}
	/**
	 * 记录退款详情
	 * @param $array
	 * @return bool
	 * @author yxd
	 * */
	public function insertRefundDetail($refundDetail){
		$table   = C('DB_PREFIX')."order_refund_detail";
		return $this->sql("INSERT INTO {$table} SET ".array2sql($refundDetail))->insert();
	}
}