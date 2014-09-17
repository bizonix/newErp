<?php
/**
 * 类名：UserCompetenceView
 * 功能：用户开放授权管理视图层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class UserCompetenceView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= UserCompetenceAct::actIndex();
        $this->smarty->assign('title','用户开放授权管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('userCompetence.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= UserCompetenceAct::actAdd();
	    $this->smarty->assign('title','添加用户开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('gids',$data['gids']);
		$this->smarty->display('userCompetenceAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= UserCompetenceAct::actModify();
	    $this->smarty->assign('title','修改用户开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('competence',$data['res']['competence']);   
	    $this->smarty->assign('id',$data['gid']);
	    $this->smarty->assign('gids',$data['gids']);
		$this->smarty->display('userCompetenceModify.htm');		
	}	
}
?>