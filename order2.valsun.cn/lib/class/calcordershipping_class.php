<?php
/*
 * 订单运输方式选择和运费计算
 * @add by : linzhengxiang ,date : 20140611
 */

class CalcOrderShipping {

	private $errMsg = array();				//装载重量计算过程中的异常信息（无重量、无包材等），异常信息需要提交到数据库统一管理
	private $orderData = array();
	
	public function __construct(){
		
	}
	
	/**
	 * 赋值订单变量
	 * @param array $orderData
	 * @author lzx
	 */
	public function setOrder($orderData){
		$this->orderData = $orderData;
	}
	
	/**
	 * 获取错误信息
	 * @eturn array 错误信息数据需要打到订单相关表中，记录错误编号用于订单查询
	 * @author lzx 
	 */
	public function getErrMsg(){
		return $this->errMsg;
	}
	
	/**
	 * 订单重量计算
	 * @return float $orderweight
	 * @author herman.xi 20140620
	 */
	public function calcOrderWeight() {
		if (empty($this->orderData)){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
		$orderdetails = $this->orderData['orderDetail'];
		$volume		  = 0;				//初始化体积
		$orderWeight  = 0; 				//初始化要返回的订单重量变量
		foreach ($this->orderData['orderDetail'] AS $orderdetail){
			$ordersku 	 = $orderdetail['orderDetail']['sku'];
			$orderamount = $orderdetail['orderDetail']['amount'];
			$skudata 	 = M("InterfacePc")->getSkuInfo($ordersku);
			if (empty($skudata)){
				return array(0, 0, 0);
			}
			foreach ($skudata['skuInfo'] AS $skuinfo){
				$pmId 	= $skuinfo['skuDetail']['pmId'];
				$sku 	= $skuinfo['skuDetail']['sku'];
				$amount = $skuinfo['amount'];//update by zqt 20140717 数量格式修正
				$weight = $skuinfo['skuDetail']['goodsWeight'];
				$volume += $skuinfo['skuDetail']['goodsLength']*$skuinfo['skuDetail']['goodsWidth']*$skuinfo['skuDetail']['goodsHeight']*$amount*$orderamount;
				$orderWeight += $weight*$amount*$orderamount;
			}
		}
		$pmInfo = M("InterfacePc")->getMaterInfoById($pmId);//获取包材信息
		if($pmInfo){
			$orderWeight += $pmInfo['pmWeight'];
		}
		$this->orderData['order']['calcWeight'] = $orderWeight;
		return array($orderWeight, $volume, $pmId);
	}
	
	/**
	 * 综合调用函数返回最后计算出来的运费和运输方式
	 */
	public function calcOrderCarrierAndShippingFee(){
		if (empty($this->orderData)){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
		$carriers = $this->calcOrderCarriers();
		if (!$shippingfees = $this->calcOrderShippingFee($carriers)){
			//记录错误，需要自己写到消息提示配置里面。
			return false;
		}
		return $this->chooseOrderShipping($shippingfees);
	}
	
	/**
	 * 运输方式匹配
	 * @return array $carriers
	 * @author lzx
	 */
	public function calcOrderCarriers() {

		#1、对应平台录入平台运输方式和对应可以走的运输方式，如果为匹配返回false，提示用户添加对应匹配关系
		#2、获取特殊料号运输方式对应转化，剔除掉对应被转化运输方式
		#3、根据平台获取对应平台shipping进行扩展运输方式检验确认.demo 如下
		list($weight, $volume, $pmid) = $this->calcOrderWeight();
		$this->orderData['order']['calcWeight'] = $weight;
		$this->orderData['order']['pmId'] 		= $pmid;
		$carriers = '*';
		$accountId = $this->orderData['order']['accountId'];
		$shipping = M('Account')->getShippingByAccout($accountId);
		$extenmethod = "calc".ucfirst($shipping)."OrderExtension";
		if(method_exists($this, $extenmethod)){
			$carriers = $this->$extenmethod();
		}
		$this->orderData['order']['usefulTransportId'] = implode(',', $carriers);
		return $carriers;
	}
	
	/**
	 * 根据上面的运输方式到运输方式管理系统获取运费
	 * @param array $carriers
	 * @return array
	 * @author lzx
	 * @last modified by Herman.Xi 20140628
	 */
	public function calcOrderShippingFee($carriers){

		$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];
		$carrierId = join(',', $carriers);
		$carrierinfos = M('InterfaceTran')->getBatchFixShippingFee($carrierId, $countryName, $calcWeight, $postCode, 2);
		return $carrierinfos;
	}
	
	/**
	 * 根据运输方式和价格确定最后真正走的运输方式
	 * @param array $shippingfees
	 * @return array
	 * @author lzx
	 */
	public function chooseOrderShipping($shippingfees){
		$accountId = $this->orderData['order']['accountId'];
		$shipping = M('Account')->getShippingByAccout($accountId);
		$extenmethod = "choose".ucfirst($shipping)."OrderShippingExtension";
		if(method_exists($this, $extenmethod)){
			$carrier = $this->$extenmethod($shippingfees);
		}

		$this->orderData['order']['channelId'] 	  = $carrier['channelId'];
		$this->orderData['order']['transportId']  = $carrier['carrierId'];
		$this->orderData['order']['calcShipping'] = $carrier['fee'];
		return $this->orderData;
	}
	
	/**
	 * 对应ebay平台特殊运输方式转换，
	 * demo：如ebay在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcEbayOrderExtension(){
		$calcWeight  = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode    = $this->orderData['orderUserInfo']['postCode'];
		$accountId   = $this->orderData['order']['accountId'];
		$total  	 = $this->orderData['order']['actualTotal'];
		$currency	 = $this->orderData['orderExtension']['currency'];
		$accountinfo = M('Account')->getAccountById($accountId);
		$account	 = $accountinfo['account'];
		$is_eub		 = $accountinfo['is_eub'];
		//$ebay_total  缺少币种转化
		
		//EUB账号直接转换
		if ($is_eub&&in_array($countryName, array('United States','US','Puerto Rico'))&&$totalweight <= 2){
			$carriers = array('EUB');
		}else if(in_array($countryName, array('United States','US','Puerto Rico')) && !in_array($account, array('eshoppingstar75','easyshopping095','beromantic520','happyforu19','edealsmart')) && $total >= 5 && $calcWeight <= 2){
			$carriers = array('EUB');
		}else		
		//(陈小霞)2013-09-06 09:44:11  帮忙设置下这些账号的Russian Federation,Russia,Brazil,Brasil,Argentina三个国家总金额超过8的，设置挂号发货，其他账号或者其他国家仍然跟以前一样，中国邮政超过40挂号，香港小包超过70挂号
		if(in_array($countryName, array('Russian Federation','Russia','Brazil','Brasil','Argentina')) && $total >= 8 && in_array($account,array('365digital','digitalzone88','itshotsale77','cndirect998','cndirect55','befdi','easydeal365','enicer','doeon','starangle88','zealdora','360beauty','befashion','charmday88','dresslink','easebon','work4best','eshop2098','happydeal88','easytrade2099','easyshopping678','futurestar99','wellchange','voguebase55','keyhere','tradekoo','niceforu365','dealinthebox','niceinthebox','enjoy24hours','betterdeals255','bestinthebox','sunwebhome','sunwebzone','befdimall','ishop2099','elerose88','cafase88','choiceroad')) && in_array($currency, array('GBP','USD','EUR'))){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Wristwatch) > 0){
				if($total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$carriers = array('中国邮政挂号');
			}
		}else if(in_array($countryName, array('Kyrgyzstan','Jersey'))){
			if($total >= 70 ){
				$carriers = array('香港小包挂号');
			}else{
				$carriers = array('香港小包平邮');
			}
		}else if ($total>40){
			$carriers = $totalweight <= 2 ? array('中国邮政挂号', 'EUB') : array('中国邮政挂号');
		}else{
			$carriers = $totalweight <= 2 ? array('中国邮政平邮', 'EUB') : array('中国邮政平邮');
		}
		$transportIds = array();
		foreach ($carriers AS $carrier){
			array_push($transportIds, M('InterfaceTran')->getCarrierIdByName($carrier));
		}
		return $transportIds;
	}
	
	/**
	 * 对应亚马逊平台特殊运输方式转换
	 */
	private function calcAmazonOrderExtension(){
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$calcWeight = $this->orderData['order']['calcWeight'];
		$accountId = $this->orderData['order']['accountId'];
		$currency = $this->orderData['orderUserInfo']['currency'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];
		$actualTotal = $this->orderData['order']['actualTotal'];
		$transportId = $this->orderData['order']['transportId'];
		$accountinfo = M('Account')->getAccountById($accountId);
		$account	 = $accountinfo['account'];

		if(in_array($countryName, array("United States", "Puerto Rico", "PuertoRico", "Virgin Islands (U.S.)"))){
			$transportId = 'EUB';
			if($calcWeight > 2){
				$transportId = 'FedEx';
			}
		}else if($countryName == "Canada"){
			$transportId = '中国邮政挂号';
		}else if($countryName == "United Kingdom"){
			$transportId = '德国邮政挂号';
			if($calcWeight > 0.74){
				$transportId = 'FedEx';
			}
		}else if($countryName == ''){
			return '';
		}else{
			$transportId = '德国邮政挂号';
		}
		
		if($account == 'sunweb'){
			$transportId = '中国邮政平邮';
			if(in_array($countryName, array("United States", "Puerto Rico", "PuertoRico", "Virgin Islands (U.S.)","Austria","Belgium","Canary Islands","Channel Islands","Denmark","France","Germany","Ireland","Italy","Luxembourg","Monaco","Netherlands","Norway","San Marino","Spain","Sweden","Switzerland","United Kingdom","Vatican City State")) && $calcWeight > 2){
				$transportId = 'FedEx';
			}else if(in_array($countryName, array("Canada","Mexico")) && $calcWeight > 3){
				$transportId = 'FedEx';	
			}
		}
		
		if($currency == 'USD' && in_array($account,array('zeagoo889','Finejo2099'))){
			$ama_countrys = array('Portugal', 'Bosnia and Herzegovina', 'Czech Republic','Finland,Belgium','Slovakia','Austria','Andorra','Montenegro','Estonia','Sweden','Malta','Vatican City State','Croatia', 'Republic of Iceland','Russian Federation','Luxembourg','Bulgaria','Germany','Spain','Norway','Italy','Serbia','Albania','Belarus','Switzerland','Denmark','Cyprus','Greece','Hungary,Slovenia','Moldova','Macedonia','Liechtenstein','Svalbard and Jan Mayen','San Marino','Latvia','Guernsey','Romania','Gibraltar','Netherlands','Lithuania','Monaco','Jersey','France','Ireland','Ukraine','Polan');
			if(in_array($countryName,$ama_countrys)){
				$transportId = 'Global Mail';
			}
		}
		
		if(in_array($ebay_currency, array('GBP','EUR')) && in_array($account,array('zeagoo889','finejo2099','lantomall'))){
			$ama_countrys = array('Holland','Czech','Estonia','Slovakia','Slovenia','Sweden','Hungary','France','Germany','Denmark','Belgium','Finland','Spain','Poland','United Kingdom');
			if(in_array($ebay_countryname,$ama_countrys)){
				$transportId = 'Global Mail';
			}
		}
		
		if($shippment_py && in_array($account,array('zeagoo889'))){
			$transportId = '中国邮政平邮';
		}
		
		if($shippment_py2 && in_array($account,array('Finejo2099'))){
			$transportId = '中国邮政平邮';
		}
		
		if(strpos($transportId, '中国邮政') !== FALSE){
			if(count($array_intersect_zhijiayou) > 0 || count($array_intersect_yieti) > 0 || count($array_intersect_Wristwatch) > 0){
				if($actualTotal >= 70 ){
					$transportId = '香港小包挂号';
				}else{
					$transportId = '香港小包平邮';
				}
			}
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	/**
	 * 对应速卖通平台特殊运输方式转换
	 */
	private function calcAliexpressOrderExtension(){
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$calcWeight  = $this->orderData['order']['calcWeight'];
		$ORtransport = $this->orderData['order']['ORtransport'];
		$currency 	 = $this->orderData['orderUserInfo']['currency'];
		$postCode 	 = $this->orderData['orderUserInfo']['postCode'];
		$actualTotal = $this->orderData['order']['actualTotal'];
		$accountId   = $this->orderData['order']['accountId'];
		$accountinfo = M('Account')->getAccountById($accountId);
		$account	 = $accountinfo['account'];
		$accountType = $accountinfo['accountType'];
		
		//判断平邮账号的字段om_account添加
		if(in_array($ORtransport, array('Hongkong Post Air Mail', 'HK Post Air Mail', 'HKPAM', 'Hongkong Post Airmail', 'HK Post Airmail','HongKong Post Air Mail'))){
			$carrier		= '香港小包挂号';
			if($accountType==2 && $actualTotal < 40){
				$carrier		= '香港小包平邮';
			}
		}
		if(in_array($ORtransport, array('UPSS', 'UPS Express Saver'))){
			$carrier = 'UPS';
		}
		if($ORtransport=='DHL'){
			$carrier = 'DHL';
		}
		if($ORtransport=='EMS'){
			$carrier = 'EMS';
		}
		if(in_array($ORtransport, array('ChinaPost Post Air Mail', 'China Post Air Mail', 'CPAM', 'China Post Airmail'))){
			$carrier = '中国邮政挂号';
			if($accountType==2 && $actualTotal < 40){
				$carrier = '中国邮政平邮';
			}
		}
		if($ORtransport=='ePacket'){
			$carrier = 'EUB';
		}
		if($ORtransport=='Singapore Post'){
			$carrier = '新加坡小包挂号';
		}
		if($ORtransport=="Fedex IE"){
			$carrier = 'FedEx';
		}
		if($ORtransport=="Russian Air"){
			$carrier = $actualTotal < 40 ? '俄速通平邮' : '俄速通挂号';
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($carrier);
		return array($transportId);
	}
	
	/**
	 * 对应独立商城平台特殊运输方式转换，
	 * demo：如Valsun在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcValsunOrderExtension(){
		//echo "==========="; echo "\n";
		$ORtransport = $this->orderData['order']['ORtransport'];
		/*$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];*/
		if($ORtransport == 'dhlfixed' || $ORtransport == 'dhlperweight' || $ORtransport == 'dhl'){
			$carrier ='DHL';
		}else if ($ORtransport=='fedex' || $ORtransport=='Fedex'){
			$carrier ='FedEx';
		}else if ($ORtransport=='chinapostreg' || $ORtransport=='Chinapostreg'){
			$carrier ='中国邮政挂号';
		}else if ($ORtransport=='chinapost' || $ORtransport=='Chinapost'){
			$carrier ='中国邮政平邮';
		}else if($ORtransport=='ems'){
			$carrier ='EMS';
		}else if($ORtransport=='emszones'){ 
			$carrier ='EMS';
		}else if($ORtransport=='sfexpress'){
			$carrier ='顺丰快递';
		}else if($ORtransport=='stoexpress'){
			$carrier ='申通快递';
		}else if($ORtransport=='ups'){
			$carrier ='UPS美国专线';
		}else if($ORtransport=='epacket'){
			$carrier ='EUB';
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($carrier);
		return array($transportId);
	}
	
	/**
	 * 对应国内销售平台特殊运输方式转换，
	 * demo：如Valsun在某段时间内不能内什么运输方式，在这里可以剔除掉
	 */
	private function calcDomesticOrderExtension(){
		//echo "==========="; echo "\n";
		$transportId = $this->orderData['order']['transportId'];
		/*$calcWeight = $this->orderData['order']['calcWeight'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$postCode = $this->orderData['orderUserInfo']['postCode'];*/
		switch($transportId){
			case 'hongkong post air mail' :
			case 'hk post air mail' :
			case 'hkpam' :
			case 'hongkong post airmail' :
			case 'hk post airmail' :
				$transportId = '香港小包挂号';
				break;
			case 'upss' :
			case 'ups express saver' :
				$transportId = 'UPS';
				break;
			case 'dhl' :
				$transportId = 'DHL';
				break;
			case 'ems' :
				$transportId = 'EMS';
				break;
			case 'chinapost post air mail' :
			case 'china post air mail' :
			case 'cpam' :
			case 'china post airmail' :
				$transportId = '中国邮政挂号';
				break;
			case 'china post air mail (surface)' :
				$transportId = '中国邮政平邮';
				break;
			case 'epacket' :
				$transportId = 'EUB';
				break;
			case 'fedex' :
			case 'fedex ip' :
			case 'fedex ie' :
				$transportId = 'FedEx';
				break;
			default :
				//$transportId = '';
		}
		$transportId = M('InterfaceTran')->getCarrierIdByName($transportId);
		return array($transportId);
	}
	
	#####################  可以扩展多个平台运输方式选择  一定要按照平台表：suffix 递增 订单信息，明细扩展表后缀,命名规则##########################
	
	/**
	 * 对应ebay最优运输方式选择和价格差别选择
	 * demo： 如EUB的价格高于平台的10%还是会选择EUB
	 */
	private function chooseEbayOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if($shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应Amazon最优运输方式选择和价格差别选择
	 * demo： 如EUB的价格高于平台的10%还是会选择EUB
	 */
	private function chooseAmazonOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应速卖通最优运输方式选择和价格差别选择
	 */
	private function chooseAliexpressOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应独立商城最优运输方式选择和价格差别选择
	 */
	private function chooseValsunOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;	
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;	
			}
		}
		return $carrierinfo;
	}
	
	/**
	 * 对应独立商城最优运输方式选择和价格差别选择
	 */
	private function chooseDomesticOrderShippingExtension($shippingfees){
		$carrierinfo = array();
		foreach($shippingfees as $key => $shippfeevalue){
			if($shippfeevalue['fee'] == 0){
				continue;
			}
			if(isset($carrierinfo) && $shippfeevalue['fee'] < $carrierinfo['fee']){
				$carrierinfo = $shippfeevalue;
			}else{
				$carrierinfo = $shippfeevalue;
			}
		}
		return $carrierinfo;
	}
	#####################  可以扩展多个平台最优运输方式选择和价格差别选择  一定要按照平台表：suffix 递增 订单信息，明细扩展表后缀,命名规则##########################
}