<?php
/*
 * 名称：OrderindexAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * 修改：Herman.Xi @ 20131205
 * 修改：linzhengxiang @ 20140522
 * */
class OrderAct extends CheckAct{
	
	private $orderpower = array();
	
	public function __construct(){
		parent::__construct();
	}
	
	public function act_getOrderList(){
		$conditions = $this->act_getOrderCondition();
        $sort 	 = 'ORDER BY id DESC';	//排序方式  待开发
        if(sizeof($conditions) == 1 && sizeof($conditions['order']) == 1){
            $orderlist = M('Order')->getOrderList($conditions, $this->page, $this->perpage, $sort);
            //$orderlist = array();
        }else{
            $orderlist = M('Order')->getOrderList($conditions, $this->page, $this->perpage, $sort);
        }
        //$orderlist = M('Order')->getOrderList($conditions, $this->page, $this->perpage, $sort);


        //var_dump(get_skudailystatus('SV002401_R'));
		//var_dump(M('InterfacePc')->getSkuinfo('001'));
		//var_dump(M('InterfacePc')->getSkuinfo('CB001260_3'));
		//var_dump(M('InterfacePc')->getSkuinfo('TK_CB49'));
    	//var_dump(get_orderskulist('CB001260_3'));
		//var_dump(get_orderskulist('TK_CB49'));
		//var_dump(get_orderskulist('001'));
		//var_dump(M('InterfacePower')->userLogin('linzhengxiang@sailvan.com', 'abc-1231'));
		//var_dump(M('Order')->getOrderList($conditions, $this->page, $this->perpage, $sort));
		//var_dump(M('InterfacePower')->getUserInfo(10));
		//var_dump(M('InterfacePower')->userLogin('linzhengxiang@sailvan.com', 'abc-123'));
		//var_dump(M('InterfacePower')->getUserPower('5fe15e41a36a5f9e1f683656f9d88fdc'));
		//var_dump(M('StatusMenu')->getStatusMenuByUserId(get_userid()));

		//var_dump($conditions, M('Order')->getAllRunSql());
		return $orderlist;
	}
	
	public function act_getOrderCount(){
		$conditions = $this->act_getOrderCondition();
		return M('Order')->getOrderCount($conditions);
	}
	
	public function act_setOrderUserPower($name, $data){
		if (isset($this->orderpower[$name])&&!empty($this->orderpower[$name])){
			return false;
		}
		$data = array_unique(array_filter(array_map("intval", $data)));
		$this->orderpower[$name] = $data;
		return true;
	}
	
