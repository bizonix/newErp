<?php
/*
 * 缓存用户信息的model
 * 涂兴隆 2013/7/16
 */

defined('WEB_PATH') ? '' : exit;
class UserCacheModel {
    public static $errCode = 0;
    public static $errMsg = '';
	public static $url1 = 'http://gw.open.valsun.cn:88/router/rest?';  //开放系统入口地址
	public static $url = 'http://idc.gw.open.valsun.cn/router/rest?';  //开放系统入口地址
	public static $token = '18006c5d80cf4a05518e382adccb3469'; //用户token(222)测试用

    protected static $dbConn;    //数据库连接

    /*
     * 初始化数据库
     */
    private function initDB(){
        global $dbConn;
        self::$dbConn = $dbConn;
    }

    /*
     * 缓存用户信息到本地
     * 参数 $userid 用户id $token token
     * 返回 操作成功返回用户信息 失败返回 false
     */
    public static function userInfoCache($token){
		global $memc_obj;
        $data=Auth::getAllUserInfo($token);    //鉴权系统拉取权限
        $userinfo = json_decode($data, TRUE);
        if(json_last_error() != JSON_ERROR_NONE){  //json数据解析出错
            $errCode = 1;
            $errMsg = '解析鉴权系统返回json出错！';
            return false;
        }
        //存储用户权限信息到memcache
		$memkey = md5("allUserInfo");
		$memresult = $memc_obj->get($memkey);
		if(!$memresult){
        	self::cacheInfoToMemcache($memkey, $userinfo);
		}
        //self::cacheLocalPower($userid);
        return $userinfo;
    }

	public static function getSkuInfo($sku) {
		$sku = isset($sku) ? trim($sku) : '';
		if($sku == '') {
			return false;
		}
		global $memc_obj;
		$ret = $memc_obj->get_extral("sku_info_".$sku);
		if($ret) {
			return $memc_obj->get_extral("sku_info_".$sku);
		} else {
			//echo '2222222';
		}
	}

	public static function goodsInfosCache($fields,$where){
		global $memc_obj;
        //存储用户权限信息到memcache
		$memkey = md5("pc_goods".trim($where));
		$memresult = $memc_obj->get($memkey);
		if(!$memresult){
			$data=self::getGoodsInfos($fields,$where);    //鉴权系统拉取权限
			//$userinfo = json_decode($data, TRUE);
			if(json_last_error() != JSON_ERROR_NONE){  //json数据解析出错
				$errCode = 1;
				$errMsg = '解析鉴权系统返回json出错！';
				return false;
			}
			self::cacheInfoToMemcache($memkey, $data);
		}else{
			$data = unserialize($memresult);
		}
		return $data;
	}

    /*
    * 获取缓存在本地的用户信息
    * 参数  $userid  用户userid
    * 返回  用户信息的关联数组 没找到信息 则返回空数组
    */
    public static function getLocalUserInfo($userid){
        self::initDB();
        $sql = "select * from pc_user where userPowerId=$userid";
        $row = sself::$dbConn->fetch_first();
        if(empty($row)){    //没找到用户信息
            return array();
        } else {
            return $row;
        }
    }

    /*
     * 将用户信权限息缓存在memcache中
     * $userid,$userinfo 用户id 用户信息
     * memcache键值 为 C('CACHEGROUP')+ userid + _power
     */
    protected function cacheInfoToMemcache($memcacheKey,$memcacheValue){
        global $memc_obj;
        $isok = $memc_obj->set($memcacheKey, serialize($memcacheValue),C('CACHELIFETIME'));
        if(!$isok){
            $errCode = 2;
            $errMsg = 'memcache缓存权限出错!';
        }
        return $isok;
    }

	//自定义过期时间
	protected function cacheInfoToMemcache2($memcacheKey,$memcacheValue, $cachelifetime){
        global $memc_obj;
        $isok = $memc_obj->set($memcacheKey, serialize($memcacheValue), $cachelifetime);
        if(!$isok){
            $errCode = 2;
            $errMsg = 'memcache缓存权限出错!';
        }
        return $isok;
    }

