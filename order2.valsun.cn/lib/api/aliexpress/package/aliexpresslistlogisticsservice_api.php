<?php
/**
 * ����ѷץ����-��ȡaliexpress֧�ֵ�������Ϣ
 */
include_once WEB_PATH."lib/api/aliexpress/aliexpressSession.php";
class AliexpressListLogisticsService extends AliexpressSession{
    public function __construct(){
		parent::__construct();
	}
    /***********************************************************
	 *	��ȡaliexpress֧�ֵ�������Ϣ
	 */
	 public function listLogisticsService(){
		$data = array(
			'access_token'	=>$this->access_token
		); 
		$url = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.listLogisticsService/{$this->appKey}";
		return json_decode($this->Curl($url,$data),true);	
	 }

}
?>