	public function act_clearOrderUserPower(){
		$this->orderpower = array();
		return true;
	}
	/**
	 * 根据条件统计该条件下的订单数
	 * @param [platformId,accountId,ordersTime]
	 * @return array
	 * @author yxd
	 */
	public function act_getOrdersByPlatForm(){   
		$platformId        = isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : 0;
		$accountId         = isset($_REQUEST['accountId']) ? $_REQUEST['accountId'] : 0;
		$OrderTime1        = isset($_REQUEST['OrderTime1']) ?$_REQUEST['OrderTime1'] :0;
		$OrderTime2        = isset($_REQUEST['OrderTime2']) ?$_REQUEST['OrderTime2'] :0;
		$searchTimeType    = isset($_REQUEST['searchTimeType']) ? $_REQUEST['searchTimeType'] : 0;
		if(empty($platformId) || empty($OrderTime1) ||empty($OrderTime2) || empty($searchTimeType)){
			self::$errMsg[4006]   = "平台和时间参数必填";
			return false;
		}
		$OrderTime1    = strtotime($OrderTime1);
		$OrderTime2    = strtotime($OrderTime2);
		if($searchTimeType==3){
			$timeType   = "orderAddTime";
		}
		if($searchTimeType==2){
			$timeType   = "";
		}
		if($searchTimeType==1){
			$timeType   = "paymentTime";
		}
		$condition['platformId']   = array('$e'=>$platformId);
		if($accountId){
		$condition['accountId']    = array('$e'=>$accountId);
		}
		$condition[$timeType]      = array('$b'=>"$OrderTime1"."-"."$OrderTime2");
		$ordersData     = M("order")->getOrdersByPlatForm($condition);
		$orderData1     = array();//平台下账号分类
		$orderData2     = array();//平台下账号，状态分类
		$orderData3     = array();//平台下账号，状态,类型 分类
		if(empty($ordersData)){
			return false;
		}
		foreach($ordersData as $value){
			$accountIdkey                       = $value['accountId'];
			$orderData1[$accountIdkey][]    = $value;
		}
		
		foreach($orderData1 as $key=>$data){
			foreach($data as $value){
				$orderData2[$key][$value['orderStatus']][]   = $value;
			}
		}
		
		$typKeyArr    = array();//账号数组
		$statusKeyArr     = array();//状态数组
		$statusKeyArr        = array();//类型数组     
		foreach($orderData2 as $aid=>$adata){
			$accountKeyArr[]        = $aid;
			foreach($adata as $sid=>$sdata){
				$statusKeyArr[]     = $sid;
				foreach($sdata as $value){
					$typKeyArr[]    = $value['orderType'];
					$orderData3[$aid][$sid][$value['orderType']][]   =1;
				}
			}
		}
		$accountKeyArr    = array_unique($accountKeyArr);
		$statusKeyArr     = array_unique($statusKeyArr);
		$typKeyArr        = array_unique($typKeyArr);
		$countData    = array();
		foreach ($accountKeyArr as $accountkey){
			foreach($statusKeyArr as $statuskey){
				foreach($typKeyArr as $typekey){
					$countData[$accountkey][$statuskey][$typekey]    = count($orderData3[$accountkey][$statuskey][$typekey]);
					if($countData[$accountkey][$statuskey][$typekey]==0){
						unset($countData[$accountkey][$statuskey][$typekey]);
					}
				}
			}
		}
		return $countData;
	}

