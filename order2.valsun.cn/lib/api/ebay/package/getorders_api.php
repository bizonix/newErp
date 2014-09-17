<?php
/*
 * 获取订单抓取ID列表
 * @add lzx, date 20140612
 */
include_once WEB_PATH."lib/api/ebay/eBaySession.php";
class GetOrders extends eBaySession{
	
	protected $verb = 'GetOrders';
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取订单id键值
	 * @param datetime $start
	 * @param datetime $end
	 * @param int $pcount
	 * @return array
	 * @author lzx
	 */
	public function getOrderIds($start, $end, $pcount){
		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
			<'.$this->verb.'Request xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->requestToken.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
				<OutputSelector>PaginationResult</OutputSelector>
				<OutputSelector>HasMoreOrders</OutputSelector>
				<OutputSelector>ReturnedOrderCountActual</OutputSelector>				
				<OutputSelector>OrderArray.Order.OrderID</OutputSelector>
				<OutputSelector>OrderArray.Order.PaidTime</OutputSelector>
				<OutputSelector>OrderArray.Order.ShippedTime</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector>
				<ModTimeFrom>'.$start.'</ModTimeFrom>
				<ModTimeTo>'.$end.'</ModTimeTo>
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>'.$pcount.'</PageNumber>
				</Pagination>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>All</OrderStatus>
			</'.$this->verb.'Request>';
		return $this->sendHttpRequest($requestXmlBody);
	}
	
	/**
	 * 获取订单详情
	 * @param array $order_ids
	 * @return array
	 * @author lzx
	 */
	public function getOrderLists($order_ids){
		
		$order_ids = array_filter($order_ids, create_function('$orderid', 'return preg_match("/^\d{12}(|\-\d{12,14}|\-0)$/i", $orderid)>0;'));
		if (count($order_ids)==0){
			return false;
		}
		$valid_orderids = array_map(create_function('$a','return "<OrderID>".$a."</OrderID>";'), $order_ids);
		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
			<'.$this->verb.'Request xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->requestToken.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
				<OrderIDArray>'."\n".implode("\n\t",$valid_orderids)."\n".'</OrderIDArray>
			</'.$this->verb.'Request>';
		return $this->sendHttpRequest($requestXmlBody);
	}
}
?>