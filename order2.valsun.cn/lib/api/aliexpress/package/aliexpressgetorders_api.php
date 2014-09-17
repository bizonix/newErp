<?php
/**
 * ����ѷץ����-ץ����
 */
include_once WEB_PATH."lib/api/aliexpress/aliexpressSession.php";
class AliexpressGetOrders extends AliexpressSession{
    public function __construct(){
		parent::__construct();
	}
	
	/**********************��ȡ������Ϣ**********************/
	public function findOrderListQuery($createDateStart = '', $createDateEnd = ''){
		$data = array(
			'access_token' => $this->access_token,
            
			'page'		   => '1',
			'pageSize'	   => '50',
			//'createDateStart'	=>	'07/05/2014',
			//'createDateEnd' 	=>	'07/07/2014',
			'orderStatus'  => 'WAIT_SELLER_SEND_GOODS'
			//'orderStatus'	=>'WAIT_BUYER_ACCEPT_GOODS'
		);
		
		if($createDateStart){
			$data['createDateStart'] = $createDateStart;
		}
		if($createDateStart){
			$data['createDateEnd'] = $createDateEnd;
		}
		
		$url = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.findOrderListQuery/{$this->appKey}";     
        $List		= json_decode($this->Curl($url,$data),true);           
		$orderList	= array();
		if(!empty($List["orderList"])){
			foreach($List["orderList"] as $k=>$v){
				$orderId = $v["orderId"];
				$orderList[$orderId]['detail'] = $this->findOrderById($orderId);
				$orderList[$orderId]['v']	   = $v;
			}			
			for($i=2;$i<=ceil($List["totalItem"]/$data['pageSize']);$i++){
				$data['page'] = $i;
				$List = json_decode($this->Curl($url,$data),true);
				foreach($List["orderList"] as $k=>$v){
					$orderId = $v["orderId"];
					$orderList[$orderId]['detail'] = $this->findOrderById($orderId);
					$orderList[$orderId]['v']	   = $v;
				}
			}			
		}
		unset($List);
		return $orderList;		
	}
	
    /**
     * ��ݶ����Ż�ȡ��������
     */
	public function findOrderById($orderId){
		$data = array(
			  'access_token' => $this->access_token,
			  'orderId'		 => $orderId
		);
		$url = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.findOrderById/{$this->appKey}";
		return json_decode($this->Curl($url,$data),true);
	}
}
?>
