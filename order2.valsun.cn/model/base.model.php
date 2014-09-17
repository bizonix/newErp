<?php
/*
*基础操作类(model)
*@add by Herman.Xi ,date20130901
*@modify by : linzhengxiang ,date : 20140523
*/
defined('WEB_PATH') ? '' : exit;
class BaseModel{
	
	
	protected $dbConn  = '';
	protected $cache   = '';
	protected $recache = false;
	protected $options = array();
	private   static $_sql    = array();
	protected static $errMsg  = array();
	
	//构造函数自动加载DB对象
	public function __construct(){
		if (!is_object($this->dbConn)){
			$this->_initDB();
		}
		if (!is_object($this->cache)){
			$this->_initCache();
		}
	}
	
	//魔法函数，连贯操作的实现
	public function __call($method,$args){
		$allowfun = array('sql', 'key', 'sort', 'perpage', 'page', 'limit');
		$fun = strtolower($method);
        if(in_array($fun, $allowfun, true)) {
            $this->options[$fun] = $args[0];
            return $this;
        }else if (preg_match("/^get([a-z]+)ById$/i", $method, $match)>0){
        	$source = isset($args[1]) ? $args[1] : array('mysql');
        	$cachetime = isset($args[2]) ? $args[2] : 600;
        	$data = $this->sql("SELECT * FROM ".C('DB_PREFIX').hump2underline(lcfirst($match[1]))." WHERE id=".intval($args[0]))->limit(1)->select($source, $cachetime);
        	return isset($data[0]) ? $data[0] : array();
        }else{
        	debug_print_backtrace();
        	exit("BaseModel __call {$method} not exist! ");
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
	
	protected function resetConnect() {
		//待开发
	}
	
	private function buildConnect(){
		//待开发
	}
	
	/**
	 * 重新封装sql语句查询函数，新增缓存
	 * @param array $source   支持array('cache', 'mysql')
	 * @param int $cachetime  支持缓存才生效，设置缓存时间
	 * @return array 查询结果
	 * @author lzx
	 */
	protected function select($source=array('mysql'), $cachetime=900){
		
		$sql 	 = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$sort 	 = isset($this->options['sort']) ? trim($this->options['sort']) : '';
		$limit	 = isset($this->options['limit']) ? $this->options['limit'] : 0;
    	$page 	 = isset($this->options['page']) ? intval($this->options['page']) : 1;
    	$perpage = isset($this->options['perpage']) ? intval($this->options['perpage']) : 10;
    	
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'select');
			return array();
		}
		if (preg_match("/^\s*select/i", $sql)>0&&$limit!=='*'){
			$limit = intval($limit);
			$limit = "LIMIT ".($limit>0 ? $limit : (($page-1)*$perpage).", {$perpage}");
		}else{
			$limit = '';
		}
    	$sql = "{$sql} {$sort} {$limit}";
    	self::$_sql[] = $sql;
    	$cachekey = 'om_sql_select_'.md5($sql);
		if (in_array('cache', $source)&&!$this->recache){
			$cachedata = $this->cache->get($cachekey);
			if (!empty($cachedata)){
				return $this->changeArrayKey(json_decode($cachedata, true));
			}
		}
		if (in_array('mysql', $source)){
			$query = $this->dbConn->query($sql);
			$mysqldatas = $this->dbConn->fetch_array_all($query);
			if (in_array('cache', $source)) $this->cache->set($cachekey, json_encode($mysqldatas), $cachetime);
			return $this->changeArrayKey($mysqldatas);
		}
		return	array();
	}
	
