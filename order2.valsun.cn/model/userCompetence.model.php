<?php
/*
 * 类名：UserCompetenceModel
 * 功能：用户权限颗粒model层
 * 版本：1.0
 * 日期：2013/9/12
 * 作者：管拥军
 * 修改：linzhengxiang @ 20140530
 */
class UserCompetenceModel extends CommonModel{	

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 实例化父类，完成数据库插入判断
	 * @see commonModel::checkIsExists()
	 * @param string $data
	 * @return bool
	 * @author lzx
	 */
	public function checkIsExists($data){
		return false;
	}
	
	/**
	 * 根据用户ID和平台ID获取对应的账户权限
	 * @param int $uid
	 * @param int $platformid
	 * @return array
	 * @author lzx
	 */
	public function getAcountPowerByUserId($uid, $platformid){
		$competence = $this->getCompetenceByUserId($uid);
		$lists = json_decode($competence['visible_platform_account'], true);
		return isset($lists[$platformid]) ? $lists[$platformid] : false;
	}
	
	/**
	 * 根据用户ID获取对应的平台权限
	 * @param int $uid
	 * @return array
	 * @author lzx
	 */
	public function getPlatformPowerByUserId($uid){
		$competence = $this->getCompetenceByUserId($uid);
		$lists = json_decode($competence['visible_platform_account'], true);
		return !empty($lists) ? array_keys($lists) : false;
	}
	/**
	 * 获取一个用户的订单操作权限
	 * @param int $uid 用户id
	 * @return array
	 * @author andy
	 */
	public function getOrderPowerByUserId($uid){
		$competence = $this->getCompetenceByUserId($uid);
		return explode(',', $competence['visible_editorder']);
	}
	/**
	 * 获取一个用户在指定平台可用的账号操作权限
	 * @param int $uid 用户id
	 * @param int $platformid 平台id
	 * @return array
	 * @author andy
	 */
	public function getPlatformAcountPowerByUserId($uid, $platformid){
		$competence = $this->getCompetenceByUserId($uid);
		$lists = json_decode($competence['visible_platform_account'], true);
		return isset($lists[$platformid]) ? $lists[$platformid] : array();
	}
	/**
	 * 获取一个用户可以查看的文件夹权限
	 * @param int $uid 用户id
	 * @return array
	 * @author andy
	 */
	public function getFoldersPowerByUserId($uid){
		$competence = $this->getCompetenceByUserId($uid);
		return explode(',', $competence['visible_showfolder']);
	}
	/**
	 * 查看用户权限
	 * @param int $uid 用户id
	 * @return array
	 * @author lzx
	 */
    public function getCompetenceByUserId($uid){
    	$competencelist = $this->sql("SELECT * FROM ".C('DB_PREFIX')."user_competence WHERE global_user_id=".intval($uid))->limit(1)->select(array('cache', 'mysql'), 0);
    	return isset($competencelist[0]) ? $competencelist[0] : false;
	}
}
?>