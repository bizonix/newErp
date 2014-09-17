<?php
	/********************************************************************************
	  *******************************************************************************/
class AliexpressSession
{
	protected $server		  =	'https://gw.api.alibaba.com';
	protected $rootpath		  =	'openapi';					//openapi,fileapi
	protected $protocol		  =	'param2';					//param2,json2,jsonp2,xml2,http,param,json,jsonp,xml
	protected $ns			  =	'aliexpress.open';
	protected $version		  =	1;
    //这里先写死，后面动态根据账号赋值
          
	protected $appKey		  =	'895611';					//填自己的
	protected $appSecret	  =	'EcwaA6#3H:p';				//填自己的
	protected $refresh_token  =	"96f3a689-a9a8-4858-bd37-7d53d673c39b";//填自己的
    
	protected $callback_url	  =	"http://202.103.191.209:88/aliexpress/callback.php";
	protected $access_token ;
	
    protected $logname;
    protected $account;
    
    function __construct() {
	   $this->logname = date("Y-m-d_H-i-s").rand(1, 9).'.log';
	}

	public function setConfig($account, $appKey, $appSecret, $refresh_token){
	    $this->account		 = $account;
		$this->appKey		 = $appKey;
		$this->appSecret	 = $appSecret;
		$this->refresh_token = $refresh_token;
	}	

	public function doInit(){
		$token = $this->getToken();       
		$this->access_token	= $token->access_token;       
	}
    
    public function Curl($url,$vars=''){
		$ch =curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($vars));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		$content = curl_exec($ch);
		curl_close($ch);
        //写传入和返回结果日志
        $this->backupRequestAndResponseXml($url.'/'.http_build_query($vars), $content);
		return $content;
	}
	
	//生成签名
	public function Sign($vars){
		$str = '';
		ksort($vars);
		foreach($vars as $k=>$v){
			$str .= $k.$v;
		}
		return strtoupper(bin2hex(hash_hmac('sha1',$str,$this->appSecret,true)));
	}
	
	public function getCode(){
		$getCodeUrl = $this->server .'/auth/authorize.htm?client_id='.$this->appKey .'&site=aliexpress&redirect_uri='.$this->callback_url.'&_aop_signature='.$this->Sign(array('client_id' => $this->appKey,'redirect_uri' =>$this->callback_url,'site' => 'aliexpress'));
		return '<a href="javascript:void(0)" onclick="window.open(\''.$getCodeUrl.'\',\'child\',\'width=500,height=380\');">请先登陆并授权</a>';
	}
	
	//获取授权
	public function getToken(){
		if(!empty($this->refresh_token)){
			$getTokenUrl = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/system.oauth2/refreshToken/{$this->appKey}";
			$data = array(
				'grant_type'	=> 'refresh_token',		//授权类型
				'client_id'		=> $this->appKey,				//app唯一标示
				'client_secret'	=> $this->appSecret,			//app密钥
				'refresh_token'	=> $this->refresh_token,		//app入口地址
			);
			$data['_aop_signature'] = $this->Sign($data); 
			return json_decode($this->Curl($getTokenUrl,$data));			
		}else{
			$getTokenUrl="{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/system.oauth2/getToken/{$this->appKey}";
			$data =array(
				'grant_type'		=> 'authorization_code',	//授权类型
				'need_refresh_token'=> 'true',				//是否需要返回长效token
				'client_id'			=> $this->appKey,				//app唯一标示
				'client_secret'		=> $this->appSecret,			//app密钥
				'redirect_uri'		=> $this->redirectUrl,			//app入口地址
				'code'				=> $_SESSION['code'],	//bug
			);
			return json_decode($this->Curl($getTokenUrl,$data));
		}
	}
	
    //日志方法
	private function backupRequestAndResponseXml($requestBody, $responseBody){
		$tracelists = debug_backtrace();
		$savelist = array('class'=>'errorclass', 'function'=>'errorfunction');
		foreach ($tracelists AS $tracelist){
			// /data/web/re.order.valsun.cn/action/buttplatform/ebayButt.action.php  调用demo
			if (preg_match("/action\/buttplatform\/[a-z]*\.action\.php$/i", $tracelist['file'])>0){
				$savelist = array('class'=>$tracelist['class'], 'function'=>$tracelist['function']);
				break;
			}
		}
		$savecontent = "##############################################  requestBody start ###################################################\n\n".
		$savecontent .= "{$requestBody}\n\n";
		$savecontent .= "##############################################  requestBody end   ###################################################\n\n\n\n";
		$savecontent .= "############################################## responseBody start ###################################################\n\n";
		$savecontent .= "{$responseBody}\n\n";
		$savecontent .= "##############################################  responseBody end  ###################################################";
		$savepath	= EBAY_RAW_DATA_PATH.'aliexpress/'.$savelist['class'].'/'.$savelist['function'].'/'.$this->account.'/'.date('Y-m').'/'.date('d').'/'.$this->logname;
		write_log($savepath, $savecontent);
	}
}
?>