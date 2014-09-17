<?php

/**
 *  add by 姚晓东 2014/08/13
 */
 
class OrderRefundModel  extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取退款记录
	 */
	public function getRefundList($condition){
		$condition    = implode(" and ", array2where($condition));
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE $condition ")->limit("*")->select(array("mysql"),1800);
	}
	/**
	 * 获取指定订单下已申请退款的金额
	 * @param int id
	 * @return  array
	 * @author yxd
	 */
	public function getRefundSum($id){
		$result    = $this->sql('SELECT totalSum, refundSum FROM  '.$this->getTableName()." WHERE omOrderId=$id AND status !=2 AND is_delete=0")->limit("*")->select();
		if( count($result) == 0 ) {
            return array('totalSum' => 0, 'refundSum' => 0);            
        }
        $totalSum = isset($result[0]['totalSum']) ? $result[0]['totalSum'] : 0;
        $refundedSum = 0;
        foreach($result as $refund) {
        	$refundedSum += $refund['refundSum'];
        }
        return array('totalSum' => $totalSum, 'refundSum' => $refundedSum);		
	}
	
	
	
}
?>
