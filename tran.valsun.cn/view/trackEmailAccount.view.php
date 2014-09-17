<?php
/**
 * 类名：TrackEmailAccountView
 * 功能：客服邮件帐号视图层
 * 版本：1.0
 * 日期：2014/04/10
 * 作者：管拥军
 */
class TrackEmailAccountView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackEmailAccountAct::actIndex();
        $this->smarty->assign('title','客服邮件帐号');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('trackEmailAccount.htm');
	}

	//添加页面渲染
	public function view_add(){
		$data 	= TrackEmailAccountAct::actAdd();
	    $this->smarty->assign('title','添加客服邮件帐号');
        $this->smarty->assign('lists',$data['lists']);   
		$this->smarty->display('trackEmailAccountAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackEmailAccountAct::actModify();
	    $this->smarty->assign('title','修改客服邮件帐号');
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('acc_plat',$data['res']['platForm']);   
	    $this->smarty->assign('acc_count',$data['res']['platAccount']);   
	    $this->smarty->assign('acc_user_name',$data['res']['userName']);   
	    $this->smarty->assign('acc_user_email',$data['res']['userEmail']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('trackEmailAccountModify.htm');		
	}	
}
?>