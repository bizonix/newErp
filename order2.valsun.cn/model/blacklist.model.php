<?php
/**
 * 黑名单管理
 *
 * @add by yxd ,date 2014/06/10
 */
class BlacklistModel extends  CommonModel{
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 分页获取黑名单列表
	 * return array
	 */
	public function getBlackList($data,$page=1, $perpage=50, $sort='ORDER BY id DESC'){
		$condition    =	$this->getBlacklistSql($data);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."blacklist WHERE  $condition ")->page($page)->perpage($perpage)->sort($sort)->select(array('cache', 'mysql'));
	}
	/**
	 * 外部使用接口
	 * 
	 */
	public function isExitInBlacklist($condition){
		$data    = $this->sql("SELECT id FROM ".$this->getTableName()." WHERE $condition")->limit("*")->select(array('mysql'));
		$exit    = count($data);
		if($exit>0){
			return true;
		}else{
			return false;
		}
		
	}
	/**
	 * 通过黑名单id获取黑名单信息
	 * param id
	 * return array
	 */
	public function getBlackListByid($id){
		$where    = "";
		if($id){
			$where    .=  " AND id=$id ";
		}
		return $this->sql("	SELECT * FROM ".C('DB_PREFIX')."blacklist WHERE is_delete=0 $where")->limit("*")->select(array('mysql'));
	}
	/**
	 * 获取黑名单数量
	 */
	public function getBlacklistCount($data){
		return $this->sql($this->replaceSql2Count("SELECT * FROM ".$this->getTableName()." WHERE ".$this->getBlacklistSql($data)))->count();
	}
	/**
	 * 根据查询条件组装查询SQL语句
	 * @prama array
	 * @return string
	 * @author yxd
	 */
	public function getBlacklistSql($data){
		$where     = implode(" AND ", array2where($data));
		return $where ;
	}
}
?>