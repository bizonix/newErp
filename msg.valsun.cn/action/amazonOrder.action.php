<?php
class OrderAct{
/*
     * 从订单系统获取订单信息
     */
    function act_getOderInfo(){
        $userid  	 = isset($_GET['userId']) ? $_GET['userId'] : FALSE;                     //买家账号
        $seller  	 = isset($_GET['seller']) ? $_GET['seller'] : FALSE;                     //卖家账号
        $email  	 = isset($_GET['email']) ? $_GET['email']    : FALSE;
        $ordernum    = isset($_GET['ordernum']) ? $_GET['ordernum'] : FALSE;
        $mid         = isset($_GET['mid'])    ? $_GET['mid']    : FALSE;                     
        if ($userid === FALSE) {
        	$data = array('errCode'=>10045, 'errMsg'=>'缺少参数');
        	echo json_encode($data);
        	exit;
        }
        $result = getOpenSysApi('msg.Amazon.getOrerInfo', array('buyeraccount'=>$userid, 'selleraccount'=>$seller, 'email'=>$email, 'ordernum'=>$ordernum));
        //print_r($result);exit;
        if ($result === FALSE) {        //获取开发系统出错
        	$data = array('errCode'=>10046, 'errMsg'=>'访问出错!');
        	echo json_encode($data);
        	exit;
        }
        if(isset($result['data']['totalbuy'])) unset($result['data']['totalbuy']);
        if(isset($result['data']['totalnum'])) unset($result['data']['totalnum']) ;

        $historystr = '';
//          print_r($result);exit;
        /* ----- 生成历史记录  -----*/
        $tbtitle     = <<<EOF
                        <tr class="title">
                            <td>订单编号</td>
                            <td>买家账号</td>
                            <td>SKU</td>
                            <td>Itemid</td>
                            <td>数量</td>
                            <td>单价</td>
                            <td>总金额</td>
                            <td>订单状态</td>
                            <td>付款时间</td>
                            <td>发货时间</td>
                            <td>运输方式</td>
                            <td>跟踪号</td>
        					<td class='showMoreIdMsg' style='display:none;'>交易Id</td>
        					<td class='showMoreMailMsg' style='display:none;'>收款邮箱</td>
                            <td>评价</td>
                        </tr>
EOF;
        $default_addr       = FALSE;
        $listInTwomonth     = '';
        $listMoreTwomonth   ='';
        if (!empty($result['data'])) {
            foreach ($result['data'] as $value) {
                // print_r($value);exit;
                $paytime        = isset($value['ebay_paidtime']) && !empty($value['ebay_paidtime'])   ? date('Y-m-d H:i:s',  $value['ebay_paidtime']):'';       //付款日期
                $shiptime       = isset($value['scantime']) && !empty($value['scantime'])           ? date('Y-m-d H:i:s', $value['scantime']):'';                     //发货日期
                $couny          = isset($value['ebay_currency']) && !empty($value['ebay_currency'])     ? $value['ebay_currency']:'';                           //发货日期
                // echo $paytime;exit;
                $address    = $value['ebay_username'].',&nbsp;'.$value['ebay_street'].'&nbsp;'.$value['ebay_street1'].'&nbsp;'.$value['ebay_city'].'&nbsp;'.
                    $value['ebay_state'].',&nbsp;'.$value['ebay_postcode'].',&nbsp;'.$value['ebay_countryname'];
                if ($default_addr === FALSE) {
                	$default_addr  = $address;
                }
                $address    = str_replace('"', '\"', $address);
                $buyer_account  = isset($value['ebay_userid'])     ? $value['ebay_userid']:'';       //买家账号
                $money          = isset($value['ebay_total'])      ? $value['ebay_total']:'';        //金额
                $status         = isset($value['ebay_status'])     ? $value['ebay_status']:'';       //状态
                
                $tracknumber    = isset($value['ebay_tracknumber'])? $value['ebay_tracknumber']:'';  //跟踪号
                $transactionId	= isset($value['ebay_ptid'])? $value['ebay_ptid']:'';    //交易Id
                $PayPalEmail	= isset($value['PayPalEmailAddress'])? $value['PayPalEmailAddress']:'';    //收款邮箱
                $catename       = isset($value['catename'])        ? $value['catename']:'';          //状态
                $orderid        = isset($value['ebay_id'])         ? $value['ebay_id']:'';           //订单号
                $carrier        = isset($value['ebay_carrier'])    ? $value['ebay_carrier']:'';      //运输方式
                if (!empty($tracknumber)) {
                    $trackstr    = <<<EOF
                    <a href="javascript:queryExpressInfo('ebay', '$carrier','$tracknumber', 'zh')">$tracknumber</a>
EOF;
                }
                
                $skurow         = '';
                if (isset($value['orderdetail'])) {
                    foreach ($value['orderdetail'] as $skuitem){
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
                        if (empty($listInTwomonth) || (time() - $value['ebay_createdtime']) < 5184000 ) {
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
                            </tr>
EOF;
                        }
                    }
                }
            }
        }
        
//         print_r($return);exit;
        $data = array('errCode'=>10047, 'errMsg'=>'OK', 'list1'=>$tbtitle.$listInTwomonth, 'list2'=>$listMoreTwomonth, 'title'=>$tbtitle,'defaddr'=>$default_addr);
        $fun = $_GET['callback'];
        echo $fun."(".json_encode($data).")";
        exit;
    }
	
}
?>