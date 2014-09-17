<?php
/**
 * 类名：TrackEmailTemplateView
 * 功能：跟踪邮件模版视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class TrackEmailTemplateView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackEmailTemplateAct::actIndex();
        $this->smarty->assign('title','跟踪邮件模版');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('trackEmailTemplate.htm');
	}

	//添加页面渲染
	public function view_add(){
		$data 	= TrackEmailTemplateAct::actAdd();
	    $this->smarty->assign('title','添加跟踪邮件模版');
        $this->smarty->assign('lists',$data['lists']);   
		$this->smarty->display('trackEmailTemplateAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackEmailTemplateAct::actModify();
	    $this->smarty->assign('title','修改跟踪邮件模版');
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('temp_plat',$data['res']['platForm']);   
	    $this->smarty->assign('temp_name',$data['res']['tempName']);   
	    $this->smarty->assign('temp_title',$data['res']['title']);   
	    $this->smarty->assign('temp_content',$data['res']['content']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('trackEmailTemplateModify.htm');		
	}	
}
?>