<?php
/*
 * 订单状态操作
 *
 * @add by : andy ,date : 20140816
 */

class OrderStatus{
	
	private $allStatus = array();

	public function __construct(){
		$this->allStatus = C('ORDER_STATUS');
	}
	
	//获取所有分类，包括子分类
	public function getAllStatus(){
		
		$result = array();
		$allStatus = $this->allStatus;
		
		foreach($allStatus as $statusName=>$statusArr){
			$result[$statusArr['id']] = $statusArr['name'];
			if(!empty($statusArr['types'])){
				
				$result[$statusArr['id']]['child'] = array();
				
				foreach($statusArr['types'] as $tmp_key=>$tmp_val){
					$result[$statusArr['id']]['child'][$tmp_val['id']] = $tmp_val['name'];
				}
			}
		}
	}
	//获取一个分类下的子分类
	public function getChildStatusByParentId($pid){
		
		$allStatus = $this->getAllStatus();
		
		if(isset($allStatus[$pid])){
			return $allStatus[$pid];
		}else{
			return array();
		}
		
	}
	//根据分类编码，获取分类的id
	public function getStatusIdByCode($id){
		
		$allStatus = $this->getAllStatus();
		
		foreach($allStatus as $statusName=>$statusArr){
			$result[$statusArr['id']] = $statusArr['name'];
			if(!empty($statusArr['types'])){
				
				$result[$statusArr['id']]['child'] = array();
				
				foreach($statusArr['types'] as $tmp_key=>$tmp_val){
					$result[$statusArr['id']]['child'][$tmp_val['id']] = $tmp_val['name'];
				}
			}
		}
		
	}
	
}
?>