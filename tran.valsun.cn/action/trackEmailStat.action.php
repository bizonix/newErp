<?php
/**
 * 类名：TrackEmailStatAct
 * 功能：跟踪邮件动作处理层
 * 版本：2.0
 * 日期：2014/07/11
 * 作者：管拥军
 */
  
class TrackEmailStatAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackEmailStatAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$trackEmailStat		= new TrackEmailStatModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('trackNumber','platAccount'))) redirect_to("index.php?mod=trackEmailStat&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		if(!empty($timeNode)) {
			if(!in_array($timeNode,array('addTime','lastTime'))) redirect_to("index.php?mod=trackEmailStat&act=index");
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
		$data['key']	 	= $key;
		$data['type']	 	= $type;
		$data['lists']		= $res;
		$data['pages']		= $pageStr;
		$data['timeNode']   = $timeNode;
		$data['startTime']	= $startTime ? date('Y-m-d',$startTime) : '';
		$data['endTime']	= $endTime ? date('Y-m-d',$endTime) : '';
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