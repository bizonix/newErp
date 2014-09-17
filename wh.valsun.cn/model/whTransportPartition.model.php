<?php

/*
 * 运输方式分区Model
 * ADD BY cmf 2014.7.22
 */
class WhTransportPartitionModel extends WhBaseModel {
	
	/**
	 *	通过渠道ID和国家名获取分区
	 *  @param $channelId
	 *  @param @country
	 *  @return array()
	 *  @author cmf
	 *  @modify czq
	 */
	public function getPartition($channelId = 0, $country = ''){
		if(!$channelId){
			return array();
		}
		$list = WhTransportPartitionModel::select("channelId='".$channelId."' AND status=1 AND is_delete=0");
		$result = array();
		if($list){
			foreach($list as $val){
				$countrylist = json_decode($val['countryWhiteList'], true);
				if(!$countrylist || in_array($country, $countrylist)){
					unset($val['countryWhiteList']);
					$val['code'] = 'A'.substr('0'.$val['partition'], -2);
					$result = $val;
					break;
				}
			}
		}
		return $result;
	}
}

?>
