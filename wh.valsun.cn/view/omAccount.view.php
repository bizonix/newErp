<?php
/**
 * OmAccountView
 */
class OmAccountView extends BaseView {
	//添加页面
	public function view_showUserCompense(){

		$shipArr = array();
		$platformAccountList = CommonModel::getPlatformAccountList();   //获取平台及对应账号
		$shipingtyplist      = CommonModel::getShipingTypeList();      //运输方式列表		
		$uid   			     = $_GET['uid'];
		$shipArr			 = UserCompetenceModel::showCompetenceVisibleShip($uid);
		$powerAccountList    = UserCompetenceModel::showCompetenceVisibleAccount($uid);   //获取权限对应账号
		$powerPlatformList   = UserCompetenceModel::getCompetenceVisiblePlat($uid);   //获取权限对应平台

		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('shipArr', $shipArr);
		$this->smarty->assign('shipingtypelist', $shipingtyplist);
		$this->smarty->assign('platformAccountList', $platformAccountList);
		$this->smarty->assign('powerAccountList', $powerAccountList);
		$this->smarty->assign('powerPlatformList', $powerPlatformList);
		$this->smarty->display("showUserCompense.htm");
		
	}
	
}