<?php
/*
 * 名称：OrderModifyAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * */
/*include_once WEB_PATH.'model/orderModify.model.php';
include_once WEB_PATH.'model/omAvailable.model.php';
include_once WEB_PATH.'model/common.model.php';
include_once WEB_PATH.'lib/functions.php';*/
class OrderModifyAct{
	public static $errCode = 0;
	public static $errMsg = '';
	
	//获取对应订单详情
	public function act_getModifyOrderList($id,$ostatus,$otype,$storeId = 1){
		/*$id			=	$_REQUEST['orderid'];
		$ostatus	=	$_REQUEST['ostatus'];
		$otype		=	$_REQUEST['otype'];*/
		$where = ' WHERE da.is_delete = 0 AND da.storeId = '.$storeId.' AND da.orderStatus = '.$ostatus;
		if($otype != ''){
			$where .=	' AND da.orderType	= '.$otype;
		}
		$where .=	' AND da.id	= '.$id;
		/*$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);*/
		$tableName = 'om_unshipped_order';
		$orderlist = OrderindexModel::showSearchOrderList($tableName, $where);
		//var_dump($orderlist);
		$data['order'] = $orderlist[$id];
		//var_dump($orderlist); exit;
		
		/*$price	=	array();
		$data['order']	=	OrderModifyModel::index($$tableName,$where);
		foreach($data['order'] as $k => $v){
			$data['order'][$k]['ordersTime']	=	date("Y-m-d H:i:s",$v['ordersTime']);
			$data['order'][$k]['paymentTime']	=	date("Y-m-d H:i:s",$v['paymentTime']);
			if($data['order'][$k]['createdTime'] != ''){
				$data['order'][$k]['createdTime']	=	date("Y-m-d H:i:s",$v['createdTime']);
			}
			$goodinfo	=	GoodsModel::getSkuinfo($v['sku']);
			$data['order'][$k]['price']			=	$goodinfo['goods_cost'];
		}
		*/
		/*$data['combinePackage']	=	'';
		
		switch($data['order']['combinePackage']){
			case 0:
				$data['combinePackage']	=	'正常订单(未合并包裹)';
				break;
			case 1:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE main_order_id = "'.$data['order']['id'].'"');
				$data['combinePackage']	.=	'该订单是合并包裹'.$result[0]['main_order_id'].'的一部分';
				break;
			case 2:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE split_order_id = "'.$data['order']['id'].'"');
				$data['combinePackage']	.=	'该订单是合并包裹'.$result[0]['main_order_id'].'的一部分';
				break;
			default:
				break;
		}
		
		$data['splitMessage']	=	'';

		switch($data['order']['isSplit']){
			case 0:
				$data['splitMessage']	=	'正常订单(未拆分)';
				break;
			case 1:
				$result	=	OmAvailableModel::getTNameList(' `om_records_splitOrder` ',' * ',' WHERE main_order_id = "'.$data['order']['id'].'"');
				$data['splitMessage']	.=	'该订单拆分成'.count($result).'个订单';
				break;
			case 2:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE split_order_id = "'.$data['order']['id'].'"');
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE main_order_id = "'.$result[0]['main_order_id'].'"');
				$data['splitMessage']	.=	'该订单与'.count($result).'个订单由订单'.$result[0]['main_order_id'].'拆分而来';
				break;
			default:
				break;
		}*/
		
		//$data['transport']	=	CommonModel::getCarrierList();
		//$data['materials']	=	GoodsModel::getMaterInfo();
		$data['operationLog']=	OmAvailableModel::getTNameList('`om_order_log`',' * ',' WHERE omOrderId = '.$id);
		if(empty($data['operationLog'])){
			$data['operationLog'] = array(
									array(
										'note'	=>	'暂无操作日志',
										'createdTime'	=>	'',
									)
								);
		} else {
			foreach($data['operationLog'] as $k => $v){
				$data['operationLog'][$k]['createdTime'] = date("Y-m-d H:i:s",$v['createdTime']);
			}
		}
		$data['currency'] = OmAvailableModel :: getTNameList('om_currency',' * ',' WHERE 1');
		//var_dump($data); exit;
		self::$errCode	=	OrderindexModel::$errCode;
		self::$errMsg	=	OrderindexModel::$errMsg;
		return $data;
	}
	
	public function act_index(){
		$id			=	$_REQUEST['orderid'];
		$ostatus	=	$_REQUEST['ostatus'];
		$otype		=	$_REQUEST['otype'];
		$storeId = 1;
		$where = ' WHERE da.is_delete = 0 AND da.storeId = '.$storeId.' AND da.orderStatus = '.$ostatus;
		if($otype != ''){
			$where .=	' AND da.orderType	= '.$otype;
		}
		$where .=	' AND da.id	= '.$id;
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);
		
		$orderlist = OrderindexModel::showSearchOrderList($tableName, $where);
		$data['order'] = $orderlist[$id];
		//var_dump($orderlist); exit;
		
