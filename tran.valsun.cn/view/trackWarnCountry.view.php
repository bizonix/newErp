<?php
/**
 * 类名：TrackWarnCountryView
 * 功能：目的地国家预警管理视图层
 * 版本：1.0
 * 日期：2014/05/23
 * 作者：管拥军
 */
class TrackWarnCountryView extends BaseView{
	
	//首页页面渲染
	public function view_index(){
		$data 	= TrackWarnCountryAct::actIndex();
        $this->smarty->assign('title','目的地国家预警管理');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']);   
		$this->smarty->display('trackWarnCountry.htm');
	}	
	
	//添加页面渲染
	public function view_add(){
		$data 	= TrackWarnCountryAct::actAdd();
        $this->smarty->assign('lists',$data['lists']);   
        $this->smarty->assign('shipTrack',$data['tracks']);
		$this->smarty->assign('title','添加目的地国家预警');
		$this->smarty->display('trackWarnCountryAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackWarnCountryAct::actModify();
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('shipTrack',$data['tracks']);
	    $this->smarty->assign('carrier_name',$data['res']['trackName']);   
	    $this->smarty->assign('ship_country',$data['res']['countryName']);   
	    $this->smarty->assign('ship_id',$data['res']['carrierId']);   
	    $this->smarty->assign('id',$data['id']);
		$this->smarty->assign('title','修改目的地国家预警');
		$this->smarty->display('trackWarnCountryModify.htm');		
	}	
}
?>