	/**
	 * 重新封装sql语句统计函数，新增缓存
	 * @param array $source   支持array('cache', 'mysql')
	 * @param int $cachetime  支持缓存才生效，设置缓存时间
	 * @return int 查询结果
	 * @author lzx
	 */
	protected function count($source=array('mysql'), $cachetime=900){
		
		$sql = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$this->options = array();
		self::$_sql[] = $sql;
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'count');
			return 0;
		}
		$cachekey = 'om_sql_count_'.md5($sql);
		if (in_array('cache', $source)&&!$this->recache){
			$cachenum = $this->cache->get($cachekey);
			if (!empty($cachenum)){
				return $cachenum;
			}
		}
		if (in_array('mysql', $source)){
			$query = $this->dbConn->query($sql);
			$mysqldata = $this->dbConn->fetch_array($query);
			if (in_array('cache', $source)) $this->cache->set($cachekey, $mysqldata['count'], $cachetime);
			return $mysqldata['count'];
		}
	}
	
	/**
	 * 重新封装sql语句更新函数
	 * @return bool 执行结果
	 * @author lzx
	 */
	protected function update(){
		$sql = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$this->options = array();
		self::$_sql[] = $sql;
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'update');
			return false;
		}
		if (preg_match("/^\s*update/i", $sql)==0){
			self::$errMsg[10019] = get_promptmsg(10019, 'update', $sql);
			return false;
		}
		return $this->dbConn->query($sql);
	}
	
	/**
	 * 重新封装sql语句插入函数
	 * @return bool 执行结果
	 * @author lzx
	 */
	protected function insert(){
		$sql = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$this->options = array();
		self::$_sql[] = $sql;
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'insert');
			return false;
		}
		if (preg_match("/^\s*insert/i", $sql)==0){
			self::$errMsg[10019] = get_promptmsg(10019, 'insert', $sql);
			return false;
		}
		return $this->dbConn->query($sql);
	}
	
	/**
	 * 重新封装sql语句创建函数
	 * @return bool 执行结果
	 * @author lzx
	 */
	protected function create(){
		$sql = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$this->options = array();
		self::$_sql[] = $sql;
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'create');
			return false;
		}
		if (preg_match("/^\s*create/i", $sql)==0){
			self::$errMsg[10019] = get_promptmsg(10019, 'create', $sql);
			return false;
		}
		return $this->dbConn->query($sql);
	}
	
	/**
	 * 重新封装sql语句删除函数，逻辑删除
	 * @return bool 执行结果
	 * @author lzx
	 */
	protected function delete(){
		$sql = isset($this->options['sql']) ? trim($this->options['sql']) : '';
		$this->options = array();
		self::$_sql[] = $sql;
		if (empty($sql)){
			self::$errMsg[10020] = get_promptmsg(10020, 'delete');
			return false;
		}
		if (preg_match("/^\s*update.*is_delete\s*=\s*1/i", $sql)==0){
			self::$errMsg[10019] = get_promptmsg(10019, 'delete', $sql);
			return false;
		}
		return $this->dbConn->query($sql);
	}

	public function begin() {
		$this->dbConn->begin();
	}

	public function commit() {
		$this->dbConn->commit();
	}

	public function rollback() {
		$this->dbConn->rollback();
	}

	public function autoCommit() {
		mysql_query('SET autocommit=1');
	}
	
	/**
	 * 获取刚刚新增的数据行id
	 * @return int 自增主键
	 * @author lzx
	 */
	public function getLastInsertId(){
		return mysql_insert_id();
	}
	
	/**
	 * 获取刚刚新增的数据行id
	 * @return string 刚刚执行的sql语句
	 * @author lzx
	 */
	public function getLastRunSql(){
		return array_pop(self::$_sql);
	}
	
	/**
	 * 获取刚刚新增的数据行id
	 * @return array 所有被执行的sql语句
	 * @author lzx
	 */
	public function getAllRunSql(){
		return self::$_sql;
	}
	
	/**
	 * 跟进类名转化为表名
	 * @return string 数据表名
	 * @author lzx
	 */
	protected function getTableName(){
		return C('DB_PREFIX').hump2underline(lcfirst(str_replace('Model', '', get_class($this))));
	}
	
	/**
	 * 切换返回数组的KEY值
	 * @param array $data
	 * @return array
	 * @author lzx
	 */
	private function changeArrayKey($data){
		$key = isset($this->options['key']) ? trim($this->options['key']) : '';
		$this->options = array();
		if (empty($key)||empty($data)||!isset($data[0][$key])){
			return $data;
		}
		$reulst = array();
		foreach ($data AS $k=>$list){
			$reulst[$list[$key]] = $list;
		}
		unset($data);
		return $reulst;
	}
}