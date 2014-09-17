<?php
//淘宝抓单类-标记发货
include_once WEB_PATH."lib/api/taobao/taobaoSession.php";
class TaobaoLogisticsOfflineSend extends TaobaoSession{
    public function __construct(){
		parent::__construct();
	}
	
	/*****************************************************
     *	标记发货功能处理
     */
    function taobaoLogisticsOfflineSend($recordnumber, $company_code, $tracknumber){    
    	$paramArr = array(    
    	    	'method' => 'taobao.logistics.offline.send', 
    		   'session' => $this->session, 
    	     'timestamp' => date('Y-m-d H:i:s'),			
    		    'format' => 'json', 
        	   'app_key' => $this->appKey, 			
    	    		 'v' => '2.0',  	   
    		'sign_method'=> 'md5',
    		       'tid' => $recordnumber,  
    	  'company_code' => $company_code, 
    	 	   'out_sid' => $tracknumber
    
    	);
    	$sign		= $this->tmall_createSign($paramArr,$this->appSecret);
    	$strParam	= $this->tmall_createStrParam($paramArr);
    	$strParam .= 'sign='.$sign;
    	$urls	=	$this->url.$strParam;   	
    	$cnt	=	0;	
    	while($cnt < 3 && ($result=@$this->tmall_vita_get_url_content($urls))===FALSE) $cnt++;
    	$json_data	=	json_decode($result,true);
    	return $json_data;   
    }
}
?>