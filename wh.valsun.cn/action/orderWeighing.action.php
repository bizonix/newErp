<?php
/*
*小包称重扫描功能
*@author by heminghua 
*/
class orderWeighingAct extends Auth{
	public static $errCode = 0;
	public static $errMsg = "";
	 /*
     * 构造函数
     */
    function __construct ()
    {
    	
    }
	//扫描订单号
	public function act_orderWeighingCheck(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$where = "where id={$orderid}";
		$orderinfo = orderWeighingModel::selectRecord($where);
		if(!orderinfo){
			self::$errCode = 502;
			self::$errMsg = "此发货单号不存在！";
			return false;
		}
		if($orderinfo[0]['orderStatus']!=PKS_WWEIGHING){
			if($orderinfo[0]['orderStatus']==PKS_WDISTRICT){
				self::$errCode = 514;
				self::$errMsg = "此发货单已在待分区，不用再扫描！";
				return false;
			}else if($orderinfo[0]['orderStatus']==PKS_WAITING_SORTING){
				self::$errCode = 514;
				self::$errMsg = "此发货单还在待分拣中！";
				return false;
			}else{
				self::$errCode = 514;
				self::$errMsg = "此发货单在".LibraryStatusModel::getStatusNameByStatusCode($orderinfo[0]['orderStatus'])."状态，请确认！";
				return false;
			}
		}
		$flat_transport = C('flat_transport');
		$carrier = CommonModel::getShipingNameById($orderinfo[0]['transportId']);
		if(in_array($carrier,$flat_transport)){
			$arr['type'] = "flat";
		}else{
			self::$errCode = 503;
			self::$errMsg  = "此发货单运输方式不属于小包，请确认！";
			return false;
		}
		
		$partionId  = CommonModel::getChannelIdByIds($orderinfo[0]['transportId'],$orderinfo[0]['countryName']);
		$account    = CommonModel::getAccountNameById($orderinfo[0]['accountId']);
		//运输公司
		$channelId  = printLabelModel::getMcFromCarrier($orderinfo[0]['id'],$carrier,$orderinfo[0]['countryName'],$account);

		$arr['channelId']   = $channelId;
		$arr['transportId'] = $orderinfo[0]['transportId'];
		$arr['partionId']   = $partionId;
		$arr['countryName'] = $orderinfo[0]['countryName'];
		$arr['orderid']     = $orderid;
		return $arr;  
		
	}
	
	//平邮处理
	public function act_orderWeighingFlat(){
		$orderid     = isset($_POST['orderid'])?$_POST['orderid']:"";
		$mailway     = isset($_POST['channelId'])?$_POST['channelId']:"";
		//$partionId   = isset($_POST['partionId'])?$_POST['partionId']:"";//不作处理
		$orderweight = isset($_POST['orderweight'])?$_POST['orderweight']:"";
		
		if(!is_numeric($orderid) || empty($orderid)){
			self::$errCode = 501;
			self::$errMsg = "错误的发货单号！";
			return false;
		}
		$where 		= "where id={$orderid}";
		$lists 		= orderWeighingModel::selectRecord($where);
		$calcweight = $lists[0]['calcWeight']*1000; //估算重量
		$userName 	= $_SESSION['userCnName']; 
        
		/**重量拦截的逻辑**/
        $vip_users  = C('weight_vip'); //获取具有VIP权限的
        $lists = orderWeighingModel::selectOrderDetail($orderid);
        if(!in_array($userName, $vip_users)){ //判断是否具有vip操作权限
        	$minRate = 0.8;
        	$maxRate = 1.2;
        }else{
        	$minRate = 0.5;
        	$maxRate = 2;
        }
        if($calcweight<50&&$orderweight<50){
        	if(abs($orderweight-$calcweight)>=20){
        		self::$errCode = 509;
        		self::$errMsg  = "该订单重量与实际不符合！系统重量为{$calcweight}称重重量为{$orderweight}";
        		return false;
        	}
        }else if(($orderweight<$calcweight*$minRate||$orderweight>$calcweight*$maxRate)&&$calcweight!=0){
        	self::$errCode = 510;
        	self::$errMsg  = "该订单重量与实际不符合！系统重量为{$calcweight}称重重量为{$orderweight}";
        	return false;
        }
		
		TransactionBaseModel :: begin();
		//更新状态，插入记录
		$userId = $_SESSION['userId'];
		$msg = orderWeighingModel::insertRecord($orderid,$userId);
		if(!$msg){
			self::$errCode = 511;
			self::$errMsg  = "插入称重记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		
		$msg = orderWeighingModel::updateRecord($orderid,$orderweight,$userId);
		if(!$msg){
			self::$errCode = 512;
			self::$errMsg  = "更新操作记录表失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		//更新发货单实际重量和状态
		$orderData = array(
			'orderWeight' 	=> $orderweight,
			'orderStatus'  	=> PKS_WDISTRICT,
		);

		$msg = WhShippingOrderModel::update($orderData,$orderid);//orderWeighingModel::updateStatus($orderid);
		if(!$msg){
			self::$errCode = 513;
			self::$errMsg  = "更新发货单重量和状态失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		//插入运输方式跟踪号申请记录表
		$transportData = array(
			'shipOrderId'	=> $orderid,
			'createTime'	=> time(),
		);
		$msg = WhWaveOrderTransportModel::insert($transportData);
		if(!$msg){
			self::$errCode = 514;
			self::$errMsg  = "插入运输方式跟踪号记录表失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		WhPushModel::pushOrderStatus($orderid,'PKS_WDISTRICT',$userId,time());        //状态推送
		TransactionBaseModel :: commit();
		return true;
	}
	
	//料号称重
	public function act_skuWeighing(){
		$sku       = isset($_POST['sku'])?$_POST['sku']:"";
		$sku       = get_goodsSn($sku);
		$skuweight = isset($_POST['skuweight'])?$_POST['skuweight']:"";
		
		if(empty($sku) || empty($skuweight)){
			self::$errCode = 333;
			self::$errMsg = "料号或重量不能为空！";
			return false;
		}

		$info = CommonModel::updateSkuWeight($sku,$skuweight,$_SESSION['userId']);
		if($info == 200){
			self::$errCode = 200;
			self::$errMsg = "重量录入成功！";
			return true;
        }else{
			self::$errCode = 404;
			self::$errMsg = "重量录入出错！";
			return false;
		}
	}
    
    /**
     * orderWeighingAct::act_OrderWeight()
     * 重新计算老ERP订单重量 
     * @return void
     */
    public function act_orderWeight(){
        $ebay_id    =   isset($_POST['ebay_id']) ? $_POST['ebay_id'] : "";
        $user       =   $_SESSION['userCnName'];
        $info       =   CommonModel::updateOrderWeight($ebay_id, $user);
        if($info['res_code'] == 200){
			self::$errCode = 200;
            $weight        = $info['weight']*1000;
			self::$errMsg  = $ebay_id.'订单同步成功！订单重量:'.$weight.'g';
			return true;
        }else{
			self::$errCode = $info['res_code'];
			self::$errMsg  = '订单同步失败！';
			return false;
		}
    }
	
	/*
	public function act_getChannel(){
		global $memc_obj;
		$list = $memc_obj->get_extral('trans_system_channelinfo');
		return "haha";
	}*/
}
?>