    /*
     * 获得权限列表
     * $userid 用户id
     * 返回值 成功返回权限数组 失败返回false
     */
    public static function getPowerList($userid){
        global $memc_obj;
        $result = $memc_obj->get($userid.'_power');
        if(!$result){   //没找到信息
            $errCode = 3;
            $errMsg = '没找到权限信息缓存！';
            return $result;
        }
        $data = unserialize($result) ;
        if(!$data){     //反序列化失败
            $errCode = 4;
            $errMsg = '反序列化权限信息失败！';
            return $result;
        }
        return $data;
    }

    /*
     * 缓存本地权限到系统
     * 涂兴隆
     */
    public static function cacheLocalPower($uid){
        global $memc_obj;
        $usermanager = new localUserManageModel();
        $userinfo =$usermanager->getUserInfoById($uid);
        $powerlist = $usermanager->translatePowerList(unserialize($userinfo['powerlist']));
        $powerlist = serialize($powerlist);
        if(empty($userinfo)){   //没找到本地用户信息 则存空数组
            $memc_obj->set($uid.'_localpower', serialize(array()),C('CACHELIFETIME'));
        }else{
            $r = $memc_obj->set($uid.'_localpower', $powerlist,C('CACHELIFETIME'));
        }
    }

    /*
     * 从memcache获得本地权限
     */
    public static function getLocalPowerList($userid){
        global $memc_obj;
        $result = $memc_obj->get($userid.'_localpower');
        if(!$result){   //没找到信息
            $errCode = 3;
            $errMsg = '没找到权限信息缓存！';
            return $result;
        }
        $data = unserialize($result) ;
        if(!$data){     //反序列化失败
            $errCode = 4;
            $errMsg = '反序列化权限信息失败！';
            return $result;
        }
        return $data;
    }

