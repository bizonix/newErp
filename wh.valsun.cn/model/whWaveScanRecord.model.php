<?php
/**
 * 配货单明细表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveScanRecordModel extends WhBaseModel {
	
	/**
	 *	获取波次料号对应全部发货单和分拣记录(发货单、筒号、待分拣数)
	 *	@param  $sku:料号
	 *	@param  $waveId:波次
	 *	@author cmf
	 */
	public static function getRecordInfoBySku($sku, $waveId) {
		$sql = "select b.id as relation_id, c.id as record_id, c.pickUserId, c.pickTime, b.waveId, a.shipOrderId, a.sku, a.amount as skuAmount, b.pickLight, c.amount as pickcount, c.pickStatus, d.transportId from wh_shipping_orderdetail a 
				left join wh_wave_shipping_relation b ON(a.shipOrderId=b.shipOrderId)
				left join wh_wave_pick_record c ON(a.shipOrderId=c.shipOrderId AND a.sku=c.sku)
				left join wh_shipping_order d ON(a.shipOrderId=d.id)
				where a.sku='$sku' AND b.waveId='$waveId' order by a.shipOrderId asc";
		$list = WhWaveScanRecordModel::query($sql);
		return $list ? $list : array();
	}
	
	/**
	 *	获取单SKU波次对应的全部发货单(含SKU数量)
	 *	注：多SKU波次对应发货单调用：WhWaveShippingRelationModel::getShipOrders($waveId, $shipOrderId);
	 *	@param  $waveId:波次
	 *	@param  $shipOrderId:发货单号 
	 *	@author cmf
	 */
	public static function getShipOrders($waveId = '', $shipOrderId = '') {
		$sql = "select b.transportId, a.shipOrderId, a.sku, sum(a.amount) as skuAmount, c.waveId from wh_shipping_orderdetail a 
				left join wh_shipping_order b ON(a.shipOrderId=b.id)
				left join wh_wave_shipping_relation c ON(c.shipOrderId=b.id)
				where c.waveId='$waveId'".($shipOrderId ? " AND a.shipOrderId='$shipOrderId'" : "")." group by a.shipOrderId order by a.amount asc, a.id ASC";
		$list = WhWaveScanRecordModel::query($sql);
		return $list ? $list : array();
	}
	
	/**
	 *	获取用户负责区域内波次SKU配货路由
	 *	@param  $waveId:波次
	 *	@param  $uid:用户ID
	 *	@param  $limit:返回SKU条数
	 *	@return 返回用户负责区域内需配货的SKU
	 *	@author cmf
	 */
	public static function getUserAreaSkuList($waveId, $uid, $areas, $limit = 0){
		//$whereArr[] = "(a.scanUserId=0 OR a.scanUserId='{$uid}')";
		$whereArr[] = "a.scanUserId='{$uid}'";
		//$whereArr[] = 'a.scanStatus=0';
		$whereArr[] = "a.is_delete=0";
		$whereArr[] = "a.waveId='{$waveId}'";
		$whereArr[] = "a.area IN('".implode("','", $areas)."')";
		$whereArr[] = "b.is_delete=0";
		$whereArr[] = "b.routeType=4";
		//$whereArr[] = "e.userId='$uid'";
		$sql = implode(' AND ', $whereArr);
		/*$sql = "select a.id, a.sku, a.skuAmount, a.amount, a.scanUserId, a.scanStatus, a.scanTime, a.storey, a.pName, c.id as skuid from wh_wave_scan_record a 
				left join wh_wave_area_info d ON(d.areaName=a.area AND d.is_delete=0)
				left join wh_wave_route_relation b ON(d.id=b.name AND b.is_delete=0)
				left join pc_goods c ON(c.sku=a.sku AND c.is_delete=0)
				left join wh_wave_area_user_relation e ON (e.areaId=d.id AND e.is_delete=0 AND e.userId='".$uid."')
				where ".$sql." order by a.scanStatus ASC, a.scanTime ASC, a.storey DESC, b.route asc, a.pName ASC".($limit ? " limit ".$limit : "");*/
				
		/*$sql = "select w.areaName from wh_wave_area_user_relation e left join wh_wave_area_info as w on e.areaId = w.id where e.userId='{$uid}' ";
		$areaNames = WhWaveScanRecordModel::query($sql);*/
		
		$sql = "select a.* from wh_wave_scan_record as a
				left join wh_wave_route_relation as b 
				on a.pName = b.name
				where ".$sql." order by a.scanTime ASC, b.route ASC, a.pName ASC".($limit ? " limit ".$limit : "");
		//echo $sql; echo "<br>";
		$list = WhWaveScanRecordModel::query($sql);
		foreach($list as $key => $val){
			$val['skucode'] = get_skuGoodsCode($val['sku']);
			$list[$key] = $val;
		}
		return $list ? $list : array();
	}
    
    /**
     * WhWaveScanRecordModel::inser_scan_data()
     * 插入配货记录数据
     * @param int $wave_id 波次ID
     * @param int $shipOrderId 发货单ID
     * @param array $scan_data 配货数据数组
     * @author Gary
     * @return bool
     */
    public static function inser_scan_data($wave_id, $scan_data){
        self::initDB();
        $wave_id    =   intval($wave_id);
        //$shipOrderId    =   intval($shipOrderId);
        if(!$wave_id || !is_array($scan_data)){
            return FALSE;
        }
        $data   =   '';
        $scan_data  =   array_filter($scan_data);
        foreach($scan_data as $val){ //拼接配货记录
            $record =   WhWaveScanRecordModel::select(array('waveId'=>$wave_id, 'sku'=>$val['sku'], 'pName'=>$val['pName'], 'is_delete'=>0), array('id', 'skuAmount'));
            if(!empty($record)){
                $skuAmount  =   $record[0]['skuAmount'] + $val['skuAmount'];
                $info       =   self::update(array('skuAmount'=>$skuAmount), $record[0]['id']);
            }else{
                $data   =   "('{$wave_id}', '{$val['sku']}', '{$val['skuAmount']}', '{$val['pName']}', '{$val['storey']}', '{$val['area']}')";
                $sql    =   'insert into '.self::$tablename.' (waveId, sku, skuAmount, pName, storey, area) values '.$data;
                //echo $sql;exit;
                $info   =   self::$dbConn->query($sql);
            }
            if($info == FALSE){
                return $info;
            }
        }
        return $info;  
    }
    
    /**
     * WhWaveScanRecordModel::get_scan_record_union_area()
     * 获取指定配货单的配货记录及区域负责人UID 
     * @param mixed $waveId
     * @return void
     */
    public static function get_scan_record_union_area($waveId){
        self::initDB();
        $where  =   array2where(array('a.waveId in'=>$waveId, 'a.is_delete'=> 0));
//        $sql    =   'select a.sku,a.skuAmount,a.pName,a.storey,a.area,c.userId from wh_wave_scan_record a
//                        left join wh_wave_area_info b on a.area = b.areaName left join wh_wave_area_user_relation c on
//                        c.areaId = b.id where '.$where.' order by a.storey desc, b.id asc,a.pName asc';
        $sql    =   'select a.sku,a.skuAmount,a.pName,a.storey,a.area from wh_wave_scan_record a
                        left join wh_wave_route_relation c on c.name = a.pName where '.$where
                        .'and c.routeType = 4 order by c.route asc, a.pName asc';
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }

}