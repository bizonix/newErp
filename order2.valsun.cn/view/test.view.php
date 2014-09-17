<?php
/**
 * 黑名单管理
 * 
 * @add by yxd ,date 2014/06/10
 */
class testView extends BaseView{
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	//added by andy
	public function view_getskuinfo(){
		$sku = $_GET['sku'];
		$skuinfo = M("InterfacePc")->getSkuinfo($sku);
		echo '<pre>====';
		print_r($skuinfo);
		exit;
	}
	
	
	//added by andy
	public function view_getskulocation(){
		$sku = $_GET['sku'];
		$skulocation = M("InterfaceWh")->getSkuPosition($sku);//获取仓位数组，包含多仓位
		
		echo '<pre>====';
		print_r($skulocation);
		exit;
	}
//added by andy
	public function view_gethgbm(){
		$sku = $_GET['sku'];
		$json = array('SV000786');
		$skulocation = M("InterfacePc")->getHscodeInfoBySpuArr(json_encode($json));//获取仓位数组，包含多仓位
		
		echo '<pre>====';
		print_r($skulocation);
		exit;
	}
	
	public function view_getaccountname(){
		$arr = M('Account')->getAccountNameByPlatformId(2);
		echo '<pre>====';
		print_r($arr);
		exit;
	}
	
	public function view_getholdnumbers(){
		
	echo 'test';
	$sku = $_GET['sku'];
		$arr = M('order')->getSkuHoldingStockNumbers($sku);
		echo '<pre>====';
		print_r($arr);
		exit;
	}
	
	//http://re.order.valsun.cn/index.php?mod=test&act=getSkuStores&sku=SV000786_XL
	public function view_getSkuStores(){
		
		echo 'test';
		$sku = $_GET['sku'];
		$arr = M('interfaceWh')->getSkuStores(array($sku));
		echo '<pre>====';
		print_r($arr);
		exit;
	}
	
	/*http://re.order.valsun.cn/index.php?mod=test&act=getSkuStock&sku=SV000786_XL
	 * Array
(
    [SV000786_XL] => Array
        (
            [1] => 105
        )

)

Array
(
    [17020_W] => Array
        (
            [1] => 116
        )

    [SV000786_XL] => Array
        (
            [1] => 105
        )

)
	 */
	public function view_getSkuStock(){
		
	echo 'test';
	$sku = $_GET['sku'];//echo 'sku:'.$sku;exit;
		//$arr = M('interfaceWh')->getSkuStock(array(1=>array($sku,'17020_W'),2=>array($sku,'17020_W')));
		$arr = M('interfaceWh')->getSkuStock(array(1=>array($sku,'17020_W')));
		echo '<pre>====';
		print_r($arr);
		exit;
	}
	
	public function view_getInstance(){
		
	echo 'test';
	$obj = getInstance('formatorder');
	$obj->setOrder(array('a'=>'aaaa','b'=>'bbbb'));
	
	
		
		$obj2 = getInstance('formatorder');
	$obj2->setOrder(array('a'=>'99999999999','b'=>'88'));
		echo '<pre>====';
		print_r($obj->orderData);
		
		echo '<pre>====';
		print_r($obj2->orderData);
		exit;
	}
	public function view_splitOrderWithOrderDetail(){
		
		//$arr = M('OrderManage')->splitOrderWithOrderDetail('1524865',array(array('SV000786_X'=>1),array('SV000786_XL'=>1)));
		$arr = M('OrderManage')->splitOrderWithOrderDetail('1493252',array(2=>array('13545_K_30'=>1),1=>array('3633_W_L'=>10)));
		echo '<pre>000====';
		print_r($arr); 
	}
	
	public function view_trycatch(){
		try{
			test();
			throw new Exception('file is not exists');
		} catch (Exception $e) {
			echo 'geterror:'.$e->getMessage();
		}
	}
	
	public function view_handleCombineOrders(){
		M('orderManage')->handleCombineOrders();
		
	}
	public function view_getOrderStatusByStatusCode(){
		$statusmenu = M('StatusMenu')->getOrderStatusByStatusCode('ORDER_NO_NEED_SHIP','id');
		echo '<pre>userid:';
		print_r($statusmenu); 
	}
	
