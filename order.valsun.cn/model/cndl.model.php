<?php
/*
*商城接口
*ADD BY heminghua 
*/
class cndlModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selecPlatform($where){
		self::initDB();
		$sql = "SELECT * from om_platform {$where}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectAccount($where){
		self::initDB();
		$sql = "SELECT * from om_account {$where}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectOrder($where){
		self::initDB();
		$sql = "SELECT id from om_unshipped_order {$where}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function insertOrder($ordersql){
		self::initDB();
		$sql = "INSERT TINO om_unshipped_order SET {$ordersql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//$ret = self::$dbConn->fetch_array($query);
			return mysql_insert_id();
		}else{
			return false;
		}
	}
	public static function insertOrderExt($ordersql){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_extension_CN SET {$ordersql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//$ret = self::$dbConn->fetch_array($query);
			return true;
		}else{
			return false;
		}
	}
	public static function insertOrderUserInfo($sql){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_userInfo SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//$ret = self::$dbConn->fetch_array($query);
			return true;
		}else{
			return false;
		}
	}
	public static function insertOrderDetail($sql){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_detail SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//$ret = self::$dbConn->fetch_array($query);
			return true;
		}else{
			return false;
		}
	}
	public static function insertOrderDetailExtCN($sql){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_detail_extension_CN SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//$ret = self::$dbConn->fetch_array($query);
			return true;
		}else{
			return false;
		}
	}
	public static function carrier($ebay_carrier){
		if($ebay_carrier == 'dhlfixed' || $ebay_carrier == 'dhlperweight' || $ebay_carrier == 'dhl'){
			return 'DHL';
		}else if ($ebay_carrier=='fedex' || $ebay_carrier=='Fedex'){
			return 'FedEx';
		}else if ($ebay_carrier=='chinapostreg' || $ebay_carrier=='Chinapostreg'){
			return '中国邮政挂号';
		}else if ($ebay_carrier=='chinapost' || $ebay_carrier=='Chinapost'){
			return '中国邮政平邮';
		}else if($ebay_carrier=='ems'){
			return 'EMS';
		}else if($ebay_carrier=='emszones'){
			return 'EMS';
		}else if($ebay_carrier=='sfexpress'){
			return '顺丰快递';
		}else if($ebay_carrier=='stoexpress'){
			return '申通快递';
		}else{
			return $ebay_carrier;
		}
	}
	public static function auto_contrast_intercept($orderData){

		global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		global $GLOBAL_EBAY_ACCOUNT;
		global $express_delivery,$no_express_delivery;
		if (!$SYSTEM_ACCOUNTS) {
			$__liquid_items_array = PurchaseAPIModel::getAdjustransportFromPurchase();
			//var_dump($__liquid_items_array);
			//echo time(); echo "<br>";
			$__liquid_items_postbyhkpost = array_filter(explode(",", $__liquid_items_array['液体产品']));//液体产品
			$__liquid_items_postbyfedex = array_filter(explode(",", $__liquid_items_array['贵重产品']));//贵重物品走联邦
			$__liquid_items_cptohkpost = array_filter(explode(",", $__liquid_items_array['指甲油产品']));//指甲油转香港小包
			$__liquid_items_elecsku = array_filter(explode(",", $__liquid_items_array['电子类产品']));//电子类产品走香港小包
			//$__elecsku_countrycn_array = array_filter(explode(",", $__liquid_items_array['']));//电子类产品指定国家
			$__liquid_items_fenmocsku = array_filter(explode(",", $__liquid_items_array['粉末状产品'])); //粉末状SKU
			$__liquid_items_BuiltinBattery  = array_filter(explode(",", $__liquid_items_array['内置电池产品'])); //内置电池类产品
			$__liquid_items_SuperSpecific = array('6471','14995'); //超规格的产品，长度大于60cm, 三边大于 90cm
			$__liquid_items_Paste = array_filter(explode(",", $__liquid_items_array['膏状产品']));//膏状SKU*/
			$__liquid_items_elecWithoutBattery = array_filter(explode(",", $__liquid_items_array['电子类【不带电池】']));//电子类【不带电池】*/
			$__liquid_items_OutWeight = array_filter(explode(",", $__liquid_items_array['超重产品>=1.9kg']));//超重产品>=1.9kg*/
			
			//取统一包装材料重量数据
			//$MaterInfo = CommonModel::getMaterInfo();
			
			//取统一国家中文名对应英文名
			/*$ec = "select * from ebay_countrys where ebay_user='$user' ";
			$result = $dbConn->execute($ec);
			$ebay_country_lists = $dbConn->getResultArray($result);
			$global_countrycn_coutryen = array();
			foreach($ebay_country_lists AS $ebay_country_list){
				$global_countrycn_coutryen[trim($ebay_country_list['countryen'])] = trim($ebay_country_list['countrycn']);
			}*/
			
			//取各个平台的账号名称
			$SYSTEM_ACCOUNTS = OmAvailableModel::getPlatformAccount();
			//echo "<pre>";print_r($SYSTEM_ACCOUNTS);
			$express_delivery = array();
			$express_delivery_value = array();
			$no_express_delivery = array();
			$no_express_delivery_value = array();
			$express_delivery_value = CommonModel::getCarrierListById(1);
			/*foreach($express_delivery_arr as $value){
				$express_delivery_value[$value['id']] = $value['carrierNameCn'];
			}*/
			$express_delivery = array_keys($express_delivery_value);
			//var_dump($express_delivery);
			$no_express_delivery_value = CommonModel::getCarrierListById(0);
			/*foreach($no_express_delivery_arr as $value){
				$no_express_delivery_value[$value['id']] = $value['carrierNameCn'];
			}*/
			$no_express_delivery = array_keys($no_express_delivery_value);
		}
		
		
		if(!$GLOBAL_EBAY_ACCOUNT){
			$GLOBAL_EBAY_ACCOUNT = array();
			foreach($SYSTEM_ACCOUNTS as $acct){
				foreach($acct as $key=>$value){
					$GLOBAL_EBAY_ACCOUNT[$key] = $value;
				}
				
			}
		}
		//var_dump($GLOBAL_EBAY_ACCOUNT);exit;
		self::initDB();
		//var_dump($orderData); echo "\n";
		$log_data = "";
		$actualTotal0 = 0; //该订单实际总数
		//$ebay_id = $orderData['orderData']['id'];
		$orderStatus = empty($orderData['orderData']['orderStatus']) ? C('STATEPENDING') : $orderData['orderData']['orderStatus'];
		$orderType = empty($orderData['orderData']['orderType']) ? C('STATEPENDING_CONV') : $orderData['orderData']['orderType'];
		$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
		$isNote = $orderData['orderData']['isNote'];
		if($isNote){
			$orderType = C('STATEPENDING_MSG');
		}
		$calcWeight = $orderData['orderData']['calcWeight'];
		$pmId = $orderData['orderData']['pmId'];
		//var_dump($calcWeight); echo "\n";
		$orderdetaillist = $orderData['orderDetail'];
		if(empty($calcWeight)){
			$calcInfo = CommonModel::calcAddOrderWeight($orderdetaillist);//计算重量和包材
			$calcWeight = $calcInfo[0];
			$pmId = $calcInfo[1];
			$orderData['orderData']['calcWeight'] = $calcWeight;
			$orderData['orderData']['pmId'] = $pmId;
		}
		//var_dump($calcWeight); echo "\n";
		$transportId = @$orderData['orderData']['transportId'];
		$countryName = $orderData['orderUserInfoData']['countryName'];
		$accountId = $orderData['orderData']['accountId'];
		$actualTotal	= $orderData['orderData']['actualTotal'];
		$ebay_username = $orderData['orderUserInfoData']['username'];
		$orderDataid = $orderData['orderExtenData']['orderId'];
		$ebay_usermail = $orderData['orderUserInfoData']['email'];
		$PayPalEmailAddress = @$orderData['orderExtenData']['PayPalEmailAddress'];
		//echo "------$countryName-----\n";
		//echo "订单计算重量:$calcWeight\t\n";
		
		$contain_special_item = false;
		$contain_os_item = false;
		$ow_status = array();
		//foreach ($orderdetaillist AS $orderdetail){
		for($i = 0; $i < count($orderdetaillist); $i++){
			$sku = $orderdetaillist[$i]['orderDetailData']['sku'];
			$itemPrice = $orderdetaillist[$i]['orderDetailData']['itemPrice'];
			$amount = $orderdetaillist[$i]['orderDetailData']['amount'];
			$shippingFee = $orderdetaillist[$i]['orderDetailData']['shippingFee'];
			//var_dump($sku);
			$sku_arr = GoodsModel::get_realskuinfo($sku);
			//var_dump($sku_arr); exit;
			$actualTotal0 += $itemPrice*$amount + $shippingFee;
			foreach($sku_arr as $or_sku => $or_nums){

				if(in_array($or_sku,$__liquid_items_fenmocsku) || in_array($or_sku,$__liquid_items_SuperSpecific) || in_array($or_sku,$__liquid_items_BuiltinBattery)){ //粉末状,超规格产品 走福建邮局
					$contain_special_item = true;
				} 
				if(preg_match("/^US01\+.*/", $or_sku, $matchArr) || preg_match("/^US1\+.*/", $or_sku, $matchArr) ){
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号订单---{$ebay_id}-----料号：{$or_sku}--!\n\n";
					$contain_os_item = true;
					if(strpos($or_sku,"US01+") !== false){
						$matchStr=substr($matchArr[0],5);//去除前面
						//$matchStr = str_replace("US1+", "", $or_sku);
					}else{
						//$matchStr=substr($matchArr[0],5);//去除前面
						$matchStr = str_replace("US1+", "", $or_sku);
					}
					$n=strpos($matchStr,':');//寻找位置
					if($n){$matchStr=substr($matchStr,0,$n);}//删除后面

					if(preg_match("/^0+(\w+)/",$matchStr,$matchArr)){
						$matchStr = $matchArr[1];
					}
					
					$orderData['orderDetail'][$i]['orderDetailData']['sku'] = $matchStr;
					//OrderAddModel::updateDetailExtension(array('sku'=>$matchStr), " id = {$orderdetail['ebay_id']} ");

					//$virtualnum = check_oversea_stock($matchStr); //检查海外仓虚拟库存
					$virtualnum = 1; //预留获取海外仓虚拟库存接口 20131225
					if($virtualnum >= 0){
						$ow_status[] = C('STATEPENDING_OVERSEA'); //海外仓待处理
					}else{
						$ow_status[] = C('STATEOUTOFSTOCK_OVERSEA'); //海外仓缺货
					}
				}
			}
		}
		if($contain_special_item){
			/*$sql = "update ebay_order set ebay_carrierstyle ='1' where ebay_id ={$ebay_id}"; //add by Herman.Xi 记录该订单含有特殊料号
			$dbConnn->query($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含粉末状超规格产品---{$ebay_id}---!\n\n";*/
		}
		if($contain_os_item){
			if(in_array(C('STATEOUTOFSTOCK_OVERSEA'),$ow_status)){
				$orderData['orderData']['orderStatus'] = C('STATEPENDING_OS');
				$orderData['orderData']['orderType'] = C('STATEOUTOFSTOCK_OVERSEA');
			}else{
				$orderData['orderData']['orderStatus'] = C('STATEPENDING_OS');
				$orderData['orderData']['orderType'] = C('STATEPENDING_OVERSEA');
			}
			
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t更新海外仓料号订单状态为{$final_status}---{$ebay_id}--{$sql}-!\n\n";
			
			/*if($final_status == C('STATEPENDING_OVERSEA')){

				$calcWeight = calcWeight($ebay_id);
				$skunums	 = checkSkuNum($ebay_id);
				if($skunums === true){
					continue;
				}else if ($calcWeight>20) {
					if($skunums==1){
						usCalcShipCost($ebay_id);
					}
				} else {
					 usCalcShipCost($ebay_id);
				}
			}*/
			//$calcWeight = recalcorderweight($order_sn, $ebay_packingmaterial); //modified by Herman.Xi 2012-10-17
			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号---自动跳转---的状态为---$final_status!\n\n";
			CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $orderData;
			//continue;
		}
		
		$interceptrtn = self::intercept_exception_orders($orderData);
		//var_dump($interceptrtn);
		
		if($interceptrtn){
			$orderData['orderData']['orderStatus'] = $interceptrtn['orderStatus'];
			$orderData['orderData']['orderType'] = $interceptrtn['orderType'];
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $orderData;
		}
		
		$record_details = array();
		$is_640 = false;
		//var_dump($orderdetaillist); exit;
		foreach ($orderdetaillist AS $orderdetail){
			//var_dump($orderdetail['sku']);
			$sku = $orderdetail['orderDetailData']['sku'];
			//$itemPrice = $orderdetail['orderDetailData']['itemPrice'];
			$amount = $orderdetail['orderDetailData']['amount'];
			//$shippingFee = $orderdetail['orderDetailData']['shippingFee'];
			
			$sku_arr = GoodsModel::get_realskuinfo($sku);
			//var_dump($sku_arr); exit;
			$hava_goodscount = true;
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums = $or_nums*$amount;
				if (!CommonModel::check_sku($or_sku, $allnums)){
					//超大订单状态
					$orderStatus = C('STATEOVERSIZEDORDERS');
					if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['aliexpress']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['B2B外单'])*/ || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['DHgate']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['出口通']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['线下结算客户'])){
						$orderType = C('STATEOVERSIZEDORDERS_CONFIRM');
					}else{
						$orderType = C('STATEOVERSIZEDORDERS_PEND');
					}
					//self::insert_mark_shipping($ebay_id);
					$is_640 = true;
					break;
				}else{
					$skuinfo = CommonModel::get_sku_info($or_sku);
					$salensend = CommonModel::getpartsaleandnosendall($or_sku);
					
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
					if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || ($skuinfo['realnums'] - $salensend - $allnums) < 0){
						$hava_goodscount = false;
						break;
					}
				}
			}
			if($hava_goodscount){$record_details[] = $orderdetail;}
		}
		if($is_640){
			$orderData['orderData']['orderStatus'] = $orderStatus;
			$orderData['orderData']['orderType'] = $orderType;
			//$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $orderData;
		}
		$count_record_details = count($record_details);
		$count_orderdetaillist = count($orderdetaillist);
		$final_status = $orderStatus; //原始状态
		
		if($count_record_details == 0){
			//更新至自动拦截发货状态
			/*if (!in_array($ebay_carrier, $no_express_delivery)){
				$final_status = 658;
			}else {
				$final_status = 661;
			}*/
			//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
			$orderStatus = C('STATEOUTOFSTOCK');
			$orderType = C('STATEOUTOFSTOCK_AO');
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEOUTOFSTOCK_AO')."!\n\n";
			//self::insert_mark_shipping($ebay_id);
			//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			//$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$orderData['orderData']['orderStatus'] = $orderStatus;
			$orderData['orderData']['orderType'] = $orderType;
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $orderData;
		}else if($count_record_details < $count_orderdetaillist){
			//更新至自动部分发货状态
			/*if (!in_array($ebay_carrier, $no_express_delivery)){
				//$final_status = 659;
				$final_status = 640;
			}else {
				$final_status = 660;
			}*/
			$orderStatus = C('STATEOUTOFSTOCK');
			$orderType = C('STATEOUTOFSTOCK_PO');
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEOUTOFSTOCK_PO')."!\n\n";
			//self::insert_mark_shipping($ebay_id);
			//$returnStatus = array('orderStatus'=>C('STATEOUTOFSTOCK'), 'orderType'=>C('STATEOUTOFSTOCK_PO'));
			//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			//$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$orderData['orderData']['orderStatus'] = $orderStatus;
			$orderData['orderData']['orderType'] = $orderType;
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $orderData;
		}else if($count_record_details == $count_orderdetaillist){
			//正常发货状态
			if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['ebay'])){
				if(in_array($orderStatus, array(C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_PO')))){
					//$final_status = 618;//ebay订单自动拦截有货不能移动到待处理和有留言 modified by Herman.Xi @ 20130325(移动到缺货需打印中)
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_HASARRIVED');
					}
				}else{
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_CONV');
					}
				}
			}/*else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
				if(in_array($orderStatus, array(C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_PO')))){
					//$final_status = 629; //德国订单区别于正常订单
					//$final_status = 618; //modified by Herman.Xi @20130823 雷贤容需要修改成缺货需打印中
					//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEPENDING_HASARRIVED')."!\n\n";
					//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					$orderStatus = C('STATEPENDING');
					$orderType = C('STATEPENDING_HASARRIVED');
					//$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
				}else{
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "德国订单有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_CONV');
					} 
					//德国订单进入正常订单流程	
				}
			}*/else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['aliexpress']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['B2B外单'])*/){
				if(in_array($countryName, array('Russian Federation', 'Russia')) && strpos($ebay_carrier, '中国邮政')!==false && str_word_count($ebay_username) < 2){
					$orderStatus = C('STATESYNCINTERCEPT');
					$orderType = C('STATESYNCINTERCEPT_AB');

					$orderData['orderData']['orderStatus'] = $orderStatus;
					$orderData['orderData']['orderType'] = $orderType;
					$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
					CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					return $orderData;
				}
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['DHgate'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['dresslink.com'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['cndirect.com'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['Amazon'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else{
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}
			if($isNote == 1){
				echo "有留言\t";
				$orderType = C('STATEPENDING_MSG');
			}
			/*if(self::judge_contain_combinesku_new($orderdetaillist)){
				$final_status = 606;
			}*/
			if($calcWeight > 2){
				echo "\t 超重订单";
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_OW');
			}
			
			$expressArr = CommonModel::getCarrierInfoById(1);
			if(in_array($transportId, $expressArr)){
				$orderType = C('STATEPENDING_HASARRIVED');
			}
			/*if (!in_array($ebay_carrier, $no_express_delivery) && !empty($ebay_carrier)){
				if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['ebay']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
					$final_status = 641;//ebay和海外都跳转到 待打印线下和异常订单
				}else{
					$final_status = 639;
				}
			}*/
		}else{
			$log_data .= "[".date("Y-m-d H:i:s")."]\t订单同步状态有误,请联系IT解决!";
		}
		
		//$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
		$orderData['orderData']['orderStatus'] = $orderStatus;
		$orderData['orderData']['orderType'] = $orderType;
		$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
		
		//print_r($GLOBAL_EBAY_ACCOUNT);
		CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
		return $orderData;
	}
	public static function intercept_exception_orders($orderData){
		global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		global $GLOBAL_EBAY_ACCOUNT;
		global $express_delivery,$no_express_delivery;
		global $actualTotal0;
		if (!$SYSTEM_ACCOUNTS) {
			require_once  WEB_PATH."conf/scripts/script.ebay.config.php";
		}
		self::initDB();
		$log_data = '';
		$orderStatus = empty($orderData['orderData']['orderStatus']) ? C('STATEPENDING') : $orderData['orderData']['orderStatus'];
		$orderType = empty($orderData['orderData']['orderType']) ? C('STATEPENDING_CONV') : $orderData['orderData']['orderType'];
		$isNote = $orderData['orderData']['isNote'];
		if($isNote){
			$orderType = C('STATEPENDING_MSG');
		}
		$transportId = $orderData['orderData']['transportId'];
		$countryName = $orderData['orderUserInfoData']['countryName'];
		$accountId = $orderData['orderData']['accountId'];
		$paymentTime = $orderData['orderData']['paymentTime'];
		$actualTotal	= $orderData['orderData']['actualTotal'];
		$ebay_username = $orderData['orderUserInfoData']['username'];
		$orderDataid = $orderData['orderExtenData']['orderId'];
		$ebay_usermail = $orderData['orderUserInfoData']['email'];
		$PayPalEmailAddress = $orderData['orderExtenData']['PayPalEmailAddress'];
		
		/*echo "<pre>";
		echo "-----------------------------<br>";
		var_dump($SYSTEM_ACCOUNTS['Amazon']);*/

		if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['Amazon'])){//非线下amazon账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($countryName)){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
				
				//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				//CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}
		}

		/*echo "<pre>";
		echo "-----------------------------<br>";
		var_dump($GLOBAL_EBAY_ACCOUNT[$accountId]);
		var_dump($SYSTEM_ACCOUNTS['ebay']);
		var_dump($GLOBAL_EBAY_ACCOUNT[$accountId]);
		var_dump($SYSTEM_ACCOUNTS['海外销售平台']);
		var_dump($orderDataid);*/
		
		if((in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['ebay']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])*/) && !empty($orderDataid)){//非线下ebay账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($countryName)){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}
			if($actualTotal != $actualTotal0){
				$actualTotal0 = (string) $actualTotal0;
			}
			echo "[".date("Y-m-d H:i:s")."]\t总价记录---{$ebay_id}---系统总价{$actualTotal}---计算总价{$actualTotal0}\n";
			if(in_array($ebay_usermail, array("", "Invalid Request")) && $ebay_carrier=='EUB'){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}else if($actualTotal != $actualTotal0 && $orderStatus == 1){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}else if(!empty($PayPalEmailAddress) && !in_array(strtolower($PayPalEmailAddress),PaypalEmailModel::get_account_paypalemails($accountId)) && $orderStatus == 1){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATEPENDING_EXCPAY');
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			//CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
		}
		
		if($orderStatus == C('STATEOUTOFSTOCK')){//缺货和自动拦截判断
			//ebay 线上订单EUB大于5天,平邮和挂号大于7天不发货,不包括快递
			//海外销售十天
			$timeout = false;
			//$orderDataid = isset($orderData['ebay_orderid']) ? $orderData['ebay_orderid'] : '';
			//$ebay_paidtime = isset($orderData['ebay_paidtime']) ? $orderData['ebay_paidtime'] : '';
			if(!empty($paymentTime)){//线上订单,付款时间不能为空
				$diff_time = ceil((time()-$paymentTime)/(3600*24));
				if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['ebay'])){
					if($ebay_carrier == 'EUB' && $diff_time > 5){
						$timeout = true;
					}else if((strpos($ebay_carrier, '平邮')!==false || strpos($ebay_carrier, '挂号')!==false) && $diff_time > 7){
						$timeout = true;
					}
				}/*else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
					if((strpos($ebay_carrier, '中国邮政平邮')!==false && $diff_time > 5) || $diff_time > 10){
						$timeout = true;
					}
				}*/
			}
			if($timeout){
				//$log_data .= "\n缺货订单={$ebay_id}======移动到缺货需退款中======\n";
				$orderStatus = C('STATEREFUND');
				$orderType = C('STATEREFUND_OUTSTOCK');
				//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				//CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
				//continue;
			}
		}
		
		if(in_array($orderStatus, array(C('STATESTOCKEXCEPTION')))){//缺货处理\合并包裹处理
			$have_goodscount = true;
			foreach ($orderdetaillist AS $orderdetail){
				$sku_arr = GoodsModel::get_realskuinfo($orderdetail['sku']);
				foreach($sku_arr as $or_sku => $or_nums){
					$allnums = $or_nums*$orderdetail['ebay_amount'];
					$skuinfo = CommonModel::get_sku_info($or_sku);
					$salensend = CommonModel::getpartsaleandnosendall($or_sku);
					//$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = '$or_sku' ";
					//self::$dbConn->query($sql);
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					//$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
					if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || ($skuinfo['realnums'] - $salensend) < 0){//缺货本身算待发货，不能重复扣除
						$have_goodscount = false;
						break;
					}
				}
			}
			if($have_goodscount){
				/*$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');*/
				$log_data .= "\n缺货订单={$ebay_id}======有货至待打印======\n";
				//$final_status = 618;
				//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				//CommonModel::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
				//continue;
			}
		}
		return false;
	}
}
?>