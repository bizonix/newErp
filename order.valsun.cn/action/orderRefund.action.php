<?php


/*
 * 通用action
 * ADD BY zqt 2013.9.5
 */
class OrderRefundAct extends Auth {
	static $errCode = 0;
	static $errMsg  = '';


	/**
	 * 取得指定iostore记录
	 */
	function act_getRecordList($tName, $field, $where) { //表名，field，WHERE
        $orderObj  = new OrderRefundModel();
		$list = $orderObj->getTNameList($tName, $field, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = OrderRefundModel :: $errCode;
			self :: $errMsg  = OrderRefundModel :: $errMsg;
			return false;
		}
	}
	
	/**
	 * 取得指定数量
	 */
	function act_getRecordNums($where='') { //表名，field，WHERE
        //退款数量
		if(!$where){
			$where = " WHERE is_delete=0 ";
		}
        $accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId IN ('.join(",", $platformsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}
		
		$num = OrderRefundModel::getOrderRefundNums($where);
		self :: $errCode = OrderRefundModel :: $errCode;
		self :: $errMsg  = OrderRefundModel :: $errMsg;
		if ($num === false) {
			return false;
		} else {
			return $num;
		}
	}
    
	/**
	 * 取得指定数量
	 */
	function act_getRefundList($where='') { //表名，field，WHERE
        //退款数量
		if(!$where){
			$where = " WHERE is_delete=0 ";
		}
        $accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId IN ('.join(",", $platformsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
		    $where .= " AND 1=2 ";
		}
		
		$data = OrderRefundModel::getOrderRefundList2($where);
		self :: $errCode = OrderRefundModel :: $errCode;
		self :: $errMsg  = OrderRefundModel :: $errMsg;
		if (!$data) {
			return array();
		} else {
			return $data;
		}
	}
	
   	/*
	 * 取得指定iostore记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = OmAvailableModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
            foreach($list as $key => $v) {
                $list[$key]['addTime'] = Date('Y-m-d h:i',$v['addTime']);
                $list[$key]['updateTime'] = Date('Y-m-d h:i:s',$v['updateTime']);
                //$list[$key]['refundType'] = ($v['refundType'] == '1') ? '全部退款' : '部分退款';
            }
			return $list;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg  = OmAvailableModel :: $errMsg;
			return false;
		}
	}
    
   	function act_getTNameCount($tName, $where) {
		$ret = OmAvailableModel :: getTNameCount($tName, $where);
		if ($ret !== false) {  
			return $ret;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}



    /**
     * 获取退款信息
     */
   	function act_getRefundInfo() {
        
        $id = isset($_POST['orderId']) ? trim($_POST['orderId']) : '';  
        if($id == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';
            return false;
        }      
        $table = " `om_order_refund` a , `om_order_refund_detail` b ";
        $field = " a.*,b.sku,b.amount,b.actualPrice ";
        $where = " WHERE a.id = '$id' AND a.id = b.orderRefundId ";        
        //$orderObj  = new OrderRefundModel();       
        $refundInfo = OrderRefundModel::getTNameList($table, $field, $where);
        if(!isset($refundInfo)) {
            self::$errCode  = 2;
            self::$errMsg   = '结果为空！'; 
            return false; 
        }
		    
        $refundInfo[0]['addTime'] = date('Y-m-d h:i:s', $refundInfo[0]['addTime']);         
        self::$errCode  = OrderRefundModel::$errCode;
        self::$errMsg   = OrderRefundModel::$errMsg;            
        return $refundInfo; 
	}
    
	/**
     * 申请CASE单据
     */
   	function act_applyCaseRefund() {  

		$id 		= isset($_POST['orderId']) ? trim($_POST['orderId']) : '';
		$ostatus 	= isset($_POST['orderStatus']) ? trim($_POST['orderStatus']) : '';
		$otype 		= isset($_POST['orderType']) ? trim($_POST['orderType']) : '';
		
        if($id == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';
            return FALSE; 
        }
        
        /*$refundedInfo = self::act_getRefundedSum($id);
        //var_dump($refundedInfo);
        //print_r($refundedInfo);
        $totalSum    = $refundedInfo['totalSum'];
        $refundedSum = $refundedInfo['refundSum'];
          //var_dump($refundedSum);
        if(($refundedSum != 0) && ($refundedSum >= $totalSum)) {
            self::$errCode  = 002;
            self::$errMsg   = '该订单累计申请退款金额已达订单金额，不可再申请！';            
            return FALSE; 
        }*/
        
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		$orderInfo = OrderRefundModel::getOrderInfo($tableName, $id);
        //$orderObj  = new OrderRefundModel();
        //$orderInfo = OrderRefundModel::getTNameList($table, $field, $where);
        /*if(!$orderInfo) {
           $orderInfo['refundedSum'] = $refundedSum;
        }*/
		//var_dump($orderInfo); exit;
        self::$errCode  = OrderRefundModel::$errCode;
        self::$errMsg   = OrderRefundModel::$errMsg;
        return $orderInfo;
    }
	
     /**
     * 申请退款
     */
   	function act_applyRefund() {  

		$id 		= isset($_POST['orderId']) ? trim($_POST['orderId']) : '';
		$ostatus 	= isset($_POST['orderStatus']) ? trim($_POST['orderStatus']) : '';
		$otype 		= isset($_POST['orderType']) ? trim($_POST['orderType']) : '';
		
        if($id == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';
            return FALSE; 
        }
        
        $refundedInfo = self::act_getRefundedSum($id);
        //var_dump($refundedInfo);
        //print_r($refundedInfo);
        $totalSum    = $refundedInfo['totalSum'];
        $refundedSum = $refundedInfo['refundSum'];
          //var_dump($refundedSum);
        if(($refundedSum != 0) && ($refundedSum >= $totalSum)) {
            self::$errCode  = 002;
            self::$errMsg   = '该订单累计申请退款金额已达订单金额，不可再申请！';            
            return FALSE; 
        }
        
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		$orderInfo = OrderRefundModel::getOrderInfo($tableName, $id);
        //$orderObj  = new OrderRefundModel();
        //$orderInfo = OrderRefundModel::getTNameList($table, $field, $where);
        if(!$orderInfo) {
           $orderInfo['refundedSum'] = $refundedSum;
        }
		//var_dump($orderInfo); exit;
        self::$errCode  = OrderRefundModel::$errCode;
        self::$errMsg   = OrderRefundModel::$errMsg;
        return $orderInfo;
    }
    
    /**
     * 获取指定订单下已申请退款的金额
     */
   	function act_getRefundedSum($id) {
   	    
        $time  = time();
        $table = ' `om_order_refund` ';
        $field = ' `totalSum`, `refundSum` ';
        $where = ' WHERE omOrderId = '.$id.' AND status != 2 ';        
        $orderObj = new OrderRefundModel();       
        $result   = $orderObj->getTNameList($table, $field, $where);
        if( count($result) == 0 ) {
            return array('totalSum' => 0, 'refundSum' => 0);            
        }
       
        $totalSum = isset($result[0]['totalSum']) ? $result[0]['totalSum'] : 0;
        $refundedSum = 0; 
        foreach($result as $refund) {
            $refundedSum += $refund['refundSum'];
        }
                  
        self::$errCode  = 0;
        self::$errMsg   = ''; 
        return array('totalSum' => $totalSum, 'refundSum' => $refundedSum);        
    } 
    
    
    /**
     * 写入退款单
     */
   	function act_addRefundInfo() {     
   	    //print_r($_POST);
        //echo '00000000000000000000';
		global $dbConn;
		$dbcon = $dbConn;
   	    $refundInfo = isset($_POST['orderobj']) ? $_POST['orderobj'] : '';
		$orderType  = isset($refundInfo['orderType']) ? $refundInfo['orderType'] : 1; 
        if($refundInfo == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';            
            return false; 
        }
        $id         = isset($refundInfo['id']) ? $refundInfo['id'] : '';
        if($id == '') {
            self::$errCode  = 2;
            self::$errMsg   = '参数Id为空！';            
            return false; 
        }
		if($orderType == 1){  
			//$ppRtnInfo = $this->act_curlRefund($refundInfo);

			$httpParsedResponseAr = $this->act_curlRefund($refundInfo);
			//var_dump($httpParsedResponseAr);exit;
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]) ) {

				$dataArr = $refundInfo;
				$operator = $_SESSION["userCnName"];

				$time = time();

				$sql = "insert into ebay_refund_log (order_id,trade_id,ebay_account,buyer_id,refund_reson,refund_type,operator,refund_time,paypal_account,money,currency,country) values ('{$dataArr['id']}','{$dataArr['PayPalPaymentId']}','{$dataArr['accountId']}','{$dataArr['platformUsername']}','{$dataArr['reason']}','{$dataArr['refundType']}','{$operator}','{$time}','{$dataArr['paypalAccount']}','{$dataArr['refundSum']}','{$dataArr['currency']}','{$dataArr['countryName']}')";
				//echo $sql."\n";
				$dbcon->execute($sql); //记录操作信息

				$sql = "insert into ebay_refund_log_detail (order_id,sku,amount) values ";//记录退款单个sku 信息
				$skuArr = $dataArr['skuArr'];
				foreach($skuArr as $key=>$sku){
					$sql .= "('{$dataArr['id']}','{$sku['sku']}','{$sku['amount']}')";
					if($key < count($skuArr)-1){
						$sql .= ",";
					}
				}

				if($dbcon->execute($sql)){
					$rtnMsg = "订单编号: ".$dataArr['id']."退款成功";
				}else{
					$rtnMsg = $sql;

				}
				$sql = " UPDATE `om_unshipped_order` SET orderStatus=2000 , orderType=1004 WHERE id={$dataArr['id']}";
				$dbConn->execute($sql);
				$sql = " UPDATE `om_shipped_order` SET orderStatus=660 , orderType=710 WHERE id={$dataArr['id']}";
				$dbConn->execute($sql);
				self::$errCode  = 200;
			}else{
				self::$errCode = 404;
				$rtnMsg = "订单编号: ".$dataArr['id']."退款失败". urldecode($httpParsedResponseAr['L_LONGMESSAGE0']).'错误代码：'.$httpParsedResponseAr['L_ERRORCODE0'];
				self::$errMsg = $rtnMsg;
				return false;
			}
		}
        //$orderObj = new OrderRefundModel();  
        BaseModel::begin();
		$sysUserId = $_SESSION['sysUserId'];
        $time = time();
        $data = array();
        $data['omOrderId']        = $refundInfo['id'];
        $data['recordNumber']     = $refundInfo['recordNumber'];
        $data['sellerAccountId']  = $refundInfo['accountId'];        
        $data['totalSum']         = $refundInfo['totalSum'];
        $data['refundSum']        = $refundInfo['refundSum'];
        $data['refundType']       = ($refundInfo['refundType'] == 'Full') ? 1 : 0;
        $data['platformUsername'] = $refundInfo['platformUsername'];
        $data['platformId']       = $refundInfo['platformId'];
        $data['platform']         = $refundInfo['platform'];
        $data['transId']          = $refundInfo['PayPalPaymentId'];
        $data['paypalAccount']    = $refundInfo['paypalAccount'];
        $data['pass']             = $refundInfo['pass'];
        $data['signature']        = $refundInfo['signature'];
        $data['reason']           = $refundInfo['reason'];  
        $data['note']             = $refundInfo['note'];  
        $data['currency']         = $refundInfo['currency'];
		$data['orderType']        = $orderType;
        $data['addTime']          = $time;
		$data['creatorId']        = $sysUserId;
        $tableRefund              = 'om_order_refund';
        $setField                 = array2sql($data); 
          
  		$insertId = OrderRefundModel::addTNameRow($tableRefund, $setField);
		if($insertId !== FALSE) {
            foreach($refundInfo['skuArr'] as $key => $orderInfo){
                $refundDetail = array();
                $refundDetail['orderRefundId'] = $insertId;
                $refundDetail['sku']           = $orderInfo['sku'];
                $refundDetail['amount']        = $orderInfo['amount'];
                $refundDetail['actualPrice']   = $orderInfo['actualPrice'];
                $refundDetail['addTime']       = $time;   
              
                $tableRefundDetail = 'om_order_refund_detail';
                $setFields = array2sql($refundDetail);
                $ret2 = OrderRefundModel::addTNameRow($tableRefundDetail, $setFields);
               	if($ret2 !== FALSE) {      	    
                    //
                } else {                  
                    self :: $errCode = OrderRefundModel::$errCode;
                    self :: $errMsg  = OrderRefundModel::$errMsg; 
                    BaseModel::rollback();           
                    return false;
                }                          
            }
 		} else { 		 
            self :: $errCode = OrderRefundModel::$errCode;
            self :: $errMsg  = OrderRefundModel::$errMsg;
           BaseModel::rollback();            
            return false;
		}
        //echo '88888888888888888';
        BaseModel::commit();
        //self::$errMsg   = '生成单据成功！'; 
        self::$errMsg   = $rtnMsg; 
        return true;
	} 
      
    
    /**
     * 取消退款
     */
   	function act_cancelRefund() {
   	    //echo '1111111111111111111111';
        $id = isset($_POST['orderId']) ? trim($_POST['orderId']) : '';     
        if($id == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';             
            return false; 
        } 
        
        $time  = time();
        $table = " `om_order_refund` ";
        $field = " status = '2',updateTime = '$time'  ";
        $where = " WHERE id = '$id' ";        
        $orderObj = new OrderRefundModel();       
        $result   = $orderObj->updateTNameRow($table, $field, $where);
        if(!$result) {
            self::$errCode  = 2;
            self::$errMsg   = '更新订单状态出错！';            
            return false; 
        }          
        self::$errCode  = 200;
        self::$errMsg   = '取消退款成功！'; 
        return true;    
    }
	
    /**
     * 更新退款记录
     */
   	function act_updateRefundInfo($id) {   	   
   	    $refundId = isset($id) ? $id : '';      
        if($refundId == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数非法！';            
            return false; 
        }        
      
        $time  = time();
        $table = " `om_order_refund` ";
        $field = " status = '1', updateTime = '$time' ";
        $where = " WHERE id = '$refundId' ";              
        $orderObj = new OrderRefundModel();             
        $result   = $orderObj->updateTNameRow($table, $field, $where);
        if(!$result) {
            self::$errCode  = 3;
            self::$errMsg   = '更新订单状态出错！';            
            return false; 
        }         
        self::$errCode  = 0;
        self::$errMsg   = ''; 
        return true;
	}      

	/**
	 * 退款操作
	 */


	//发送请求给paypal ......
	function act_curlRefund($dataArr){
		
		$paypal_account     = trim($dataArr['paypalAccount']);
		$paypal_passwd      = trim($dataArr['pass']);
		$signature          = trim($dataArr['signature']);
		$account			= trim($dataArr['totalSum']);
		$transactionID		= urlencode(trim($dataArr['PayPalPaymentId']));
		//$transactionID	= urlencode('8UB78354D73053524'); //for test.........
		$refundType			= urlencode(trim($dataArr['refundType']));
		$currencyID			= urlencode($dataArr['currency']);
		$amount				= trim($dataArr['refundSum']);
		$memo               = $dataArr['note'];
		$nvpStr             = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID&NOTE=$memo";
		
		if($refundType == 'Partial'){
			$nvpStr=$nvpStr."&AMT=$amount";
		}
		//var_dump($paypal_account,$paypal_passwd,$signature,'RefundTransaction', $nvpStr , $account);
		$ppRtnInfo = $this->PPHttpPost($paypal_account,$paypal_passwd,$signature,'RefundTransaction', $nvpStr , $account);
		return $ppRtnInfo;
		//print_r($ppRtnInfo);
	}

	function act_excuteRefund(){
		global $dbConn;
    	$orderId = $_POST['orderId'];
        if($orderId == '') {
            self::$errCode  = 1;
            self::$errMsg   = '参数refund Id 有误！';            
            return false; 
        }
		$OrderRefundList = OrderRefundModel::getOrderRefundList("*", " WHERE id = {$orderId} AND is_delete = 0");
		//var_dump($OrderRefundList);
		if(!$OrderRefundList){
			self :: $errCode = "001";
			self :: $errMsg = "获取信息失败！";
            return false;	
		}
		if($OrderRefundList[0]['orderType'] == 2){
			//手动退款
			$rtn = OrderRefundModel :: updateOrderRefund(" status = '1', updateTime = ".time(), " WHERE id = {$orderId} AND is_delete = 0");

			$sql = " UPDATE `om_unshipped_order` SET orderStatus=2000 , orderType=1004 WHERE id={$orderId}";
			$dbConn->execute($sql);
			$sql = " UPDATE `om_shipped_order` SET orderStatus=660 , orderType=710 WHERE id={$orderId}";
			$dbConn->execute($sql);
			self :: $errCode = OrderRefundModel :: $errCode;
			self :: $errMsg = OrderRefundModel :: $errMsg;
            return $rtn;
		}else if($OrderRefundList[0]['orderType'] == 1){
			//PayPal退款
			$paypal_account     = trim($dataArr['paypalAccount']);
			$paypal_passwd      = trim($dataArr['pass']);
			$signature          = trim($dataArr['signature']);     
			$account			= trim($dataArr['totalSum']);
			$transactionID		= urlencode(trim($dataArr['PayPalPaymentId']));
			//$transactionID	= urlencode('8UB78354D73053524'); //for test.........
			$refundType			= urlencode(trim($dataArr['refundType']));
			$currencyID			= urlencode($dataArr['currency']);
			$amount				= trim($dataArr['refundSum']);
			$memo               = $dataArr['note'];
			//$operator           = $_SESSION['truename']; //操作人
			
			//////////////////For test///////////////////////
			/*
			$paypal_account = 'keyhere_api1.gmail.com';//trim($dataArr['paypalAccount']);
			$paypal_passwd  = 'Z2MM4MR7JYNXJJTU';      //trim($dataArr['pass']);
			$signature      = 'A.KeI6NrmIaMvyjNhuLwy2pLV0zPAQ20fUMuWkw30BkqbQWQM8mSQ2MX';//trim($dataArr['signature']); 
			*/
			/*
			$paypal_account = 'chenyishan77_api1.gmail.com';//trim($dataArr['paypalAccount']);
			$paypal_passwd  = 'DYWRHBMGN3FE5RH2';      //trim($dataArr['pass']);
			$signature      = 'AZevjjIseNWGR7SfrsQRQeCXRhikAy2qVHVUJOAviaPPhxzAYrKpsYkl';//trim($dataArr['signature']); 
			
			$account		= '4.19';              //trim($dataArr['totalSum']);
			$transactionID	= '46P33414V65055144'; //urlencode(trim($dataArr['PayPalPaymentId']));
			$refundType		= 'Full';              // urlencode(trim($dataArr['refundType']));
			$currencyID		= 'USD';               //urlencode($dataArr['currency']);
			$amount			= '1';                 //trim($dataArr['refundSum']);
			$memo           = 'Refund for a customer'; //$dataArr['note'];    
			*/
			/////////////////////////////////////////
			
			if($refundType == "Full"){
				$money_amount   = $account;
			}else{
				$money_amount   = $amount;
			}
			$nvpStr             = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID&NOTE=$memo";
			
			//$nvpStr="&TRANSACTIONID=$transaction_id&REFUNDTYPE=$refundType&CURRENCYCODE=$currency&NOTE=$memo";
			if($refundType == 'Partial'){
				$nvpStr=$nvpStr."&AMT=$amount";
			}
			//return self::act_updateRefundInfo($orderId);
			$rtn = OrderRefundModel :: updateOrderRefund(" status = '1', updateTime = ".time(), " WHERE id = {$orderId} AND is_delete = 0");
			self :: $errCode = OrderRefundModel :: $errCode;
			self :: $errMsg = OrderRefundModel :: $errMsg;
            return $rtn;
		
			$httpParsedResponseAr = self::PPHttpPost($paypal_account,$paypal_passwd,$signature,'RefundTransaction', $nvpStr , $account);
			
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				self :: $errCode = 200;
				self :: $errMsg = '请求Paypal退款成功！';
				return true;           
			} else  {
				self :: $errCode = 4;
				self :: $errMsg = '请求Paypal退款失败！';
				return false;       	
			}	
		}
 	}
	
    /**
     * 封装paypal退款的接口
     */
    function PPHttpPost($paypal_account,$paypal_passwd,$signature,$methodName_, $nvpStr_ ,$account) {
    
    	//global $environment,$dbConn;    
    	$API_UserName	= $paypal_account;
    	$API_Password	= $paypal_passwd;
    	$API_Signature	= $signature;    
    	
    	//Set up your API credentials, PayPal end point, and API version.
    	$API_UserName  = urlencode($API_UserName);
    	$API_Password  = urlencode($API_Password);
    	$API_Signature = urlencode($API_Signature);
    	$API_Endpoint  = "https://api-3t.paypal.com/nvp";
    	//$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp"; //for test    
        //define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');    
    	//$version = urlencode('51.0');
    	$version = urlencode('65.1');
    	//Set the curl parameters.
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    	curl_setopt($ch, CURLOPT_VERBOSE, 1);     
    	//Turn off the server and peer verification (TrustManager Concept).
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);     
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    	curl_setopt($ch, CURLOPT_POST, 1);
     
    	// Set the API operation, version, and API signature in the request.
    	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
     
    	// Set the request as a POST FIELD for curl.
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);     
    	// Get response from the server.
    	$httpResponse = curl_exec($ch);    
    	if(!$httpResponse) {
    		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
    	}
    	// Extract the response details.
    	$httpResponseAr = explode("&", $httpResponse);     
    	$httpParsedResponseAr = array();
    	foreach ($httpResponseAr as $i => $value) {
    		$tmpAr = explode("=", $value);
    		if(sizeof($tmpAr) > 1) {
    			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
    		}
    	}    
    	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
    		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    	}     
    	return $httpParsedResponseAr;    
    }
}
?>
