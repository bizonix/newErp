<?php

/*
 * 发货单明细表Model
 * ADD BY cmf 2014.7.22
 */
class WhShippingOrderdetailModel extends WhBaseModel {
	/**
     * WhShippingOrderdetailModel::getShipDetails()
     * 获取发货单详情
     * @param int $shipOrderId 发货单ID
     * @author Gary
     */
    public static function getShipDetails($shipOrderId){
    	self::initDB();
        $tablename  =   self::$tablename;
        $shipOrderId    =   intval(trim($shipOrderId));
        if(!$shipOrderId){
            return FALSE;
        }
        $sql    =   "select * from {$tablename} where shipOrderId = '{$shipOrderId}' and is_delete = 0 order by id DESC";
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
    	return $res;
    }
    
    /**
     * 获取波次对应的SKU列表
     * @param  $waveId:波次ID
     * @author cmf
     */
    public static function getShippingOrderSkuList($waveId){
		$sql = "select a.shipOrderId,a.sku,a.amount,a.positionId,a.pName,b.areaId,d.areaName,b.storey,b.storeId,c.waveId from wh_shipping_orderdetail a 
				left join wh_position_distribution b ON(a.positionId=b.id)
				left join wh_wave_shipping_relation c ON(c.shipOrderId=a.shipOrderId)
				left join wh_wave_area_info d ON(b.areaId=d.id)
				left join wh_wave_route_relation e ON(b.pName=e.name)
				where c.waveId='$waveId' order by b.storey DESC, e.route ASC, c.id ASC";
		$list = self::query($sql);
		return $list ? $list : array();
    }
    /**
     * WhShippingOrderdetailModel::getShipDetailsById()
     * 获取发货单表的状态和明细
     * @author 陈先钰
     * @param int $shipOrderId 发货单ID
     * @return
     */
    public static function getShipDetailsById($shipOrderId){
        self::initDB();
        $tablename  =   self::$tablename;
        $shipOrderId    =   intval(trim($shipOrderId));
        if(!$shipOrderId){
            return FALSE;
        }
        $sql    =   "select b.orderStatus,b.isSplit,a.* from {$tablename} a left join wh_shipping_order b on b.id = a.shipOrderId where b.id = '{$shipOrderId}' and b.is_delete = 0";
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
    	return $res;
    }
    /**
     * WhShippingOrderdetailModel::getDetailsByCombineSku()
     * 根据发货单和组合料号获取发货单明细信息
     * @param mixed $shipOrderId 发货单ID
     * @param mixed $combineSku组合料号
     * @return array
     */
    public static function getDetailsByCombineSku($shipOrderId,$combineSku){
         self::initDB();
        $tablename  =   self::$tablename;
        $shipOrderId    =   intval(trim($shipOrderId));
        if(!$shipOrderId){
            return FALSE;
        }
        $sql    =   "select * from {$tablename} where shipOrderId = '{$shipOrderId}' and combineSku = '{$combineSku}'";
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
    	return $res;
    }
    /**
     * WhShippingOrderdetailModel::getShipDetailUnionPcGoods()
     * 发货单料号详情关联料号信息表
     * @param int $shipOrderId
     * @return void
     */
    public static function getShipDetailUnionPcGoods($shipOrderId){
        self::initDB();
        $shipOrderId    =   intval($shipOrderId);
        $res            =   array();
        if($shipOrderId){
            $sql        =   'select a.sku, a.itemTitle, a.itemPrice, a.amount, a.pName, b.pmId, b.isPacking,b.packageType
                                ,b.goodsWeight,b.spu,c.name as goodsCategory from wh_shipping_orderdetail a left join pc_goods b
                                on a.sku = b.sku left join pc_goods_category c on c.path=b.goodsCategory 
                                where a.shipOrderId = "'.$shipOrderId.'" and b.is_delete = 0';
            //echo $sql;exit;
            $sql        =   self::$dbConn->query($sql);
            $res        =   self::$dbConn->fetch_array_all($sql);
        }
        return $res;
    }
    /**
     * WhShippingOrderdetailModel::updateShipDetailByShipOrderId()
     * @author cxy
     *  根据发货单号和SKU逻辑删除发货明细
     * @param mixed $shipOrderId发货单号
     * @param mixed $sku 真实SKU
     * @return
     */
    public static function updateShipDetailByShipOrderId($shipOrderId,$sku){
        self::initDB();
        $tablename      =   self::$tablename;
        $shipOrderId    =   intval(trim($shipOrderId));
        $sql            = "update {$tablename} set is_delete = 1 where shipOrderId = '{$shipOrderId}' and sku ='{$sku}' ";
        $query          =   self::$dbConn->query($sql);
       	if($query){
			return true;;	
		}else{
			return false;	
		}
    }
    /**
     * WhShippingOrderdetailModel::select_datail_category()
     * 根据发货单获取料号详情关联发货明细表
     * @author 陈先钰
     * @param mixed $shipOrderId
     * @return
     */
    public static function select_datail_category($shipOrderId){
        self::initDB();
        $shipOrderId    =   intval(trim($shipOrderId));
        if(!$shipOrderId){
            return FALSE;
        }
        $sql    =   "select a.*,b.goodsCategory from wh_shipping_orderdetail a left join pc_goods b on a.sku = b.sku  where shipOrderId = '{$shipOrderId}' and a.is_delete = 0 ";
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
    	return $res;
    
    }
    
    
}
?>
