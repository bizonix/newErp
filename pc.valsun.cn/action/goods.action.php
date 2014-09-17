<?php

/**
*类名：GoodsAct
*功能：处理产品信息及spu
*作者：hws
*
*/
class GoodsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	//获取产品类表
	function act_getGoodsList($select = '*', $where) {
		//调用model层获取数据
		$list = GoodsModel :: getGoodsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = GoodsModel :: $errCode;
			self :: $errMsg = GoodsModel :: $errMsg;
			return false;
		}
	}

	//获取产品数量
	static function act_getGoodsListNum($where) {
		//调用model层获取数据
		$list = GoodsModel :: getGoodsListNum($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = GoodsModel :: $errCode;
			self :: $errMsg = GoodsModel :: $errMsg;
			return false;
		}
	}

	//增加产品
	function act_addGoods() {
		$data = array ();
		//$property_arr = array();
		$data['spu'] = post_check(trim($_POST['spu']));
		$data['sku'] = post_check(trim($_POST['sku']));
		$data['goodsName'] = post_check(trim($_POST['goodsName']));
		$data['goodsDecNameByEN'] = post_check(trim($_POST['goodsDecNameByEN']));
		$data['goodsDecNameByCN'] = post_check(trim($_POST['goodsDecNameByCN']));
		$data['goodsCost'] = post_check(trim($_POST['goodsCost']));
		$data['goodsWeight'] = post_check(trim($_POST['goodsWeight']));
		$data['goodsNote'] = post_check(trim($_POST['goodsNote']));
		$data['goodsLength'] = post_check(trim($_POST['goodsLength']));
		$data['goodsWidth'] = post_check(trim($_POST['goodsWidth']));
		$data['goodsHeight'] = post_check(trim($_POST['goodsHeight']));
		$data['goodsColor'] = post_check(trim($_POST['goodsColor']));
		$data['goodsCategory'] = post_check(trim($_POST['goodsCategory']));
		$data['goodsStatus'] = post_check(trim($_POST['goodsStatus']));
		$data['goodsCustomsCode'] = post_check(trim($_POST['goodsCustomsCode']));
		$data['goodsDecWorth'] = post_check(trim($_POST['goodsDecWorth']));
		$data['purchaseId'] = post_check(trim($_POST['purchaseId']));
		$data['pmId'] = post_check(trim($_POST['pmId']));
		$data['isPacking'] = post_check($_POST['isPacking']);
		$data['goodsCode'] = post_check(trim($_POST['goodsCode']));
		$data['goodsSort'] = post_check(trim($_POST['goodsSort']));

		$time = time();
		$data['goodsCreatedTime'] = $time;

		if ($data['spu'] == '' || $data['sku'] == '' || $data['goodsName'] == '') {
			return array (
				'state' => 'no'
			);
			exit;
		}

		$list = GoodsModel :: getGoodsList("*", "where sku='{$data['sku']}' and is_delete=0");
		if ($list) {
			return array (
				'state' => 'have'
			);
			exit;
		}

		$res = GoodsModel :: insertRow($data);
		if ($res) {
			return array (
				'state' => 'ok'
			);
		} else {
			return array (
				'state' => 'no'
			);
		}

	}

	//修改商品
	function act_modGoods() {
		$data = array ();
		$id = post_check(trim($_POST['goodid']));
		$data['spu'] = post_check(trim($_POST['spu']));
		$data['sku'] = post_check(trim($_POST['sku']));
		$data['goodsName'] = post_check(trim($_POST['goodsName']));
		$data['goodsDecNameByEN'] = post_check(trim($_POST['goodsDecNameByEN']));
		$data['goodsDecNameByCN'] = post_check(trim($_POST['goodsDecNameByCN']));
		$data['goodsCost'] = post_check(trim($_POST['goodsCost']));
		$data['goodsWeight'] = post_check(trim($_POST['goodsWeight']));
		$data['goodsNote'] = post_check(trim($_POST['goodsNote']));
		$data['goodsLength'] = post_check(trim($_POST['goodsLength']));
		$data['goodsWidth'] = post_check(trim($_POST['goodsWidth']));
		$data['goodsHeight'] = post_check(trim($_POST['goodsHeight']));
		$data['goodsColor'] = post_check(trim($_POST['goodsColor']));
		$data['goodsCategory'] = post_check(trim($_POST['goodsCategory']));
		$data['goodsStatus'] = post_check(trim($_POST['goodsStatus']));
		$data['goodsCustomsCode'] = post_check(trim($_POST['goodsCustomsCode']));
		$data['goodsDecWorth'] = post_check(trim($_POST['goodsDecWorth']));
		$data['purchaseId'] = post_check(trim($_POST['purchaseId']));
		$data['pmId'] = post_check(trim($_POST['pmId']));
		$data['isPacking'] = $_POST['isPacking'];
		$data['goodsCode'] = post_check(trim($_POST['goodsCode']));
		$data['goodsSort'] = post_check(trim($_POST['goodsSort']));
		$time = time();
		$data['goodsCreatedTime'] = $time;

		if (!is_numeric($id)) {
			return array (
				'state' => 'no'
			);
		}
		$where = "AND id='{$id}'";
		if (GoodsModel :: update($data, $where)) {
			return array (
				'state' => 'ok'
			);
		} else {
			return array (
				'state' => 'no'
			);
		}
	}

	//删掉产品
	function act_delGoods() {
		$id = trim($_POST['id']);
		$where = "and `id` = '$id'";
		$data = array (
			'is_delete' => 1
		);
		if (GoodsModel :: update($data, $where)) {
			return array (
				'state' => 'ok'
			);
		} else {
			return array (
				'state' => 'no'
			);
		}
	}

	//添加重量时检测该sku是否存在
	function act_isExistSku() {
		$goodsCode = trim($_POST['goodsCode']);
		$skuList = getSkuBygoodsCode($goodsCode);
		if (empty ($skuList)) {
			return false;
		} else {
			return $skuList[0]['sku'];
		}
	}

    //根据goodsCode返回对应的sku包材相关信息，包括，长，宽，高，包装，类型，包材，包材容量
	function act_getVpInfoByGoodsCode(){
		$goodsCode = trim($_POST['goodsCode']);
		$skuList = getSkuBygoodsCode($goodsCode);
        if(empty($skuList)){
            return false;
        }else{
            $returnArr = array();
            $returnArr['goodsLength'] = $skuList[0]['goodsLength'];
            $returnArr['goodsWidth'] = $skuList[0]['goodsWidth'];
            $returnArr['goodsHeight'] = $skuList[0]['goodsHeight'];
            $returnArr['pmName'] = '';
            $returnArr['pmCapacity'] = $skuList[0]['pmCapacity'];
            $returnArr['isPacking'] = $skuList[0]['isPacking'];
            $returnArr['packageType'] = $skuList[0]['packageType'];
            $tName = 'pc_packing_material';
            $select = 'pmName';
            $where = "WHERE is_delete=0 and id={$skuList[0]['pmId']}";
            $pmList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($pmList[0]['pmName'])){
                $returnArr['pmName'] = $pmList[0]['pmName'];
            }
        }
		return $returnArr;
	}

	//将pmId转换成对应包材
	function act_addSkuPm() {
		$goodsCode = post_check($_POST['goodsCode']);
		$pmId = intval(trim($_POST['pmId']));
		if (intval($pmId) <= 0) {
			return false;
		}
		if (intval($goodsCode) <= 0) {
			return false;
		}
		$tName = 'pc_packing_material';
		$where = "where id=$pmId";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			return false;
		}
		$skuList = getSkuBygoodsCode($goodsCode);
		if (empty ($skuList)) {
			return false;
		}
		$tName = 'pc_goods';
		$set = "SET pmId=$pmId";
		$where = "WHERE spu='{$skuList[0]['spu']}'";
		$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($affectRow === false) {
			return false;
		}
		$pName = PackingMaterialsModel :: getPmNameById($pmId);
		$skuList = OmAvailableModel :: getTNameList($tName, 'sku,spu', "WHERE spu='{$skuList[0]['spu']}'");
		return array (
			'state' => true,
			'sku' => $skuList[0]['sku'] . ' 等 ' . count($skuList
		) . ' 个料号', 'pName' => $pName);
	}

	//料号称重
	public function act_skuWeighing() {
		$sku = isset ($_POST['sku']) ? $_POST['sku'] : ""; //料号条码
		$skuweight = isset ($_POST['skuweight']) ? ($_POST['skuweight'] / 1000) : "";
        $userId = $_SESSION['userId'];
        if(intval($userId) <= 0){
            self :: $errCode = 111;
			self :: $errMsg = "登陆超时，请重新登陆！";
			return false;
        }
		if (empty ($sku) || empty ($skuweight)) {
			self :: $errCode = 333;
			self :: $errMsg = "料号或重量不能为空！";
			return false;
		}
		$skuList = getSkuBygoodsCode($sku); //根据条码获取真实sku
		if (empty ($skuList)) {
			self :: $errCode = 404;
			self :: $errMsg = '料号不存在';
			return false;
		}
        $oldWeight = !empty($skuList[0]['goodsWeight'])?$skuList[0]['goodsWeight']:0;//先找出该sku的重量
        if($oldWeight != $skuweight){//如果新旧重量不相等时，则更新
            try {
                $flag = false;//标识是否需要审核流程
                if(!empty($oldWeight)){
                    $rate = abs($skuweight - $oldWeight)/$oldWeight;
                    if($rate > 0.5){
                        $flag = true;
                    }
                }
                if($flag){
                    $tName = 'pc_goods_weight_audit';
                    $where = "WHERE is_delete=0 and status=1 and sku='{$skuList[0]['sku']}'";
                    $auditingListCount = OmAvailableModel::getTNameCount($tName, $where);
                    if($auditingListCount){
                        self :: $errCode = 501;
            			self :: $errMsg = $skuList[0]['sku'] . " 存在待审核记录，请对应人先去审核！ ";
            			return true;
                    }else{
                        $tName = 'pc_goods_weight_audit';
                        $dataArr = array();
                        $dataArr['sku'] = $skuList[0]['sku'];
                        $dataArr['oldWeight'] = $oldWeight;
                        $dataArr['newWeight'] = $skuweight;
                        $dataArr['addUserId'] = $userId;
                        $dataArr['addTime'] = time();
                        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
                        self :: $errCode = 502;
            			self :: $errMsg = $skuList[0]['sku'] . " 新重量 $skuweight KG 超出原始重量 $oldWeight KG 的50%，已添加至重量审核记录，请对应人去审核！ ";
            			return true;
                    }
                }else{
                    BaseModel::begin();
                    $tName = 'pc_goods';
        			$set = "SET goodsWeight='{$skuweight}'";
        			$where = "WHERE sku='{$skuList[0]['sku']}'";
        			OmAvailableModel :: updateTNameRow($tName, $set, $where);//更新重量

                    $tName = 'pc_goods_weight_audit';//将审核列表中该sku不通过记录的is_delete=1掉
                    $set = 'set is_delete=1 and status=3';
                    $where = "WHERE sku='{$skuList[0]['sku']}'";
                    OmAvailableModel::updateTNameRow($tName, $set, $where);

        			//添加重量备份记录
                    addWeightBackupsModify($skuList[0]['sku'], $skuweight, $userId);
        			//
        			$paraArr['goods_sn'] = $skuList[0]['sku'];
        			$paraArr['goods_weight'] = $skuweight;
        			$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsSnWeight', $paraArr, 'gw88');

                    BaseModel::commit();
                    BaseModel::autoCommit();
                    $string = empty($oldWeight)?"(Kg) 录入成功！":"(Kg) 更新成功，原来重量为 $oldWeight(Kg)";
        			self :: $errCode = 200;
        			self :: $errMsg = $skuList[0]['sku'] . " 重量 " . $skuweight . $string;
        			return true;
                }
    		} catch (Exception $e) {
                BaseModel::rollback();
                BaseModel::autoCommit();
    			self :: $errCode = 404;
    			self :: $errMsg = $skuList[0]['sku'] . " 重量 " . $skuweight . "(Kg) 录入失败！ ".$e->getMessage();
    			return false;
    		}
        }else{
            self :: $errCode = 200;
			self :: $errMsg = $skuList[0]['sku'] . " 重量 无修改，为 $skuweight(Kg)";
			return true;
        }


	}

	//ajax拉去图片(by sku)
	function act_ajaxGetPicBySku() {
		$sku = isset ($_POST['sku']) ? $_POST['sku'] : ""; //料号条码
		if (empty ($sku)) {
			return false;
		}
		$picUrl = getPicFromOpenSys($sku);
		return $picUrl;
	}

	//ajax拉去图片(by spu)
	function act_ajaxGetPicBySpu() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : ""; //料号条码
		if (empty ($spu)) {
			return false;
		}
		$picUrl = getPicFromOpenSysSpu($spu);
		return $picUrl;
	}

	//ajax拉去全部图片(by spu)
	function act_ajaxGetAllArtPicBySpu() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : ""; //料号条码
		if (empty ($spu)) {
			return false;
		}
		$picUrl = getAllArtPicFromOpenSysSpu($spu);
		return $picUrl;
	}

	//ajax拉去全部图片(by spuArr)
	function act_ajaxGetAllArtPicBySpuArr() {
		$spuArr = isset ($_POST['spu']) ? $_POST['spu'] : ""; //料号条码
		if (empty ($spuArr)) {
			return false;
		}
		$picUrlArr = getPicFromOpenSysByArr($spuArr);
		return $picUrlArr;
	}

	function act_skuVP() {
		$goodsCode = post_check($_POST['goodsCode']);
		$goodsLength = trim($_POST['goodsLength']);
		$goodsWidth = trim($_POST['goodsWidth']);
		$goodsHeight = trim($_POST['goodsHeight']);
		$isPacking = trim($_POST['isPacking']);
		$packageType = trim($_POST['packageType']);
		$userId = $_SESSION['userId'];
        if(intval($userId) <= 0){
            self :: $errCode = 101;
			self :: $errMsg = '登陆超时，请重新登陆';
			return false;
        }

		$pmId = trim($_POST['pmId']);
		$pmCapacity = intval(trim($_POST['pmCapacity']));
		if ($pmCapacity <= 0) {
			$pmCapacity = 1;
		}
		$pmId = getPmIdByPmCode($pmId); //获取对应的包材id,找不到对应包材返回false;

		if (is_numeric($goodsLength) <= 0 || is_numeric($goodsLength) > 9999) {
			self :: $errCode = 102;
			self :: $errMsg = '长度有误';
			return false;
		}
		if (is_numeric($goodsWidth) <= 0 || is_numeric($goodsWidth) > 9999) {
			self :: $errCode = 103;
			self :: $errMsg = '宽度有误';
			return false;
		}
		if (is_numeric($goodsHeight) <= 0 || is_numeric($goodsHeight) > 9999) {
			self :: $errCode = 104;
			self :: $errMsg = '高度有误';
			return false;
		}
		if (!$pmId) {
			self :: $errCode = 105;
			self :: $errMsg = '包材条码有误，找不到对应包材';
			return false;
		}

		$skuList = getSkuBygoodsCode($goodsCode);
		if (empty ($skuList)) {
			self :: $errCode = 107;
			self :: $errMsg = '找不到对应SKU';
			return false;
		}
		$skuTmp = $skuList[0]['sku'];
        //旧的VP信息
        $oldGoodsLength = $skuList[0]['goodsLength'];
        $oldGoodsWidth = $skuList[0]['goodsWidth'];
        $oldGoodsHeight = $skuList[0]['goodsHeight'];
        //旧的包材信息
        $oldPmId = $skuList[0]['pmId'];
        $oldPmCapacity = $skuList[0]['pmCapacity'];
        $vpArr = array();//体积、包材、
        $volumeFlag = false;//体积变化标识，默认无变化
        $pmFlag = false;//包材变化标识，默认无变化
        if($oldGoodsLength != $goodsLength || $oldGoodsWidth != $goodsWidth || $oldGoodsHeight != $goodsHeight){//如果长，宽，高有一个与之前不相等
            $vpArr['goodsLength'] = $goodsLength;
            $vpArr['goodsWidth'] = $goodsWidth;
            $vpArr['goodsHeight'] = $goodsHeight;
            $volumeFlag = true;//体积变化
        }
        if($oldPmId != $pmId || $oldPmCapacity != $pmCapacity){
            $vpArr['pmId'] = $pmId;
            $vpArr['pmCapacity'] = $pmCapacity;
            $pmFlag = true;//包材变化
        }
        $vpArr['isPacking'] = $isPacking;
        $vpArr['packageType'] = $packageType;
        try{
            BaseModel::begin();
            $tName = 'pc_goods';
    		$where = "WHERE spu='{$skuList[0]['spu']}'";
    		OmAvailableModel::updateTNameRow2arr($tName, $vpArr, $where);
    		$skuList = OmAvailableModel :: getTNameList($tName, 'sku,spu', "WHERE spu='{$skuList[0]['spu']}'");
            foreach($skuList as $value){
                $skuBackups = $value['sku'];
                if(!empty($skuBackups)){//sku有效
                    if($volumeFlag){//体积变化则添加记录到对应表中
                        addVolumeBackupsModify($skuBackups, $goodsLength, $goodsWidth, $goodsHeight, $userId);//添加体积变化记录
                    }
                    if($pmFlag){//包材变化
                        addPmBackupsModify($skuBackups, $pmId, $pmCapacity, $userId);//添加包材变化记录
                    }
                }
            }
    		$pName = PackingMaterialsModel :: getPmNameById($pmId);
    		//同步新数据到旧系统
    		$paraArr = array ();
    		$paraArr['goods_sn'] = str_pad($skuList[0]['spu'], 3, 0, STR_PAD_LEFT);
    		$paraArr['goods_length'] = $goodsLength;
    		$paraArr['goods_width'] = $goodsWidth;
    		$paraArr['goods_height'] = $goodsHeight;
    		$is_packing = $isPacking;
    		$paraArr['ispacking'] = $is_packing == 1 ? 0 : 1;

    		$paraArr['ebay_packingmaterial'] = $pName;
    		$paraArr['package_type'] = $packageType;
    		$paraArr['capacity'] = $pmCapacity;
    		$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsVp', $paraArr, 'gw88');
            BaseModel::commit();
            BaseModel::autoCommit();
            return array (
    			'state' => 200,
    			'sku' => $skuTmp . ' 等 ' . count($skuList
    		) . ' 个料号', 'pName' => $pName);
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            echo $e->getMessage();
        }

	}

    //VP2
    function act_skuVP2() {
		$goodsCode = post_check($_POST['goodsCode']);
		$goodsLength = trim($_POST['goodsLength']);
		$goodsWidth = trim($_POST['goodsWidth']);
		$goodsHeight = trim($_POST['goodsHeight']);
		$isPacking = trim($_POST['isPacking']);
		$packageType = trim($_POST['packageType']);
		$userId = $_SESSION['userId'];
        if(intval($userId) <= 0){
            self :: $errCode = 101;
			self :: $errMsg = '登陆超时，请重新登陆';
			return false;
        }

		$pmId = trim($_POST['pmId']);
		$pmCapacity = intval(trim($_POST['pmCapacity']));
		if ($pmCapacity <= 0) {
			$pmCapacity = 1;
		}
		$pmId = getPmIdByPmCode($pmId); //获取对应的包材id,找不到对应包材返回false;

		if (is_numeric($goodsLength) <= 0 || is_numeric($goodsLength) > 9999) {
			self :: $errCode = 102;
			self :: $errMsg = '长度有误';
			return false;
		}
		if (is_numeric($goodsWidth) <= 0 || is_numeric($goodsWidth) > 9999) {
			self :: $errCode = 103;
			self :: $errMsg = '宽度有误';
			return false;
		}
		if (is_numeric($goodsHeight) <= 0 || is_numeric($goodsHeight) > 9999) {
			self :: $errCode = 104;
			self :: $errMsg = '高度有误';
			return false;
		}
		if (!$pmId) {
			self :: $errCode = 105;
			self :: $errMsg = '包材条码有误，找不到对应包材';
			return false;
		}

		$skuList = getSkuBygoodsCode($goodsCode);
		if (empty ($skuList)) {
			self :: $errCode = 107;
			self :: $errMsg = '找不到对应SKU';
			return false;
		}
		$skuTmp = $skuList[0]['sku'];
        //旧的VP信息
        $oldGoodsLength = $skuList[0]['goodsLength'];
        $oldGoodsWidth = $skuList[0]['goodsWidth'];
        $oldGoodsHeight = $skuList[0]['goodsHeight'];
        //旧的包材信息
        $oldPmId = $skuList[0]['pmId'];
        $oldPmCapacity = $skuList[0]['pmCapacity'];
        $vpArr = array();//体积、包材、
        $volumeFlag = false;//体积变化标识，默认无变化
        $pmFlag = false;//包材变化标识，默认无变化
        if($oldGoodsLength != $goodsLength || $oldGoodsWidth != $goodsWidth || $oldGoodsHeight != $goodsHeight){//如果长，宽，高有一个与之前不相等
            $vpArr['goodsLength'] = $goodsLength;
            $vpArr['goodsWidth'] = $goodsWidth;
            $vpArr['goodsHeight'] = $goodsHeight;
            $volumeFlag = true;//体积变化
        }
        if($oldPmId != $pmId || $oldPmCapacity != $pmCapacity){
            $vpArr['pmId'] = $pmId;
            $vpArr['pmCapacity'] = $pmCapacity;
            $pmFlag = true;//包材变化
        }
        $vpArr['isPacking'] = $isPacking;
        $vpArr['packageType'] = $packageType;
        try{
            BaseModel::begin();
            $tName = 'pc_goods';
    		$where = "WHERE sku='$skuTmp'";
    		OmAvailableModel::updateTNameRow2arr($tName, $vpArr, $where);
            if($volumeFlag){//体积变化则添加记录到对应表中
                addVolumeBackupsModify($skuTmp, $goodsLength, $goodsWidth, $goodsHeight, $userId);//添加体积变化记录
            }
            if($pmFlag){//包材变化
                addPmBackupsModify($skuTmp, $pmId, $pmCapacity, $userId);//添加包材变化记录
            }
    		$pName = PackingMaterialsModel :: getPmNameById($pmId);

    		$paraArr = array ();
    		$paraArr['goods_sn'] = str_pad($skuTmp, 3, 0, STR_PAD_LEFT);
    		$paraArr['goods_length'] = $goodsLength;
    		$paraArr['goods_width'] = $goodsWidth;
    		$paraArr['goods_height'] = $goodsHeight;
    		$is_packing = $isPacking;
    		$paraArr['ispacking'] = $is_packing == 1 ? 0 : 1;

    		$paraArr['ebay_packingmaterial'] = $pName;
    		$paraArr['package_type'] = $packageType;
    		$paraArr['capacity'] = $pmCapacity;
    		$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsVp2', $paraArr, 'gw88');
            BaseModel::commit();
            BaseModel::autoCommit();
            return array (
    			'state' => 200,
    			'sku' => $skuTmp.' 单个SKU', 'pName' => $pName);
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            echo $e->getMessage();
        }

	}

	function act_addSkuVolume() {
		$goodsCode = post_check($_POST['goodsCode']);
		$goodsLength = trim($_POST['goodsLength']);
		$goodsWidth = trim($_POST['goodsWidth']);
		$goodsHeight = trim($_POST['goodsHeight']);
		if (intval($goodsCode) == 0) {
			return false;
		}
		if (is_numeric($goodsLength) <= 0 || is_numeric($goodsLength) > 9999) {
			return false;
		}
		if (is_numeric($goodsWidth) <= 0 || is_numeric($goodsWidth) > 9999) {
			return false;
		}
		if (is_numeric($goodsHeight) <= 0 || is_numeric($goodsHeight) > 9999) {
			return false;
		}
		$skuList = getSkuBygoodsCode($goodsCode);
		if (empty ($skuList)) {
			return false;
		}
		$tName = 'pc_goods';
		$set = "SET goodsLength=$goodsLength,goodsWidth=$goodsWidth,goodsHeight=$goodsHeight";
		$where = "WHERE spu='{$skuList[0]['spu']}'";
		$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($affectRow === false) {
			return false;
		}
		$skuList = OmAvailableModel :: getTNameList($tName, 'sku,spu', "WHERE spu='{$skuList[0]['spu']}'");
		return array (
			'state' => 200,
			'sku' => $skuList[0]['sku'] . ' 等 ' . count($skuList
		) . ' 个料号');
	}

	//在spu_achivie表中审核不通过操作
	function act_noPassAudit() {
		if (!isAccessAll('autoCreateSpu', 'auditSpuArchive')) { //检测是否有权限
			self :: $errCode = 100;
			self :: $errMsg = '没有权限';
			return false;
		}
		$userId = $_SESSION['userId']; //统一用户的id
		if (intval($userId) <= 0) {
			self :: $errCode = 002;
			self :: $errMsg = "登陆超时，请重试";
			return false;
		}
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
		$noPassReason = $_POST['noPassReason'] ? post_check(trim($_POST['noPassReason'])) : '';

		if (empty ($spu)) {
			self :: $errCode = 101;
			self :: $errMsg = 'SPU为空';
			return false;
		}

		$tName = 'pc_spu_archive';
		$select = 'spu,categoryPath,spuName,spuStatus,spuPurchasePrice,spuLowestPrice,spuCalWeight,spuNote,spuSort,spuCreatedTime,purchaseId,referMonthSales,minNum,freight,platformId,secretInfo,lowestUrl,bidUrl';
		$where = "WHERE spu='$spu' and is_delete=0 ";
		$spuAchiveList = OmAvailableModel :: getTNameList($tName, $select, $where); //检测该spu是否存在
		if (empty ($spuAchiveList)) {
			self :: $errCode = 102;
			self :: $errMsg = "$spu 不存在";
			return false;
		}
		$tName = 'pc_spu_archive_no_pass_record';
		$select = 'spuName,isCounterAudit';
		$where = "WHERE spu='$spu' order by id desc limit 1";
		$spuNoPassList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($spuNoPassList)) { //根据spuName判断是否该spu已经被审核不通过过
			if ($spuNoPassList[0]['spuName'] == $spuAchiveList[0]['spuName'] && $spuAchiveList[0]['isCounterAudit'] == 1) {
				self :: $errCode = 103;
				self :: $errMsg = "$spu 已经在不通过列表中，请不要重复审核";
				return false;
			}
		}
		if (isSpuExist($spu)) {
			self :: $errCode = 104;
			self :: $errMsg = "$spu 下已经存在子料号，不能再次审核";
			return false;
		}
		try {
			BaseModel :: begin();
			$spuAchive = $spuAchiveList[0]; //审核不通过的SPU档案信息

			$tName = 'pc_spu_archive';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除不通过的SPU档案记录

            $tName = 'pc_archive_spu_property_value_relation';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除不通过的SPU产品档案选择属性记录

            $tName = 'pc_spu_archive_pk_sku';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除不通过的SPU产品档案选择属性记录

            $tName = 'pc_archive_spu_input_value_relation';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除不通过的SPU产品档案文本记录

            $tName = 'pc_archive_spu_input_size_measure';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除不通过的SPU产品档案测量记录


			$tName = 'pc_auto_create_spu';
			$set = "SET status=1";
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: updateTNameRow($tName, $set, $where); //将自动生成SPU列表中的该spu改成未进入系统

			$tName = 'pc_spu_archive_no_pass_record';
			$spuAchive['noPassReason'] = $noPassReason;
			$spuAchive['auditorId'] = $userId;
			$spuAchive['auditTime'] = time();
			OmAvailableModel :: addTNameRow2arr($tName, $spuAchive); //在审核不通过记录表中添加该spu不通过信息记录

			BaseModel :: commit();
			BaseModel :: autoCommit();
			self :: $errCode = 200;
			self :: $errMsg = "审核不通过成功";
			return true;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			self :: $errCode = 404;
			self :: $errMsg = $e->getMessage();
			return false;
		}
	}

	//在spu_no_pass_record表中进行反审核操作
	function act_counterAuditInNoPass() {
		if (!isAccessAll('autoCreateSpu', 'auditSpuArchive')) { //检测是否有权限
			self :: $errCode = 100;
			self :: $errMsg = '没有权限';
			return false;
		}
		$userId = $_SESSION['userId']; //统一用户的id
		if (intval($userId) <= 0) {
			self :: $errCode = 002;
			self :: $errMsg = "登陆超时，请重试";
			return false;
		}
		$cid = $_POST['cid'] ? post_check(trim($_POST['cid'])) : '';
		$cid = intval($cid);
		if ($cid <= 0) {
			self :: $errCode = 101;
			self :: $errMsg = '非法参数';
			return false;
		}

		$tName = 'pc_spu_archive_no_pass_record';
		$select = 'spu,categoryPath,spuName,spuStatus,spuPurchasePrice,spuLowestPrice,spuCalWeight,spuNote,spuSort,spuCreatedTime,purchaseId,referMonthSales,minNum,freight,platformId,secretInfo,lowestUrl,bidUrl';
		$where = "WHERE id=$cid";
		$spuNoPassList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuNoPassList)) {
			self :: $errCode = 105;
			self :: $errMsg = "该spu不存在于审核不通过列表中";
			return false;
		}
		$spu = $spuNoPassList[0]['spu'];
		$tName = 'pc_spu_archive';
		$where = "WHERE spu='$spu'";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count) {
			self :: $errCode = 102;
			self :: $errMsg = '该SPU已经存在档案，不能反审核';
			return false;
		}

		if (isSpuExist($spu)) {
			self :: $errCode = 104;
			self :: $errMsg = "$spu 下已经存在子料号，不能再次审核";
			return false;
		}

		try {
			BaseModel :: begin();
			$spuNoPass = $spuNoPassList[0]; //审核不通过的SPU档案信息

			$tName = 'pc_spu_archive';
			$spuNoPass['auditStatus'] = 2; //将SPU的状态变为审核通过
			OmAvailableModel :: addTNameRow2arr($tName, $spuNoPass); //想SPU档案表中添加该信息

			$tName = 'pc_auto_create_spu';
			$set = "SET status=2";
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: updateTNameRow($tName, $set, $where); //将自动生成SPU列表中的该spu改成进入系统

			$tName = 'pc_spu_archive_no_pass_record';
			$updateData = array ();
			$updateData['isCounterAudit'] = 2; //标记这条记录已经被反审核过
			$updateData['counterAuditorId'] = $userId;
			$updateData['counterAuditTime'] = time();
			$where = "WHERE id=$cid";
			OmAvailableModel :: updateTNameRow2arr($tName, $updateData, $where); //在审核不通过记录表中更新反审核记录

			BaseModel :: commit();
			BaseModel :: autoCommit();
			self :: $errCode = 200;
			self :: $errMsg = "{$spuNoPass['spu']} 反审核成功";
			return true;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			self :: $errCode = 404;
			self :: $errMsg = $e->getMessage();
			return false;
		}
	}

	//单个删除组合料号
	function act_deleteComSku() {
		$comSku = $_POST['comSku'] ? post_check(trim($_POST['comSku'])) : '';
		if (empty ($comSku)) {
			self :: $errCode = '101';
			self :: $errMsg = '料号为空！';
			return false;
		}
		try {
			BaseModel :: begin();
			$tName = 'pc_goods_combine';
			$set = "SET is_delete=1";
			$where = "WHERE combineSku='$comSku'";
			OmAvailableModel :: updateTNameRow($tName, $set, $where);
			$tName = 'pc_sku_combine_relation';
			$where = "WHERE combineSku='$comSku'";
			OmAvailableModel :: deleteTNameRow($tName, $where);
			BaseModel :: commit();
			BaseModel :: autoCommit();
			$array = array (
				'goods_sn' => $comSku
			);
			OmAvailableModel :: newData2ErpInterfOpen('pc.erp.deleteCombine', $array, 'gw88');
			self :: $errCode = '200';
			self :: $errMsg = "$comSku 删除成功";
			return true;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			self :: $errCode = '404';
			self :: $errMsg = $e->getMessage();
			return false;
		}
	}

	//对接深圳ERP系统，取得对应sku对应的仓位信息，skuArr为数组
	function act_getLocationByArrFromERP() {
		$skuArr = isset ($_POST['skuArr']) ? $_POST['skuArr'] : ""; //料号条码
		if (!is_array($skuArr) || empty ($skuArr)) { //$spu是一个数组
			return false;
		}
		$returnArr = array ();
        /*
		foreach ($skuArr as $sku) {
			if (empty ($sku)) {
				continue;
			}
			$goods_location = UserCacheModel :: getOpenSysApi('pc.erp.getGoodsLocationFromEbay_goods2', array (
				'goods_sn' => $sku
			), 'gw88', false);
			if (!empty ($goods_location)) {
				$returnArr[$sku] = $goods_location;
			}
		}
        */
        //换成新的方式获取仓位
        $tmpArr = array();
        foreach($skuArr as $sku){
            $tmpArr[] = "'".$sku."'";
        }
        $tmpStr = implode(',', $tmpArr);
        if(!empty($tmpArr)){
            $tName = 'pc_goods_whId_location_raletion';
            $select = 'sku,location';
            $where = "WHERE sku in($tmpStr)";
            $skuLocationList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($skuLocationList as $value){
                if(!empty($value['location'])){
                    $returnArr[$value['sku']] = $value['location'];
                }                
            }
        }
		return $returnArr;
	}

	function act_sessionStart() {
		@ session_start();
		if (intval($_SESSION['userId']) <= 0) {
			redirect_to(WEB_URL . "index.php?mod=public&act=login"); // 跳转到登陆页
			exit;
		}
	}

    //批量修改成本
    function act_updateCostBatch() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : "";
        $goodsCost = isset ($_POST['goodsCost']) ? $_POST['goodsCost'] : 0;
        $userId = $_SESSION['userId'];
        if(empty($spu) || empty($goodsCost)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU或者成本为空";
			return false;
        }
        if(intval($userId) <= 0){
            self :: $errCode = '103';
			self :: $errMsg = "登陆超时，请重试";
			return false;
        }
        $tName = 'pc_goods';
        $select = 'sku,goodsCost';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($skuList)){
            self :: $errCode = '102';
			self :: $errMsg = "不存在该SPU的料号";
			return false;
        }
        try{
            BaseModel::begin();
            //更新成本
            $tName = 'pc_goods';
            $set = "SET goodsCost='$goodsCost'";
            $where = "WHERE spu='$spu'";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            $skuListarr = array();
            foreach($skuList as $value){
                $sku = $value['sku'];
                $oldGoodsCost = $value['goodsCost'];
                if($oldGoodsCost != $goodsCost){
                    addCostBackupsModify($sku, $goodsCost, $userId);//添加成本历史记录
                    $skuListarr[] = $value['sku'];
                }
            }
            $skuListStr = implode(',', $skuListarr);//已经更新的SKU字符串
            //同步数据到旧系统
            $goodsArr = array();
            $goodsArr['goods_sn'] = $spu;
            $goodsArr['goods_cost'] = $goodsCost;
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateCostBatch',$goodsArr,'gw88');//同步到旧ERP系统中
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
			self :: $errMsg = !empty($skuListStr)?"SPU下料号：$skuListStr 成本批量更新成功!":"无修改！";
			return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
			self :: $errMsg = "更新失败，请联系相关人员！";
			return false;
        }
	}

    //批量修改状态
    function act_updateStatusBatch() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : "";
        $goodsStatus = isset ($_POST['goodsStatus']) ? $_POST['goodsStatus'] : 0;
        $reason = isset ($_POST['reason']) ? $_POST['reason'] : '';
        $userId = $_SESSION['userId'];
        $now = time();
        if(empty($spu) || empty($goodsStatus)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU或者状态为空";
			return false;
        }
        if(intval($userId) <= 0){
            self :: $errCode = '103';
			self :: $errMsg = "登陆超时，请重试";
			return false;
        }
        $tName = 'pc_goods';
        $select = 'sku,goodsStatus';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($skuList)){
            self :: $errCode = '102';
			self :: $errMsg = "不存在该SPU的料号";
			return false;
        }
        try{
            BaseModel::begin();
            //更新状态
            $tName = 'pc_goods';
            $set = "SET goodsStatus='$goodsStatus',goodsUpdateTime='$now'";
            $where = "WHERE spu='$spu'";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            $skuListarr = array();
            foreach($skuList as $value){
                $sku = $value['sku'];
                $oldGoodsStatus = $value['goodsStatus'];
                if($oldGoodsStatus != $goodsStatus){
                    addStatusBackupsModify($sku, $goodsStatus, $reason, $userId);//添加成本历史记录
                    $skuListarr[] = $value['sku'];
                }
            }
            $skuListStr = implode(',', $skuListarr);//已经更新的SKU字符串
            //同步数据到旧系统
            $goodsArr = array();
            $goodsArr['goods_sn'] = $spu;

            if($goodsStatus == 1){//在线
                $goodsArr['isuse'] = 0;
            }elseif($goodsStatus == 51){//PK产品
                $goodsArr['isuse'] = 51;
            }elseif($goodsStatus == 2){//停售
                $goodsArr['isuse'] = 1;
            }elseif($goodsStatus == 3){//暂时停售
                $goodsArr['isuse'] = 3;
            }else{//其余的都做下线处理
                $goodsArr['isuse'] = 1;
            }
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateStatusBatch',$goodsArr,'gw88');//同步到旧ERP系统中
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
			self :: $errMsg = !empty($skuListStr)?"SPU下料号：$skuListStr 状态批量更新成功!":"无修改！";
			return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
			self :: $errMsg = "更新失败，请联系相关人员！";
			return false;
        }
	}

    //批量修改新/老品
    function act_updateIsNewBatch() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : "";
        $isNew = isset ($_POST['isNew']) ? $_POST['isNew'] : 0;
        $userId = $_SESSION['userId'];
        if(empty($spu)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU为空";
			return false;
        }
        if(intval($userId) <= 0){
            self :: $errCode = '103';
			self :: $errMsg = "登陆超时，请重试";
			return false;
        }
        $tName = 'pc_goods';
        $select = 'sku,isNew';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($skuList)){
            self :: $errCode = '102';
			self :: $errMsg = "不存在该SPU的料号";
			return false;
        }
        try{
            BaseModel::begin();
            //更新新/老品
            $tName = 'pc_goods';
            $set = "SET isNew='$isNew'";
            $where = "WHERE spu='$spu'";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            $skuListarr = array();
            foreach($skuList as $value){
                $sku = $value['sku'];
                $oldIsNew = $value['isNew'];
                if($oldIsNew != $isNew){
                    $skuListarr[] = $value['sku'];
                }
            }
            $skuListStr = implode(',', $skuListarr);//已经更新的SKU字符串
            $isNewStr = $isNew == 1?'新品':'老品';
            $addUserName = getPersonNameById($userId);
            error_log(date('Y-m-d_H:i')."—— spu:$spu 批量更新新/老品为 $isNewStr by $addUserName \r\n",3,WEB_PATH."log/batchModifySkuIsNew.txt");
            //同步数据到旧系统
            $goodsArr = array();
            $goodsArr['goods_sn'] = $spu;
            $goodsArr['is_new'] = $isNew;
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateIsNewBatch',$goodsArr,'gw88');//同步到旧ERP系统中
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
			self :: $errMsg = !empty($skuListStr)?"SPU下料号：$skuListStr 新/老品批量更新成功!":"无修改！";
			return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
			self :: $errMsg = "更新失败，请联系相关人员！";
			return false;
        }
	}

    //批量修改供应商
    function act_updateParterIdBatch() {
		$spu = isset ($_POST['spu']) ? $_POST['spu'] : "";
        $partnerId = isset ($_POST['partnerId']) ? $_POST['partnerId'] : 0;
        $userId = $_SESSION['userId'];
        if(empty($spu) || empty($partnerId)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU或供应商为空";
			return false;
        }
        if(intval($userId) <= 0){
            self :: $errCode = '103';
			self :: $errMsg = "登陆超时，请重试";
			return false;
        }
        $tName = 'pc_goods';
        $select = 'sku';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($skuList)){
            self :: $errCode = '102';
			self :: $errMsg = "不存在该SPU的料号";
			return false;
        }
        $skuListArr = array();
        foreach($skuList as $value){
            $skuListArr[] = "'".$value['sku']."'";
        }
        $skuListStr = implode(',', $skuListArr);
        try{
            BaseModel::begin();
            //更新新/老品

            $tName = 'pc_goods_partner_relation';
            $where = "WHERE sku in($skuListStr)";
            OmAvailableModel::deleteTNameRow($tName, $where);//先删除旧的供应商对应关系
            foreach($skuList as $value){
                $partnerRelationArr = array();
                $partnerRelationArr['sku'] = $value['sku'];
                $partnerRelationArr['partnerId'] = $partnerId;
                OmAvailableModel::addTNameRow2arr($tName, $partnerRelationArr);
            }
            //同步数据到旧系统
            $goodsArr = array();
            $goodsArr['goods_sn'] = $spu;
            $goodsArr['factory'] = $partnerId;
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateParterIdBatch',$goodsArr,'gw88');//同步到旧ERP系统中
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
			self :: $errMsg = "SPU $spu 下所有料号批量更新供应商成功!";
			return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
			self :: $errMsg = "更新失败，请联系相关人员！";
			return false;
        }
	}

    function  act_getSpuHscodeTaxList(){
        $spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';//spu

        $tName = 'pc_spu_tax_hscode';
        $select = '*';
        $where = "WHERE 1=1 ";//退料单 的iostoreTypeId=2

        if(!empty($spu)){
            $where .= "AND spu='$spu' ";
        }

        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id desc ".$page->limit;
		$spuHscodeTaxList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($spuHscodeTaxList as $key=>$value){
            $tName = 'pc_goods';
            $select = 'goodsName';
            $where = "WHERE is_delete=0 AND spu='{$value['spu']}' limit 1";
            $spuNameList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($spuNameList)){
                $spuHscodeTaxList[$key]['spuName'] = $spuNameList[0]['goodsName'];
            }
        }
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
        return array('spuHscodeTaxList'=>$spuHscodeTaxList,'show_page'=>$show_page);
	}

    //销售更改统一状态的接口
	function act_updateIsAgreeStatusForSaler() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
        $platformId = $_POST['platformId'] ? post_check(trim($_POST['platformId'])) : 0;
        $isSingSpu = $_POST['isSingSpu'] ? post_check(trim($_POST['isSingSpu'])) : 0;
        $value = $_POST['value'] ? post_check(trim($_POST['value'])) : 0;
		if (empty ($spu) || !in_array($platformId, array(1,2,11,14)) || !in_array($value, array(2,3)) || !in_array($isSingSpu, array(1,2))) {
			self :: $errCode = '101';
			self :: $errMsg = '错误！';
			return false;
		}
        if($isSingSpu == 1){
            $tName = 'pc_spu_saler_single';
        }else{
            $tName = 'pc_spu_saler_combine';
        }
        $where = "WHERE is_delete=0 and spu='$spu' and platformId='$platformId'";
        $dataArr = array();
		$dataArr['isAgree'] = $value;
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        self :: $errCode = '200';
		self :: $errMsg = '操作成功';
		return true;
	}

    //产品制作人更改统一状态的接口
	function act_updateIsAgreeStatusForWebMaker() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
        $platformId = $_POST['platformId'] ? post_check(trim($_POST['platformId'])) : 0;
        $isSingSpu = $_POST['isSingSpu'] ? post_check(trim($_POST['isSingSpu'])) : 0;
        $value = $_POST['value'] ? post_check(trim($_POST['value'])) : 0;
		if (empty ($spu) || !in_array($platformId, array(999)) || !in_array($value, array(2,3)) || !in_array($isSingSpu, array(1,2))) {
			self :: $errCode = '101';
			self :: $errMsg = '错误！';
			return false;
		}
        $tName = 'pc_spu_web_maker';
        $where = "WHERE is_delete=0 and spu='$spu' and isSingSpu='$isSingSpu' order by id desc limit 1";
        $dataArr = array();
		$dataArr['isAgree'] = $value;
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        self :: $errCode = '200';
		self :: $errMsg = '操作成功';
		return true;
	}

    //根据真实SPU返回对应的虚拟SPU字符串
	function act_isExistBySpu() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
		if (empty($spu)) {
			self :: $errCode = '101';
			self :: $errMsg = 'SPU为空';
			return false;
		}
        $spuFlag = isSpuExist($spu);
        if(!$spuFlag){
            $tName = 'pc_goods_combine';
            $where = "WHERE is_delete=0 and combineSpu='$spu'";
            $combineSpuCount = OmAvailableModel::getTNameCount($tName, $where);
            if($combineSpuCount){
                $spuFlag = true;
            }
        }
        if($spuFlag){
            self :: $errCode = '200';
    		self :: $errMsg = '成功';
    		return true;
        }else{
            self :: $errCode = '404';
    		self :: $errMsg = '不存在该SPU';
    		return false;
        }

	}

    //根据真实SPU返回对应的虚拟SPU字符串
	function act_getCombineSpuBySpu() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';

		if (empty($spu)) {
			self :: $errCode = '101';
			self :: $errMsg = 'SPU为空';
			return false;
		}
        $combineSpuArr = getCombineSpuBySpu($spu);
        $str = '';
        if(!empty($combineSpuArr)){
            $str = implode(',', $combineSpuArr);
        }
        self :: $errCode = '200';
		self :: $errMsg = '操作成功';
		return $str;
	}

    //添加修改流程记录
	function act_addSpuModityRecordOn() {

		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
        $combineSpuRelativeContent = $_POST['combineSpuRelativeContent'] ? post_check(trim($_POST['combineSpuRelativeContent'])) : '';
        $recordType = $_POST['recordType'] ? intval($_POST['recordType']) : 0;
        $modityContent = $_POST['modityContent'] ? ($_POST['modityContent']) : '';
        $note = $_POST['note'] ? post_check(trim($_POST['note'])) : '';
        $modifyTypeName = $_POST['modifyTypeName']?$_POST['modifyTypeName']:'';
		$userId = $_SESSION['userId'];
        if (empty($spu)) {
			$status = "SPU为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
		}
        if ($recordType <= 0) {
			$status = "修改/优化 信息有误！";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
		}
        if (empty($modityContent)) {
			$status = "修改内容为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
		}
        if (empty($modifyTypeName)) {
			$status = "修改类型为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
		}
        if(intval($userId) <= 0){
            $status = "登陆超时";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        $dataArr = array();
        $dataArr['spu'] = $spu;
        $dataArr['combineSpuRelativeContent'] = $combineSpuRelativeContent;
        $dataArr['recordType'] = $recordType;
        $dataArr['modifyTypeName'] = implode(',', $modifyTypeName);
        $dataArr['modityContent'] = base64_encode($modityContent);
        $dataArr['note'] = $note;
        $dataArr['addUserId'] = $userId;
        $dataArr['addTime'] = time();
        $tName = 'pc_spu_web_maker';
        $select = 'webMakerId';
        $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
        $PEIdList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($PEIdList)){
            $dataArr['PEId'] = $PEIdList[0]['webMakerId'];
        }
        $tName = 'pc_spu_modify_record';
        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        $status = "添加成功";
		echo '<script language="javascript">
                alert("'.$status.'");
              </script>';
        header("Location:index.php?mod=autoCreateSpu&act=getSpuModityRecordList&statusSee=$status");
		exit;
	}

    //单个删除修改流程的记录
    function act_deleteSpuModifyRecordById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_spu_modify_record';
        $set = "SET is_delete=1";
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        self :: $errCode = '200';
		self :: $errMsg = "删除成功";
		return true;
	}

    //领取修改流程的记录
    function act_takeSpuModifyRecordById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $dataArr = array();
        $dataArr['status'] = 2;
        $dataArr['handleTime'] = time();
        $tName = 'pc_spu_modify_record';
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        self :: $errCode = '200';
		self :: $errMsg = "领取成功";
		return true;
	}

    //完成修改流程的记录
    function act_completeSpuModifyRecordById() {
		$id = intval($_POST['id']);
        $isRephoto = intval($_POST['isRephoto']);
        $photoCount = intval($_POST['photoCount']);
        $isModityDescri = intval($_POST['isModityDescri']);
        $isAddSku = intval($_POST['isAddSku']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录';
			return false;
        }
        if($isRephoto <= 0){
            self :: $errCode = '102';
			self :: $errMsg = '是否重拍信息有误';
			return false;
        }
        if($photoCount <= 0){
            self :: $errCode = '103';
			self :: $errMsg = '图片张数有误';
			return false;
        }
        if($isModityDescri <= 0){
            self :: $errCode = '104';
			self :: $errMsg = '是否修改描述信息有误';
			return false;
        }
        if($isAddSku <= 0){
            self :: $errCode = '104';
			self :: $errMsg = '是否添加子料号信息有误';
			return false;
        }
        $dataArr = array();
        $dataArr['status'] = 3;
        $dataArr['completeTime'] = time();
        $dataArr['isRephoto'] = $isRephoto;
        $dataArr['photoCount'] = $photoCount;
        $dataArr['isModityDescri'] = $isModityDescri;
        $dataArr['isAddSku'] = $isAddSku;
        $tName = 'pc_spu_modify_record';
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        self :: $errCode = '200';
		self :: $errMsg = "完成成功";
		return true;
	}

    //SPU修改流程的提交追加内容记录
    function act_appendSpuModityRecordOn() {
		$id = intval($_POST['id']);
        $appendContent1 = $_POST['appendContent1'];
        if($id <= 0){
            $status = "无效记录";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        if(empty($appendContent1)){
            $status = "追加内容为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        if(intval($_SESSION['userId']) <= 0){
            $status = "登陆超时";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        $dataArr = array();
        $dataArr['appendContent1'] = base64_encode($appendContent1);
        $dataArr['appendContent1Time'] = time();
        $tName = 'pc_spu_modify_record';
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        $status = "追加成功";
		echo '<script language="javascript">
                alert("'.$status.'");
              </script>';
        header("Location:index.php?mod=autoCreateSpu&act=getSpuModityRecordList&statusSee=$status");
		exit;
	}

    //SPU修改流程的提交修改产品工程师
    function act_updateSpuModifyRecordPEIdOn() {
		$id = intval($_POST['id']);
        $PEId = intval($_POST['PEId']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录';
			return false;
        }
        if($PEId <= 0){
            self :: $errCode = '102';
			self :: $errMsg = '产品工程师为空';
			return false;
        }
        $dataArr = array();
        $dataArr['PEId'] = $PEId;
        $tName = 'pc_spu_modify_record';
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        self :: $errCode = '200';
		self :: $errMsg = "修改产品制作人成功";
		return true;
	}

    //SPU修改流程的提交修改产品工程师
    function act_getNearestSkuWeight() {
		$sku = $_POST['sku'];
        $skuWeight = 0;
        $tName = 'pc_goods';
        $select = 'goodsWeight';
        $where = "WHERE is_delete=0 and sku='$sku'";
        $skuWeightList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuWeightList)){
            $skuWeight = $skuWeightList[0]['goodsWeight'];
        }
        self :: $errCode = '200';
		self :: $errMsg = "成功";
		return $skuWeight;
	}

    //审核sku重量审核列表记录
    function act_auditSkuWeight() {
		$id = intval($_POST['id']);
        $auditValue = intval($_POST['auditValue']);
        $userId = intval($_SESSION['userId']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录';
			return false;
        }
        if($auditValue <= 0){
            self :: $errCode = '102';
			self :: $errMsg = '无效状态';
			return false;
        }
        if($userId <= 0){
            self :: $errCode = '103';
			self :: $errMsg = '登陆超时';
			return false;
        }
        $tName = 'pc_goods_weight_audit';
        $select = 'sku,newWeight';
        $where = "WHERE is_delete=0 and id='$id'";
        $auditList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($auditList)){
            self :: $errCode = '104';
			self :: $errMsg = '记录为空';
			return false;
        }
        try{
            BaseModel::begin();
            $dataArr = array();
            $dataArr['status'] = $auditValue;
            $dataArr['auditerId'] = $userId;
            $dataArr['auditTime'] = time();
            $tName = 'pc_goods_weight_audit';
            $where = "WHERE id=$id";
            OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
            if($auditValue == 2){//审核通过
                addWeightBackupsModify($auditList[0]['sku'], $auditList[0]['newWeight'], $userId);//添加重量变化记录
                $tName = 'pc_goods';
                $where = "where is_delete=0 and sku='{$auditList[0]['sku']}'";
                $dataArr = array();
                $dataArr['goodsWeight'] = $auditList[0]['newWeight'];
                OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
                //同步数据到深圳erp
                $paraArr['goods_sn'] = $auditList[0]['sku'];
    			$paraArr['goods_weight'] = $auditList[0]['newWeight'];
    			$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsSnWeight', $paraArr, 'gw88');
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
    		self :: $errMsg = "操作成功";
    		return true;
        }catch(Excetion $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
    		self :: $errMsg = "系统错误--".$e->getMessage();
    		return true;
        }

	}

    //SPU修改流程的提交修改产品工程师
    function act_getSkuBySpu() {
		$spu = $_POST['spu'];
        $tName = 'pc_goods';
        $select = 'sku';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuList)){
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return $skuList;
        }else{
            self :: $errCode = '400';
    		self :: $errMsg = "该SPU下无SKU信息";
    		return false;
        }

	}

    //指派数量维护中修改指定记录的数量
    function act_updatePECountOn() {
		$id = !empty($_POST['id'])?$_POST['id']:0;
        $count = !empty($_POST['count'])?$_POST['count']:0;
        $addUserId = $_SESSION['userId'];
        if(intval($id) <= 0){
            self :: $errCode = '101';
    		self :: $errMsg = "无效记录";
    		return false;
        }
        if(intval($count) <= 0){
            self :: $errCode = '102';
    		self :: $errMsg = "数量必须为正整数";
    		return false;
        }
        if(intval($addUserId) <= 0){
            self :: $errCode = '110';
    		self :: $errMsg = "登陆超时";
    		return false;
        }
        $tName = 'pc_products_pe_count';
        $select = 'count';
        $where = "WHERE is_delete=0 and id='$id'";
        $PECountList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($PECountList)){
            self :: $errCode = '103';
    		self :: $errMsg = "记录不存在";
    		return false;
        }
        if($PECountList[0]['count'] == $count){
            self :: $errCode = '104';
    		self :: $errMsg = "无修改";
    		return false;
        }
        $dataArr = array();
        $dataArr['count'] = $count;
        $dataArr['lastUpdateTime'] = time();
        OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);

        self :: $errCode = '200';
		self :: $errMsg = "修改成功";
		return $skuList;
	}

    function act_addPECountOn() {
		$PEId = intval($_POST['PEId']);
        $count = intval($_POST['count']);
        if($PEId <= 0){
            $status = "无效记录";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        if($count <= 0){
            $status = "数量必须为正整数";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        if(intval($_SESSION['userId']) <= 0){
            $status = "登陆超时";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        $tName = 'pc_products_pe_count';
        $where = "WHERE is_delete=0 AND PEId='$PEId'";
        $isExsit = OmAvailableModel::getTNameCount($tName, $where);
        if($isExsit){
            $status = "已经存在该产品工程师的指派数量记录";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        $dataArr = array();
        $dataArr['PEId'] = $PEId;
        $dataArr['count'] = $count;
        $dataArr['addUserId'] = $_SESSION['userId'];
        $dataArr['addTime'] = time();
        $tName = 'pc_products_pe_count';
        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        $status = "添加成功";
		echo '<script language="javascript">
                alert("'.$status.'");
              </script>';
        header("Location:index.php?mod=products&act=getProductsPECountList&status=$status");
		exit;
	}

    //updateSpuPerson.htm下提交产品制作人验证
    function act_checkSubmitWebMaker() {
		$spu = $_POST['spu'];
        $isSingSpu = $_POST['isSingSpu'];
        $webMakerId = $_POST['webMakerId'];
        if($isSingSpu == 2){//虚拟料号
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return true;
        }
        if(intval($webMakerId) <= 0 || isAccessAll('autoCreateSpu','isCanUpdateWebMakerPower')){
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return true;
        }
        $tName = 'pc_spu_web_maker';
        $select = 'webMakerId';
        $where = "WHERE is_delete=0 and spu='$spu' order by id desc";
        $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
        if($spuWebMakerList[0]['webMakerId'] == $webMakerId){
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return true;
        }
        /* 限制暂时不用
        $platformIdArr = array(1,2,11);//其中一个平台必须要有对应销售人员记录
        $platformIdStr = implode(',', $platformIdArr);
        $tName = 'pc_spu_saler_single';
        $where = "WHERE is_delete=0 AND spu='$spu' AND platformId in($platformIdStr)";
        $singleSalerInfoCount = OmAvailableModel::getTNameCount($tName, $where);
        if(!$singleSalerInfoCount){
            self :: $errCode = '400';
    		self :: $errMsg = "ebay/aliexpress/amazon 平台中至少要存在一个销售人员记录";
    		return false;
        }
        **/
        $tName = 'pc_spu_web_maker';
        $select = 'isTake,isAgree';
        $where = "WHERE is_delete=0 and spu='$spu' and webMakerId='$webMakerId' order by id desc";
        $webMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
        if($webMakerList[0]['isTake'] != 0){
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return true;
        }
        $appointedCountToWebMaker = getAppointSpuCountByWebMakerId($webMakerId);//已经指派给产品制作人的数量
        $countPE = getPECountByPEId($webMakerId);//该产品制作人最多能被指派的数量
        if($appointedCountToWebMaker < $countPE){
            self :: $errCode = '200';
    		self :: $errMsg = "成功";
    		return true;
        }else{
            self :: $errCode = '404';
    		self :: $errMsg = "该产品制作人数量已达极限，请重新选择产品制作人";
    		return false;
        }

	}
    
    //单个删除料号转换记录
    function act_deleteSkuConversion() {
		$id = intval($_GET['id']);
        if($id <= 0){
            $status = "无效记录";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    window.history.back();
                  </script>';
			exit;
        }
        $tName = 'pc_sku_conversion';
        $set = "SET is_delete=1";
        $where = "WHERE auditStatus=1 AND id=$id";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        $status = "删除成功";
		echo '<script language="javascript">
                alert("'.$status.'");
                window.history.back();
              </script>';
		exit;
	}
    
    //根据SPU调用QC的接口返回对应SPU最近的检测员ID
    function act_getSpuQcUserBySpu() {
		$spu = $_POST['spu'];
        if(empty($spu)){
            self :: $errCode = '101';
    		self :: $errMsg = "SPU为空";
    		return false;
        }
        $spuQcUserIdList = UserCacheModel::getOpenSysApi('qc.getDetecorBySupArr',array('spuArr'=>json_encode(array($spu))));
        $qcUserId = $spuQcUserIdList['data'][$spu];
        if(intval($qcUserId) <= 0){
            self :: $errCode = '404';
    		self :: $errMsg = "接口返回为空";
    		return false;
        }else{
            self :: $errCode = '200';
    		self :: $errMsg = "返回成功";
    		return getPersonNameById($qcUserId);
        }       
	}
    
    //修改产品档案的类别
    function act_updateSpuArchivePid() {
		$spu = $_POST['spu'];
        $pid = $_POST['pid'];
        $userId = $_SESSION['userId'];
        if(intval($userId) <= 0){
            self :: $errCode = '100';
    		self :: $errMsg = "登陆超时，请重试";
    		return false;
        }
        if(empty($spu) || empty($pid)){
            self :: $errCode = '101';
    		self :: $errMsg = "异常，错误！";
    		return false;
        }
        $tName = 'pc_spu_archive';
        $select = 'categoryPath';
        $where = "WHERE is_delete=0 AND spu='$spu'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuList)){
            self :: $errCode = '102';
    		self :: $errMsg = "该SPU档案不存在";
    		return false;
        }
        if($spuList[0]['categoryPath'] == $pid){
            self :: $errCode = '110';
    		self :: $errMsg = "类别无修改";
    		return false;
        }
        $tName = 'pc_goods_category';
		$where = "WHERE path='$pid' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if(!$count){
			self :: $errCode = '103';
    		self :: $errMsg = "所选类别不存在，请刷新重试！";
    		return false;
		}
		$where = "WHERE path like'$pid-%' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if($count){
            self :: $errCode = '104';
    		self :: $errMsg = "产品档案只能建立在最小分类下，请选择最小分类";
    		return false;
		}
        try {
			BaseModel :: begin();

			$tName = 'pc_spu_archive';
			$where = "WHERE spu='$spu'";
            $dataTmpArr = array();
            $dataTmpArr['categoryPath'] = $pid;
			OmAvailableModel::updateTNameRow2arr($tName, $dataTmpArr, $where);//更新SPU档案的类别

            $tName = 'pc_archive_spu_property_value_relation';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除该SPU产品档案选择属性记录

            $tName = 'pc_archive_spu_input_value_relation';
			$where = "WHERE spu='$spu'";
			OmAvailableModel :: deleteTNameRow($tName, $where); //物理删除SPU产品档案文本记录
            
            $tName = 'pc_goods';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $dataTmpArr = array();
            $dataTmpArr['goodsCategory'] = $pid;
            OmAvailableModel::updateTNameRow2arr($tName, $dataTmpArr, $where);//更新该SPU下所有SKU的类别
            
			BaseModel :: commit();
			BaseModel :: autoCommit();
            OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateGoodsCategoryBySpu',array('spu'=>$spu,'pid'=>$pid),'gw88');
			$personName = getPersonNameById($userId);
            error_log(date('Y-m-d_H:i')." $personName 将SPU:$spu 类别改为 $pid 原始类别为: {$spuList[0]['categoryPath']} \r\n",3,WEB_PATH."log/updateSpuCategoryLog.txt");
            self :: $errCode = 200;
			self :: $errMsg = "修改成功";
			return true;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			self :: $errCode = 404;
			self :: $errMsg = '修改失败，原因为：'.$e->getMessage();
			return false;
		}
	}
    
    //根据id返回对应的SPU海关相关记录
    function act_getSpuHsListById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录';
			return false;
        }
        $tName = 'pc_spu_tax_hscode';
        $select = '*';
        $where = "WHERE id='$id'";
        $psthList = OmAvailableModel::getTNameList($tName, $select, $where);
        self :: $errCode = '200';
		self :: $errMsg = "成功";
		return $psthList[0];
	}
    
    //根据id修改SPU海关相关记录
    function act_updateSpuHsRelaById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录';
			return false;
        }
        $userId = $_SESSION['userId'];
        if($id <= 0){
            self :: $errCode = '103';
			self :: $errMsg = '登陆超时';
			return false;
        }
        $personName = getPersonNameById($userId);
        $tName = 'pc_spu_tax_hscode';
        $select = 'spu';
        $where = "WHERE id='$id'";
        $psthList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($psthList)){
            self :: $errCode = '102';
    		self :: $errMsg = "无记录";
    		return false;
        }
        $dataTmpArr = array();
        $dataTmpArr['customsName'] = !empty($_POST['customsName'])?$_POST['customsName']:'';
        $dataTmpArr['materialCN'] = !empty($_POST['materialCN'])?$_POST['materialCN']:'';
        $dataTmpArr['customsNameEN'] = !empty($_POST['customsNameEN'])?$_POST['customsNameEN']:'';
        $dataTmpArr['materialEN'] = !empty($_POST['materialEN'])?$_POST['materialEN']:'';
        $dataTmpArr['hsCode'] = !empty($_POST['hsCode'])?$_POST['hsCode']:'';
        $dataTmpArr['exportRebateRate'] = !empty($_POST['exportRebateRate'])?$_POST['exportRebateRate']:'';
        $dataTmpArr['importMFNRates'] = !empty($_POST['importMFNRates'])?$_POST['importMFNRates']:'';
        $dataTmpArr['generalRate'] = !empty($_POST['generalRate'])?$_POST['generalRate']:'';
        $dataTmpArr['RegulatoryConditions'] = !empty($_POST['RegulatoryConditions'])?$_POST['RegulatoryConditions']:'';
        $dataTmpArr = array_filter($dataTmpArr);
        $jsonData = json_encode($dataTmpArr);
        if(!empty($dataTmpArr)){
            OmAvailableModel::updateTNameRow2arr($tName, $dataTmpArr, $where);
            error_log(date('Y-m-d_H:i')."——{$psthList[0]['spu']} 更新成功 BY $personName, data: $jsonData \r\n",3,WEB_PATH."log/spuHscodeTax.txt");
            self :: $errCode = '200';
    		self :: $errMsg = "更新成功";
    		return true;
        }else{
            self :: $errCode = '200';
    		self :: $errMsg = "无数据提交";
    		return true;
        }
        
	}
    

}
?>