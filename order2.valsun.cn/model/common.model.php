<?php
/*
*模板通用操作类
*@add by : linzhengxiang ,date : 20140525
*/
class CommonModel extends ValidateModel{
	
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 插入信息
	 * @param array $data
	 * @author lzx
	 */
	public function insertData($data){
		$fdata = $this->formatInsertField($this->getTableName(), $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		
		if ($this->checkIsExists($fdata)){
			return false;
		}
		return $this->sql("INSERT INTO ".$this->getTableName()." SET ".array2sql($fdata))->insert();
	}
	
	/**
	 * 更新信息
	 * @param array $data
	 * @author lzx
	 */
	public function updateData($id, $data){
		$id = intval($id);
		if ($id==0){
			return false;
		}
		$fdata = $this->formatUpdateField($this->getTableName(), $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("UPDATE ".$this->getTableName()." SET ".array2sql($fdata)." WHERE id={$id}")->update();
	}
	
	public function replaceData($id, $data, $column='id'){
		$id = intval($id);
		$column = addslashes($column);
		
		if ($id==0){
			return false;
		}
		$fdata = $this->formatUpdateField($this->getTableName(), $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		
		if ($this->checkIsExists($fdata)){
			return false;
		}
		$check = $this->sql("SELECT COUNT(*) AS count FROM {$this->getTableName()} WHERE {$column}={$id}")->count();
		
		if ($check==0) {
			$fdata[$column] = $id;
			return $this->insertData($fdata);
		}else{
			return $this->sql("UPDATE ".$this->getTableName()." SET ".array2sql($fdata)." WHERE {$column}={$id}")->update();
		}
	}
	
	/**
	 * 删除信息
	 * @param array $data
	 * @author lzx
	 */
	public function deleteData($id){
		$id = intval($id);
		if ($id==0){
			return false;
		}
		return $this->sql("UPDATE ".$this->getTableName()." SET is_delete=1 WHERE id={$id}")->delete();
	}
	
	/**
	 * 删除信息
	 * @param array $data
	 * @author lzx
	 */
	public function replaceSql2Count($sql){
		if (preg_match("/(`[a-z]*`)\.\*/", $sql)>0){
			return preg_replace("/(`[a-z]*`)\.\*/", "COUNT(\$1.id) AS count", $sql);
		}else if(preg_match("/^SELECT\s*\*/i", $sql)>0){
			return preg_replace("/^SELECT\s*\*/i", "SELECT COUNT(*) AS count", $sql);
		}else{
			return false;
		}
	}
	
	public function checkIsExists($data){
		return false;
	}
	
	
	public function resetCache(){
		$this->recache = true;
	}
	
	public function getErrorMsg(){
		return self::$errMsg;
	}
}