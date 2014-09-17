<?php

/**
 * 配货单发货单关系表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveShippingRelationModel extends WhBaseModel {
	
	/**
	 *	获取多SKU波次对应的全部发货单
	 *	注：单SKU波次对应发货单调用：WhWaveScanRecordModel::getShipOrders($waveId, $shipOrderId);
	 *	@param  $waveId:波次
	 *	@param  $shipOrderId:发货单号
	 *	@author cmf
	 */	
	public static function getShipOrders($waveId = '', $shipOrderId = '') {
		$sql = "select b.transportId, a.waveId, a.shipOrderId from wh_wave_shipping_relation a 
				left join wh_shipping_order b ON(a.shipOrderId=b.id)
				where a.is_delete=0 AND a.waveId='$waveId'".($shipOrderId ? " AND a.shipOrderId='$shipOrderId'" : "")." group by a.shipOrderId order by a.id ASC";
		$list = WhWaveScanRecordModel::query($sql);
		return $list ? $list : array();
	}	
    
    /**
     * WhWaveShippingRelationModel::insert_relation_data()
     * 插入关系表数据 
     * @param int $waveId 配货单ID
     * @param int $shipOrderId 发货单ID
     * @author Gary
     * @return bool
     */
    public static function insert_relation_data($waveId, $shipOrderId){
        self::initDB();
        $waveId     =   intval($waveId);
        $shipOrderId=   intval($shipOrderId);
        if($waveId && $shipOrderId){
            $sql    =   'insert into '.self::$tablename." (waveId, shipOrderId) values ('{$waveId}', '{$shipOrderId}')";
            //echo $sql."<br />";exit;
            return self::$dbConn->query($sql);
        }else{
            return FALSE;
        }
    }
	
    /**
     * 通过配货单获取所有对应的发货单号
     * @param number $waveId
     * @return array ids
     * @author czq
     */
    public static function getShippingOrderIdsByWaveId($waveId){
    	$waveId = intval($waveId);
    	return self::select('waveId = '.$waveId.' AND is_delete = 0 ','shipOrderId');
    } 
    /**
     * WhWaveShippingRelationModel::select_not_scanning_by_id() 
     * 该查询配货单和发货单关系表的配货单类型是一个发货单对应多个配货单的关系（也就是单发货单类型）
     * 通过发货单查找没有扫描过得配货单
     * @author cxy
     * @param mixed $shipOrderId发货单ID
     * @return
     */
    public static function select_not_scanning_by_id($shipOrderId){
        $shipOrderId = intval($shipOrderId);
        return self::select('shipOrderId = '.$shipOrderId.' AND is_delete = 0 and pickUserId = 0 and pickTime = 0','waveId');
    }
    /**
     * WhWaveShippingRelationModel::select_not_scanning()
     * 
     * 通过配货单获取配货单关系表的信息
     * @author cxy
     * @param mixed $waveId配货单ID
     * @return
     */
    public static function select_not_scanning($waveId){
        self::initDB();
        $waveId = intval($waveId);
        $sql       = "select * from `wh_wave_shipping_relation` where `waveId` = '{$waveId}' AND is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;  
    }
}
?>
