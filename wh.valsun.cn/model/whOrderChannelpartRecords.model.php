<?php

/*
 * 订单分渠道明细表Model
 * ADD BY czq 2014.09.16
 */
class WhOrderChannelpartRecordsModel extends WhBaseModel {
	
	/**
	 *	通过分区ID和用户ID获取订单分渠道记录
	 *  @param $partitionId
	 *  @param $userId
	 *  @return array $result
	 *  @author czq
	 */
	public function getChannelpartRecords($partitionId = 0, $userId = 0){
		if(!$partitionId || !$userId){
			return array();
		}
		$result = WhOrderChannelpartRecordsModel::find("partitionId='".$partitionId."' AND scanUserId='".$userId);
		return $result;
	}	
}