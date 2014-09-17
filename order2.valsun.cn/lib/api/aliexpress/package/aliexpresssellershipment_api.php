<?php
/**
 * ����ѷץ����-��Ƿ���
 */
include_once WEB_PATH."lib/api/aliexpress/aliexpressSession.php";
class AliexpressSellerShipment extends AliexpressSession{
    public function __construct(){
		parent::__construct();
	}
    /********************************************************
	 *	�Զ�Ӧ������Ƿ��ţ� ֧��ȫ�������� ���ַ���
	 *	var	serviceName	����������
	 *	var	logisticsNo	����׷�ٺ�
	 *	var	sendType	���ͷ�ʽ��all,part��
	 *	var	outRef		��Ӧ�Ķ�����
	 */
	public function sellerShipment($serviceName, $logisticsNo, $sendType, $outRef, $description=""){
		$data	=	array(
			'access_token'	=>	$this->access_token,
			'serviceName'	=>	$serviceName,
			'logisticsNo'	=>	$logisticsNo,
			'sendType'		=>	$sendType,
			'outRef'		=>	$outRef
		);
		
		if(!empty($description)){
			$data['description'] = $description;
		}
		$url = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.sellerShipment/{$this->appKey}";
		return json_decode($this->Curl($url,$data),true);	
	}
}
?>
