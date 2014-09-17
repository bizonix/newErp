<?php
/*
 * 订单相关通用函数
 * @add by lzx ,date 20140528
 */

/**
 * 根据平台id获取平台名称
 * @param int $id 平台编号
 * @return string
 * @author lzx
 */
function get_platnamebyid($id){
    $data = A('Platform')->act_getPlatformById($id);
	return isset($data['platform']) ? $data['platform'] : '';
}

/**
 * 根据账号id获取账号名称
 * @param int $id 账号id
 * @return string
 * @author lzx
 */
function get_accountnamebyid($id){
	$data   = A('Account')->act_getAccountById($id);
	return isset($data['account']) ? $data['account'] : '';
}

/**
 * 根据账号id获取平台id
 * @param int $id 账号id
 * @return id
 * @author yxd
 * */

function get_platFromidbyaccountid($id){
	$data            = A('Account')->act_getPlatformidByAccountid($id);
	$platformId      = $data[0]['platformId'];
	return $platformId;
}
/**
 * 根据账号id获取平台名称
 * @param int $id 账号id
 * @return string
 * @author yxd
 * */

function get_platnamebyaccountid($id){
	$platformId      = get_platFromidbyaccountid($id);
	$platformName    = get_platnamebyid($platformId);
	return $platformName;
}

/**
 * 根据渠道id获取运输方式
 * @param int $id 运输方式id
 * @return string
 * @author lzx
 */
function get_carriernamebyid($id){
	$carrierlist    = M('InterfaceTran')->key('id')->getCarrierList(2);
	$chenellists     = M('InterfaceTran')->getChannelList();
	foreach ($chenellists as $value){
		$chenellist[$value['id']]    = $value['channelName'];
	}
	if(strpos("$id",",")){
		$idarr          = explode(",", $id);
		$returnData     = returnTCArrFromChannelIdArr($idarr);
		 $returnDataNa      = array();
	    $carrnaArr         = array();
	    foreach($returnData as $key=>$chenel){
	    	$carrnaArr         = array();
	    	foreach($chenel as $value){
	    		$carrnaArr[]  =  $chenellist[$value];
	    	}
	    	$returnDataNa[$carrierlist[$key]['carrierNameCn']]    = $carrnaArr;
	    }
		$returnDataNa     = Tarr2Str($returnDataNa,":",",","&nbsp;&nbsp;&nbsp;");
		return $returnDataNa;
	}else{
	     $returnData       = returnTCArrFromChannelIdArr(array($id));
	    $returnDataNa      = array();
	   
	    foreach($returnData as $key=>$chenel){
	    	$carrnaArr         = array();
	    	foreach($chenel as $value){
	    		$carrnaArr[]  = $chenellist[$value];
	    	}
	    	$returnDataNa[$carrierlist[$key]['carrierNameCn']]    = $carrnaArr;
	    }
		$returnDataNa     = Tarr2Str($returnDataNa,":",",","&nbsp;&nbsp;&nbsp;");
		return $returnDataNa;
	}
	
}

/**
 * @param $id
 * @return mixed
 * 根据运输方式的ID获取运输方式的中文名称
 */
function getCarrierNameByCarrierId($id){
    $carrierlist = M('InterfaceTran')->key('id')->getCarrierList(2);
    return $carrierlist[$id]['carrierNameCn'];
}

/**
 * 获取所有的运输列表
 * @return mixed
 */
function get_carrierList(){
    $carrierlist = M('InterfaceTran')->getCarrierList(2);
    return $carrierlist;
}
/**
 * 根据运输方式获取运输方式id
 * @param string $name 运输方式
 * @return string
 * @author lzx
 */
function get_carrieridbyname($name){
    $carrierlist = M('InterfaceTran')->key('carrierNameCn')->getCarrierList(2);
	return $carrierlist[$name]['id'];
	
}

/**
 * 根据状态type获取状态类型名称
 * @param int $tid 状态码code
 * @return string
 * @author lzx
 */
function get_groupmenunamebyid($cid){
    $groupmenu = A('StatusMenu')->act_getGroupMenuByCode($cid);
	return $groupmenu['groupName'];
}

/**
 * 根据包材id获取名称
 * @param int $id 包材id
 * @return string
 * @author lzx
 */
function get_maternamebyid($id){
    $materlist = M('InterfacePc')->key('id')->getMaterList();
	return isset($materlist[$id]['pmName']) ? $materlist[$id]['pmName'] : '无';
}

function get_materList(){
    $materlist = M('InterfacePc')->key('id')->getMaterList();
    return $materlist;
}

/**
 * 根据包材名称获取id
 * @param string $name 包材名称
 * @return string
 * @author lzx
 */
