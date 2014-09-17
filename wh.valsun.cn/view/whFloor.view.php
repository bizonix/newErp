<?php
/*
 * 楼层管理列表
 * Herman.Xi @20140804
 */
class WhFloorView extends BaseView{
	//A仓位信息
    public function view_floorList(){
		$storeId    = isset($_GET['storeId'])?post_check($_GET['storeId']):'';
		
		$WhFloorAct  = new WhFloorAct();
		$getFloorList = $WhFloorAct->act_getFloorList("*","where storeId={$storeId}");
		
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓库列表'),
						 array('url'=>'#','title'=>'楼层设置')
                );
		//$floors = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20);
		$floors_DC = array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六',7=>'七',8=>'八',9=>'九',10=>'十',11=>'十一',12=>'十二',13=>'十三',14=>'十四',15=>'十五',16=>'十六',17=>'十七',18=>'十八',19=>'十九',20=>'二十');
		$this->smarty->assign('getFloorList',$getFloorList);
		$this->smarty->assign('storeId',$storeId);
		$this->smarty->assign('floors_DC',$floors_DC);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', 'A仓管理');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = "010";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('whFloorList.htm');
    }
}