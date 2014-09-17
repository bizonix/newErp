<?php
/**
 * 类名：CountriesUsazoneView
 * 功能：美国邮政分区管理视图层
 * 版本：1.2
 * 日期：2013/12/16
 * 作者：管拥军
 */
class CountriesUsazoneView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= CountriesUsazoneAct::actIndex();
        $this->smarty->assign('title','美国邮政分区管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('countriesUsazone.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= CountriesUsazoneAct::actAdd();
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('title','添加美国邮政分区');
		$this->smarty->display('countriesUsazoneAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= CountriesUsazoneAct::actModify();
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('ow_zip_code',$data['res']['zip_code']);   
	    $this->smarty->assign('ow_zone',$data['res']['zone']);   
	    $this->smarty->assign('cn_title',$data['res']['cn_title']);   
	    $this->smarty->assign('transitId',$data['res']['transitId']);   
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->assign('title','修改美国邮政分区');
		$this->smarty->display('countriesUsazoneModify.htm');		
	}		
}
?>