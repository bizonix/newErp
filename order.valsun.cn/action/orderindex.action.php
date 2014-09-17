<?php
/*
 * 名称：OrderModifyAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * 修改：Herman.Xi @ 20131205
 * */
/*include_once WEB_PATH.'model/orderModify.model.php';
include_once WEB_PATH.'model/omAvailable.model.php';
include_once WEB_PATH.'model/common.model.php';
include_once WEB_PATH.'model/orderindex.model.php';*/
class OrderindexAct{
	public static $errCode = 0;
	public static $errMsg = '';
	
	//获取对应订单详情
	public function act_index($parameterArr,$searchKeywordsType='',$searchKeywords='',$limit='',$ostatus = ''){
		
		$where	=	'';
		$table	=	'';
		if($searchKeywordsType != ''){
			switch($searchKeywordsType){
				case 1:
					$where				=	' AND username="'.$searchKeywords.'"';
					$table				=	'om_unshipped_order_userInfo';
					break;
				case 2:
					$where				=	' AND email="'.$searchKeywords.'"';
					$table				=	'om_unshipped_order_userInfo';
					break;
				case 3:
					$where				=	' AND da.recordNumber="'.$searchKeywords.'"';
					$table				=	'';
					break;
				case 4:
					$where				=	' AND PayPalPaymentId="'.$searchKeywords.'"';
					$table				=	'om_unshipped_order_extension_ebay';
					break;
				case 5:
					$where				=	' AND tracknumber="'.$searchKeywords.'"';
					$table				=	'om_order_tracknumber';
					break;
				case 6:
					$where				=	' AND da.id="'.$searchKeywords.'"';
					$table				=	'';
					break;
			}
		}		
		
		$data	=	OrderindexModel::index($parameterArr,$where,$table,$limit,$ostatus);
		return $data;
	}
	
	/*
	 * 根据条件获取对应订单详情的数量(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	/*public function act_showSearchOrderNum($ostatus, $otype, $parameterArr = '', $searchKeywordsType='', $searchKeywords='', $storeId = 1){
		$searchPlatformId = $parameterArr['searchPlatformId'];
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		$where = ' WHERE is_delete = 0 AND storeId = '.$storeId.' AND orderStatus = '.$ostatus;
		if($otype != ''){
			$where .=	' AND orderType	= '.$otype;
		}
		if($searchKeywordsType != ''){
			$OmAccountAct = new OmAccountAct();
			$platfrom = $OmAccountAct->act_getPlatformSuffixById($searchPlatformId);
			$extension = $platfrom['suffix'];//获取后缀名称
			if($searchKeywordsType == 4 && empty($extension)){
				self::$errCode = '0001';
				self::$errMsg  = '扩展信息请先选择平台！';
				return false;	
			}
			switch($searchKeywordsType){
				case 1:
					$where				=	' AND username="'.$searchKeywords.'"';
					$table				=	$tableName.'_userInfo';
					break;
				case 2:
					$where				=	' AND email="'.$searchKeywords.'"';
					$table				=	$tableName.'_userInfo';
					break;
				case 3:
					$where				=	' AND db.recordNumber="'.$searchKeywords.'"';
					$table				=	'';
					break;
				case 4:
					$where				=	' AND PayPalPaymentId="'.$searchKeywords.'"';
					$table				=	$table.'_extension_ebay';
					break;
				case 5:
					$where				=	' AND tracknumber="'.$searchKeywords.'"';
					$table				=	'om_order_tracknumber';
					break;
				case 6:
					$where				=	' AND da.id="'.$searchKeywords.'"';
					$table				=	'';
					break;
			}
		}	
		$data	=	OrderindexModel::showOrderNum($tableName, $where);
		
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}*/
	