function get_materidbyname($name){
    $materlist = M('InterfacePc')->key('pmName')->getMaterList();
	return isset($materlist[$name]['id']) ? $materlist[$name]['id'] : 0;
}

/**
 * 根据SKU信息获取每日销售情况
 * @param string $sku
 * @return string
 * @author lzx
 */
function get_skudailystatus($sku){
	$dailystatus = MC("SELECT * FROM ".C('DB_PREFIX')."sku_daily_status WHERE sku='{$sku}'", 900);
	return isset($dailystatus[0]) ? $dailystatus[0] : false;
}

function get_useracountpower($uid){
	$accounts = array();
	$competence = A('UserCompetence')->act_getCompetenceByUserId($uid);
	$lists = json_decode($competence['visible_platform_account'], true);
	foreach ($lists AS $platform=>$_account){
		$accounts = array_merge($accounts, $_account);
	}
	return $accounts;
}
function get_userplatacountpower($uid){
	$competence = A('UserCompetence')->act_getCompetenceByUserId($uid);
	return json_decode($competence['visible_platform_account'], true);
}

/**
 * 根据SKU获取对应的已订购数量
 * @param string $sku
 * @return int 已购数量
 * @author lzx
 */
function get_reservecount($sku){
	return M('InterfacePurchase')->getReserveCount($sku);
}

/**
 * 根据SKU|虚拟SKU信息获取相关组合情况
 * @param string $sku
 * @return array
 * @author lzx
 */
function get_orderskulist($sku){
	$orderskus = array();
	$skulists = M('InterfacePc')->getSkuinfo($sku);
	if (empty($skulists)){
		return false;
	}
	$combineskus = array();
	if ($skulists['isCombine']==1){
		if (strpos($sku, '*')!==false){
			list($a, $skupic) = explode('*', $sku);
		}else{
			$skupic = $sku;
		}
		$combineskus['spu']		= $sku;  //为特别设置spu 可以能存在不能找到图片bug
		$combineskus['sku']	   	= $sku;
		$combineskus['skupic'] 	= $skupic;
		$combineskus['amount'] 	= 1;
	}
	foreach ($skulists['skuInfo'] AS $_sku=>$skuinfo){
		$orderskus[$_sku]['spu'] 		 = $skuinfo['skuDetail']['spu'];
		$orderskus[$_sku]['sku'] 		 = $_sku;
		$orderskus[$_sku]['skupic'] 	 = $_sku;
		$orderskus[$_sku]['amount'] 	 = $skuinfo['amount'];
		$orderskus[$_sku]['goodsCost'] 	 = $skuinfo['skuDetail']['goodsCost'];
		$orderskus[$_sku]['purchaseId']  = $skuinfo['skuDetail']['purchaseId'];
		$orderskus[$_sku]['goodsStatus'] = $skuinfo['skuDetail']['goodsStatus'];
	}
	return array('realsku'=>$orderskus, 'combinesku'=>$combineskus, 'isCombine'=>$skulists['isCombine']);
}

/**
 * 跟进itemid获取URL地址
 * @param string $itemid
 * @return string
 * @author lzx
 */
function get_itemurl($itemid, $pid){
	switch ($pid){
		case 1  : $url="http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item={$itemid}"; break;
		case 2  : $url="http://www.aliexpress.com/item/New-1mm-Silver-Metallic-Caviar-Beads-Studs-Nail-Art-Glitter-Nail-Decoration-13229/{$itemid}.html"; break;
		case 11 : $url="http://www.amazon.com/gp/product/{$itemid}"; break;
		default : $url="#";
	}
	return $url;
}

/**
 * 根据真实的SKU（通过检测是否有料号转换）
 * @param string $sku
 * @return string
 * @author zqt
 */
function get_newSkuFromSkuConversion($sku){
    $skuConversionKVArr = M('interfacePc')->getSkuConversionArr();//获取所有审核通过的料号转换数组，K为old_sku,V为new_sku
    if(isset($skuConversionKVArr[$sku])){
	   $sku = $skuConversionKVArr[$sku];
	}
    return $sku;
}

/**
 * 标记已处理
 * @param int omorderid  int status
 * @return bool 
 * @author yxd
 */
function do_operated($omOrderid,$statusId){
	$data      = array('omOrderId'=>$omOrderid,'statusId'=>$statusId,'addTime'=>time,'userId'=>get_userid());
	$result    = M('OrderOperated')->insertData($data);
	return $result;
}

/**
 * 判断是否已处理
 * @param int omorderid  int status
 * @return bool 
 * @author yxd
 */
