<?php
/**
 * 类名：WebAdView
 * 功能：网站广告管理视图层
 * 版本：1.0
 * 日期：2014/07/18
 * 作者：管拥军
 */
 
class WebAdView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= WebAdAct::actIndex();
        $this->smarty->assign('title','网站广告管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('typeId',$data['typeId']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('admin/webAd.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= WebAdAct::actAdd();
	    $this->smarty->assign('title','添加网站广告信息');
		$this->smarty->display('admin/webAdAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= WebAdAct::actModify();
	    $this->smarty->assign('title','修改网站广告信息');
	    $this->smarty->assign('topic',$data['res']['topic']);   
	    $this->smarty->assign('content',htmlspecialchars($data['res']['content']));   
	    $this->smarty->assign('is_enable',$data['res']['is_enable']);
	    $this->smarty->assign('layer',$data['res']['layer']);
	    $this->smarty->assign('typeId',$data['res']['typeId']);
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->display('admin/webAdModify.htm');		
	}	
}
?>