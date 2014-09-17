<?php


/*
 * 仓库基础信息管理(action)
 * ADD BY chenwei 2013.8.13
 */
class SkuStockAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 仓库名称管理数据查询
	 */
	function act_getSkuStockList($where) {
		$list = SkuStockModel :: getSkuStockList($where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_getSkuStockCount($where) {
		$list = SkuStockModel :: getSkuStockCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = SkuStockModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = SkuStockModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_addTNameRow($tName, $set) {
		$ret = SkuStockModel :: addTNameRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_updateTNameRow($tName, $set, $where) {
		$ret = SkuStockModel :: updateTNameRow($tName, $set, $where);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = SkuStockModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}
	
	//获取子类信息
	function  act_getCategoryInfo($cateid=0){
		if($_POST){
			$id = $_POST['id'];
		}else{
			$id = $cateid;
		}
		$list = SkuStockModel::getCategoryInfo($id);		
		if($list){
			return $list;
		}else{
			self::$errCode = SkuStockModel::$errCode;
			self::$errMsg  = SkuStockModel::$errMsg;
			return false;
		}
	}
	
	//ajax拉去图片(by sku)
    function act_ajaxGetPicBySku(){
        $sku  = isset($_POST['sku'])?$_POST['sku']:"";//料号条码
        if(empty($sku)){
            return false;
        }
        $picUrl = getPicFromOpenSys($sku);
        return $picUrl;
    }

	//对外接口
	//添加根据订单ID，添加对应发货单，完整功能整合
	function act_addWhShipingOrderCAT(){
		global $memc_obj; //调用memcache获取sku信息
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串(客户端要先json然后再base64))
		if (empty ($jsonArr)) {
			self :: $errCode = '0101';
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = '0201';
			self :: $errMsg = 'error array';
			return 0;
		}
		if (!is_array($jsonArr['shipOrderDetail'])) {
			self :: $errCode = '0301';
			self :: $errMsg = 'shipOrderDetail error array';
			return 0;
		}
		try{
			TransactionBaseModel::begin();
			$originOrderId = $jsonArr['originOrderId']; //订单ID
			if(intval($originOrderId) == 0){
				self :: $errCode = '0401';
				self :: $errMsg = 'error originOrderId';
				return 0;
			}
			$recordNumber = $jsonArr['recordNumber']; //订单记录各平台id号
			$shipOrderDetail = $jsonArr['shipOrderDetail'];//订单详细,为数组记录

			$username = $jsonArr['username']; //收件人
			$platformUsername = $jsonArr['platformUsername']; //对应平台的用户登陆名称,买家id
			$email = $jsonArr['email']; //客户邮箱
			$countryName = $jsonArr['countryName']; //收件人国家名称
			$countrySn = $jsonArr['countrySn']; //收件人国家简称
			$state = $jsonArr['state']; //收件人省份，州名
			$city = $jsonArr['city']; //收件人城市名称
			$street = $jsonArr['street']; //收件人街道
			$address2 = $jsonArr['address2']; //收件人地址2
			$address3 = $jsonArr['address3']; //收件人地址3
			$currency = $jsonArr['currency']; //币种
			$landline = $jsonArr['landline']; //座机
			$phone = $jsonArr['phone']; //手机
			$zipCode = $jsonArr['zipCode']; //邮政编码
			$transportId = $jsonArr['transportId']; //运输方式ID
			$accountId = $jsonArr['accountId']; //发货单对应销售账号
			$orderAttributes = $jsonArr['orderAttributes']; //发货单属性状态id,为数组
			if(empty($orderAttributes) || !is_array($orderAttributes)){
				self :: $errCode = '0501';
				self :: $errMsg = 'error orderAttributes';
				return 0;
			}
			$pmId = $jsonArr['pmId']; //包装材料ID
			$isFixed = $jsonArr['isFixed']; //是否固定运输方式，默认2最优运输方式；1固定运输方式
			$total = $jsonArr['total']; //发货单总价值
			$channelId = $jsonArr['channelId']; //渠道ID
			$calcWeight = $jsonArr['calcWeight']; //估算重量，单位是kg
			$calcShipping = $jsonArr['calcShipping']; //估算运费
			$createdTime = $jsonArr['createdTime'] ? $jsonArr['createdTime'] : time(); //添加时间
			$orderTypeId = $jsonArr['orderTypeId'] ? $jsonArr['orderTypeId'] : 1; //发货单类别，默认为1，发货单；2为配货单
			$companyId = $jsonArr['companyId'] ? $jsonArr['companyId'] : 1; //公司名称ID，默认赛维网络科技
			$storeId = $jsonArr['storeId'] ? $jsonArr['storeId'] : 1; //仓库ID，默认为1赛维网络深圳仓库

			$tName = 'wh_shipping_order';
			$set = "SET username='$username',platformUsername='$platformUsername',email='$email',countryName='$countryName',
			        countrySn='$countrySn',state='$state',city='$city',street='$street',address2='$address2',
			        address3='$address3',currency='$currency',landline='$landline',
			        phone='$phone',zipCode='$zipCode',transportId='$transportId',
			        accountId='$accountId',pmId='$pmId',
			        isFixed='$isFixed',total='$total',channelId='$channelId',
			        calcWeight='$calcWeight',calcShipping='$calcShipping',createdTime='$createdTime',
			        orderTypeId='$orderTypeId',companyId='$companyId',storeId='$storeId' ";
			$insertId = WhIoStoreModel :: addTNameRow($tName, $set);
			if (!$insertId) {
				self :: $errCode = '0801';
				self :: $errMsg = 'addRow error';
				throw new Exception('add shipOrder error');
			}

			$shipOrderId = $insertId; //发货单ID
			foreach($shipOrderDetail as $detail){

				$sku = $detail['sku']; //sku
				$amount = $detail['amount']; //配货数量
				if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/", $sku) || intval($amount) == 0){
					self :: $errCode = '0811';
					self :: $errMsg = 'sku or amount error';
					throw new Exception('sku or amount error');
				}
				//echo 'sku == '.$sku.'<br/>';
				$storeId = $detail['storeId'] ? $detail['storeId'] : 1; //仓库ID，默认为1赛维网络深圳仓库
				//echo '$memc_obj====';
				//print_r($memc_obj);
				//echo'<br/>';
				$skuInfo = $memc_obj->get_extral("sku_info_" . $sku); //调用memcache取得对应单料号或组合料号的重量
				//echo '$skuInfo=======';
				//print_r($skuInfo);
				//echo'<br/>';
				if(empty($skuInfo)){
					self :: $errCode = '0814';
					self :: $errMsg = 'empty skuInfo';
					throw new Exception('skuInfo');
				}
//				$ppp = $memc_obj->get_extral("pc_packing_material"); //调用memcache取得对应单料号或组合料号的重量
//				//echo '$ppp=======';
//				//print_r($ppp);
//				//echo'<br/>';
				if(!empty($skuInfo['sku']) && is_array($skuInfo['sku'])){//为组合料号
					foreach ($skuInfo['sku'] as $key => $value) { //循环$skuInfo下的sku的键，找出所有真实料号及对应数量,$key为组合料号下对应的真实单料号，value为对应数量
						if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/", $key) || intval($value) == 0){
							self :: $errCode = '0812';
							self :: $errMsg = 'sku or amount error';
							throw new Exception('sku or amount error');
						}
						$singSkuAmount = $value*$amount;
						//echo '$key == '.$key.'<br/>';
						//echo '$singSkuAmount == '.$singSkuAmount.'<br/>';
						$tName = 'wh_shipping_orderdetail';
						$set = "SET shipOrderId='$shipOrderId',combineSku='$sku',combineNum='$amount',sku='$key',
						        amount='$singSkuAmount',storeId='$storeId' ";
						$insertDetailId = WhIoStoreModel :: addTNameRow($tName, $set);
						if (!$insertDetailId) {
							self :: $errCode = '0802';
							self :: $errMsg = 'add shipOrderDetail1 error';
							throw new Exception('add shipOrderDetail1 error');
						}
					}
				}else{
					$tName = 'wh_shipping_orderdetail';
					$set = "SET shipOrderId='$shipOrderId',sku='$sku',
					        amount='$amount',storeId='$storeId' ";
					$insertDetailId = WhIoStoreModel :: addTNameRow($tName, $set);
					//echo'++++++++++++++++++++++++++';
					//echo '$insertDetailId========='.$insertDetailId.'<br/>';
					if (!$insertDetailId) {
						self :: $errCode = '0822';
						self :: $errMsg = 'add shipOrderDetail2 error';
						throw new Exception('add shipOrderDetail2 error');
					}
				}

			}
			//插入发货单和属性关系表
			//echo '$orderAttributes=======';
				//print_r($orderAttributes);
				//echo'<br/>';
			$tName = 'wh_order_attributes_relation';
			foreach($orderAttributes as $orderAttribute){
				if($orderAttribute != 1 && $orderAttribute != 2){
						self :: $errCode = '0805';
						self :: $errMsg = 'orderAttribute error';
						throw new Exception('orderAttribute error');
				}
				$set = "SET shippingOrderId='$shipOrderId',attributeId='$orderAttribute'";
				$insertARId = WhIoStoreModel :: addTNameRow($tName, $set);
				//echo'++++++++++++++++++++++++++';
					//echo '$insertARId========='.$insertARId.'<br/>';
				if ($insertARId !== 0) {
					self :: $errCode = '0806';
					self :: $errMsg = 'add insertARId error';
					throw new Exception('add insertARId error');
				}
			}

			//插入订单发货单关系表
			$tName = 'wh_shipping_order_relation';
			$set = "SET originOrderId='$originOrderId',shipOrderId='$shipOrderId',recordNumber='$recordNumber',storeId='$storeId' ";
			$insertRelationId = WhIoStoreModel :: addTNameRow($tName, $set);
			if (!$insertRelationId) {
				self :: $errCode = '0803';
				self :: $errMsg = 'add relation error';
				throw new Exception('add relation error');
			}
			TransactionBaseModel::commit();
			TransactionBaseModel::autoCommit();
			self :: $errCode = '222';
			self :: $errMsg = "success";
			return 1;
		}catch(Exception $e){
			TransactionBaseModel::rollback();
			TransactionBaseModel::autoCommit();
			self :: $errCode = '404';
			self :: $errMsg = $e->getMessage();
            return 0;
		}
	}

    //对外接口
    //根据originOrderId订单号，取得其对应的发货单号的状态
    function act_getOrderStatusByOriginId(){
       $originOrderId = isset ($_GET['originOrderId']) ? $_GET['originOrderId'] : '';//订单记录id
       $storeId = isset ($_GET['storeId']) ? $_GET['storeId'] : 1;//仓库id

       if(empty($originOrderId)){
          self :: $errCode = 1;
          self :: $errMsg = 'empty originOrderId';
	      return 0;
       }
       $tName = 'wh_shipping_order_relation';
       $select = 'shipOrderId';
       $where = "WHERE originOrderId='$originOrderId' AND storeId='$storeId'";
       $whShipOrderIdList = SkuStockModel::getTNameList($tName, $select, $where);
       if(empty($whShipOrderIdList)){//未找到originOrderId对应的shipOrderId
          self :: $errCode = 2;
          self :: $errMsg = 'empty whShipOrderIdList';
	      return 0;
       }
       $shipOrderId = $whShipOrderIdList[0]['shipOrderId'];
       $tName = 'wh_shipping_order';
       $select = 'orderStatus';//发货单状态
       $where = "WHERE id='$shipOrderId'";
       $whOrderStatusList = SkuStockModel::getTNameList($tName, $select, $where);
       if(empty($whOrderStatusList)){
          self :: $errCode = 3;
          self :: $errMsg = 'empty whOrderStatusList';
	      return 0;
       }
       $orderStatus = $whOrderStatusList[0]['orderStatus'];
       self :: $errCode = 200;
       self :: $errMsg = 'success';
       return $orderStatus;
    }

    //对外接口
    //根据originOrderId订单号，废弃其对应的发货单号（修改其状态）
    function act_discardShippingOrderByOriginId(){
       $unuse = PKS_UNUSUAL;//常量，为废弃状态
       $originOrderId = isset ($_GET['originOrderId']) ? $_GET['originOrderId'] : '';//订单记录id
       $storeId = isset ($_GET['storeId']) ? $_GET['storeId'] : 1;//订单记录id
       if(empty($originOrderId)){
          self :: $errCode = 1;
          self :: $errMsg = 'empty originOrderId';
	      return 0;
       }
       $tName = 'wh_shipping_order_relation';
       $select = 'shipOrderId';
       $where = "WHERE originOrderId='$originOrderId' AND storeId='$storeId'";
       $whShipOrderIdList = SkuStockModel::getTNameList($tName, $select, $where);
       if(empty($whShipOrderIdList)){//未找到originOrderId对应的shipOrderId
          self :: $errCode = 2;
          self :: $errMsg = 'empty whShipOrderIdList';
	      return 0;
       }
       $shipOrderId = $whShipOrderIdList[0]['shipOrderId'];
       $tName = 'wh_shipping_order';
       $set = "SET orderStatus='$unuse'";//发货单状态
       $where = "WHERE id='$shipOrderId'";
       $affectRow = SkuStockModel::updateTNameRow($tName, $set, $where);
       if(!$affectRow){
          self :: $errCode = 3;
          self :: $errMsg = 'update error';
	      return 0;
       }
       self :: $errCode = 200;
       self :: $errMsg = 'success';
       return 1;
    }

    //对外接口
    //根据sku,storeId取得其对应的库存数量
    function act_getActualStockBySS(){
       $sku = isset ($_GET['sku']) ? $_GET['sku'] : '';//sku
       $storeId = isset ($_GET['storeId']) ? $_GET['storeId'] : 1;//订单记录id
       if(empty($sku)){
          self :: $errCode = 1;
          self :: $errMsg = 'empty sku';
	      return 0;
       }
       $tName = 'wh_sku_location';
       $select = 'actualStock';
       $where = "WHERE sku='$sku' AND storeId='$storeId'";
       $whActualStockList = SkuStockModel::getTNameList($tName, $select, $where);
       if(empty($whShipOrderIdList)){//未找到originOrderId对应的shipOrderId
          self :: $errCode = 2;
          self :: $errMsg = 'empty whActualStockList';
	      return 0;
       }
       $actualStock = $whShipOrderIdList[0]['actualStock'];
       self :: $errCode = 200;
       self :: $errMsg = 'success';
       return $actualStock;
    }

}
?>
