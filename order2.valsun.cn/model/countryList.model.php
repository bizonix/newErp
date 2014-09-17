<?php
class CountryListModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	/**
	 * @param string regions_en 国家或地区英文
	 * @return Boolean
	 * @author yxd
	 */
	public function  checkIsExists($country){
		$checkdata = $this->sql("SELECT * FROM ".$this->getTableName()." WHERE regions_en='$country' AND is_delete=0")->select();
		if (empty($checkdata)){
			self::$errMsg[10017] = get_promptmsg(10017,'系统不存在此国家');
			return true;
		}
		return false;
	}
	/**
	 * @param string regions_en 国家或地区英文
	 * @return String  	regions_jc 国家或地区缩写
	 * @author yxd
	 */
	public function geZhByEn($country){
		$regions_jc    = $this->sql("SELECT regions_jc FROM ".$this->getTableName()." WHERE regions_en='$country' AND is_delete=0")->select();
		return $regions_jc[0]['regions_jc'];
	}
}
