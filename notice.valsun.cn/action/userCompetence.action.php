<?php
/**
 * 功能：修改用户每日可发短信action
 * 作者：张志强
 * 时间：2014/08/20
 */
include_once WEB_PATH."action/base.action.php";
include_once WEB_PATH."model/userCompetence.model.php";

Class UserCompetenceAct extends BaseAction {
	public function act_updateUserCompetence() {
		function removeEmpty($v) {
			if(trim($v) !== '') {
				return true;
			}
		}
		$nameList		= urldecode($_GET['nameList']);
		$nameList		= explode(",", $nameList);
		$nameList		= array_filter($nameList, "removeEmpty");
		$smsnum			= trim($_GET['smsnum']);
		return UserCompetenceModel::updateUserCompetence($nameList, $smsnum);
	}
}
?>