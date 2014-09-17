<?php
/*
 *通用验证方法类
 *@add by : linzhengxiang ,date : 20140523
 */
class TransformOrderAct extends TransformAct{
	
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
		####################### start 扩展通用验证  ##########################
		####################### end   扩展通用验证  ##########################
	}
	
	public function act_transformGetOrder(){
		//xxxxx用做专门扩展验证
		self::$errMsg[123] = 'for you test error';
		return true;
	}
	
	/**
	 * 插入订单的验证和转化
	 * @author yxd
	 * @return boolean
	 */
	public function act_transformInsertOrder(){	
		#################order表信息验证及格式化##############################
		$distributorsData    = json_decode('[
    {
        "order": {
            "recordNumber": "12680437",
            "account": "hello",
            "ordersTime": "2014-08-02 09:44:38",
            "paymentMethod": "PayPal",
            "paymentTime": "2014-08-02 09:44:38",
            "onlineTotal": "7.26",
            "currency": "AUD",
            "actualShipping": "22.000",
            "ORtransport": "chinapost"
        },
        "orderExtension": {
			"companyId": "yks",
            "payPalPaymentId": "7HE13018KL3271431",
            "orderId": "380882639665-508802711025",
            "feedback": "none"
        },
        "orderUserInfo": {
            "username": "Tahlia Dodd",
            "platformUsername": "t_ahlia24",
            "email": "tahlsxx@hotmail.com",
            "countryName": "Australia",
            "countrySn": "AU",
            "currency": "AUD",
            "state": "New South Wales",
            "city": "Peakhurst",
            "address1": "760a forest road",
            "address2": "760a forest road",
			"address3": "760a forest road",
            "phone": "02 91536215",
            "zipCode": "2210"
        },
	    "orderDeclarationContent" :[{
			"spu": "SV003829",
			"amount": 10,
			"price": 100,
			"enTitle": "T-Shirt",
			"cnTitle": "T恤",
			"hamcodes": "8531100000",
			"material":"棉",
			"unit": "pics"
	    }],
        "orderDetails": [
            {
                "orderDetail": {
                    "recordNumber": "630634",
                    "itemPrice": "7.26",
				    "itemId": "7733333",
                    "sku": "SV001920_BE_M",
                    "onlinesku": "SV001920_BE_M:N98B",
                    "amount": "1",
                    "shippingFee": "3.00",
                    "createdTime": "2014-08-02 09:44:38"
                },
                "orderDetailExtension": {
                    "itemTitle": "Womens Tummy Control Underbust Slimming Shapewear Shaper Suit Body Control N98B[Beige,Asian M (US S(4-6)  UK 6-8  EU ...",
                    "itemURL": "11"
                }
            }
        ]
    }
]',true);
		$order           = $distributorsData[0]['order'];
		$recordNumber    = $order['recordNumber'];//平台对应的订单号
		if(!isset($recordNumber)){
			self::$errMsg[123]    = '平台对应的订单号不能为空';
			return false;
		}
		if(!preg_match("/^\w*$/",$recordNumber)){
			self::$errMsg[123]    = 'recordnumber格式不规范';
		}
		$account    = $order['account'];//账号
		if(!isset($account)){
			self::$errMsg[123]    = '账号不能为空';
			return false;
		}
		//验证账号是否存在
		if(!M('Account')->checkIsExists(array('account'=>$account))){
			self::$errMsg[1234]    = '非法账号--不存在';
			return false;
		}
		//根据账号查找账号Id
		$accountId    = M('Account')->getAccountIdByName($account);//账号id
        if(empty($accountId)){
        	self::$errMsg[1235]    = '非法账号--不存在';
        	return false; 
        }

        //根据账号查平台Id
        $platformId    = M('Account')->getPlatformid($accountId);//平台id
        $platformId    = $platformId[0]['platformId'];
        if(empty($platformId)){
        	self::$errMsg[1236]    = '非法账号';
        	return false;
        }
        
        
        //这里还要验证该$account_id下这个$recordNumber是否存在查重
        if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$accountId))){
        	self::$errMsg[10043]    = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
        	return false;
        }	
       
        //验证时间格式
        $ordersTime    = $order['ordersTime'];//订单在平台的生成时间
        if(!validate_datetime($ordersTime)){
        	self::$errMsg[124]    = '非法时间格式ordersTime标准格式为[2014-08-03 18:58:23]';
        	return false;
        }
        $ordersTime     = strtotime($ordersTime);//系统以时间戳格式存放
        
        $paymentTime    = $order['paymentTime'];//订单付款时间
        if(!validate_datetime($paymentTime)){
        	self::$errMsg[124]    = "非法时间格式paymentTime标准格式为[2014-08-03 18:58:23]";
        	return false;
        }
        $paymentTime     = strtotime(paymentTime);//系统以时间戳格式存放
        
        $orderAddTime    = time();//订单进入系统时间时间
        
        $onlineTotal    = $order['onlineTotal'];//线上总价 可以为空
        if(!validate_float2($onlineTotal)){
        	self::$errMsg[125]    = "非法线上总价标准格式为[12.00]";
        	return false;
        }
        
        $currency     = $order['currency'];//币种3个大写字母，必填
        if(!preg_match("/^([A-Z]{3})$/",$currency)){
        	self::$errMsg[124]    = "非法币种标准格式为[3个大写字母组成]";
        	return false;
        }
        
        $paymentMethod    = $order['paymentMethod'];//付款方式
        if(empty($paymentMethod)){
        	self::$errMsg[111]    = "付款方式不能为空";
        	return false;
        }
        
        $ORtransport    = $order['ORtransport'];// 订单进系统时分配给它的原始运输方式，只用作备份
        
 
        $actualShipping    = $order['actualShipping'];//线上时间付款运费 可以为空
        if(!validate_float3($actualShipping)){
        	self::$errMsg[125]    = "运费格式非法标准格式为[12.000]";
        	return false;
        }
       
			
        $orderArr = array(
        		'recordNumber'		=> $recordNumber,
        		'platformId'		=> $platformId,
        		'accountId'			=> $accountId,
        		'ordersTime'		=> $ordersTime,
        		'paymentMethod'     => $paymentMethod,
         		'paymentTime' 		=> $paymentTime,
        		'onlineTotal' 		=> $onlineTotal,
        		'currency'          => $currency,
        		'ORtransport' 	    => $ORtransport,
        		'actualShipping' 	=> $actualShipping,
        		'orderAddTime'      => $orderAddTime
        );
        ################orderdetail 及exteendtion数据验证#######################
        $orderDetails             = $distributorsData[0]['orderDetails'];
        $orderDetailArr           = array();
        foreach($orderDetails as $value){
        	$orderDetail              = $value['orderDetail'];//订单详情表

        	$itemPrice                = isset($orderDetail['itemPrice'])?$orderDetail['itemPrice']:0.00;//平台对应的销售单价
        	if(!validate_float2($itemPrice)){
        		self::$errMsg[123]    = "平台对应的销售单价不规范标准格式为[12.00]";
        		return false;
        	}
        	$itemId                   = isset($orderDetail['itemId'])?$orderDetail['itemId']:null;//商品在平台上的ID
        	if(!preg_match("/^\d*$/",$itemId)){
        		self::$errMsg[123]    = "商品在平台上的ID不规范标准格式为[5454143004]";
        		return false;
        	}
        	$sku                      = $orderDetail['sku'];
        	$isSkuExsit    =M("InterfacePc")->getSkuinfo($sku);
        	if(empty($isSkuExsit)){
        		self::$errMsg[123]    = "sku在系统中不存在";
        		return false;
        	}
        	$onlinesku                = isset($orderDetail['onlinesku'])?$orderDetail['onlinesku']:null;//线上sku
        	$amount                   = isset($orderDetail['amount']) ? $orderDetail['amount']:0;  
        	if(!preg_match("/^\d*$/",$amount)){
        		self::$errMsg    = "订单中的商品数量不规范，必须为有效数字";
        		return false;
        	} 
        	$amount                   = intval($orderDetail['amount']);
        	$shippingFee              = $orderDetail['shippingFee'];//平台对应料号的运费
        	if(!validate_float2($shippingFee)){
        		self::$errMsg[123]    = "平台对应料号的运费不规范标准格式为[12.00]";
        		return false;
        	}
        	
        	$orderDetailExtension     = $value['orderDetailExtension'];//订单详情扩展表   
        	$itemTitle                = isset($orderDetailExtension['itemTitle'])?$orderDetailExtension['itemTitle']:null;
        	$itemTitle                = htmlentities($itemTitle);
        	$itemURL                  = isset($orderDetailExtension['itemURL'])?$orderDetailExtension['itemURL']:null;
        	
        	$orderDetailArr[]         = array(
        	"orderDetail"     => array(
        			"recordNumber"    => $recordNumber,
        			'itemId'          => $itemId,
        			"itemPrice"       => $itemPrice,
        		    "sku"             => $sku,
        		    "onlinesku"       => $onlinesku,
        			"amount"          => $amount,
        			"shippingFee"     => $shippingFee
        	),
        	"orderDetailExtension"    => array(
        		"itemTitle"           => $itemTitle,
        		"itemURL"             => $itemURL
        	)
        	
        	);
        }
        
       
         ################# orderUserInfo###################################
         $orderUserInfo       = $distributorsData[0]['orderUserInfo'];//获取分销商订单用户数据
         $username    	      = $orderUserInfo['username'];//收件人
         $platformUsername    = isset($orderUserInfo['platformUsername'])?$orderUserInfo['platformUsername']:null;//对应平台的用户登陆名称
         $platformUsername    = htmlspecialchars($platformUsername);
         $email  		      = $orderUserInfo['email'];//客户邮箱
         if(!validate_email($email)){
         	self::$errMsg['146']    = "非法邮箱格式";
         	return   false;
         }
         $countryname    	  = $orderUserInfo['countryName'];//收件人国家名
         if(M('CountryList')->checkIsExists($countryname)){
         	self::$errMsg[145]    = "非法国家";
         	return false;
         }
         $countrySn           = M('CountryList')->geZhByEn($countryname);//国家简码
         if(empty($countrySn)){
         	self::$errMsg[145]    = "非法国家";
         	return false;
         }
         $currency            = isset($orderUserInfo['currency'])?$orderUserInfo['currency']:null;//币种
         $city 		  	      = $orderUserInfo['city'];//买家所在城市
         $city                = htmlspecialchars($city);
         $state       	      = $orderUserInfo['state'];//买家所在州
         $state               = htmlspecialchars($state);
         $address1	  	      = isset($orderUserInfo['address1'])?$orderUserInfo['address1']:0;
         $address1            = htmlspecialchars($address1);
         $address2            = isset($orderUserInfo['address2'])?$orderUserInfo['address2']:0;
         $address2            = htmlspecialchars($address2);
         $address3            = isset($orderUserInfo['address3'])?$orderUserInfo['address3']:0;
         $address3            = htmlspecialchars($address3);
         $landline            = isset($orderUserInfo['landline'])?$orderUserInfo['landline']:0;//座机
        
         $phone               = isset($orderUserInfo['phone'])?$orderUserInfo['phone']:0;//手机
         if(!validate_phone($phone)){
         	self::$errMsg[124]    = "phone格式不规范";
         	return false;
         }
         $zipCode             = $orderUserInfo['zipCode'];//邮编
         if(!validate_zipCode($zipCode)){
         	self::$errMsg[124]    = "zipCode不规范";
         	return false;
         }
         $orderUserInfoArr    = array(
         		'username'			=> $username,
         		'platformUsername'	=> $platformUsername,
         		'email'				=> $email,
         		'countryName'		=> $countryname,
         		'countrySn'			=> $countrySn,
         		'currency'			=> $currency,
         		'state' 			=> $state,
         		'city' 				=> $city,
         		'county'            => "sss",
         		'address1' 			=> $address1,
         		'address2' 			=> $address2,
         		'address3' 			=> $address3,
         		'phone' 			=> $phone,  
         		'zipCode' 			=> $zipCode,
         );
         ################# orderextension#######################################
        $orderExtensions    = $distributorsData[0]['orderExtension'];
       
        $companyId           = $orderExtensions['companyId'];
     	$orderId             = $orderExtensions['orderId'];//ebay系统线上抓取组合ID，格式为itemid-transid
        $feedback            = $orderExtensions['feedback'];//客户留言
        $orderExtensionArr   = array(
        		'companyId'     => $companyId,
        		'orderId'       => $orderId,
         		'feedback'		=> $feedback,
         );
         #####################orderDeclarationContent##########################
        $orderDeclarationContent       = $distributorsData[0]['orderDeclarationContent'];
        $orderDeclarationContentArr    = array();
        foreach ($orderDeclarationContent as $value){
        	$spu            = $value['spu'];//申报料号
        	$amount         = $value['amount'];//申报数量
        	if(!preg_match("/^\d*$/",$amount)){
        		self::$errMsg[124]    = "申报数量必须为有效数字";
        		return false;
        	}
        	$price          = $value['price'];//申报价值（美金）
        	if(validate_float2($price)){
        		self::$errMsg[125]    = "申报价值格式不规范标准格式为[12.00]";
        		return false;
        	}
        	$enTitle        = $value['enTitle'];//申报名称（英文）
        	$enTitle        = htmlspecialchars($enTitle);
        	$cnTitle        = $value['cnTitle'];//申报名称（文）
        	$cnTitle        = htmlspecialchars($cnTitle);
        	$hamcodes       = $value['hamcodes'];//海关编码
        	$material       = $value['material'];//申报材质
        	$unit           = $value['unit'];//计量单位
        /* 	$description    = $value['description'];//申报描述
        	$description    = htmlspecialchars($description); */
        	$orderDeclarationContentArr[]    = array(
        		"spu"            => $spu,
        	    "amount"         => $amount,
        		"price"          => $price,
        		"enTitle"        => $enTitle,
        		"cnTitle"        => $cnTitle,
        		"hamcodes"       => $hamcodes,
        		"material"       => $material,
        		"unit"           => $unit,
        		"datetime"       => time()
         	);
        }
         
		//格式化数据
		
		$data[]    = Array(
			'order'                     => $orderArr,
			'orderDetail'               => $orderDetailArr,
            'orderExtension'            => $orderExtensionArr,
            'orderUserInfo'             => $orderUserInfoArr,
			'orderDeclarationContent'   => $orderDeclarationContentArr
        );
		################  end 格式化POST信息到统一数组   ##################
		return array($data);
	}
}