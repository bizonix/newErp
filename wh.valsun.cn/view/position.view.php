<?php
/*
 * 仓位管理
 */
class PositionView extends BaseView{
	//A仓位信息
    public function view_positionList(){
		$state    = isset($_GET['state'])?post_check($_GET['state']):'';
		echo $state; echo "<br>";
		$this->smarty->assign('state',$state);
		$position_arr  = array();
		$PositionAct   = new PositionAct();
		$position_info = $PositionAct->act_getPositionList("*","where storeId=1");
		if(!empty($position_info)){
			foreach($position_info as $position){
				$x = $position['x_alixs'];
				$y = $position['y_alixs'];
				$z = $position['z_alixs'];
				$f = $position['floor'];
				$position_arr["($x,$y,$z,$f)"] = $position['pName'];
			}
		}
		//var_dump($position_arr);
		$this->smarty->assign('position_arr', $position_arr);

		$hang = 40;					//一列行数
		$row_position_nums = 4;     //一行仓位数
		$second_north_row = 6;		//二楼北区列数
		$second_south_row = 7;		//二楼南区列数
		$third_row = 8;				//三楼南区列数
		$distance = 10;				//南北区距离
		$distance_row = $row_position_nums+1;        //每列X坐标间隔数
		$this->smarty->assign('hang', $hang);
		$this->smarty->assign('row_position_nums', $row_position_nums);
		$this->smarty->assign('second_north_row', $second_north_row);
		$this->smarty->assign('second_south_row', $second_south_row);
		$this->smarty->assign('third_row', $third_row);
		$this->smarty->assign('distance', $distance);
		$this->smarty->assign('distance_row', $distance_row);
		
		
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),              //面包屑数据
                        array('url'=>'index.php?mod=position&act=positionList','title'=>'A仓管理'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', 'A仓管理');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = "010";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('positionInfo.htm');
    }
	
	//B仓位信息
    public function view_positionListB(){
		//$state    = isset($_GET['state'])?post_check($_GET['state']):'';
		//$this->smarty->assign('state',$state);
		$position_arr  = array();
		$PositionAct   = new PositionAct();
		$position_info = $PositionAct->act_getPositionList("*","where storeId=2");
		if(!empty($position_info)){
			foreach($position_info as $position){
				$x = $position['x_alixs'];
				$y = $position['y_alixs'];
				$z = $position['z_alixs'];
				$f = $position['floor'];
				$position_arr["($x,$y,$z,$f)"] = $position['pName'];
			}
		}		
		$this->smarty->assign('position_arr', $position_arr);

		$hang = 40;					//一列行数
		$row_position_nums = 4;     //一行仓位数
		$second_north_row = 6;		//二楼北区列数
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
		
		
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),              //面包屑数据
                        array('url'=>'index.php?mod=position&act=positionList','title'=>'B仓管理'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', 'B仓管理');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = "011";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('positionInfoB.htm');
    }
}