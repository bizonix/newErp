<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '../../.');

/*
 * 封装亚马逊请求基础类
 */
class AmazonSession{
	
	protected $config;
	protected $siteID;
	protected $account;
	protected $logname;
	
	public function __construct(){
		//自动加载类
		spl_autoload_register(array('AmazonSession', 'autoload'));
		$this->logname = date("Y-m-d_H-i-s").rand(1, 9).'.log';
	}
	
	//定义自动加载
	public function autoload($className){
		$filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . $filePath)){
			require_once $filePath;
			return;
		}
	}
	
	public function setRequestConfig($authorize){
		if (empty($authorize)||!is_array($authorize)){
			return false;
		}
		define('APPLICATION_NAME', 		$authorize['appname']);
		define('APPLICATION_VERSION', 	$authorize['appversion']);
		define('AWS_ACCESS_KEY_ID', 	$authorize['acckeyid']);
		define('AWS_SECRET_ACCESS_KEY', $authorize['acckey']);  
		define('MERCHANT_ID', 			$authorize['merchantid']);
		define('MARKETPLACE_ID', 		$authorize['marketplaceid']);
		define('SPIDER_ACOUNT', 		$authorize['account']);
		define('SPIDER_SITE', 			$authorize['site']);
		define('SAVE_LOG_NAME', 		$this->logname);
		$this->config = array (
		   'ServiceURL' 	=> $authorize['serviceUrl'],
		   'ProxyHost'  	=> null,
		   'ProxyPort'  	=> -1,
		   'MaxErrorRetry'  => 5,
		);
		return true;
	}
}
?>