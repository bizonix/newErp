<?php
class TopmenuModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	/**
	 * 获取菜单信息
	 * @return array
	 * @author yxd
	 */
	public function getTopmenuLists($condition,$page=1, $perpage=50, $sort='ORDER BY id DESC'){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE ".$this->getTopmenuSql($condition))->sort($sort)->page($page)->perpage($perpage)->select(array('mysql'),0);
	}
	/**
	 * 获取menu数量
	 * @return array
	 * @author yxd 
	 */
	public function getTopmenuCount($condition){
		return $this->sql($this->replaceSql2Count("SELECT * FROM ".$this->getTableName()." WHERE ".$this->getTopmenuSql($condition)))->count();
	}
	
	public function getTopmenuSql($data){
		$where     = implode(" AND ", array2where($data));
		return $where ;
	}
	/**
	 * 获取一级导航
	 * @return array;
	 * @author yxd
	 */
	public function getmenuTop1(){
		return $this->sql(" SELECT id,name FROM ".$this->getTableName()." WHERE position=1 AND is_delete=0 ")->limit("*")->select(array('mysql'),0);
	}	
	
	/**
	 * 获取二级导航
	 * @return array;
	 * @author yxd
	 */
	public function getmenuTop2(){
		return $this->sql(" SELECT id,name FROM ".$this->getTableName()." WHERE position=2 AND is_delete=0 ")->limit("*")->select(array('mysql'),0);
	}
	
	/**
	 * 通过父级model获取父级id
	 * @param string $pmodel
	 * @return int id
	 * @author yxd
	 */
	public function getPidByModel($pmodel){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE model='$pmodel' AND is_delete=0 ")->limit("*")->select(array('mysql'),0);
	}
	
	
	/**
	 * 通过父级id获取父级model
	 * @param string $pid
	 * @return int model
	 * @author yxd
	 */
	public function getModelBypid($pid){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE id='$pid' AND is_delete=0 ")->limit("*")->select(array('mysql'),0);
	}
	
	/**
	 * 通过model获取导航父级id
	 * @param  string model
	 * @return int pid
	 * @param yxd
	 */
	public function getPidSortByMod($model){
		 $data    = $this->sql("SELECT pid,sort,model FROM ".$this->getTableName()." WHERE model='$model' and is_delete=0 ")->limit("*")->select(array('mysql'),0);
		 $pid     = $data[0]['pid'];
		 $model   = $data[0]['model'];
		 if(pid==0 && strpos($model,"-")){
		 	$sort    = $data[0]['sort'];
		 }else{
		 	$sort    = $this->sql("SELECT sort FROM ".$this->getTableName()." WHERE id=$pid and is_delete=0 ")->limit("*")->select(array('mysql'),0);
		 	$sort    = $sort[0]['sort'];
		 }
		 
		 return $sort; 
	}
	/**
	 * 通过model获取导航排序sort
	 * @param string model
	 * @return int sort
	 * @author
	 */
	public function getSortByMod($model){
		$sort    = $this->sql("SELECT sort FROM ".$this->getTableName()." WHERE model='$model' and is_delete=0 ")->limit("*")->select(array('mysql'),0);
		$sort    = $sort[0]['sort'];
		return $sort;
	}
	/**
	 * @param string $model
	 * @return array
	 * @author yxd
	 */
	public function getMenuByModel($model){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE model='$model' AND is_delete=0 ")->limit("*")->select(array('mysql'),0);
	}
}

?>