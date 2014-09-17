<?php
/*
 * message回复
 */
include_once WEB_PATH.'lib/opensys_functions.php';
include_once WEB_PATH.'lib/rabbitmq.class.php';         //消息队列类
class MessageReplyView extends BaseView {
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
        	$msgdata = array('data'=>array('请指定要回复的message'), 'link'=>'index.php?mod=messagefilter&act=getMessageListByConditions');
        	goErrMsgPage($msgdata);
        	exit;
        }
        
        /*----- 获得message信息 -----*/
        $idar = explode(',', $msgids);  //数组
        $idar = array_map('intval', $idar); //转换成
        $msg_obj = new messageModel();
        $msg_list = $msg_obj->getMessageInfo($idar);
        foreach ($msg_list as &$entity){
            if (file_exists(MSGREALPREFIX.$entity['filepath'])){  //检测message文件是否存在
                $entity['msgcontent'] = file_get_contents(MSGREALPREFIX.$entity['filepath']);
            } else {
                $entity['msgcontent'] = "
                    message消息没找到 ，<a href='javascript:getmessagebody(".$entity['id'].")'>点此修复</a>
                    ";
            }
        }
        /*----- 获得message信息 -----*/
        
        /*----- 获得模板信息 -----*/
        $tpl_obj = new MessageTemplateModel();
        $tpl_list = $tpl_obj->getTplList($_SESSION['globaluserid'], array('id', 'name', 'iscommon', 'incommonuse', 'content'));
        // print_r($tpl_list);exit;
        $this->smarty->assign('tpllist', $tpl_list);
        /*----- 获得模板信息  -----*/
        
        /*----- 获得文件夹分类列表 -----*/
        $categorylist = new messagecategoryModel();
        $cat_list = $categorylist->getAllCategoryInfoList();
        $this->smarty->assign('catlist', $cat_list);
        /*----- 获得文件夹分类列表 -----*/
        
        $this->smarty->assign('msglist', $msg_list);
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('toptitle', 'message回复');
        $this->smarty->display('msgreply.htm');
    }
    
    /*
     * message回复表单     速卖通 订单留言
     */
    public function view_replyMessageFormAliOrder(){
        $msgids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if ($msgids === '') {   //没指定id
            $msgdata = array('data'=>array('请指定要回复的message'), 'link'=>'index.php?mod=messagefilter&act=getAliOrderList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        /*----- 获得message信息 -----*/
        $idar = explode(',', $msgids);                                                                              //数组
        $idar = array_map('intval', $idar);                                                                         //转换成
        $msg_obj = new messageModel();
        $msg_list = $msg_obj->getMessageInfoAliOrder($idar);
        $localuser  = new GetLoacalUserModel();
        foreach ($msg_list as &$value) {
            $value['createtime']    = trunToLosangeles('Y-m-d H:i:s', $value['createtimestamp']);
            $value['communi']       = $msg_obj->getCommunicationList($value['orderid']);
            $bigestid   = 0;        //最大messageid
            $idregion   = array();
            foreach ($value['communi'] as &$val){
                $val['responsetime']    = date('Y-m-d H:i:s', $val['responsetime']);
                $val['createtimestamp'] = trunToLosangeles('Y-m-d H:i:s', $val['createtimestamp']);
                if ($bigestid < $val['message_id']) {
                	$bigestid  = $val['message_id'];
                }
                if ($val['role'] == 1) {
                	$idar[]         = $val['id'];;
                    if ($val['hasread']==0) {
                    	$idregion[]    = $val['message_id'];
                    }
                }
                $val['content'] = str_replace("\n", '<br>', $val['content']);
            }
            sort($idregion);
            if (count($idregion)>0) {
            	$refirst   = $idregion[0];
            	$reend     = $idregion[(count($idregion)-1)];
            	$msg_obj->markAsReadByMsgId($idregion);                                //标记为已读状态
            	$msg_obj->markUser($idregion, $_SESSION['globaluserid']);              //标记回复人id
            } else {
                $refirst   = 0;
                $reend     = 0;
            }
            $value['idregion_h']    = $refirst;
            $value['idregion_e']    = $reend;
            $value['bigestid']      = $bigestid;
        }//print_r($idregion);exit;
        
        /*----- 获得message信息 -----*/
        /*----- 获得模板信息 -----*/
        $tpl_obj = new MessageTemplateModel();
        $tpl_list = $tpl_obj->getTplList($_SESSION['globaluserid'], array('id', 'name', 'iscommon', 'content','incommonuse'), 2);
        $this->smarty->assign('tpllist', $tpl_list);
        /*----- 获得模板信息  -----*/
        
        /*----- 获得文件夹分类列表 -----*/
        $categorylist = new messagecategoryModel();
        $cat_list = $categorylist->getAllCategoryInfoList(' and is_delete=0',2);
        $this->smarty->assign('catlist', $cat_list);
        /*----- 获得文件夹分类列表 -----*/
        $this->smarty->assign('msglist', $msg_list);
        $this->smarty->assign('bigpriidList', $bigpriidList);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('toptitle', 'message回复');
        $this->smarty->display('msgreplyaliorder.htm');
    }

    /*
     * message回复表单     速卖通 订单留言
     */
    public function view_replyMessageFormAliSite(){
        $msgids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if ($msgids === '') {   //没指定id
            $msgdata = array('data'=>array('请指定要回复的message'), 'link'=>'index.php?mod=messagefilter&act=getAliSiteList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        /*----- 获得message信息 -----*/
        $idar = explode(',', $msgids);                                                                              //数组
        $idar = array_map('intval', $idar);                                                                         //转换成
        $msg_obj = new messageModel();
        $msg_list = $msg_obj->getMessageInfoAliSite($idar);
        foreach ($msg_list as &$value) {
            $value['createtime']    = trunToLosangeles('Y-m-d H:i:s', $value['createtimestamp']);//echo $value['relationId'];
            $value['commnuni']      = $msg_obj->getRlatedSiteMessage($value['relationId']);
            $value['orderids']      = array();
            $temparr    = array();
            $bigestid   = 0;
            $idregion   = array();
            
            foreach ($value['commnuni'] as &$cval){
                $cval['createtimestamp']    = trunToLosangeles('Y:m:d H:i:s', $cval['createtimestamp']);
                if (1 == $cval['role'] && !empty($cval['orderUrl']) && !empty($cval['orderId'])) {
                    if (!in_array($cval['orderId'], $temparr)) {
                    	$value['orderids'][]    = array('account'=>$cval['receiverid'],'orderid'=>$cval['orderId']);
                    	$temparr[]              = $cval['orderId'];
                    }
                }
                if ($bigestid < $cval['message_id']) {
                	$bigestid  = $cval['message_id'];
                }
                
                if ($cval['role'] == 1) {
                    $idar[]         = $cval['id'];;
                    if ($cval['hasread']==0) {
                        $idregion[]    = $cval['message_id'];
                    }
                }
                $cval['content']   = str_replace("\n", '</br>', $cval['content']);
            }
//             print_r($idregion);
            sort($idregion);
            if (count($idregion)>0) {
                $refirst   = $idregion[0];
                $reend     = $idregion[(count($idregion)-1)];
                $msg_obj->markAsReadByMsgId_site($idregion);                                //标记为已读状态
                $msg_obj->markUser_site($idregion, $_SESSION['globaluserid']);              //标记回复人id
            } else {
                $refirst   = 0;
                $reend     = 0;
            }
            $value['idregion_h']    = $refirst;
            $value['idregion_e']    = $reend;
            
            $value['bigestid']  = $bigestid;
        }//print_r($msg_list);exit;
        /*----- 获得message信息 -----*/
        
        /*----- 获得模板信息 -----*/
        $tpl_obj = new MessageTemplateModel();
        $tpl_list = $tpl_obj->getTplList($_SESSION['globaluserid'], array('id', 'name', 'iscommon', 'incommonuse', 'content'), 2);
        $this->smarty->assign('tpllist', $tpl_list);
        /*----- 获得模板信息  -----*/
        
        /*----- 获得文件夹分类列表 -----*/
        $categorylist = new messagecategoryModel();
        $cat_list = $categorylist->getAllCategoryInfoList(' and is_delete=0 ',2);
        $this->smarty->assign('catlist', $cat_list);
        /*----- 获得文件夹分类列表 -----*/
//         print_r($msg_list);exit;
        $this->smarty->assign('msglist', $msg_list);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('toptitle', 'message回复');
        $this->smarty->display('msgreplyalisite.htm');
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
    
    /*
     * 回复消息操作
     */
    public function view_replyMessage(){
        $msgid  = isset($_POST['msgid']) ? $_POST['msgid'] : 0;             //回复的message
        $text   = isset($_POST['text'])  ? trim($_POST['text']) : '';        //回复的内容
        $iscopy = isset($_POST['copy'])  ? trim($_POST['copy']) : 1 ;        //是否抄送到用户邮箱 
        $msg_obj = new messageModel();
        $messageinfo = $msg_obj->getMessageInfo(array($msgid));
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
        $newid      = 0;    //新的队列主键id
        $doresult   = $msg_obj->insertMessageReply($msgid, $iscopy, $text, $msginfo['classid'], $msginfo['ebay_account'], $newid);   	
        $mq_obj     = new RabbitMQClass(MQ_USER, MQ_PSW, MQ_VHOST, MQ_SERVER);
        $mq_obj->queue_publish(MQ_EXCHANGE, array('id'=>$newid));
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
     * 回复消息操作  速卖通 订单留言
     */
    public function view_replyMessageAli(){
        include_once WEB_PATH.'lib/AliMessage.class.php';
        $msgid      = isset($_POST['msgid']) ? $_POST['msgid'] : 0;                                         //订单号
        $text       = isset($_POST['text'])  ? trim($_POST['text']) : '';                                   //回复的内容
        $account    = isset($_GET['account']) ? trim($_GET['account']) : '';                                //账号
        $bigestid   = isset($_GET['bigestid']) ? trim($_GET['bigestid']) : '';                              //id最大值
        $first      = isset($_POST['first']) ? trim($_POST['first']) : 0;                                   //回复的留言区间 
        $end        = isset($_POST['end']) ? trim($_POST['end']) : 0;
        
        $msg_obj    = new messageModel();
//         $msg_obj->setOrderMsgStatus(2, $first, $end, );exit;
        if(empty($account)){
            $msgdata = array('errCode'=>10017, 'errMsg'=>'账号缺少!');
            echo json_encode($msgdata);
            exit();
        }
        if(empty($text)){
            $msgdata = array('errCode'=>10013, 'errMsg'=>'回复内容不能为空!');
            echo json_encode($msgdata);
            exit();
        }
        
        $aliorderObj   = new AliOderMessageModel();
        $aliorderObj->markOrderMsgAsReplyed($first, $end, $msgid);
        
        //加载token信息
        $configFile = WEB_PATH.'lib/ali_keys/'."config_{$account}.php";
        if (file_exists($configFile)){
            include $configFile;
        }else{
            echo date('Y-m-d H:i:s', time()).'---'.__LINE__."key file was not found !\n";exit;
        }
        
        $aliRepl_obj    = new AliMessage();
        $aliRepl_obj->setConfig($appKey,$appSecret,$refresh_token);
        $aliRepl_obj->doInit();
        $result = $aliRepl_obj->replyOrderMessage ( $msgid, $text );
        $alireobj = new AliOrderReplyModel ();
        $data = array ();
        $data ['orderid']       = $msgid;
        $data ['content']       = $text;
        $data ['replyuser']     = $_SESSION ['globaluserid'];
        $alireobj->insertData ( $data );
        $starttime      = time () - 300;
        $endtime        = time () + 300;
        $starttime      = trunToLosangeles ( 'm/d/Y H:i:s', $starttime );
        $endtime        = trunToLosangeles ( 'm/d/Y H:i:s', $endtime );sleep(6);
        $newMsg         = $aliRepl_obj->getOrderMessageMin ( $starttime, $endtime, $msgid, $account ,$bigestid);
        $newdiv         = '';
        if (!empty($newMsg)) {
            foreach ( $newMsg as $nmsg ) {
                if ($bigestid < $nmsg['id']) {
                	$bigestid  = $nmsg['id'];
                }
                $sendtime = aliTranslateTime ( $nmsg ['gmtCreate'] );
                $sendtime = date ( 'Y-m-d H:i:s', $sendtime );
                $newdiv .= <<<EOF
                   <div style="background-color:#E1FACF; padding:2px; margin-top:3px;margin-bottom:3px;">
    				        <div><span style=" width:100px;font-family:Arial,Verdana,Helvetica,sans-serif; font-size:13px;">$nmsg[senderName]</span>&nbsp;:$sendtime</div>
    				        <div style="padding-left:113px;color:#525252;font-family:Arial,Verdana,Helvetica,sans-serif;font-size:13px;">$nmsg[content]</div>
    					</div>
EOF;
            }
        }
        if (FALSE === $result) {                                                                            //执行失败
            $msgdata = array('errCode'=>10015, 'errMsg'=>'回复失败!');
            echo json_encode($msgdata);
            exit();
        } else {                                                                                            //执行成功
            $msgdata = array('errCode'=>10016, 'errMsg'=>'回复成功!','newmsg'=>$newdiv, 'bigestid'=>$bigestid);
            echo json_encode($msgdata);
            exit();
        }
    }
    
    /*
     * 回复消息操作  速卖通 站内信
    */
    public function view_replyMessageAli_site(){
        include_once WEB_PATH.'lib/AliMessage.class.php';
        $text       = isset($_POST['text'])  ? trim($_POST['text']) : '';                                   //回复的内容
        $buyerId    = isset($_GET['buyerid']) ? trim($_GET['buyerid']) : '';                                //买家id
        $account    = isset($_GET['account']) ? trim($_GET['account']) : '';                                //卖家账号
        $relationid = isset($_GET['relationid']) ? trim($_GET['relationid']) : '';                          //消息关系id
        $bigestid   = isset($_GET['bigestid']) ? trim($_GET['bigestid']) : '';                              //最大的id
        
        $region_h   = isset($_GET['region_h']) ? trim($_GET['region_h']) : 0;                               //回复区间id 头
        $region_e   = isset($_GET['region_e']) ? trim($_GET['region_e']) : 0;                               //回复区间id 尾
        
        $msg_obj    = new messageModel();
        if(empty($buyerId)){
            $msgdata = array('errCode'=>10017, 'errMsg'=>'缺少买家ID!');
            echo json_encode($msgdata);
            exit();
        }
        if(empty($account)){
            $msgdata = array('errCode'=>10017, 'errMsg'=>'缺少卖家账号!');
            echo json_encode($msgdata);
            exit();
        }
        if(empty($text)){
            $msgdata = array('errCode'=>10013, 'errMsg'=>'回复内容不能为空!');
            echo json_encode($msgdata);
            exit();
        }
        //加载token信息
        $configFile = WEB_PATH.'lib/ali_keys/'."config_{$account}.php";
        //                  echo $configFile;"\n";exit;
        if (file_exists($configFile)){
            include $configFile;
        }else{
            echo date('Y-m-d H:i:s', time()).'---'.__LINE__."key file was not found !\n";exit;
        }
        
        /*将消息标记为已读*/
        $mysql_obj      = new MysqlModel();
        $dataSet        = array('hasread'=>2, 'replytime'=>time());
        $whereSql       = " where message_id>=$region_h and message_id<=$region_e and role=1 and relationId=$relationid";
        $effectNum      = $mysql_obj->update('msg_alisitemessage', $dataSet, $whereSql);
        
        $aliRepl_obj    = new AliMessage();
        $aliRepl_obj->setConfig($appKey,$appSecret,$refresh_token);
        $aliRepl_obj->doInit();
        
        $result             = $aliRepl_obj->replySiteMessage ( $buyerId, $text );
        $alireobj           = new AliSiteReplyModel ();
        $data               = array ();
        $data ['relationid']    = $relationid;                                                          //站内信关联id
        $data ['content']       = $text;                                                                //内容
        $data ['replyuser']     = $_SESSION ['globaluserid'];                                           //回复人id
        $alireobj->addNewReplyData( $data );
        $starttime = time () - 300;
        $endtime = time () + 300;
        $starttime = trunToLosangeles ( 'm/d/Y H:i:s', $starttime );
        $endtime = trunToLosangeles ( 'm/d/Y H:i:s', $endtime );sleep(6);
        $newMsg = $aliRepl_obj->getSiteMessageMin ( $starttime, $endtime, $bigestid, $buyerId );
//         print_r($newMsg);exit;
        $newdiv = '';
        foreach ( $newMsg as $nmsg ) {
            if ($bigestid < $nmsg['id']) {
            	$bigestid = $nmsg['id'];
            }
            $sendtime = aliTranslateTime ( $nmsg['gmtCreate'] );
            $sendtime = date ( 'Y-m-d H:i:s', $sendtime );
            // print_r($nmsg);exit;
            $imgstr     = '';
            $orderstr   = '';
            if (!empty($nmsg['orderUrl'])) {
            	$orderstr  = <<<EOF
            	<div style="float:left; margin-left:10px;">
                    <a href="$nmsg[orderUrl]" target="_blank">$nmsg[orderId]</a>
                </div>
EOF;
            }
            if (!empty($nmsg['fileUrl'])) {
            	$imgstr    = <<<EOF
            	<div style="float:left;padding-left:3px;padding-top:1px;">
				    <a href="{$commn[fileUrl]}" target="_blank"><img src="{$commn[fileUrl]}" style="width:78px; height:78px;"></a>
				</div>
EOF;
            }
            
            $newdiv .= <<<EOF
                <div style="background-color:#E1FACF; padding:2px; margin-top:3px;margin-bottom:3px;">
	                        <div><span style=" width:100px;font-family:Arial,Verdana,Helvetica,sans-serif; font-size:13px;">$nmsg[senderName]</span>&nbsp;:$sendtime</div>
	                        <div style="padding-left:113px;color:#525252;font-family:Arial,Verdana,Helvetica,sans-serif;font-size:13px;">$nmsg[content]</div>
		                    <div style="background-color:#fff">
	                        $imgstr $orderstr
			                <div style="clear:both;"></div>
	                        </div>
	                    </div>
EOF;
        }
        if (FALSE === $result) {                                                 //执行失败
            $msgdata = array('errCode'=>10015, 'errMsg'=>'回复失败!');
            echo json_encode($msgdata);
            exit();
        } else {                                                                 //执行成功
            $msgdata = array('errCode'=>10016, 'errMsg'=>'回复成功!','newmsg'=>$newdiv, 'bigestid'=>$bigestid);
            echo json_encode($msgdata);
            exit();
        }
    }
    /*
     * 标记为已经回复
     */
    public function view_markAsRead(){
        $msgids = isset($_POST['msgids']) ? trim($_POST['msgids']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type'])  : 'read';         //标记类型
        $msgids = clearData($msgids);
        $msg_obj = new messageModel();
        $mq_obj     = new RabbitMQClass(MQ_USER, MQ_PSW, MQ_VHOST, MQ_SERVER);
        foreach ($msgids as $idval) {
            $messageinfo = $msg_obj->getMessageInfo(array($idval));
            if (!empty($messageinfo)) {
            	$messageinfo = $messageinfo[0];
            	$newid      = 0;                                               //新的队列主键id
            	//if ($messageinfo['status'] == 0) {
            		$msg_obj->markAaRead($idval, $messageinfo['classid'],$type, $messageinfo['ebay_account'], $newid);
            	//}
            	$mq_obj->queue_publish(MQ_EXCHANGE, array('id'=>$newid));
            }
        }
        $msgdata = array('errCode'=>10020, 'errMsg'=>'操作成功');
        echo json_encode($msgdata);
        exit();
    }
    
    /*
     * ajax获取message内容
     * 目的：解决当message消息文件丢失时重新从ebay上获取message消息内容
     */
    public function view_getMessageBody(){
        $id = isset($_GET['id']) ? $_GET['id'] : FALSE;
        if (!is_numeric($id) || $id=== FALSE ) {
            $msgdata = array('errCode'=>10040, 'errMsg'=>'缺少参数!');
            echo json_encode($msgdata);
            exit();
        }
        
        $msg_obj = new messageModel();
        $msginfo = $msg_obj->getMessageInfo(array($id));
        if (empty($msginfo)) {
        	$msgdata = array('errCode'=>10041, 'errMsg'=>'message不存在!');
            echo json_encode($msgdata);
            exit();
        }
        $msginfo = $msginfo[0];
        
        /*----- 获取message内容 -----*/
        $fetch_obj  = new FetchMessageModel();
        $result     = $fetch_obj->fetchMessageBody($msginfo['message_id'], $msginfo['ebay_account']);
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
     * 从订单系统获取订单信息
     */
    function view_getOderInfo(){
        $userid = isset($_GET['userId']) ? $_GET['userId'] : FALSE;                     //买家账号
        $seller = isset($_GET['seller']) ? $_GET['seller'] : FALSE;                     //卖家账号
        $mid    = isset($_GET['mid'])    ? $_GET['mid']    : FALSE;                     
        if ($userid === FALSE) {
        	$data = array('errCode'=>10045, 'errMsg'=>'缺少参数');
        	echo json_encode($data);
        	exit;
        }
        
        $result = getOpenSysApi(OPENGETORDER, array('type'=>'orderinfo','buyeraccount'=>$userid, 'selleraccount'=>$seller));
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
                            <td>地址信息</td>
        					<td>更多</td>
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
            }
        }
        
//         print_r($return);exit;
        $data = array('errCode'=>10047, 'errMsg'=>'OK', 'list1'=>$tbtitle.$listInTwomonth, 'list2'=>$listMoreTwomonth, 'title'=>$tbtitle,'defaddr'=>$default_addr);
        echo json_encode($data);
        exit;
    }
    
    /*
     * 回复页面 ajax获取message对应的订单详情
     */
    public function view_fetchAliOrderDetail(){
    	$id		= isset($_GET['id']) 	? intval($_GET['id']) : FALSE;
    	$type	= isset($_GET['type']) 	? trim($_GET['type']) : FALSE;
    	$returndata	= array('errCode'=>0,'errMsg'=>'');
    	if (empty($id)) {												//id不合法
    		$returndata['errCode']	= 1;
    		$returndata['errMsg']	= 'id不合法';
    		echo json_encode($returndata);
    		exit;
    	}
    	$alimsg_obj		= new AliOderMessageModel();
    	if ($type == 'order') {
    		$messageinfo	= $alimsg_obj->getMessageInfoByMessageId($id);
    	} else {
    		$messageinfo	= $alimsg_obj->getMessageInfoByMessageId_site($id);
    		if (empty($messageinfo['orderId']) || empty($messageinfo['orderUrl']) ) {
    		    $returndata['errCode']	= 9;
    		    $returndata['errMsg']	= '未关联订单号 !';
    		    echo json_encode($returndata);
    		    exit;
    		}
    	}
    	if (empty($messageinfo)) {
    		$returndata['errCode']	= 2;
    		$returndata['errMsg']	= '没找到messsage信息!';
    		echo json_encode($returndata);
    		exit;
    	}
    	$receiverid		= $messageinfo['receiverid'];
//     	$receiverid    = 'cn1500439756';
    	if ($type == 'order') {
    		$orderid	= $messageinfo['orderid'];
    	} else {
    		$orderid	= $messageinfo['orderId'];
    	}//$orderid = '60293405706949';
    	if (empty($orderid)) {													//订单号不存在
    		$returndata['errCode']	= 3;
    		$returndata['errMsg']	= '改留言不存在订单号!';
    		echo json_encode($returndata);
    		exit;
    	}
    	$tokenfilepath	= WEB_PATH."lib/ali_keys/config_{$receiverid}.php";
    	if (!file_exists($tokenfilepath)) {
    		$returndata['errCode']	= 2;
    		$returndata['errMsg']	= '没找到messsage信息!';
    		echo json_encode($returndata);
    		exit;
    	}
    	include_once ''.$tokenfilepath;
    	include_once WEB_PATH.'lib/AliMessage.class.php';
    	$ali_obj	= new AliMessage();
    	$ali_obj->setConfig($appKey,$appSecret,$refresh_token);
    	$ali_obj->doInit();
    	$detail 	= $ali_obj->fetchOrderdetail($orderid);
    	if ($detail == FALSE) {													//获取消息失败
    		$returndata['errCode']	= 4;
    		$returndata['errMsg']	= '获取订单详情失败!';
    		echo json_encode($returndata);
    		exit;
    	}
//     	print_r($detail);exit;
    	$time   = $detail['gmtCreate'];
    	$year   = substr($time, 0,4);                                              //年
    	$month  = substr($time, 4,2);                                              //月
    	$day    = substr($time, 6,2);                                              //日
    	$time   =  $month.'/'.$day.'/'.$year;
    	$timeend   = $month.'/'.(intval($day)+1).'/'.$year;
    	$skustr = '';
    	foreach ($detail['childOrderList'] as $sku){
    	    $skucode   = substr($sku['skuCode'],0, strlen($sku['skuCode'])-1);
    	    $tmpstr    = $skucode;
    	    $hstr      = strrchr($tmpstr, '*');                                    //处理组合料号
    	    $tmpstr    = $hstr===FALSE ? $tmpstr : ltrim($hstr, '*');     
    	    $spu       = explode('_', $tmpstr);
    	    $spu       = $spu[0];
    	    $attr      = json_decode($sku[productAttributes], TRUE);
    	    $skuimgurl = getSkuImg($spu, $tmpstr, 'G');
    	    $attrinfo  = json_decode($sku['productAttributes'], TRUE);
//     	    print_r($sku);exit;
            $attrstr    = array();
    	    if (isset($sku['productAttributes']) && !empty($attrinfo['sku'])){
    	        foreach ($attrinfo['sku'] as $attrval){
    	            $attrstr[]   = $attrval['pName'].':'.$attrval['pValue'];
    	        }
    	    }
//     	    print_r($attrstr);exit;
    	    $attrstr   = implode('<br>', $attrstr);
    	    $skustr .= <<<EOF
				<tr>
						<td>
							<img src="$skuimgurl" width="50" height="50">
						</td>
						<td align="left">
						  <a href="http://www.aliexpress.com/item/something/$sku[productId].html" target="_blank">$sku[productName]</a>
						</td>
						<td>
						          $attrstr
						</td>
						<td>
						        $skucode          
						</td>
						<td>
						        $sku[productCount]
						</td>
						<td>
					           {$sku[productPrice][currency][symbol]}{$sku[productPrice][amount]}
						</td>
				</tr>
EOF;
    	}
        $skulist    = <<<EOF
					<tr class="title">
						<td>
							图片
						</td>
						<td>
							标题
						</td>
						<td style="width:60px;">
							属性
						</td>
						<td>
							SKU
						</td>
						<td style="width:60px;">
							数量
						</td>
						<td>
							单价
						</td>
					</tr>
                    $skustr
EOF;
        //计算倒计时
        $timeStr    = '';
        if ($detail['orderStatus'] == 'RISK_CONTROL') {                                 //计算风控倒计时
        	$paytimestamp  = aliTranslateTime($detail['gmtPaySuccess']);                //付款时间戳
        	$riskendtime   = $paytimestamp+86400;
        	$timeStr       = date('m/d/Y H:i:s', $riskendtime);                         //风控结束时间字符串表示
        	$timeStr       = $riskendtime;
        } elseif ($detail['logisticsStatus'] == 'SELLER_SEND_GOODS' && $detail['orderStatus'] != 'FINISH') {
            $sendTimeStamp  = aliTranslateTime($detail['logisticInfoList'][0]['gmtSend']);  //发货时间戳
            $days           = $alimsg_obj->culculateCountdown($receiverid,$detail['logisticInfoList'][0]['logisticsTypeCode'], 
                              $detail['receiptAddress']['country']);
            if ($days === FALSE) {
                $timeStr        = AliOderMessageModel::$errMsg;
            } else {
                $endtime        = $sendTimeStamp+($days*86400);
                //$timeStr        = date('m/d/Y H:i:s', $endtime);
                $timeStr        = $endtime;
            }
            
        } 
        
    	//物流信息
    	$shipstr   = '';
    	if (isset($detail['logisticInfoList'][0])) {
    		$shipstr  .= '运输方式:'.$detail['logisticInfoList'][0]['logisticsTypeCode'].'<br>';
    		$carrier   = $detail['logisticInfoList'][0]['logisticsTypeCode'];
            $tracksn   = $detail['logisticInfoList'][0]['logisticsNo'];
            $trackstr  = <<<EOF
            <a href="javascript:queryExpressInfo('aliexpress', '$carrier', '$tracksn', 'zh')">$tracksn</a>
EOF;
            $shipstr  .= '跟踪号:'.$trackstr.'<br>';
    	}
    	$returndata['errCode']	= 5;
    	$returndata['errMsg']	= '成功!';
    	$returndata['data']		= array(
    		'buyer'           => $detail['buyerInfo']['firstName'].$detail['buyerInfo']['lastName'],                       //买家
    		'seller'          => $detail['sellerOperatorLoginId'],                                                         //卖家
    		'orderId'         => $orderid,                                                                                 //订单号
    		'createtime'      => formateAliTime($detail['gmtCreate']),                                                     //交易创建时间
    	    'paytime'         => !empty($detail['gmtPaySuccess']) ? formateAliTime($detail['gmtPaySuccess']):'未付款',       //付款时间
            'initOderAmount'  => $detail['initOderAmount']['currency']['symbol'].$detail['initOderAmount']['amount'],      //产品总金额
    	    'OderAmount'      => $detail['orderAmount']['currency']['symbol'].$detail['orderAmount']['amount'],            //订单金额
    	    'paytype'         => '-----',                                                                                  //付款方式
    	    'fundStatus'      => $this->status2str($detail['fundStatus']),                                                 //资金状态
    	    'loanStatus'      => $this->status2str($detail['loanStatus']),                                                 //放款状态
    	    'issueStatus'     => $this->status2str($detail['issueStatus']),                                                //纠纷状态
    	    'issuscolor'      => $detail['issueStatus']=='NO_ISSUE' ? 'green' : 'red',
    	    'orderstatus'     => $this->status2str($detail['orderStatus']),                                                //订单状态
    	    'logisticsStatus' => $this->status2str($detail['logisticsStatus']),                                            //物流状态
    	    'logisticsMoney'  => $detail['logisticsAmount']['amount'],                                                     //物流金额
    	    'mobileNo'        => $detail['receiptAddress']['mobileNo'],                                                    //手机
    	    'phoneNumber'     => $detail['receiptAddress']['phoneArea'].'-'.$detail['receiptAddress']['phoneNumber'],      //座机
    	    'address'         => $detail['receiptAddress']['detailAddress'].' '.$detail['receiptAddress']['city'].' '.
    	                         $detail['receiptAddress']['province'].' '. $detail['receiptAddress']['country'],
    	    'skulist'         => $skulist,
    	    'refund'          => isset($detail['refundInfo']) ? '退款状态'.$detail['refundInfo']['refundStatus'].'<br>'.
    	                            '退款类型'.$detail['refundInfo']['refundType'] : '',
    	    'logisticInfo'    => $shipstr,
    	    'buyerSignerFullname'  => isset($detail['buyerSignerFullname']) ? $detail['buyerSignerFullname'] : '',
    	    'zip'             => isset($detail['receiptAddress']['zip']) ? $detail['receiptAddress']['zip'] : '',
    	    'email'           => isset($detail['buyerInfo']['email']) ? $detail['buyerInfo']['email'] : '' ,                 
    	    'timestr'         => $timeStr,                                                                                  //倒计时日期
    	    'commission'      => '$'.round($detail['orderAmount']['amount']*0.05, 2),                                                //佣金
    	    'profit'          => '$'.round($detail['orderAmount']['amount']*0.95, 2),
    	);
    	if (isset($detail['logisticInfoList'][0]['gmtSend'])) {
    		$timeinfo    = extractAliTimeInfo($detail['logisticInfoList'][0]['gmtSend']);
    		$returndata['data']['shippedtime']    = $timeinfo['year'].'-'.$timeinfo['month'].'-'.$timeinfo['day'].' '.
    		                                          $timeinfo['hour'].':'.$timeinfo['minit'].':'.$timeinfo['second'];
    	} else {
    	    $returndata['data']['shippedtime'] = '';
    	}
    	$accountname   = aliAccountf2Name($detail['sellerOperatorLoginId']);
    	if ($accountname  == FALSE) {      //没找到对应关系
    		$accountname == '';
    	}
    	$result_sys = getOpenSysApi('aliExpressOrderInfo', array('type'=>'orderinfo','recordnumber'=>$orderid, 'selleraccount'=>$accountname));
//     	print_r($result_sys);exit;
    	if (!empty($result_sys['data'])) {
    		$returndata['data']['systemnum']      = $result_sys['data'][0]['ebay_id'];                 //系统编号
    		$returndata['data']['syscarrer']      = $result_sys['data'][0]['ebay_carrier'];            //运输方式
    		$returndata['data']['systracknumber'] = $result_sys['data'][0]['ebay_tracknumber'];        //跟踪号
//     		$returndata['data']['shippedtime']    = empty($result_sys['data'][0]['ShippedTime']) ? '' : date('Y-m-d H:i:s', $result_sys['data'][0]['ShippedTime']);  //发货时间
    		$returndata['data']['status']         = $result_sys['data'][0]['catename'];                //发货状态
    	    $returndata['data']['ebay_note']      = $result_sys['data'][0]['note'];                    //订单留言
    	} else {
    	    $returndata['data']['systemnum']      = '';                 //系统编号
    	    $returndata['data']['syscarrer']      = '';                 //运输方式
    	    $returndata['data']['systracknumber'] = '';                 //跟踪号
//     	    $returndata['data']['shippedtime']    = '';                 //发货时间
    	    $returndata['data']['status']         = '';                 //发货状态
    	    $returndata['data']['ebay_note']      = '';    
    	}
//     	print_r($returndata);exit;
    	echo json_encode($returndata);
    }
    
    /*
     * 速卖通订单状态到字符串的转换
     */
    private function status2str($status){
        switch ($status) {
        	case 'NO_ISSUE':
            	return  '无纠纷';
            	break;
        	case 'IN_ISSUE':
        	    return '纠纷中';
        	    break;
        	case 'END_ISSUE':
        	    return '纠纷结束';
        	    break;
        	case 'loan_none':
        	    return '无放款';
        	    break;
        	case 'wait_loan':
        	    return '等待放款';
        	    break;
        	case 'loan_ok':
        	    return '放款成功';
        	    break ;
        	case 'WAIT_SELLER_SEND_GOODS':
        	    return '等待卖家发货';
        	    break;
        	case 'RISK_CONTROL':
        	    return '24小时风控中';
        	    break;
        	case 'SELLER_SEND_PART_GOODS':
        	    return '卖家部分发货';
        	    break;
        	case 'SELLER_SEND_GOODS':
        	    return '卖家已发货';
        	    break ;
        	case 'BUYER_ACCEPT_GOODS':
        	    return '买家已确认收货';
        	    break;
        	case 'pay_success':
        	    return '付款成功';
        	    break;
        	case 'PAY_SUCCESS':
        	    return '付款成功';
        	    break;
        	case 'WAIT_BUYER_ACCEPT_GOODS':
        	    return '等待买家收货';
        	    break;
        	case 'FINISH':
        	    return '已完成';
        	    break;
        	case 'IN_CANCEL':
        	    return '取消订单';
        	    break;
        	default:
        		return $status;
        	break;
        }   
    }
    
    /*
     * 批量重回message
     */
    public function view_reReplyMessage(){
        $ids        = isset($_GET['ids']) ? trim($_GET['ids']) : FALSE;
        $returndata = array('errCode'=>0, 'errMsg'=>'');
        if (empty($ids)) {
        	$returndata['errCode'] = 100;
        	$returndata['errMsg']  = '缺少参数!';
        	echo json_encode($returndata); exit;
        }
        $ids    = clearData($ids);
        $msg_ojb    = new messageModel();
        if (empty($ids)) {
            $returndata['errCode'] = 101;
            $returndata['errMsg']  = '缺少参数!';
            echo json_encode($returndata); exit;
        }
//         print_r($ids);exit;
        $result = $msg_ojb->reReplyMessage_ebay($ids);
        $returndata['errCode']  = 102;
        $returndata['errMsg']   = '';
        $returndata['data']     = $result;
        echo json_encode($returndata);
    }
    
    /*
     * 回复页面 ajax获取message对应的订单详情 跟订单号和卖家账号
    */
    public function view_fetchAliOrderDetailByOrderId(){
        $orderid		= isset($_GET['orid']) 	? trim($_GET['orid']) : FALSE;
        $account    	= isset($_GET['account']) 	? trim($_GET['account']) : FALSE;
        $receiverid		= $account;
        if (empty($receiverid)){
            $returndata['errCode']	= 3;
            $returndata['errMsg']	= '缺少卖家账号!';
            echo json_encode($returndata);
            exit;
        }
        if (empty($orderid)) {													//订单号不存在
            $returndata['errCode']	= 3;
            $returndata['errMsg']	= '缺少订单号!';
            echo json_encode($returndata);
            exit;
        }
        $tokenfilepath	= WEB_PATH."lib/ali_keys/config_{$receiverid}.php";
        if (!file_exists($tokenfilepath)) {
            $returndata['errCode']	= 2;
            $returndata['errMsg']	= '没找到messsage信息!';
            echo json_encode($returndata);
            exit;
        }
        include_once ''.$tokenfilepath;
        include_once WEB_PATH.'lib/AliMessage.class.php';
        $ali_obj	= new AliMessage();
        $ali_obj->setConfig($appKey,$appSecret,$refresh_token);
        $ali_obj->doInit();//$orderid = '60637623549259';
        $detail 	= $ali_obj->fetchOrderdetail($orderid);
        if ($detail == FALSE) {													//获取消息失败
            $returndata['errCode']	= 4;
            $returndata['errMsg']	= '获取订单详情失败!';
            echo json_encode($returndata);
            exit;
        }//print_r($detail);exit;
        $alimsg_obj		= new AliOderMessageModel();
        $time   = $detail['gmtCreate'];
        $year   = substr($time, 0,4);          //年
        $month  = substr($time, 4,2);          //月
        $day    = substr($time, 6,2);          //日
        $time   =  $month.'/'.$day.'/'.$year;
        $timeend   = $month.'/'.(intval($day)+1).'/'.$year;
        $skustr = '';
        foreach ($detail['childOrderList'] as $sku){
            $skucode   = substr($sku['skuCode'],0, strlen($sku['skuCode'])-1);
            $tmpstr    = $skucode;
            $hstr      = strrchr($tmpstr, '*');                                   //处理组合料号
            $tmpstr    = $hstr===FALSE ? $tmpstr : ltrim($hstr, '*');
            $spu       = explode('_', $tmpstr);
            $spu       = $spu[0];
            $attr      = json_decode($sku[productAttributes], TRUE);
            $skuimgurl = getSkuImg($spu, $tmpstr, 'G');
            $attrinfo  = json_decode($sku['productAttributes'], TRUE);
            $attrstr    = array();
            if (isset($sku['productAttributes']) && !empty($attrinfo['sku'])){
                foreach ($attrinfo['sku'] as $attrval){
                    $attrstr[]   = $attrval['pName'].':'.$attrval['pValue'];
                }
            }
            $attrstr   = implode('<br>', $attrstr);
            $skustr .= <<<EOF
				<tr>
						<td>
							<img src="$skuimgurl" width="50" height="50">
						</td>
						<td align="left">
						<a href="http://www.aliexpress.com/item/something/$sku[productId].html" target="_blank">$sku[productName]</a>
						</td>
						<td>
						          $attrstr
						</td>
						<td>
						        $skucode
						</td>
						<td>
						        $sku[productCount]
						</td>
						<td>
					           {$sku[productPrice][currency][symbol]}{$sku[productPrice][amount]}
						</td>
					</tr>
EOF;
        }
        $skulist    = <<<EOF
					<tr class="title">
						<td>
							图片
						</td>
						<td>
							标题
						</td>
						<td style="width:60px;">
							属性
						</td>
						<td>
							SKU
						</td>
						<td style="width:60px;">
							数量
						</td>
						<td>
							单价
						</td>
					</tr>
                    $skustr
EOF;
        //计算倒计时
        $timeStr    = '';
        if ($detail['orderStatus'] == 'RISK_CONTROL') {                                                             //计算风控倒计时
            $paytimestamp  = aliTranslateTime($detail['gmtPaySuccess']);                                            //付款时间戳
            $riskendtime   = $paytimestamp+86400;
            $timeStr       = date('m/d/Y H:i:s', $riskendtime);                                                     //风控结束时间字符串表示
            $timeStr       = $riskendtime;
        } elseif ($detail['logisticsStatus'] == 'SELLER_SEND_GOODS' && $detail['orderStatus'] != 'FINISH') {
            $sendTimeStamp  = aliTranslateTime($detail['logisticInfoList'][0]['gmtSend']);                          //发货时间戳
            $days           = $alimsg_obj->culculateCountdown($receiverid,$detail['logisticInfoList'][0]['logisticsTypeCode'],
                    $detail['receiptAddress']['country']);
            if ($days === FALSE) {
                $timeStr        = AliOderMessageModel::$errMsg;
            } else {
                $endtime        = $sendTimeStamp+($days*86400);
                $timeStr        = $endtime;
            }
    
        }
    
        //物流信息
        $shipstr   = '';
        if (isset($detail['logisticInfoList'][0])) {
            $shipstr  .= '运输方式:'.$detail['logisticInfoList'][0]['logisticsTypeCode'].'<br>';
            $carrier   = $detail['logisticInfoList'][0]['logisticsTypeCode'];
            $tracksn   = $detail['logisticInfoList'][0]['logisticsNo'];
            $trackstr  = <<<EOF
            <a href="javascript:queryExpressInfo('aliexpress', '$carrier', '$tracksn', 'zh')">$tracksn</a>
EOF;
            $shipstr  .= '跟踪号:'.$trackstr.'<br>';
        }
        $returndata['errCode']	= 5;
        $returndata['errMsg']	= '成功!';
        $returndata['data']		= array(
                'buyer'           => $detail['buyerInfo']['firstName'].$detail['buyerInfo']['lastName'],                       //买家
                'seller'          => $detail['sellerOperatorLoginId'],                                                         //卖家
                'orderId'         => $orderid,                                                                                 //订单号
                'createtime'      => formateAliTime($detail['gmtCreate']),                                                     //交易创建时间
                'paytime'         => !empty($detail['gmtPaySuccess']) ? formateAliTime($detail['gmtPaySuccess']):'未付款',       //付款时间
                'initOderAmount'  => $detail['initOderAmount']['currency']['symbol'].$detail['initOderAmount']['amount'],      //产品总金额
                'OderAmount'      => $detail['orderAmount']['currency']['symbol'].$detail['orderAmount']['amount'],            //订单金额
                'paytype'         => '-----',                                                                                  //付款方式
                'fundStatus'      => $this->status2str($detail['fundStatus']),                                                 //资金状态
                'loanStatus'      => $this->status2str($detail['loanStatus']),                                                 //放款状态
                'issueStatus'     => $this->status2str($detail['issueStatus']),                                                //纠纷状态
                'issuscolor'      => $detail['issueStatus']=='NO_ISSUE' ? 'green' : 'red',
                'orderstatus'     => $this->status2str($detail['orderStatus']),                                                //订单状态
                'logisticsStatus' => $this->status2str($detail['logisticsStatus']),                                            //物流状态
                'logisticsMoney'  => $detail['logisticsAmount']['amount'],                                                     //物流金额
                'mobileNo'        => $detail['receiptAddress']['mobileNo'],                                                    //手机
                'phoneNumber'     => $detail['receiptAddress']['phoneArea'].'-'.$detail['receiptAddress']['phoneNumber'],      //座机
                'address'         => $detail['receiptAddress']['detailAddress'].' '.$detail['receiptAddress']['city'].' '.
                $detail['receiptAddress']['province'].' '. $detail['receiptAddress']['country'],
                'skulist'         => $skulist,
                'refund'          => isset($detail['refundInfo']) ? '退款状态'.$detail['refundInfo']['refundStatus'].'<br>'.
                '退款类型'.$detail['refundInfo']['refundType'] : '',
                'logisticInfo'    => $shipstr,
                'buyerSignerFullname'  => isset($detail['buyerSignerFullname']) ? $detail['buyerSignerFullname'] : '',
                'zip'             => isset($detail['receiptAddress']['zip']) ? $detail['receiptAddress']['zip'] : '',
                'email'           => isset($detail['buyerInfo']['email']) ? $detail['buyerInfo']['email'] : '' ,
                'timestr'         => $timeStr,                                                                                  //倒计时日期
                'commission'      => '$'.round($detail['orderAmount']['amount']*0.05, 2),                                                //佣金
                'profit'          => '$'.round($detail['orderAmount']['amount']*0.95, 2),
        );
        if (isset($detail['logisticInfoList'][0]['gmtSend'])) {
            $timeinfo    = extractAliTimeInfo($detail['logisticInfoList'][0]['gmtSend']);
            $returndata['data']['shippedtime']    = $timeinfo['year'].'-'.$timeinfo['month'].'-'.$timeinfo['day'].' '.
                    $timeinfo['hour'].':'.$timeinfo['minit'].':'.$timeinfo['second'];
        } else {
            $returndata['data']['shippedtime'] = '';
        }
        $accountname   = aliAccountf2Name($detail['sellerOperatorLoginId']);
        if ($accountname  == FALSE) {      //没找到对应关系
            $accountname == '';
        }
        $accountname   = aliAccountf2Name($account);
        if ($accountname  == FALSE) {      //没找到对应关系
            $accountname == '';
        }
        $result_sys = getOpenSysApi('aliExpressOrderInfo', array('type'=>'orderinfo','recordnumber'=>$orderid, 'selleraccount'=>$accountname));
        if (!empty($result_sys['data'])) {
            $returndata['data']['systemnum']      = $result_sys['data'][0]['ebay_id'];                 //系统编号
            $returndata['data']['syscarrer']      = $result_sys['data'][0]['ebay_carrier'];            //运输方式
            $returndata['data']['systracknumber'] = $result_sys['data'][0]['ebay_tracknumber'];        //跟踪号
            //     		$returndata['data']['shippedtime']    = empty($result_sys['data'][0]['ShippedTime']) ? '' : date('Y-m-d H:i:s', $result_sys['data'][0]['ShippedTime']);  //发货时间
            $returndata['data']['status']         = $result_sys['data'][0]['catename'];                //发货状态
            $returndata['data']['ebay_note']      = $result_sys['data'][0]['note'];                    //订单留言
        } else {
            $returndata['data']['systemnum']      = '';                 //系统编号
            $returndata['data']['syscarrer']      = '';                 //运输方式
            $returndata['data']['systracknumber'] = '';                 //跟踪号
            //     	    $returndata['data']['shippedtime']    = '';                 //发货时间
            $returndata['data']['status']         = '';                 //发货状态
            $returndata['data']['ebay_note']      = '';
        }
//             	print_r($returndata);exit;
        echo json_encode($returndata);
    }
    
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
    
}

