<?php 
	@session_start();
	$_SESSION['user']='vipchen';
	error_reporting(0);
	require_once "ebay_order_cron_config.php";
	require_once "ebaylibrary/eBaySession.php";
	require_once 'global_ebay_accounts.php';
	require_once 'script_root_path.php';
	if($argc<2)
	{
		exit("Usage: /usr/bin/php $argv[0] ebayAccount itemid.... \n");	
	}
	$itemlist     = array();
	$ebayaccount = trim($argv[1]);
	if(!preg_match('#^[\da-zA-Z]+$#i',$ebayaccount)){
		exit("Invaild ebay account:$ebayaccount");
	}
	if(!in_array($ebayaccount,$GLOBAL_EBAY_ACCOUNT)){
		exit("$ebayaccount is not support now !");
	}
	for($kk=2; $kk<$argc; $kk++)
	{
		$itemid = trim($argv[$kk]);
		if(preg_match('#^\d{12}$#i',$itemid))
		{
			$itemlist[] = $itemid;
		}
		else
		{
			exit("Wrong itemid[".$itemid."],script exit now\n");
		}
	}
	$__token_file='ebaylibrary/keys/keys_'.$ebayaccount.'.php';
	if(!file_exists(SCRIPT_ROOT.$__token_file))
	{
		exit("$__token_file does not exists!!!");
	}
	else
	{
		require_once SCRIPT_ROOT.$__token_file;
		for($nn=0; $nn<count($itemlist); $nn++)
		{
			$ItemID = $itemlist[$nn];
			GetListByItemID($userToken,$ItemID,$ebayaccount);	
		}
	}
	
function GetListByItemID($token,$itemid,$account)
{
	global $dbConn,$devID,$appID,$certID,$serverUrl,$siteID,$detailLevel,$compatabilityLevel;
	$user  = 'vipchen';
	$verb  = 'GetItem';
	$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<RequesterCredentials>
									<eBayAuthToken>'.$token.'</eBayAuthToken>
								</RequesterCredentials>
								<ItemID>'.$itemid.'</ItemID>
								<WarningLevel>High</WarningLevel>
								</GetItemRequest>';
	$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
	$responseXml = $session->sendHttpRequest($requestXmlBody);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
	$responseDoc = new DomDocument();
    $responseDoc->loadXML($responseXml);
	$data	    = XML_unserialize($responseXml);
	//print_r($data);
	$Ack	= $data['GetItemResponse']['Ack'];
	if($Ack=='Success'){
		$result		 				= $data['GetItemResponse']['Item']; 
		$ViewItemURL 				= $result['ListingDetails']['ViewItemURL'];
		$main_QuantitySold 			= $result['SellingStatus']['QuantitySold'];
		$main_Quantity    			= $result['Quantity'];
		$Title       				= $result['Title'];
		$main_SKU		 			= $result['SKU'];
		$ListingType 				= $result['ListingType'];
		$StartPrice  				= $result['StartPrice'];
		$main_QuantityAvailable 	= $main_Quantity-$main_QuantitySold;
		$StartPricecurrencyID		= $result['BuyItNowPrice attr']['currencyID'];
		$ShippingCost				= $result['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'];
		$Site                   	= $result['Site'];
		echo '运费:'.$ShippingCost."\n";
		$pos = strpos($main_SKU,':');
		if($pos===false){
			$realsku = str_pad($main_SKU,3,'0',STR_PAD_LEFT);
		}else{
			$realsku = substr($main_SKU,0,$pos);
		}
		$sql					= "select id from ebay_list where ItemID='$itemid' and ebay_account ='$account' ";
		$sql					= $dbConn->execute($sql);
		$sql					= $dbConn->getResultArray($sql);
		if(count($sql)==1){
			$del = "delete from ebay_list where ItemID='{$itemid}'";
			$dbConn->execute($del);
		}
		$main_list	= "insert into ebay_list(status,ItemID,ViewItemURL,QuantitySold,Quantity,Title,SKU,realSKU,ListingType,StartPrice,ebay_account,ebay_user,QuantityAvailable,StartPricecurrencyID,ShippingCost,Site) value('0','$itemid','$ViewItemURL','$main_QuantitySold','$main_Quantity','$Title','$main_SKU','$realsku','$ListingType','$StartPrice','$account','$user','$main_QuantityAvailable','$StartPricecurrencyID','$ShippingCost','$Site')";
		//echo $main_list."\n";
		if($dbConn->execute($main_list))
		{
			echo $itemid.'同步成功'."\n";
			$Variations		= $result['Variations']['Variation'];
			if($Variations !='')
			{
				for($i=0;$i<count($Variations);$i++)
				{
					$SKU			= $Variations[$i]['SKU'];
					$Quantity		= $Variations[$i]['Quantity'];
					$StartPrice		= $Variations[$i]['StartPrice'];
					$QuantitySold	= $Variations[$i]['SellingStatus']['QuantitySold'];
					$tjstr			= '';
					$VariationSpecifics	= $Variations[$i]['VariationSpecifics'];
					if($VariationSpecifics != '')
					{
						$NameValueList	= $Variations[$i]['VariationSpecifics']['NameValueList']['Name'];
						if($NameValueList != '')
						{
							$NameValueList			= array();
							$NameValueList[0] 		= $Variations[$i]['VariationSpecifics']['NameValueList'];
						}
						for($n=0;$n<count($NameValueList);$n++)
						{
							$Nname		= $NameValueList[$n]['Name'];
							$Nvalue		= $NameValueList[$n]['Value'];
							$tjstr		.= $Nname.'**'.$Nvalue.'++';
						}
						$tjstr			= mysql_real_escape_string($tjstr);
					}
					
					$QuantityAvailable	= $Quantity - $QuantitySold;
					$sel = "select id from ebay_listvariations where ebay_account='$account' and itemid='$itemid' and SKU='$SKU' ";	    
					$sel = $dbConn->execute($sel);
					$sel = $dbConn->getResultArray($sel);
					if(count($sel)!=0){
						$d_del = "delete from ebay_listvariations where itemid='$itemid'";
						$dbConn->execute($d_del);
					}
					$detail_list = "insert into ebay_listvariations(SKU,Quantity,StartPrice,itemid,ebay_account,QuantitySold,QuantityAvailable,VariationSpecifics) values('$SKU','$Quantity','$StartPrice','$itemid','$account','$QuantitySold','$QuantityAvailable','$tjstr')";
					//echo $detail_list."\n";
					$dbConn->execute($detail_list);
				}
			}
		}else{
			echo $itemid.'同步失败<br>';
		}	
	}
}
?>