		/*$price	=	array();
		$data['order']	=	OrderModifyModel::index($$tableName,$where);
		foreach($data['order'] as $k => $v){
			$data['order'][$k]['ordersTime']	=	date("Y-m-d H:i:s",$v['ordersTime']);
			$data['order'][$k]['paymentTime']	=	date("Y-m-d H:i:s",$v['paymentTime']);
			if($data['order'][$k]['createdTime'] != ''){
				$data['order'][$k]['createdTime']	=	date("Y-m-d H:i:s",$v['createdTime']);
			}
			$goodinfo	=	GoodsModel::getSkuinfo($v['sku']);
			$data['order'][$k]['price']			=	$goodinfo['goods_cost'];
		}
		$data['combinePackageMessage']	=	'';
		
		switch($data['order'][0]['combinePackage']){
			case 0:
				$data['combinePackageMessage']	=	'正常订单(未合并包裹)';
				break;
			case 1:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE main_order_id = "'.$data['order'][0]['id'].'"');
				$data['combinePackageMessage']	.=	'该订单是合并包裹'.$result[0]['main_order_id'].'的一部分';
				break;
			case 2:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE split_order_id = "'.$data['order'][0]['id'].'"');
				$data['combinePackageMessage']	.=	'该订单是合并包裹'.$result[0]['main_order_id'].'的一部分';
				break;
			default:
				break;
		}

		$data['isSplitMessage']	=	'';

		switch($data['order'][0]['isSplit']){
			case 0:
				$data['isSplitMessage']	=	'正常订单(未拆分)';
				break;
			case 1:
				$result	=	OmAvailableModel::getTNameList(' `om_records_splitOrder` ',' * ',' WHERE main_order_id = "'.$data['order'][0]['id'].'"');
				$data['isSplitMessage']	.=	'该订单拆分成'.count($result).'个订单';
				break;
			case 2:
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE split_order_id = "'.$data['order'][0]['id'].'"');
				$result	=	OmAvailableModel::getTNameList(' `om_records_combinePackage` ',' * ',' WHERE main_order_id = "'.$result[0]['main_order_id'].'"');
				$data['isSplitMessage']	.=	'该订单与'.count($result).'个订单由订单'.$result[0]['main_order_id'].'拆分而来';
				break;
			default:
				break;
		}*/
		
		//$data['transport']	=	CommonModel::getCarrierList();
		//$data['materials']	=	GoodsModel::getMaterInfo();
		
