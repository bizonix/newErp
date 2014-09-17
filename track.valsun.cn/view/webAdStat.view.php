<?php
/**
 * 类名：WebAdStatView
 * 功能：网站广告统计视图层
 * 版本：1.0
 * 日期：2014/07/21
 * 作者：管拥军
 */
class WebAdStatView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= WebAdStatAct::actIndex();
        $this->smarty->assign('title','网站广告统计');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('adId',$data['adId']); 
        $this->smarty->assign('lists',$data['lists']);   
        $this->smarty->assign('adList',$data['adList']);   
	    $this->smarty->assign('pageStr',$data['pages']); 
	    $this->smarty->assign('pageStr',$data['pages']);
        $this->smarty->assign('timeNode',$data['timeNode']); 
        $this->smarty->assign('startTimeValue',$data['startTime']); 
        $this->smarty->assign('endTimeValue',$data['endTime']);
		$this->smarty->display('admin/webAdStat.htm');
	}		
}
?>