	private function act_getOrderCondition(){
		#########################################################################
		#########					 查询条件 start			 			#########
		#########################################################################
		$nowtime 	= time();
		$starttime 	= $endtime = 0;
		$searcherrs = $ordercd = $userinfo = array();
		if (isset($_GET['KeywordsType'])&&$_GET['KeywordsType']!='*'&&!empty($_GET['KeywordsType'])){
			$field = trim($_GET['KeywordsType']);
			if (isset($_GET['Keywords'])&&!empty($_GET['Keywords'])){
				$Keywords = trim($_GET['Keywords']);
                if(in_array($field,array('platformId'))){
                    $Keywords = getPlatformIdFromName($Keywords);
                }elseif(in_array($field,array('accountId'))){
                    $Keywords = getAccountIdFromName($Keywords);
                }
				$keycond = strpos($Keywords, ',')!==false ? array('$in'=>array2strarray(explode(',', $Keywords))) : array('$e'=>$Keywords);
				if(in_array($field, array('id', 'recordNumber','platformId','accountId'))){
					$ordercd[$field] = $keycond;
				}else if(in_array($field, array('email', 'platformUsername'))){
					$userinfo[$field] = $keycond;
				}else if(in_array($field, array('tracknumber'))){
					$trackcd[$field] = $keycond;
				}else if(in_array($field, array('PayPalPaymentId'))){
					$orderextcd[$field] = $keycond;
				}
			}
		}


        if(isset($_GET['status']) &&$_GET['status']!='*'&&!empty($_GET['status'])){
            $ordercd['status']  = array('$e'=>intval($_GET['status']));
            $ordercd['menu']    = array('$e'=>intval($_GET['menu']));
        }

		if (isset($_GET['platformId'])&&$_GET['platformId']!='*'&&!empty($_GET['platformId'])){
			$ordercd['platformId'] = array('$e'=>intval($_GET['platformId']));
		}
		if (isset($_GET['accountId'])&&$_GET['accountId']!='*'&&!empty($_GET['accountId'])){
			$ordercd['accountId'] = array('$e'=>intval($_GET['accountId']));
		}
		if (isset($_GET['transportationType'])&&$_GET['transportationType']!='*'&&!empty($_GET['transportationType'])){
			$ordercd['transportationType'] = array('$e'=>intval($_GET['transportationType']));
		}
		if (isset($_GET['transportation'])&&$_GET['transportation']!='*'&&!empty($_GET['transportation'])){
			$ordercd['transportId'] = array('$e'=>intval($_GET['transportation']));
		}
		if (isset($_GET['isNote'])&&$_GET['isNote']!='*'&&!empty($_GET['isNote'])){
			$ordercd['isNote'] = array('$e'=>intval($_GET['isNote']));
		}
		if (isset($_GET['ostatus'])&&!empty($_GET['ostatus'])&&$_GET['ostatus']!='*'){
			$ordercd['orderStatus'] = array('$e'=>intval($_GET['ostatus']));
		}
		if (isset($_GET['isexpressdelivery'])&&!empty($_GET['isexpressdelivery'])&&$_GET['isexpressdelivery']!='*'){
			$ordercd['isExpressDelivery'] = array('$e'=>intval($_GET['isexpressdelivery']));
		}
		if (isset($_GET['otype'])&&!empty($_GET['otype'])&&$_GET['otype']!='*'){
			$ordercd['orderType'] = array('$e'=>intval($_GET['otype']));
		}
		if (isset($_GET['countrySn'])&&!empty($_GET['countrySn'])){
			$countrySn = trim($_GET['countrySn']);
			if ($this->act_checkCountryCode($countrySn)!==false){
				$userinfo['countrySn'] = array('$e'=>$countrySn);
			}else{
				$searcherrs[$this->errCode] = get_promptmsg($this->errCode, $countrySn);
			}
		}
		$sTimeType = isset($_GET['searchTimeType'])&&!empty($_GET['searchTimeType']) ? trim($_GET['searchTimeType']) : 'paymentTime';
		if (isset($_GET['OrderTime1'])&&!empty($_GET['OrderTime1'])){
			$OrderTime1 = trim($_GET['OrderTime1']);
			if (validate_datetime($OrderTime1)){
				$starttime = strtotime($OrderTime1);
			}else{
				$searcherrs[10005] = get_promptmsg(10005, $OrderTime1);
			}
		}
		if (isset($_GET['OrderTime2'])&&!empty($_GET['OrderTime2'])){
			$OrderTime2 = trim($_GET['OrderTime2']);
			if (validate_datetime($OrderTime2)){
				$endtime = strtotime($OrderTime2);
			}else{
				$searcherrs[10006] = get_promptmsg(10006, $OrderTime2);
			}
		}
		if ($starttime>0&&$endtime===0&&$nowtime){
			$ordercd[$sTimeType] = array('$gt'=>$starttime);
		}else if ($starttime===0&&$endtime>0){
			$ordercd[$sTimeType] = array('$lt'=>$endtime);
		}else if ($starttime<$endtime){
			$ordercd[$sTimeType] = array('$b'=>"{$starttime}-{$endtime}");;
		}else if ($starttime>$endtime){
			$searcherrs[10007] = get_promptmsg(10007, $OrderTime1, $OrderTime2);
		}else if ($starttime>0&&$starttime=$endtime){
			$searcherrs[10008] = get_promptmsg(10008, $OrderTime1, $OrderTime2);
		}
		if (isset($this->orderpower)&&!empty($this->orderpower)){
			foreach ($this->orderpower AS $name=>$plist){
				$ordercds[] = "{$name} IN (".implode(',', $plist).")";
			}
		}
		//分表相关

		$ordercd['is_delete'] = array('$e'=>0);
		//查询条件合并
		$conditions = array();
		$conditions['order'] = $ordercd;  //主表必须在前面
		if (!empty($userinfo)){
			$conditions['userinfo'] = $userinfo;
		}
		if (!empty($trackcd)){
			$conditions['trackcd'] = $trackcd;
		}
		if (!empty($orderextcd)){
			$conditions['orderextcd'] = $orderextcd;
		}
		#########################################################################
		#########					 查询条件 end			 				#########
		#########################################################################
		return $conditions;
	}
    
