<?php

/*
 * 分渠道Model
 * @author czq
 * @date 2014-09-16
 */
class WhChannelPartitionModel extends WhBaseModel {
	
	/**
	 *	通过渠道ID和国家名获取分区
	 *  @param $channelId
	 *  @return array()
	 *  @modify czq
	 */
	public static function getChannelPartition($channelId = 0){
		$list = WhChannelPartitionModel::select("channelId='".$channelId."' AND is_delete=0");
		return $list;
	}
	
	/**
	 * 获取全部已用的桶号
	 * @return array $partitions:
	 * @author czq
	 */
	public static function getUsedPartitions(){
		$partionList = WhChannelPartitionModel::select(' is_delete = 0 ORDER BY partition ASC');
		$partitions 	 = array();
		if($partionList){
			foreach($partionList as $list){
				$partitions = $list['partition'];
			}
		}
		return $partitions;
	}
}

?>
