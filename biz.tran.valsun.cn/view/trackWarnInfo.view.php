<?php
/**
 * 类名：TrackWarnInfoView
 * 功能：运输方式跟踪号预警管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class TrackWarnInfoView extends BaseView{

	//首页页面渲染
	public function view_index(){
		
		$trackWarnInfo	= new TrackWarnInfoAct();
        $this->smarty->assign('title','跟踪号预警');
		
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$countryId		= isset($_GET['countryId']) ? intval($_GET['countryId']) : 0;
		$carrierId		= isset($_GET['carrierId']) ? intval($_GET['carrierId']) : 0;
		$channelId		= isset($_GET['channelId']) ? intval($_GET['channelId']) : 0;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$warnLevel		= isset($_GET['warnLevel']) ? intval($_GET['warnLevel']) : '';
		$is_warn		= isset($_GET['is_warn']) ? intval($_GET['is_warn']) : 1;
		$status			= isset($_GET['status']) ? intval($_GET['status']) : -1;
		//读取用户细颗粒权限
		$competences 	= $_SESSION['competences'];
		if(!empty($competences['competence'])) $competences = json_decode($competences['competence'],true);
		if(!in_array($carrierId, $competences['carrierId'])) {
			if(isset($competences['carrierId'])) $carrierId	 =	implode(",",$competences['carrierId']);
		}
		if(!in_array($channelId, $competences['channelId'])) {
			if(isset($competences['channelId'])) $channelId	 =	implode(",",$competences['channelId']);
		}
		$condition		= "1";
		if(!empty($countryId)) {
			$condition	.= " AND countryId = '{$countryId}'";
		}
		if($status>=0) {
			$condition	.= " AND status = '{$status}'";
		}
		if(!empty($carrierId)) {
			$condition	.= " AND carrierId IN({$carrierId})";
		}
		if(!empty($channelId)) {
			$condition	.= " AND channelId IN({$channelId})";
		}
		if(!empty($timeNode)) {
			if(!in_array($timeNode,array('scanTime','lastTime','trackTime'))) redirect_to("index.php?mod=trackWarnInfo&act=index");
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($startTime && $endTime) {
				$condition	.= ' AND '.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}		
		if($type && $key) {
			if(!in_array($type,array('orderSn','trackNumber','recordId'))) redirect_to("index.php?mod=trackWarnInfo&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		if($warnLevel === 0) {//全部节点预警
			$condition	.= " AND warnLevel > 0";
		} elseif ($warnLevel === -1) { //没预警节点
			$condition	.= " AND warnLevel = 0";
		} elseif (!empty($warnLevel)) { //某个预警节点
			$warnStr	= str_pad($warnStr,($warnLevel-1),"_",STR_PAD_LEFT);
			switch ($is_warn) {
				case 1:
					$condition	.= " AND warnLevel like '{$warnStr}1%'";
				break;
				case 2:
					$condition	.= " AND warnLevel like '{$warnStr}0%' AND nodeEff like '{$warnStr}1%'";
				break;
				case 3:
					$condition	.= " AND nodeEff like '{$warnStr}1%'";
				break;
				default:
					$condition	.= " AND warnLevel like '{$warnStr}1%'";
			}
		}
		// echo $condition,"###",$warnStr;
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $trackWarnInfo->actList($condition, $curpage, $pagenum);
		$total			= $trackWarnInfo->actListCount($condition);//页面总数量
		$startTimeValue	= $startTime ? date('Y-m-d',$startTime) : '';
		$endTimeValue	= $endTime ? date('Y-m-d',$endTime) : '';
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr = '暂无数据';
		}
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//条件选项 
        $this->smarty->assign('countryId',$countryId);//国家ID 
        $this->smarty->assign('carrierId',$carrierId);//运输方式ID 
        $this->smarty->assign('status',$status);//跟踪号状态
        $this->smarty->assign('is_warn',$is_warn);//跟踪号状态
        $this->smarty->assign('lists',$res);//数据集
		$carrierList	= TransOpenApiModel::getCarrier(2);
        $this->smarty->assign('carrierList',$carrierList);//运输方式列表
        $countrylist 	= TransOpenApiModel::getCountriesStandard(); //标准国家名称列表
        $this->smarty->assign('countrylist',$countrylist);
		$statusList	= C('TRACK_STATUS_DETAIL');
        $this->smarty->assign('statusList',$statusList);//跟踪号状态列表  		
	    $this->smarty->assign('pageStr',$pageStr);//分页输出
        $this->smarty->assign('timeNode',$timeNode);//时间条件 
        $this->smarty->assign('startTimeValue',$startTimeValue);//开始时间 
        $this->smarty->assign('endTimeValue',$endTimeValue);//结束时间
		$this->smarty->display('trackWarnInfo.htm');
	}	
}
?>