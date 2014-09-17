<?php
/**
 * 类名：TrackInquiryView
 * 功能：运德物流系统查询视图层
 * 版本：1.0
 * 日期：2014/01/17
 * 作者：管拥军
 */
class TrackInquiryView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackInquiryAct::actIndex();
        $this->smarty->assign('title', stripslashes($data['config']['WEB_INDEX_TITLE']));		
        $this->smarty->assign('keywords',stripslashes($data['config']['WEB_INDEX_KEYWORD']));		
        $this->smarty->assign('description',stripslashes($data['config']['WEB_INDEX_DESCRIPTIO']));		
        $this->smarty->assign('carriers',$data['carriers']);		
        $this->smarty->assign('version',C("SYSTEM_VERSION"));
		$this->smarty->display('trackInquiry.htm');
	}

	//跟踪页面渲染
	public function view_track(){
		$data 	= TrackInquiryAct::actTrack();
        $this->smarty->assign('title',"Check Result New");
		$this->smarty->assign('keywords',"{$data['carrierEn']},{$data['tracknum']},Check Result");		
        $this->smarty->assign('description',"{$data['carrierEn']} {$data['tracknum']} Check Result");  
        $this->smarty->assign('carrier',$data['carrier']);		
        $this->smarty->assign('carrierEn',$data['carrierEn']);		
        $this->smarty->assign('tracknum',$data['tracknum']);		
        $this->smarty->assign('carriers',$data['carriers']);		
        $this->smarty->assign('version',C("SYSTEM_VERSION"));
		$this->smarty->display('trackInquiryTrackNew.htm');
	}
}
?>