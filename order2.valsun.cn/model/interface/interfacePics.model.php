<?php
/*
 *从开放系统获取信息类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class FromOpenModel{
	
	protected $dbConn  = '';
	protected $cache   = '';
	protected static $errMsg  = array();
	private $_key = '';
	
	//构造函数自动加载DB对象
	public function __construct(){
		if (!is_object($this->dbConn)){
			$this->_initDB();
		}
		if (!is_object($this->cache)){
			$this->_initCache();
		}
	}
	
	//初始化mysqlDB
	private function _initDB(){
		global $dbConn;
		$this->dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//初始化缓存
	private function _initCache(){
		global $memc_obj;
		$this->cache = $memc_obj;
	}
	
	public function key($key){
		$this->_key = $key;
		return $this;
	}
	
    /**
     * 根据SPU的数组获取所有SPU图片
     * @param $spu spu数组
     * @param $picType 类型，默认为G
	 * @return array
	 * @author lzx
     */
	public function getSpuAllPic($spu, $picType='G'){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['spu'] = json_encode($spu);
        $conf['picType'] = $picType;
		$result = callOpenSystem($conf, $requesturl, 600);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
	/**
     * 获取所有渠道信息
	 * @return array
	 * @author lzx
     */
	public function getChannelList($carrierId='all'){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$requesturl = array_shift($conf);
		$conf['carrierId'] = $carrierId;
		$result = callOpenSystem($conf, $requesturl, 600);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
    /**
     * 获取运输方式列表信息,填写正确的运输方式参数类型（0非快递，1快递，2全部）
	 * @return array
	 * @author lzx
     */
	public function getCarrierList($type){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$requesturl = array_shift($conf);
		$conf['type'] = intval($type);
		$result = callOpenSystem($conf, $requesturl, 600);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
	/**
     * 获取单个sku信息
	 * @param string $sku 
	 * @return array
	 * @author lzx
     */
	public function getSkuinfo($sku){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$requesturl = array_shift($conf);
		$conf['sku'] = $sku;
		$result = callOpenSystem($conf, $requesturl, 600);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
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
		$requesturl 		= array_shift($conf);
		$conf['user_name'] 	= $username;
		$conf['pwd'] 		= $password;
		$conf['sysName'] 	= C('AUTH_SYSNAME');
		$conf['sysToken'] 	= C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf, $requesturl, 1800);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
	}
	
	/**
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userInsert($userinfo){
    	$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$requesturl 		= array_shift($conf);
		$conf['action'] 	= 'addApiUser';
		$conf['newInfo'] 	= base64_encode(json_encode($userinfo));
		$conf['sysName'] 	= C('AUTH_SYSNAME');
		$conf['sysToken'] 	= C('AUTH_SYSTOKEN');
		$result = callOpenSystem($conf, $requesturl, 1800);
		$data = json_decode($result,true);
		if (isset($data['errCode'])&&$data['errCode']>0) {
			self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
			return false;
		}else{
			return $data;
		}
		require_once WEB_PATH."api/include/functions.php";
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.addUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiUser',
				'newInfo' => $newInfo,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$addUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$addUserInfo	= json_decode($addUserInfo,true);
		if($addUserInfo['userId']){
			return "ok";
		}else {
			echo $addUserInfo['errMsg'];
			return false;
		}
	}
	
	/**
	 * UserModel::userUpdate()
	 * 修改用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userUpdate($newInfo,$userToken){
		require_once WEB_PATH."api/include/functions.php";
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.updateUserInfo.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'updateUserInfo',
				'newInfo' => $newInfo,  
				'userToken' => $userToken,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$updateUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$updateUserInfo	= json_decode($updateUserInfo,true);
		if($updateUserInfo['errCode']=='0'){
			return "ok";
		}else {
			echo $updateUserInfo['errMsg'];
			return false;
		}
	}
	
	/**
	 * UserModel::userDelete()
	 * 删除用户走开放系统
	 * add by 管拥军 2013-08-23
	 * @return  bool
	 */
    public static function userDelete($userToken){
		require_once WEB_PATH."api/include/functions.php";
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.deleteApiUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'userToken' => $userToken,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$deleteUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$deleteUserInfo	= json_decode($deleteUserInfo,true);
		if($deleteUserInfo['errCode']=='0'){
			return "ok";
		}else {
			echo $deleteUserInfo['errMsg'];
			return false;
		}
	}
    
    /**
     * 根据函数名获取对应的配置信息
     * @param string $fun
     * @return array
     * @author lzx
     */
    private function getRequestConf($fun){
    	F('opensys');
    	$cachekey = "om_FromOpenModel-RequestConf_{$fun}";
    	if ($openconf = $this->cache->get($cachekey)){
    		return json_decode($openconf, true);
    	}
    	$sql = "SELECT requesturl,method,format,v,username FROM ".C('DB_PREFIX')."from_open_config WHERE functionname='{$fun}'";
    	$query = $this->dbConn->query($sql);
    	$openconf = $this->dbConn->fetch_array($query);
    	if (empty($openconf)) $this->cache->set($cachekey, json_encode($openconf), 600);	
    	return $openconf;
    }
    
	/**
	 * 切换返回数组的KEY值
	 * @param array $data
	 * @return array
	 * @author lzx
	 */
	private function changeArrayKey($data){
		$key = $this->_key;
		if (empty($key)||empty($data)||!isset($data[0][$key])){
			return $data;
		}
		$reulst = array();
		foreach ($data AS $k=>$list){
			$reulst[$list[$key]] = $list;
		}
		unset($data);
		$this->_key = '';
		return $reulst;
	}
    
	/**
	 * 获取错误信息
	 * @return string msg
	 * @author lzx
	 */
	public function getErrorMsg(){
		return self::$errMsg;
	}
}
?>