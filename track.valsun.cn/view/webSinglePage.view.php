<?php
/**
 * 类名：WebSinglePageView
 * 功能：网站单页管理视图层
 * 版本：1.0
 * 日期：2014/07/16
 * 作者：管拥军
 */
 
class WebSinglePageView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= WebSinglePageAct::actIndex();
        $this->smarty->assign('title','网站单页管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('admin/webSinglePage.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= WebSinglePageAct::actAdd();
	    $this->smarty->assign('title','添加网站单页信息');
		$this->smarty->display('admin/webSinglePageAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= WebSinglePageAct::actModify();
	    $this->smarty->assign('title','修改网站单页信息');
	    $this->smarty->assign('topic',$data['res']['topic']);   
	    $this->smarty->assign('content',htmlspecialchars($data['res']['content']));   
	    $this->smarty->assign('is_enable',$data['res']['is_enable']);
	    $this->smarty->assign('layer',$data['res']['layer']);
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->display('admin/webSinglePageModify.htm');		
	}

	//查看页面渲染
	public function view_view(){
		$data 	= WebSinglePageAct::actModify();
	    $this->smarty->assign('content',$data['res']['content']);
        $this->smarty->assign('version',C("SYSTEM_VERSION"));
	    $this->smarty->assign('id',$data['id']);
		if(empty($data['res'])) {
			$this->smarty->assign('title','Page not found');
			$this->smarty->assign('keywords',"404");		
			$this->smarty->assign('description',"Page not found");
			$this->smarty->display('404.htm');
		} else {
			$this->smarty->assign('title',$data['res']['topic']);
			$this->smarty->assign('keywords',"{$data['res']['topic']}");		
			$this->smarty->assign('description',"{$data['res']['topic']}");
			$this->smarty->display('webSinglePage.htm');
		}
	}
}
?>