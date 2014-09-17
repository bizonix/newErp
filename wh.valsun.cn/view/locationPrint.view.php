<?php
/*
 * 仓位打印
 */
class LocationPrintView extends BaseView{ 
	
    public function view_locationPrint(){
		$locationPrintAct = new LocationPrintAct();
		$info 			  = $locationPrintAct->act_getPrintList();
		//print_r($info);exit;
		//面包屑
		$navlist = array(
					array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),
					array('url'=>'','title'=>'仓位打印'),
			   );
		$this->smarty->assign('toplevel', 4);
        $this->smarty->assign('secondlevel', 46);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('info', $info);
		$this->smarty->display("locationPrint.htm");
    }
	
	//楼层仓位规则页面
    public function view_floorRule(){
		$locationPrintAct = new LocationPrintAct();
		$lists 			  = $locationPrintAct->act_getFloorRuleLists();
		//面包屑
		$navlist = array(
					array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),
					array('url'=>'index.php?mod=locationPrint&act=locationPrint','title'=>'仓位打印'),
					array('url'=>'','title'=>'楼层规则列表'),
			   );
		$this->smarty->assign('toplevel', 4);
        $this->smarty->assign('secondlevel', 46);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('lists', $lists);
	
		$this->smarty->display('floorRule.htm');
	}
	
	//修改楼层仓位规则
	public function view_editRule(){
		$locationPrintAct = new LocationPrintAct();
		$lists 			  = $locationPrintAct->act_getFloorRuleLists();			
		//面包屑
		$navlist = array(
					array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),
					array('url'=>'index.php?mod=locationPrint&act=locationPrint','title'=>'仓位打印'),
					array('url'=>'index.php?mod=locationPrint&act=floorRule','title'=>'楼层规则列表'),
					array('url'=>'','title'=>'修改楼层规则'),
			   );
		$this->smarty->assign('toplevel', 4);
        $this->smarty->assign('secondlevel', 46);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('lists', $lists);
	
		$this->smarty->display('foorRuleAdd.htm');
	}
	
	//新增楼层仓位规则
	public function view_addRule(){
		//面包屑
		$navlist = array(
					array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓位设置'),
					array('url'=>'index.php?mod=locationPrint&act=locationPrint','title'=>'仓位打印'),
					array('url'=>'index.php?mod=locationPrint&act=floorRule','title'=>'楼层规则列表'),
					array('url'=>'','title'=>'新增楼层'),
			   );
		$this->smarty->assign('toplevel', 4);
        $this->smarty->assign('secondlevel', 46);
		$this->smarty->assign('navlist', $navlist);
	
		$this->smarty->display('foorRuleAdd.htm');
	}
	
	//提交增/改楼层仓位规则
	public function view_sureAddFloor(){
		$locationPrintAct = new LocationPrintAct();
		$lists 			  = $locationPrintAct->act_sureAddFloor();		
		header('location:index.php?mod=locationPrint&act=floorRule&state=操作成功');exit;
	}
	
	//提交增/改楼层仓位规则
	public function view_printFloor(){
		$locationPrintAct = new LocationPrintAct();
		$info 			  = $locationPrintAct->act_printFloor();
		$this->smarty->assign('info', $info);
		$this->smarty->display('printLocation.htm');
	}
	
	//打印区域列表
	public function view_printArea(){
		//$locationPrintAct = new LocationPrintAct();
		$info = A('LocationPrint')->act_printArea();
		$this->smarty->assign('info', $info);
		$this->smarty->display('printLocation.htm');
	}
}