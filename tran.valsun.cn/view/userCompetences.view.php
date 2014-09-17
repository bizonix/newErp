<?php
/**
 * 类名：UserCompetencesView
 * 功能：开放授权管理视图层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class UserCompetencesView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= UserCompetencesAct::actIndex();
        $this->smarty->assign('title','开放授权管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('userCompetences.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= UserCompetencesAct::actAdd();
	    $this->smarty->assign('title','添加开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
		$this->smarty->display('userCompetencesAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= UserCompetencesAct::actModify();
	    $this->smarty->assign('title','修改开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('ucp_title',$data['res']['title']);   
	    $this->smarty->assign('ucp_item',$data['res']['item']);   
	    $this->smarty->assign('ucp_content',$data['res']['content']);   
	    $this->smarty->assign('ucp_pid',$data['res']['pid']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('userCompetencesModify.htm');		
	}	
}
?>