	/*
	 * 根据条件获取对应订单详情的数量(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_showOrder($ostatus, $otype, $limit='', $parameterArr = '', $storeId = 1){
		$where = ' WHERE da.is_delete = 0 AND da.storeId = '.$storeId;
		$UserCompetenceAct = new UserCompetenceAct();
		$accountList = $UserCompetenceAct->act_showGlobalUser();
		if(!$accountList){
			if($limit != ''){
				return array();
			}else{
				return 0;
			}
		}
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		//$tableName = "om_unshipped_order";
		$searchKeywordsType=isset($parameterArr['searchKeywordsType']) ? $parameterArr['searchKeywordsType'] : '';
		$searchKeywords=isset($parameterArr['searchKeywords']) ? $parameterArr['searchKeywords'] : '';
		$AbOrderList=isset($parameterArr['AbOrderList']) ? $parameterArr['AbOrderList'] : '';
		if(!$AbOrderList){
			if($ostatus != ''){
				$where .=	' AND da.orderStatus = '.$ostatus;
			}
			if($otype != ''){
				$where .=	' AND da.orderType	= '.$otype;
			}
		}
		if(is_array($AbOrderList)){
			$where	.=	' AND da.id in ("'.join('","',$AbOrderList).'") AND orderStatus != '.C('STATEOUTOFSTOCK'). ' AND orderType != '.C('STATEOUTOFSTOCK_ABNORMAL');
		}else if($AbOrderList != ''){
			$where	.=	' AND da.id="'.$AbOrderList.'" AND orderStatus != '.C('STATEOUTOFSTOCK'). ' AND orderType != '.C('STATEOUTOFSTOCK_ABNORMAL');
		}
		if(!empty($parameterArr) && $searchKeywordsType != ''){
			$searchPlatformId = $parameterArr['searchPlatformId'];
			/*if($searchKeywordsType == 4 && empty($searchPlatformId)){
				self::$errCode = '0001';
				self::$errMsg  = '扩展信息请先选择平台！';
				if($limit != ''){
					return array();		
				}else{
					return 0;	
				}
			}*/
			$OmAccountAct = new OmAccountAct();
			$platfrom = $OmAccountAct->act_getPlatformSuffixById($searchPlatformId);
			$extension = $platfrom['suffix'];//获取后缀名称
			$searchTable		=	'';
			if($searchKeywords){
				switch($searchKeywordsType){
					case 1:
						$_where				=	' AND platformUsername="'.$searchKeywords.'"';
						$searchTable		=	$tableName.'_userInfo';
						break;
					case 2:
						$_where				=	' AND email="'.$searchKeywords.'"';
						$searchTable		=	$tableName.'_userInfo';
						break;
					case 3:
						$where				.=	' AND da.recordNumber="'.$searchKeywords.'"';
						break;
					case 4:
						$_where				=	' AND PayPalPaymentId="'.$searchKeywords.'"';
						$searchTable		=	$tableName.'_extension_'.$extension;
						break;
					case 5:
						$_where				=	' AND tracknumber="'.$searchKeywords.'"';
						$searchTable		=	'om_order_tracknumber';
						break;
					case 6:
						$where				.=	' AND da.id="'.$searchKeywords.'"';
						break;
				}
			}
			
			if($searchTable != ''){
				//echo $searchTable; echo "<br>";
				//echo $_where; echo "<br>";
				$searchTableList	=	OmAvailableModel::getTNameList($searchTable, ' * ' , ' WHERE 1 '.$_where);
				//var_dump($searchTableList);
				//$where	=	'';
				if(empty($searchTableList)){
					self::$errCode = '0002';
					self::$errMsg  = '无法获取扩展信息！';
					if($limit != ''){
						return array();		
					}else{
						return 0;	
					}
				}
				//var_dump("--------------");
				$arrayId	=	'';
				foreach($searchTableList as $key => $tableValue){
					if($arrayId == ''){
						$arrayId	.=	'('.$tableValue['omOrderId'];
					} else {
						$arrayId	.=	','.$tableValue['omOrderId'];
					}
				}
				$arrayId	.=	')';
				$where .= ' AND da.id in '.$arrayId;

			}
	
			//order表
			if($searchPlatformId != ''){
				$where .= ' AND da.platformId = "'.$searchPlatformId.'" ';
				if($parameterArr['searchAccountId'] != ''){
					$where .= ' AND da.accountId = "'.$parameterArr['searchAccountId'].'" ';
				}
			}
			if($parameterArr['searchIsNote'] != ''){
				switch($parameterArr['searchIsNote']){
					case 1:
						$where .= ' AND da.isNote = "1" ';
						break;
					case 2:
						$where .= ' AND da.isNote = "0" ';
						break;
					default:
						break;
				}
			}
	
