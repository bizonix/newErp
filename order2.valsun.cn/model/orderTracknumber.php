<?php
/**
 * 跟踪号管理
 * @author yxd 2014/09/02
 */
class OrderTracknumberModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取为上传的跟踪号记录
	 * @param accountId 
	 * @return array
	 * @param  yxd
	 */
	public function getunUp($accountId){
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE accountId=$accountId AND isUp=1 ")->limit("*")->select(array('mysql'),0);
	}
	
}
?>