	public function view_getOrderStatusByGroupId(){
		$statusmenu = M('StatusMenu')->getOrderStatusByGroupId(111);
		echo '<pre>userid:';
		print_r($statusmenu); 
	}
	
public function view_check_priv4(){
		$statusmenu = A('StatusMenu')->act_getStatusMenuByUserId(get_userid());
		echo '<pre>userid:'.get_userid();
		print_r($statusmenu); 
	}
	public function view_getTopmenuLists(){
		$user_power       = M('topmenu')->getTopmenuLists(array('is_delete'=>array('$e'=>0)),1,500);
		echo '<pre>000====';
		print_r($user_power); 
	}
public function view_getOrderPowerByUserId(){
		$arr = M('userCompetence')->getOrderPowerByUserId(668);
		echo '<pre>000====';
		print_r($arr); 
	}
	
	public function view_getAcountPowerByUserId(){
		$arr = M('userCompetence')->getAcountPowerByUserId(8,1);
		echo '<pre>000====';
		print_r($arr); 
	}
	public function view_getPlatformPowerByUserId(){
		$arr = M('userCompetence')->getPlatformPowerByUserId(8);
		echo '<pre>000====';
		print_r($arr); 
	}
	public function view_get_amazon_cache(){
		
		global $memc_obj;
		echo '33:'.urldecode('%23');
		if($_GET['my']){
			echo 'my:'.$_GET['my'];exit;
		}
		$orderDetailObjList = $memc_obj->get('ENABLE_AMAZON_GET_ORDER_CACHE');
		$cache_content = var_export($orderDetailObjList, true);
		
		$this->smarty->assign('cache_content', $cache_content);
		$this->smarty->display("test_get_amazon_cache.htm");
	}
	
	//re.order.valsun.cn/index.php?mod=test&act=delete_cache&cache=om_Interface_RequestConf_getSkuStockByArrNew
	public function view_delete_cache(){
		
		$cache = $_GET['cache'];
	
		global $memc_obj;
		
		$orderDetailObjList = $memc_obj->delete($cache);
		echo 'finish;';
		exit;
	}
public function view_overweightorder(){
		$order_id = 1998850;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            
            
		$info = M('orderManage')->handleOverWeightOrder($order);
		var_dump($info);
	}
public function view_interceptOrder(){
		$order_id = 1999122;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            
            F('FormatOrder')->interceptOrder($order);
		
		var_dump($info);
	}
	
public function view_updateOrderUsefulTransportId(){
		$order_id = 1999123;
		$order = M('order')->getFullUnshippedOrderById(array($order_id));
		$order = $order[$order_id];
		
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            
        $stmOb = F('SpecialTransportMethod');
		
		$stmOb->setOrder($order);
		$info = $stmOb->updateOrderUsefulTransportId();
		    
		//$info = M('orderManage')->handleOverWeightOrder($order);
		var_dump($info);
	}
public function view_returnTCArrFromChannelIdArr(){
		
		$info = returnTCArrFromChannelIdArr(array(19,67));
		   
		    foreach($info as $key=>$val){
		    	foreach($val as $v){
		    		$CarrierName = M("InterfaceTran")->getCarrierNameById($v);
		    		echo '$CarrierName:'.$CarrierName;
		    	}
		    	
		    }
		
		//$info = M('orderManage')->handleOverWeightOrder($order);
		//var_dump($info);
	}
public function view_getPlatformToCarrierByINA(){
		$platformId = 1;
		$platformCarrierName = 'ePacketChina';
		
		$info = M('PlatformToCarrier')->getPlatformToCarrierByINA($platformId,$platformCarrierName);
		//$info = M('orderManage')->handleOverWeightOrder($order);
		echo '<pre>';
		print_r($info);
		
	}

public function view_getSkuStockByArrNew(){
		$info = M("interfaceWh")->getSkuStockByArrNew(array(1=>array('150')));
		echo '<pre>';
		print_r($info);
		
	}
	
public function view_mod(){
		$info = 10%3;
		echo '<pre>';
		print_r($info);
		
	}
	
}
?>