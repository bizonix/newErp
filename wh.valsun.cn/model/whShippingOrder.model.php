<?php

/*
 * 发货单表Model
 * ADD BY cmf 2014.7.22
 */
class WhShippingOrderModel extends WhBaseModel {
	
    /**
     * WhShippingOrderModel::get_order_info()
     * 获取发货单信息
     * @param array $select 多个数组，单个字符
     * @param array $where 条件数组
     * @param bool $fetch_one  是否只获取一条记录
     * @author Gary
     * @return void
     */
    public static function get_order_info($select, $where, $fetch_one = FALSE){
        self::initDB();
        $select     =   array2select($select);
        $where      =   array2where($where);
        $sql        =   "select $select from ".self::$tablename." where $where and is_delete = 0";
        $sql        =   self::$dbConn->query($sql);
        $func       =   $fetch_one ? 'fetch_array' : 'fetch_array_all';
        $res        =   self::$dbConn->$func($sql);
        return $res;
    }
    
    /**
     * WhShippingOrderModel::get_order_info_union_table()
     * 获取发货单详细信息、跟踪号、重量、运费等
     * @param int $shipOrderId
     * @return void
     */
    public static function get_order_info_union_table($shipOrderId){
        self::initDB();
        $shipOrderId    =   intval($shipOrderId);
        $res            =   array();
        if($shipOrderId){
            $sql        =   "select a.*, b.tracknumber, a.orderWeight as actualWeight, c.actualShipping from wh_shipping_order a left join
                                wh_order_tracknumber b on a.id= b.shipOrderId left join wh_shipping_order_records c 
                                on a.id = c.shipOrderId where a.id = '{$shipOrderId}'";
            //echo $sql;exit;
            $sql        =   self::$dbConn->query($sql);
            $res        =   self::$dbConn->fetch_array($sql);
        }
        return $res;
    }
    
    /**
     * WhShippingOrderModel::get_order_detail_info()
     * 根据发货单id获取发货单基本信息及料号明细
     * @param int $shipOrderId
     * @param bool $fetch_one
     * @author Gary
     * @return void
     */
    /*public static function get_order_detail_info($shipOrderId, $fetch_one){
        self::initDB();
        $shipOrderId    =   intval(trim($shipOrderId));
        $sql            =   "select a.*, b.* from wh_shipping_order a left join wh_shipping_orderdetail b on a.id = b.shipOrderId where a.id = '{$shipOrderId}'";
        $sql            =   self::$dbConn->query($sql);
        $res            =   self::$dbConn->fetch_array_all($sql);
        print_r($res);exit;
        return $res;
    }*/
    /**
     * WhShippingOrderModel::update_shipping_order_by_id()
     * 更新发货单的状态
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @param int $status 状态
     * @return void
     */
    public static function update_shipping_order_by_id($where,$set){
        self::initDB();
        $tablename      =   self::$tablename;
        $sql            = "update {$tablename} set $set  where $where";
        $query          =   self::$dbConn->query($sql);
       	if($query){
			return true;;	
		}else{
			return false;	
		}
    }
	
	/**
     * WhShippingOrderModel::update_shipping_order()
     * 更新发货单的相关字段
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @param int $status 状态
     * @return void
     */
    public static function update_shipping_order($where,$set){
        self::initDB();
        $sql            = "update ".self::$tablename." set {$set} where {$where} ";
        $query          = self::$dbConn->query($sql);
		
		return self::affected_rows();
    }
    /**
     * WhShippingOrderModel::select_shiping_by_id_country()
     * 根据美国的国家名和发货单号获取美国州名简称和发货单的信息
     * @author cxy 
     * @param mixed $country 国家名
     * @param mixed $shipOrderId 发货单号
     * @return
     */
    public static function select_shiping_by_id_country($country,$shipOrderId){
        self::initDB();
        $shipOrderId    =   intval($shipOrderId);
        $country        = trim($country);
        $res            =   array();
        if($shipOrderId){
            $sql        =   "select a.*, b.ups_code from wh_shipping_order a left join
                                wh_wave_ups_country b on a.countryName= b.country  where a.id = '{$shipOrderId}' and a.countryName = '{$country}' and is_delete = 0 LIMIT 1";
            //echo $sql;exit;
            $sql        =   self::$dbConn->query($sql);
            $res        =   self::$dbConn->fetch_array($sql);
        }
        return $res;
    }
    /**
     * WhShippingOrderModel::select_shipping_order_relation()
     * 根据发货单号获取订单系统的编号
     * @author 陈先钰
     * @param mixed $shipOrderId 发货单号
     * @param integer $storeId仓库ID，默认为1赛维网络深圳仓库
     * @param integer $systemId 订单系统ID1默认订单系统
     * @return
     */
    public static function select_shipping_order_relation($shipOrderId,$storeId = 1,$companyId = 1){
        self::initDB();
        $shipOrderId    =   trim(intval($shipOrderId));
        $res            =   array();
        if($shipOrderId){
            $sql        =   "select originOrderId from wh_shipping_order_relation  where shipOrderId = '{$shipOrderId}' and companyId = '{$companyId}' and storeId = '{$storeId}' LIMIT 1";
           // echo $sql;exit;
            $sql        =   self::$dbConn->query($sql);
            $res        =   self::$dbConn->fetch_array($sql);
        }
        return $res;
   
    }
}
?>
