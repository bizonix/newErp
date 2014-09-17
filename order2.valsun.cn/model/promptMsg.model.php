<?php
class PromptMsgModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	/**
	 * 获取错误信息
	 * @return array
	 * @author lzx
	 */
	public function getPromptMsgLists($page=1, $perpage=50, $sort='ORDER BY id DESC'){
		return $this->sql($this->getPromptMsgSql())->sort($sort)->page($page)->perpage($perpage)->select(array('cache', 'mysql'));
	}
	/**
	 * 获取错误数量
	 * @return array
	 * @author lzx
	 */
	public function getPromptMsgCount(){
		return $this->sql(preg_replace("/^SELECT\s*\*/", "SELECT COUNT(*) AS count", $this->getPromptMsgSql()))->count();
	}
	
	public function getPromptMsgSql(){
		return "SELECT * FROM ".C('DB_PREFIX')."prompt_msg WHERE is_delete = 0";
	}
	
	/**
	 * 通过ID获取错误信息
	 * @param id
	 * @return array
	 * @author xyd
	 */
	public function getPromptMsgByid($id){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE id= $id and is_delete =0")->limit('*')->select(array('cache', 'mysql'));
	}
}

?>