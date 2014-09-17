<?php
/**
 * TransportStrategyAct
 * 
 * @package order2.valsun.cn
 * @author blog.anchen8.net
 * @copyright 2014
 * @version $Id$
 * @access public
 */
/**
 * TransportStrategyAct
 * 
 * @package order2.valsun.cn
 * @author blog.anchen8.net
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class TransportStrategyAct extends CheckAct{

	public function __construct(){
		parent::__construct();
        $this->perpage = 5;
	}

	/**
	 * 提供错误信息列表
	 * @return array
	 * @author lzx
	 */
	public function act_getTransportStrategyLists() {
	    $data = array();
	    $data['is_delete'] = array('$e'=>0);
        if(isset($_GET['accountId']) > 0){
            $data['id'] = array('$e'=>intval($_GET['accountId']));
        }   		
		$accountList =  M('Account')->getAccountList($data, $this->page, $this->perpage);
        //print_r(M('InterfaceTran')->key('id')->getAllCountryList());exit;
        //print_r(C('TRANSPORT_STRATEGY_CURRENCY_ARRAY'));exit;
        if(!empty($accountList)){
            $TSObject = M('TransportStrategy');
            $carrierlist = M('InterfaceTran')->key('id')->getCarrierList(2);//id=>array()
            $channelList = M('InterfaceTran')->key('id')->getChannelList();//id=>array()
            //print_r($channelList);exit;
            $countryList = M('InterfaceTran')->key('id')->getAllCountryList();
            $currencyList = C('TRANSPORT_STRATEGY_CURRENCY_ARRAY');
            foreach($accountList as $k=>$v){
                $constraintTypeList = $TSObject->getAccountConstraintTypeByAccountId($v['id']);
                $constraintTypeList = $constraintTypeList[0];
                $accountList[$k]['constraintType'] = $constraintTypeList;
                
                $constraintTypeStrArr = array();
                if(!empty($constraintTypeList)){
                    $constraintTypeStrArr['isSpecialSpuForce'] = $constraintTypeList['isSpecialSpuForce'] == 1?'允许特殊料号运输方式':'无视特殊料号运输方式';
                    $constraintTypeStrArr['isPlatOrAccountPri'] = $constraintTypeList['isPlatOrAccountPri'] == 1?'平台优先':'账号优先';
                }
                            
                $accountList[$k]['accountConstraintTypeStr'] = implode(',', array_filter($constraintTypeStrArr));//账号约束/优先级显示字符串
                $conditionTransportList = $TSObject->getConditionTransportByAccountId($v['id']);               
                if(!empty($conditionTransportList[0]['channelIdStr'])){
                    $conditionTransportList[0]['transportIdArr'] = explode(',', $conditionTransportList[0]['channelIdStr']);
                }
                $accountList[$k]['conditionTransport'] = $conditionTransportList[0];
                $tmpChannelNameArr = array();
                $returnTCArr = returnTCArrFromChannelIdArr(explode(',', $conditionTransportList[0]['channelIdStr']));
                //print_r($returnTCArr);
                foreach($returnTCArr as $tmpTransportId=>$tmpChannelIdArr){
                    foreach($tmpChannelIdArr as $tmpChannelId){
                        $tmpChannelNameArr[$carrierlist[$tmpTransportId]['carrierNameCn']][] = $channelList[$tmpChannelId]['channelName'];
                    }                    
                }
                //print_r($tmpChannelNameArr);exit;
                $accountList[$k]['conditionTransportStr'] = Tarr2Str($tmpChannelNameArr);//基础运输方式ids渠道ids对应名称字符串
                
                
                $conditionCountryList = $TSObject->getConditionCountryByAccountId($v['id']);//国家对应transportId的json字符串
                $countryTransportArr = explode('&', $conditionCountryList[0]['countryId_channelIdStr']);//每个数组是countryId和channelId的关系字符串
                if(!empty($countryTransportArr)){
                    $tmpConditionCountryArr = array();                    
                    foreach($countryTransportArr as $ctStr){//$ctStr 每个数组是countryId和transportId的关系字符串
                        $tmpArr = array();
                        $ctStrArr = explode(':', $ctStr);//$ctStrArr 0为countryIds，1为channelIds
                        $tmpCountryIdArr = array();
                        $tmpCountryNameArr = array();
                        $tmpTransportIdArr = array();
                        $tmpTransportNameArr = array();
                        if(!empty($ctStrArr[0]) && !empty($ctStrArr[1])){//$ctStrArr 0为countryIds，1为transportIds 都不为空
                            foreach(explode(',', $ctStrArr[0]) as $tmpCountryId){
                                $tmpCountryIdArr[] = $tmpCountryId;//国家id
                                $tmpCountryNameArr[] = $countryList[$tmpCountryId]['countryNameEn'];//国家英文名
                            }
                            $tmpArr['countryIdArr'] = $tmpCountryIdArr;
                            $tmpArr['countryNameArr'] = $tmpCountryNameArr;
                            foreach(explode(',', $ctStrArr[1]) as $tmpChannelId){
                                $tmpTransportIdArr[] = $tmpChannelId;//运输方式id
                                //$tmpTransportNameArr[] = $channelList[$tmpChannelId]['channelName'];//运输方式中文名
                            }
                            $tmpArr['transportIdArr'] = $tmpTransportIdArr;
                            $returnTCArr = returnTCArrFromChannelIdArr($tmpTransportIdArr);
                            $tmpChannelNameArr = array();
                            foreach($returnTCArr as $tmpTransportId=>$tmpChannelIdArr){
                                foreach($tmpChannelIdArr as $tmpChannelId){
                                    $tmpChannelNameArr[$carrierlist[$tmpTransportId]['carrierNameCn']][] = $channelList[$tmpChannelId]['channelName'];
                                }                    
                            }
                            $tmpArr['transportNameArr'] = Tarr2Str($tmpChannelNameArr);
                            $tmpArr['priority'] = $conditionCountryList[0]['priority'];
                        }
                        if(!empty($tmpArr)){
                            $tmpConditionCountryArr[] = $tmpArr;
                        }                       
                    }                    
                    $accountList[$k]['conditionCountry'] = $tmpConditionCountryArr;
                    $conditionCountryStr = '';
                    foreach($tmpConditionCountryArr as $v3){
                        $conditionCountryStr .= implode(',', $v3['countryNameArr']).':'.$v3['transportNameArr'].'&';
                    }
                    if($conditionCountryStr != ''){
                        $conditionCountryStr .= '优先级:'.$tmpConditionCountryArr[0]['priority'];
                    }                    
                    $accountList[$k]['conditionCountryStr'] = $conditionCountryStr;
                }
                
                
                $conditionCurrencyList = $TSObject->getConditionCurrencyByAccountId($v['id']);//币种对应transportId的json字符串
                $currencyTransportArr = json_decode($conditionCurrencyList[0]['currencyId_channelIdJson'], true);    
                if(!empty($currencyTransportArr) && is_array($currencyTransportArr)){
                    $currencyTransportIdStrArr = array();
                    foreach($currencyTransportArr as $currencyId=>$currencyTransportIdArr){
                        $tmpCurrencyStr = $currencyList[$currencyId]['CN'];
                        $tmpCurrencyTransportNameArr = array();
                        $returnTCArr = returnTCArrFromChannelIdArr($currencyTransportIdArr);
                        $tmpChannelNameArr = array();
                        foreach($returnTCArr as $tmpTransportId=>$tmpChannelIdArr){
                            foreach($tmpChannelIdArr as $tmpChannelId){
                                $tmpChannelNameArr[$carrierlist[$tmpTransportId]['carrierNameCn']][] = $channelList[$tmpChannelId]['channelName'];
                            }                    
                        }
                        $currencyTransportIdStrArr[] = $tmpCurrencyStr.'->'.Tarr2Str($tmpChannelNameArr);
                    }
                    $accountList[$k]['conditionCurrency'] = $currencyTransportArr;
                    $accountList[$k]['conditionCurrencyPriority'] = $conditionCurrencyList[0]['priority'];
                    $accountList[$k]['conditionCurrencyStr'] = implode('&', $currencyTransportIdStrArr).'&'.'优先级:'.$conditionCurrencyList[0]['priority'];                  
                }
                
                $conditionAmountList = $TSObject->getConditionAmountByAccountId($v['id']);//金额
                $conditionAmountList = $conditionAmountList[0];
                $accountList[$k]['conditionAmount'] = $conditionAmountList;
                if(!empty($conditionAmountList)){
                    $conditionStr1 = '';
                    $conditionStr2 = '';
                    if(!empty($conditionAmountList['symbolCondition1'])){
                        $conditionStr1 = "条件：".$conditionAmountList['symbolCondition1'].' '.$conditionAmountList['amountCondition1'];
                    }
                    if(!empty($conditionAmountList['symbolCondition2'])){
                        $conditionStr2 = "条件：".$conditionAmountList['symbolCondition2'].' '.$conditionAmountList['amountCondition2'];
                    }
                    $allowTypeStr = $conditionAmountList['allowType'] == 1?'满足一项':'满足所有';
                    $tmpTransportIdArr = explode(',', $conditionAmountList['channelIdStr']);
                    $tmpTransportNameArr = array();
                    $tmpTransportStr = '';
                    if(!empty($tmpTransportIdArr)){
                        $returnTCArr = returnTCArrFromChannelIdArr($tmpTransportIdArr);
                        $tmpChannelNameArr = array();
                        foreach($returnTCArr as $tmpTransportId=>$tmpChannelIdArr){
                            foreach($tmpChannelIdArr as $tmpChannelId){
                                $tmpChannelNameArr[$carrierlist[$tmpTransportId]['carrierNameCn']][] = $channelList[$tmpChannelId]['channelName'];
                            }                    
                        }
                        $tmpTransportStr = Tarr2Str($tmpChannelNameArr);
                        if($tmpTransportStr != ''){
                            $tmpTransportStr = '['.$tmpTransportStr.']';
                        }
                    }                   
                    $tmpPriorityStr = "(优先级：".$conditionAmountList['priority'].")";
                    $accountList[$k]['conditionAmountTransportIdArr'] = $tmpTransportIdArr;
                    $accountList[$k]['conditionAmountStr'] = implode(',', array($conditionStr1, $conditionStr2, $allowTypeStr, $tmpPriorityStr, $tmpTransportStr));//金额-约束
                }               
            }
        }        
        //print_r($accountList);exit;
        return $accountList;     
	}
	
	/**
	 * 添加或修改om_transport_strategy_account_constraint_type表记录（账号约束类型表（包含特殊料号强制类型和账号约束条件优先/订单指定优先））
	 * @return bool
	 * @author zqt
	 */
	public function  act_editConstraintType(){
		$platformId	= intval($_POST['platformId']);
		$accountId  = intval($_POST['accountId']);
        $isSpecialSpuForce	= intval($_POST['isSpecialSpuForce']);
		$isPlatOrAccountPri  = intval($_POST['isPlatOrAccountPri']);
        $isOn	= intval($_POST['isOn']);
        $flag = false;
        if($platformId <=0 || $accountId <=0 || $isSpecialSpuForce <=0 || $isPlatOrAccountPri <=0 || $isOn <=0){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $TSObject = M('TransportStrategy');
		$ctList = $TSObject->getAccountConstraintTypeByAccountId($accountId);
        $table = C('DB_PREFIX')."transport_strategy_account_constraint_type";
        if(empty($ctList)){//找不到记录是，插入
            $insertData = array();
            $insertData['platformId'] = $platformId;
            $insertData['accountId'] = $accountId;
            $insertData['isSpecialSpuForce'] = $isSpecialSpuForce;
            $insertData['isPlatOrAccountPri'] = $isPlatOrAccountPri;
            $insertData['isOn'] = $isOn;
            $flag = $TSObject->insertTSData($table, $insertData);
        }else{
            $updateData = array();
            $updateData['isSpecialSpuForce'] = $isSpecialSpuForce;
            $updateData['isPlatOrAccountPri'] = $isPlatOrAccountPri;
            $updateData['isOn'] = $isOn;
            $flag = $TSObject->updateByAccountId($table, $accountId, $updateData);
        }
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 添加或修改om_transport_strategy_account_basic_transport表记录（账号约束类型表（包含特殊料号强制类型和账号约束条件优先/订单指定优先））
	 * @return bool
	 * @author zqt
	 */
	public function  act_editConditionTransport(){
		$platformId	= intval($_POST['platformId']);
		$accountId  = intval($_POST['accountId']);
        $transportIdStr	= $_POST['transportIdStr'];
        $flag = false;
        if($platformId <=0 || $accountId <=0 || $transportIdStr == ''){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $TSObject = M('TransportStrategy');
		$ctList = $TSObject->getConditionTransportByAccountId($accountId);
        $table = C('DB_PREFIX')."transport_strategy_account_basic_transport";
        if(empty($ctList)){//找不到记录是，插入
            $insertData = array();
            $insertData['platformId'] = $platformId;
            $insertData['accountId'] = $accountId;
            $insertData['channelIdStr'] = $transportIdStr;
            $flag = $TSObject->insertTSData($table, $insertData);
        }else{
            $updateData = array();
            $updateData['channelIdStr'] = $transportIdStr;
            $flag = $TSObject->updateByAccountId($table, $accountId, $updateData);
        }
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 添加或修改om_transport_strategy_condition_amount表记录（账号约束类型表（包含特殊料号强制类型和账号约束条件优先/订单指定优先））
	 * @return bool
	 * @author zqt
	 */
	public function  act_editConditionAmount(){
		$platformId	= intval($_POST['platformId']);
		$accountId  = intval($_POST['accountId']);
        $allowType = intval($_POST['allowType']);
        $symbolCondition1 = $_POST['symbolCondition1'];
        $symbolCondition2 = $_POST['symbolCondition2'];
        $amountCondition1 = $_POST['amountCondition1'];
        $amountCondition2 = $_POST['amountCondition2'];
        $tmpTransportStr = $_POST['tmpTransportStr'];
        $priority = intval($_POST['priority']);
        $flag = false;
        if($platformId <=0 || $accountId <=0 || $allowType <=0 || $priority <=0){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        if($tmpTransportStr == ''){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $TSObject = M('TransportStrategy');
		$ctList = $TSObject->getConditionAmountByAccountId($accountId);
        //var_dump($ctList);exit;
        $table = C('DB_PREFIX')."transport_strategy_condition_amount";
        if(empty($ctList)){//找不到记录是，插入
            $insertData = array();
            $insertData['platformId'] = $platformId;
            $insertData['accountId'] = $accountId;
            $insertData['allowType'] = $allowType;
            $insertData['symbolCondition1'] = $symbolCondition1;
            $insertData['symbolCondition2'] = $symbolCondition2;
            $insertData['amountCondition1'] = $amountCondition1;
            $insertData['amountCondition2'] = $amountCondition2;
            $insertData['priority'] = $priority;
            $insertData['channelIdStr'] = $tmpTransportStr;
            //var_dump($insertData);exit;
            $flag = $TSObject->insertTSData($table, $insertData);
        }else{
            $updateData = array();
            $updateData['allowType'] = $allowType;
            $updateData['symbolCondition1'] = $symbolCondition1;
            $updateData['symbolCondition2'] = $symbolCondition2;
            $updateData['amountCondition1'] = $amountCondition1;
            $updateData['amountCondition2'] = $amountCondition2;
            $updateData['priority'] = $priority;
            $updateData['channelIdStr'] = $tmpTransportStr;
            $flag = $TSObject->updateByAccountId($table, $accountId, $updateData);
        }
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 添加或修改om_transport_strategy_condition_country表记录（账号约束类型表（包含特殊料号强制类型和账号约束条件优先/订单指定优先））
	 * @return bool
	 * @author zqt
	 */
	public function  act_editConditionCountry(){
		$platformId	= intval($_POST['platformId']);
		$accountId  = intval($_POST['accountId']);        
        $countryTransportPriority = intval($_POST['countryTransportPriority']);
        $countryTransportArr = json_decode($_POST['tmpArr1'], true);
        if($platformId <=0 || $accountId <=0 || $countryTransportPriority <= 0){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        if(!is_array($countryTransportArr) || empty($countryTransportArr)){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        //print_r($countryTransportArr);exit;
        $countryList = M('InterfaceTran')->getAllCountryList();
        $countryKVList = array();
        foreach($countryList as $value){
            $countryKVList[$value['id']] = $value['countryNameEn'];
        }
        $conditonArr = array();//总数组
        $allCoutryNameArr = array();
        foreach($countryTransportArr as $countryTransportIdArr){
            $tmpCountryNameStr = $countryTransportIdArr[0];
            $tmpTransportIdArr = $countryTransportIdArr[1];
            if(empty($tmpCountryNameStr) || empty($tmpTransportIdArr)){
                self::$errMsg[10040] = get_promptmsg(10040);
                return false;
            }else{
                $tmpCountryNameArr = explode(',', $tmpCountryNameStr);
                $tmpCountryIdArr = array();
                foreach($tmpCountryNameArr as $tmpCountryName){
                    $allCoutryNameArr[] = $tmpCountryName;
                    $tmpCountryId = array_search($tmpCountryName, $countryKVList);
                    if($tmpCountryId == false){
                        self::$errMsg[10002] = get_promptmsg(10002, $tmpCountryName);
                        return false;
                    }else{
                        $tmpCountryIdArr[] = $tmpCountryId;
                    }
                }
                $tmpCountryIdStr = implode(',', $tmpCountryIdArr);
                $tmpTransportIdStr = implode(',', $tmpTransportIdArr);                
                $conditonArr[] = $tmpCountryIdStr.':'.$tmpTransportIdStr;
            }
        }
        if(count($allCoutryNameArr) != count(array_unique($allCoutryNameArr))){
            self::$errMsg[10132] = get_promptmsg(10132, $tmpCountryName);
            return false;
        }
        if(empty($conditonArr)){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $conditonStr = implode('&', $conditonArr);
        //print_r($conditonStr);exit;
        $flag = false;        
        $TSObject = M('TransportStrategy');
		$ctList = $TSObject->getConditionCountryByAccountId($accountId);
        //var_dump($ctList);exit;
        $table = C('DB_PREFIX')."transport_strategy_condition_country";
        if(empty($ctList)){//找不到记录是，插入
            $insertData = array();
            $insertData['platformId'] = $platformId;
            $insertData['accountId'] = $accountId;
            $insertData['priority'] = $countryTransportPriority;
            $insertData['countryId_channelIdStr'] = $conditonStr;
            //var_dump($insertData);exit;
            $flag = $TSObject->insertTSData($table, $insertData);
        }else{
            $updateData = array();
            $updateData['countryId_channelIdStr'] = $conditonStr;
            $updateData['priority'] = $countryTransportPriority;
            $flag = $TSObject->updateByAccountId($table, $accountId, $updateData);
        }
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 添加或修改om_transport_strategy_condition_country表记录（账号约束类型表（包含特殊料号强制类型和账号约束条件优先/订单指定优先））
	 * @return bool
	 * @author zqt
	 */
	public function  act_editConditionCurrency(){
		$platformId	= intval($_POST['platformId']);
		$accountId  = intval($_POST['accountId']);        
        $currencyTransportPriority = intval($_POST['currencyTransportPriority']);
        $currencyTransportJsonArr = json_decode($_POST['tmpArr1'], true);
        //print_r($platformId.'   '.$accountId.'     '.$currencyTransportPriority.'    '.$currencyTransportJsonArr);exit;
        if($platformId <=0 || $accountId <=0 || $currencyTransportPriority <= 0){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        if(!is_array($currencyTransportJsonArr) || empty($currencyTransportJsonArr)){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $currencyTransportNewArr = array();
        foreach($currencyTransportJsonArr as $value){
            $currencyTransportNewArr[$value[0]] = $value[1];
        }
        //print_r($currencyTransportNewArr);exit;
        if(empty($currencyTransportNewArr)){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $currencyTransportNewJson = json_encode($currencyTransportNewArr);
        $flag = false;        
        $TSObject = M('TransportStrategy');
		$ctList = $TSObject->getConditionCurrencyByAccountId($accountId);
        //var_dump($ctList);exit;
        $table = C('DB_PREFIX')."transport_strategy_condition_currency";
        if(empty($ctList)){//找不到记录是，插入
            $insertData = array();
            $insertData['platformId'] = $platformId;
            $insertData['accountId'] = $accountId;
            $insertData['priority'] = $currencyTransportPriority;
            $insertData['currencyId_channelIdJson'] = $currencyTransportNewJson;
            //var_dump($insertData);exit;
            $flag = $TSObject->insertTSData($table, $insertData);
        }else{
            $updateData = array();
            $updateData['currencyId_channelIdJson'] = $currencyTransportNewJson;
            $updateData['priority'] = $currencyTransportPriority;
            $flag = $TSObject->updateByAccountId($table, $accountId, $updateData);
        }
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 根据前台传来的type，del指定accountId的记录
	 * @return bool
	 * @author zqt
	 */
	public function  act_delCondition(){
		$type	= $_POST['type'];
		$accountId  = intval($_POST['accountId']);
        //echo $type.'   '.$accountId;exit; 
        if($accountId <= 0){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $tableName = '';
        if($type == 'conditionTransport'){
            $tableName = 'om_transport_strategy_account_basic_transport';
        }
        if($type == 'conditionAmount'){
            $tableName = 'om_transport_strategy_condition_amount';
        }
        if($type == 'conditionCountry'){
            $tableName = 'om_transport_strategy_condition_country';
        }
        if($type == 'conditionCurrency'){
            $tableName = 'om_transport_strategy_condition_currency';
        }
        if($tableName == ''){
            self::$errMsg[10040] = get_promptmsg(10040);
            return false;
        }
        $updateData = array();
        $updateData['is_delete'] = 1;
        $TSObject = M('TransportStrategy');
        $flag = $TSObject->updateByAccountId($tableName, $accountId, $updateData);
        if($flag){
            self::$errMsg[200] = get_promptmsg(200, '提交');
        }else{
            self::$errMsg[10131] = get_promptmsg(10131);
        }
		return $flag;
	}
    
    /**
	 * 函数内部调用，根据accountId取得该accountId下设置的基础运输方式id数组
     * @param  accountId 账号Id
	 * @return array 该账号下设置的基础运输方式id
	 * @author zqt
	 */
	function getConditionTransportByAccountId($accountId){
	   $returnTransportIdArr = array();//要返回的运输方式id数组
       $ctList = M('TransportStrategy')->getConditionTransportByAccountId($accountId);
       if(!empty($ctList[0]['channelIdStr'])){
          foreach(explode(',', $ctList[0]['channelIdStr']) as $transportId){
            $returnTransportIdArr[] = $transportId;
          }
       }
       return $returnTransportIdArr;
	}
    
    /**
	 * 函数内部调用，根据accountId取得该accountId下设置的金额运输方式id数组
     * @param  accountId 账号Id
     * @param  amount 订单总金额（包括平台上分配的运费）
	 * @return array 该账号下设置的基础运输方式id及对应优先级的数组,不满足或其他，则返回空的array
	 * @author zqt
	 */
	function getConditionAmountByAccountId($accountId, $amount){
	   $returnTransportIdArr = array();//要返回的运输方式id数组
       $caList = M('TransportStrategy')->getConditionAmountByAccountId($accountId);
       if(!empty($caList)){
          //条件1:
          $conditon1 = false;//默认条件一不满足
          if($caList[0]['symbolCondition1'] == '>'){
             if($amount > $caList[0]['amountCondition1']){
                $conditon1 = true;
             }
          }elseif($caList[0]['symbolCondition1'] == '>='){
             if($amount >= $caList[0]['amountCondition1']){
                $conditon1 = true;
             }
          }elseif($caList[0]['symbolCondition1'] == '<'){
             if($amount < $caList[0]['amountCondition1']){
                $conditon1 = true;
             }
          }elseif($caList[0]['symbolCondition1'] == '<='){
             if($amount <= $caList[0]['amountCondition1']){
                $conditon1 = true;
             }
          }
          //条件2：
          $conditon2 = false;//默认条件二不满足
          if($caList[0]['symbolCondition2'] == '>'){
             if($amount > $caList[0]['amountCondition2']){
                $conditon2 = true;
             }
          }elseif($caList[0]['symbolCondition2'] == '>='){
             if($amount >= $caList[0]['amountCondition2']){
                $conditon2 = true;
             }
          }elseif($caList[0]['symbolCondition2'] == '<'){
             if($amount < $caList[0]['amountCondition2']){
                $conditon2 = true;
             }
          }elseif($caList[0]['symbolCondition2'] == '<='){
             if($amount <= $caList[0]['amountCondition2']){
                $conditon2 = true;
             }
          }
          $finalCondition = $caList[0]['allowType'] == 1?$conditon1||$conditon2:$conditon1&&$conditon2;//allowType == 1为满足其中一项即可，2为满足所有条件
          if($finalCondition){
             if(!empty($caList[0]['channelIdStr'])){
                foreach(explode(',', $caList[0]['channelIdStr']) as $transportId){
                    $returnTransportIdArr['transportIdArr'][] = $transportId;
                }
                $returnTransportIdArr['priority'] = $caList[0]['priority'];
             }
          }
       }
       return $returnTransportIdArr;
	}
    
    /**
	 * 函数内部调用，根据accountId取得该accountId下设置的国家运输方式id数组
     * @param  accountId 账号Id
     * @param  countryId 国家id（国家及id统一由运输方式管理那里获取）
	 * @return array 该账号下设置的基础运输方式id及对应优先级的数组,不满足或其他，则返回空的array
	 * @author zqt
	 */
	function getConditionCountryByAccountId($accountId, $countryId){
	   $returnTransportIdArr = array();//要返回的运输方式id数组
       $ccList = M('TransportStrategy')->getConditionCountryByAccountId($accountId);
       if(!empty($ccList)){
          $ctKVArr = array();//国家-运输方式id的数组
          $perCountryIdTransportIdStrArr = explode('&', $ccList[0]['countryId_channelIdStr']);
          foreach($perCountryIdTransportIdStrArr as $perCountryIdTransportIdStr){//单条countryId,transportId对应字符串
             $ctArr = explode(':', $perCountryIdTransportIdStr);
             $countryIdStr = $ctArr[0];//国家id的字符串
             $transportIdStr = $ctArr[1];//transportId的字符串
             $countryIdArr = explode(',', $countryIdStr);//国家id数组
             $transportIdArr = explode(',', $transportIdStr);//运输方式id数组
             foreach($countryIdArr as $value){
                $ctKVArr[$value] = $transportIdArr;
             }
          }
          if(!empty($ctKVArr[$countryId])){
            $returnTransportIdArr['transportIdArr'] = $ctKVArr[$countryId];
            $returnTransportIdArr['priority'] = $ccList[0]['priority'];
          }          
       }
       return $returnTransportIdArr;
	}
    
    /**
	 * 函数内部调用，根据accountId取得该accountId下设置的币种运输方式id数组
     * @param  accountId 账号Id
     * @param  countryId 币种id（币种中文名称/英文名称对应的id在配置文件中控制）
	 * @return array 该账号下设置的基础运输方式id及对应优先级的数组,不满足或其他，则返回空的array
	 * @author zqt
	 */
	function getConditionCurrencyByAccountId($accountId, $currencyId){
	   $returnTransportIdArr = array();//要返回的运输方式id数组
       $ccList = M('TransportStrategy')->getConditionCurrencyByAccountId($accountId);
       if(!empty($ccList[0]['currencyId_channelIdJson'])){
          $ctArr = json_decode($ccList[0]['currencyId_channelIdJson'], true);
          if(is_array($ctArr) && !empty($ctArr) && !empty($ctArr[$currencyId])){
             $returnTransportIdArr['transportIdArr'] = $ctArr[$currencyId];
             $returnTransportIdArr['priority'] = $ccList[0]['priority'];
          }          
       }
       return $returnTransportIdArr;
	}
    
    /**
	 * 函数内部调用，根据accountId取得过滤该账号限制后的transportIdArr(为渠道id)
     * @param  array $orderData 订单大数组
	 * @return array 经过账号策略限制后输出的transportIdArr，返回为空表示
	 * @author zqt
	 */
	function accountConditionByOrderData($orderData){
	   $returnArr = false;//默认为false,标识该账号不需要或者未启用过滤
       $countryList = M('InterfaceTran')->key('countryNameEn')->getAllCountryList();//国家id,name对应数组
       $currencyList = C('TRANSPORT_STRATEGY_CURRENCY_ARRAY');//币种数组
       $currencyIdENArr = array();//币种id对应英文名数组
       foreach($currencyList as $key=>$value){
          $currencyIdENArr[$key] = $value['EN'];
       }
       $accountId = $orderData['order']['accountId'];
       $amount = $orderData['order']['actualTotal'];//订单实际金额
       $countryName = $orderData['orderUserInfo']['countryName'];
       $currency = $orderData['order']['currency'];
       $constraintTypeList = M('TransportStrategy')->getAccountConstraintTypeByAccountId($accountId);
       if($constraintTypeList[0]['isOn'] == 1){//该账号有并且启用了
           M('orderLog')->orderOperatorLog('no sql', '该账号策略生效了', $orderData['order']['id']);
           $priorityTransportArr = array();
           $amountTranArr = $this->getConditionAmountByAccountId($accountId, $amount);
           M('orderLog')->orderOperatorLog('no sql', '金额约束条件返回的渠道id为：'.json_encode($amountTranArr), $orderData['order']['id']);
           $countryTranArr = $this->getConditionCountryByAccountId($accountId, $countryList[$countryName]['id']);
           M('orderLog')->orderOperatorLog('no sql', '国家约束条件返回的渠道id为：'.json_encode($countryTranArr), $orderData['order']['id']);
           $currencyTranArr = $this->getConditionCurrencyByAccountId($accountId, array_search($currency, $currencyIdENArr));
           M('orderLog')->orderOperatorLog('no sql', '币种约束条件返回的渠道id为：'.json_encode($currencyTranArr), $orderData['order']['id']);
           if(!empty($amountTranArr)){//这里如果priority相同的话，数组会丢失，所以 3个约束条件中的priority不能相同
             $priorityTransportArr[$amountTranArr['priority']] = $amountTranArr;
           }
           if(!empty($countryTranArr)){
             $priorityTransportArr[$countryTranArr['priority']] = $countryTranArr;
           }
           if(!empty($currencyTranArr)){
             $priorityTransportArr[$currencyTranArr['priority']] = $currencyTranArr;
           }
           if(!empty($priorityTransportArr)){
              ksort($priorityTransportArr);//对数组进行优先级排序
              M('orderLog')->orderOperatorLog('no sql', '按优先级排序后的列表为'.json_encode($priorityTransportArr), $orderData['order']['id']);
           }           
           $conditionTransportIdArr = $this->getConditionTransportByAccountId($accountId);//该账号基础运输方式，优先级最低
           if(!empty($conditionTransportIdArr)){
              $priorityTransportArr[]['transportIdArr'] = $conditionTransportIdArr;
              M('orderLog')->orderOperatorLog('no sql', '该账号的基础运输方式渠道id为：'.json_encode($conditionTransportIdArr), $orderData['order']['id']);
           }
           if(!empty($priorityTransportArr)){                         
              $tmpArr1 = array_shift($priorityTransportArr);//将优先级高点的先取出 = array_shift($priorityTransportArr);//将优先级最高先取出
              $tmpArr1 = $tmpArr1['transportIdArr'];
              M('orderLog')->orderOperatorLog('no sql', '取出优先级最高的渠道id：'.json_encode($tmpArr1), $orderData['order']['id']);
              while(!empty($priorityTransportArr)){
                 M('orderLog')->orderOperatorLog('no sql', '约束条件策略大于2个，进入循环取交集', $orderData['order']['id']);
                 $tmpArr2 = array_shift($priorityTransportArr);//将优先级低点的先取出    
                 $tmpArr2 = $tmpArr2['transportIdArr'];
                 M('orderLog')->orderOperatorLog('no sql', '取出优先级次高的渠道id：'.json_encode($tmpArr2), $orderData['order']['id']);
                 $intersectTmpArr = array_intersect($tmpArr1, $tmpArr2);//取交集                 
                 $tmpArr1 = empty($intersectTmpArr)?$tmpArr1:$intersectTmpArr;//有交集时取交集，无交集时取优先级最高的，遍历
                 M('orderLog')->orderOperatorLog('no sql', '取得前两个集合的交集(无交集取优先级高的)：'.json_encode($tmpArr1), $orderData['order']['id']);
                 M('orderLog')->orderOperatorLog('no sql', '将交集赋值给最高优先级的渠道id集合，进入下次循环', $orderData['order']['id']);
              }
              $returnArr = $tmpArr1;
           }           
       }
       M('orderLog')->orderOperatorLog('no sql', '账号约束条件（基础，金额，国家，币种）最终选出的渠道id为：'.json_encode($returnArr), $orderData['order']['id']);
       return $returnArr;
	}
    
    
}
?>