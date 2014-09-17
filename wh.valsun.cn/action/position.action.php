<?php
/**
*类名：仓位管理
*功能：处理仓位信息
*作者：hws
*
*/
class PositionAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取仓位信息
	function  act_getPositionList($select = '*',$where){
		$list =	PositionModel::getPositionList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = PositionModel::$errCode;
			self::$errMsg  = PositionModel::$errMsg;
			return false;
		}
	}
	
	//获取当前仓位信息
	function  act_getCurrentPosition(){
		$info 	 = array();
		$x_alixs = $_POST['axis_x'];
		$y_alixs = $_POST['axis_y'];
		$floor   = $_POST['floor'];
		$storeid = $_POST['storeid'];
        $storey  = intval(trim($_POST['storey']));
		$list    = PositionModel::getPositionList("*","where x_alixs='$x_alixs' and y_alixs='$y_alixs' and floor='$floor' and storeId={$storeid} and storey='{$storey}'");
		if($list){
			foreach($list as $l){
				$info[$l['z_alixs']] = array(
					'name'   => $l['pName'],
					'enable' => $l['is_enable'],
					'type'   => $l['type']
				);
			}
			return $info;
		}else{
			self::$errCode = PositionModel::$errCode;
			self::$errMsg  = PositionModel::$errMsg;
			return false;
		}
	}
	
	//仓位添加、更新
	function  act_opisitionManage(){
		$data 	    = array();
		$position_a = array();
		$position_d = array();
		$mark     = true;
		$position = $_POST['position'];
		$x_alixs  = $_POST['axis_x'];
		$y_alixs  = $_POST['axis_y'];
		$floor    = $_POST['floor'];
		$storeid  = $_POST['storeid'];
        $storey   = intval(trim($_POST['storey']));
		
		$position_a = explode('|',$position);
		foreach($position_a as $po_a){
			$position_d = explode(',',$po_a);
			$pname = trim($position_d[0]);
			if(!empty($pname)){
				$lists 		= PositionModel::getPositionList("*","where pName='{$pname}' and storeId={$storeid} and storey = '{$storey}'");
				if(!empty($lists)){
					$data = array(
						'x_alixs' 	=> $x_alixs,
						'y_alixs' 	=> $y_alixs,
						'z_alixs'   => $position_d[1],
						'floor'     => $floor,
						'is_enable' => $position_d[2],
						'type'      => $position_d[3]
					);
					if(!PositionModel::update($data,"and id='{$lists[0]['id']}'")){
						$mark = false;
					}
				}else{
					$list 		= PositionModel::getPositionList("*","where x_alixs='$x_alixs' and y_alixs='$y_alixs' and z_alixs='$position_d[1]' and floor='$floor' and storeId={$storeid} and storey='{$storey}'");
					if(!empty($list)){
						$data = array(
							'pName'     => $position_d[0],
							'is_enable' => $position_d[2],
							'type'      => $position_d[3]
						);
						if(!PositionModel::update($data,"and id='{$list[0]['id']}'")){
							$mark = false;
						}
					}else{
						if(!empty($position_d[0])){
							$data = array(
								'pName'   	=> $position_d[0],
								'x_alixs' 	=> $x_alixs,
								'y_alixs' 	=> $y_alixs,
								'z_alixs'   => $position_d[1],
								'floor'     => $floor,
								'is_enable' => $position_d[2],
								'type'      => $position_d[3],
								'storeId' 	=> $storeid,
                                'storey'    => $storey
							);
							if(!PositionModel::insertRow($data)){
								$mark = false;
							}
						}
					}
				}
			}
		}
		if($mark){
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "更新失败，请重试!";
			return false;
		}
	}
	
	//更新仓位索引表
	function  act_updatePositionIndex(){
		set_time_limit(0);
		$x_aixs = 80;
		$y_aixs = 40;
		//$z_aixs = 100;
		//$z_step = 100;
		$floor  = 1;
		$f_step = 1;

		//清空数据库
		PositionModel::delPositonIndex("where storeId=1");

		//8级，一列一通道
		$x_step8 = 5;
		$y_step8 = 2;
		$i8 = 0;
		$g8 = 1;
		while($i8<$x_aixs){
			$j = 0;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i8} and x_alixs<".($i8+$x_step8)." and y_alixs<={$j} and y_alixs>".($j-$y_step8)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i8}' and x_alixs<'{($i8+$x_step8)}' and y_alixs>'{($j-$y_step8)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						//$position_id = $position_id."-".$j."-".$k."-".$floor."-".$where;
						$data = array(
							'level' 	 => 8,
							'piece'      => $g8,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if($insertid){
							$g8 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step8;
			}
			$i8 += $x_step8;
		}

		//7级，一列两通道
		$x_step7 = 5;
		$y_step7 = 4;
		$i7 = 0;
		$g7 = 1;
		while($i7<$x_aixs){
			$j = 2;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i7} and x_alixs<".($i7+$x_step7)." and y_alixs<={$j} and y_alixs>".($j-$y_step7)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i7}' and x_alixs<'{($i7+$x_step7)}' and y_alixs<='{$j}' and y_alixs>'{($j-$y_step7)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						$data = array(
							'level' 	 => 7,
							'piece'      => $g7,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if(insertid){
							$g7 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step7;
			}
			$i7 += $x_step7;
		}

		//6级，两列四通道
		$x_step6 = 10;
		$y_step6 = 8;
		$i6 = 0;
		$g6 = 1;
		while($i6<$x_aixs){
			$j = 6;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i6} and x_alixs<".($i6+$x_step6)." and y_alixs<={$j} and y_alixs>".($j-$y_step6)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i6}' and x_alixs<'{($i6+$x_step6)}' and y_alixs<='{$j}' and y_alixs>'{($j-$y_step6)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						$data = array(
							'level' 	 => 6,
							'piece'      => $g6,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if($insertid){
							$g6 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step6;
			}
			$i6 += $x_step6;
		}

		//5级，两列八通道
		$x_step5 = 10;
		$y_step5 = 16;
		$i5 = 0;
		$g5 = 1;
		while($i5<$x_aixs){
			$j = 14;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i5} and x_alixs<".($i5+$x_step5)." and y_alixs<={$j} and y_alixs>".($j-$y_step5)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i5}' and x_alixs<'{($i5+$x_step5)}' and y_alixs<='{$j}' and y_alixs>'{($j-$y_step5)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						$data = array(
							'level' 	 => 5,
							'piece'      => $g5,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if($insertid){
							$g5 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step5;
			}
			$i5 += $x_step5;
		}

		//4级，南北八通道(切半)
		$x_step4 = 40;
		$y_step4 = 16;
		$i4 = 0;
		$g4 = 1;
		while($i4<$x_aixs){
			$j = 14;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i4} and x_alixs<".($i4+$x_step4)." and y_alixs<={$j} and y_alixs>".($j-$y_step4)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i4}' and x_alixs<'{($i4+$x_step4)}' and y_alixs<='{$j}' and y_alixs>'{($j-$y_step4)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						$data = array(
							'level' 	 => 4,
							'piece'      => $g4,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if($insertid){
							$g4 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step4;
			}
			$i4 += $x_step4;
		}

		//3级，南北区
		$x_step3 = 40;
		$y_step3 = 40;
		$i3 = 0;
		$g3 = 1;
		while($i3<$x_aixs){
			$j = 38;
			while($j<=$y_aixs){
				$k = 0;
				while($k<=$floor){
					$where =  "where x_alixs>={$i3} and x_alixs<".($i3+$x_step3)." and y_alixs<={$j} and y_alixs>".($j-$y_step3)." and floor={$k} and storeId=1";
					//$position_info = PositionModel::getPositionList("id","where x_alixs>='{$i3}' and x_alixs<'{($i3+$x_step3)}' and y_alixs<='{$j}' and y_alixs>'{($j-$y_step3)}' and floor='{$k}'");
					$position_info = PositionModel::getPositionList("id",$where);
					if(!empty($position_info)){
						$data 		 = array();
						$position_id = '';
						foreach($position_info as $position){
							$position_id .= ','.$position['id'];
						}
						$position_id = trim($position_id,',');
						$data = array(
							'level' 	 => 3,
							'piece'      => $g3,
							'positionId' => $position_id
						);
						$insertid = PositionModel::insertPositonIndex($data);
						if($insertid){
							$g3 += 1;
						}
					}
					$k += $f_step;
				}
				$j += $y_step3;
			}
			$i3 += $x_step3;
		}
		//2级，二三楼
		$g2 = 1;
		$k = 0;
		while($k<=$floor){
			$position_info = PositionModel::getPositionList("id","where floor='{$k}' and storeId=1");
			if(!empty($position_info)){
				$data 		 = array();
				$position_id = '';
				foreach($position_info as $position){
					$position_id .= ','.$position['id'];
				}
				$position_id = trim($position_id,',');
				$data = array(
					'level' 	 => 2,
					'piece'      => $g2,
					'positionId' => $position_id
				);
				$insertid = PositionModel::insertPositonIndex($data);
				if($insertid){
					$g2 += 1;
				}
			}
			$k += $f_step;
		}
		return true;
	}
	
}


?>