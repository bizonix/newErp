<?php
if($argc!=2){
    exit("Usage: /usr/bin/php       $argv[0] eBayAccount \n");
}

$aliAccountKVArr = M('Account')->getAccountNameByPlatformId(2);//取得aliexpress平台下的所有账号列表

    //平台对ERP账号对应关系，后期改到配置文件
    $erp_user_mapping = array(
	"cn1000268236"	=>	"3ACYBER",
	"cn1000421358"	=>	"szsunweb",
	"cn1000616054"	=>	"E-Global",
	"cn1000960806"	=>	"beauty365",
	"cn1000983412"	=>	"caracc",
	"cn1000983826"	=>	"Bagfashion",
	"cn1000999030"	=>	"prettyhair",
	"cn1001428059"	=>	"LovelyBaby",
	"cn1001392417"	=>	"Finejo",
	"cn1001424576"	=>	"5season",
	"cn1001656836"	=>	"fashiondeal",
	"cn1001711574"	=>	"Sunshine",
	"cn1001718610"	=>	"fashionqueen",
	"cn1001739224"	=>	"shiningstar",
	"cn1500053764"	=>	"babyhouse",
	"cn1500152370"	=>	"fashionzone",
	"cn1500226033"	=>	"shoesacc",
	"cn1500293467"	=>	"superdeal",
	"cn1500439756"	=>	"istore",
	"cn1500514645"	=>	"ladyzone",
	"cn1500688776"	=>	"beautywomen",
	"cn1501288533"	=>	"womensworld",
	"cn1501287427"	=>	"myzone",
	"cn1501540493"	=>	"homestyle",	//2013-08-01
	"cn1501578304"	=>	"championacc",	//2013-08-01
	"cn1501595926"	=>	"digitallife",	//2013-08-01
	"cn1501638006"	=>	"Etime",		//2013-08-20
	"cn1510304665"	=>	"citymiss",
	"cn1500440054"	=>	"zeagoo360",
	//taotaoAccount
	"cn1501642501"	=>	"taotaocart",
	"cn1501654678"	=>	"arttao",
	"cn1501654797"	=>	"taochains",
	"cn1501655651"	=>	"etaosky",
	"cn1501656206"	=>	"tmallbasket",
	"cn1501656494"	=>	"mucheer",
	"cn1501657160"	=>	"lantao",
	"cn1501657334"	=>	"direttao",
	"cn1501657572"	=>	"hitao",
	"cn1501686293"	=>	"taolink",
	//------挂号转平邮账号-----
	"cn1510515579"	=>	"acitylife",
	"cn1510509503"	=>	"etrademart",
	"cn1510509429"	=>	"centermall",
	"cn1510514024"	=>	"viphouse",
	//-------------
    );
    
    $erp_user_mapping = array_flip($erp_user_mapping);  

$surfaceMailAccountArr = array( "cn1510515579"	=>	"acitylife",
                            	"cn1510509503"	=>	"etrademart",
                            	"cn1510509429"	=>	"centermall",
                            	"cn1510514024"	=>	"viphouse");//平邮账号数组，目前是写死，后期从配置中获取或其他途径

$aliexpressAccount = trim($argv[1]);//账号参数

$account		   = $erp_user_mapping[$aliexpressAccount];
$accountId		   = array_search($aliexpressAccount, $aliAccountKVArr);
$allCarrierList	   = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
//var_dump($GLOBAL_EBAY_ACCOUNT); echo "\n";exit;
if($accountId === false){//检测该账号参数是否在平台的账号内
    exit("$aliexpressAccount is not support now !\n");
}

echo "-------------------start ".date("Y-m-d H:i:s")."------------------------\n";

if($aliexpressAccount == "womensworld") {//特殊账号特殊处理，后期在配置文件中定义或其他途径
	$startTime = strtotime("-24 hour");
}else{
	$startTime = strtotime("-48 hour");
}
$hasShipmentOrderList = M('Order')->getUnMarkShippingOrdersByAS($accountId,$startTime);//根据账号及称重时间，筛选出需要标记发货的订单记录

$hasShipmentOrderCount = count($hasShipmentOrderList);
if($hasShipmentOrderCount <= 0){
    exit("No order to handel\n");
}

$transportData = array();//将全部的运输方式转换成k=>v形式
foreach($allCarrierList as $value){
    $transportData[$value['id']] = trim($value['carrierNameCn']);
}

