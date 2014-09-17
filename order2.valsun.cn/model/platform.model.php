<?php
/*
*提供账号和平台列表接口
*@add by : linzhengxiang ,date : 20140525
*/
class PlatformModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	/**
	 * 获取所以平台信息
	 * @return array
	 * @author lzx
	 */
	public function getPlatformLists(){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."platform WHERE is_delete = 0")->limit('*')->select(array('cache', 'mysql'));
	}
	/**
	 * 实例化父类，完成数据库插入判断
	 * @see commonModel::checkIsExists()
	 * @param string $data
	 * @return bool
	 * @author lzx
	 */
	public function checkIsExists($data){
		$checkdata = $this->sql("SELECT * FROM ".C('DB_PREFIX')."platform WHERE platform='{$data['platform']}' AND is_delete=0")->select();
		if (!empty($checkdata)){
			self::$errMsg[10017]    = get_promptmsg(10017, $data['platform']);
			return true;
		}
		return false;
	}
	
	public function getSuffixByPlatform($platformid){
		$result    = $this->sql("SELECT suffix FROM ".C('DB_PREFIX')."platform WHERE id=".intval($platformid))->limit(1)->select(array('cache', 'mysql'));
		return isset($result[0]['suffix']) ? $result[0]['suffix'] : false;
	}
}