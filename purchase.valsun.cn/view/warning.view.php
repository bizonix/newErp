<?php
/**
 * 类名：WarningView
 * 功能：催货+海外仓预警视图层
 * 版本：1.0
 * 日期：2013/8/5
 * 作者：管拥军
 */
require_once WEB_PATH.'lib/page.php';
class WarningView extends BaseView{

	//催货预警逻辑
	public function view_speed(){
		$mod	= $_GET['mod'];
		$act	= $_GET['act'];
		$Warning	= new WarningAct();
        $this->smarty->assign('title','催货预警');
        $this->smarty->assign('mod',$mod);//模块权限     
        $this->smarty->assign('act',$act);//模块权限     
             
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? intval($_GET['page']) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? trim($_GET['key']) : '';
		$timeNode		= isset($_GET['timenode']) ? trim($_GET['timenode']) : '';
		$status			= isset($_GET['status']) ? intval($_GET['status']) : 1;
		$condition		= "1";
		//$condition		.= " AND status = ".$status;
		if($type && $key){
			if(!in_array($type,array('sku','spu'))) redirect_to("index.php?mod=warning&act=speed");
			$condition	.= ' AND a.'.$type." = '".$key."'";
		}
		if($timeNode){
			if(!in_array($timeNode,array('goodsCreatedTime','auditTime'))) redirect_to("index.php?mod=warning&act=speed");
			$startTime		= isset($_GET['starttime']) ? strtotime(trim($_GET['starttime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endtime']) ? strtotime(trim($_GET['endtime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($startTime && $endTime){
				$condition	.= ' AND a.'.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$result			= $Warning->actList($condition, $curpage, $pagenum);
		$total			= $Warning->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$startTimeValue	= $startTime ? date('Y-m-d',$startTime) : '';
		$endTimeValue	= $endTime ? date('Y-m-d',$endTime) : '';
		$pageStr		= "";
		if($result){
			if($total>$perNum){
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('timeNode',$timeNode);//时间条件 
        $this->smarty->assign('startTimeValue',$startTimeValue);//开始时间 
        $this->smarty->assign('endTimeValue',$endTimeValue);//结束时间 
    
        $this->smarty->assign('type',$type);//循环赋值 
        $this->smarty->assign('lists',$result);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('warning.htm');
	}
	
	//海外仓预警逻辑
	public function view_oversea(){
		$mod	= $_GET['mod'];
		$act	= $_GET['act'];
		$Warning	= new WarningAct();
        $this->smarty->assign('title','海外仓预警');
        $this->smarty->assign('mod',$mod);//模块权限     
        $this->smarty->assign('act',$act);//模块权限     
             
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? intval($_GET['page']) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? trim($_GET['key']) : '';
		$timeNode		= isset($_GET['timenode']) ? trim($_GET['timenode']) : '';
		$status			= isset($_GET['status']) ? intval($_GET['status']) : 1;
		$condition		= "1";
		//$condition		.= " AND status = ".$status;
		if($type && $key){
			if(!in_array($type,array('sku','spu','ordersn'))) header("location:index.php?mod=warning&act=oversea");
			$condition	.= ' AND a.'.$type." = '".$key."'";
		}
		if($timeNode){
			if(!in_array($timeNode,array('goodsCreatedTime','auditTime'))) header("location:index.php?mod=warning&act=oversea");
			$startTime		= isset($_GET['starttime']) ? strtotime(trim($_GET['starttime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endtime']) ? strtotime(trim($_GET['endtime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($startTime && $endTime){
				$condition	.= ' AND a.'.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$result			= $Warning->actList($condition, $curpage, $pagenum);
		$total			= $Warning->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$startTimeValue	= $startTime ? date('Y-m-d',$startTime) : '';
		$endTimeValue	= $endTime ? date('Y-m-d',$endTime) : '';
		$pageStr		= "";
		if($result){
			if($total>$perNum){
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('timeNode',$timeNode);//时间条件 
        $this->smarty->assign('startTimeValue',$startTimeValue);//开始时间 
        $this->smarty->assign('endTimeValue',$endTimeValue);//结束时间 
    
        $this->smarty->assign('type',$type);//循环赋值 
        $this->smarty->assign('lists',$result);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('warning.htm');
	}
}
?>