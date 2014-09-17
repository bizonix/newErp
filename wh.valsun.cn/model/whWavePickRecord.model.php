<?php

/*
 * 配货单分拣记录表Model
 * ADD BY cmf 2014.7.22
 */
class WhWavePickRecordModel extends WhBaseModel {
	
	/*
		检查波次发货单分拣是否已完结
		@param  $waveId: 波次
		@param  $shipOrderId: 发货单号
		@return true:正常完结，false:异常完结，array():返回未开始分拣的SKU
		@author cmf
	*/
	public static function checkPickStatus($waveId = 0, $shipOrderId = 0){
		if(!$waveId && !$shipOrderId){
			return false;
		}
		$sql = "select pickStatus, amount, sku from wh_wave_pick_record where is_delete=0 AND waveId='$waveId'".($shipOrderId ? " AND shipOrderId='$shipOrderId'" : "");
		$list = WhWavePickRecordModel::query($sql);
        if($list){
            $result = true;
            foreach($list as $val){
                if(empty($val['amount'])){
                    $skus[] = $val['sku'];
                }
                if($val['amount'] && !$val['pickStatus']){
                    $result = false;
                }
            }
        }else{
            $result = 0;
        }
        if($skus){
            return implode(',', $skus);
        }else{
            return $result;
        }
	}
    /**
     * WhWavePickRecordModel::get_pickByIdSku()
     * 根据发货单号和SKU获取配货单分拣记录信息
     * 
     * @author 陈先钰
     * @param mixed $shipOrderId 发货单号
     * @param mixed $sku 真实SKU
     * @return array
     */
    public static function get_pickByIdSku($shipOrderId,$sku){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where shipOrderId = {$shipOrderId} and sku = '{$sku}' and is_delete = 0 and pickStatus != 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    /**
     * WhWavePickRecordModel::get_all_pick()
     * 根据分拣人对分拣记录进行分组
     * @author 陈先钰
     * @param mixed $start
     * @param mixed $end 结束时间
     * @return
     */
    public static function get_all_pick($start,$end){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select a.skuAmount,a.shipOrderId,a.pickUserId,a.pickTime from {$tableName} a  where a.pickTime BETWEEN $start and $end  and a.pickStatus != 0  order by a.pickUserId";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;

    }
	
    /**
     * 根据发货单查找为料号未分拣完成的发货单
     * @param $waveId
     * @return Ambigous <boolean, multitype:multitype: >
     * @author czq
     */
    public static function getSkuPickRecord($waveId){
    	$sql = "SELECT DISTINCT a.pickLight,b.shipOrderId FROM wh_wave_shipping_relation a INNER JOIN wh_wave_pick_record b ON a.shipOrderId = b.shipOrderId 
    			WHERE b.waveId = '{$waveId}' AND b.pickStatus=0 AND b.is_delete = 0 ";
    	return self::query($sql);
    }
}
?>
