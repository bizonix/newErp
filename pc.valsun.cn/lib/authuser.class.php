<?php
/**
 * 类名：AuthUser
 * 功能：岗位的权限控制
 * 版本：1.0
 * 日期：2013/8/7
 * 作者：林正祥
 * 配置文件增加设置：
 * USER_AUTH_ON 		是否需要认证
 * USER_AUTH_ID 		认证用户id
 * USER_AUTH_COMPANY 	认证公司id
 * USER_AUTH_TYPE 		认证类型
 * USER_AUTH_KEY 		认证识别号
 * NOT_AUTH_NODE 		无需认证节点
 * USER_AUTH_GATEWAY 	认证网关
 * TABLE_USER_INFO 		用户表名称
 */

class AuthUser {

    /**
     * 验证当前访问节点是否有权限
     * @param string $module	模块名称
     * @param string $node		节点名称
     * @return bool ture/false:
     */
    static function checkLogin($module, $node){
        // 判断该项目是否需要认证
        if (C('USER_AUTH_ON')===false){
        	return true;
        }
        // 判断当前模块是否为不需要认证模块
        if (C('NOT_AUTH_NODE')!=''){
        	$notauths = explode(',', C('NOT_AUTH_NODE'));
        	if (in_array($module.'-'.$node, $notauths)){
        		return true;
        	}
        }
        // 认证方式1为登陆认证，2为实时认证
        if (C('USER_AUTH_TYPE')===1){
        	$accesslists = isset($_SESSION[C('USER_AUTH_KEY')]) ? $_SESSION[C('USER_AUTH_KEY')] : AuthUser::getAccessList();
        }
   		if (C('USER_AUTH_TYPE')===2){
        	$accesslists = AuthUser::getAccessList();
        }
        if (isset($accesslists[$module])&&in_array($node, $accesslists[$module])){
        	return true;
        }else{
        	return false;
        }
    }

    /**
     * 获取用户对应权限数据
     * @return multitype:
     */
    static public function getAccessList(){
    	
    	global $dbConn;
    	
    	// 判断检验权限所需基础数据是否齐全
    	if (!isset($_SESSION[C('USER_AUTH_ID')])){
    		return array();
    	}
        $userid = $_SESSION[C('USER_AUTH_ID')];
        
        $sql = "SELECT user_power FROM ".C('TABLE_USER_INFO')." WHERE user_id='{$userid}' LIMIT 1";
        $_accesslist = $dbConn->fetch_first($sql);
		if(empty($_accesslist)){
			return array();
		}
		$accesslists = json_decode($_accesslist['user_power'], true);
		$aceessresults = is_array($accesslists) ? $accesslists : array();
		//缓存到session中
		$_SESSION[C('USER_AUTH_KEY')] = $aceessresults;
		unset($_accesslist, $accesslists);
		return $aceessresults;
    }
}