<?php
/**
*类名：仓位管理
*功能：处理仓位信息
*作者：Herman.Xi @ 20140817
*参考黄伟生仓位管理action
*/
class WhPositionAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取仓位信息
	function  act_getPositionList($select = '*',$where){
		$list =	M('WhPosition')->getPositionList($select,$where);
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
		$info     =   array();
		$where['x_alixs']  =   intval($_POST['axis_x']);
		$where['y_alixs']  =   intval($_POST['axis_y']);
		$where['floor']    =   intval($_POST['floor']);
		$where['storeId']  =   intval($_POST['storeid']);
        $where['areaId']   =   intval($_POST['areaId']);
        $where['storey']   =   intval($_POST['storey']);
		//$list     =   PositionModel::getPositionList("*","where x_alixs='$x_alixs' and y_alixs='$y_alixs' and floor='$floor' and storeId={$storeid}");
        $list       =   WhPositionDistributionModel::select($where, 'pName, is_enable, type, z_alixs');
        //print_r($list);exit;
		if($list){
			foreach($list as $l){
				$info[$l['z_alixs']][] = array( //获取同层货架上的所有仓位
					'name'   => $l['pName'],
					'enable' => $l['is_enable'],
					'type'   => $l['type']
				);
			}
            $new_info   =   array();
            foreach($info as $key => $v){
                $pNames     =   get_filed_array('name', $v);
                $new_info[$key]['name']     =   implode(',', $pNames);
                $new_info[$key]['enable']   =   $v[0]['is_enable'];
                $new_info[$key]['type']     =   $v[0]['type'];
            }
            //print_r($new_info);exit;
            unset($info);
			return $new_info;
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
		$storey   = $_POST['storey'];
		$areaId   = $_POST['areaId'];
		
		$position_a = explode('|',$position);
		foreach($position_a as $po_a){
			$position_d = explode(',',$po_a);
            $alixs      =  array_splice($position_d, -3);
            //print_r($position_d);exit;
            $where  =   array(
                            'x_alixs' => $x_alixs,
                            'y_alixs' => $y_alixs,
                            'z_alixs' => $alixs[0],
                            'storey'  => $storey,
                            'areaId'  => $areaId,
                            'storeId' => $storeid
                        );
            $info   =   WhPositionDistributionModel::delete_data($where); //先清空该位置仓位信息
            foreach($position_d as $pname){
                if(!empty($pname)){
    				$lists 		= PositionModel::getPositionList("*","where pName='{$pname}' and storeId={$storeid}");
    				if(!empty($lists)){
    					$data = array(
    						'x_alixs' 	=> $x_alixs,
    						'y_alixs' 	=> $y_alixs,
    						'z_alixs'   => $alixs[0],
    						'floor'     => $floor,
    						'storeId'   => $storeid,
    						'storey'    => $storey,
    						'areaId'    => $areaId,
    						'is_enable' => $alixs[1],
    						'type'      => $alixs[2]
    					);
    					if(!WhPositionModel::update($data,"and id='{$lists[0]['id']}'")){
    						$mark = false;
    					}
    				}else{
    					$list 		= WhPositionModel::getPositionList("*","where pName='$pname' and x_alixs='$x_alixs' and y_alixs='$y_alixs' and z_alixs='$position_d[1]' and floor='$floor' and storeId={$storeid}");
    					if(!empty($list)){
    						$data = array(
    							'pName'     => $pname,
    							'is_enable' => $alixs[1],
    							'type'      => $alixs[2]
    						);
    						if(!WhPositionModel::update($data,"and id='{$list[0]['id']}'")){
    							$mark = false;
    						}
    					}else{
    						if(!empty($pname)){
    							$data = array(
    								'pName'   	=> $pname,
    								'x_alixs' 	=> $x_alixs,
    								'y_alixs' 	=> $y_alixs,
    								'z_alixs'   => $alixs[0],
    								'floor'     => $floor,
    								'storey'    => $storey,
    								'areaId'    => $areaId,
    								'is_enable' => $alixs[1],
    								'type'      => $alixs[2],
    								'storeId' 	=> $storeid
    							);
    							if(!WhPositionModel::insertRow($data)){
    								$mark = false;
    							}
    						}
    					}
    				}
    			}
            }
			//$pname = trim($position_d[0]);
			
		}
		if($mark){
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "更新失败，请重试!";
			return false;
		}
	}
	
	/*
	 * 初始化分区与仓位对应关系
	 * Herman.Xi @20140804
	 */
	public function act_InitPartition(){
		$areaId = $_POST['areaId'];
		$AreaList = A("WhArea")->act_getAreaList($select = '*', "where id = {$areaId}");
		//var_dump($AreaList);
		$areaName = $AreaList[0]['areaName'];
		$end_x_alixs = $AreaList[0]['end_x_alixs'];
		$end_y_alixs = $AreaList[0]['end_y_alixs'];
		return M("WhPosition")->InitPartition($end_x_alixs,$end_y_alixs);
	}
	
}