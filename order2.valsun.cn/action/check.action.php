<?php
/*
 *通用验证方法类
 *@add by : linzhengxiang ,date : 20140523
 */
class CheckAct extends CommonAct{
	
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 验证国家简码是否符合规范和是否存在
	 * @param string $shortcode
	 */
	protected function act_checkCountryCode ($shortcode){
		if (preg_match("/^[A-Z]{2,3}$/", $shortcode)===0){
			$this->errCode = 10001;
			return false;
		}
		/*查询模型验证 待完成*/
		return true;
	}
	
	/**
	 * 验证国家名称是否符合规范和是否存在
	 * @param string $shortcode
	 */
	protected function act_checkCountryName ($countryname){
		if (preg_match("/^[A-Z]{1}/", $countryname)===0){
			$this->errCode = 10002;
			return false;
		}
		/*查询模型验证 待完成*/
		return true;
	}
	
	/**
	 * 验证SKU是否符合规范和是否存在
	 * @param string $sku
	 */
	protected function act_checkSkuEffect ($sku){
		if (preg_match("/^[A-Z0-9]{3}/", $sku)===0){
			$this->errCode = 10003;
			return false;
		}
		/*查询模型验证 待完成*/
		return true;
	}
	
	protected function act_formatField(){
		
	}
	
}