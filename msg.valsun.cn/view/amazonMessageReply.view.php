<?php
/*
 * message回复
 */
include_once WEB_PATH.'lib/opensys_functions.php';
include_once WEB_PATH.'lib/rabbitmq.class.php';         //消息队列类
require_once WEB_PATH.'lib/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
class AmazonMessageReplyView extends BaseView {
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * message回复表单
     */
    public function view_replyMessageForm(){
        
        $msgids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if ($msgids === '') {   //没指定id
        	$msgdata = array('data'=>array('请指定要回复的message'), 'link'=>'index.php?mod=amazonMessagefilter&act=getMessageListByConditions');
        	goErrMsgPage($msgdata);
        	exit;
        }
        
        /*----- 获得message信息 -----*/
        $idar      = explode(',', $msgids);  
        $idar      = array_map('intval', $idar); //一次打开的所有邮件的id集
        $emails    = array();
        $ordernums = array();
        $order_obj = new AmazonOrderModel();
        $msg_obj   = new amazonmessageModel();
        
        $msg_list  = $msg_obj->getMessageInfo($idar);
        foreach ($msg_list as &$entity){
        	$entity['sendtime']       = date('Y-m-d H:i:s',$entity['sendtime']);
        	//$emails[$entity['id']]    = $entity['sendid'];
        	//$ordernums[$entity['id']] = $entity['ordernum'];
        	//$BuyerandSeller   = $order_obj->getAmazonBuyerandSeller($entity['ordernum'], $entity['sendid']);
        	//$BuyerandSeller   = isset($BuyerandSeller[0]) ? $BuyerandSeller[0] : $BuyerandSeller;
            if (file_exists($entity['messagepath'])){  //检测message文件是否存在
                $entity['msgcontent'] = file_get_contents($entity['messagepath']);
            } else {
                $entity['msgcontent'] = "残念，暂时木有找到邮件本体！<br />邮件id: ". $entity['message_id']. "<br />邮件发送时间: ".
                		$entity['sendtime'];
            }
        }
        
        /*----- 获得message信息 -----*/
        /*----- 获得模板信息 -----*/
        $tpl_obj  = new AmazonMessageTemplateModel();
        $tpl_list = $tpl_obj->getTplList($_SESSION['globaluserid'], array('id', 'name', 'type', 'content'));
        // print_r($tpl_list);exit;
        $this->smarty->assign('tpllist', $tpl_list);
        /*----- 获得模板信息  -----*/
        
        /*----- 获得文件夹分类列表 -----*/
        $categorylist = new amazonmessagecategoryModel();
        $cat_list     = $categorylist->getAllCategoryInfoList();
        $this->smarty->assign('catlist', $cat_list);
        /*----- 获得文件夹分类列表 -----*/
       
        
        $this->smarty->assign('msglist', $msg_list);
        $this->smarty->assign('sec_menue', 5);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('toptitle', 'message回复');
        $this->smarty->display('msgreplyAmazon.htm');
    }
    
    
    
    /*
     * ajax获取订单买家姓名
    */
    public function view_ajaxGetBuyerandSeller(){
    	$ordernum  = isset($_GET['ordernum']) ? trim($_GET['ordernum']) : 0;
    	$email     = isset($_GET['email']) ? trim($_GET['email']) : '';
    	$order_obj = new AmazonOrderModel();
    	$buyer     = getOpenSysApi(OPENGETAMAZONORDER,array('email'=>$email,'ordernumber'=>$ordernum));
    	$buyer     = isset($buyer[0]) ? $buyer[0] : $buyer;
    	echo json_encode(array('buyer'=>$buyer['ebay_userid'],'seller'=>$buyer['ebay_account']));
    }
    /*
     * ajax拉取message信息到系统
     */
    public function view_ajaxGetTpl()
    {
        $tid = isset($_GET['tid']) ? trim($_GET['tid']) : 0;
        if (!(is_numeric($tid) && $tid>0)) { //tid不合法
        	$msgdata= array('errCode'=>9001, 'errMsg'=>'模板不合法!');
        	echo json_encode($msgdata);
        	exit;
        }
        $tpl_obj = new MessageTemplateModel();
        $tpl_info = $tpl_obj->getTplInfoById($tid);
        if (empty($tid)) {  
        	$msgdata = array('errCode'=>9002, 'errMsg'=>'模板不存在!');
        	echo json_encode($msgdata);
        	exit;
        } else {
            $msgdata = array('errCode'=>9003, 'errMsg'=>'', 'data'=>$tpl_info['content']);
        	echo json_encode($msgdata);
        	exit;
        }
    }
    
