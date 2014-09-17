<?php

/*
 * 发货单投放记录表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveShippingPickRecordModel extends WhBaseModel {
	
	/**
	 * 根据发货单查找记录
	 * @param number $shipOrderId
	 * @author czq
	 */
	public static function getRecordInfoByShipOrderId($shipOrderId){
		$sql = "SELECT a.pickLight,b.shipOrderId,b.pickStatus FROM wh_wave_shipping_relation a INNER JOIN wh_wave_shipping_pick_record b ON 
				a.shipOrderId = b.shipOrderId WHERE b.shipOrderId = '{$shipOrderId}' AND b.is_delete = 0";
		return self::query($sql);
	}
}
?>
