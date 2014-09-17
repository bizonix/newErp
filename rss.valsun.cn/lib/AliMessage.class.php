<?php
class AliMessage{

	var $server			     =	'https://gw.api.alibaba.com';
	var $rootpath		     =	'openapi';					               //openapi,fileapi
	var $protocol		     =	'param2';					               //param2,json2,jsonp2,xml2,http,param,json,jsonp,xml
	var $ns				     =	'aliexpress.open';
	var $version		     =	1;
	var $appKey			     =	'895611';					               //填自己的
	var $appSecret		     =	'EcwaA6#3H:p';				               //填自己的
	var $refresh_token	     =	"96f3a689-a9a8-4858-bd37-7d53d673c39b";    //填自己的
	var $callback_url	     =	"http://202.103.191.209:88/aliexpress/callback.php";
	
	private $accountslist      = array(
	        'cn1000268236',
            'cn1000421358',
            'cn1000616054',
            'cn1000960806',
            'cn1000983412',
            'cn1000983826',
            'cn1000999030',
            'cn1001428059',
            'cn1001392417',
            'cn1001424576',
            'cn1001656836',
            'cn1001711574',
            'cn1001718610',
            'cn1001739224',
            'cn1500053764',
            'cn1500152370',
            'cn1500226033',
            'cn1500293467',
            'cn1500439756',
            'cn1500514645',
            'cn1500688776',
            'cn1501288533',
            'cn1501287427',
            'cn1501540493',
            'cn1501578304',
            'cn1501595926',
            'cn1501638006',
            'cn1501642501',
            'cn1501654678',
            'cn1501654797',
            'cn1501655651',
            'cn1501656206',
            'cn1501656494',
            'cn1501657160',
            'cn1501657334',
            'cn1501657572',
            'cn1501686293',
            '3acyber',
            'szsunweb',
            'beauty365',
            'caracc88',
            'bagfashion789',
            'cn1001315312',
            'szfinejo',
            'cn1001377688',
            'cn1001379555',
            'cn1001711552',
            'cn1001718385',
            'cn1001739214',
            'cn1500053754',
            'cn1500152269',
            'cn1500293372',
            'cn1500225927',
            'cn1500439632',
            'cn1500439946',
            'cn1500514393',
            'cn1500688658',
            'cn1501288484',
            'cn1501287406',
            'cn1501534536',
            'cn1501578269',
            'cn1501595496',
            'cn1501637888',
            'cn1510309914',
	        'cn1510304665'
	);
	
	public static $errCode   = 0;
	public static $errMsg    = '';
	private $dbconn          = null;                                       //DB对象

	var $access_token ;

	function __construct() {
	    global $dbConn ;
	    $this->dbconn  = $dbConn;
	}

	function setConfig($appKey,$appSecret,$refresh_token){
		$this->appKey		=	$appKey;
		$this->appSecret	=	$appSecret;
		$this->refresh_token=	$refresh_token;
	}	

	function doInit(){
		$token	=	$this->getToken();
		$this->access_token	=	$token->access_token;
	}

	function Curl($url,$vars=''){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($vars));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		$content=curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	
	//生成签名
	function Sign($vars){
		$str='';
		ksort($vars);
		foreach($vars as $k=>$v){
			$str.=$k.$v;
		}
		return strtoupper(bin2hex(hash_hmac('sha1',$str,$this->appSecret,true)));
	}
	
    //生成签名
	function getCode(){
		$getCodeUrl = $this->server .'/auth/authorize.htm?client_id='.$this->appKey .'&site=aliexpress&redirect_uri='.$this->callback_url.'&_aop_signature='.$this->Sign(array('client_id' => $this->appKey,'redirect_uri' =>$this->callback_url,'site' => 'aliexpress'));
		return '<a href="javascript:void(0)" onclick="window.open(\''.$getCodeUrl.'\',\'child\',\'width=500,height=380\');">请先登陆并授权</a>';
	}
	