    public  function view_getHistoryMessages(){
    	
    	
    	
    }
    
    /*
     * 回复消息操作
     */
    public function view_replyMessage(){
        $msgid        = isset($_POST['msgid']) ? $_POST['msgid'] : 0;             //回复的message
        $text         = isset($_POST['text'])  ? trim($_POST['text']) : '';        //回复的内容
        $iscopy       = isset($_POST['copy'])  ? trim($_POST['copy']) : 1 ;        //是否抄送到用户邮箱 
        $hasattach    = isset($_POST['hasattach'])  ? trim($_POST['hasattach']) : '';
        $msg_obj      =  new amazonmessageModel();
        $account_obj  =  new AmazonAccountModel();
        $messageinfo  = $msg_obj->getMessageInfo(array($msgid));
        
        if (empty($messageinfo)) {                                          //message不存在
        	$msgdata = array('errCode'=>10010, 'errMsg'=>'message不存在');
        	echo json_encode($msgdata);
        	exit();
        }
    	if(empty($text)){
    	    $msgdata = array('errCode'=>10013, 'errMsg'=>'回复内容不能为空!');
                echo json_encode($msgdata);
                exit();
    	}
    	
        $msginfo = $messageinfo[0];
        $pwd     = $account_obj->getAmazonPasswordByGmail($msginfo['recieveid'])[0]['password'];
        $pwd     = base64_decode($pwd);
        
        $newid      = 0;    //新的队列主键id
        $doresult   = $msg_obj->insertMessageReply($msgid, $iscopy, $text, $msginfo['classid'], $msginfo['amazon_account'], $newid);
        $connection = new AMQPConnection(MQ_SERVER, 5672, 'admin', 'admin','valsun_message');
		$channel    = $connection->channel();
		$channel->exchange_declare(MQ_EXCHANGE_AMAZON, 'fanout', false, true, false); 
		$channel->queue_declare(MQ_QUEUE_AMAZON, false, true, false, false);
		$send     = preg_split('/@/',$recieveid)[0];
		$msg_uid  =  preg_replace("/$send/",'',$msginfo['message_id']);
		if($hasattach == 'yes'){
			$attach = $msginfo['send_attachpath'];
		} else if($hasattach == 'no'){
			$attach = '';
		}
		$text    = '<pre>'.$text.'</pre>';
		$data       = json_encode(array('mid'=>$msgid,'msgbody'=>$text,'subject'=>$msginfo['subject'],
					 'sendid'=>$msginfo['sendid'],'recieveid'=>$msginfo['recieveid'],'pwd'=>$pwd,'attach'=>$attach,
					 'msg_uid'=>$msg_uid));
		
		$msg        = new AMQPMessage($data,array('delivery_mode' => 2));//消息持久化
		$channel->basic_publish($msg,MQ_EXCHANGE_AMAZON);
		$channel->close();
		$connection->close();
        
        if ($doresult == TRUE) {
        	$msgdata = array('errCode'=>10011, 'errMsg'=>'操作成功!');
        	echo json_encode($msgdata);
        	exit();
        } else {
            $msgdata = array('errCode'=>10012, 'errMsg'=>'操作失败!');
            echo json_encode($msgdata);
            exit();
        }
    }

    
    /*
     * 标记为已经回复
     */
    public function view_markAsRead(){
        $msgids  = isset($_POST['msgids']) ? trim($_POST['msgids']) : '';
        $type    = isset($_POST['type']) ? trim($_POST['type'])  : 'read';         //标记类型
        $iscopy  = isset($_POST['copy'])  ? trim($_POST['copy']) : 1 ;        //是否抄送到用户邮箱
        $text    = isset($_POST['text'])  ? trim($_POST['text']) : '';        //回复的内容
        $msgids  = clearData($msgids);//返回索引数组
        $msg_obj = new amazonmessageModel();
        //$mq_obj     = new RabbitMQClass(MQ_USER, MQ_PSW, MQ_VHOST, MQ_SERVER);
        $msg_obj->updateMessageStatus($msgids, 3);
        foreach ($msgids as $idval) {
            $messageinfo = $msg_obj->getMessageInfo(array($idval));
            if (!empty($messageinfo)) {
            	 $msginfo   = $messageinfo[0];
            	 $msgid     = $msginfo['id'];
            	 $messageupdate = array(
            	 		'replyuser_id' => $_SESSION['globaluserid'],
            	 		'replytime'    => time(),
            	 		'status'       => 3  //3为标记回复
            	 );
            	 $msg_obj->updateMessageData($messageupdate, ' where id='.$msgid);
            	/*$newid      = 0;                                               //新的队列主键id
            	//if ($messageinfo['status'] == 0) {
            		$msg_obj->markAaRead($idval, $messageinfo['classid'],$type, $messageinfo['amazon_account'], $newid);
            	//}
            	$mq_obj->queue_publish(MQ_EXCHANGE, array('id'=>$newid)); */
            }
        }
        $msgdata = array('errCode'=>10020, 'errMsg'=>'操作成功');
        echo json_encode($msgdata);
        exit();
    }
    
