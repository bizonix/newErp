<?php
class FromOpenConfigModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	/**
	 * 获取错误信息
	 * @return array
	 * @author lzx
	 */
	public function getFromOpenConfigLists($page=1, $perpage=50, $sort='ORDER BY id DESC'){
		return $this->sql($this->getFromOpenConfigSql())->sort($sort)->page($page)->perpage($perpage)->select(array('cache', 'mysql'));
	}
	/**
	 * 获取错误数量
	 * @return array
	 * @author lzx
	 */
	public function getFromOpenConfigCount(){
		return $this->sql($this->replaceSql2Count($this->getFromOpenConfigSql()))->count();
	}
	
	public function getFromOpenConfigSql(){
		return "SELECT * FROM ".C('DB_PREFIX')."from_open_config WHERE is_delete=0 ";
	}
	
	/**
	 * 根据id获取接口信息
	 * @param id
	 * @return array
	 * @author yxd
	 */
	public function act_getfromOpenConfigByid($id){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE id=$id AND is_delete=0")->limit("*")->select(array('cache', 'mysql'));
	}
	
}

?>
