<?php
/**
 * 类名：ApiVisitStatView
 * 功能：API调用统计视图层
 * 版本：1.0
 * 日期：2014/7/10
 * 作者：管拥军
 */
class ApiVisitStatView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= ApiVisitStatAct::actIndex();
        $this->smarty->assign('title','API调用统计');
        $this->smarty->assign('apiId',$data['apiId']); 
        $this->smarty->assign('lists',$data['lists']);   
        $this->smarty->assign('apiList',$data['apiList']);   
	    $this->smarty->assign('pageStr',$data['pages']);
        $this->smarty->assign('timeNode',$data['timeNode']); 
        $this->smarty->assign('startTimeValue',$data['startTime']); 
        $this->smarty->assign('endTimeValue',$data['endTime']); 
		$this->smarty->display('apiVisitStat.htm');
	}		
}
?>