<?php
/**
 * WhShippingOrderRelationModel
 * 货单订单关系表
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class WhShippingOrderRelationModel extends WhBaseModel {
	/**
	 * WhShippingOrderRelationModel::get_orderId()
	 * 获取发货单对应的订单号 
	 * @param mixed $shipOrderId 单个字符串 多个数组  123456 or array(123456,456789)
	 * @return void
	 */
	public static function get_orderId($shipOrderId){
	   self::initDB();
       $shipOrderIds=   is_array($shipOrderId) ? array_map('intval', $shipOrderId) : array(intval($shipOrderId));
       $sql         =   'select originOrderId,shipOrderId from '.self::$tablename.' where shipOrderId in ('.implode(',', $shipOrderIds).') AND is_delete = 0';
       //echo $sql;
       $sql         =   self::$dbConn->query($sql);
       $res         =   self::$dbConn->fetch_array_all($sql);
       if(empty($res)){
            return 0;
       }
       if(is_array($shipOrderId)){
            $return =   array();    
            foreach($res as $val){
                $return[$val['shipOrderId']]    =   $val['originOrderId'];
            }
       }else{
            $return =   $res[0]['originOrderId'];
       }
       return $return;
	}
	
	/**
	 * 通过原始订单id获取发货单id
	 * @param array $originOrderId
	 * @return array
	 * @author czq
	 */
	public static function get_shipOrderId($originOrderId){
		self::initDB();
		$originOrderIds 	=   is_array($originOrderId) ? array_map('intval', $originOrderId) : array(intval($originOrderId));
		$sql        	 	=   'select originOrderId,shipOrderId from '.self::$tablename.' where originOrderId in ('.implode(',', $originOrderIds).') AND is_delete = 0';
		//echo $sql;
		$sql         =   self::$dbConn->query($sql);
		$res         =   self::$dbConn->fetch_array_all($sql);
		if(empty($res)){
			return 0;
		}
		if(is_array($shipOrderId)){
			$return =   array();
			foreach($res as $val){
				$return[$val['originOrderId']]    =   $val['shipOrderId'];
			}
		}else{
			$return =   $res[0]['shipOrderId'];
		}
		return $return;
	}
      /**
     * WhShippingOrderRelationModel::update_shipping_by_orderId()
     * 根据发货单更新订单编号
     * @author cxy
     * @param mixed $where 
     * @param mixed $set
     * @return
     */
    public static function update_shipping_by_orderId($where,$set){
        self::initDB();
        $tablename  =   self::$tablename;
        $sql            = "update {$tablename} set $set  where $where";
        $query          =   self::$dbConn->query($sql);
       	if($query){
			return true;;	
		}else{
			return false;	
		}
        
    }
}
?>
