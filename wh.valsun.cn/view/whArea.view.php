<?php
/*
 * 区域管理列表
 * Herman.Xi @20140804
 */
class WhAreaView extends BaseView{
	//A仓位信息
    public function view_areaList(){
		$storeId    = isset($_GET['storeId'])?post_check($_GET['storeId']):'';
		$floorId    = isset($_GET['floorId'])?post_check($_GET['floorId']):'';
		
		//$WhAreaAct  = new WhAreaAct();
		$getAreaList = A('WhArea')->act_getAreaList("*","where floorId={$floorId}");
		/*echo "<pre>";
		var_dump($getAreaList); exit;*/
		$area_arr  	= array();
		$area_id_arr  	= array();
		if(!empty($getAreaList)){
			foreach($getAreaList as $position){
				$id 			= $position['id'];
				$areaName 		= $position['areaName'];
				$vxa 			= $position['v_x_alixs'];
				$vya 			= $position['v_y_alixs'];
				/*$sx 			= $position['start_x_alixs'];
				$sy 			= $position['start_y_alixs'];
				$ex 			= $position['end_x_alixs'];
				$ey 			= $position['end_y_alixs'];
				$f 				= $position['floorId'];*/
				$area_arr["($vxa,$vya)"] = $areaName;
				$area_id_arr["($vxa,$vya)"] = $id;
				
				/*$area_arr[$f][$id]["sx"] = $sx;
				$area_arr[$f][$id]["sy"] = $sy;
				$area_arr[$f][$id]["ex"] = $ex;
				$area_arr[$f][$id]["ey"] = $ey;
				$area_arr[$f][$id]['areaName'] = $areaName;*/
			}
		}
		
		$row_position_cloumns = 30;					//一列行数
		$row_position_nums    = 30;                 //一行仓位数
		$distance_row = $row_position_nums+1;       //每列X坐标间隔数
		
		$this->smarty->assign('row_position_cloumns', $row_position_cloumns);
		$this->smarty->assign('row_position_nums', $row_position_nums);
		$this->smarty->assign('distance_row', $distance_row);
		
		//var_dump($area_arr);
		/*$line 	= 40;					//一列行数
		$column = 40;					//一行列数
		$row_position_nums = 4;     //一行区域数
		$distance_row = $row_position_nums+1;        //每列X坐标间隔数
		$this->smarty->assign('line', $line);
		$this->smarty->assign('column', $column);*/
		$this->smarty->assign('area_arr', $area_arr);
		$this->smarty->assign('area_id_arr', $area_id_arr);
		$this->smarty->assign('distance_row', $distance_row);
		$this->smarty->assign('floorId', $floorId);
		$this->smarty->assign('storeId', $storeId);
		
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓库列表'),
						 array('url'=>'index.php?mod=WhArea&act=areaList','title'=>'区域设置')
                );
		$this->smarty->assign('storeId',$storeId);
		$this->smarty->assign('whCode',$whCode);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', "楼层{$floorId}管理");
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = "010";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('whAreaList.htm');
    }
}