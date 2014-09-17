<?php
/**
 * 类名：TrackWarnCarrierView
 * 功能：运输方式名预警管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class TrackWarnCarrierView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$trackWarnCarrier	= new TrackWarnCarrierAct();
        $this->smarty->assign('title','运输方式名预警管理');
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		= "1";
		if ($type && $key) {
			if (!in_array($type,array('erpName','trackName'))) redirect_to("index.php?mod=trackWarnCarrier&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $trackWarnCarrier->actList($condition, $curpage, $pagenum);
		$total			= $trackWarnCarrier->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//条件选项 
        $this->smarty->assign('lists',$res);//数据集   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('trackWarnCarrier.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$erpCarrierList	= TransOpenApiAct::act_getErpCarrierList();
        $this->smarty->assign('shipErp',$erpCarrierList);//ERP运输方式列表 
		$carrierList	= TransOpenApiModel::getCarrier(2);
        $this->smarty->assign('lists',$carrierList);//运输方式列表   
	    $trackCarrierList	= TransOpenApiModel::getTrackCarrierList();
        $this->smarty->assign('shipTrack',$trackCarrierList);//跟踪号系统运输方式列表
		$this->smarty->assign('title','添加运输方式名预警');
		$this->smarty->display('trackWarnCarrierAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改运输方式名预警');
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id) || !is_numeric($id)) {
			redirect_to("index.php?mod=trackWarnCarrier&act=index");
			exit;
		}
		$trackWarnCarrier	= new TrackWarnCarrierAct();
		$res		= $trackWarnCarrier->actModify($id);
		$erpCarrierList	= TransOpenApiAct::act_getErpCarrierList();
        $this->smarty->assign('shipErp',$erpCarrierList);//ERP运输方式列表 
		$carrierList	= TransOpenApiModel::getCarrier(2);
        $this->smarty->assign('lists',$carrierList);//运输方式列表
	    $trackCarrierList	= TransOpenApiModel::getTrackCarrierList();
        $this->smarty->assign('shipTrack',$trackCarrierList);//跟踪号系统运输方式列表
	    $this->smarty->assign('carrier_name',$res['trackName']);   
	    $this->smarty->assign('ship_erp',$res['erpName']);   
	    $this->smarty->assign('ship_id',$res['carrierId']);   
	    $this->smarty->assign('id',$res['id']);   
		$this->smarty->display('trackWarnCarrierModify.htm');		
	}	
}
?>