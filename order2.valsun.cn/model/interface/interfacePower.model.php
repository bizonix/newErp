<?php
/*
 *鉴权系统相关接口操作类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfacePowerModel extends InterfaceModel {
	
	public function __construct(){
		parent::__construct();
	}
	   
	/**
	 * 用户登录走开放系统
	 * @param string $username
	 * @param string $password
	 * @return array
	 * @author lzx
	 */
    public function userLogin($username, $password){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['user_name'] 	= $username;
		$conf['pwd'] 		= $password;
		$conf['sysName'] 	= C('AUTH_SYSNAME');
		$conf['sysToken'] 	= C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
	
	/**
	 * 根据统一用户编号给鉴权系统，返回用户相关信息
	 * @param int $userId: 统一用户编号
	 * @return array
	 * @author lzx
	 */
	public function getUserInfo($userId){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['queryConditions'] = json_encode(array('userId' => intval($userId)));
		$conf['sysName'] 		 = C('AUTH_SYSNAME');
		$conf['sysToken'] 		 = C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data[0];
		}
	}
	
	/**
	 * 根据统一用户编号给鉴权系统，返回用户相关信息
	 * @param int $userId: 统一用户编号
	 * @return array
	 * @author lzx
	 */
	public function getUserPower($usertoken){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['userToken'] = $usertoken;
		$conf['sysName']   = C('AUTH_SYSNAME');
		$conf['sysToken']  = C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data['power'];
		}
	}
	
	/**
	 * 根据统一用户编号获取该用户权限下面的所有用户列表
	 * @param int $userId: 统一用户编号
	 * @param int $page
	 * @param int $num
	 * @return array
	 * @author lzx
	 */
	public function getUserList($userid, $page=1, $num=200){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}		$conf['global_user_id'] = $userid;
        /*接口目前只支持返回全部，自己根据全部数据分页
		$conf['page']   		= $page;
		$conf['num']  			= $num;
        **/
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
    
    /**
	 * 修改用户统一密码密码
	 * @param $userId1 修改人Id
     * @param $userId2 被修改人Id
     * @param $userId2 密码
	 * @return array
	 * @author lzx
	 */
    public function userUpdatePsw($userId1, $userId2, $psw){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['userId1'] = $userId1;
		$conf['userId2'] = $userId2;
		$conf['psw']     = $psw;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
        if(!empty($data)){
            return $data;
        }else{
            return false;
        }
		
	}
	
	/**
	 * 新增用户走开放系统
	 * @param array $newuser
	 * @return array
	 * @author lzx
	 */
    public function userInsert($newuser){		
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['newInfo']   = $newuser;
		$conf['action']    = 'addApiUser';
		$conf['sysName']   = C('AUTH_SYSNAME');
		$conf['sysToken']  = C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
	
	/**
	 * 新增用户走开放系统
	 * @param array $newuser
	 * @return array
	 * @author lzx
	 */
    public function userUpdate($newuser, $userToken){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['newInfo']   = $newuser;
		$conf['action']    = 'updateUserInfo';
		$conf['userToken'] = $userToken;
		$conf['sysName']   = C('AUTH_SYSNAME');
		$conf['sysToken']  = C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
	
	/**
	 * UserModel::userDelete()
	 * 删除用户走开放系统
	 * add by 管拥军 2013-08-23
	 * @return  bool
	 */
    public function userDelete($userToken){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['userToken'] = $userToken;
		$conf['sysName']   = C('AUTH_SYSNAME');
		$conf['sysToken']  = C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
    
    /**
	 * 获取所有的公司信息（只包含本公司的）
	 * add by zqt
	 * @return  array
	 */
    public function getAllCompanyInfo(){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    /**
	 * 获取所有的部门信息（只包含本公司的）
	 * add by zqt
	 * @return  array
	 */
    public function getAllDeptInfo(){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    /**
	 * 获取所有的岗位信息（只包含本公司的）
	 * add by zqt
	 * @return  array
	 */
    public function getAllJobInfo(){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    /**
	 * 获取所有的用户信息（只包含本公司的）
	 * add by zqt
	 * @return  array
	 */
    public function getAllUserIdUserNameInfo(){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
}
?>