function get_operateByAS($omOrderid,$statusId){
	$operatedInfo    = M('OrderOperated')->get_operateByAS($omOrderid,$statusId);
	if(count($operatedInfo)>=1){
		return true;
	}else{
		return false;
	}
}
/**
 * 根据SKU判断该SKU是否是组合料号
 * @param string $sku
 * @return bool
 * @author zqt
 */
function get_isCombineSku($sku){
	//获取料号下详细信息
    $flag = false;//默认不是组合料号
    $skuInfoArr = M("InterfacePc")->getSkuInfo($sku);//，返回结果维度请自己去开放系统测试
	$isCombine = $skuInfoArr['isCombine'];//是否是组合料号标识,0为单料号，1为组合料号
	if ($isCombine == 1){//如果是单料号
        $flag = true;
	}
    return $flag;
}

/**
 * 根据SKU获取料号下详细信息
 * @param string $sku
 * @return array
 * @author zqt
 */
function get_realskuinfo($sku){
	//获取料号下详细信息
    $skuInfoArr = M("InterfacePc")->getSkuInfo($sku);//，返回结果维度请自己去开放系统测试
	$isCombine = $skuInfoArr['isCombine'];//是否是组合料号标识,0为单料号，1为组合料号
	if ($isCombine != 1){//如果是单料号
        $sku = $this->get_newSkuFromSkuConversion($sku);//获取料号转换后最新的SKU
		return array($sku=>1);
	}else{//虚拟料号
	    $results = array();
    	foreach($skuInfoArr['skuInfo'] AS $skuinfo){
			$sku 	= $skuinfo['skuDetail']['sku'];
			$amount = $skuinfo['amount'];
    		$sku = $this->get_newSkuFromSkuConversion($sku);//获取料号转换后最新的SKU
    		if(isset($results[trim($sku)])){
    			$results[trim($sku)] += $amount;
    		}else{
    			$results[trim($sku)] = $amount;		
    		}
    	}
    	return $results;
	}	
}

/**
 * 根据真实SKU料号的信息,如果是虚拟料号或者不存在，则返回false
 * @param string $sku
 * @return array
 * @author zqt
 */
function get_trueSkuInfo($sku){
	//获取料号下详细信息
    $skuInfoArr = M("InterfacePc")->getSkuInfo($sku);//，返回结果维度请自己去开放系统测试
	$isCombine = $skuInfoArr['isCombine'];//是否是组合料号标识,0为单料号，1为组合料号
	if ($isCombine == 1){//如果是组合料号        
		return false;
	}else{//真实料号
	    return !empty($skuInfoArr['skuInfo'][$sku]['skuDetail'])?$skuInfoArr['skuInfo'][$sku]['skuDetail']:false;
	}	
}

/**
 * 根据真实SKU料号和仓库位置获取库存信息
 * @param string $sku
 * @return array
 * @author zqt
 */
function get_skuStock($sku, $storeid){
	//获取料号下详细信息
    $skuStore = M("InterfaceWh")->getSkuStock(array($storeid=>array($sku)));
	return !empty($skuStore) ?  $skuStore[$sku][$storeid] : false;
}

/**
 * 获取运输方式快递或者非快递
 * @param $isExpressDeliverys
 * @return string
 * @author dy
 */

function get_isExpressDeliveryName($isExpressDeliverys){
    $str = '';
    if($isExpressDeliverys == 1){
        $str = '快递';
    }elseif($isExpressDeliverys == 0){
        $str = '非快递';
    }else{
        $str = '未知类型';
    }

    return $str;
}

/**
 * @param $id
 * @return string
 * 获取一级状态的名称，根据自定义数组
 */
function get_orderStatusName($id){
    $statusName = '未知状态';
    if($statusNameList = M('statusMenu')->getOrderStatusName($id)){
        $statusName = $statusNameList[0]['statusName'];
    }
    return $statusName;
}

/**
 * @param $orderTypeId
 * @return string
 * 获取二级状态的名称，根据自定义数组
 */
function get_orderTypeName($orderTypeId){
    $orderType   = '未知类型';
    if($orderTypeList = M('statusMenu')->getOrderStatusName($orderTypeId)){
        $orderType = $orderTypeList[0]['statusName'];
    }
    return $orderType;
}

/**
 * 通过渠道ID获取对应的运输方式的名称和对应渠道的名称
 * 组装并且展示
 * @param $usefulChannel
 * @return string
 */
