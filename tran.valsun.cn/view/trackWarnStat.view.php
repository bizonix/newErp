<?php
/**
 * 类名：TrackWarnStatView
 * 功能：运输方式跟踪号统计管理视图层
 * 版本：1.0
 * 日期：2013/11/21
 * 作者：管拥军
 */
class TrackWarnStatView extends BaseView{

	//首页页面渲染
	public function view_index(){
        $this->smarty->assign('title','跟踪号信息统计');
		$carrierList	= TransOpenApiModel::getCarrier(2);
        $this->smarty->assign('carrierList',$carrierList);//运输方式列表 
		$queryObj 		= new ShipfeeQueryModel();
        $countrylist 	= $queryObj->getStandardCountryName(); //标准国家名称列表
        $this->smarty->assign('countrylist',$countrylist);
		$this->smarty->display('trackWarnStat.htm');
	}	
}
?>