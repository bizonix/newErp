<?php
/**
 * 类名：CarrierOpenView
 * 功能：运输方式开放管理视图层
 * 版本：1.0
 * 日期：2014/07/07
 * 作者：管拥军
 */
 
class CarrierOpenView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= CarrierOpenAct::actIndex();
        $this->smarty->assign('title','运输方式开放列表');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
        $this->smarty->assign('carrierId',$data['carrierId']); 
        $this->smarty->assign('carrierList',$data['carriers']);
		$this->smarty->display('carrierOpen.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= CarrierOpenAct::actAdd();
	    $this->smarty->assign('title','添加开放运输方式');
        $this->smarty->assign('lists',$data['lists']);   
		$this->smarty->display('carrierOpenAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= CarrierOpenAct::actModify();
	    $this->smarty->assign('title','修改开放运输方式');
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('carrierIndex',$data['res']['carrierIndex']);   
	    $this->smarty->assign('carrierId',$data['res']['carrierId']);   
	    $this->smarty->assign('carrierDiscount',$data['res']['carrierDiscount']);   
	    $this->smarty->assign('carrierAbb',$data['res']['carrierAbb']);   
	    $this->smarty->assign('carrierEn',$data['res']['carrierEn']);   
	    $this->smarty->assign('carrierAging',$data['res']['carrierAging']);   
	    $this->smarty->assign('carrierNote',$data['res']['carrierNote']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('carrierOpenModify.htm');		
	}	
}
?>