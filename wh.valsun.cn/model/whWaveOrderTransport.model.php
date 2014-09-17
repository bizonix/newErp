<?php

/*
 * 运输方式跟踪号记录表
 * @author czq
 * @date 2014-08-04
 */
class WhWaveOrderTransportModel extends WhBaseModel {
	
	/**
	 * 获取申请运输方式记录
	 * @param number $limit
	 * @author czq
	 */
	public static function getOrderTransportRecords($limit,$status,$where=''){
		$sql = " SELECT a.*,b.shipOrderId,b.transportId,b.tracknumber,b.status FROM wh_shipping_order a INNER JOIN wh_wave_order_transport b
					ON a.id = b.shipOrderId WHERE b.status='{$status}' {$where} Order By b.createTime ASC limit $limit
				";
		return self::query($sql);
	}
	
}
?>