		$data['operationLog']=	OmAvailableModel::getTNameList('`om_order_log`',' * ',' WHERE omOrderId = '.$id);
		if(empty($data['operationLog'])){
			$data['operationLog'] = array(
									array(
										'note'	=>	'暂无操作日志',
										'createdTime'	=>	'',
									)
								);
		} else {
			foreach($data['operationLog'] as $k => $v){
				$data['operationLog'][$k]['createdTime'] = date("Y-m-d H:i:s",$v['createdTime']);
			}
		}
		//$data['currency'] = OmAvailableModel :: getTNameList('om_currency',' * ',' WHERE 1');
		//var_dump($data); exit;
		self::$errCode	=	OrderindexModel::$errCode;
		self::$errMsg	=	OrderindexModel::$errMsg;
		return $data;
	}
	
	//更新订单状态
	public function act_modify(){
		$skuNumber		=	0;
		$skuAmount		=	0;
		$skuSql			=	'';
		$detailArr		=	array();
		$num			=	$_REQUEST['num'];
		$orderId		=	$_REQUEST['orderId'];
		$detailId		=	$_REQUEST['detailId'];
		$transport		=	$_REQUEST['transport'];
		$materials		=	$_REQUEST['materials'];
		$skuString		=	$_REQUEST['skuString'];
		$numberString	=	$_REQUEST['numberString'];


		$countryName	=	$_REQUEST['countryName'];
		$state			=	$_REQUEST['state'];
		$city			=	$_REQUEST['city'];
		$street			=	$_REQUEST['street'];
		$address2		=	$_REQUEST['address2'];
		$address3		=	$_REQUEST['address3'];
		$landline		=	$_REQUEST['landline'];
		$phone			=	$_REQUEST['phone'];
		$zipCode		=	$_REQUEST['zipCode'];
		$currency		=	$_REQUEST['currency'];
		$notes			=	$_REQUEST['notes'];
		
		
				
		$detailIdArr	=	explode(',',$detailId);
		$skuArr			=	explode(',',$skuString);
		$numberArr		=	explode(',',$numberString);
		for($i = 0;$i < $num ;$i++){
			$detailArr[$i]['id']		=	$detailIdArr[$i];
			$detailArr[$i]['sku']	=	$skuArr[$i];
			$detailArr[$i]['amount']	=	$numberArr[$i];
		}
		$orderData 	= 	OmAvailableModel :: getTNameList(' om_unshipped_order ',' * ',' WHERE id = '.$orderId);
		$set	=	' SET transportId = "'.$transport.'" , pmId	=	"'.$materials.'" ';
		$where	=	' WHERE id = '.$orderId;
		$ret1 = OmAvailableModel :: updateTNameRow('om_unshipped_order',$set,$where);
		if($ret1	===	false){
			self :: $errCode	= OmAvailableModel :: $errCode;
			self :: $errMsg		= OmAvailableModel :: $errMsg;
			return false;
		}
		foreach($detailArr as $k => $v){
			$skuData = 	OmAvailableModel :: getTNameList(' om_unshipped_order_detail ',' * ',' WHERE id = '.$v['id']);
			$set	=	' SET sku = "'.$v['sku'].'" , amount = "'.$v['amount'].'" ';
			$where	=	' WHERE id = '.$v['id'];
			$ret2 = OmAvailableModel :: updateTNameRow('om_unshipped_order_detail',$set,$where);
			if($ret2	===	false){
				self :: $errCode	= OmAvailableModel :: $errCode;
				self :: $errMsg		= OmAvailableModel :: $errMsg;
				return false;
			}
			if($ret2 !==	0){
				$skuSql	.=	' UPDATE om_unshipped_order_detail SET sku = \"'.$v['sku'].'\" , amount = \"'.$v['amount'].'\" WHERE id = '.$v['id'];	
				if($skuData[0]['sku']	!=	$v['sku']){
					$skuNumber	=	1;
				}
				if($skuData[0]['amount']	!=	$v['amount']){
					$skuAmount	=	1;
				}
			}
		}
		
		$set	=	' SET countryName = "'.$countryName.'" , state = "'.$state.'" , city	=	"'.$city.'" , street	=	"'.$street.'" , address2	=	"'.$address2.'" , address3	=	"'.$address3.'" , landline	=	"'.$landline.'" , phone	=	"'.$phone.'" , zipCode	=	"'.$zipCode.'" , currency	=	"'.$currency.'" ';
		$where	=	' WHERE omOrderId = '.$orderId;
		$ret3 = OmAvailableModel :: updateTNameRow('om_unshipped_order_userInfo',$set,$where);
		if($ret3	===	false){
			self :: $errCode	= OmAvailableModel :: $errCode;
			self :: $errMsg		= OmAvailableModel :: $errMsg;
			return false;
		}
		if($ret3 !== 0){
			$userInfoSql	.=	' UPDATE om_unshipped_order_userInfo  SET countryName = \"'.$countryName.'\" , state = \"'.$state.'\" , city	=	\"'.$city.'\" , street	=	\"'.$street.'\" , address2	=	\"'.$address2.'\" , address3	=	\"'.$address3.'\" , landline	=	\"'.$landline.'\" , phone	=	\"'.$phone.'\" , zipCode	=	\"'.$zipCode.'\" , currency	=	\"'.$currency.'\" WHERE omOrderId = '.$orderId;	
		}

		$currencyData = 	OmAvailableModel :: getTNameList(' om_order_notes ',' * ',' WHERE omOrderId = '.$orderId);
		if(!empty($currencyData)){
			$set	=	' SET content = "'.$notes.'" , userId = "'.$_SESSION['sysUserId'].'" ,createdTime = "'.time().'" ';
			$ret4 = OmAvailableModel :: updateTNameRow('om_order_notes',$set,$where);
			if($ret4	===	false){
				self :: $errCode	= OmAvailableModel :: $errCode;
				self :: $errMsg		= OmAvailableModel :: $errMsg;
				return false;
			}
			if($ret4 !== 0){
				$notesSql	.=	' UPDATE om_unshipped_order_userInfo  SET notes = \"'.$notes.'\" , userId = \"'.$_SESSION['sysUserId'].'\" ,createdTime = \"'.time().'\"   WHERE omOrderId = '.$orderId;	
			}
		} else {
			$addNotes	=	' (omOrderId,content,userId,createdTime) VALUES ("'.$orderId.'","'.$notes.'","'.$_SESSION['sysUserId'].'","'.time().'")';
			$ret4 = OmAvailableModel :: addTNameRow('om_order_notes',$addNotes);
			if($ret4	===	false){
				self :: $errCode	= OmAvailableModel :: $errCode;
				self :: $errMsg		= OmAvailableModel :: $errMsg;
				return false;
			}
			if($ret4 !== 0){
				$notesSql	.=	' INSERT INTO om_order_notes (omOrderId,content,userId,createdTime) VALUES (\"'.$orderId.'\",\"'.$notes.'\",\"'.$_SESSION['sysUserId'].'\",\"'.time().'\")';	
			}
		}
		
		
		if(($ret1 !== 0)||($ret2 !== 0)||($ret3 !== 0)||($ret4 !== 0)){
			$value	=	' (`operatorId`,`omOrderId`,`oldStatus`,`newStatus`,`sql`,`note`,`createdTime`) VALUES (';
			$value	.=	'"'.$_SESSION['sysUserId'].'",';
			$value	.=	'"'.$orderId.'",';
			$status	=	($orderData[0]['orderType'] == '') ? $orderData[0]['orderStatus'] : $orderData[0]['orderType'];
			$value	.=	'"'.$status.'",';
			$value	.=	'"'.$status.'",';
			$value	.=	'"UPDATE om_unshipped_order  SET transportId = \"'.$transport.'\" , pmId	=	\"'.$materials.'\" WHERE id = \"'.$orderId.'\" '.$skuSql.'",';
			$value	.=	' "修改了';
			if($orderData[0]['transportId'] !=	$transport){
				$value	.=	' 运输方式 ';
			}
			if($orderData[0]['pmId'] !=	$materials){
				$value	.=	' 包装材料 ';
			}
			if($skuNumber	==	1){
				$value	.=	' sku ';
			}
			if($skuAmount	==	1){
				$value	.=	' 数量 ';
			}
			if($ret3 !== 0){
				$value	.=	' 运费详情 ';
			}
			if($ret4 !== 0){
				$value	.=	' 备注 ';
			}
			$value	.=	'" ,';
			$value	.=	'"'.time().'")';
			//$result  =	OmAvailableModel::addTNameRow(' `om_order_log_2013-09_2013-12` ',$value);
		}
		
		return true;
	}
	
	//判断订单是否锁定
	public function act_judgeLock(){
		$id	=	$_REQUEST['id'];
		$ret	=	OrderModifyModel::judegLock($id);
		if(!$ret){
			self :: $errCode	= OrderModifyModel :: $errCode;
			self :: $errMsg		= OrderModifyModel :: $errMsg;
			return false;
		} else {
			if($ret[0]['isLock'] != '1'){
				return true;
			} else {
				if($ret[0]['lockUser']	!=	$_SESSION['sysUserId']){
					$account	=	UserModel::getUsernameById($ret[0]['lockUser']);
					self :: $errCode = "001";
					self :: $errMsg = "订单已被用户".$account.'于'.date("Y-m-d H:i",$ret[0]['lockTime']).'锁定！';
					return false; //失败则设置错误码和错误信息， 返回false
				} else {
					return true;
				}
			}
		}
	}
	
	//批量移动
	public function act_batchMove(){
		$type	=	$_REQUEST['type'];
		$ostatus=	$_REQUEST['ostatus'];
		$otype	=	$_REQUEST['otype'];
		$valuestr	=	$_REQUEST['valuestr'];
		$sysUserId = $_SESSION['sysUserId'];
		//var_dump($valuestr); exit;
		$where = ' WHERE 1 ';
		if(!empty($valuestr)){
			$idArr = explode(',', $valuestr);
			$where .= ' AND id in ('.join(',', $idArr).') ';	
		}
		if($ostatus){
			$where .= ' AND orderStatus = '.$ostatus;
		}
		if($otype){
			$where .= ' AND orderType = '.$otype;
		}
		if (empty($ostatus)||empty($otype)){
			self :: $errCode = 500;
			self :: $errMsg = "不能在状态或类别为ALL的状态下进行批量移动！";
			return false;
		}
		$accountacc = $_SESSION['accountacc'];
		//var_dump($accountacc);
		if($accountacc){
			$where .= ' AND ('.$accountacc.') ';
		}
		$update_arr = array();
		BaseModel :: begin(); //开始事务
		if($type == 1){
			$ProductStatus = new ProductStatus();
			$UserCompetenceAct = new UserCompetenceAct();
			$batch_otype_val = $_REQUEST['batch_otype_val'];
			$batch_ostatus_val = $_REQUEST['batch_ostatus_val'];
			$visible_movefolder = $UserCompetenceAct->act_getInStatusIds($otype, $sysUserId);
			if($batch_ostatus_val == 900){
				self :: $errCode = 400;
				self :: $errMsg = "注意不能直接移动到 仓库发货 ，需要申请打印";
				return false;
			}
			if($otype && !in_array($batch_otype_val,$visible_movefolder)){
				self :: $errCode = 500;
				self :: $errMsg = "无权限从 {$otype} 移动到 {$batch_otype_val}";
				return false;
			}
			$update_arr['orderStatus'] = $batch_ostatus_val;
			$update_arr['orderType'] = $batch_otype_val;
			
			if($ProductStatus->updateSkuStatusByOrderStatus($idArr, $batch_ostatus_val, $batch_otype_val)){
				if($ret = OrderModifyModel::batchMove($update_arr, $where)){
						
				}else{
					BaseModel :: rollback();
				}
			}else{
				BaseModel :: rollback();	
			}
		}else if($type == 2){
			$batch_transport_val = $_REQUEST['batch_transport_val'];
			$update_arr['transportId'] = $batch_transport_val;
			$ret	=	OrderModifyModel::batchMove($update_arr, $where);
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self :: $errCode = OrderModifyModel :: $errCode;
		self :: $errMsg = OrderModifyModel :: $errMsg;
		/*if(!$ret){
			return false;
		}*/
		return $ret;
	}
	
	//批量添加
	public function act_batchAdd(){
		require_once WEB_PATH."conf/scripts/script.ebay.config.php";
		$orderid = isset($_POST['omOrderId'])?trim($_POST['omOrderId']):"";
		$data = isset($_POST['data'])?$_POST['data']:"";
		$add_pitemid = $data['add_pitemid'];
		$add_precordno = $data['add_precordno'];
		$add_psku = $data['add_psku'];
		$add_pname = $data['add_pname'];
		$add_pprice = $data['add_pprice'];
		$add_sspfee = $data['add_sspfee'];
		$add_pqty = $data['add_pqty'];
		$add_notes = $data['add_notes'];
		$platformId = $data['detail_platformId'];
		$platfrom = omAccountModel::getPlatformSuffixById($platformId);
		$extension = $platfrom['suffix'];//获取后缀名称
		//var_dump($add_pitemid);
		foreach($add_pitemid as $key => $value){
			$add_detail = array();
			$add_detailExtral = array();
			if(isset($add_pitemid[$key]) && !empty($add_pitemid[$key])){
				$add_detailExtral['itemId'] = $add_pitemid[$key];
			}
			if(isset($add_precordno[$key]) && !empty($add_precordno[$key])){
				$add_detail['recordNumber'] = $add_precordno[$key];
			}
			if(isset($add_psku[$key]) && !empty($add_psku[$key])){
				$add_detail['sku'] = $add_psku[$key];
			}	
			if(isset($add_pname[$key]) && !empty($add_pname[$key])){
				$add_detailExtral['itemTitle'] = $add_pname[$key];
			}
			if(isset($add_sspfee[$key]) && !empty($add_sspfee[$key])){
				$add_detail['itemPrice'] = $add_sspfee[$key];
			}
			if(isset($add_pprice[$key]) && !empty($add_pprice[$key])){
				$add_detail['itemPrice'] = $add_pprice[$key];
			}
			if(isset($add_pname[$key]) && !empty($add_pname[$key])){
				$add_detail['shippingFee'] = $add_pname[$key];
			}
			if(isset($add_pqty[$key]) && !empty($add_pqty[$key])){
				$add_detail['amount'] = $add_pqty[$key];
			}
			if(isset($add_notes[$key]) && !empty($add_notes[$key])){
				$add_detailExtral['note'] = $add_notes[$key];
			}
			BaseModel :: begin(); //开始事务
			if($add_detail){
				//var_dump($update_order);
				$add_detail['omOrderId'] = $orderid;
				$add_detail['createdTime'] = time();
				$insertDetailId = OrderAddModel::insertOrderdetail($add_detail);
				if($insertDetailId){
					/*$tableName = "om_unshipped_order";
					$where = " WHERE id = ".$orderid;
					
					$updateOrder = array();
					$orderData = OrderindexModel::showOrderList($tableName, $where);
					$orderData = $orderData[$orderid];
					$orderDetail = $orderData['orderDetail'];
					$calcInfo = CommonModel :: calcAddOrderWeight($orderDetail);//计算重量和包材
					//var_dump($calcInfo); exit;
					$updateOrder['calcWeight'] = $calcInfo[0];
					$updateOrder['pmId'] = $calcInfo[1];
					if(count($orderDetail) > 1){
						$updateOrder['orderAttribute'] = 3;
					}else if(isset($orderDetail[0]['orderDetailData']['amount']) && $orderDetail[0]['orderDetailData']['amount'] > 1){
						$updateOrder['orderAttribute'] = 2;
					}
					$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData,1);//计算运费
					//var_dump($calcShippingInfo); exit;
					//$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
					$updateOrder['channelId'] = $calcShippingInfo['fee']['channelId'];
					
					$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
					//print_r($interceptInfo); exit;
					$updateOrder['orderStatus'] = $interceptInfo['orderStatus'];
					$updateOrder['orderType'] = $interceptInfo['orderType'];
					
					$rtn2 = OrderindexModel::updateOrder($tableName, $updateOrder, $where);
					if(!$rtn2){
						BaseModel :: rollback();
					}*/
				}else{
					BaseModel :: rollback();
				}
			}
			if($add_detailExtral && $insertDetailId){
				//echo $insertDetailId;
				$add_detailExtral['omOrderdetailId'] = $insertDetailId;
				if(!OrderAddModel::insertDetailExtension($add_detailExtral, $extension)){
					BaseModel :: rollback();	
				}
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
		}
		self :: $errCode = OrderModifyModel :: $errCode;
		self :: $errMsg = OrderModifyModel :: $errMsg;
		return true;
	}
	
	//批量添加
	public function act_addNote($orderid,$data){
		$add_notes = $data['add_notes'];
		//var_dump($add_pitemid);
		if(empty($add_notes)){
			self :: $errCode = '400';
			self :: $errMsg = '没有提交数据';
			return false;
		}
		foreach($add_notes as $key => $value){
			$add_note = array();
			if(isset($add_notes[$key]) && !empty($add_notes[$key])){
				$add_note['content'] = $add_notes[$key];
			}
			if($add_note){
				$add_note['omOrderId'] = $orderid;
				$add_note['userId'] = $_SESSION['sysUserId'];
				$add_note['createdTime'] = time();
				if(!OrderAddModel::insertOrderNotesRow($add_note)){
					self :: $errCode = OrderModifyModel :: $errCode;
					self :: $errMsg = OrderModifyModel :: $errMsg;
					return false;	
				}
			}
		}
		self :: $errCode = OrderModifyModel :: $errCode;
		self :: $errMsg = OrderModifyModel :: $errMsg;
		return true;
	}

	//订单锁定
	public function act_Lock(){
		$id	=	$_REQUEST['id'];
		$set	=	' SET isLock = "1" , lockUser = "'.$_SESSION['sysUserId'].'" , lockTime = "'.time().'" ';
		$where	=	' WHERE id = '.$id;
		$ret = OrderModifyModel :: updateTName(' om_unshipped_order ',$set,$where);
		if(!$ret){
			self :: $errCode	= OrderModifyModel :: $errCode;
			self :: $errMsg		= OrderModifyModel :: $errMsg;
			return false;
		} else {
			return true;
		}
	}
	//订单解锁
	public function act_unLock(){
		$id	=	$_REQUEST['id'];
		$sysUserId = $_SESSION['sysUserId'];
		if(!is_array($id)){
			$id = explode(",",$id);
		}
		foreach($id as $k => $v){
			$set	=	' SET isLock = "" , lockUser = "" , lockTime = "" ';
			$where	=	' WHERE id = '.$v.' AND lockUser = '.$sysUserId;
			$ret = OrderModifyModel :: updateTName('om_unshipped_order',$set,$where);
			if(!$ret){
				self :: $errCode	= OrderModifyModel :: $errCode;
				self :: $errMsg		= OrderModifyModel :: $errMsg;
				return false;
			}
		}
		return true;
	}
	//更改运输方式
	public function act_changeTransportation(){
		$transportationType	=	$_REQUEST['transportationType'];
		switch($transportationType){
			case '1':
				$transportation = CommonModel::getCarrierList(1);			//快递
				break;
			case '2':
				$transportation = CommonModel::getCarrierList(0);		//平邮
				break;
			default:
				$transportation = CommonModel::getCarrierList();	//所有运输方式
				break;
		}
		if($transportation){
			return $transportation;
		} else {
			self :: $errCode	= '998';
			self :: $errMsg		= '无法取到运输方式';
			return false;
		}
	}
	
	//更改平台列表显示对应账号信息
	public function act_changeplatformId(){
		$platformId	= $_REQUEST['platformId'];
		
		$accountList = omAccountModel::accountListByPid($platformId);
		//var_dump($accountList);
		if($accountList){
			return $accountList;
		} else {
			self :: $errCode	= '998';
			self :: $errMsg		= '无法取到对应平台的账号信息';
			return false;
		}
	}
	
	/*
     * 删除操作
	 * herman.xi @20131214
     */
    public function act_deleteDetail() {
        global $memc_obj;
		require_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//var_dump($_POST); exit;
		$update_order = array();
		$update_userinfo = array();
		$omData = $_POST['omData'];
		$omOrderDetailId = $_POST['omOrderDetailId'];
		$orderid = $_POST['orderid'];
		$detailArr = explode(',',$omData);
		//缺少事件
		BaseModel :: begin(); //开始事务
		$tableName = "om_unshipped_order_detail";
		$where = " WHERE id = ".$omOrderDetailId;
		
		$rtn = OrderindexModel::deleteOrderDetailData($tableName, $where);
		if($rtn){
			/*$tableName = "om_unshipped_order";
			$where = " WHERE id = ".$orderid;
			
			$updateOrder = array();
			$orderData = OrderindexModel::showOrderList($tableName, $where);
			$orderData = $orderData[$orderid];
			$orderDetail = $orderData['orderDetail'];
			$calcInfo = CommonModel :: calcAddOrderWeight($orderDetail);//计算重量和包材
			//var_dump($calcInfo); exit;
			$updateOrder['calcWeight'] = $calcInfo[0];
			$updateOrder['pmId'] = $calcInfo[1];
			if(count($orderDetail) > 1){
				$updateOrder['orderAttribute'] = 3;
			}else if(isset($orderDetail[0]['orderDetailData']['amount']) && $orderDetail[0]['orderDetailData']['amount'] > 1){
				$updateOrder['orderAttribute'] = 2;
			}
			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData,1);//计算运费
			//var_dump($calcShippingInfo); exit;
			//$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
			$updateOrder['channelId'] = $calcShippingInfo['fee']['channelId'];
			
			$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
			//print_r($interceptInfo); exit;
			$updateOrder['orderStatus'] = $interceptInfo['orderStatus'];
			$updateOrder['orderType'] = $interceptInfo['orderType'];
			
			$rtn2 = OrderindexModel::updateOrder($tableName, $updateOrder, $where);
			if(!$rtn2){
				BaseModel :: rollback();
			}*/
		}else{
			BaseModel :: rollback();
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $rtn;
    }
	
	/*
     * 更新
	 * herman.xi @20131214
     */
    public function act_updateDetail() {
        global $memc_obj;
		$start = time();
		//ob_start();//不加这个是不行的(貌似不加可以)
		$omAvailableAct = new OmAvailableAct();
		global $definedArr;
		require_once WEB_PATH."conf/scripts/script.ebay.config.php";
		$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', ' WHERE is_delete=0 ');
		$FLIP_GLOBAL_EBAY_ACCOUNT = array_flip($GLOBAL_EBAY_ACCOUNT);
		$definedArr = get_defined_vars();
		/*$end = time();
		echo $end-$start; echo "<br>";
		$start = $end;*/
		//var_dump($_POST); exit;
		$orderid = $_POST['orderid'];
		$omOrderDetailId = $_POST['omOrderDetailId'];
		//$data = unset($_POST['omOrderDetailId']);
		$update_detail = array();
		$update_detail_extral = array();
		$update_detail['recordNumber'] = $_POST['recordNumber'];
		$update_detail['sku'] = $_POST['sku'];
		$update_detail_extral['itemTitle'] = $_POST['itemTitle'];
		$update_detail['itemPrice'] = $_POST['itemPrice'];
		$update_detail['shippingFee'] = $_POST['shippingFee'];
		$update_detail['amount'] = $_POST['amount'];
		$update_detail_extral['note'] = $_POST['note'];
		$platformId = $_POST['detail_platformId'];
		
		$platfrom = omAccountModel::getPlatformSuffixById($platformId);
		$extension = $platfrom['suffix'];//获取后缀名称
			
		$detailArr = explode(',',$omData);
		//缺少事件
		BaseModel :: begin(); //开始事务
		$tableName = "om_unshipped_order_detail";
		$where = " WHERE id = ".$omOrderDetailId;
		if($update_detail){
			$rtn = OrderindexModel::updateOrder($tableName,$update_detail,$where);
			if($rtn){
				/*$tableName = "om_unshipped_order";
				$where = " WHERE id = ".$orderid;
				
				$updateOrder = array();
				$orderData = OrderindexModel::showOrderList($tableName, $where);
				$orderData = $orderData[$orderid];
				//var_dump($orderData);
				$orderDetail = $orderData['orderDetail'];
				$calcInfo = CommonModel :: calcAddOrderWeight($orderDetail);//计算重量和包材
				//var_dump($calcInfo); exit;
				$updateOrder['calcWeight'] = $calcInfo[0];
				$updateOrder['pmId'] = $calcInfo[1];
				if(count($orderDetail) > 1){
					$updateOrder['orderAttribute'] = 3;
				}else if(isset($orderDetail[0]['orderDetailData']['amount']) && $orderDetail[0]['orderDetailData']['amount'] > 1){
					$updateOrder['orderAttribute'] = 2;
				}
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData,1);//计算运费

				//var_dump($calcShippingInfo); exit;
				//$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
				$updateOrder['channelId'] = $calcShippingInfo['fee']['channelId'];
				
				$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
				//print_r($interceptInfo); exit;

				$updateOrder['orderStatus'] = $interceptInfo['orderStatus'];
				$updateOrder['orderType'] = $interceptInfo['orderType'];
				
				$rtn2 = OrderindexModel::updateOrder($tableName, $updateOrder, $where);
				if(!$rtn2){
					BaseModel :: rollback();
				}*/
			}else{
				BaseModel :: rollback();
			}
		}
		//ob_end_clean();
		if($update_detail_extral){
			$tableName = "om_unshipped_order_detail_extension_".$extension;
			$where = " WHERE omOrderdetailId = ".$omOrderDetailId;
			$rtn = OrderindexModel::updateOrder($tableName,$update_detail_extral,$where);
			if(!$rtn){
				BaseModel :: rollback();
			}
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $rtn;
    }
	
	/*
     * 提交编辑页面
	 * herman.xi @20131214
     */
    public function act_batchDeleteDetail() {
        global $memc_obj;
		require_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//var_dump($_POST); exit;
		$update_order = array();
		$update_userinfo = array();
		$omData = $_POST['omData'];
		$omOrderId = $_POST['omOrderId'];
		$detailArr = explode(',',$omData);
		//缺少事件
		BaseModel :: begin(); //开始事务
		foreach($detailArr as $id){
			$tableName = "om_unshipped_order_detail";
			$where = " WHERE omOrderId = ".$omOrderId. " AND id = ".$id;
			$rtn = OrderindexModel::deleteOrderDetailData($tableName, $where);
			if($rtn){
				/*$tableName = "om_unshipped_order";
				$where = " WHERE id = ".$omOrderId;
				
				$updateOrder = array();
				$orderData = OrderindexModel::showOrderList($tableName, $where);
				$orderData = $orderData[$omOrderId];
				$orderDetail = $orderData['orderDetail'];
				$calcInfo = CommonModel :: calcAddOrderWeight($orderDetail);//计算重量和包材
				//var_dump($calcInfo); exit;
				$updateOrder['calcWeight'] = $calcInfo[0];
				$updateOrder['pmId'] = $calcInfo[1];
				if(count($orderDetail) > 1){
					$updateOrder['orderAttribute'] = 3;
				}else if(isset($orderDetail[0]['orderDetailData']['amount']) && $orderDetail[0]['orderDetailData']['amount'] > 1){
					$updateOrder['orderAttribute'] = 2;
				}
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData,1);//计算运费
				//var_dump($calcShippingInfo); exit;
				//$insert_orderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
				$updateOrder['channelId'] = $calcShippingInfo['fee']['channelId'];
				
				$interceptInfo = CommonModel :: auto_contrast_intercept($orderData);
				//print_r($interceptInfo); exit;
				$updateOrder['orderStatus'] = $interceptInfo['orderStatus'];
				$updateOrder['orderType'] = $interceptInfo['orderType'];
				
				$rtn2 = OrderindexModel::updateOrder($tableName, $updateOrder, $where);
				if(!$rtn2){
					BaseModel :: rollback();
				}*/
			}else{
				BaseModel :: rollback();
			}
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self::$errCode = OrderindexModel::$errCode;
		self::$errMsg  = OrderindexModel::$errMsg;
		return $rtn;
    }
	public function act_modifyOrder(){
		if(isset($_POST) && !empty($_POST)){

			//$OrderModifyAct->act_modifyOrder();
			$orderid = isset($_POST['orderid']) ? $_POST['orderid']: '';	
			$ostatus = isset($_POST['edit_ostatus']) ? $_POST['edit_ostatus'] : $_POST['ostatus'];	
			$otype   = isset($_POST['edit_otype']) ? $_POST['edit_otype'] : $_POST['otype'];
			$update_order = array();
			$update_userinfo = array();
			$update_tracknumber = array();
			//$orderid = $_POST['orderid'];
			//var_dump($_POST); exit;
			$updatestatus = false;

			if(!empty($_POST['data'])){
				$data = $_POST['data'];
			}
			if(isset($data['username'])){
				$update_userinfo['username'] = $data['username'];
			}
			if(isset($data['orderStatus'])){
				$update_order['orderStatus'] = $data['orderStatus'];
			}
			if(isset($data['orderType'])){
				$update_order['orderType'] = $data['orderType'];
				$updatestatus = true;
			}
			if(isset($data['street'])){
				$update_userinfo['street'] = $data['street'];
			}
			if(isset($data['platformUsername'])){
				$update_userinfo['platformUsername'] = $data['platformUsername'];
			}
			if(isset($data['address2'])){
				$update_userinfo['address2'] = $data['address2'];
			}
			if(isset($data['actualShipping'])){
				$update_order['actualShipping'] = $data['actualShipping'];
			}
			if(isset($data['city'])){
				$update_userinfo['city'] = $data['city'];
			}
			if(isset($data['state'])){
				$update_userinfo['state'] = $data['state'];
			}
			if(isset($data['countryName'])){
				$update_userinfo['countryName'] = $data['countryName'];
			}
			if(isset($data['zipCode'])){
				$update_userinfo['zipCode'] = $data['zipCode'];
			}
			if(isset($data['landline'])){
				$update_userinfo['landline'] = $data['landline'];
			}
			if(isset($data['phone'])){
				$update_userinfo['phone'] = $data['phone'];
			}
			if(isset($data['transportId'])){
				$update_order['transportId'] = $data['transportId'];
			}
			if(isset($data['update_notes'])){
				$update_note = $data['update_notes'];
			}
			if(isset($data['note_new'])){
				$add_note = $data['note_new'];
			}

			
			if($data['edit_tracknumber']){
				$update_tracknumber['omOrderId'] = $orderid;
				$update_tracknumber['tracknumber'] = $data['edit_tracknumber'];
				$update_tracknumber['addUser'] = $_SESSION['sysUserId'];
				$update_tracknumber['createdTime'] = time();
				//var_dump($update_tracknumber); exit;
			}
			BaseModel :: begin(); //开始事务
			if($update_order /*&& $_POST['action'] == 'update'*/){
				//$sql = "UPDATE om_unshipped_order set ".array2sql($update_order)." WHERE id = ".$orderid;
				//$msg = commonModel::orderLog($orderid,$update_order['orderStatus'],$update_order['orderType'],$sql);
				
				if(OrderindexModel::updateOrder('om_unshipped_order', $update_order, ' WHERE id = '.$orderid)){
					if($updatestatus){
						$ProductStatus = new ProductStatus();
						if(!$ProductStatus->updateSkuStatusByOrderStatus(array($orderid), $batch_ostatus_val, $batch_otype_val)){
							BaseModel :: rollback();
						}	
					}
					$modify_showerrorinfo = "<font color='green'>更新成功</font>";
				}else{
					self :: $errCode = "001";
					self :: $errMsg = "更新订单信息失败！";
					BaseModel :: rollback();
					return false;
				}
			}
			if($update_userinfo /*&& $_POST['action'] == 'update'*/){
				//var_dump($update_userinfo);
				if(OrderindexModel::updateOrder('om_unshipped_order_userInfo', $update_userinfo, ' WHERE omOrderId = '.$orderid)){
					$modify_showerrorinfo = "<font color='green'>更新成功</font>";	
				}else{
					self :: $errCode = "002";
					self :: $errMsg = "更新订单用户信息失败！";
					BaseModel :: rollback();
					return false;
				}
			}
			if($update_tracknumber){
				//echo $msg;
				if(!OrderAddModel::insertOrderTrackRow($update_tracknumber)){
					self :: $errCode = "003";
					self :: $errMsg = "更新订单跟踪号插入失败！";
					BaseModel :: rollback();
					return false;
				}
			}
			if($update_note){
				foreach($update_note as $key=>$value){
					$notes = explode("###",$value);
					$where = " where content='{$notes[0]}' and omOrderId=$orderid";
					$set = "set content='{$notes[1]}',userId={$_SESSION['sysUserId']},createdTime=".time();
					$msg = OmAvailableModel::updateTNameRow("om_order_notes",$set,$where);
					if(!$msg){
						self :: $errCode = "004";
						self :: $errMsg = "更新订单备注失败！";
						BaseModel :: rollback();
						return false;
					}
				}
			}
			if($add_note){
				foreach($add_note as $key=>$value){
					$set = "set omOrderId={$orderid},content='{$value}',userId={$_SESSION['sysUserId']},createdTime=".time();
					$msg = OmAvailableModel::insertRow("om_order_notes",$set);
					if(!$msg){
						self :: $errCode = "005";
						self :: $errMsg = "插入订单备注失败！";
						BaseModel :: rollback();
						return false;
					}
				}
			}
			self :: $errCode = "200";
			self :: $errMsg = "整个订单信息更新成功！";
			BaseModel :: commit();
			BaseModel :: autoCommit();
		}
		
	}

	public function act_recalculated_bak(){
		$orderId	=	isset($_REQUEST['orderid']) ? $_REQUEST['orderid'] : '';
		if(empty($orderId)){
			self :: $errCode = "006";
			self :: $errMsg = "订单号错误!";
			return false;
		}
		$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$where	=	' WHERE id = "'.$orderId.'" ';
		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped,$where);
		
		$shipOrderList	=	isset($shipOrderList[$orderId]) ? $shipOrderList[$orderId] : '';
		if(empty($shipOrderList)){
			self :: $errCode = "007";
			self :: $errMsg = "查询数据出错!";
			return false;
		}
		$orderDetail	=	$shipOrderList['orderDetail'];
		$obj_order_detail_data	=	array();
		foreach($orderDetail as $k => $v){

			$orderdata_detail	=	array();
			$orderdata_detail['recordNumber']	=	$v['orderDetailData']['recordNumber'];    			 
			$orderdata_detail['sku']			=	$v['orderDetailData']['sku']; 
			$orderdata_detail['itemPrice']      =	$v['orderDetailData']['itemPrice'];
			$orderdata_detail['amount']     	=	$v['orderDetailData']['amount'];
			$orderdata_detail['createdTime']    =	time();    	
			
			$orderDetailExtAli	=	array();               
			$orderDetailExtAli['itemTitle']	    =	$v['orderDetailExtenData']['itemTitle']; 
			$orderDetailExtAli['itemURL']	    =	$v['orderDetailExtenData']['itemURL']; 
			$orderDetailExtAli['itemId']	    =	$v['orderDetailExtenData']['itemId']; 
			$orderDetailExtAli['transId']	    =	$v['orderDetailExtenData']['transId']; 
			$orderDetailExtAli['note']	        =	$v['orderDetailExtenData']['note']; 

			$obj_order_detail_data[] = array(
											'orderDetailData' => $orderdata_detail,			
											'orderDetailExtenData' => $orderDetailExtAli
										);
		}
		$calcInfo = CommonModel :: calcAddOrderWeight($obj_order_detail_data);//计算重量和包材
		if(isset($calcInfo[0])){
			return $calcInfo[0];
		} else {
			self :: $errCode = "008";
			self :: $errMsg = "计算数据出错!";
			return false;
		}
	}
	
}
?>