     /**
	 * 根据前端传来的userId和platformId,获取该用户的信息记录，om_buyerinfo 
	 * @return array()
	 * @author zqt
	 */
    //获取用户信息
	public function  act_getBuyerinfo(){
		$userid 	= trim($_POST['userid']);
		$platformId = trim($_POST['platformId']);
		$user_info  = M('Order')->getBuyerinfo($userid, $platformId);
        if(empty($user_info)){
            self::$errMsg[10040] = get_promptmsg(10040);
        }else{
            self::$errMsg[200] = get_promptmsg(200, '获取用户信息');
        }
		return $user_info;
	}
	
	/**
	 * 获取当前订单页面的表关键词
	 * @return string()bool
	 * @author czq
	 */
	public function act_getTableKey(){
		return M('Order')->getTableKey();
	}
	
	/**
	 * 根据订单id数组获取对应的完整订单信息
	 * @param array $ids
	 * @return array
	 * @author czq
	 */
	public function act_getOrderById($tablekey,$ids){
		return M('Order')->getOrderById($tablekey,$ids);
	}
	
	/**
	 * 根据订单id数组获取对应的完整订单信息
	 * @param array $ids
	 * @return array
	 * @author czq
	 */
	public function act_getFullUnshippedOrderById($ids){
		return M('Order')->getFullUnshippedOrderById($ids);
	}
    
    /**
	 * 根据GET传递过来的订单id，返回对应的订单完整信息
	 * @return array
	 * @author zqt
	 */
	public function act_getFullUnshippedOrderByGetId(){
	    $orderid = intval($_GET['orderid']);
		return array_pop(M('Order')->getFullUnshippedOrderById(array($orderid)));
	}
	
	/**
	 * 根据订单id数组获取对应的订单明细
	 * @param array $ids
	 * @return array
	 * @author czq
	 */
	public function act_getUnshippedOrderDetailById($ids){
		return M('Order')->getUnshippedOrderDetailById($ids);
	}
	
	/**
	 * 获取拆分订单的信息
	 * @return string $info
	 * @author czq
	 */
	public function act_getSplitOrder(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		/*$ostatus = $_POST['orderStatus'];
		 $otype = $_POST['orderType'];*/
		 
		$details  = M('Order')->getUnshippedOrderDetailById(array($orderid));
		$detail   = $details[$orderid];
		if(!$detail){
			self::$errMsg[10075] = get_promptmsg(10075);
			return false;
		}
		if(count($detail)==1&&$detail[0]['amount']==1){
			//self::$errMsg[10076] = get_promptmsg(10076);
			//return false;
		}
		$arr = array();
		foreach($detail as $key=>$value){
			$arr[] = $value['sku']."*".$value['amount'];
		}
		$info = join(',', $arr);
		self::$errMsg[200] = get_promptmsg(200,'获取拆分订单');
		return $info;
	}

