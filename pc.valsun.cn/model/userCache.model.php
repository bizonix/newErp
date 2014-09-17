<?php
/*
 * 缓存用户信息的model
 * 涂兴隆 2013/7/16
 */

defined('WEB_PATH') ? '' : exit;

class UserCacheModel {
    public static $errCode = 0;
    public static $errMsg = '';
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
    public static function userInfoCache($token, $userid){
        self::initDB();
        $data=Auth::getUserInfo($token);    //鉴权系统拉取权限
        $userinfo = json_decode($data, TRUE);
        if(json_last_error() != JSON_ERROR_NONE){  //json数据解析出错
            $errCode = 1;
            $errMsg = '解析鉴权系统返回json出错！';
            return false;
        }

        $sql = "select lastUpdateTime from pc_user where userPowerId = $userid";

        $row = self::$dbConn->fetch_first($sql);
        $time = time();
        $ip = getClientIP();        //客户端ip
        //var_dump($userinfo);exit;
        $username = mysql_real_escape_string($userinfo['userName']);
        $phone = mysql_real_escape_string($userinfo['phone']);
        $email = mysql_real_escape_string($userinfo['email']);
        if(!empty($row)){
            if(json_decode(intval($userinfo['lastUpdateTime']),TRUE) > $row['lastUpdateTime']){   //鉴权系统信息已更新 更新本地数据
                $up_sql = "update pc_user set userName = '$username', userTel='$phone', userMail='$email', userIp = '$ip', userActive = userActive+1, lastUpdateTime=$time where userPowerId=$userid";
                self::$dbConn->query($up_sql);
            }else{  //信息没有更新 则只更新登陆次数
                $up_sql = "update pc_user set userActive=userActive+1 where userPowerId=$userid";
                //echo $up_sql;exit;
                self::$dbConn->query($up_sql);
            }
        } else {    //没找到结果集 新增用户数据
            $in_sql = "insert into pc_user values (null, $userid, '$username', '', '' , '' , '' , '', '', '', '$phone', '$email', '$ip', 1, '', $time, 0, $time)";
            self::$dbConn->query($up_sql);
        }
        //存储用户权限信息到memcache
        self::cacheUserInfoToMemcache($userid,  $userinfo['power']);
        return $userinfo;
    }

    /*
    * 获取缓存在本地的用户信息
    * 参数  $userid  用户userid
    * 返回  用户信息的关联数组 没找到信息 则返回空数组
    */
    public static function getLocalUserInfo($userid){
        self::initDB();
        $sql = "select * from pc_user where userPowerId=$userid";
        $row = self::$dbConn->fetch_first();
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
    protected function cacheUserInfoToMemcache($userid,$userpower){
        global $memc_obj;
        $isok = $memc_obj->set($userid.'_power', serialize($userpower),C('CACHELIFETIME'));
        if(!$isok){
            $errCode = 2;
            $errMsg = 'memcache缓存权限出错!';
        }//var_dump($isok);exit;
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
        if($data === false){     //反序列化失败
            $errCode = 4;
            $errMsg = '反序列化权限信息失败！';
            return $result;
        }
        return $data;
    }

 /* 调用开放系统指定接口的公用方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by zqt
     */
	public static function getOpenSysApi($method, $paArr, $idc='',$decode=true){
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
            if($idc == ''){
                $url = self::$url;
            }else{
                $url = 'http://gw.open.valsun.cn:88/router/rest?';
            }
			//构造Url
			$urls = $url.$strParam;
           // echo self::$token.'<br>';
            //echo $urls;
//            exit;
			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			//print_r($result);
//            exit;
            if($decode){
              $data	= json_decode($result,true);  
            }else{
              $data = $result;  
            }
            
			
// 			var_dump($data,$result,"++___+++");exit;
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
    
    /* 调用开放系统指定接口的公用方法POST的方法
     * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
     * add by zqt
     */
	public static function getOpenSysApiPost($method, $paArr, $idc=''){
        include_once WEB_PATH."api/include/functions.php";
        if(empty($method) || empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{
			$paramArr = array(
				'format' => 'json',
					 'v' => '1.0',
				'app_key'	 => 'valsun.cn',
                'protocol'	=> 'param2'
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
			$sign = createSign2($paramArr,self::$token);
			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";
            if($idc == ''){
                $url = self::$url;
            }else{
                $url = 'http://gw.open.valsun.cn:88/router/rest?';
            }
			//构造Url
			$urls = $url.$strParam;
           // echo self::$token.'<br>';
            //echo $urls;
//            exit;
			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@Curl($urls, $paramArr))===FALSE) $cnt++;
			//print_r($result);
//            exit;
			$data	= json_decode($result,true);
// 			var_dump($data,$result,"++___+++");exit;
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

}

