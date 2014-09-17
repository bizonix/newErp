<?php
/*
*方法名称：CheckEbayFee
*功能：检查eBay费用
*返回数据：返回最后所有eBay费用总和
*开发人：冯赛明
*开发时间：2013-5-17
* modify： winday
*/
function CheckEbayFee($xmlContent, $account)
{
	$error		=	array();
	$feeDetal	=	array();
	$totalFee	=	0.00;
	$err_code	=	0;
	$currencyID	=	"USD";
	$ItemID	=	"";
	if(!empty($xmlContent)){
		$xml 		= 	new SimpleXMLElement($xmlContent);
		$ItemID		=	(string)$xml->ItemID;	
		if($xml->Ack == "Failure" || $xml->Ack == "Warning"){
			if($xml->Ack == "Warning"){
				$err_code	=	1;
			}else{
				$err_code	=	2;
			}
			$errors	=	$xml->Errors;
			if(sizeof($errors) >0){
				foreach($errors as $v){
					$v	=	(array)$v;
					$error[]	=	array(
						"errMsg"	=>	preg_replace(array("/</","/>/"), array("'","'"), $v['LongMessage']),
						"errCode"	=>	$v['ErrorCode'],
						"type"		=>	$v['SeverityCode']
					);
				}
			}
		}
		
		if($xml->Ack != "Failure"){
			preg_match_all('/currencyID="([A-Z]{3})"/',$xmlContent,$feeMatch);	//匹配所有的货币单位
			$index	=	0;
			if(sizeof($xml->Fees->Fee) > 0){
				foreach ($xml->Fees->Fee as $element => $node){
					if(floatval($node->Fee)>0.00)
					{
						$node		=	(array)$node;
						if($node['Name'] == "ListingFee") continue;	//ListingFee等同于InsertionFee
						$fee		=	floatval($node['Fee']);
						$currency	=	isset($feeMatch[1][$index]) ? $feeMatch[1][$index]: "";
						$feeDetal[]	=	array("Name"=>$node['Name'], "Fee"=>$fee, "currency"=>$currency);
						$totalFee	+=	$fee;
					}
				}
			}	
		}
	}
	return array(
		"errCode"	=>	$err_code,
		"error"		=>	$error,
		"ItemID"	=>	$ItemID,
		"feeDetail"	=>	$feeDetal,
		"totalFee"	=>	$totalFee,
		"account"	=>	$account
	);
}
?>