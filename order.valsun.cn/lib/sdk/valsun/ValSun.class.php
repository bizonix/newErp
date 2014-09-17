<?php
class Valsun{

	var $method		=	"";
	var $timestamp	=	"";
	var $format		=	"json";		//返回的数据格式
	var $app_key	=	"";			//商家编码
	var $app_secret	=	"";			//申请的secret key，请不要公开！ 
	var $version	=	"1.0";		//接口版本
	var $server		=	"http://test.gw.open.valsun.cn/router/rest?";		//入口URL

	public function __construct() {
	}

	
	public function setConfig($app_key, $app_secret){
		$this->app_key		=	$app_key;
		$this->app_secret	=	$app_secret;
	}

	/***********************************************
	 *	curl 请求
	 *	@param $url		string	请求的url地址
	 *	@param $vars	array	需要post的数据(key=>val)
	 */
	public function Curl($url, $vars=''){
		//echo $url."<br>";
		$ch	=	curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($vars));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		$content	=	curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	

	/***********************************************
	 *	生成签名
	 *	@param	$paramArr	array	
	 *	@return string
	 */
	public function createSign($paramArr) { 
	    $str	=	""; 
	    ksort($paramArr); 
	    foreach ($paramArr as $key => $val) { 
	       if ($key !='' && $val !='') { 
	           $str	.=	$key.$val; 
	       } 
	    } 
	    
	    $sign	=	strtoupper(md5($str.$this->app_secret));
	    return	$sign; 
	}

}

?>