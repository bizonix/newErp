<?php
/**
 * 类名：TrackWarnExportView
 * 功能：运输方式跟踪号信息导出视图层
 * 版本：1.0
 * 日期：2014/01/02
 * 作者：管拥军
 */
class TrackWarnExportView extends BaseView{

	//首页页面渲染
	public function view_index(){
        $this->smarty->assign('title','跟踪号信息报表导出');
		$carrierList	= TransOpenApiModel::getCarrier(2);
        $this->smarty->assign('carrierList',$carrierList);//运输方式列表
		$statusList	= C('TRACK_STATUS_DETAIL');
        $this->smarty->assign('statusList',$statusList);//跟踪号状态列表 
		$this->smarty->display('trackWarnExport.htm');
	}	
}
?>