$aliexpress = A('AliexpressButt');
$aliexpress->setToken($aliexpressAccount);

if($hasShipmentOrderCount > 0){//存在要标记发货的订单
	foreach($hasShipmentOrderList as $val){
		$omOrderId		= $val['id'];
		$transportId	= $val['transportId'];
		$carrier		= $transportData[$transportId];
		echo "开始上传订单【{$omOrderId}】 ----------运输方式={$carrier}------\n\n";
		$recordNumber	= $val['recordNumber'];
        //这里要验证平邮账号的运输方式是否是运德或平邮，否则报错提示
        /*
        
        
        */
		$orderTracknumberList = M('Order')->getOrderTracknumberList(array($omOrderId)); //获取订单的跟踪号
        $orderTracknumber = $orderTracknumberList[$omOrderId]['tracknumber'][0]['tracknumber'];//该omOrderId的跟踪号
		if(empty($orderTracknumber)){
		    echo "订单【{$omOrderId}】 ------无跟踪号，跳过------\n\n";
			continue;//无跟踪号不处理
		}
        $shippedOrderListRecNum = M('Order')->getUnMarkShippingOrdersByReNum($recordNumber);//根据recordNumber取得对应的已发货的订单记录		
		$total = count($shippedOrderListRecNum);        
		switch ($transportId) {		//$ebay_carrier
			case "4":	//香港小包挂号
				$serviceName            = 'HKPAM';      //Hongkong Post Air Mail
				break;
			case "46":	//UPS
				$serviceName            = 'UPS';
				break;
			case "8":	//DHL
				$serviceName            = 'DHL';
				break;
			case "9":	//Fedex
				$serviceName            = 'FEDEX_IE';
				break;
			case "70":
				$serviceName            = 'TNT';
				break;
			case "5":
				$serviceName            = 'EMS';
				break;
			case "2":	//中国邮政挂号
				$serviceName            = 'CPAM';       //China Post Air Mail
				break;
			case "6":	//EUB
				$serviceName            = 'EMS_ZX_ZX_US';       //EUB
				break;

			case "52":	//新加坡小包挂号
				$serviceName            = 'SGP';
				break;
			case "61":		//WEDO
				$serviceName            = 'Other';
				break;
			default:
				$serviceName			= $transportData[$transportId];	//$ebay_carrier;
				break;
		}

		$Website = $serviceName=='Other' ? "http://www.wedoexpress.com/index.php?mod=trackInquiry&act=index&carrier=wedo&tracknum={$orderTracknumber}" : '';
		if($total == 1){//正常订单或者合并订单或者合并包裹，B2B 没有合并包裹， 只有合并订单
            if($shippedOrderListRecNum[0]['combineOrder'] == 2){//合并后新订单下的子订单标记发货
                $splitOrderIdArr = M('Order')->getSplitOrderIdByMainOrderId($shippedOrderListRecNum[0]['id']);
                foreach($splitOrderIdArr as $value){
                    $splitRecordNumer = M('Order')->getShippedOrderRecordNumberById($value['id']);
                    if($aliexpress->sellerShipment($serviceName, $orderTracknumber, 'all', $splitRecordNumer, $Website)){
                        M('OrderModify')->updateShippedOrer($shippedOrderListRecNum[0]['id'],array('marketTime'=>time()));
                    }
                }
            }
            if($aliexpress->sellerShipment($serviceName, $orderTracknumber, 'all', $recordNumber, $Website)){//该订单标记发货
                M('OrderModify')->updateShippedOrer($shippedOrderListRecNum[0]['id'],array('marketTime'=>time()));
            }
		}
		if($total > 1) {//拆分订单
			$tmpType  = "all";
			$TmpEmpty = 0;
			foreach ($shippedOrderListRecNum as $v){
				if(empty($v['ShippedTime'])){   //存在未发货的
					$TmpEmpty++;
				}
			}
			if($TmpEmpty > 1){
				$tmpType = "part";
			}
			if($aliexpress->sellerShipment($serviceName, $orderTracknumber, $tmpType, $recordNumber, $Website)){//该订单标记发货
                M('OrderModify')->updateShippedOrer($shippedOrderListRecNum[0]['id'],array('marketTime'=>time()));
            }
		}
	}
}

echo "-------------------end ".date("Y-m-d H:i:s")."------------------------\n";
exit;