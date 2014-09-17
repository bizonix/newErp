<?php
/**
 * 类名：WedoApiView
 * 功能：开放业务管理视图层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class WedoApiView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
        $this->smarty->assign('title','开放业务管理');
		$this->smarty->display('wedoApi.htm');
	}
	
	//运德物流订单excell文件上传页面渲染
	public function view_orderImport(){
        $this->smarty->assign('title','运德物流订单文件导入');
		$this->smarty->display('wedoOrderImport.htm');
	}
	
	//运德物流订单导出页面渲染
	public function view_orderExport(){
        $this->smarty->assign('title','运德物流订单文件导出');
		$this->smarty->display('wedoOrderExport.htm');
	}
	
	//运德物流订单excell文件上传保存
	public function view_saveOrderImport(){
		$data	= WedoApiAct::actSaveOrderImport();
        $this->smarty->assign('title','运德物流订单文件导入');
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('wedoOrderImport.htm');

	}
	
	//运德跟踪号生成首页页面渲染
	public function view_wedoSn(){
		$data 	= WedoApiAct::actWedoSn();
        $this->smarty->assign('title','运德跟踪号生成管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('wedoSn.htm');
	}
	
	//添加运德跟踪号生成页面渲染
	public function view_wedoSnAdd(){
		$data 	= WedoApiAct::actWedoSnAdd();
	    $this->smarty->assign('title','添加运德跟踪号生成信息');
	    $this->smarty->assign('gids',$data['gids']);
		$this->smarty->display('wedoSnAdd.htm');		
	}
	
	//修改运德跟踪号生成页面渲染
	public function view_wedoSnModify(){
		$data 	= WedoApiAct::actWedoSnModify();
	    $this->smarty->assign('title','修改运德跟踪号生成信息');
	    $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('wedo_sn',$data['res']['wedo_sn']);   
	    $this->smarty->assign('id',$data['gid']);
	    $this->smarty->assign('gids',$data['gids']);
		$this->smarty->display('wedoSnModify.htm');		
	}	
}
?>