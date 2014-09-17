<?php
/**
 * 类名：WebConfigView
 * 功能：网站后台配置管理视图层
 * 版本：1.0
 * 日期：2014/07/16
 * 作者：管拥军
 */
 
class WebConfigView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= WebConfigAct::actIndex();
        $this->smarty->assign('title','网站后台配置管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('admin/webConfig.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= WebConfigAct::actAdd();
	    $this->smarty->assign('title','添加网站后台配置信息');
		$this->smarty->display('admin/webConfigAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= WebConfigAct::actModify();
	    $this->smarty->assign('title','修改网站后台配置信息');
	    $this->smarty->assign('cKey',$data['res']['cKey']);   
	    $this->smarty->assign('cValue',htmlspecialchars($data['res']['cValue']));   
	    $this->smarty->assign('is_enable',$data['res']['is_enable']);
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->display('admin/webConfigModify.htm');		
	}	
}
?>