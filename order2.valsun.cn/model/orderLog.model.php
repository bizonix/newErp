<?php
/*
 *操作日志model
 *ADD BY yxd
 * 
 */
class OrderLogModel extends CommonModel{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 操作日志
	 * @return array
	 * @author yxd
	 */
	public function getOrderLogList(){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE is_delete=0")->limit('*')->select(array('cache', 'mysql'), 1800);
	}

    /**
     * 写入LOG日志
     * @param $sql              //修改订单的SQL语句
     * @param $note             //修改订单的操作
     * @param $omOrderId        //订单ID
     * @return bool
     */
    function orderOperatorLog($sql,$note,$omOrderId){
        $nowtime                  =   time();
        $noteArr                  =   array();
        $noteArr['operatorId']    =   $_SESSION['sysUserId']?$_SESSION['sysUserId']:0;
        $noteArr['omOrderId']     =   $omOrderId;
        $noteArr['operatorNote']  =   $note;
        $noteArr['sql']           =   addslashes($sql);
        $noteArr['createdTime']   =   $nowtime;

        $table = C('DB_PREFIX').'order_logs';
        $fdata = $this->formatInsertField($table, $noteArr);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;
            return false;
        }
        $this->sql("INSERT ".$table." SET ".array2sql($noteArr))->insert();
    }
}
?>