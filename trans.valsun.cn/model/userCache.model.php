<?php
/*
 * 缓存用户信息的model
 * 涂兴隆 2013/7/16
 */

defined('WEB_PATH') ? '' : exit;

class UserCacheModel {
    public static $errCode = 0;
    public static $errMsg = '';
    
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
        //var_dump($userinfo);exit;
        
        $sql = "select updatetime from trans_user where uid = $userid";
        $row = self::$dbConn->fetch_first($sql);
        $time = time();
        //var_dump($userinfo);exit;
        $username = mysql_real_escape_string($userinfo['userName']);
        if(!empty($row)){
            if(intval($userinfo['lastUpdateTime']) > $row['updatetime']){   //鉴权系统信息已更新 更新本地数据
                $up_sql = "update trans_user set username = '$username', updatetime=$time where uid=$userid";
                self::$dbConn->query($up_sql);
            }
        } else {    //没找到结果集 新增用户数据
            $powerlist = array();
            $powerlist = serialize($powerlist);
            $in_sql = "insert into trans_user values ($userid, '$username', '1', '$powerlist','0', $time)";
            self::$dbConn->query($in_sql);
        }
        //存储用户权限信息到memcache
        self::cacheUserInfoToMemcache($userid,  $userinfo['power']);
        self::cacheLocalPower($userid);
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
    protected function cacheUserInfoToMemcache($userid,$userpower){
        global $memc_obj;
        $isok = $memc_obj->set($userid.'_power', serialize($userpower),C('CACHELIFETIME'));
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
}