	//获取授权
	function getToken(){
		if(!empty($this->refresh_token)){
			$getTokenUrl="{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/system.oauth2/refreshToken/{$this->appKey}";
			$data =array(
				'grant_type'		=>'refresh_token',		              //授权类型
				'client_id'			=>$this->appKey,				      //app唯一标示
				'client_secret'		=>$this->appSecret,			          //app密钥
				'refresh_token'		=>$this->refresh_token,		          //app入口地址
			);
			$data['_aop_signature']=$this->Sign($data); 
			return json_decode($this->Curl($getTokenUrl,$data));
		}else{
			$getTokenUrl="{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/system.oauth2/getToken/{$this->appKey}";
			$data =array(
				'grant_type'		=>'authorization_code',	              //授权类型
				'need_refresh_token'=>'true',				              //是否需要返回长效token
				'client_id'			=>$this->appKey,				      //app唯一标示
				'client_secret'		=>$this->appSecret,			          //app密钥
				'redirect_uri'		=>$this->redirectUrl,			      //app入口地址
				'code'				=>$_SESSION['code'],	              //bug
			);
			$result  = json_decode($this->Curl($getTokenUrl,$data));
		}
	}
	
	/*
	 * 获取订单留言
	 */
	function getOrderMessage($starttime, $endtime){
	    global $ali_user;
        // echo $ali_user;exit;
	    $currentpage   = 1;                                                            //当前页码 默认为一
	    $aliorder_obj  = new AliOderMessageModel();                                    //message数据库处理类
	    $roundtimes    = 1;
	    $pageindex     = 1;
	    while (($roundtimes++)<=100){                                                   //一次最多抓50次
	       echo "\ncurrent page number : $pageindex\n";
	       $data	= array(                                                           //参数列表
    	            'access_token'	=>$this->access_token,
    	            'currentPage'	=>$pageindex,
    	            'pageSize'		=>ALI_PAGESIZE,
    	            'startTime'	    =>$starttime,
    	            'endTime'	    =>$endtime,
    	           );//print_r($data);
	        $url		= "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.queryOrderMsgList/{$this->appKey}";
    	    $result     = $this->Curl($url,$data);
    	    $List		= json_decode($result,true);
//             print_r($List);exit();
    	    if ($List === FALSE) {                                                     //返回数据个格式错误 则跳出重来 <防止网络故障>
    	    	writeLog(ALIREPLYERR, '返回数据格式错误 --- '.$result."\n".var_export($data));
    	    	continue;
    	    }
            
            if(isset($List['error_code'])){                                            //返回报错信息
                writeLog(ALI_LOGPATH, '返回数据格式错误 --- '.$result."\n".var_export($data,TRUE));
                continue;
            }
    	    
    	    $data  = array();
    	    $msg_list  = $List['msgList'];
    	    if (empty($msg_list)){                                                     //抓取的消息为空 则说明该时间段的数据已经抓取完成了 则跳出
    	        break;
    	    }
    	    foreach ($msg_list as $msgval){
                $data['message_id'] = $msgval['id'];
                $exists = $aliorder_obj->checkIfExistsByMsgId($data['message_id']);
                if ($exists) {                                                          // 改消息已经抓取过了 <防止重复抓取>
                    echo "message has exists! --- ID : $data[message_id]\n";
                    continue;
                }
                $data['senderid']       = $msgval['senderLoginId'];                     // 发送人id
                $data['orderid']        = $msgval['orderId'];                           // 订单号
                $data['receiverid']     = $msgval['receiverLoginId'];                   // 接收人id
                $data['recievername']   = $msgval['receiverName'];                      // 接收人名字
                $data['sendername']     = $msgval['senderName'];                        // 发送人名称
//                 echo "sender => ", $data['senderid'],"\n";
                if ( $this->isWorker($data['senderid']) ) {                                   
                    $data['role']       = 0;                                            //0表示工作人员回复的
                } else {
                    $data['role']       = 1;                                            //1表示客户回复的
                }
//                 print_r($data);continue;
                $data['orderurl']       = $msgval['orderUrl'];                          // 订单地址
                $data['content']        = $msgval['content'];                           // 留言内容
                $data['piclink']        = $msgval['fileUrl'];                           // 图片文件内容
                $data['createtimestr']  = $msgval['gmtCreate'];                         // 留言生成时间
                $data['createtimestamp']= aliTranslateTime($msgval['gmtCreate']);       //转换时间戳
                $data['addtime']        = time();                                       // 抓单时间
                $data['havefile']       = $msgval['haveFile'];                          // 是否有附件
                $data['isread']         = $msgval['read'];                              // 是否已经
//                 echo 'role => ',$data['role'], "+\n";  
                if ($data['role'] == 1) {                                               //客户留言则分配文件夹 工作人员回复不分配文件夹
                    $data['fieldid']            = $this->getFiledId($data['senderid'], $data['receiverid']);         //获得分类id信息
                } else {
                    $data['fieldid']    = 0;
                }
//                 echo $data['fieldid'], "\n\n";continue;
                $orderDetail            = $this->fetchOrderdetail($data['orderid']);
//                 print_r($orderDetail);exit;
                if (isset($orderDetail['frozenStatus']) && $orderDetail['frozenStatus']=='IN_FROZEN') {         //冻结中订单
                	$data['orderstatus']   = strtoupper($orderDetail['frozenStatus']);
                } elseif( isset($orderDetail['issueStatus']) && $orderDetail['issueStatus']=='IN_ISSUE') {
                    $data['orderstatus']   = strtoupper($orderDetail['issueStatus']);
                } elseif (isset($orderDetail['orderStatus'])) {
                	$data['orderstatus']   = strtoupper($orderDetail['orderStatus']);
                } else {
                    $data['orderstatus']   = '';
                }
                $insert_result = $aliorder_obj->insertNewRecords($data);                // 存入数据库
                
                if ($insert_result == FALSE) {                                          // 插入失败 写日志
                    writeLog(ALIREPLYERR, AliOderMessageModel::$errMsg);
                    continue;
                } else {
                    echo 'add success !  ', $data['message_id'], "\n";
                }
    	    }
    	    $pageindex++;  
	        //$roundtimes++;
	    }
	    
	    unset($List);
	    return TRUE;
	}
	