			if($parameterArr['searchTransportation'] != ''){
				$where .= ' AND da.transportId = '.$parameterArr['searchTransportation'];
			} else {
				if($parameterArr['searchTransportationType'] != ''){
					if($parameterArr['searchTransportationType'] == 1){//快递
						$kdCarrierIDList = CommonModel::getCarrierExp();
						//echo "<pre>"; print_r($kdCarrierIDList); echo "<br>";
						$where	.=	' AND da.transportId in ('.join(',', $kdCarrierIDList).')';
					}else if($parameterArr['searchTransportationType'] == 2){//平邮
						$xbCarrierIDList = CommonModel::getCarrierNoExp();
						$where	.=	' AND da.transportId in ('.join(',', $xbCarrierIDList).')';
					}
				}
			}
			//echo $where; echo "<br>";
			if($parameterArr['searchIsBuji'] != ''){
				switch($parameterArr['searchIsBuji']){
					case 1:
						$where	.=	' AND da.isBuji = 2 ';
						break;
					case 2:
						$where	.=	' AND da.isBuji != 2 ';
						break;
					default:
						break;
				}
			}
			/*if($parameterArr['searchIsTracknumber'] != ''){
				switch($parameterArr['searchIsTracknumber']){
					case 1:
						$where	.=	' AND da.isBuji = 2 ';
						break;
					case 2:
						$where	.=	' AND da.isBuji != 2 ';
						break;
					default:
						break;
				}
			}*/
			if($parameterArr['searchIsLock'] != ''){
				switch($parameterArr['searchIsLock']){
					case 2:
						$where	.=	' AND da.isLock = 0 ';
						break;
					case 1:
						$where	.=	' AND da.isLock = 1 ';
						break;
					default:
						break;
				}
			}
			$id_arr = array();
			if($parameterArr['searchTimeType']==1){
				if(($parameterArr['searchOrderTime1'] != '')&&($parameterArr['searchOrderTime2'] != '')){
					$where .= ' AND da.paymentTime >= "'.strtotime($parameterArr['searchOrderTime1']).'" AND da.paymentTime <= "'.strtotime($parameterArr['searchOrderTime2']).'" ';
				}
			}elseif($parameterArr['searchTimeType']==2){
				if(($parameterArr['searchOrderTime1'] != '')&&($parameterArr['searchOrderTime2'] != '')){
					$warehouse_where = "where weighTime between ".strtotime($parameterArr['searchOrderTime1'])." AND ".strtotime($parameterArr['searchOrderTime2']);
					$warehouse = OmAvailableModel::getTNameList($tableName."_warehouse","omOrderId",$warehouse_where);
					if($warehouse){
						foreach($warehouse as $key=>$value){
							$id_arr[] = $value['omOrderId'];
						}
						
						//$where .= ' AND da.id in('.$id_str.') ';
					}
				}
			}elseif($parameterArr['searchTimeType']==3){//add by zqt ,同步时间搜索
				if(($parameterArr['searchOrderTime1'] != '')&&($parameterArr['searchOrderTime2'] != '')){
					$where .= ' AND da.orderAddTime >= "'.strtotime($parameterArr['searchOrderTime1']).'" AND da.orderAddTime <= "'.strtotime($parameterArr['searchOrderTime2']).'" ';
				}
			}
			/*if($otype	!=	''){
				$where	.=	' AND da.orderType	=	'.$otype;
			}*/
			//order_detail表
			if($parameterArr['searchReviews'] != ''){
				switch($parameterArr['searchReviews']){
					case 1:
						$where	.=	' AND db.reviews is NULL ';
						break;
					case 2:
						$where	.=	' AND db.reviews = "1" ';
						break;
					case 3:
						$where	.=	' AND db.reviews = "2" ';
						break;
					case 4:
						$where	.=	' AND db.reviews = "3" ';
						break;
					default:
						break;
				}
			}
			
			//order_userInfo表
			$userInfo_where = "where 1=1";
			if($parameterArr['countryName'] != ''){
				$userInfo_where .= ' AND countryName = "'.$parameterArr['countryName'].'" ';
			}
			if($parameterArr['state'] != ''){
				$userInfo_where .= ' AND state = "'.$parameterArr['state'].'" ';
			}
			if($parameterArr['city'] != ''){
				$userInfo_where .= ' AND city = "'.$parameterArr['city'].'" ';
			}
			if($parameterArr['zipCode'] != ''){
				$userInfo_where .= ' AND zipCode = "'.$parameterArr['zipCode'].'" ';
			}