	/*
     * 获取goods信息
     */
	public static function getGoodsInfos($fields = '*',$where = 'id=1'){

        if(empty($fields) || empty($where)){   //参数不完整
            self::$errCode = 301;
            self::$errMsg = '参数信息不完整';
            return false;
        }else{
			$paramArr = array(
				/* API系统级输入参数 Start */
				'method' => 'pc.goods.info.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
				'username'	 => 'valsun.cn',
				/* API系统级参数 End */

				/* API应用级输入参数 Start*/
				'fields' =>  $fields,  //返回字段
				'where' => $where, //需要搜索的字段
			);

			//生成签名
			$sign = createSign($paramArr,self::$token);
			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url
			$urls = self::$url.$strParam;
			//echo $urls,"<br/>";exit;

			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			//$result = file_get_contents($urls);
			$data	= json_decode($result,true);
			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = "000";
        		self::$errMsg = "is empty!";
			}
		}
    }

    /**
     * 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by zqt
     */
	public static function getOpenSysApi($method, $paArr){
		include_once WEB_PATH."api/include/functions.php";
        if(empty($method) || empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{
			$paramArr = array(
				'format' => 'json',
					 'v' => '1.0',
				'username'	 => 'valsun.cn'
			);
            $paramArr['method'] = $method;//调用接口名称，系统级参数
            foreach($paArr as $key=>$value){
                if(!is_array($value)){//如果传递的应用级参数不是数组的话，直接加入到paramArr中
                    $paramArr[$key] = $value;
                }else{
                    $paramArr['jsonArr'] = base64_encode(json_encode($value));//对数组进行jsonencode再对其进行base64编码进行传递，否则直接传递数组会出错
                }
            }
			//生成签名
			$sign = createSign($paramArr,self::$token);
			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url
			$urls = self::$url.$strParam;
			//echo $urls,"<br/>";exit;

			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			//$result = file_get_contents($urls);
			$data	= json_decode($result,true);

			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = "000";
        		self::$errMsg = "is empty!";
			}
		}
    }
	
	/**
     * 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by hws
     */
	public static function callOpenSystem($paArr,$type = "get"){
		include_once WEB_PATH."api/include/functions.php";
        if(empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{		
            $paramArr = $paArr;
			$paramArr['format']   = 'json';
			$paramArr['v'] 		  = '1.0';
			
			//生成签名
			if($type == "post"){
				$paramArr['app_key'] 	= 'valsun.cn';
				$paramArr['protocol']	= 'param2';
				$sign = createSignP($paramArr,self::$token);
			}else{
				$paramArr['username'] = 'valsun.cn';
				$sign = createSign($paramArr,self::$token);
			}

			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url       
			$urls = self::$url.$strParam;
			//echo $urls,"<br/>";
			//连接超时自动重试3次
			$cnt=0;
			if($type == "post"){
				while($cnt < 3 && ($result=@curl($urls,$paramArr))===FALSE) $cnt++;
			}else{
				while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			}
			//$result = file_get_contents($urls);
			//print_r($result);die;
			$data	= json_decode($result,true);

			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = $data['errCode'];
        		self::$errMsg  = $data['errMsg'];
				return false;
			}
		}
    }
	
	/**
     * 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by hws
     */
	public static function callOpenSystem1($paArr,$type = "get"){
		include_once WEB_PATH."api/include/functions.php";
        if(empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{		
            $paramArr = $paArr;
			$paramArr['format']   = 'json';
			$paramArr['v'] 		  = '1.0';
			$paramArr['username'] = 'valsun.cn';

			//生成签名
			if($type == "post"){
				$sign = createSignP($paramArr,self::$token);
			}else{
				$sign = createSign($paramArr,self::$token);
			}

			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url       
			$urls = self::$url1.$strParam;
			//echo $urls,"<br/>";
			//连接超时自动重试3次
			$cnt=0;
			if($type == "post"){
				while($cnt < 3 && ($result=@curl($urls,$paramArr))===FALSE) $cnt++;
			}else{
				while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			}
			//$result = file_get_contents($urls);
			//print_r($result);die;
			//$data	= json_decode($result,true);

			if($result){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return 1;
			}else{
				self::$errCode = $data['errCode'];
        		self::$errMsg  = $data['errMsg'];
				return 0;
			}
		}
    }
	
	/**
     * 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by hws
     */
	public static function callOpenSystem2($paArr,$type = "get"){
		include_once WEB_PATH."api/include/functions.php";
        if(empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{		
            $paramArr = $paArr;
			$paramArr['format']   = 'json';
			$paramArr['v'] 		  = '1.0';

        	//生成签名
			if($type == "post"){
				$paramArr['app_key'] 	= 'valsun.cn';
				$paramArr['protocol']	= 'param2';
				$sign = createSignP($paramArr,self::$token);
			}else{
				$paramArr['username'] = 'valsun.cn';
				$sign = createSign($paramArr,self::$token);
			}

			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url       
			$urls = self::$url1.$strParam;
			if($_GET['test']=='test'){
				echo $urls,"<br/>";//exit;
			}
			//连接超时自动重试3次
			$cnt=0;
			if($type == "post"){
				while($cnt < 3 && ($result=@curl($urls,$paramArr))===FALSE) $cnt++;
			}else{
				while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			}
			//$result = file_get_contents($urls);
			$data	= json_decode($result,true);
            
			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = $data['errCode'];
        		self::$errMsg  = $data['errMsg'];
				return false;
			}
		}
    }
	
	/**
     * 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by hws
     */
	public static function callOpenSystemForRq($paArr,$type = "get"){
		include_once "/data/web/wh.valsun.cn/api/include/functions.php";
        if(empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{		
            $paramArr = $paArr;
			$paramArr['format']   = 'json';
			$paramArr['v'] 		  = '1.0';
			$paramArr['username'] = 'valsun.cn';

			//生成签名
			if($type == "post"){
				$sign = createSignP($paramArr,self::$token);
			}else{
				$sign = createSign($paramArr,self::$token);
			}

			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";

			//构造Url       
			$urls = self::$url.$strParam;
			//echo $urls,"<br/>";
			//连接超时自动重试3次
			$cnt=0;
			if($type == "post"){
				while($cnt < 3 && ($result=@curl($urls,$paramArr))===FALSE) $cnt++;
			}else{
				while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			}
			//$result = file_get_contents($urls);
			//print_r($result);die;
			$data	= json_decode($result,true);

			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = $data['errCode'];
        		self::$errMsg  = $data['errMsg'];
				return false;
			}
		}
    }
}