	/*
	 * 获取站内
	 */
	public function getSiteMessage($starttime, $endtime){
	    global $ali_user;
	    $currentpage   = 1;                                                            //当前页码 默认为一
	    $aliorder_obj  = new AliOderMessageModel();                                    //message数据库处理类
	    $roundtimes    = 1;
	    $pageindex     = 1;
	    while (($roundtimes++)<=50){                                                   //一次最多抓50次
	       echo "\ncurrent page number : $pageindex\n";
	       $data	= array(                                                           //参数列表
    	            'access_token'	=>$this->access_token,
    	            'currentPage'	=>$pageindex,
    	            'pageSize'		=>ALI_PAGESIZE,
    	            'startTime'	    =>$starttime,
    	            'endTime'	    =>$endtime,
    	           );
	        $url		= "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.queryMessageList/{$this->appKey}";
    	    $result     = $this->Curl($url,$data);
    	    $List		= json_decode($result,true);
            
    	    if ($List === FALSE) {                                                        //返回数据个格式错误 则跳出重来 <防止网络故障>
    	    	writeLog(ALI_LOGPATH, '返回数据格式错误 --- '.$result."\n".var_export($data,TRUE));
    	    	continue;
    	    }
            
            if(isset($List['error_code'])){                                               //返回报错信息
                writeLog(ALI_LOGPATH, '返回数据格式错误 --- '.$result."\n".var_export($data,TRUE));
    	    	continue;
            }
    	     // print_r($List); exit;
    	    
    	    $msg_list  = $List['msgList'];
    	    if (empty($msg_list)){                                                        //抓取的消息为空 则说明该时间段的数据已经抓取完成了 则跳出
    	        break;
    	    }
    	    foreach ($msg_list as $msgval){
    	        $data  = array();
                $data['message_id'] = $msgval['id'];
                $exists = $aliorder_obj->checkIfExistsBySiteMsgId($data['message_id']);
                if ($exists) {                                                            // 改消息已经抓取过了 <防止重复抓取>
                    echo "message has exists! --- ID : $data[message_id]\n";
                    continue;
                }
                $data['relationId']         = $msgval['relationId'];                      // 关系id
                $data['senderid']           = $msgval['senderLoginId'];                   // 发送人登陆id
//                 echo "sender => ", $data['senderid'],"\n";
    	        if ( $this->isWorker($data['senderid']) ) {                                   
                    $data['role']       = 0;                                              //0表示工作人员回复的
                } else {
                    $data['role']       = 1;                                              //1表示客户回复的
                }
//                 print_r($data);continue;
                $data['sendername']         = $msgval['senderName'];                      // 发送人名称
                $data['receiverid']         = $msgval['receiverLoginId'];                 // 接收人登陆id
                $data['receivername']       = $msgval['receiverName'];                    // 接收名称
                $data['productUrl']         = $msgval['productUrl'];                      // 产品url
                $data['productName']         = $msgval['productName'];                    // 产品名称
                $data['productId']          = $msgval['productId'];                       // 产品id
                $data['typeId']             = $msgval['typeId'];                          // 类型id
                $data['addtime']            = time();                                     // 抓单时间
                $data['orderUrl']           = trim($msgval['orderUrl']);                  // 订单url
                $data['orderId']            = trim($msgval['orderId']);                   // 订单id
                $data['gmtCreate']          = $msgval['gmtCreate'];                       // message产生时间
                $data['createtimestamp']    = aliTranslateTime($msgval['gmtCreate']);     //转换时间戳
                $data['content']            = $msgval['content'];                         // 消息内容
                $data['isRead']             = $msgval['isRead'];                          // 是否已读取
                $data['haveFile']           = $msgval['haveFile'];                        // 是否有附件
                $data['fileUrl']            = $msgval['fileUrl'];                         // 附件地址
                if ($data['role'] == 1) {                                                 //客户留言则分配文件夹 工作人员回复不分配文件夹
                	$data['fieldid']            = $this->getFiledId($data['senderid'], $data['receiverid']);         //获得分类id信息
                } else {
                    $data['fieldid']    = 0; 
                }
//                 echo $data['fieldid'],"\n\n";continue;
                if (!empty($data['orderId']) && !empty($data['orderUrl'])) {                                    //该留言与订单绑定
                    $orderDetail   = $this->fetchOrderdetail($data['orderId']);
    //                 print_r($orderDetail);exit;
                    if (isset($orderDetail['frozenStatus']) && $orderDetail['frozenStatus']=='IN_FROZEN') {         //冻结中订单
                    	$data['orderstatus']   = strtoupper($orderDetail['frozenStatus']);
                    } elseif( isset($orderDetail['issueStatus']) && $orderDetail['issueStatus']=='IN_ISSUE') {
                        $data['orderstatus']   = strtoupper($orderDetail['issueStatus']);
                    } elseif (isset($orderDetail['orderStatus'])) {
                    	$data['orderstatus']   = strtoupper($orderDetail['orderStatus']);
                    } else {
                        $data['orderstatus']   = '';
                    }
                } 
                
                $insert_result = $aliorder_obj->insertNewSiteMsgRecords($data);                                   // 存入数据库
                if ($insert_result == FALSE) {                                                                    // 插入失败 写日志
                    writeLog(ALI_LOGPATH, AliOderMessageModel::$errMsg);
                    continue;
                } else {
                    echo 'add success !  ', $data['message_id'], "\n";
                }
    	    }
    	    $pageindex++;  
	        //$roundtimes++;
	    }
	    
	    unset($List);
	    return TRUE;
	}
    