    /*
     * ajax获取message内容
     * 目的：解决当message消息文件丢失时重新从Amazon上获取message消息内容
     */
    public function view_getMessageBody(){
        $id = isset($_GET['id']) ? $_GET['id'] : FALSE;
        if (!is_numeric($id) || $id=== FALSE ) {
            $msgdata = array('errCode'=>10040, 'errMsg'=>'缺少参数!');
            echo json_encode($msgdata);
            exit();
        }
        
        $msg_obj = new amazonmessageModel();
        $msginfo = $msg_obj->getMessageInfo(array($id));
        if (empty($msginfo)) {
        	$msgdata = array('errCode'=>10041, 'errMsg'=>'message不存在!');
            echo json_encode($msgdata);
            exit();
        }
        $msginfo = $msginfo[0];
        
        /*----- 获取message内容 -----*/
        $fetch_obj  = new FetchAmazonMessageModel();
        $result     = $fetch_obj->fetchMessageBody($msginfo['message_id'], $msginfo['amazon_account']);
        if ($result === FALSE) {
        	$msgdata = array('errCode'=>10042, 'errMsg'=>FetchMessageModel::$errMsg);
            echo json_encode($msgdata);
            exit();
        }
        /*----- 获取message内容 -----*/
        
        /*----- 更新数据库 -----*/
        $upresult = $msg_obj->updateMessageData(array('filepath'=>$result), ' where id='.$id);
        if ($upresult) {
        	$msgdata = array('errCode'=>10043, 'errMsg'=>'成功', 'str'=>file_get_contents(MSGREALPREFIX.$result));
            echo json_encode($msgdata);
            exit();
        } else {
            $msgdata = array('errCode'=>10044, 'errMsg'=>'失败');
            echo json_encode($msgdata);
            exit();
        }
        /*----- 更新数据库 -----*/
    }
    
    /*
     * 从订单系统获取订单信息中的买家
    */
    function view_getOderInfoByOrderNum($ordernum){
    	
    
    }
    
