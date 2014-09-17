<?php
/**
 * 类名：WebAdStatAct
 * 功能：网站广告统计动作处理层
 * 版本：1.0
 * 日期：2014/07/21
 * 作者：管拥军
 */
  
class WebAdStatAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * WebAdStatAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$ipNum			= 0;
		$webAdStat		= new WebAdStatModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$adId			= isset($_GET['adId']) ? abs(intval($_GET['adId'])) : 0;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$condition		.= "1";
		if(!empty($adId)) {
			$condition	.= " AND a.adId = '{$adId}'";
		}
		if($type && $key) {
			if(!in_array($type,array('ip'))) redirect_to("index.php?mod=webAdStat&act=index");
			if($type=='ip') {
				if(!(preg_match("/^([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})$/",$key))) {
					show_message($this->smarty,"IP地址参数有误!","");
					exit;
				}
				$ipNum		= sprintf('%u',ip2long($key));
				$condition	.= " AND a.ipNum = '{$ipNum}'";
			} else {
				$condition	.= ' AND a.'.$type." = '".$key."'";
			}
		}
		if(!empty($timeNode)) {
			if(!in_array($timeNode,array('addTime','lastTime'))) redirect_to("index.php?mod=webAdStat&act=index");
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($startTime && $endTime) {
				$condition	.= ' AND a.'.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $webAdStat->modListCount($condition);
		$res			= $webAdStat->modList($condition, $curpage, $pagenum);
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
		$data['adId']	 	= $adId;
		$data['lists']		= $res;
		$data['pages']		= $pageStr;
		$data['timeNode']   = $timeNode;
		$data['startTime']	= $startTime ? date('Y-m-d',$startTime) : '';
		$data['endTime']	= $endTime ? date('Y-m-d',$endTime) : '';
		$data['adList']		= WebAdModel::adList(1,"id,topic");
		self::$errCode   	= webAdStatModel::$errCode;
        self::$errMsg    	= webAdStatModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");
			exit;
		}
        return $data;
    }

	
	/**
	 * WebAdStatAct::act_webAdStat()
	 * 记录网站广告点击
	 * @param string $ids 广告ID
	 * @return array;
	 */
	public function act_webAdStat(){
		$ids				= isset($_REQUEST["ids"]) ? post_check($_REQUEST["ids"]) : "";
		$ip					= getClientIP();
		$ipNum				= sprintf('%u',ip2long($ip));
		if(empty($ids) || !(preg_match("/^([\d]+,)*[\d]$/",$ids))){
			self::$errCode  = "广告ID参数有误！";
			self::$errMsg   = 10000;
			return false;
		}
		$res 				= WebAdStatModel::showIpAdStat($ipNum,$ids);
		$stats				= isset($res['count']) ? $res['count'] : 0;
		$times				= time();
		if(empty($stats)) {
			$res			= WebAdStatModel::updateStatInfo($ipNum,$ids,array("adId"=>$ids, "ip"=>$ip, "ipNum"=>$ipNum, "count"=>1,"addTime"=>$times,"lastTime"=>$times));
		} else {
			$res			= WebAdStatModel::updateStatInfo($ipNum,$ids,array("adId"=>$ids, "ip"=>$ip, "ipNum"=>$ipNum, "count"=>$stats+1,"lastTime"=>$times));
		}
		self::$errCode  	= WebAdStatModel::$errCode;
        self::$errMsg   	= WebAdStatModel::$errMsg;
		return $res;
    }
}
?>