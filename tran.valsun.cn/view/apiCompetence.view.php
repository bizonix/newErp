<?php
/**
 * 类名：ApiCompetenceView
 * 功能：API开放授权管理视图层
 * 版本：1.0
 * 日期：2014/07/10
 * 作者：管拥军
 */
 
class ApiCompetenceView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= ApiCompetenceAct::actIndex();
        $this->smarty->assign('title','API开放授权管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('apiCompetence.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= ApiCompetenceAct::actAdd();
	    $this->smarty->assign('title','添加API开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('gids',$data['gids']);
		$this->smarty->display('apiCompetenceAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= ApiCompetenceAct::actModify();
	    $this->smarty->assign('title','修改API开放授权信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('gids',$data['gids']);
	    $this->smarty->assign('apiName',$data['res']['apiName']);   
	    $this->smarty->assign('apiValue',explode(",",$data['res']['apiValue']));   
	    $this->smarty->assign('apiMaxCount',$data['res']['apiMaxCount']);   
	    $this->smarty->assign('apiEnable',$data['res']['is_enable']);
	    $this->smarty->assign('apiUid',$data['res']['apiUid']);
	    $this->smarty->assign('apiToken',$data['res']['apiToken']);
	    $this->smarty->assign('apiTokenExpire',$data['res']['apiTokenExpire']);
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->display('apiCompetenceModify.htm');		
	}	
}
?>