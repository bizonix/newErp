<?php
/*
 * 缓存用户信息的model
 * 涂兴隆 2013/7/16
 */

defined('WEB_PATH') ? '' : exit;

class UserCacheModel {
    public static $errCode = 0;
    public static $errMsg = '';
	public static $url = 'http://gw.open.valsun.cn/router/rest?';  //开放系统入口地址
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
	 *获取memcache中用户信息
	 */
	public function getUsernameById($userId){
		$mem = new Memcache;
		$mem->connect("192.168.200.150",11211);	
		
		$var = $mem->get('GlobalUser_'.$userId);		
		if(empty($var))
		{
			//$url = 'dev.power.valsun.cn/api/mem.php';//开发环境
			$url = 'http://power.valsun.cn/api/mem.php';//正式环境
			$urlPost = 'userId='.$userId;	
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$url);//设置你要抓取的URL
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//设置CURL参数，要求结果保存到字符串还是输出到屏幕上
			curl_setopt($curl,CURLOPT_POST,1);//设置为POST提交
			curl_setopt($curl,CURLOPT_POSTFIELDS,$urlPost);//提交的参数
			$data=curl_exec($curl);//运行CURL，请求网页
			curl_close($curl);
		}
		$var = $mem->get('GlobalUser_'.$userId);
		$mem->close();
		return $var;
	}
	
	/*
	 * 获取用户信息
	 * 根据系统级id获取用户信息
	 * $way  默认是鉴权和缓存 
	 */
	public function getUserInfoBySysId($userId,$way=1){
	    $returnar  = array(
	    	'userName'=>NULL,
	    );
	    if ($way === 1) {
	    	$info = $this->getUsernameById($userId);
	    	if (!empty($info)) {
	    	    $decode = json_decode($info, TRUE);
	    	    if (is_array($decode)) {
	    	    	$returnar['userName'] = $decode[0]['userName'];
	    	    } else {
	    	        $returnar['userName'] = '';
	    	    }
	    	}
	    } else {
	        $glu_ojb   = new GetLoacalUserModel();
	        $user      = $glu_ojb->getUserInfoBySysId($userId);
	        if (!empty($user)) {
	            $returnar['userName'] = $user['global_user_name'];
	        }
	    }
	    return $returnar;
	}
	
}