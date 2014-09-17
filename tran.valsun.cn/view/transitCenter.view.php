<?php
/**
 * 类名：TransitCenterView
 * 功能：转运中心管理视图层
 * 版本：1.0
 * 日期：2014/05/28
 * 作者：管拥军
 */
class TransitCenterView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TransitCenterAct::actIndex();
        $this->smarty->assign('title','转运中心管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('transitCenter.htm');
	}	
	
	//添加页面渲染
	public function view_add(){
	    $this->smarty->assign('title','添加转运中心');
		$this->smarty->display('transitCenterAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TransitCenterAct::actModify();
	    $this->smarty->assign('cn_name',$data['res']['cn_title']);   
	    $this->smarty->assign('en_name',$data['res']['en_title']);   
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->assign('title','修改转运中心');
		$this->smarty->display('transitCenterModify.htm');		
	}	
}
?>