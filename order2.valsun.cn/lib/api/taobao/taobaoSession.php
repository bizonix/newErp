<?php
	/********************************************************************************
	  *******************************************************************************/
class TaobaoSession
{
	protected $session; //token session
    protected $start_created; //查询交易创建时间开始
    protected $end_created; //查询交易创建时间结束
    protected $status = "WAIT_SELLER_SEND_GOODS"; //交易状态	  WAIT_SELLER_SEND_GOODS  WAIT_BUYER_CONFIRM_GOODS
    protected $buyer_nick = ''; //买家淘宝昵称
    protected $type = ''; //交易类型列表
    protected $rate_status = ''; //评价状态
    protected $tag = '';
    protected $page_no = 1;
    protected $page_size = 50;
    protected $account;
    protected $logname;
    protected $url = 'http://gw.api.taobao.com/router/rest?'; //正式环境提交URL
    protected $appKey = '21460636'; //填写自己申请的AppKey
    protected $appSecret = 'df0cb97ac64f603c799082dde8966c6b'; //填写自己申请的$appSecret
    protected $defalut_carrier = '韵达快递';

    function __construct() {
	   $this->logname = date("Y-m-d_H-i-s").rand(1, 9).'.log';
	}

	public function setConfig($account, $session, $appSecret, $appKey, $defalut_carrier)
    {
        $this->session = $session; //token session
        $this->start_created = date("Y-m-d H:i:s", strtotime("-1 day")); //查询交易创建时间开始
        $this->end_created = date("Y-m-d H:i:s"); //查询交易创建时间结束
        $this->account = $account;
        $this->appKey = $appKey; //填写自己申请的AppKey
        $this->appSecret = $appSecret; //填写自己申请的$appSecret
        if(!empty($defalut_carrier)){
        	$this->defalut_carrier = $defalut_carrier;
        }
    }

	//获取数据兼容file_get_contents与curl
	function tmall_vita_get_url_content($url) {
		if(function_exists('file_get_contents')) {
			$file_contents = file_get_contents($url);
		} else {
			$ch = curl_init();
			$timeout = 2;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}
		$this->backupRequestAndResponseXml($url, $file_contents);
		return $file_contents;
	}

	//签名函数
	function tmall_createSign ($paramArr) {
	    global $appSecret;
	    $sign = $appSecret;
	    ksort($paramArr);
	    foreach ($paramArr as $key => $val) {
	       if ($key !='' && $val !='') {
	           $sign .= $key.$val;
	       }
	    }
	    $sign = strtoupper(md5($sign.$appSecret));
	    return $sign;
	}

	//组参函数
	function tmall_createStrParam ($paramArr) {
	    $strParam = '';
	    foreach ($paramArr as $key => $val) {
	       if ($key != '' && $val !='') {
	           $strParam .= $key.'='.urlencode($val).'&';
	       }
	    }
	    return $strParam;
	}

	//解析xml函数
	function tmall_getXmlData ($strXml) {
		$pos = strpos($strXml, 'xml');
		if ($pos) {
			$xmlCode=simplexml_load_string($strXml,'SimpleXMLElement', LIBXML_NOCDATA);
			$arrayCode=tmall_get_object_vars_final($xmlCode);
			return $arrayCode ;
		} else {
			return '';
		}
	}

	function tmall_get_object_vars_final($obj){
		if(is_object($obj)){
			$obj=get_object_vars($obj);
		}
		if(is_array($obj)){
			foreach ($obj as $key=>$value){
				$obj[$key]=tmall_get_object_vars_final($value);
			}
		}
		return $obj;
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
		$savepath	= EBAY_RAW_DATA_PATH.'taobao/'.$savelist['class'].'/'.$savelist['function'].'/'.$this->account.'/'.date('Y-m').'/'.date('d').'/'.$this->logname;
		write_log($savepath, $savecontent);
	}
}
?>