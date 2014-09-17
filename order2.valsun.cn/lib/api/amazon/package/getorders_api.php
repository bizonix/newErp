<?php
/*
 * 获取订单抓取ID列表
 * @add lzx, date 20140612
 */
include_once WEB_PATH."lib/api/amazon/AmazonSession.php";
class GetOrders extends AmazonSession{
	private $_requestTimes = 0;
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 抓取一段时间内的订单
	 * @param datetime $starttime
	 * @param datetime $endtime
	 * @return array
	 * @author lzx
	 */
	public function getOrderLists($starttime, $endtime){
		
		$allResponse = array();
		$service 	 = new MarketplaceWebServiceOrders_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, APPLICATION_NAME, APPLICATION_VERSION, $this->config);
		$request 	 = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
		$request->setSellerId(MERCHANT_ID);
		$request->setMarketplaceId(MARKETPLACE_ID);
		$request->setCreatedAfter($starttime);
		$request->setCreatedBefore($endtime);
		$request->setOrderStatus(array("Unshipped", "PartiallyShipped"));
		$request->setMaxResultsPerPage(100);
		$response 		= $service->listOrders($request);
		$listOrdersResult = $response->getListOrdersResult();
		$allResponse[] 	= $listOrdersResult;
		#########################   分页抓取剩下的分页   start  ####################################
		$while_index	=	0;
		while($listOrdersResult->isSetNextToken()) {
			
			$request		=	new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
			$request->setSellerId(MERCHANT_ID);
			$request->setNextToken($listOrdersResult->getNextToken());
			$response		=	$service->ListOrdersByNextToken($request);
			$listOrdersResult = $response->getListOrdersByNextTokenResult();
			$allResponse[]	=	$listOrdersResult;
			//ListOrders 和 ListOrdersByNextToken 操作的最大请求限额为 6 个防止意外循环
			if($while_index%5 == 4){
				sleep(61);				//恢复速度1分钟
			}
			//容错机制， 异常后自动弹出
			if($while_index > 50){
				break;
			}
			$while_index++;
		}
		#########################   分页抓取剩下的分页   end   ####################################
		return $allResponse;
	}
	
	/**
	 * 抓取订单产品详细
	 * @param number $OrderId 
	 * @return array
	 * @author czq
	 */
	public function getOrderDetailLists($OrderId){
		$service 	 = new MarketplaceWebServiceOrders_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, APPLICATION_NAME, APPLICATION_VERSION, $this->config);
		$request 	 = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
		$request->setSellerId(MERCHANT_ID);
		$request->setAmazonOrderId($OrderId);
		$response 				= $service->listOrderItems($request);
		$listOrderItemsResult 	= $response->getListOrderItemsResult();
		$all_response = array();
		$all_response[] = $listOrderItemsResult;
		while($listOrderItemsResult->isSetNextToken()){
			$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest();
			$request->setSellerId(MERCHANT_ID);
			$request->setNextToken($listOrderItemsResult->getNextToken());
			$response				=	$service->ListOrdersByNextToken($request);
			$listOrderItemsResult 	= 	$response->getListOrderItemsByNextTokenResult();
			$all_response[]			=	$listOrderItemsResult;
			//最大的请求权限为30个,每30个就sleep 60秒恢复
			if($this->_requestTimes % 30 == 0){
				sleep(60);
				$this->_requestTimes = 0;
			}
			
			$this->_requestTimes ++;
		}
		return $all_response;
	}
}
?>