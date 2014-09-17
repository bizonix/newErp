<?php
/*
 * 仓位管理
 */
class WhPositionView extends BaseView{
	//A仓位信息
    public function view_positionList(){
		$storeId    = isset($_GET['storeId'])?post_check($_GET['storeId']):'';
		$floorId    = isset($_GET['floorId'])?post_check($_GET['floorId']):'';
		$storey     = M('WhFloor')->getFloorWhCode($floorId);
		$areaId    = isset($_GET['areaId'])?post_check($_GET['areaId']):'';
		//echo $areaId; echo "<br>";
		$getAreaList = A('WhArea')->act_getAreaList("*","where id='{$areaId}'");
		//echo "<pre>";
		//var_dump($getAreaList); exit;
		$start_x_alixs = $getAreaList[0]['start_x_alixs'];
		$start_y_alixs = $getAreaList[0]['start_y_alixs'];
		//echo $start_x_alixs; echo "<br>";
		//echo $start_y_alixs; echo "<br>";
		$this->smarty->assign('storey',$storey);
		$this->smarty->assign('start_x_alixs',$start_x_alixs);
		$this->smarty->assign('start_y_alixs',$start_y_alixs);
		
		$position_arr  = array();
		if($areaId){
			//$WhPositionAct   = new WhPositionAct();
			$position_info = A('WhPosition')->act_getPositionList("*","where areaId='{$areaId}' and storeId= '{$storeId}'");
			if(!empty($position_info)){
				foreach($position_info as $position){
					$x = $position['x_alixs'];
					$y = $position['y_alixs'];
					$z = $position['z_alixs'];
					$f = $position['floor'];
//					$position_arr["($x,$y,$z,$f)"][] = $position['pName'];
                    $position_arr["($x,$y,$z,$f)"]['first_position'] = $position['pName'];
				}
			}
			$this->smarty->assign('areaId',$areaId);
		}
//        $new_posotion_arr   =   array();
//        
//		foreach($position_arr as $key=>$val){
//            $new_posotion_arr[$key]['first_position']   =   $val[0];
//            $new_posotion_arr[$key]['all_position']     =   implode(',', $val);
//		}

		$this->smarty->assign('storeId',$storeId);
		$this->smarty->assign('position_arr', $position_arr);

		$hang = 7;					//一列行数
		$row_position_nums = 20;     //一行仓位数
		$second_north_row = 1;		//二楼列数
		//$second_south_row = 7;		//二楼南区列数
		//$third_row = 8;				//三楼南区列数
		//$distance = 10;				//南北区距离
		$distance_row = $row_position_nums+1;        //每列X坐标间隔数
		
		$this->smarty->assign('hang', $hang);
		$this->smarty->assign('row_position_nums', $row_position_nums);
		$this->smarty->assign('second_north_row', $second_north_row);
		//$this->smarty->assign('second_south_row', $second_south_row);
		//$this->smarty->assign('third_row', $third_row);
		//$this->smarty->assign('distance', $distance);
		$this->smarty->assign('distance_row', $distance_row);
		
		$toptitle = '区域'.$areaId.'仓位排列图';
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),              //面包屑数据
                        array('url'=>'index.php?mod=position&act=positionList','title'=>$toptitle),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', $toptitle);
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = "010";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
		
		$this->smarty->display('whPositionList.htm');
    }
	
}