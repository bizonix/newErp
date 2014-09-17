<?php
class CurrencyModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	/**
	 * 汇率信息
	 * @return array
	 * @author yxd
	 */
	public function getCurrencyList( ){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."currency ")->limit("*")->select(array('mysql'));
	}
	/**
	 * 获取单条汇率信息
	 * @param  id
	 * @return array
	 * @author yxd
	 */
	public function getCurrency($id){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."currency WHERE id=$id ")->limit("*")->select(array('mysql'));
	}
}

?>
