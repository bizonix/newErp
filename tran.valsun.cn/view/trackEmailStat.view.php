<?php
/**
 * 类名：TrackEmailStatView
 * 功能：跟踪邮件视图层
 * 版本：2.0
 * 日期：2014/7/11
 * 作者：管拥军
 */
class TrackEmailStatView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackEmailStatAct::actIndex();
        $this->smarty->assign('title','跟踪邮件');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);   
	    $this->smarty->assign('pageStr',$data['pages']); 
	    $this->smarty->assign('pageStr',$data['pages']);
        $this->smarty->assign('timeNode',$data['timeNode']); 
        $this->smarty->assign('startTimeValue',$data['startTime']); 
        $this->smarty->assign('endTimeValue',$data['endTime']);
		$this->smarty->display('trackEmailStat.htm');
	}		
}
?>