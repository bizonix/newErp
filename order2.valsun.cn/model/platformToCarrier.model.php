<?php
/**
 * 运输方式管理
 * add by yxd ,date 2014/07/08
 */
class PlatformToCarrierModel extends  CommonModel{
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 分页获取运输方式列表
	 * return array
	 */
	public function getPlatformToCarrier($data,$sort='ORDER BY id DESC'){
		$condition    =	$this->getPlatformToCarrierSql($data);
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE  $condition ")->limit("*")->select(array('mysql'));
	}
	
	/**
	 * 运输方式id获取运输方式信息
	 * param id
	 * return array
	 */
	/* public function getPlatformToCarrierByid($id){
		return $this->sql("	SELECT * FROM ".$this->getTableName()." WHERE is_delete=0 AND id=$id ")->limit("*")->select(array('mysql'));
	} */
	
	/**
	 * 根据平台id和平台上运输方式名称获取平台运输方式信息
	 */
	public function getPlatformToCarrierByINA($platformId,$platformCarrierName){
		return $this->sql(" SELECT * FROM ".$this->getTableName()." WHERE is_delete=0 AND platformId=$platformId AND platformCarrierName='$platformCarrierName' ")->limit("*")->select(array('mysql'));
	}
	/**
	 * 获取运输方式信息数量
	 */
	public function getPlatformToCarrierCount($data){
		return $this->sql($this->replaceSql2Count("SELECT * FROM ".$this->getTableName()." WHERE ".$this->getPlatformToCarrierSql($data)))->count();
	}
	/**
	 * 根据查询条件组装查询SQL语句
	 * @prama array
	 * @return string
	 * @author yxd
	 */
	public function getPlatformToCarrierSql($data){
		$where     = implode(" AND ", array2where($data));
		return $where ;
	}
	
	public function checkIsExist($data){
		$res     = $this->getPlatformToCarrier($data);
		if(count($res)>=1){
			return true;
		}else{
			return false;
		}
	}
}
?>