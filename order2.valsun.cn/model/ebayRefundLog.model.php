<?php
/**
 * 退款操作日志接口
 * @author姚晓东
 *
 */
class EbayRefundLogModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取退款日志记录
	 */
	public function getRefundLogList($condition){
		$condition    = implode(" and ", array2where($condition));
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE $condition ")->limit("*")->select(array("mysql"),1800);
	}
}