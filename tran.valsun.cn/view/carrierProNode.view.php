<?php
/**
 * 类名：CarrierProNodeView
 * 功能：运输方式处理节点管理视图层
 * 版本：1.0
 * 日期：2014/07/08
 * 作者：管拥军
 */
 
class CarrierProNodeView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= CarrierProNodeAct::actIndex();
        $this->smarty->assign('title','运输方式处理节点列表');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
        $this->smarty->assign('carrierId',$data['carrierId']); 
        $this->smarty->assign('carrierList',$data['carriers']);
		$this->smarty->display('carrierProNode.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= CarrierProNodeAct::actAdd();
	    $this->smarty->assign('title','添加运输方式处理节点');
        $this->smarty->assign('lists',$data['lists']);   
		$this->smarty->display('carrierProNodeAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= CarrierProNodeAct::actModify();
	    $this->smarty->assign('title','修改运输方式处理节点');
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('nodeKey',$data['res']['nodeKey']);   
	    $this->smarty->assign('carrierId',$data['res']['carrierId']);   
	    $this->smarty->assign('nodeTitle',$data['res']['nodeName']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('carrierProNodeModify.htm');		
	}	
}
?>