    /*
     * 根据发送者id来判断放在哪个文件夹
     * return fieldid
     */
    public function getFiledId($senderid, $account){
        $firstletter    = substr($senderid, 0, 1);
        $sql    = "select id from msg_messagecategory where platform=2 and ebay_account='$account' and rules like '%$firstletter%'";
//         echo $sql, "\n"; 
        $query  = mysql_query($sql);
        $row    = mysql_fetch_assoc($query);
        return empty($row) ?  0 : $row['id'];
    }
    
    /*
     * 回复订单留言
     */
    function replyOrderMessage($orderid, $content){ //return true;
        $data    = array(                               //参数列表
                    'access_token'  =>$this->access_token,
                    'orderId'       =>$orderid,
                    'content'       =>$content,
                   );//print_r($data);exit;
       $url        = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.addOrderMessage/{$this->appKey}";
       $result     = $this->Curl($url,$data);
       
       if ($result != '0') {                                       //处理失败
           self::$errCode   = 401;
           self::$errMsg    = var_export($data, TRUE);
           //writeLog(ALIREPLYERR, '返回数据格式错误 --- '.$result."\n".var_export($data, TRUE));
           return FALSE;
       } else {                                                             //处理成功
           return TRUE;
       }
    }

    /*
     * 回复  站内信
     */
    function replySiteMessage($buyreid, $content){ //return TRUE;
        $content    = htmlentities($content);
        $data    = array(                                                   //参数列表
                    'access_token'  =>$this->access_token,
                    'buyerId'       =>$buyreid,
                    'content'       =>$content,
                   );
//        print_r($data);exit;
       $url        = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.addMessage/{$this->appKey}";
       $result     = $this->Curl($url,$data);
       // $result     = json_decode($result,true);
       if ($result!= '0') {                                       //处理失败
           slef::$errCode   = 401;
           self::$errMsg    = var_export($data, TRUE);
           writeLog(ALIREPLYERR, '返回数据格式错误 --- '.$result."\n".var_export($data, TRUE));
           return FALSE;
       } else {                                                             //处理成功
           return TRUE;
       }
    }
    
