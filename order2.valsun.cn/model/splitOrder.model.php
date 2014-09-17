<?php
/*
*拆分订单功能
*ADD BY heminghua 
*/
class SplitOrderModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 拆分超重订单,只支持单个订单，不用事务，在调用层在用事务关联
	 */
	public static function overWeightSplit($omOrderId) {
		!self::$dbConn ? self::initDB() : null;
		global $memc_obj; //调用memcache获取sku信息
		$mctime = time();
		//var_dump($moOrderIdArr);
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId.' and is_delete = 0 and storeId = 1';
		$orderData = OrderindexModel::showOrderList($tableName, $where);
		$orderDetail = $orderData[$omOrderId]['orderDetail'];
		$obj_order_data = $orderData[$omOrderId]['orderData'];
		$orderExtenData = $orderData[$omOrderId]['orderExtenData'];
		$orderUserInfoData = $orderData[$omOrderId]['orderUserInfoData'];
		$_actualTotal = $obj_order_data['actualTotal'];
		$_actualShipping = $obj_order_data['actualShipping'];
		$_platformId = $obj_order_data['platformId'];
		$_mainId = $obj_order_data['id'];
		$_transportId = $obj_order_data['transportId'];
		
		if($_transportId==6){
			self :: $errCode = '0002';
			self :: $errMsg = "EUB 订单不允许超重拆分！";
			return false;
		}
		
		if(in_array($_platformId,array(2,3,4))){
			$erroinfo = self::overWeightSplitB2B($omOrderId);
			if($erroinfo){
				return true;
			}else{
				return false;
			}
		}
		if(!in_array($_platformId, array(1,5,8,10,16))){//预留，和独立出来
			self :: $errCode = '0001';
			self :: $errMsg = "该平台还未开通超重拆分功能！";
			return false;
		}

		//var_dump($orderDetail); exit;
		$omAvailableAct = new OmAvailableAct();
		$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', ' WHERE is_delete=0 AND platformId in(1,5) ');
		
		$weightlists = array();
		$skuinfo = array();
		$goods_sn_nums = 0;
		$shippfee_arr = array();
		foreach($orderDetail as $k=>$f){
			$sku = trim($f['orderDetailData']['sku']);
			$amount = $f['orderDetailData']['amount'];
			$shippingFee = $f['orderDetailData']['shippingFee'];
			$goods_sn_nums += $amount;
			$shippfee_arr[$sku] = round($shippingFee/$amount,3);//单个料号的运费
			$skuinfo[$sku] = $f;
			for($i=1; $i<=$amount; $i++){
				$var = $sku;
				$oneskuweight = CommonModel::calcOnlySkuWeight($var, 1);//一个sku的重量
				$weightlists[$var][] = $oneskuweight[0];
			}
		}
		//var_dump($weightlists); exit;
		if($goods_sn_nums <= 1){
			self :: $errCode = '0010';
			self :: $errMsg = "只有一个料号组成，不允许超重拆分";
			return false;
		}
		//echo "==========="; exit;
		$keyarray = array();
		$keyarrays = array();
		$checkweight = 0;
		$arrinfo = CommonModel::calcNowOrderWeight($omOrderId);
		//var_dump($arrinfo); exit;
		$realweight = $arrinfo[0];
		$realcosts = $arrinfo[2];
		$itemprices = $arrinfo[3];
		
		foreach($weightlists as $wk => $wv){
			foreach($wv as $weightlist){
				$checkweight += $weightlist;
				if($checkweight>1.85){
					$keyarrays[] = $keyarray;
					$keyarray = array();
					$checkweight = $weightlist;
					$keyarray[$wk][] = $wk;
				}else{
					$keyarray[$wk][] = $wk;
				}
			}
		}
		if(!empty($keyarray)){
			$keyarrays[] = $keyarray;
		}
		//var_dump($keyarrays); exit;
		BaseModel :: begin(); //开始事务
		$insert_orderData = array();
		foreach($keyarrays as $keyarray){
			$ebay_total = 0;
			$totalweight = 0;
			$insert_ebay_ids = array();
			//var_dump($skuinfo); echo "<br>";
			foreach($keyarray as $k => $kav){
				//var_dump($skuinfo[$k]['orderDetailData']['itemPrice'], count($kav));
				$ebay_total += ($skuinfo[$k]['orderDetailData']['itemPrice'] + $shippfee_arr[$k]) * count($kav);
			}
			
			$shipfee = 0;
			//$val = generateOrdersn();
			$insert_obj_order_data = $obj_order_data;
			unset($insert_obj_order_data['id']);
			$insert_obj_order_data['actualTotal'] = $ebay_total;
			$insert_obj_order_data['orderType'] = C('STATEPENDING_OWDONE');
			$insert_obj_order_data['orderAddTime'] = $mctime;
			$insert_obj_order_data['isSplit'] = 2;
			
			$insert_orderExtenData = $orderExtenData;
			unset($insert_orderExtenData['id']);
			$insert_orderUserInfoData = $orderUserInfoData;
			unset($insert_orderUserInfoData['id']);
			
			$insert_orderData = array('orderData' => $insert_obj_order_data,
								'orderExtenData' => $insert_orderExtenData,					  
								'orderUserInfoData' => $insert_orderUserInfoData
							   );
			/*$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, create_date) values ('$recordnumber', '$ebay_id', '$insert_ebay_id', '".date("Y-m-d H:i:s")."')";
			$split_log .= "添加主定单和拆分订单到关系表中\r\n".$sql ."\r\n";
			$dbcon->execute($sql) or die("Fail : $sql");*/
			$obj_order_detail_data = array();
			foreach($keyarray as $k => $kav){
				$sku = $k;
				$amount = count($kav);
				
				$insert_orderDetailData = $skuinfo[$k]['orderDetailData'];
				unset($insert_orderDetailData['id']);
				$insert_orderDetailData['sku'] = strtoupper($sku);
				$insert_orderDetailData['amount'] = $amount;
				$insert_orderDetailData['createdTime'] = $mctime;
				if(isset($shippfee_arr[$sku])){
					$insert_orderDetailData['shippingFee'] = $shippfee_arr[$sku]*$amount;//相同料号运费拆分
				}
				
				$insert_orderDetailExtenData = $skuinfo[$k]['orderDetailExtenData'];
				unset($insert_orderDetailExtenData['id']);
				
				$obj_order_detail_data[] = array('orderDetailData' => $insert_orderDetailData,			
												'orderDetailExtenData' => $insert_orderDetailExtenData,
												);
			}
			$insert_orderData['orderDetail'] = $obj_order_detail_data;
			//echo "<pre>";
			//var_dump($obj_order_detail_data); exit;
			$calcInfo = CommonModel :: calcAddOrderWeight($obj_order_detail_data);//计算重量和包材
			//var_dump($calcInfo); exit;
			$insert_orderData['orderData']['calcWeight'] = $calcInfo[0];
			$insert_orderData['orderData']['pmId'] = $calcInfo[1];
			//var_dump($insert_orderData); exit;
			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insert_orderData,1);//计算运费
			//var_dump($calcShippingInfo); exit;
			$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
			$insert_orderData['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];
			
			/*$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
			//print_r($interceptInfo); exit;
			$orderData['orderData']['orderStatus'] = $interceptInfo['orderStatus'];
			$orderData['orderData']['orderType'] = $interceptInfo['orderType'];*/
						
			//$totalweight 	= recalcorderweight($val, $ebay_packingmaterial);
			if(in_array($GLOBAL_EBAY_ACCOUNT[$insert_orderData['orderData']['accountId']], array('dresslink.com','cndirect.com'))){
				//$ordershipfee = $calcShippingInfo['fee'];
				$splitweight = $calcInfo[0];
				$splitcosts = $calcInfo[2];
				$splititemprices = $calcInfo[3];
				$actualTotal = round(($splititemprices/$itemprices)*$_actualTotal,3);//成本价比例拆分
				$actualShipping = round(($splitweight/$realweight)*$_actualShipping,3);//运费按重量拆分
				
				$insert_orderData['orderData']['actualTotal'] = $actualTotal;
				$insert_orderData['orderData']['actualShipping'] = $actualShipping;
				//echo $val."--------".$splititemprices."------------".$itemprices."<br>";
				//$split_log .= "超重订单 $ebay_id 拆分出新订单 $insert_ebay_id \r\n";
			}
			//var_dump($insert_orderData);
			if($_spitId = OrderAddModel :: insertAllOrderRowNoEvent($insert_orderData)){
				//echo $split_log .= 'insert success!' . "\n"; exit;
				//var_dump($_mainId,$_spitId); exit;
				if(!OrderLogModel::insertOrderLog($_spitId, 'INSERT ORDER')){
					BaseModel :: rollback();
					self :: $errCode = '001';
					self :: $errMsg = "split error!";
					return false;
				}
				if(!OrderRecordModel::insertSpitRecords($_mainId,$_spitId)){
					BaseModel :: rollback();
					self :: $errCode = '002';
					self :: $errMsg = "split error!";
					return false;
				}
			}else{
				$split_log .= 'insert error!' . "\n";
				BaseModel :: rollback();
				self :: $errCode = '003';
				self :: $errMsg = "split error!";
				return false;
			}
			if(!OrderindexModel::deleteOrderData($tableName, $where)){
				self :: $errCode = '004';
				self :: $errMsg = "split error!";
				return false;
			}
			if(!OrderLogModel::insertOrderLog($_mainId, 'DELETE ORDER')){
				BaseModel :: rollback();
				self :: $errCode = '005';
				self :: $errMsg = "split error!";
				return false;
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
		}
		self :: $errCode = '200';
		self :: $errMsg = "split success!";
		return true;
	}
	
	/*
	 * 自动拆分,只支持单个订单，在调用层在用事务关联
	 */
	public static function handSplitOrder($omOrderId) {
		!self::$dbConn ? self::initDB() : null;
		global $memc_obj; //调用memcache获取sku信息
		$mctime = time();
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId.' and is_delete = 0 and storeId = 1';
		$orderData = OrderindexModel::showOrderList($tableName, $where);
		$orderDetail = $orderData[$omOrderId]['orderDetail'];
		$obj_order_data = $orderData[$omOrderId]['orderData'];
		$orderExtenData = $orderData[$omOrderId]['orderExtenData'];
		$orderUserInfoData = $orderData[$omOrderId]['orderUserInfoData'];
		$_actualTotal = $obj_order_data['actualTotal'];
		$_actualShipping = $obj_order_data['actualShipping'];
		$_platformId = $obj_order_data['platformId'];
		$_mainId = $obj_order_data['id'];
		$_transportId = $obj_order_data['transportId'];
		
		if(!in_array($_platformId, array(1,5,8,10,16))){//预留，和独立出来
			self :: $errCode = '0001';
			self :: $errMsg = "该平台还未开通超重拆分功能！";
			return false;
		}
		if($_transportId==6){
			self :: $errCode = '0002';
			self :: $errMsg = "EUB 订单不允许超重拆分！";
			return false;
		}
		
		/*$order = SplitOrderModel::selectOrder($orderid);
		$details = SplitOrderModel::selectDetail($orderid);
		$userinfo = SplitOrderModel::selectUser($orderid);
		
		$platformId = $order['platformId'];
		$plateform = SplitOrderModel::selectplatform($platformId); 
		$table = "om_unshipped_order_extension_".$plateform;
		
		$extension = SplitOrderModel::selectExtension($table,$orderid);
		$warehouse = SplitOrderModel::selectWarehouse($orderid);*/
		if(!$obj_order_data){
			self::$errCode = 602;
			self::$errMsg  = "此订单不存在！";
			return false;
		}
		if($obj_order_data['isSplit']==2){
			self::$errCode = 603;
			self::$errMsg  = "此订单是拆分产生的订单，不能再被拆分！";
			return false;
		}
		
		//$omAvailableAct = new OmAvailableAct();
		//$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', ' WHERE is_delete=0 AND platformId in(1,5) ');
		
		$weightlists = array();
		$skuinfo = array();
		$goods_sn_nums = 0;
		$shippfee_arr = array();
		foreach($orderDetail as $k=>$f){
			$sku = trim($f['orderDetailData']['sku']);
			$amount = $f['orderDetailData']['amount'];
			$shippingFee = $f['orderDetailData']['shippingFee'];
			$goods_sn_nums += $amount;
			$shippfee_arr[$sku] = round($shippingFee/$amount,3);//单个料号的运费
			$skuinfo[$sku] = $f;
			for($i=1; $i<=$amount; $i++){
				$var = $sku;
				$oneskuweight = CommonModel::calcOnlySkuWeight($var, 1);//一个sku的重量
				$weightlists[$var][] = $oneskuweight[0];
			}
		}
		//var_dump($weightlists); exit;
		if($goods_sn_nums <= 1){
			self :: $errCode = '0010';
			self :: $errMsg = "只有一个料号组成，不允许超重拆分";
			return false;
		}
		
		//计算运费和总价
		/*$shippingfee = 0; 
		foreach($orderDetail as $key=>$value){
			if(in_array($value['sku'],$sku_arr)){
				$price += $value['itemPrice'];
				$shippingfee += $value['shippingfee'];
				//$result = $memc_obj->get_extral("sku_info_".$value['sku']);
				$weight += $result['weight'];
			}	
		}*/
		
		BaseModel::begin();
		
		//先插入订单
		/*foreach($order as $key=>$value){
			
			if($key=='id'){
				continue;
			}
			if($key=='isSplit'){
				$new_order[$key] = 2;
				continue;
			}
			if($key=='calcWeight'){
				$new_order[$key] = $weight;
				continue;
			}
			if($key=='calcShipping'){
				$new_order[$key] = $shippingfee;
				continue;
			}
			$new_order[$key] = $value;
		}*/
		
		foreach($new_order as $key=>$value){
			if(is_numeric($value)){
				$sql[] = "{$key}={$value}";
			}else{
				$sql[] = "{$key}='{$value}'";
			}
		}
		$sql = implode(",",$sql);
		$id = splitOrderModel::insertOrder($sql,$userId);
		if(!$id){
			self::$errCode = 604;
			self::$errMsg  = "拆分订单订单插入失败！";
			BaseModel::rollback();
			return false;
		}
		
		//插入订单明细信息
		foreach($details as $nums=>$detail){
			$new_detail = array();
			if(!in_array($detail['sku'],$sku_arr)){
				continue;
			}
			foreach($detail as $key=>$value){
				if($key=='id'){
					continue;
				}
				$new_detail[$key] = $value;
				if($key=='omOrderId'){
					$new_detail[$key] = $id;
				}				
			}
		
			$sql = array();
			
			foreach($new_detail as $key=>$value){
				if($key=='createdTime'){
					$sql[] = "{$key}=".time()." ";
					continue;
				}
				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
			}
			
			$sql = implode(",",$sql);
			$msg = splitOrderModel::insertDetail($sql,$userId);
			if(!$msg){
				self::$errCode = 605;
				self::$errMsg  = "插入拆分订单明细信息失败！";
				BaseModel::rollback();
				return false;
			}
		}
		
		$new_user = array();
		//插入用户信息
		foreach($userinfo as $key=>$value){
			$new_user[$key] = $value;
			if($key=='omOrderId'){
				$new_user[$key] = $id;
			}
			
		}
		$sql = array();
		foreach($new_user as $key=>$value){
			if(is_numeric($value)){
				$sql[] = "{$key}={$value}";
			}else{
				$sql[] = "{$key}='{$value}'";
			}
		}
		$sql = implode(",",$sql);
		$msg = splitOrderModel::insertUser($sql,$userId);
		if(!$msg){
			self::$errCode = 606;
			self::$errMsg  = "插入拆分订单用户信息失败！";
			BaseModel::rollback();
			return false;
		}
		
		//插入订单扩展信息
		$new_extension = array();
		foreach($extension as $key=>$value){
			if($key=='omOrderId'){
				$new_extension[$key] = $id;
				continue;
			}
			$new_extension[$key] = $value;
		}
		$sql = array();
		foreach($new_extension as $key=>$value){

			if(is_numeric($value)){
				$sql[] = "{$key}={$value}";
			}else{
				$sql[] = "{$key}='{$value}'";
			}
			
		}
		$sql = implode(",",$sql);
		$msg = splitOrderModel::insertExtension($table,$sql,$userId);
		if(!$msg){
			self::$errCode = 607;
			self::$errMsg  = "插入订单扩展信息失败！";
			BaseModel::rollback();
			return false;
		}
		
		
		//插入复制订单仓库信息
		if($warehouse){
			$new_warehouse = array();
			foreach($warehouse as $key=>$value){
				if($key=='omOrdeId'){
					$new_warehouse[$key] = $id;
					continue;
				}
				$new_warehouse[$key] = $value;
			}
			$sql = array();
			foreach($new_warehouse as $key=>$value){

				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
				
			}
			$sql = implode(",",$sql);
			$msg = splitOrderModel::insertWarehouse($sql,$userId);
			if(!$msg){
				self::$errCode = 608;
				self::$errMsg  = "插入复制订单仓库信息失败！";
				BaseModel::rollback();
				return false;
			}
		}
		
		//完全插入成功再插入拆分记录和订单操作记录
		
		$msg = splitOrderModel::insertSplitRecord($orderid,$id,$userId);
		if(!$msg){
			self::$errCode = 609;
			self::$errMsg  = "插入复制订单记录失败！";
			BaseModel::rollback();
			return false;
		}
		
		//最后修改原订单为拆分订单订单
		//如果无料号，若无就删除原订单
		if($type==1){
			$msg = splitOrderModel::updateOrder($orderid);
			if(!$msg){
				self::$errCode = 610;
				self::$errMsg  = "修改原订单失败！";
				BaseModel::rollback();
				return false;
			}
		}
		
		BaseModel::commit();
		return true;
	}
	
	public static function overWeightSplitB2B($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		global $memc_obj; //调用memcache获取sku信息
		$mctime = time();
		//var_dump($moOrderIdArr);
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId.' and is_delete = 0 and storeId = 1';
		$orderData = OrderindexModel::showOrderList($tableName, $where);
		$orderDetail = $orderData[$omOrderId]['orderDetail'];
		$obj_order_data = $orderData[$omOrderId]['orderData'];
		$orderExtenData = $orderData[$omOrderId]['orderExtenData'];
		$orderUserInfoData = $orderData[$omOrderId]['orderUserInfoData'];
		$_actualTotal = $obj_order_data['actualTotal'];
		$_actualShipping = $obj_order_data['actualShipping'];
		$_platformId = $obj_order_data['platformId'];
		$_mainId = $obj_order_data['id'];
		$_transportId = $obj_order_data['transportId'];
		

		//var_dump($orderDetail); exit;
		$omAvailableAct = new OmAvailableAct();
		$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', ' WHERE is_delete=0 AND platformId in(1,5) ');
		
		$weightlists = array();
		$skuinfo = array();
		$goods_sn_nums = 0;
		$shippfee_arr = array();
		$ebay_total_be = 0;
		foreach($orderDetail as $k=>$f){
			$sku = trim($f['orderDetailData']['sku']);
			$amount = $f['orderDetailData']['amount'];
			$shippingFee = $f['orderDetailData']['shippingFee'];
			$goods_sn_nums += $amount;
			$ebay_total_be += $f['orderDetailData']['amount']*$f['orderDetailData']['itemPrice'];
			$shippfee_arr[$sku] = round($shippingFee/$amount,3);//单个料号的运费
			$skuinfo[$sku] = $f;
			for($i=1; $i<=$amount; $i++){
				$var = $sku;
				$oneskuweight = CommonModel::calcOnlySkuWeight($var, 1);//一个sku的重量
				$weightlists[$var][] = $oneskuweight[0];
			}
		}
		$rate = $_actualTotal/$ebay_total_be;
		//var_dump($weightlists); exit;
		if($goods_sn_nums <= 1){
			self :: $errCode = '0020';
			self :: $errMsg = "只有一个料号组成，不允许超重拆分";
			return false;
		}
		//echo "==========="; exit;
		$keyarray = array();
		$keyarrays = array();
		$checkweight = 0;
		$arrinfo = CommonModel::calcNowOrderWeight($omOrderId);
		//var_dump($arrinfo); exit;
		$realweight = $arrinfo[0];
		$realcosts = $arrinfo[2];
		$itemprices = $arrinfo[3];
		
		foreach($weightlists as $wk => $wv){
			foreach($wv as $weightlist){
				$checkweight += $weightlist;
				if($checkweight>1.85){
					$keyarrays[] = $keyarray;
					$keyarray = array();
					$checkweight = $weightlist;
					$keyarray[$wk][] = $wk;
				}else{
					$keyarray[$wk][] = $wk;
				}
			}
		}
		if(!empty($keyarray)){
			$keyarrays[] = $keyarray;
		}
		//var_dump($keyarrays); exit;
		BaseModel :: begin(); //开始事务
		$insert_orderData = array();
		foreach($keyarrays as $keyarray){
			$ebay_total = 0;
			$totalweight = 0;
			$insert_ebay_ids = array();
			//var_dump($skuinfo); echo "<br>";
			foreach($keyarray as $k => $kav){
				//var_dump($skuinfo[$k]['orderDetailData']['itemPrice'], count($kav));
				//$ebay_total += ($skuinfo[$k]['orderDetailData']['itemPrice'] + $shippfee_arr[$k]) * count($kav);
				$ebay_total += $skuinfo[$k]['orderDetailData']['itemPrice']*count($kav);
			}
			$ebay_total = $rate*$ebay_total;
			$shipfee = 0;
			//$val = generateOrdersn();
			$insert_obj_order_data = $obj_order_data;
			unset($insert_obj_order_data['id']);
			$insert_obj_order_data['actualTotal'] = $ebay_total;
			$insert_obj_order_data['orderType'] = C('STATEPENDING_OWDONE');
			$insert_obj_order_data['orderAddTime'] = $mctime;
			$insert_obj_order_data['isSplit'] = 2;
			
			$insert_orderExtenData = $orderExtenData;
			unset($insert_orderExtenData['id']);
			$insert_orderUserInfoData = $orderUserInfoData;
			unset($insert_orderUserInfoData['id']);
			
			$insert_orderData = array('orderData' => $insert_obj_order_data,
								'orderExtenData' => $insert_orderExtenData,					  
								'orderUserInfoData' => $insert_orderUserInfoData
							   );
			/*$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, create_date) values ('$recordnumber', '$ebay_id', '$insert_ebay_id', '".date("Y-m-d H:i:s")."')";
			$split_log .= "添加主定单和拆分订单到关系表中\r\n".$sql ."\r\n";
			$dbcon->execute($sql) or die("Fail : $sql");*/
			$obj_order_detail_data = array();
			foreach($keyarray as $k => $kav){
				$sku = $k;
				$amount = count($kav);
				
				$insert_orderDetailData = $skuinfo[$k]['orderDetailData'];
				unset($insert_orderDetailData['id']);
				$insert_orderDetailData['sku'] = strtoupper($sku);
				$insert_orderDetailData['amount'] = $amount;
				$insert_orderDetailData['createdTime'] = $mctime;
				if(isset($shippfee_arr[$sku])){
					$insert_orderDetailData['shippingFee'] = $shippfee_arr[$sku]*$amount;//相同料号运费拆分
				}
				
				$insert_orderDetailExtenData = $skuinfo[$k]['orderDetailExtenData'];
				unset($insert_orderDetailExtenData['id']);
				
				$obj_order_detail_data[] = array('orderDetailData' => $insert_orderDetailData,			
												'orderDetailExtenData' => $insert_orderDetailExtenData,
												);
			}
			$insert_orderData['orderDetail'] = $obj_order_detail_data;
			//echo "<pre>";
			//var_dump($obj_order_detail_data); exit;
			$calcInfo = CommonModel :: calcAddOrderWeight($obj_order_detail_data);//计算重量和包材
			//var_dump($calcInfo); exit;
			$insert_orderData['orderData']['calcWeight'] = $calcInfo[0];
			$insert_orderData['orderData']['pmId'] = $calcInfo[1];
			//var_dump($insert_orderData); exit;
			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insert_orderData,1);//计算运费
			//var_dump($calcShippingInfo); exit;
			$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
			$insert_orderData['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];
			
			/*$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
			//print_r($interceptInfo); exit;
			$orderData['orderData']['orderStatus'] = $interceptInfo['orderStatus'];
			$orderData['orderData']['orderType'] = $interceptInfo['orderType'];*/
						

			if($_spitId = OrderAddModel :: insertAllOrderRowNoEvent($insert_orderData)){
				//echo $split_log .= 'insert success!' . "\n"; exit;
				//var_dump($_mainId,$_spitId); exit;
				if(!OrderLogModel::insertOrderLog($_spitId, 'INSERT ORDER')){
					BaseModel :: rollback();
					self :: $errCode = '0021';
					self :: $errMsg = "split error!";
					return false;
				}
				if(!OrderRecordModel::insertSpitRecords($_mainId,$_spitId)){
					BaseModel :: rollback();
					self :: $errCode = '0022';
					self :: $errMsg = "split error!";
					return false;
				}
			}else{
				$split_log .= 'insert error!' . "\n";
				BaseModel :: rollback();
				self :: $errCode = '0023';
				self :: $errMsg = "split error!";
				return false;
			}
			if(!OrderindexModel::deleteOrderData($tableName, $where)){
				self :: $errCode = '0024';
				self :: $errMsg = "split error!";
				return false;
			}
			if(!OrderLogModel::insertOrderLog($_mainId, 'DELETE ORDER')){
				BaseModel :: rollback();
				self :: $errCode = '0025';
				self :: $errMsg = "split error!";
				return false;
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
		}
		self :: $errCode = '200';
		self :: $errMsg = "split success!";
		return true;

		
	}
	/*
	 * 
	*/
	public static function selectOrder($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order WHERE id={$orderid} AND is_delete=0";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectUser($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_userInfo WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectDetail($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_detail WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}

	public static function selectplatform($platformId){
		self::initDB();
		$sql = "SELECT platform FROM om_platform WHERE id={$platformId}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret['platform'];
		}else{
			return false;
		}
	}
	public static function selectExtension($table,$orderid){
		self::initDB();
		$sql = "SELECT * FROM {$table} WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectWarehouse($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_warehouse WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectStatus($status){
		self::initDB();
		$sql = "SELECT * FROM om_status_menu WHERE id={$status}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function insertOrder($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$id = mysql_insert_id();
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return $id;
		}else{
			return false;
		}
	}
	public static function insertUser($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_userInfo SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertDetail($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_detail SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertExtension($table,$sql,$userId){
		self::initDB();
		$sql = "INSERT INTO {$table} SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertWarehouse($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_warehouse SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertSplitRecord($orderid,$id,$userId){
		self::initDB();
		$sql = "INSERT INTO om_records_splitOrder SET main_order_id={$orderid},split_order_id={$id},creator={$userId},createdTime=".time()." ";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	public static function insertOperationLog($sql,$userId,$type){
		self::initDB();
		$sql = addslashes($sql);
		$sql = "INSERT INTO `om_operation_log_2013-09_2013-12` SET operatorId={$userId},`sql`='{$sql}',type={$type},createdTime=".time()." ";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	public static function updateOrder($orderid){
		self::initDB();
		$sql = "UPDATE om_unshipped_order SET isSplit=1,is_delete=1 WHERE id={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	
}
?>