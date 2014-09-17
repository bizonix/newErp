<?php
/*
 *操作日志model
 *ADD BY xyd
 * 
 */
class SuperAmountSkuModel extends CommonModel{
	
	public function __construct(){
		parent::__construct();
	}
	
    /**
     * 写入LOG日志
     * @param $sql              //修改订单的SQL语句
     * @param $note             //修改订单的操作
     * @param $omOrderId        //订单ID
     * @return bool
     */
    function addSuperAmountSku($data){
      
        $table = C('DB_PREFIX').'records_order_audit';
        $fdata = $this->formatInsertField($table, $data);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;var_dump(self::$errMsg);exit;
            return false;
        }
        
        $this->sql("INSERT ".$table." SET ".array2sql($fdata))->insert();
    }
    function checkIsAuditted($order_id){
    	$table = C('DB_PREFIX').'records_order_audit';
    	
    
    	//先检查如果该订单是超重订单拆分出来的子订单，不拦截（等于审核通过）
    	$sql = "select main_order_type from om_records_splitOrder where split_order_id=".$order_id;
    	$result = $this->sql($sql)->limit(1)->select();
    	if(!empty($result) && $result[0]['main_order_type'] == 1){
    		return true;
    	}
    	
    	/**
    	//再检查超大审核是否通过
    	$sql = "select distinct auditStatus from ".$table." where omOrderId=".$order_id;
    	$result = $this->sql($sql)->select();
    	if(empty($result)){
    		return false;
    	}
    	
    	foreach($result as $row){
    		if($row['auditStatus'] == 2){//拦截
    			return false;
    		}elseif($row['auditStatus'] == 0){//待审核
    			return false;
    		}
    	}
    	/**/
    	
    	
    	return true;
    }
}
?>