    /*
     * 根据订单号获取订单 详情
     */
    function fetchOrderdetail($orderId){
    	global $ali_user;
    	$data    = array(                               //参数列表
    	'access_token'  =>$this->access_token,
    	'orderId'   	=>$orderId,
    	);//print_r($data);
    	$url        = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.findOrderById/{$this->appKey}";
    	$result     = $this->Curl($url,$data);
    	$detail     = json_decode($result,true);
    	if ($result === FALSE) {						//返回不合法的数据
    		self::$errCode	= 401;
    		self::$errMsg	= $result;
    	}
    	return $detail;
    }
    
    /*
     * 获取订单列表
     */
    function fetchOrderList($starttime, $endtime){
        global $ali_user;
        $data    = array(                               //参数列表
                'access_token'  =>$this->access_token,
                'pageSize'   	=>50,
                'page'          => 1,
                'createDateStart'=>$starttime,
                'createDateEnd' => $endtime
                
        );
        $url        = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.findOrderListQuery/{$this->appKey}";
        $result     = $this->Curl($url,$data);
        $detail     = json_decode($result,true);
        if ($result === FALSE) {						//返回不合法的数据
            self::$errCode	= 401;
            self::$errMsg	= $result;
        }
        return $detail;
    }
    
    /*
     * 获取某个订单的某区间的订单留言
     */
    function getOrderMessageMin($starttime, $endtime, $orderId, $account, $bigestid){
        $returnData = array();
        $data	= array(                               //参数列表
                    'access_token'	=>$this->access_token,
                    'currentPage'	=>1,
                    'pageSize'		=>50,
                    'orderId'       => $orderId,
                    'startTime'	    =>$starttime,
                    'endTime'	    =>$endtime,
            );//print_r($data);exit;
        $url		= "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.queryOrderMsgList/{$this->appKey}";
        $result     = $this->Curl($url,$data);
        $List		= json_decode($result,true);//print_r($List);exit;
        if (FALSE !== $List && isset($List['msgList'])) {
        	foreach ($List['msgList'] as $ival){
        	    if ($ival['id'] >$bigestid) {
        	    	$returnData[]     = $ival;
        	    }
        	}
        }
        return $returnData;
    }
    
    /*
     * 获取某个人的站内信
    */
    function getSiteMessageMin($starttime, $endtime, $bigestId, $buyerid){
        $returnData = array();
        $data	= array(                               //参数列表
                'access_token'	=> $this->access_token,
                'currentPage'	=> 1,
                'pageSize'		=> 50,
                'buyerId'       => $buyerid,
                'startTime'	    => $starttime,
                'endTime'	    => $endtime,
        );//print_r($data);exit;
        $url		= "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.queryMessageList/{$this->appKey}";
        $result     = $this->Curl($url,$data);
        $List		= json_decode($result,true);//print_r($List);exit;
//         var_dump($List);
        if (FALSE !== $List && isset($List['msgList'])) {
            foreach ($List['msgList'] as $ival){
//                 echo $ival['id'],'-----',$bigestId;exit;
                if ($ival['id'] > $bigestId) {
                    $returnData[]     = $ival;
                }
            }
        }
        return $returnData;
    }
    
    /*
     * 判断是否为工作人员回复
     */
    public function isWorker($account){
    	return in_array($account, $this->accountslist) ? TRUE : FALSE;
    }
}
