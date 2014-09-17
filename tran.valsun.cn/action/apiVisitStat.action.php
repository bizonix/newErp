<?php
/**
 * 类名：ApiVisitStatAct
 * 功能：API调用统计动作处理层
 * 版本：1.0
 * 日期：2014/04/09
 * 作者：管拥军
 */
	
class ApiVisitStatAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * ApiVisitStatAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$trackEmailStat	= new ApiVisitStatModel();
		//接收参数生成条件
		$apiId			= isset($_GET['apiId']) ? abs(intval($_GET['apiId'])) : 0;
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$condition		.= "1";
		if(!empty($apiId)) {
			$condition	.= " AND apiId = '{$apiId}'";
		}
		if(!empty($timeNode)) {
			if(!in_array($timeNode,array('firstTime','lastTime'))) redirect_to("index.php?mod=apiVisitStat&act=index");
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($startTime && $endTime) {
				$condition	.= ' AND '.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $trackEmailStat->modListCount($condition);
		$res			= $trackEmailStat->modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 = '暂无数据';
		}		
		//封装数据返回
		$data['apiId']	 	= $apiId;
		$data['lists']		= $res;
		$data['pages']	 	= $pageStr;
		$data['timeNode']   = $timeNode;
		$data['startTime']	= $startTime ? date('Y-m-d',$startTime) : '';
		$data['endTime']	= $endTime ? date('Y-m-d',$endTime) : '';
		$data['apiList'] 	= TransOpenApiModel::getApiCompetenceList();
		self::$errCode   	= trackEmailStatModel::$errCode;
        self::$errMsg    	= trackEmailStatModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }	
}
?>