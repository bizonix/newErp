<?php
/**
 * 退款sku详情
* @author姚晓东
*
*/
class EbayRefundLogDetailModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	                
	public function getRefundDetailLogList($order_id){
		return $this->sql(" SELECT * FROM ".$this->getTableName()." WHERE order_id=$order_id ")->limit("*")->select(array('mysql'), 1800);
	}
}
?>