<?php
/*
 *接口基础类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfaceModel{
	
	protected $dbConn  = '';
	protected $cache   = '';
	protected static $errMsg  = array();
	private $_key = '';
	
	/**
	 * 构造函数自动加载DB对象
	 * @author lzx
	 */
	public function __construct(){
		F('opensys');   //加载开发系统相关函数库
		if (!is_object($this->dbConn)){
			$this->_initDB();
		}
		if (!is_object($this->cache)){
			$this->_initCache();
		}
	}
	
	/**
	 * 设置返回数组的key值
	 * @param string $key
	 * @return object
	 * @author lzx
	 */
	public function key($key){
		$this->_key = $key;
		return $this;
	}
	
	/**
	 * 获取错误信息
	 * @return string msg
	 * @author lzx
	 */
	public function getErrorMsg(){
		return self::$errMsg;
	}
    
    /**
     * 根据函数名获取对应的配置信息 GET
     * @param string $fun
     * @return array
     * @author lzx
     */
    protected function getRequestConf($fun){
    	$cachekey = "om_Interface_RequestConf_{$fun}";
    	if ($openconf = $this->cache->get($cachekey)){
    		return json_decode($openconf, true);
    	}
    	$sql = "SELECT requesturl,cachetime,method,format,v,username,getOrPost FROM ".C('DB_PREFIX')."from_open_config WHERE functionname='{$fun}'";
    	$query = $this->dbConn->query($sql);
    	$openconf = $this->dbConn->fetch_array($query);
        if($openconf['getOrPost'] == 2){
            $openconf['app_key'] = $openconf['username'];
            $openconf['protocol'] = 'param2';
            unset($openconf['username']);
            unset($openconf['getOrPost']);
        }
    	if (!empty($openconf)) $this->cache->set($cachekey, json_encode($openconf), intval($openconf['cachetime']));	
    	return $openconf;
    }
    
	/**
	 * 切换返回数组的KEY值
	 * @param array $data
	 * @return array
	 * @author lzx
	 */
	protected function changeArrayKey($data){
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
	 * 初始化mysqlDB
	 * @author lzx
	 */
	private function _initDB(){
		global $dbConn;
		$this->dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/**
	 * 初始化缓存
	 * @author lzx
	 */
	private function _initCache(){
		global $memc_obj;
		$this->cache = $memc_obj;
	}
}
?>