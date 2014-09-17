<?php
/**
 * Pda_orderOutBModel
 * B仓订单出库
 * @package 仓库系统
 * @author Gary(yym)
 * @copyright 2014
 * @access public
 */
class Pda_orderOutBModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		//mysql_query('SET NAMES UTF8');
	}
    
    
    /**
     * Pda_orderOutBModel::insertOrderRecord()
     * 插入订单明细表数据
     * @param int $orderId
     * @param array($insert)
     * @return
     */
    public static function insertOrderRecord($insert)
    {
   	    self :: initDB();
        $sql    =   'insert into wh_orderb_history set '.array2sql($insert);
        $sql        =   self::$dbConn->query($sql);
        return $sql;
    }
	
	/**
	 * Pda_orderOutBModel::selectOrderRecord()
	 * 获取订单详细料号信息
	 * @param $where
	 * @return void
	 */
	public static function selectOrderRecord($where){
	   self :: initDB();
	   $sql    =   'select * from wh_orderb_history where '.array2where($where);
       $sql    =   self::$dbConn->query($sql);
       $res    =   self::$dbConn->fetch_array_all($sql);
       return $res;  
	}
    
    public static function updateOrderRecord($where, $update){
       self :: initDB();
	   $sql    =   'update wh_orderb_history set '.array2sql($update).' where '.array2where($where);
       //echo $sql;exit;
       $sql    =   self::$dbConn->query($sql);
       return $sql;  
    }
}
?>
