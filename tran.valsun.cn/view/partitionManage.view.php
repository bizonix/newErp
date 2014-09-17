<?php
/**
 * 类名：PartitionManageView
 * 功能：分区管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class PartitionManageView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$condition		= '';
		$partitionManage	= new PartitionManageAct();
        $this->smarty->assign('title','分区管理');
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$chid			= isset($_GET['chid']) ? intval($_GET['chid']) : 0;//渠道ID
		$condition		.= "1";
		$condition		.= " AND channelId = {$chid}";
		if ($type && $key) {
			if (!in_array($type,array('partitionCode','partitionName'))) redirect_to("index.php?mod=partitionManage&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $partitionManage->actList($condition, $curpage, $pagenum);
		$total			= $partitionManage->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr = '暂无数据';
		}
		$carrierId	= PartitionManageModel::getCarrierId($chid);
		//替换页面内容变量
        $this->smarty->assign('chid',$chid);//渠道ID 
        $this->smarty->assign('carrierId',$carrierId);//运输方式ID 
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//查询选项 
        $this->smarty->assign('lists',$res);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('partitionManage.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$chid	= isset($_GET['chid']) ? intval($_GET['chid']) : 0;//渠道ID
		$carrierId	= PartitionManageModel::getCarrierId($chid);
		//替换页面内容变量
        $this->smarty->assign('chid',$chid);//渠道ID 
        $this->smarty->assign('carrierId',$carrierId);//运输方式ID
	    $this->smarty->assign('title','添加分区');
		$this->smarty->display('partitionManageAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改分区');
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id) || !is_numeric($id)) {
			redirect_to("index.php?mod=partitionManage&act=index");
			exit;
		}
		
		$partitionManage = new PartitionManageAct();
		$res = $partitionManage->actModify($id);
		$carrierId	= PartitionManageModel::getCarrierId($res['channelId']);
	    $this->smarty->assign('pt_code',$res['partitionCode']);   
	    $this->smarty->assign('chid',$res['channelId']);
        $this->smarty->assign('carrierId',$carrierId);
	    $this->smarty->assign('pt_name',$res['partitionName']);   
	    $this->smarty->assign('pt_ali',$res['partitionAli']);   
	    $this->smarty->assign('pt_add',$res['returnAddress']);   
	    $this->smarty->assign('pt_enable',$res['enable']);   
	    $this->smarty->assign('pt_country',$res['countries']);   
	    $this->smarty->assign('pt_add_html',$res['returnAddHtml']);   
	    $this->smarty->assign('id',$res['id']);   
		$this->smarty->display('partitionManageModify.htm');		
	}	
}
?>