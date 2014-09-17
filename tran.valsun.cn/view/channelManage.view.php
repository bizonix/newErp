<?php
/**
 * 类名：ChannelManageView
 * 功能：渠道管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class ChannelManageView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= ChannelManageAct::actIndex();
        $this->smarty->assign('title','渠道管理');
        $this->smarty->assign('id',$data['id']); 
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('channelManage.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= ChannelManageAct::actAdd();
        $this->smarty->assign('lists',$data['lists']); 
        $this->smarty->assign('id',$data['id']); 
	    $this->smarty->assign('title','添加渠道');
		$this->smarty->display('channelManageAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= ChannelManageAct::actModify();
	    $this->smarty->assign('title','修改渠道');
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('ch_name',$data['res']['channelName']);   
	    $this->smarty->assign('ch_alias',$data['res']['channelAlias']);   
	    $this->smarty->assign('ch_post',$data['res']['postName']);   
	    $this->smarty->assign('ch_post1',$data['res']['postName1']);   
	    $this->smarty->assign('ch_post2',$data['res']['postName2']);   
	    $this->smarty->assign('ch_discount',$data['res']['discount']);   
	    $this->smarty->assign('ch_enabel',$data['res']['is_enable']);   
	    $this->smarty->assign('ship_id',$data['res']['carrierId']);   
	    $this->smarty->assign('ch_time',$data['res']['timeDiff']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('channelManageModify.htm');		
	}	
}
?>