function get_usefulCarrierChannel($usefulChannel,$char){
    $msg         = '';
    $channelName = '';
    $channelAll  = M("InterfaceTran")->key('id')->getChannelList();
    $carrierAll  = M('InterfaceTran')->key('id')->getCarrierList(2);
    if(!empty($usefulChannel)){
        $usefulChannelArray  = explode(',',$usefulChannel);
        $usefulCarrierChannelArray = returnTCArrFromChannelIdArr($usefulChannelArray);
        foreach($usefulCarrierChannelArray as $carrierId=>$channelArray){
            $channelName .= $carrierAll[$carrierId]['carrierNameCn'].': ';
            if(is_array($channelArray)){
                foreach($channelArray as $channelId){
                    $channelName.= $channelAll[$channelId]['channelName'].',';
                }
            }
            $channelName = trim($channelName,',');
            $channelName .= $char;
        }
    }else{
        $channelName = '无可用渠道ID';
    }
    $channelName = trim($channelName,$char);
    return $channelName.'&nbsp;&nbsp;'.$msg;
}

/**
 * @param $usefulChannel
 * @return string
 * 可用运输方式获取
 */
function get_usefulChannel($usefulChannel){
    $channelName    = '';
    $msg            = '';
    $restChannelAll = array();
    //var_dump($usefulChannel);
    if(!empty($usefulChannel)){
        $usefulChannel  = explode(',',$usefulChannel);
        //$info           = returnTCArrFromChannelIdArr($usefulChannel);  //没必要重复调用接口
        $channelAll     = M("InterfaceTran")->getChannelList();
        if(is_array($channelAll)){
            foreach($channelAll as $channelList){
                $id = $channelList['id'];
                $restChannelAll[$id] = $channelList;
            }
        }
        asort($restChannelAll);
        foreach($usefulChannel as $channelId){
            if(isset($restChannelAll[$channelId])){
                $channelName .= $restChannelAll[$channelId]['channelName'].',';
            }else{
                $msg .= $channelId.',';
            }
        }
        $channelName = trim($channelName,',');
        $msg         = trim($msg,',');

    }else{
        $channelName = '无可用渠道ID';
    }
    return $channelName.'&nbsp;&nbsp;'.$msg;
}

/**
 * @param $platformName
 * @return int
 * 通过传入平台名称获取对应的平台ID
 */
function getPlatformIdFromName($platformName){
    $platformList  = array();
    $platformId    = '';
    if(!empty($platformName)){
        $platformName  = strtolower($platformName);
        $platformLists = M('platform')->getPlatformLists();
        if(!empty($platformLists)){
            foreach($platformLists as $list){
                $platform = strtolower($list['platform']);
                $platformList[$platform] = $list['id'];
            }
        }
        if(isset($platformList[$platformName])){
            $platformId = $platformList[$platformName];
        }
    }
    return $platformId;
}

/**
 * @param $accountName
 * @return string
 * 通过传入帐号名称获取对应的帐号ID
 */
function getAccountIdFromName($accountName){
    $accountList  = array();
    $accountId    = '';
    if(!empty($accountName)){
        $accountLists = M('Account')->getAccountAll();
        if(!empty($accountLists)){
            foreach($accountLists as $list){
                $account               = strtolower($list['account']);
                $accountList[$account] = $list['id'];
            }
        }
        $accountName = strtolower($accountName);
        if(isset($accountList[$accountName])){
            $accountId = $accountList[$accountName];
        }
    }

    return $accountId;
}

/**
 * 推送超大订单队列给采购系统
 * 以下为demo
 * @param array $data
 * @return bool
 * @author zqt
 */
function publishMQ($jsonStr){
    $rabbitMQ = E('RabbitMQ');
    $rabbitMQ->connection('fetchorder');//服务器配置信息等
    $exchange = "SEND_BIG_ORDER_2_PH";//交换器名称
    if(empty($jsonStr)){
        self::$errMsg = get_promptmsg(10014);
        return false;
    }
	return $rabbitMQ->basicPublish($exchange, $jsonStr);
}

function getOrderCalcListById($id){
    $orderCalcList =  M('Order')->getOrderCalcListById($id);
    if(is_array($orderCalcList)){
        return $orderCalcList[0];
    }
    return  false;
}

/**
 * @param $sku
 * @return mixed
 * 每日均量
 */
function getSkuAverageDailyCount($sku){
    return F('SkuDailyInfo')->getSkuAverageDailyCount($sku);
}

function getRecordsOrderAudit($omOrderDetailId){
    $auditStatusName = '--';
    if($orderAuditData =  M('Order')->getOrderAuditListByDetailId($omOrderDetailId)){
        $auditStatus = $orderAuditData['auditStatus'];
        switch($auditStatus){
            case 1:
                $auditStatusName = '审核通过';
                break;
            case 2:
                $auditStatusName = '拦截';
                break;
            case 0:
                $auditStatusName = '待确认';
                break;
        }
    }
    return $auditStatusName;
}