			if($userInfo_where != "where 1=1"){
				$userInfo = OmAvailableModel::getTNameList($tableName."_userInfo","omOrderId",$userInfo_where);
			}
			
			if($userInfo){
				//$id_arr = array();
				foreach($userInfo as $key=>$value){
					$id_arr[] = $value['omOrderId'];
				}
			}
			
			if($id_arr){
				$where .= ' AND da.id in('.implode(",",$id_arr).') ';
			}
			
			if($parameterArr['searchSku'] != ''){
				$where	.=	' AND db.sku = "'.$parameterArr['searchSku'].'" ';
			}
			
			/*if($searchOmOrderId != ''){
				$where	.=	' AND db.recordNumber = "'.$searchOmOrderId.'" ';
			}*/
			
			if($parameterArr['searchOrderType'] != ''){
				switch($parameterArr['searchOrderType']){
					case 1:
						$where	.=	' AND da.orderAttribute =1 ';
						break;
					case 2:
						$where	.=	' AND da.orderAttribute =2 ';
						break;
					case 3:
						$where	.=	' AND da.orderAttribute =3 ';
						break;
				}
			}	
		}
		//echo $where;
		if($limit != ''){
			$extenwhere = ' GROUP BY da.id ORDER BY da.paymentTime '.$limit;
			//$extenwhere = ' ORDER BY da.paymentTime '.$limit;
			$data	=	OrderindexModel::showSearchOrderList($tableName, $where, $extenwhere);
			if (empty($data)&&empty($ostatus)&&empty($otype)){
				$data	=	$this->act_showOrder(900, 21, $limit, $parameterArr, $storeId);
			}
			
			
		}else{
			//$extenwhere = ' GROUP BY da.id ORDER BY da.paymentTime ';
			$extenwhere = ' ORDER BY da.paymentTime ';
			$data	=	OrderindexModel::showSearchOrderNum($tableName, $where, $extenwhere);
			if (empty($data)&&empty($ostatus)&&empty($otype)){
				$data	=	$this->act_showOrder(900, 21, $limit, $parameterArr, $storeId);
			}
		}
		//var_dump($data);
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}
	
	/*
	 * 根据条件获取库存异常订单详情的数量(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_showABOrder($ostatus, $otype, $limit='', $parameterArr = '', $storeId = 1){
		$where = ' WHERE da.is_delete = 0 AND da.storeId = '.$storeId;
        $accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId in ('.join(",", $platformsee).') ';
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
		$StatusMenuAct = new StatusMenuAct();
		//$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		$tableName = "om_unshipped_order";
		$searchKeywordsType=isset($parameterArr['searchKeywordsType']) ? $parameterArr['searchKeywordsType'] : '';
		$searchKeywords=isset($parameterArr['searchKeywords']) ? $parameterArr['searchKeywords'] : '';
		$AbOrderList=isset($parameterArr['AbOrderList']) ? $parameterArr['AbOrderList'] : '';
		if(!$AbOrderList){
			if($ostatus != ''){
				$where .=	' AND da.orderStatus = '.$ostatus;
			}
			if($otype != ''){
				$where .=	' AND da.orderType	= '.$otype;
			}
		}
		if(is_array($AbOrderList)){
			$where	.=	' AND da.id in ("'.join('","',$AbOrderList).'") AND orderStatus != '.C('STATEOUTOFSTOCK'). ' AND orderType != '.C('STATEOUTOFSTOCK_ABNORMAL');
		}else if($AbOrderList != ''){
			$where	.=	' AND da.id="'.$AbOrderList.'" AND orderStatus != '.C('STATEOUTOFSTOCK'). ' AND orderType != '.C('STATEOUTOFSTOCK_ABNORMAL');
		}
		//echo $where;
		if($limit != ''){
			$extenwhere = ' GROUP BY da.id ORDER BY da.paymentTime '.$limit;
			//$extenwhere = ' ORDER BY da.paymentTime '.$limit;
			$data	=	OrderindexModel::showSearchOrderList($tableName, $where, $extenwhere);
		}else{
			//$extenwhere = ' GROUP BY da.id ORDER BY da.paymentTime ';
			//$extenwhere = ' GROUP BY da.id ';
			$extenwhere = ' ORDER BY da.paymentTime ';
			$data	=	OrderindexModel::showSearchOrderNum($tableName, $where, $extenwhere);
		}
		//var_dump($data);
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_showSearchOrderNum($ostatus, $otype='', $where=''){
		$StatusMenuAct = new StatusMenuAct();
		$UserCompetenceAct = new UserCompetenceAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		//echo $where."---------<br>";
		$where .= " WHERE da.is_delete = 0 AND da.storeId = 1 ";
		$accountList = $UserCompetenceAct->act_showGlobalUser();
		if($accountList){
			//$where .= ' AND da.accountId in ( '.join(',', $accountList).' ) ';	
		}else{
			return 0;
		}
		$where .= " AND da.orderStatus='$ostatus' ";
		if($otype){
			$where .= " AND da.orderType='$otype' ";
		}
		//$extenwhere = ' GROUP BY da.id ORDER BY da.paymentTime ';
		$extenwhere = '';//' ORDER BY da.paymentTime ';
		$data	=	OrderindexModel::showSearchOrderNum($tableName, $where, $extenwhere);
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}
	
	/*
	 * 根据条件获取对应订单详情接口(最新版)
	 * last modified by Herman.Xi @20140307
	 */
	public function act_showOrderListAPI(){
		$id = $_GET['id'] ? $_GET['id'] : '';
		if(!$id){
			self::$errCode = '5806';
			self::$errMsg  = 'id is error';
			return array();
		}
		//$orderinfo = $this->act_showOrder('', '', ' limit 1 ', array('id'=>$id));
		$where = "where `id` ={$id}";
		$orderinfo = omAvailableModel::getTNameList("`om_unshipped_order`","*",$where);
		if($orderinfo){
			$platfrom = omAccountModel::getPlatformSuffixById($orderinfo[0]['platformId']);
			$extension = $platfrom['suffix'];//获取后缀名称
			$where = "where omOrderId={$id}";
			$msg = omAvailableModel::getTNameList("`om_unshipped_order_detail`","*",$where);
			$detailId = $msg[0]['id'];
			$info = omAvailableModel::getTNameList("om_unshipped_order_detail_extension_".$extension,"*","where omOrderdetailId={$detailId}");
			if($info){
				self::$errCode = '200';
				self::$errMsg  = 'success';
				return $info;
			}
		}else{
			$orderinfo = omAvailableModel::getTNameList("`om_shipped_order`","*",$where);
			$platfrom = omAccountModel::getPlatformSuffixById($orderinfo[0]['platformId']);
			$extension = $platfrom['suffix'];//获取后缀名称
			$where = "where omOrderId={$id}";
			$msg = omAvailableModel::getTNameList("`om_shipped_order_detail`","*",$where);
			$detailId = $msg[0]['id'];
			$info = omAvailableModel::getTNameList("om_shipped_order_detail_extension_".$extension,"*","where omOrderdetailId={$detailId}");
			if($info){
				self::$errCode = '200';
				self::$errMsg  = 'success';
				return $info;
			}
		}
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_showOrderList($ostatus, $otype, $where){
		
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		
		$data	=	OrderindexModel::showOrderList($tableName, $where);
		
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}
	
	public function act_getMarketTimeAPI(){
		$omOrderId = !empty($_GET['omOrderId']) ? $_GET['omOrderId'] : '';
		if(!$omOrderId){
			self::$errCode = '006';
			self::$errMsg  = 'have no omOrderId';
			return false;
		}
		$where = " id = ".$omOrderId." AND is_delete = 0 AND storeId = 1 ";
		$tableName = "om_shipped_order";
		$orderlist = OrderindexModel::showOnlyOrderList($tableName, $where);
		if($orderlist){
			self::$errCode = '007';
			self::$errMsg  = 'have no info';	
			return array($orderlist[0]['marketTime'],$orderlist[0]['ShippedTime']);
		}else{
			self::$errCode = '007';
			self::$errMsg  = 'have no info';	
			return false;
		}
	}
	
	public function act_getRealskulistAPI(){
		$omOrderId = !empty($_GET['omOrderId']) ? $_GET['omOrderId'] : '';
		if(!$omOrderId){
			self::$errCode = '006';
			self::$errMsg  = 'have no omOrderId';
			return false;
		}
		$type = !empty($_GET['type']) ? $_GET['type'] : 1;
		$data = $this->act_getRealskulist($omOrderId, '', $type);
		return $data;
	}
	
	public function act_getRealskulist($omOrderId, $where='', $type = 1, $storeId = 1){
		if($type == 1){
			$tableName = 'om_unshipped_order';
		}else if($type == 2){
			$tableName = 'om_shipped_order';
		}else{
			self::$errCode = '004';
			self::$errMsg  = '请输入需要获取信息的表格';
			return false;
		}
		$data	=	OrderindexModel::getRealskulist($omOrderId, $tableName, $where, $storeId);
		
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $data;
	}
	
	public function act_bestTransport(){
		$omOrderId = $_POST['id'];
		//echo $omOrderId;
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId;
		$orderList = OrderindexModel::showOrderList($tableName, $where);
		$orderData = $orderList[$omOrderId];
		//var_dump($orderData);
		if($orderData['orderData']['platformId'] != 1){
			self::$errCode = 323;
			self::$errMsg  = '除了ebay平台才可以使用最优运算';
			return false;
		}else{
			$data = array();
			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData);//计算运费
			//var_dump($calcShippingInfo); exit;
			$calcInfo = CommonModel :: calcAddOrderWeight($orderData['orderDetail']);//计算重量和包材
			//var_dump($calcInfo); exit;
			$data['calcWeight'] = $calcInfo[0];
			$data['pmId'] = $calcInfo[1];
			$data['calcShipping'] = $calcShippingInfo['fee'];
			$data['channelId'] = $calcShippingInfo['channelId'];
			$data['transportId'] = $calcShippingInfo['carrierId'];
			if(OrderindexModel::updateOrder($tableName,$data,$where)){
				self::$errCode = OrderindexModel::$errCode;
				self::$errMsg  = OrderindexModel::$errMsg;
				return true;	
			}else{
				self::$errCode = OrderindexModel::$errCode;
				self::$errMsg  = OrderindexModel::$errMsg;
				return false;	
			}
		}
		
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return true;
	}
	
	public function act_transportFee(){
		$omOrderId = $_POST['id'];
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId;
		$orderList = OrderindexModel::showOrderList($tableName, $where);
		$orderData = $orderList[$omOrderId];
		$data = array();
		//print_r($orderData);
		$obj_order_detail_data = array();
	
		foreach($orderData['orderDetail'] as $sku => $detail){
			$obj_order_detail_data[] = $detail['orderDetailData'];
		}

		$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
		$data['calcWeight'] = $weightfee[0];
		
		$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData,1);//计算运费
		//var_dump($calcShippingInfo); exit;
		$calcInfo = CommonModel :: calcAddOrderWeight($orderData['orderDetail']);//计算重量和包材
		//var_dump($calcInfo); exit;
		$data['calcWeight'] = $calcInfo[0];
		$data['pmId'] = $calcInfo[1];
		$data['calcShipping'] = $calcShippingInfo['fee']['fee'];
		$data['channelId'] = $calcShippingInfo['fee']['channelId'];
		
		if(OrderindexModel::updateOrder($tableName,$data,$where)){
			self::$errCode = OrderindexModel::$errCode;
			self::$errMsg  = OrderindexModel::$errMsg;
			return true;	
		}else{
			self::$errCode = OrderindexModel::$errCode;
			self::$errMsg  = OrderindexModel::$errMsg;
			return false;	
		}
	}
    
    public function act_getSYNCCount(){
		$OrderTime1 = !empty($_POST['OrderTime1'])?$_POST['OrderTime1']:'';
        $OrderTime2 = !empty($_POST['OrderTime2'])?$_POST['OrderTime2']:'';
		$start = strtotime($OrderTime1);
        $end = strtotime($OrderTime2);
		//return $OrderTime1.'  '.$OrderTime2;
        if($start >0 && $end > 0){
            $tName = 'om_unshipped_order';
            $where = " WHERE is_delete=0 AND orderAddTime>=$start AND orderAddTime<=$end";
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
            $count = OmAvailableModel::getTNameCount($tName, $where);
            self::$errCode = 200;
		    self::$errMsg  = "同步订单数为 $count";
		    return false;
        }else{
           self::$errCode = 101;
		   self::$errMsg  = '请选择同步时间';
		   return false; 
        }
	}
	
}

?>