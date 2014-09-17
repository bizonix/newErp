<?php
/*
 * 仓位打印
 */
class LocationPrintAct extends Auth {
	
	static $errCode = 0;
	static $errMsg = "";

	/**
	 * 仓位打印
	 */
	public function act_getPrintList(){
		$info = array();
		//仓库信息
		$storeLists = WarehouseManagementModel::warehouseManagementModelList();
		//楼层信息
		$floorLists = FloorModel::getInvReasonList("*", "");
		//仓库区域
		$areaLists  = M('WhArea')->getAreaList();
		
		$info['storeLists'] = $storeLists;
		$info['floorLists'] = $floorLists;
		$info['areaLists']  = $areaLists;
		return $info;
	}

	/**
	 * 获取楼层规则
	 */
	public function act_getFloorRuleLists(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where = "where 1";
		if($id!=0){
			$where .= " and id={$id}"; 
		}
		$lists = FloorModel::getInvReasonList("*", $where);
		return $lists;
	}
	
	/**
	 * 增加/修改楼层
	 */
	function  act_sureAddFloor(){
		$data 	    	 = array();
		$id 	    	 = trim($_POST['floorId']);
		$data['whFloor'] = post_check(trim($_POST['whFloor']));
		$data['whCode']  = post_check(trim($_POST['whCode']));
		if(empty($id)){
			$insertid = FloorModel::insertRow($data);
			if($insertid){
				return true;
			}else{
				return false;
			}
		}else{
			$updatedata = FloorModel::update($data,"and id='$id'");
			if($updatedata){
				return true;
			}else{
				return false;
			}
		}		
	}
	
	/**
	 * 打印
	 */
	public function act_printFloor(){
		$info 	   = array();
		$shelf     = array();
		$layer 	   = array();
		$location  = array();
		$store     = $_GET['store'];
		$floor	   = $_GET['floor'];
		$area	   = $_GET['area'];
		$Shelf1	   = $_GET['Shelf1'];
		$Shelf2    = $_GET['Shelf2'];
		$layer1    = $_GET['layer1'];
		$layer2	   = $_GET['layer2'];
		$location1 = $_GET['location1'];
		$location2 = $_GET['location2'];
		
		if($Shelf1==$Shelf2){
			$shelf[] = str_pad($Shelf1,2,0,STR_PAD_LEFT);
		}else{
			for($i=$Shelf1;$i<=$Shelf2;$i++){
				$shelf[] = str_pad($i,2,0,STR_PAD_LEFT);
			}
		}
		
		if($layer1==$layer2){
			$layer[] = str_pad($layer1,2,0,STR_PAD_LEFT);
		}else{
			for($j=$layer1;$j<=$layer2;$j++){
				$layer[] = str_pad($j,2,0,STR_PAD_LEFT);
			}
		}
		
		if($location1==$location2){
			$location[] = str_pad($location1,2,0,STR_PAD_LEFT);
		}else{
			for($k=$location1;$k<=$location2;$k++){
				$location[] = str_pad($k,2,0,STR_PAD_LEFT);
			}
		}
		
		foreach($shelf as $shelf_info){
			foreach($layer as $layer_info){
				foreach($location as $location_info){
					//$info[] = $store.$floor.$area.$shelf_info.$layer_info.$location_info;
					//$info[] = $floor."F".$area."-".$shelf_info."-".$layer_info.$location_info;
					$info[] = $area."-".$shelf_info."-".$layer_info.$location_info;
				}
			}
		}
		return $info;
	}
	
	/**
	 * 区域打印
	 */
	public function act_printArea(){
		$info 	   = array();
		$store     = $_GET['store'];
		$floor	   = $_GET['floor'];
		$area	   = $_GET['area'];
		//$info[] = $floor."F".$area;
		$info[] = $area;
		return $info;
	}
	
}