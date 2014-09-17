<?php
/**
 * 类名：TrackEmailSmtpView
 * 功能：邮件服务配置视图层
 * 版本：1.0
 * 日期：2014/04/10
 * 作者：管拥军
 */
class TrackEmailSmtpView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackEmailSmtpAct::actIndex();
        $this->smarty->assign('title','邮件服务配置');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('trackEmailSmtp.htm');
	}

	//添加页面渲染
	public function view_add(){
		$data 	= TrackEmailSmtpAct::actAdd();
	    $this->smarty->assign('title','添加邮件服务配置');
        $this->smarty->assign('lists',$data['lists']);   
		$this->smarty->display('trackEmailSmtpAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackEmailSmtpAct::actModify();
	    $this->smarty->assign('title','修改邮件服务配置');
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('smtp_plat',$data['res']['platForm']);   
	    $this->smarty->assign('smtp_count',$data['res']['platAccount']);   
	    $this->smarty->assign('smtp_user_name',$data['res']['smtpUser']);   
	    $this->smarty->assign('smtp_user_pwd',$data['res']['smtpPwd']);   
	    $this->smarty->assign('smtp_host',$data['res']['smtpHost']);   
	    $this->smarty->assign('smtp_port',$data['res']['smtpPort']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('trackEmailSmtpModify.htm');		
	}	
}
?>