    /**
     * 获取full手工退款信息
     * @return array
     * @author yxd
     */
    public function act_getfullRefund(){
    	$start        = isset($_REQUEST['handRefundStart']) ? $_REQUEST['handRefundStart'] : 0;//退款开始时间
    	$end          = isset($_REQUEST['handRefundEnd']) ? $_REQUEST['handRefundEnd'] : 0;//退款结束时间
    	$omOrderId    = isset($_REQUEST['omOrderId']) ? $_REQUEST['omOrderId'] : 0;//订单id
    	$accountId    = isset($_REQUEST['accountId']) ? $_REQUEST['accountId'] : 0;//账号id
    	$start        = strtotime($start);
    	$end          = strtotime($end);
    	if($start && $end){
    		$condition['addTime']    = array('$b'=>"$start"."-"."$end");
    	}
    	if(!empty($omOrderId)){
    		$condition['omOrderId']    = array('$e'=>$omOrderId);
    	}
    	if(!empty($accountId)){
    		$condition['accountId']    = array('$e'=>$accountId);
    	}
    	$condition['is_delete']     = array('$e'=>0);
    	$refundData    = M("OrderRefund")->getRefundList($condition);
    	$fulldata      = array();
    	foreach($refundData as $key=>$refund){
    		$refundid            = $refund['id'];
    		$refundDetail        = M("orderRefundDetail")->getRefundDetailList($refundid);
    		$refund['detail']    = $refundDetail; 
    		$fulldata[$key]      = $refund;
    	}
    	return $fulldata;
    }
    /**
     * 获取退款日志信息详情(paypal退款)
     * @author 姚晓东
     */
    public function act_getfullRefundLog(){
    	$start        = isset($_REQUEST['refundStart']) ? $_REQUEST['refundStart'] : 0;//退款开始时间
    	$end          = isset($_REQUEST['refundEnd']) ? $_REQUEST['refundEnd'] : 0;//退款结束时间
    	$omOrderId    = isset($_REQUEST['omOrderId']) ? $_REQUEST['omOrderId'] : 0;//订单id
    	$accountId    = isset($_REQUEST['accountId']) ? $_REQUEST['accountId'] : 0;//账号id
    	$start        = strtotime($start);
    	$end          = strtotime($end);
    	if($start && $end){
    		$condition['refund_time']    = array('$b'=>"$start"."-"."$end");
    	}
    	if(!empty($omOrderId)){
    		$condition['order_id']    = array('$e'=>$omOrderId);
    	}
    	if(!empty($accountId)){
    		$condition['ebay_account']    = array('$e'=>$accountId);
    	}
    	$refundData    = M("EbayRefundLog")->getRefundLogList($condition);
    	$fulldata      = array();
    	foreach($refundData as $key=>$refund){
    		$refundid            = $refund['id'];
    		$refundDetail        = M("ebayRefundLogDetail")->getRefundDetailLogList($refundid);
    		$refund['detail']    = $refundDetail;
    		$fulldata[$key]      = $refund;
    	}
    	return $fulldata;
    }
    
    /**
	 * 提供给运输方式管理系统及仓库系统接口，批量根据订单编号获取申请跟踪号所需信息
	 * @return array
	 * @author zqt
	 */
	public function act_getOrderInfoListByOmOrderIds(){
	    $omOrderIdArr = explode(',', $_REQUEST['omOrderIds']?$_REQUEST['omOrderIds']:'');
        if(empty($omOrderIdArr[0])){
            self::$errMsg[10027] = get_promptmsg(10027);
            return false;
        }
		$orderData = M('Order')->getFullUnshippedOrderById($omOrderIdArr);
        foreach($orderData as $omOrderId=>$orderArr){
            foreach($orderArr['orderDetail'] as $orderdetailIndex=>$detailArr){       
                $orderData[$omOrderId]['orderDetail'][$orderdetailIndex]['skuDetail'] = M('InterfacePc')->getSkuInfo($detailArr['orderDetail']['sku']);
            }
            $orderCalcList = M('Order')->getOrderCalcListById($omOrderId);
            if(!empty($orderCalcList[0])){
                $orderData[$omOrderId]['orderCalculation'] = $orderCalcList[0];
            }
            $orderDeclarationContentList = M('ExpressRemark')->getRemarkById($omOrderId);
            if(!empty($orderDeclarationContentList)){
                $orderData[$omOrderId]['orderDeclarationContent'] = $orderDeclarationContentList;
            }
        }
        return $orderData;
    }
}