<?php
class GetAmazonOrderAct{
	/**
     * 从旧ERP系统获取订单信息(批量获取)
     */
    public function act_getOrderInfo(){
    	require_once WEB_PATH.'lib/functions.php';
       	$orderArr    = isset($_POST['orderArr']) ? $_POST['orderArr'] : FALSE;                  
        if (empty($orderArr)) {
        	$data = array('errCode'=>10045, 'errMsg'=>'缺少参数');
        	echo json_encode($data);
        	exit;
        }
        $orderArr 	= json_encode($orderArr);
        $result 	= $this->getOldAmazonOrderInfo('amazonMsgGetOrderInfo', array('orderArr'=>$orderArr));
        print_r($result);
        $mark       = 0;
        if(!empty($result)){
        	foreach($result as $k => $v){
				$default_addr       = FALSE;
	        	$listInTwomonth     = '';
	        	$listMoreTwomonth   ='';
	        	$totalbuy 	= $v['totalbuy'];
	     		$totalnum 	= $v['totalnum'];
	     		$paytime    = isset($v['ebay_paidtime']) && !empty($v['ebay_paidtime'])  ? date('Y-m-d H:i:s',  $v['ebay_paidtime']):'';       //付款日期
	            $shiptime   = isset($v['scantime']) && !empty($v['scantime']) ? date('Y-m-d H:i:s', $v['scantime']):'';                     //发货日期
	            $couny      = isset($v['ebay_currency']) && !empty($v['ebay_currency'])? $v['ebay_currency']:'';                           //发货日期
	            $address    = $v['ebay_username'].',&nbsp;'.$v['ebay_street'].'&nbsp;'.$v['ebay_street1'].'&nbsp;'.$v['ebay_city'].'&nbsp;'.
	            $v['ebay_state'].',&nbsp;'.$v['ebay_postcode'].',&nbsp;'.$v['ebay_countryname'];
	                if ($default_addr === FALSE) {
	                	$default_addr  = $address;
	                }
	                $address    = str_replace('"', '\"', $address);
	                $buyer_account  = isset($v['ebay_userid']) ? $v['ebay_userid']:'';//买家账号
	                $money          = isset($v['ebay_total']) ? $v['ebay_total']:'';//金额
	                $status         = isset($v['ebay_status'])? $v['ebay_status']:'';//状态
	                
	                $tracknumber    = isset($v['ebay_tracknumber'])? $v['ebay_tracknumber']:'';  //跟踪号
	                $transactionId	= isset($v['ebay_ptid'])? $v['ebay_ptid']:'';    //交易Id
	                $PayPalEmail	= isset($v['PayPalEmailAddress'])? $v['PayPalEmailAddress']:'';    //收款邮箱
	                $catename       = isset($v['catename']) ? $v['catename']:'';          //状态
	                $orderid        = isset($v['ebay_id']) ? $v['ebay_id']:'';           //订单号
	                $carrier        = isset($v['ebay_carrier'])? $v['ebay_carrier']:'';      //运输方式
	                if (!empty($tracknumber)) {
	                    $trackstr    = <<<EOF
	                    <a href="javascript:queryExpressInfo('ebay', '$carrier','$tracknumber', 'zh')">$tracknumber</a>
EOF;
	                }
	         		$skurow         = '';
	     		  	if (isset($v['orderdetail'])) {
	                    foreach ($v['orderdetail'] as $skuitem){
	                        switch(strtolower($skuitem['ebay_feedback'])){
	                        case 'positive':
	                            $feedback   = '<image src="images/positive.gif">';
	                            break;
	                        case 'neutral':
	                            $feedback   = '<image src="images/neutral.gif">';
	                            break;
	                        case 'negative':
	                            $feedback   = '<image src="images/negative.gif">';
	                            break;
	                        default :
	                            $feedback   = '';
	                            break;
	                        }
	                        if (empty($listInTwomonth) || (time() - $v['ebay_createdtime']) < 5184000 ) {
	                        	$listInTwomonth    .= <<<EOF
	                        	<tr style="background-color:#ffffff">
	                                <td>$orderid</td>
	                                <td>$buyer_account</td>
	                                <td>$skuitem[sku]</td>
	                                <td><a href="http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=$skuitem[ebay_itemid]" target="_blank">$skuitem[ebay_itemid]</a></td>
	                                <td>$skuitem[ebay_amount]</td>
	                                <td>$skuitem[ebay_itemprice]</td>
	                                <td>$money($couny)</td>
	                                <td>$catename</td>
	                                <td>$paytime</td>
	                                <td>$shiptime</td>
	                                <td>$carrier</td>
	                                <td>$trackstr</td>
	                                <td class='showMoreIdMsg' style='display:none;'>$transactionId</td>
	                                <td class='showMoreMailMsg' style='display:none;'>$PayPalEmail</td>
	                                <td>$feedback</td>
	                                <td><a href='javascript:showAddress($mid,"$address")'>查看</a></td>
	                                <td><a href='javascript:showMore()'>more</a></td>
	                            </tr>
EOF;
	                        } else {
	                            $listMoreTwomonth    .= <<<EOF
	                        	<tr style="background-color:#ffffff">
	                                <td>$orderid</td>
	                                <td>$buyer_account</td>
	                                <td>$skuitem[sku]</td>
	                                <td><a href="http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=$skuitem[ebay_itemid]" target="_blank">$skuitem[ebay_itemid]</a></td>
	                                <td>$skuitem[ebay_amount]</td>
	                                <td>$skuitem[ebay_itemprice]</td>
	                                <td>$money($couny)</td>
	                                <td>$catename</td>
	                                <td>$paytime</td>
	                                <td>$shiptime</td>
	                                <td>$carrier</td>
	                                <td>$tracknumber</td>
	                                <td class='showMoreIdMsg' style='display:none;'>$transactionId</td>
	                                <td class='showMoreMailMsg' style='display:none;'>$PayPalEmail</td>
	                                <td>$feedback</td>
	                                <td><a style="color:#06F" href='javascript:showAddress($mid, "$address")'>查看</a></td>
	                                <td><a href='javascript:void(0)'>more</a></td>
	                            </tr>
EOF;
	                        }
	                    }
	                }
	                $data[$mark]['errCode']  = 200;
	                $data[$mark]['list1'] 	= $listInTwomonth;
	                $data[$mark]['list2'] 	= $listMoreTwomonth;
	                $data[$mark]['defaddr'] = $default_addr;
	                $data[$mark]['mid']     = $v['mid'];
	                $mark++;
	       }    
	    }else{
	    	$data[0]['errCode']  = 404;
	        $data[0]['list1'] 	= '';
	        $data[0]['list2'] 	= '';
	        $data[0]['defaddr']  = '';
	        $data[0]['mid']      = $v['mid'];
	    }
        echo json_encode($data);
        exit;
    }
function getOldAmazonOrderInfo($method, $paArr, $gateway=''){
	require_once WEB_PATH.'/lib/opensys_functions.php';
	if(empty($method) || empty($paArr) || !is_array($paArr)){   //参数不规范
        return false;
    }else{
        $paramArr = array(
            'format'    => 'json',
            'v'    => '1.0',
            'username'  => 'Message'
        );
        $paramArr['method'] = $method;//调用接口名称，系统级参数
        foreach($paArr as $key=>$value){
            if(!is_array($value)){//如果传递的应用级参数不是数组的话，直接加入到paramArr中
                $paramArr[$key] = $value;
            }else{
                $paramArr['jsonArr'] = base64_encode(json_encode($value));//对数组进行jsonencode再对其进行base64编码进行传递，否则直接传递数组会出错
            }
        }
        $sign = createSign($paramArr,OPENTOKEN);
        $strParam = createStrParam($paramArr);
        $strParam .= 'sign='.$sign;
        $urls = OPENURL.$strParam;
        if (!empty($gateway)){
            $urls = $gateway.$strParam;
        } else {
            $urls = OPENURL.$strParam;
        }
        $cnt=0;
        while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
        $data	= json_decode($result,true);
        if($data){
          return  $data;
        }else{
            return FALSE;
        }
    }
}
	
}
?>