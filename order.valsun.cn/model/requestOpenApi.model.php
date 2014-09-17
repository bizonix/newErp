<?php
/*
 * 请求开放系统的公用model
 */
error_reporting(-1);
include(WEB_PATH."api/include/functions.php");
class RequestOpenApiModel  {
	private $url = 'http://gw.open.valsun.cn:88/router/rest?';
	
	/*
	 * 构造函数
	 */
	public function __construct(){
		
	}
	
	/*
	 * 发送请求
	 * $parrameter 参数数组
	 */
	public function sendRequest($parameter){
		$sign = createSign($parameter);			//生成签名
		$strParam = createStrParam($parameter);	//组装参数
		$strParam .= 'sign='.$sign;
		//构造Url
		$urls = $this->url.$strParam;
		$cnt=0;
		
		while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) 
			$cnt++;
		return $result;
	}
}