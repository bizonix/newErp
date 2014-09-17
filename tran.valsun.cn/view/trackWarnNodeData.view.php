<?php
/**
 * 类名：TrackWarnNodeDataView
 * 功能：运输方式节点预警数据管理视图层
 * 版本：1.0
 * 日期：2014/05/16
 * 作者：管拥军
 */
class TrackWarnNodeDataView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackWarnNodeDataAct::actIndex();
        $this->smarty->assign('title','运输方式节点预警数据管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('trackWarnNodeData.htm');
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackWarnNodeDataAct::actModify();
	    $this->smarty->assign('title','修改节点预警数据');
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('nodeId',$data['res']['nodeId']);   
	    $this->smarty->assign('country',$data['res']['country']);   
	    $this->smarty->assign('aging',$data['res']['aging']);   
	    $this->smarty->assign('is_auto',$data['res']['is_auto']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('trackWarnNodeDataModify.htm');		
	}	
}
?>