    /*
     * 从订单系统获取订单信息
     */
    function view_getOderInfo(){
        $buyer  = isset($_GET['buyer']) ? $_GET['buyer'] : FALSE;                      //买家账号
        $seller = isset($_GET['seller']) ? $_GET['seller'] : FALSE;                     //卖家账号
        $mid    = isset($_GET['mid'])    ? $_GET['mid']    : FALSE;   
       
        if ($$buyer === FALSE) {
        	$data = array('errCode'=>10045, 'errMsg'=>'缺少参数');
        	echo json_encode($data);
        	exit;
        }
        $buyer=urlencode($buyer);
        $result = getOpenSysApi(OPENGETORDER, array('type'=>'orderinfo','buyeraccount'=>$buyer, 'selleraccount'=>$seller));
      // print_r($result);exit;
        if ($result === FALSE) {        //获取开发系统出错
        	$data = array('errCode'=>10046, 'errMsg'=>'访问出错!');
        	echo json_encode($data);
        	exit;
        }
        if(isset($result['data']['totalbuy'])) unset($result['data']['totalbuy']);
        if(isset($result['data']['totalnum'])) unset($result['data']['totalnum']) ;

        $historystr = '';
//         print_r($result);exit;
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
                            <td>评价</td>
                        </tr>
EOF;
        $default_addr       = FALSE;
        $listInTwomonth     = '';
        $listMoreTwomonth   ='';
        $counter            = 0;
        if (!empty($result['data'])) {
            foreach ($result['data'] as $value) {
            	$counter++;
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
                
                $catename       = isset($value['catename'])        ? $value['catename']:'';          //状态
                $orderid        = isset($value['ebay_id'])         ? $value['ebay_id']:'';           //订单号
                $carrier        = isset($value['ebay_carrier'])    ? $value['ebay_carrier']:'';      //运输方式
                if (!empty($tracknumber)) {
                    $trackstr    = "
                    <a href='javascript:void(0)'"." onclick=queryExpressInfo('ebay','$tracknumber','zh','$counter')>$tracknumber</a>";

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
                                <td id="carrier_$counter">$carrier</td>
                                <td>$trackstr</td>
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
                                <td id="carrier_$counter">$carrier</td>
                                <td>$trackstr</td>
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
        echo json_encode($data);
        exit;
    }
    
    
    
    /*
     * 批量重回message
     */
    /* public function view_reReplyMessage(){
        $ids        = isset($_GET['ids']) ? trim($_GET['ids']) : FALSE;
        $returndata = array('errCode'=>0, 'errMsg'=>'');
        if (empty($ids)) {
        	$returndata['errCode'] = 100;
        	$returndata['errMsg']  = '缺少参数!';
        	echo json_encode($returndata); exit;
        }
        $ids    = clearData($ids);
        $msg_ojb    = new amazonmessageModel();
        if (empty($ids)) {
            $returndata['errCode'] = 101;
            $returndata['errMsg']  = '缺少参数!';
            echo json_encode($returndata); exit;
        }
//         print_r($ids);exit;
        $result = $msg_ojb->reReplyMessage_amazon($ids);
        $returndata['errCode']  = 102;
        $returndata['errMsg']   = '';
        $returndata['data']     = $result;
        echo json_encode($returndata);
    } */
    
   
    
    /*
     * 获得运输方式的信息 
     */
    public function view_getShippingInfo(){
        $returnData     = array('code'=>0,'msg'=>'');
        
        $plartform  = isset($_GET['plartform']) ? trim($_GET['plartform'])  : '';            //查询平台 两个值 ebay or aliexpress
        $trackSn    = isset($_GET['tracksn'])   ? trim($_GET['tracksn'])    : '';            //跟踪号
        $carrier    = isset($_GET['carrier'])   ? trim($_GET['carrier'])    : '';            //运输方式名称
        $lang       = isset($_GET['lang'])      ? trim($_GET['lang'])       : '';            //查询语言
        
        if (empty($carrier) || empty($trackSn) || empty($carrier) || empty($lang)) {                         //数据完整性 检测
        	$returnData['msg'] = '数据不完整!';
        	echo json_encode($returnData);
        	exit;
        }
        if ($plartform == 'ebay') {
        	$shipping_obj   = new EbayCarrierQueryModel();
        } else {
            $shipping_obj   = new AliShippingQueryModel();
        }
//         $xxx            = $shipping_obj->getSupportedCarrier();print_r($xxx);
        $carrierName    = $shipping_obj->carrierNameReflect($carrier);
        if ($carrierName == FALSE) {
            $returnData['msg'] = '不支持的运输方式!';
            echo json_encode($returnData);
            exit;
        }
//         echo $trackSn, '+',$carrierName, '+',$lang;exit;
        $reuslte        = $shipping_obj->getShippingInfo($trackSn, $carrierName, $lang);
//         print_r($reuslte);
//         var_dump($reuslte);
        if ($reuslte === FALSE) {
        	$returnData['msg'] = '获取物流详情失败';
        } else {
            $events = array();
            if (empty($reuslte['trackingEventList'])) {
                $returnData['msg'] = '获取物流详情失败';
            } else {
                foreach ($reuslte['trackingEventList'] as $ev){
                    $events[]   = "<tr><td class='font-14'>$ev[date]</td><td class='font-14'>$ev[place]</td><td class='font-14'>$ev[details]</td></tr>";
                }
                $returnData['code'] = 1;
                $returnData['data'] = $events;
            }
        }
        echo json_encode($returnData);
    }
    
    /*
     * 下载附件
    */
    public function view_downLoadAttach(){
    	$mid     = isset($_GET['mid']) ? $_GET['mid'] : 0;
    	$msg_obj = new amazonmessageModel();
    	$msginfo = $msg_obj->getMessageInfo(array($mid))[0];
    	$attachname = $msginfo['attachname'];
    	$attachpath = WEB_PATH.'crontab/'.$msginfo['attachpath'];
    	if(!file_exists($attachpath)){
    		die('附件不存在');
    	}
    	$fp = fopen($attachpath, 'r');
    	$fsize =filesize($attachpath);
    	header("Content-type:text/html;charset=utf-8");
    	Header("Content-type: application/octet-stream");
    	Header("Accept-Ranges: bytes");
    	Header("Accept-Length:".$fsize);
    	Header("Content-Disposition: attachment; filename=".$attachname);
    	$buffer = 1024;
    	while(!feof($fp)){
    		$file_con=fread($fp,$buffer);
    		echo $file_con;
    	}
    	fclose($fp);
    }
    
}

