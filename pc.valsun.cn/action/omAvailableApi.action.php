<?php


/*
 * 通用actionApi
 * ADD BY zqt 2013.9.13
 */
class OmAvailableApiAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定表的记录,成功返回记录集数组，失败返回false
     *
	 */
	function act_getTNameList() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $select = $jsonArr['select'];//select，不用关键字SELECT
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($select) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$list = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (is_array($list)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $list;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 取得指定表的记录数,成功返回记录数count，失败返回false
     *
	 */
	function act_getTNameCount() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count !== false) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $count;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 添加记录到指定表，成功返回插入的记录ID，失败返回false
     *
	 */
	function act_addTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        if(empty($tName) || empty($set)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$insertId = OmAvailableModel :: addTNameRow($tName, $set);
		if ($insertId !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $insertId;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 修改指定表的记录,成功返回影响的记录数affectRows，失败返回false
     *
	 */
	function act_updateTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($set) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$affectRows = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $affectRows;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 根据sku返回该sku的信息，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getGoodsInfoBySku() {
	    $sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : ''; //sku
		if (empty ($sku)) {
			self :: $errCode = 101;
			self :: $errMsg = 'sku为空';
			return false;
		}
        $key = 'pc_goods_'.$sku;
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_goods';
            $select = '*';
            $where = "WHERE is_delete=0 and sku='$sku'";
    		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($skuList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $skuList[0], 0);
    			return $skuList[0];
    		} elseif(count($skuList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有该sku信息';
    			return array();
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
        }
	}

    /*
	 * 根据sku返回该sku的信息，支持sku或combineSKu,返回格式为array('sku1'=>array('skuInfo'=>array(info),'amount'=>1),'sku2'=>array('skuInfo'=>array(info),'amount'=>1))
     *
	 */
	function act_getGoodsInfoBySkuOrCombineSku() {
	    $sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : ''; //sku
		if (empty ($sku)) {
			self :: $errCode = 101;
			self :: $errMsg = 'sku为空';
			return false;
		}
        $returnArr = array();
        $tName = 'pc_goods';
        $select = '*';
        $where = "WHERE is_delete=0 and sku='$sku'";
		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if(!empty($skuList)){
          $tmpArr = array();
          $tmpArr['skuDetail'] = $skuList[0];
          $tmpArr['amount'] = 1;
          $returnArr['skuInfo'][$sku] = $tmpArr;
          $returnArr['isCombine'] = 0;//为0表示SKU不是组合料号
          self :: $errCode = 200;
		  self :: $errMsg = '真实SKU信息返回成功';
          return $returnArr;
		}
        $tName = 'pc_sku_combine_relation';
        $select = 'sku,count';
        $where = "WHERE combineSku='$sku'";
        $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuRelationList)){
            foreach($skuRelationList as $value){
                $tName = 'pc_goods';
                $select = '*';
                $where = "WHERE is_delete=0 and sku='{$value['sku']}'";
        		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
                $tmpArr = array();
                $tmpArr['skuDetail'] = !empty($skuList[0])?$skuList[0]:array();
                $tmpArr['amount'] = $value['count'];
                $returnArr['skuInfo'][$value['sku']] = $tmpArr;
                $returnArr['isCombine'] = 1;//表示SKU是组合料号

            }
            self :: $errCode = 200;
			self :: $errMsg = '虚拟SKU信息返回成功';
			return $returnArr;
        }else{
            self :: $errCode = 400;
			self :: $errMsg = '不存在该SKU信息';
			return false;
        }
	}

    /*
	 * 根据spu返回该spu的信息，无信息返回array(),有信息返回多条记录，错误返回false,单料号
     *
	 */
	function act_getGoodsInfoBySpu() {
	    $spu = isset ($_GET['spu']) ? post_check(trim($_GET['spu'])) : ''; //spu
		if (empty ($spu)) {
			self :: $errCode = 101;
			self :: $errMsg = 'spu为空';
			return false;
		}
        $tName = 'pc_goods';
        $select = '*';
        $where = "WHERE is_delete=0 and spu='$spu'";
		$spuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if(!empty($spuList)){
            self :: $errCode = 200;
			self :: $errMsg = '真实SPU信息返回成功';
			return $spuList;
		}
        $tName = 'pc_goods_combine';
        $select = '*';
        $where = "WHERE is_delete=0 and combineSpu='$spu'";
        $combineSpulist = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($combineSpulist)){
            $returnArr = array();
            foreach($combineSpulist as $value){
                $tmpArr = array();
                $tmpArr['id'] = $value['id'];
                $tmpArr['spu'] = $value['combineSpu'];
                $tmpArr['sku'] = $value['combineSku'];
                $tmpArr['purchaseId'] = $value['combineUserId'];
                $tmpArr['goodsCreatedTime'] = $value['addTime'];
                $cwArr = getTrueCWForCombineSku($value['combineSku']);//计算成本和重量
                $tmpArr['goodsCost'] = $cwArr['totalCost'];
                $tmpArr['goodsWeight'] = $cwArr['totalWeight'];
                //定义默认长，宽，高
                $goodsLength = 0;
                $goodsWidth = 0;
                $goodsHeight = 0;
                $count = 1;
                $tName = 'pc_sku_combine_relation';
                $select = '*';
                $where = "WHERE combineSku='{$value['combineSku']}'";
                $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($skuRelationList as $valueSku){
                    if($valueSku['count'] > $count){
                        $count = $valueSku['count'];
                    }
                    $tName = 'pc_goods';
                    $select = 'goodsLength,goodsWidth,goodsHeight';
                    $where = "WHERE is_delete=0 and sku='{$valueSku['sku']}'";
                    $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    if(!empty($skuList)){
                        if($skuList[0]['goodsLength'] > $goodsLength){
                            $goodsLength = $skuList[0]['goodsLength'];
                        }
                        if($skuList[0]['goodsWidth'] > $goodsWidth){
                            $goodsWidth = $skuList[0]['goodsWidth'];
                        }
                        if($skuList[0]['goodsHeight'] > $goodsHeight){
                            $goodsHeight = $skuList[0]['goodsHeight'];
                        }
                    }
                }
                $tmpArr['goodsLength'] = $goodsLength;
                $tmpArr['goodsWidth'] = $goodsWidth;
                $tmpArr['goodsHeight'] = $goodsHeight * $count;
                $returnArr[] = $tmpArr;
            }
            self :: $errCode = 200;
			self :: $errMsg = '虚拟SPU信息返回成功';
			return $returnArr;
        }else{
            self :: $errCode = 400;
			self :: $errMsg = '不存在该SPU的信息';
			return $spuList;
        }
	}

    /*
	 * 根据path返回该类别的信息，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getCategoryInfoByPath() {
	    $path = isset ($_GET['path']) ? post_check(trim($_GET['path'])) : ''; //sku
		if (empty($path)) {
			self :: $errCode = 101;
			self :: $errMsg = 'path为空';
			return false;
		}
        $key = 'pc_goods_category_'.$path;
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_goods_category';
            $select = '*';
            $where = "WHERE is_delete=0 and path='$path'";
    		$categoryList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($categoryList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $categoryList[0], 0);
    			return $categoryList[0];
    		} elseif(count($categoryList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有该path的类别信息';
    			return array();
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
   		     }
    	   }
   }

    /*
	 * 根据id返回该类别的信息，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getCategoryInfoById() {
	    $id = isset ($_GET['id']) ? post_check(trim($_GET['id'])) : ''; //sku
		if (intval ($id) == 0) {
			self :: $errCode = 101;
			self :: $errMsg = 'id不合法';
			return false;
		}
        $key = 'pc_goods_category_'.$id;
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_goods_category';
            $select = '*';
            $where = "WHERE is_delete=0 and id=$id";
    		$categoryList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($categoryList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $categoryList[0], 0);
    			return $categoryList[0];
    		} elseif(count($categoryList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有该id的类别信息';
    			return array();
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
    	  }
    }

    /*
	 * 根据pid返回对应类别，无信息返回array(),有信息返回多条记录（包括一条），错误返回false
     *
	 */
	function act_getCategoryInfoByPid() {
	    $pid = isset ($_GET['pid']) ? post_check(trim($_GET['pid'])) : ''; //sku
		if (!is_int($pid)) {
			$pid = intval($pid);
		}
        $tName = 'pc_goods_category';
        $select = '*';
        $where = "WHERE is_delete=0 and pid=$pid";
		$categoryList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (count($categoryList)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $categoryList;
		} elseif(count($categoryList) == 0){
            self :: $errCode = '201';
			self :: $errMsg = '没有该pid的类别信息';
			return array();
		}else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 返回所有类别，无信息返回array(),有信息返回多条记录（包括一条），错误返回false
     *
	 */
	function act_getCategoryInfoAll() {
	    $key = 'pc_goods_category_all';
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_goods_category';
            $select = '*';
            $where = "WHERE is_delete=0";
    		$categoryList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($categoryList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $categoryList, 0);
    			return $categoryList;
    		} elseif(count($categoryList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有类别信息';
    			return array();
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
        }

	}

    /*
	 * 根据sku返回对应供应商id，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getPartnerIdBySku() {
	    $sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : ''; //sku
		if (empty($sku)) {
			self :: $errCode = 101;
			self :: $errMsg = 'sku为空';
			return false;
		}
        $tName = 'pc_goods_partner_relation';
        $select = 'partnerId';
        $where = "WHERE sku='$sku'";
		$partnerIdList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (count($partnerIdList)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $partnerIdList[0]['partnerId'];
		} elseif(count($partnerIdList) == 0){
            self :: $errCode = '201';
			self :: $errMsg = '没有该sku对应的partnerId信息';
			return '';
		}else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

     /*
	 * 根据sku返回对应供应商id，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getSkuByPartnerId() {
	    $partnerId = isset ($_GET['partnerId']) ? post_check(trim($_GET['partnerId'])) : ''; //sku
		if (intval($partnerId) == 0) {
			self :: $errCode = 101;
			self :: $errMsg = 'partnerId有误';
			return false;
		}
        $tName = 'pc_goods_partner_relation';
        $select = 'sku';
        $where = "WHERE partnerId='$partnerId'";
		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (count($skuList)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $partnerIdList;
		} elseif(count($skuList) == 0){
            self :: $errCode = '201';
			self :: $errMsg = '没有对应partnerId的sku信息';
			return '';
		}else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 根据包材id返回对应包材信息，无信息返回array(),有信息只返回一条记录，错误返回false
     *
	 */
	function act_getPmInfoById() {
	    $id = isset ($_GET['id']) ? post_check(trim($_GET['id'])) : ''; //sku
		if (intval ($id) == 0) {
			self :: $errCode = 101;
			self :: $errMsg = 'id不合法';
			return false;
		}
        $key = 'pc_pm_'.$id;
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_packing_material';
            $select = '*';
            $where = "WHERE is_delete=0 and id=$id";
    		$pmList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($pmList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $pmList[0], 0);
    			return $pmList[0];
    		} elseif(count($pmList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有该id对应的包材信息';
    			return '';
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
        }

	}

    /*
	 * 根据所有包材信息，无信息返回array(),有信息返回多条记录，错误返回false
     *
	 */
	function act_getPmInfoAll() {
	    $key = 'pc_pm_all';
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if(false) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return $ret;
        } else {
            $tName = 'pc_packing_material';
            $select = '*';
            $where = "WHERE is_delete=0";
    		$pmList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($pmList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $pmList, 0);
    			return $pmList;
    		} elseif(count($pmList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有包材信息';
    			return '';
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
        }
	}


    /*
	 * 录入海关编码接口
     *
	 */
	function act_addHsCodeWithArr() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数不是数组格式';
			return false;
		}
        $sku = $jsonArr['sku']?post_check(trim($jsonArr['sku'])):'';//sku
        $DecNameCN = $jsonArr['DecNameCN']?post_check(trim($jsonArr['DecNameCN'])):'';//中文申报名称
        $DecNameEN = $jsonArr['DecNameEN']?post_check(trim($jsonArr['DecNameEN'])):'';//中文申报名称
        $hsCode = $jsonArr['hsCode']?post_check(trim($jsonArr['hsCode'])):'';//海关编码
        $taxRate = $jsonArr['taxRate']?post_check(trim($jsonArr['taxRate'])):0;//税率
        $isDrawTax = $jsonArr['isDrawTax']?post_check(trim($jsonArr['isDrawTax'])):1;//是否是退税产品，1为非退税产品，2为退税产品，默认为1
        $drawPoint = $jsonArr['drawPoint']?post_check(trim($jsonArr['drawPoint'])):0;//退税点
        if(empty($sku) || empty($hsCode)){
            self :: $errCode = '300';
			self :: $errMsg = 'sku或海关编码为空';
			return false;
        }
        if(!is_numeric($taxRate) || $taxRate < 0){
            self :: $errCode = '301';
			self :: $errMsg = '税率必须为非负数字';
			return false;
        }
        if(intval($isDrawTax) != 1 && intval($isDrawTax) != 2){
            self :: $errCode = '302';
			self :: $errMsg = '是否是退税产品参数有误，只能为1或2';
			return false;
        }
        if(!is_numeric($drawPoint) || $drawPoint < 0){
            self :: $errCode = '303';
			self :: $errMsg = '退税点必须为非负数字';
			return false;
        }
        $tName = 'pc_goods';
        $where = "WHERE sku='$sku' and is_delete=0";
        $countSku = OmAvailableModel::getTNameCount($tName, $where);
        if(!$countSku){
            self :: $errCode = '304';
			self :: $errMsg = '系统中没有该sku的记录';
			return false;
        }
        $tName = 'pc_sku_hscode';
        $where = "WHERE sku='$sku' and hsCode='$hsCode'";
        $countHsCode = OmAvailableModel::getTNameCount($tName, $where);
        if(!$countHsCode){
            self :: $errCode = '305';
			self :: $errMsg = "$sku 的海关编码 $hsCode 已经存在";
			return false;
        }
        $now = time();
        $set = "SET sku='$sku',DecNameCN='$DecNameCN',DecNameEN='$DecNameEN',hsCode='$hsCode',taxRate='$taxRate',isDrawTax='$isDrawTax',drawPoint='$drawPoint',createdTime='$now'";
		$insertId = OmAvailableModel::addTNameRow($tName, $set);
		if ($insertId) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $insertId;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 修改海关编码接口
     *
	 */
	function act_updateHsCodeWithArr() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数不是数组格式';
			return false;
		}
        $id = $jsonArr['id'];
        $sku = $jsonArr['sku'];//sku
        $DecNameCN = $jsonArr['DecNameCN'];//中文申报名称
        $DecNameEN = $jsonArr['DecNameEN'];//英文申报名称
        $hsCode = $jsonArr['hsCode'];//海关编码
        $taxRate = $jsonArr['taxRate'];//税率
        $isDrawTax = $jsonArr['isDrawTax'];//是否是退税产品，1为非退税产品，2为退税产品，默认为1
        $drawPoint = $jsonArr['drawPoint'];//退税点
        $isUsable = $jsonArr['isUsable'];//是否可用，1为可用，2为不可用，默认为1
        $lastUseTime = $jsonArr['lastUseTime'];//修改时间
        if(intval($id) <= 0){
            self :: $errCode = '201';
			self :: $errMsg = '非法id';
			return false;
        }
        if(empty($sku) && !empty($hsCode)){
            self :: $errCode = '300';
			self :: $errMsg = 'sku为空';
			return false;
        }
        if(!empty($sku) && empty($hsCode)){
            self :: $errCode = '300';
			self :: $errMsg = '海关编码为空';
			return false;
        }
        if(isset($taxRate)){
            if(!is_numeric($taxRate) || $taxRate < 0){
                self :: $errCode = '301';
    			self :: $errMsg = '税率必须为非负数字';
    			return false;
            }
        }

        if(isset($isDrawTax)){
            if(intval($isDrawTax) != 1 && intval($isDrawTax) != 2){
                self :: $errCode = '302';
    			self :: $errMsg = '是否为退税产品参数有误，只能为1或2';
    			return false;
            }
        }

        if(isset($drawPoint)){
            if(!is_numeric($drawPoint) || $drawPoint < 0){
                self :: $errCode = '303';
    			self :: $errMsg = '退税点必须为非负数字';
    			return false;
            }
        }


        if(isset($isUsable)){
            if(intval($isUsable) != 1 && intval($isUsable) != 2){
                self :: $errCode = '302';
    			self :: $errMsg = '是否可用参数有误，只能为1或2';
    			return false;
            }
        }

        if(isset($lastUseTime)){
            $lastUseTime = time();
        }

        $tName = 'pc_sku_hscode';
        $where = "WHERE id=$id";
        $countId = OmAvailableModel::getTNameCount($tName, $where);
        if(!$countId){
            self :: $errCode = '309';
			self :: $errMsg = '找不到指定id的记录';
			return false;
        }
        if(!empty($sku) && !empty($hsCode)){
            $where = "WHERE sku='$sku' and hsCode='$hsCode' and id<>$id";
            $countSH = OmAvailableModel::getTNameCount($tName, $where);
            if($countSH){
                 self :: $errCode = '203';
			     self :: $errMsg = "$sku $hsCode 记录已存在";
			     return false;
            }
        }
        $insertId = 1;
        if(isset($sku) || isset($DecNameCN) || isset($DecNameEN) || isset($hsCode) || isset($taxRate) || isset($isDrawTax) || isset($drawPoint) || isset($isUsable) || isset($lastUseTime)){
            $set = "SET id=id";
            if(isset($sku)){
                $set .= ",sku='$sku'";
            }
            if(isset($DecNameCN)){
                $set .= ",DecNameCN='$DecNameCN'";
            }
            if(isset($DecNameEN)){
                $set .= ",DecNameEN='$DecNameEN'";
            }
            if(isset($hsCode)){
                $set .= ",hsCode='$hsCode'";
            }
            if(isset($taxRate)){
                $set .= ",taxRate='$taxRate'";
            }
            if(isset($isDrawTax)){
                $set .= ",isDrawTax='$isDrawTax'";
            }
            if(isset($drawPoint)){
                $set .= ",drawPoint='$drawPoint'";
            }
            if(isset($isUsable)){
                $set .= ",isUsable='$isUsable'";
            }
            if(isset($lastUseTime)){
                $set .= ",lastUseTime='$lastUseTime'";
            }
            $where = " WHERE id=$id";
            $insertId = OmAvailableModel::addTNameRow($tName, $set);
        }
		if ($insertId) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $insertId;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    //根据sku获取其对应的海关编码记录
    function act_getHsCodeListBySku(){
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';//sku
        if(empty($sku)){
            self :: $errCode = '101';
			self :: $errMsg = 'sku为空';
			return false;
        }
        $tName = 'pc_sku_hscode';
        $where = "WHERE sku='$sku'";
        $select = '*';
        $hsCodeList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $hsCodeList;
    }

    //速卖通刊登组合料号调用接口，根据指定spu,amount,combineUserId返回对应信息
    function act_searchOrAddCombineInfo(){
        $spu = isset ($_GET['goods_sn']) ? post_check(trim($_GET['goods_sn'])) : '';
        $amount = isset ($_GET['amount']) ? $_GET['amount'] : '';
        $amount = intval($amount);
        $combineUserId = isset($_GET['truename'])?$_GET['truename']:'';
        $combineUserId = intval($combineUserId);
        $now = time();
        if (!preg_match("/^[A-Z0-9]+$/", $spu)) { //sku不合规范
        	self :: $errCode = '101';
			self :: $errMsg = 'SPU不合法';
			return false;
        }
        if ($amount <= 0) { //不是正数
        	self :: $errCode = '102';
			self :: $errMsg = '数量不合法';
			return false;
        }
        if ($combineUserId <= 0) { //不是正数
        	self :: $errCode = '103';
			self :: $errMsg = '组合人id不合法';
			return false;
        }
        $tName = 'pc_goods';
        $select = 'spu,sku';
        $where = "WHERE spu='$spu' and is_delete=0";
        $pcGoodsList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($pcGoodsList)){//如果spu找不到
            $select = 'spu,sku';
            $where = "WHERE sku='$spu' and is_delete=0";
            $pcGoodsList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($pcGoodsList)){//spu找不到去sku找
                self :: $errCode = '104';
    			self :: $errMsg = "SPU $spu 不存在";
    			return false;
            }
        }
        $tName = 'pc_sku_combine_relation';
        $select = '*';
        $where = "WHERE sku REGEXP '^$spu(_[A-Z0-9]+)*$' and count=$amount and combineSku REGEXP '^CB[0-9]{6}(_[A-Z0-9]+)*$'";//符合条件
        $pcCombineRelList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($pcCombineRelList)){//如果真实料号对应的虚拟料号存在，则直接返回
            $arr = array ();
        	$arrDetail = array ();
        	foreach ($pcCombineRelList as $value) {
        		$arrTmp = array ();
        		$arrTmp['CBSku'] = $value['combineSku'];
        		$arrTmp['bindSku'] = $value['sku'].'*'.$amount;
        		$arrDetail[] = $arrTmp;
        	}
        	$arr['spu'] = substr($pcCombineRelList[0]['combineSku'], 0, 8); //spu
        	$arr['detail'] = $arrDetail;
            self :: $errCode = '200';
			self :: $errMsg = "信息存在，查询返回成功";
			return json_encode($arr);
        }else{//如果不存在的话，则插入数据，然后再返回，此时要现在autoCreateSpu表中和combine表中，选出最大的CB为前缀的数字
            $numberCombine = OmAvailableModel::getMaxSpu('CB',2);//取得pc_goods_combine表中以CB为前缀的最大的数字
            $tName = 'pc_auto_create_spu';
            $select = 'spu';
            $where = "WHERE prefix='CB' order by sort desc limit 1";
            $autoCreSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $numberAutoCreSpu = intval(substr($autoCreSpuList[0]['spu'],2,6));
            $maxNumber = $numberCombine > $numberAutoCreSpu ? $numberCombine : $numberAutoCreSpu;
            $maxNumber = $maxNumber + 1;//要生成CB为前缀的最大数字
            try{
                BaseModel::begin();
                $arr = array (); //返回数组
                $arrDetail = array ();

                //要同步到深圳ERP组合料号的数据变量
                $insertIdCom = 1;//添加组合料号的insert_id
                $dataRelation = array ();//关联真实料号的数组
			    $dataRelationMem = array ();//关联真实料号的mem
                $ebayProductsCombineArr = array();
                if(count($pcGoodsList) == 1){//如果真实SPU料号只有一条记录的话，表示该SPU下没有分料号
                    $tmpSpu = 'CB' . str_pad($maxNumber, 6, '0', STR_PAD_LEFT);

                    $tName = 'pc_auto_create_spu';
                    $set = "SET spu='$tmpSpu',purchaseId='$combineUserId',createdTime='$now',sort='$maxNumber',status=2,prefix='CB',isSingSpu=2";
                    OmAvailableModel::addTNameRow($tName, $set);
                    //add by zqt ,20140403 添加关联销售记录
                    //addSalerInfoForAny($tmpSpu, 2, $combineUserId, $combineUserId);//改变逻辑
                    $tName = 'pc_goods_combine';
                    $set = "SET combineSpu='$tmpSpu',combineSku='$tmpSpu',combineUserId=$combineUserId,addTime='$now'";
                    $insertIdCom = OmAvailableModel::addTNameRow($tName, $set);
                    $tName = 'pc_sku_combine_relation';
                    $set = "SET combineSku='$tmpSpu',sku='{$pcGoodsList[0]['sku']}',count=$amount";
                    OmAvailableModel::addTNameRow($tName, $set);
                    $arr['spu'] = $tmpSpu;
            		$arrTmp = array ();
            		$arrTmp['CBSku'] = $tmpSpu;
            		$arrTmp['bindSku'] = $pcGoodsList[0]['sku'].'*'.$amount;
            		$arrDetail[] = $arrTmp;
            		$arr['detail'] = $arrDetail;


					$dataRelation[] = array (
						'combineSku' => $tmpSpu,
						'sku' => $pcGoodsList[0]['sku'],
						'count' => $amount
					);
					$dataRelationMem[] = array (
						'sku' => $pcGoodsList[0]['sku'],
						'count' => $amount
					);

                    //将新添加的sku添加到mem中
                    $dataCom = array ();

    				$dataCom['combineSpu'] = $tmpSpu;
    				$dataCom['combineSku'] = $tmpSpu;
    				$dataCom['combineNote'] = $amount.'个'.$pcGoodsList[0]['sku'].'#';
    				$dataCom['combineUserId'] = $combineUserId;
    				$dataCom['addTime'] = time();
    				$dataCom['detail'] = $dataRelationMem;
    				$value = $dataCom;

                    $key = 'pc_goods_combine_' . $tmpSpu;
    				setMemNewByKey($key, $value); //这里不保证能添加成功

                    //同步新数据到旧系统中
    				$ebayProductsCombine = array ();
    				$ebayProductsCombine['id'] = $insertIdCom;
    				$ebayProductsCombine['goods_sn'] = $tmpSpu;

					$str = $pcGoodsList[0]['sku'] . '*' . $amount;
					$strTrue = '[' . $pcGoodsList[0]['sku'] . ']';

    				$ebayProductsCombine['goods_sncombine'] = $str;
    				$ebayProductsCombine['cguser'] = getPersonNameById($combineUserId);
    				$ebayProductsCombine['ebay_user'] = 'vipchen';
    				$ebayProductsCombine['createdtime'] = time();
    				$ebayProductsCombine['truesku'] = $strTrue;

                    $ebayProductsCombineArr[] = $ebayProductsCombine;
                }else{//有分料号
                    $tmpSpu = 'CB' . str_pad($maxNumber, 6, '0', STR_PAD_LEFT);
                    $tName = 'pc_auto_create_spu';
                    $set = "SET spu='$tmpSpu',purchaseId='$combineUserId',createdTime='$now',sort='$maxNumber',status=2,prefix='CB',isSingSpu=2";
                    OmAvailableModel::addTNameRow($tName, $set);
                    //add by zqt ,20140403 添加关联销售记录
                    //addSalerInfoForAny($tmpSpu, 2, $combineUserId, $combineUserId);//改变逻辑
                    $i=0;
                    foreach($pcGoodsList as $value){
            			$suffTmp = explode($spu, $value['sku']);
            			$suff = isset($suffTmp[1])?$suffTmp[1]:'';//取得分料号去除SPU后的后缀
                        $i++;
                        if(strpos($suff,'_') !== 0){//如果找出的spu下的sku不带_,则默认按1,2,3，来区分
                            $suff = '_'.$i;
                        }
            			$tmpSku = 'CB' . str_pad($maxNumber, 6, '0', STR_PAD_LEFT) . $suff;
                        $tmpSku = trim($tmpSku);
            			$goods_sncom = $value['sku'] . '*' . $amount;
            			$now = time();
                        $tName = 'pc_goods_combine';
                        $set = "SET combineSpu='$tmpSpu',combineSku='$tmpSku',combineUserId='$combineUserId',addTime='$now'";
                        $insertIdCom = OmAvailableModel::addTNameRow($tName, $set);
                        $tName = 'pc_sku_combine_relation';
                        $set = "SET combineSku='$tmpSku',sku='{$value['sku']}',count=$amount";
            			OmAvailableModel::addTNameRow($tName, $set);
            			$arrTmp = array ();
            			$arrTmp['CBSku'] = $tmpSku;
            			$arrTmp['bindSku'] = $goods_sncom;
            			$arrDetail[] = $arrTmp;


                        $dataRelation[] = array (
						'combineSku' => $tmpSku,
						'sku' => $value['sku'],
						'count' => $amount
    					);
    					$dataRelationMem[] = array (
    						'sku' => $value['sku'],
    						'count' => $amount
    					);

                        //将新添加的sku添加到mem中
                        $dataCom = array ();

        				$dataCom['combineSpu'] = $tmpSpu;
        				$dataCom['combineSku'] = $tmpSku;
        				$dataCom['combineNote'] = $amount.'个'.$value['sku'].'#';
        				$dataCom['combineUserId'] = $combineUserId;
        				$dataCom['addTime'] = time();
        				$dataCom['detail'] = $dataRelationMem;
        				$valueMem = $dataCom;

                        $key = 'pc_goods_combine_' . $tmpSku;
        				setMemNewByKey($key, $valueMem); //这里不保证能添加成功

                        //同步新数据到旧系统中
        				$ebayProductsCombine = array ();
        				$ebayProductsCombine['id'] = $insertIdCom;
        				$ebayProductsCombine['goods_sn'] = $tmpSku;
    					$str = $value['sku'] . '*' . $amount;
    					$strTrue = '[' . $value['sku'] . ']';
        				$ebayProductsCombine['goods_sncombine'] = $str;
        				$ebayProductsCombine['cguser'] = getPersonNameById($combineUserId);
        				$ebayProductsCombine['ebay_user'] = 'vipchen';
        				$ebayProductsCombine['createdtime'] = time();
        				$ebayProductsCombine['truesku'] = $strTrue;
                        $ebayProductsCombineArr[] = $ebayProductsCombine;
                    }
                    $arr['spu'] = $tmpSpu;
	                $arr['detail'] = $arrDetail;
                }
                BaseModel::commit();
                BaseModel::autoCommit();
                if(!empty($tmpSpu)){//添加销售人员信息
                    addSalerInfoForAny($tmpSpu, 2, $combineUserId, $combineUserId);
                }
                $dataAuto = array();
                $dataAuto['sku'] = $tmpSpu;
                $dataAuto['cguser'] = getPersonNameById($combineUserId);
                $dataAuto['mainsku'] = $maxNumber;
                $dataAuto['status'] = 2;
                $dataAuto['addtime'] = time();
                $dataAuto['type'] = 7;
                OmAvailableModel::newData2ErpInterfOpen('pc.erp.addAutoCreatSpu',$dataAuto,'gw88');//插入自動生成spu

                foreach($ebayProductsCombineArr as $value){
                    $ret = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsCombine', $value, 'gw88');//插入生成的CB料號
                }
                self :: $errCode = '200';
    			self :: $errMsg = "不存在真实料号信息，插入记录成功";
    			return json_encode($arr);
            }catch(Exception $e){
                BaseModel::rollback();
                BaseModel::autoCommit();
                self :: $errCode = '404';
    			self :: $errMsg = $e->getMessage();
    			return false;
            }
        }
    }

    //添加sku_whId_location到指定表,后面要做成自动脚本
     function act_addGoodsWhIdLocationRaletion(){
        $start = 0;//循环的下标
        $per = 200;//每次通过接口取得的记录数
        $i = 1;//标识第几次通过接口取数据，初始值为第一次
        do{
            echo "这是第 $i 次 调用接口取得数据 \n";
            echo "下标为 $start 取数为 $per \n";
            $skuInfoList = UserCacheModel::getOpenSysApi('',array('start'=>$start,'per'=>$per));//调用idc上的仓库系统接口，返回指定下标及对应记录数
            $totalNum = $skuInfoList['totalNum'];//返回数据的记录数
            $skuInfo = $skuInfoList['skuInfo'];//具体的sku信息数组

            if(intval($totalNum) <= 0){
                echo "$totalNum <= 0 或者不是数字 \n";
                continue;
            }
            echo "本次要处理的记录数为 $totalNum \n";
            if(empty($skuInfo) || !is_array($skuInfo)){
                echo "$skuInfo 为空或者不是数组 \n";
                continue;
            }
            foreach($skuInfo as $value){
                $sku = $value['sku'];
                $whId = $value['whId'];
                $location = post_check(trim($value['location']));
                $storageTime = intval($value['storageTime']);
                if(!isSkuExist($sku)){//检测sku是否在产品中心存在，不存在则跳过
                    echo "$sku 在产品中心不存在，跳过 \n";
                    continue;
                }
                if(intval($whId) <= 0){
                    echo "$sku 所在的仓库id $whId 不是数字或小于等于0，跳过 \n";
                    continue;//如果该sku所在仓库id不合法，则跳过
                }
                try{
                    BaseModel::begin();
                    $tName = 'pc_goods_whId_location_raletion';
                    $where = "WHERE sku='$sku'";
                    OmAvailableModel::deleteTNameRow($tName, $where);//先删除掉该skuInfo的记录

                    $set = "SET sku='$sku',whId='$whId',location='$location',storageTime='$storageTime'";
                    OmAvailableModel::addTNameRow($tName, $set);

                    BaseModel::commit();
                    BaseModel::autoCommit();
                    echo "删除表中的 $sku 记录 成功\n";
                    echo "添加 $sku 记录 成功，SET sku='$sku',whId='$whId',location='$location',storageTime='$storageTime' \n";
                }catch(Exception $e){//发生错误则进行下次循环
                    BaseModel::rollback();
                    BaseModel::autoCommit();
                    echo "$sku 记录 删除 或 插入 SET sku='$sku',whId='$whId',location='$location',storageTime='$storageTime' 失败，数据回滚，进入下次循环\n";
                    continue;
                }
            $start += $per;//下次循环的下标
            $i++;

            }
        }while($totalNum >= $per);
     }

    //根据采购订单的成本更新对应产品的最新成本及插入历史成本记录
    function act_updateCostAndAddHistory(){
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';//sku
        $purchaseCost = $_GET['purchaseCost']?post_check(trim($_GET['purchaseCost'])):0;//成本
        $addUserId = $_GET['addUserId']?post_check(trim($_GET['addUserId'])):0;//添加人
        $addTime = time();
        if(empty($sku)){
            self :: $errCode = '101';
			self :: $errMsg = 'sku为空';
			return false;
        }
        if(!is_numeric($purchaseCost) || $purchaseCost <= 0){
            self :: $errCode = '102';
			self :: $errMsg = '成本必须大于0';
			return false;
        }
        if(intval($addUserId) <= 0){
            self :: $errCode = '103';
			self :: $errMsg = '添加人id不合法';
			return false;
        }
        $tName = 'pc_goods';
        $select = '*';
        $where = "WHERE sku='$sku' and is_delete=0";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($skuList)){
            self :: $errCode = '104';
			self :: $errMsg = "找不到 $sku 料号";
			return false;
        }
        try{
            BaseModel::begin();
            //先更新goods表中对应sku的goodsCost
            $set = "SET goodsCost='$purchaseCost'";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            //然后再历史记录表中添加一条记录
            $tName = 'pc_goods_cost_history_record';
            $set = "SET sku='$sku',purchaseCost='$purchaseCost',addUserId='$addUserId',addTime='$addTime'";
            OmAvailableModel::addTNameRow($tName, $set);

            //更新mem中的sku
			$key = 'pc_goods_' . $sku;
            $value = $skuList[0];
			$value['goodsCost'] = $purchaseCost;
			setMemNewByKey($key, $value); //这里不保证能添加成功

            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
			self :: $errMsg = "更新成功";
			return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
			self :: $errMsg = $e->getMessage();
			return false;
        }
    }

    //仓库系统流水线进行重量拦截时，重新录入重量接口
	public function act_skuWeighing(){
		$sku       = $_GET['sku']?post_check(trim($_GET['sku'])):'';//料号条码
		$skuweight = isset($_GET['skuweight'])?($_GET['skuweight']/1000):"";

		if(empty($sku) || empty($skuweight)){
			self::$errCode = 333;
			self::$errMsg = "料号或重量不能为空！";
			return false;
		}
        if(!is_numeric($skuweight) || $skuweight < 0.001){
			self::$errCode = 334;
			self::$errMsg = "重量不能小于1g！";
			return false;
		}
        $skuList = getSkuBygoodsCode($sku);//根据条码获取真实sku
        if(empty($skuList)){
            self::$errCode = 404;
			self::$errMsg = '料号不存在';
			return false;
        }
		$tName = 'pc_goods';
        $set = "SET goodsWeight='{$skuweight}'";
        $where = "WHERE sku='{$skuList[0]['sku']}'";
        $affectRow = OmAvailableModel::updateTNameRow($tName, $set, $where);
		//$info = UserCacheModel::getOpenSysApi('pc.updateTNameRow',array(array('tName'=>"pc_goods",'set'=>"goodsWeight='{$skuweight}'",'where'=>"WHERE sku='{$sku}' and is_delete = 0")));
        if($affectRow !== false){
			self::$errCode = 200;
			self::$errMsg = $skuList[0]['sku'] ." 重量 ".$skuweight."(Kg) 录入成功！";

            //$url = "add2ebay_goods_weight.php?goods_sn=".$skuList[0]['sku']."&goods_weight=".$skuweight;
//            OmAvailableModel::newData2ErpInterf($url);
            $paraArr['goods_sn'] = $skuList[0]['sku'];
            $paraArr['goods_weight'] = $skuweight;
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.addGoodsSnWeight',$paraArr,'gw88');
            //print_r($res);
//            exit;
			return true;
        }else{
			self::$errCode = 404;
			self::$errMsg = $skuList[0]['sku'] ." 重量 ".$skuweight."(Kg) 录入失败！";
			return false;
		}
	}

    /**
	 *功能：通过旧料号获取新料号资料
	 *@para $oldSku 旧料号
	 *@return  $new_sku 新料号
	 * */
	public function act_showNewSku(){
	    $old_sku = $_GET['old_sku']?post_check(trim($_GET['old_sku'])):'';
		if(empty($old_sku)){
			self::$errCode = "002";
			self::$errMsg = "Miss param";
			return;
		}
        $tName = 'pc_sku_conversion';
		$select = "new_sku";
		$where = "WHERE old_sku='$old_sku' AND is_delete=0 AND auditStatus=2 ORDER BY id DESC LIMIT 1";
		$ret = OmAvailableModel::getTNameList($tName, $select, $where);
		if($ret){
			$new_sku = $ret[0]['new_sku'];
			self::$errCode = "001";
			self::$errMsg = "success";
			return $new_sku;
		}else{
			self::$errCode = "003";
			self::$errMsg = "no this sku or delete";
			return;
		}
	}

	/**
	 *功能：获取所有的料号转换记录
	 *@return  array()
	 * */
	public function act_getSkuConversionList(){
        $tName = 'pc_sku_conversion';
		$select = "old_sku,new_sku";
		$where = "WHERE is_delete=0 AND auditStatus=2 ORDER BY id";
		$skuConversionList = OmAvailableModel::getTNameList($tName, $select, $where);
		$returnArr = array();
		foreach($skuConversionList as $value){
			$returnArr[$value['old_sku']] = $value['new_sku'];
		}
		self::$errCode = "200";
		self::$errMsg = "返回成功";
		return $returnArr;
	}

    /**
	 *功能：通过虚拟料号获取其中的料号总重量，料号总价格，包材总重量，包材总价格
	 *@para $combineSku 虚拟料号
	 *@return  array('skuTotalCost','skuTotalWeight','pmTotalWeight','pmTotalCost')
	 * */
	public function act_getCombineSkuPCW(){
	    $combineSkuArr = json_decode($_GET['combineSkuArr'],true);
        if(!is_array($combineSkuArr)){
            self::$errCode = "101";
			self::$errMsg = "参数必须是数组";
			return;
        }
        if(empty($combineSkuArr)){
            self::$errCode = "102";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnArr = array();
        foreach($combineSkuArr as $combineSku){
            if(empty($combineSku)){
                continue;
            }
            $array = array();
            $skuTotalInfoArr = getTrueCWForCombineSku($combineSku);
            $pmTotalInfoArr = getTruePMCWForCombineSku($combineSku);
            $array['skuTotalCost'] = $skuTotalInfoArr['totalCost'];
            $array['skuTotalWeight'] = $skuTotalInfoArr['totalWeight'];
            $array['pmTotalCost'] = $pmTotalInfoArr['pmTotalCost'];
            $array['pmTotalWeight'] = $pmTotalInfoArr['pmTotalWeight'];
            $returnArr[$combineSku] = $array;
        }
        self::$errCode = "200";
		self::$errMsg = "success";
		return $returnArr;
	}

    /**
	 *功能：通过Sku信息获取其对应的海关编码及材质信息
	 *@para $skuArr sku（一维）的json数组，形为json_encode(array('sku1','sku2',....)),注意，单个sku也必须封装成数组形式，非数组形式的参数均会返回报错信息
	 *@return
	 * */
	public function act_getHsCodeAndMaterialBySkuArr(){
	    $skuArr = json_decode($_GET['skuArr'],true);
        if(!is_array($skuArr)){
            self::$errCode = "101";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($skuArr)){
            self::$errCode = "102";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnArr = array();//要返回的数组
        $hsCodeArr = array();//海关编码数组
        $materialArr = array();//材质数组
        foreach($skuArr as $value){
            $sku = post_check(trim($value));//参数过滤
            if(empty($sku)){//如果为空则跳过
                continue;
            }
            //下面是获取sku对应的海关编码数组
            $tName = 'pc_sku_hscode';
            $select = 'hsCode';
            $where = "WHERE isUsable=1 AND sku='$sku' order by id desc";
            $skuHsCodeList = OmAvailableModel::getTNameList($tName, $select, $where);
            $tmpHsCodeArr = array();
            foreach($skuHsCodeList as $skuHsCode){
                $tmpHsCodeArr[] = $skuHsCode['hsCode'];
            }
            $hsCodeArr[$value] = $tmpHsCodeArr;
            //下面是获取sku对应的材质数组
            $tName = 'pc_goods';
            $select = 'spu';
            $where = "WHERE is_delete=0 AND sku='$sku'";
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $spu = $spuList[0]['spu'];
            if(!empty($spu)){
                $ppvIdArr = isExistForSpuPPV($spu, '材质');
                //echo '$ppvIdArr ==== ';
//                print_r($ppvIdArr);
//                exit;
                if(!empty($ppvIdArr)){
                    $tmpValAliArr = array();
                    foreach($ppvIdArr as $valuePpv){
                        $ppvId = intval($valuePpv['propertyValueId']);
                        if($ppvId > 0){
                            $tName = 'pc_archive_property_value';
                            $select = 'propertyValue,propertyValueAlias';//属性值名称，和属性值别名（英文名）
                            $where = "WHERE id=$ppvId";
                            $ppvValAlaArr = OmAvailableModel::getTNameList($tName, $select, $where);
                            if(!empty($ppvValAlaArr)){
                                $tmpValAliArr[] = $ppvValAlaArr;
                            }
                        }
                    }
                    //print_r($tmpValAliArr);
//                    exit;
                    $materialArr[$value] = $tmpValAliArr;
                }
            }

        }
        //
        $returnArr['hsCode'] = $hsCodeArr;
        $returnArr['material'] = $materialArr;
        self::$errCode = "200";
		self::$errMsg = "success";
		return $returnArr;
	}


    public function act_getTruePWCinfoWithSpu(){
        $combineSpu = $_GET['combineSpu']?post_check(trim($_GET['combineSpu'])):'';
		if(empty($combineSpu)){
			self::$errCode = "101";
			self::$errMsg = "combineSpu 为空";
			return;
		}
        $tName = 'pc_goods_combine';
        $select = 'combineSku';
        $where = "WHERE is_delete=0 and combineSpu='$combineSpu'";
        $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($combineSkuList)){
            self::$errCode = "102";
			self::$errMsg = "无 $combineSpu 的记录";
			return;
        }
        $CombineSpuArr = array();//要返回的数组
        $CombineSkuArr = array();
        foreach($combineSkuList as $value){
            $combineSku = $value['combineSku'];
            $goodsInfoArr = getTrueCWForCombineSku($combineSku);
            $pmInfoArr = getTruePMCWForCombineSku($combineSku);
            $goods_cost = $goodsInfoArr['totalCost'];//真实料号的价格
            $goods_weight = $goodsInfoArr['totalWeight'];//真实料号的重量
            $packingPrice = $pmInfoArr['pmTotalCost'];//包材价格
            $packingWeight = $pmInfoArr['pmTotalWeight'];//包材重量
            $tmpInfoArr = array();
            $tmpInfoArr['goods_cost'] = $goods_cost;
            $tmpInfoArr['goods_weight'] = $goods_weight;
            $tmpInfoArr['packingPrice'] = $packingPrice;
            $tmpInfoArr['packingWeight'] = $packingWeight;
            $tmpInfoArr['capacity'] = 1;
            $CombineSkuArr[$combineSku] = $tmpInfoArr;
        }
        $CombineSpuArr[$combineSpu] = $CombineSkuArr;
        self::$errCode = "200";
		self::$errMsg = "success";
		return $CombineSpuArr;
    }

    //根据时间段返回sku非在线的原因
    public function act_getNoOnlineReasonByTime(){
        $startTime = $_GET['startTime']?post_check(trim($_GET['startTime'])):'';//开始时间戳
        $endTime = $_GET['endTime']?post_check(trim($_GET['endTime'])):'';//结束时间戳
        $startTime = intval($startTime);
        $endTime = intval($endTime);
		if($startTime <= 0 && $endTime <= 0){
			self::$errCode = "101";
			self::$errMsg = "startTime，endTime不能同时为空";
			return;
		}
        $tName = 'pc_goods_update_status_reason';
        $select = '*';
        $where = "WHERE goodsStatus>1 ";
        if($startTime > 0){
            $where .= "AND addTime>=$startTime ";
        }
        if($endTime > 0){
            $where .= "AND addTime<=$endTime ";
        }
        $where .= "group by sku";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        self::$errCode = "200";
		self::$errMsg = "success";
		return $skuList;
    }

    //超卖那边需要产品提供该接口
    //根据真实SKU返回对应的SPU及含有这个真实sku的所有CombineSpu
    public function act_getSpuOrCombineSpuInfoBySku(){
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';//sku
        $returnArr = array();
		if(empty($sku)){
			self::$errCode = "101";
			self::$errMsg = "SKU为空";
			return;
		}
        $tName = 'pc_goods';
        $select = 'spu';
        $where = "WHERE is_delete=0 and sku='$sku'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuList)){
            self::$errCode = "102";
			self::$errMsg = "SPU不存在";
			return;
        }
        $spu = $spuList[0]['spu'];//求得该SKU对应的SPU
        $returnArr['spu'] = $spu;

        $tName = 'pc_sku_combine_relation';
        $select = 'combineSku';
        $where = "WHERE sku='$sku'";
        $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($combineSkuList)){//如果存在该真实料号的combineSku时
            $combineSkuArr = array();
            foreach($combineSkuList as $value){
                if(!empty($value['combineSku'])){
                    $combineSkuArr[] = "'".$value['combineSku']."'";
                }
            }
            if(!empty($combineSkuArr)){
                $combineSkuStr = implode(',', $combineSkuArr);//将$combineSkuArr拼接成string形式
                $tName = 'pc_goods_combine';
                $select = 'combineSpu,combineSku';
                $where = "WHERE combineSku in($combineSkuStr)";
                $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $combineSpuArr = array();
                foreach($combineSpuList as $value){
                    $tmpCombineSkuArr = array();
                    if(!empty($value['combineSpu']) && !empty($value['combineSku'])){
                        if(!isset($combineSpuArr[$value['combineSpu']])){
                            $combineSpuArr[$value['combineSpu']] = array();
                        }
                        $tmpArr['combineSku'] = $value['combineSku'];
                        $tName = 'pc_sku_combine_relation';
                        $select = 'sku,count';
                        $where = "WHERE combineSku='{$value['combineSku']}'";
                        $combineRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                        $tmpRelationArr = array();
                        foreach($combineRelationList as $v){
                            $tmpArr = array();
                            $tmpArr['sku'] = $v['sku'];
                            $tmpArr['count'] = $v['count'];
                            $tmpRelationArr[] = $tmpArr;
                        }
                        $tmpCombineSkuArr['combineSku'] = $value['combineSku'];
                        $tmpCombineSkuArr['combineRelation'] = $tmpRelationArr;
                        array_push($combineSpuArr[$value['combineSpu']], $tmpCombineSkuArr);

                    }
                }
                $returnArr['combineSpu'] = $combineSpuArr;
            }
        }
        self::$errCode = "200";
		self::$errMsg = "success";
		return $returnArr;
    }


    //速卖通3期那边需要产品提供该接口
    //根据spu或combineSpu返回该Spu的性质数组array('specialStatus','maxWeight','categoryPath');maxWeight包括包材重量
    public function act_getSMCBySpuOrCombineSpu(){
        $spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';//sku
        $returnArr = array();
		if(empty($spu)){
			self::$errCode = "101";
			self::$errMsg = "SPU为空";
			return;
		}
        $tName = 'pc_goods';
        $select = 'specialStatus,goodsWeight,goodsCategory,pmId';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($spuList)){//如果Spu在goods表中存在，则表示传递过来的是单料号
            $specialStatusArr = array();//状态数组
            $maxWeight = 0;//重量最大值
            $categoryPath = '';//类别
            $pmId = 0;
            $pmWeight = 0;
            foreach($spuList as $value){
                $specialStatus = $value['specialStatus'];
                if(!empty($specialStatus)){
                    $tmpSpecialStatusArr = explode(',', $specialStatus);//将状态值单个解析出来
                    foreach($tmpSpecialStatusArr as $tmpSpecialStatus){
                        if(!empty($tmpSpecialStatus)){
                            $specialStatusArr[] = $tmpSpecialStatus;
                        }
                    }
                }
                if(intval($value['pmId'])>0){
                    $pmId = $value['pmId'];
                }
                if($value['goodsWeight'] >= $maxWeight){
                    $maxWeight = $value['goodsWeight'];
                    $categoryPath = $value['goodsCategory'];
                }
            }
            if(!empty($pmId)){
                $tName = 'pc_packing_material';
                $select = 'pmWeight';
                $where = "WHERE id='$pmId'";
                $pmList = OmAvailableModel::getTNameList($tName, $select, $where);
                $pmWeight = $pmList[0]['pmWeight'];
            }
            $specialStatusArr = array_unique($specialStatusArr);//去除重复值
            sort($specialStatusArr);
            $returnArr['specialStatus'] = $specialStatusArr;
            $returnArr['maxWeight'] = $maxWeight + $pmWeight;
            $returnArr['goodsCategory'] = $categoryPath;
            self::$errCode = "200";
    		self::$errMsg = "success";
    		return $returnArr;
        }
        $tName = 'pc_goods_combine';
        $select = 'combineSku';
        $where = "WHERE combineSpu='$spu'";
        $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($combineSpuList)){//如果虚拟料号列表不为空则表示该spu是combineSpu,//对于组合料号来说，按整体来计算重量，及包括SKU对应数量
            $specialStatusArr = array();//状态数组
            $maxWeight = 0;//重量最大值
            $categoryPath = '';//类别
            $combineSkuArr = array();
            foreach($combineSpuList as $value){
                $combineSku = $value['combineSku'];
                if(!empty($combineSku)){
                    $tName = 'pc_sku_combine_relation';
                    $select = 'sku';
                    $where = "WHERE combineSku='$combineSku'";
                    $combineSkuRelationArr = OmAvailableModel::getTNameList($tName, $select, $where);
                    foreach($combineSkuRelationArr as $skuRelationArr){
                        $sku = $skuRelationArr['sku'];
                        $tName = 'pc_goods';
                        $select = 'specialStatus,goodsCategory';
                        $where = "WHERE sku='$sku'";
                        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                        if(!empty($skuList[0]['goodsCategory'])){
                            $categoryPath = $skuList[0]['goodsCategory'];
                        }
                        $specialStatus = $skuList[0]['specialStatus'];
                        if(!empty($specialStatus)){
                            $tmpSpecialStatusArr = implode(',', $specialStatus);
                            foreach($tmpSpecialStatusArr as $tmpSpecialStatus){
                                if(!empty($tmpSpecialStatus)){
                                    $specialStatusArr[] = $tmpSpecialStatus;
                                }
                            }
                        }
                    }
                    //$combineSkuArr[] = $combineSku;//combineSku数组
                    $combineSkuCWArr = getTrueCWForCombineSku($combineSku);//获取虚拟SKU对应的总重量和成本
                    $combineSkuPMCWArr = getTruePMCWForCombineSku($combineSku);//获取虚拟SKU对应的包材成本和包材重量
                   // print_r($combineSkuPMCWArr);
//                    exit;
                    $combineSkuWeight = $combineSkuCWArr['totalWeight'];
                    $combineSkuPmWeight = $combineSkuPMCWArr['pmTotalWeight'];
                    if($combineSkuWeight + $combineSkuPmWeight > $maxWeight){
                        $maxWeight = $combineSkuWeight + $combineSkuPmWeight;
                    }
                }
            }
            if(!empty($specialStatusArr)){
                $specialStatusArr = array_unique($specialStatusArr);
                sort($specialStatusArr);
            }
            $returnArr['specialStatus'] = $specialStatusArr;
            $returnArr['maxWeight'] = $maxWeight;
            $returnArr['goodsCategory'] = $categoryPath;
            self::$errCode = "200";
    		self::$errMsg = "success";
    		return $returnArr;
        }else{
            self::$errCode = "400";
    		self::$errMsg = "无该SPU信息";
    		return $returnArr;
        }

    }

    /**
	 *功能：提供给采购系统的接口，将指定sku的采购改成制定人
	 *@para $skuArr sku（一维）的json数组，形为json_encode(array('sku1','sku2',....)),注意，单个sku也必须封装成数组形式，非数组形式的参数均会返回报错信息
	 *@return
	 * */
	public function act_updatePurchaseIdBySkuArrGet(){
	    $skuArr = json_decode($_REQUEST['skuArr'],true);
        $purchaseId = isset($_REQUEST['purchaseId'])?post_check($_REQUEST['purchaseId']):0;//要转给的人id
        $addUserId = isset($_REQUEST['addUserId'])?post_check($_REQUEST['addUserId']):0;//操作人Id
        $purchaseId = intval($purchaseId);
        $addUserId = intval($addUserId);
        $skuArr = array_filter($skuArr);
        if($purchaseId <= 0){
            self::$errCode = "101";
			self::$errMsg = "purchaseId 不是正整数";
			return;
        }
        if($addUserId <= 0){
            self::$errCode = "102";
			self::$errMsg = "addUserId 不是正整数";
			return;
        }
        if(!is_array($skuArr)){
            self::$errCode = "103";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($skuArr)){
            self::$errCode = "105";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnDataArr = array();//记录返回数据
        $updateDataArr = array();//已经修改了的sku数组
        $unUpdateDataArr = array();//未修改的数组
        foreach($skuArr as $sku){
            if(!empty($sku)){
                $tName = 'pc_goods';
                $select = 'purchaseId';
                $where = "WHERE sku='$sku'";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $oldPurchaseId = $skuList[0]['purchaseId'];//找出旧的采购
                $_SESSION['userId'] = $addUserId;//操作人，即登陆人
                //if(getIsAccess($oldPurchaseId) && $oldPurchaseId != $purchaseId){//如果传过来的操作人有操作的权限，并且老采购<>新采购，则修改
                if($oldPurchaseId != $purchaseId){
                    $tName = 'pc_goods';
                    $set = "SET purchaseId='$purchaseId'";
                    $where = "WHERE sku='$sku'";
                    OmAvailableModel::updateTNameRow($tName, $set, $where);
                    updatePurchaseIdModify($sku, $purchaseId, $addUserId);
                    $updateDataArr[] = $sku;//将改动了的sku加入数组
                }else{
                    $unUpdateDataArr[] = $sku;
                }
            //同步队列到旧系统该采购人
            OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateCguser',array('goods_sn'=>$sku,'cguser'=>getPersonNameById($purchaseId)),'gw88');
            }
        }
        $returnDataArr['update'] = $updateDataArr;
        $returnDataArr['unUpdate'] = $unUpdateDataArr;
        self::$errCode = "200";
		self::$errMsg = 'success';
		return $returnDataArr;
	}

    /**
	 *功能：提供给采购系统的接口，批量修改价格
	 *@para $skuArr sku（一维）的json数组，形为json_encode(array('sku1','sku2',....)),注意，单个sku也必须封装成数组形式，非数组形式的参数均会返回报错信息
	 *@return
	 * */
	public function act_updateCostBySkuArrGet(){
	    $skuArr = json_decode($_REQUEST['skuArr'],true);
        $goodsCost = isset($_REQUEST['goodsCost'])?post_check($_REQUEST['goodsCost']):0;//要转给的人id
        $addUserId = isset($_REQUEST['addUserId'])?post_check($_REQUEST['addUserId']):0;//操作人Id
        $addUserId = intval($addUserId);
        $skuArr = array_filter($skuArr);
        if(!is_numeric($goodsCost) || $goodsCost <= 0){
            self::$errCode = "101";
			self::$errMsg = "成本必须是大于0的正数";
			return;
        }
        if($addUserId <= 0){
            self::$errCode = "102";
			self::$errMsg = "addUserId 不是正整数";
			return;
        }
        if(!is_array($skuArr)){
            self::$errCode = "103";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($skuArr)){
            self::$errCode = "105";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnDataArr = array();//记录返回数据
        $updateDataArr = array();//已经修改了的sku数组
        $unUpdateDataArr = array();//未修改的数组
        foreach($skuArr as $sku){
            if(!empty($sku)){
                $tName = 'pc_goods';
                $select = 'purchaseId,goodsCost';
                $where = "WHERE sku='$sku'";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $oldPurchaseId = $skuList[0]['purchaseId'];//采购员
                $oldGoodsCost = $skuList[0]['goodsCost'];//找出旧的价格
                $_SESSION['userId'] = $addUserId;
                if($oldGoodsCost != $goodsCost){//如果传过来的操作人有操作的权限，并且数据有更新，则修改
                    $tName = 'pc_goods';//先更新goods表
                    $set = "SET goodsCost='$goodsCost'";
                    $where = "WHERE sku='$sku'";
                    OmAvailableModel::updateTNameRow($tName, $set, $where);
                    addCostBackupsModify($sku, $goodsCost, $addUserId);//调用公用方法，修改价格
                    $updateDataArr[] = $sku;//将改动了的sku加入数组
                }else{
                    $unUpdateDataArr[] = $sku;
                }
                $goodsArr = array();
                $goodsArr['goods_sn'] = $sku;
                $goodsArr['goods_cost'] = $goodsCost;
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateCostBatch',$goodsArr,'gw88');//同步到旧ERP系统中
            }
        }
        $returnDataArr['update'] = $updateDataArr;
        $returnDataArr['unUpdate'] = $unUpdateDataArr;
        self::$errCode = "200";
		self::$errMsg = 'success';
		return $returnDataArr;
	}

    /**
	 *功能：提供给采购系统的接口，批量修改成本核算价格
	 *@para $skuArr sku（一维）的json数组，形为json_encode(array('sku1','sku2',....)),注意，单个sku也必须封装成数组形式，非数组形式的参数均会返回报错信息
	 *@return
	 * */
	public function act_updateCheckCostBySkuArr(){
	    $skuArr = json_decode($_REQUEST['skuArr'],true);
        $checkCost = isset($_REQUEST['checkCost'])?post_check($_REQUEST['checkCost']):0;//要转给的人id
        //$addUserId = isset($_REQUEST['addUserId'])?post_check($_REQUEST['addUserId']):0;//操作人Id
        //$addUserId = intval($addUserId);
        $skuArr = array_filter($skuArr);
        if(!is_numeric($checkCost) || $checkCost <= 0){
            self::$errCode = "101";
			self::$errMsg = "成本核算必须是大于0的正数";
			return;
        }
        //if($addUserId <= 0){
//            self::$errCode = "102";
//			self::$errMsg = "addUserId 不是正整数";
//			return;
//        }
        if(!is_array($skuArr)){
            self::$errCode = "103";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($skuArr)){
            self::$errCode = "105";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnDataArr = array();//记录返回数据
        $updateDataArr = array();//已经修改了的sku数组
        $unUpdateDataArr = array();//未修改的数组
        foreach($skuArr as $sku){
            if(!empty($sku)){
                $tName = 'pc_goods';
                $select = 'checkCost';
                $where = "WHERE sku='$sku'";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $oldCheckCost = $skuList[0]['checkCost'];//找出旧的价格
                //$_SESSION['userId'] = $addUserId;
                if($oldCheckCost != $checkCost){//如果传过来的操作人有操作的权限，并且数据有更新，则修改
                    updateCheckCostModify($sku, $checkCost);//调用公用方法，修改
                    $updateDataArr[] = $sku;//将改动了的sku加入数组
                }else{
                    $unUpdateDataArr[] = $sku;
                }
                $goodsArr = array();
                $goodsArr['goods_sn'] = $sku;
                $goodsArr['checkCost'] = $checkCost;
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateCheckCost',$goodsArr,'gw88');
            }
        }
        $returnDataArr['update'] = $updateDataArr;
        $returnDataArr['unUpdate'] = $unUpdateDataArr;
        self::$errCode = "200";
		self::$errMsg = 'success';
		return $returnDataArr;
	}

    /**
	 *功能：提供给财务系统的接口，获取单料号总数
	 * */
	public function act_getSkuCount(){
	   $tName = 'pc_goods';
       $where = "WHERE is_delete=0";
       $count = OmAvailableModel::getTNameCount($tName, $where);
       self::$errCode = "200";
	   self::$errMsg = 'success';
	   return $count;
    }

    /**
	 *功能：提供给财务系统的接口，根据页数及每页显示条数获得记录
	 * */
	public function act_getSkuInfoByPageAndPer(){
	   $page = isset($_GET['page'])?post_check($_GET['page']):0;//页数
       $per = isset($_GET['per'])?post_check($_GET['per']):0;//每页显示数
       if(intval($page) < 1){
           self::$errCode = "101";
           self::$errMsg = "页码不能小于1";
       	   return;
       }
       if(intval($per) < 10){
           self::$errCode = "102";
           self::$errMsg = "每页条数必须大于10";
       	   return;
       }
       $start = ($page - 1)*$per;
	   $tName = 'pc_goods';
       $select = '*';
       $where = "WHERE is_delete=0 limit $start,$per";
       $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
       $count = count($skuList);
       for($i=0;$i<$count;$i++){
          $personName = getPersonNameById($skuList[$i]['purchaseId']);
          $skuList[$i]['purchaseName'] = $personName;
       }
       self::$errCode = "200";
	   self::$errMsg = 'success';
	   return $skuList;
    }

    /**
	 *功能：提供给仓库系统的接口，重量拦截重新得到重量
	 * */
	public function act_setSkuWeightInWh(){
	   $sku = isset ($_GET['sku']) ? $_GET['sku'] : "";
       $skuweight = isset ($_GET['skuweight']) ? ($_GET['skuweight'] / 1000) : "";//传递过来的重量为g
       $userId = isset ($_GET['userId']) ? $_GET['userId'] : 0;

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
                BaseModel::begin();
    			$tName = 'pc_goods';
    			$set = "SET goodsWeight='{$skuweight}'";
    			$where = "WHERE sku='{$skuList[0]['sku']}'";
    			OmAvailableModel :: updateTNameRow($tName, $set, $where);
    			//$info = UserCacheModel::getOpenSysApi('pc.updateTNameRow',array(array('tName'=>"pc_goods",'set'=>"goodsWeight='{$skuweight}'",'where'=>"WHERE sku='{$sku}' and is_delete = 0")));

    			//添加重量备份记录
    			//$tName = 'pc_goods_weight_backups';
//    			$backupsArr = array ();
//    			$backupsArr['sku'] = $skuList[0]['sku'];
//    			$backupsArr['goodsWeight'] = $skuweight;
//    			$backupsArr['addUserId'] = $userId;
//    			$backupsArr['addTime'] = time();
//    			OmAvailableModel :: addTNameRow2arr($tName, $backupsArr);
                addWeightBackupsModify($skuList[0]['sku'], $skuweight, $userId);
    			//
    			//$url = "add2ebay_goods_weight.php?goods_sn=".$skuList[0]['sku']."&goods_weight=".$skuweight;
    			//            OmAvailableModel::newData2ErpInterf($url);
    			$paraArr['goods_sn'] = $skuList[0]['sku'];
    			$paraArr['goods_weight'] = $skuweight;
    			$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsSnWeight', $paraArr, 'gw88');
    			//print_r($res);
    			//            exit;
                BaseModel::commit();
                BaseModel::autoCommit();
                $string = empty($oldWeight)?"(Kg) 录入成功！":"(Kg) 更新成功，原来重量为 $oldWeight(Kg)";
    			self :: $errCode = 200;
    			self :: $errMsg = $skuList[0]['sku'] . " 重量 " . $skuweight . $string;
    			return true;
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

    /**
	 *功能：提供给仓库系统的接口，审核领料单
	 * */
	public function act_auditIoStoreInWh(){
	    $ordersn = isset ($_GET['ordersn']) ? $_GET['ordersn'] : "";
        $isAudit = isset ($_GET['isAudit']) ? $_GET['isAudit'] : 0;
        $auditorId = isset ($_GET['auditorId']) ? $_GET['auditorId'] : 0;
        $now = time();
        if(intval($auditorId) <= 0){
		    self :: $errCode = 101;
			self :: $errMsg = "审核人有误";
			return false;
        }
		if (empty($ordersn)){
			self :: $errCode = 102;
			self :: $errMsg = "单号不能为空！";
			return false;
		}
        if ($isAudit !=2 && $isAudit !=3){//isAudit=2为审核通过，3为审核不通过
			self :: $errCode = 102;
			self :: $errMsg = "审核状态值有误，只能为通过或不通过";
			return false;
		}
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 and isAudit=1 and ordersn='$ordersn'";
        $ioStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($ioStoreList)){
            self :: $errCode = 103;
			self :: $errMsg = "该单据号不存在或者已经审核过";
			return false;
        }
        try{
            BaseModel::begin();
            //$tName = 'pc_products_iostore';
            $dataIostore = array();
            $dataIostore['isAudit'] = $isAudit;
            $dataIostore['auditorId'] = $userId;
            $dataIostore['auditTime'] = $now;
            OmAvailableModel::updateTNameRow2arr($tName, $dataIostore, $where);//将表头改为审核状态
            $tName = 'pc_products_iostore_detail';
            $dataIostoreDetail = array();
            $dataIostoreDetail['isAudit'] = $isAudit;
            $where = "WHERE is_delete=0 AND iostoreId='{$ioStoreList[0]['id']}'";
            OmAvailableModel::updateTNameRow2arr($tName, $dataIostoreDetail, $where);//将表体的料号（is_delete=0）改为审核状态
            //if($ioStoreList[0]['iostoreTypeId'] == 1 && $ioStoreList[0]['useTypeId'] == 1){//如果该单是制作领料单（新品下单的），则要将该单据下的料号加到新品列表去
//                $select = 'sku';
//                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
//                foreach($skuList as $value){
//                    $sku = $value['sku'];
//                    $tName = 'pc_products';
//                    $dataProducts = array();
//                    $dataProducts['sku'] = $sku;
//                    OmAvailableModel::addTNameRow2arr($tName, $dataProducts);//将detail中的sku加入到产品制作表中
//                }
//            }
            if($ioStoreList[0]['iostoreTypeId'] == 2 && $ioStoreList[0]['useTypeId'] == 1){//如果该单是制作退料单，则要将该单中的料号状态改变为已经归还
                $select = 'sku';
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($skuList as $value){
                    $sku = $value['sku'];
                    $tName = 'pc_products';
                    $dataProducts = array();
                    $dataProducts['productsReturnerId'] = $ioStoreList[0]['addUserId'];//归还人即该单据的添加人
                    $dataProducts['productsReturnTime'] = $now;//归还人即该单据的添加人
                    $where = "WHERE sku='$sku'";
                    OmAvailableModel::updateTNameRow2arr($tName, $dataProducts, $where);//将detail中的sku加入到产品制作表中
                }
            }
            BaseModel::commit();
            BaseModel::autoCommit();

        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();

        }
    }

    /**
	 *功能：提供给刊登系统的接口，根据spu或combineSpu返回对应的sku及类别
	 * */
	public function act_getSkuAndCategoryWithSpuForPA(){
	    $spu = isset ($_REQUEST['spu']) ? post_check(trim($_REQUEST['spu'])) : "";
		if (empty($spu)){
			self :: $errCode = 101;
			self :: $errMsg = "spu为空";
			return false;
		}
        $returnArr = array();
        $returnArr[$spu] = array();//要返回的数组信息
        $array = array();//临时的数组变量
        $tName = 'pc_goods';
        $select = 'sku,goodsCategory';
        $where = "WHERE is_delete=0 AND spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuList)){//如果在goods表找到了直接返回，否则去组合表中查
            foreach($skuList as $value){
                $tmpArr = array();
                $tmpArr['sku'] = $value['sku'];
                $tmpArr['goods_category'] = !empty($value['goodsCategory'])?$value['goodsCategory']:0;//如果无类别信息返回0
                $array[] = $tmpArr;
            }
            $returnArr[$spu] = $array;
            self :: $errCode = 200;
			self :: $errMsg = "成功";
			return $returnArr;
        }
        $tName = 'pc_goods_combine';
        $select = 'combineSku';
        $where = "WHERE is_delete=0 AND combineSpu='$spu'";
        $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($combineSkuList)){
            foreach($combineSkuList as $value){
                $combineSku = $value['combineSku'];
                $tName = 'pc_sku_combine_relation';
                $select = 'sku';
                $where = "WHERE combineSku='$combineSku'";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $sku = $skuList[0]['sku'];
                $tmpArr = array();
                $tmpArr['sku'] = $combineSku;
                $tName = 'pc_goods';
                $select = 'goodsCategory';
                $where = "WHERE is_delete=0 AND sku='$sku'";
                $goodsCategoryList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpArr['goods_category'] = !empty($goodsCategoryList[0]['goodsCategory'])?$goodsCategoryList[0]['goodsCategory']:0;//如果无类别信息返回0
                $array[] = $tmpArr;
            }
            $returnArr[$spu] = $array;
            self :: $errCode = 200;
			self :: $errMsg = "成功";
			return $returnArr;
        }else{
            self :: $errCode = 400;
			self :: $errMsg = "不存在 $spu 信息";
			return false;
        }
    }

    /**
	 *功能：提供给刊登系统的接口，根据spu或combineSpu数组返回对应的sku及类别
	 * */
	public function act_getSkuAndCategoryWithSpuArrForPA(){
	    $spuArr = json_decode($_REQUEST['spuArr'],true);
		if (!is_array($spuArr) || empty($spuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "spuArr 不是数组或者为空";
			return false;
		}
        $returnArr = array();
        foreach($spuArr as $spu){
            $returnArr[$spu] = array();//要返回的数组信息
            $array = array();//临时的数组变量
            $tName = 'pc_goods';
            $select = 'sku,goodsCategory';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($skuList)){//如果在goods表找到了直接返回，否则去组合表中查
                foreach($skuList as $value){
                    $tmpArr = array();
                    $tmpArr['sku'] = $value['sku'];
                    $tmpArr['goods_category'] = !empty($value['goodsCategory'])?$value['goodsCategory']:0;//如果无类别信息返回0
                    $array[] = $tmpArr;
                }
                $returnArr[$spu] = $array;
                continue;
            }
            $tName = 'pc_goods_combine';
            $select = 'combineSku';
            $where = "WHERE is_delete=0 AND combineSpu='$spu'";
            $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($combineSkuList)){
                foreach($combineSkuList as $value){
                    $combineSku = $value['combineSku'];
                    $tName = 'pc_sku_combine_relation';
                    $select = 'sku';
                    $where = "WHERE combineSku='$combineSku'";
                    $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $sku = $skuList[0]['sku'];
                    $tmpArr = array();
                    $tmpArr['sku'] = $combineSku;
                    $tName = 'pc_goods';
                    $select = 'goodsCategory';
                    $where = "WHERE is_delete=0 AND sku='$sku'";
                    $goodsCategoryList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $tmpArr['goods_category'] = !empty($goodsCategoryList[0]['goodsCategory'])?$goodsCategoryList[0]['goodsCategory']:0;//如果无类别信息返回0
                    $array[] = $tmpArr;
                }
                $returnArr[$spu] = $array;

            }else{
                $returnArr[$spu] = array();
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
     * 提供给刊登那边的接口，根据SPU返回对应SPU下每个SKU的状态
     * 三无产品， 即符合以下任一条件的产品
        1. 无包材
        2. 无重量
        3. 无英文品名
        4. 无海关编码
        5. 无仓位
        6. 停售
     * 传递一个spu， 拉取所有sku的三无判断， 以sku为键值对
        array(
        //sku=>array(状态码, 原因)
        '1001_A' => array('0',''),    //正常
        '1001_B' => array('1','无仓位'),
        '1001_C' => array('2','无重量'),
        '1001_D' => array('6','停售', 'ebay'),
        )
        不同原因用不同的状态码标示。

        add by zqt 20140626 支持虚拟SPU
     */
    function act_getSkusStatusCodeBySpu() {
		$spu = isset ($_REQUEST['spu']) ? $_REQUEST['spu'] : "";
        if(empty($spu)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU为空";
			return false;
        }
        $returnArr = array();
        $tName = 'pc_goods';
        $select = 'sku,goodsStatus,pmId,goodsWeight';
        $where = "WHERE is_delete=0 AND spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuList)){//单料号
            $tName = 'pc_spu_tax_hscode';
            $select = 'customsNameEN,hsCode';
            $where = "WHERE spu='$spu'";
            $spuHscodeList = OmAvailableModel::getTNameList($tName, $select ,$where);
            $customsNameEN = $spuHscodeList[0]['customsNameEN'];//英文品名
            $hsCode        = $spuHscodeList[0]['hsCode'];//海关编码
		    foreach($skuList as $value){
		       $sku = $value['sku'];
               $goodsStatus = $value['goodsStatus'];//状态
               $pmId = $value['pmId'];//包材id
               $goodsWeight = $value['goodsWeight'];//重量
               $tName = 'pc_goods_whId_location_raletion';
               $where = "WHERE sku='$sku' AND isHasLocation=1";
               $isHasLocation = OmAvailableModel::getTNameCount($tName, $where);//是否有仓位
               $tmpArr = array();
               $flag = 0;//标识改料号是否正常，默认为0，正常
               if(intval($pmId) <= 0){
                  $tmpArr[1] = '无包材';
                  $flag = 1;
               }
               if($goodsWeight == 0){
                  $tmpArr[2] = '无重量';
                  $flag = 1;
               }
               //恢复拦截
               if(empty($customsNameEN)){
                  $tmpArr[3] = '无英文品名';
                  $flag = 1;
               }
               if(empty($hsCode)){
                  $tmpArr[4] = '无海关编码';
                  $flag = 1;
               }

               if(!$isHasLocation){
                  $tmpArr[5] = '无仓位';
                  $flag = 1;
               }
               if($goodsStatus != 1 && $goodsStatus != 51){
                  $tmpArr[6] = '停售/暂时停售';
                  $flag = 1;
               }
               if($flag == 0){
                  $tmpArr[0] = '正常';
               }
               $returnArr[$sku] = $tmpArr;
		    }
            self :: $errCode = '200';
			self :: $errMsg = "真实SPU返回成功";
			return $returnArr;
        }
        //虚拟料号
        $combineSkuDetailInfo = getSkuDetailInfoByCombineSpu($spu);
        if(!empty($combineSkuDetailInfo)){
            $skuArr = array();
            foreach($combineSkuDetailInfo as $combineSku=>$skuDetailInfoArr){
                $skuArr = array();
                foreach($skuDetailInfoArr as $sku=>$count){
                    $skuArr[] = "'".$sku."'";
                }
                $skuSqlStr = implode(',', $skuArr);
                if(!empty($skuSqlStr)){
                    $tName = 'pc_goods';
                    $select = 'spu,sku,goodsStatus,pmId,goodsWeight';
                    $where = "WHERE is_delete=0 AND sku in($skuSqlStr)";
                    $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $flag = 0;//标识改料号是否正常，默认为0，正常
                    $tmpArr = array();
                    if(!empty($skuList)){//单料号
            		    foreach($skuList as $value){
            		       $spu = $value['spu'];
            		       $sku = $value['sku'];
                           $goodsStatus = $value['goodsStatus'];//状态
                           $pmId = $value['pmId'];//包材id
                           $goodsWeight = $value['goodsWeight'];//重量
                           $tName = 'pc_spu_tax_hscode';
                           $select = 'customsNameEN,hsCode';
                           $where = "WHERE spu='$spu'";
                           $spuHscodeList = OmAvailableModel::getTNameList($tName, $select ,$where);
                           $customsNameEN = $spuHscodeList[0]['customsNameEN'];//英文品名
                           $hsCode        = $spuHscodeList[0]['hsCode'];//海关编码

                           $tName = 'pc_goods_whId_location_raletion';
                           $where = "WHERE sku='$sku' AND isHasLocation=1";
                           $isHasLocation = OmAvailableModel::getTNameCount($tName, $where);//是否有仓位

                           if(intval($pmId) <= 0){
                              $tmpArr[1] = '无包材';
                              $flag = 1;
                           }
                           if($goodsWeight == 0){
                              $tmpArr[2] = '无重量';
                              $flag = 1;
                           }
                           //恢复拦截
                           if(empty($customsNameEN)){
                              $tmpArr[3] = '无英文品名';
                              $flag = 1;
                           }
                           if(empty($hsCode)){
                              $tmpArr[4] = '无海关编码';
                              $flag = 1;
                           }
                           if(!$isHasLocation){
                              $tmpArr[5] = '无仓位';
                              $flag = 1;
                           }
                           if($goodsStatus != 1 && $goodsStatus != 51){
                              $tmpArr[6] = '停售/暂时停售';
                              $flag = 1;
                           }
            		    }
                        if($flag == 0){
                           $tmpArr[0] = '正常';
                        }
                    }
                    $returnArr[$combineSku] = $tmpArr;
                }
            }
            self :: $errCode = '200';
			self :: $errMsg = "虚拟SPU返回成功";
			return $returnArr;
        }else{
            self :: $errCode = '102';
			self :: $errMsg = "该SPU下不存在SKU";
			return false;
        }
	}

    /**
     * 提供给刊登那边的接口，根据SPU返回对应SPU下每个SKU的状态
     * 三无产品， 即符合以下任一条件的产品
        1. 无包材
        2. 无重量
        3. 无英文品名
        4. 无海关编码
        5. 无仓位
        6. 停售
     * 传递一个spu， 拉取所有sku的三无判断， 以sku为键值对
        array(
        //sku=>array(状态码, 原因)
        '1001_A' => array('0',''),    //正常
        '1001_B' => array('1','无仓位'),
        '1001_C' => array('2','无重量'),
        '1001_D' => array('6','停售', 'ebay'),
        )
        不同原因用不同的状态码标示。

        add by zqt 20140626 支持虚拟SPU
        为第二个版本接口
     */
    function act_getSkusStatusCodeBySpuV2() {
		$spu = isset ($_REQUEST['spu']) ? $_REQUEST['spu'] : "";
        if(empty($spu)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU为空";
			return false;
        }
        $returnArr = array();
        $tName = 'pc_goods';
        $select = 'sku,goodsStatus,pmId,goodsWeight';
        $where = "WHERE is_delete=0 AND spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuList)){//单料号
            $tName = 'pc_spu_tax_hscode';
            $select = 'customsNameEN,hsCode';
            $where = "WHERE spu='$spu'";
            $spuHscodeList = OmAvailableModel::getTNameList($tName, $select ,$where);
            $customsNameEN = $spuHscodeList[0]['customsNameEN'];//英文品名
            $hsCode        = $spuHscodeList[0]['hsCode'];//海关编码
            $skuTmpArr = array();
            foreach($skuList as $value){
                $skuTmpArr[] = $value['sku'];
            }
            $skuTmpArr = array_filter($skuTmpArr);
            $overSeaSkuLocationList = UserCacheModel::getOpenSysApi('oversea.getSkuPos',array('type'=>'getSkuArrPos','skuArr'=>json_encode($skuTmpArr)));//根据SKU数组获取对应的美国仓库的仓位信息
            $overSeaSkuLocationKVArr = $overSeaSkuLocationList['data'];
		    foreach($skuList as $value){
		       $sku = $value['sku'];
               $goodsStatus = $value['goodsStatus'];//状态
               $pmId = $value['pmId'];//包材id
               $goodsWeight = $value['goodsWeight'];//重量
               $tName = 'pc_goods_whId_location_raletion';
               $where = "WHERE sku='$sku' AND isHasLocation=1";
               $isHasLocation = OmAvailableModel::getTNameCount($tName, $where);//是否有仓位
               $tmpArr = array();//返回的为二维数组，第一维度为1，或2,1为中国仓，2为美国仓，第二维标识状态
               $flagCN = 0;//标识该料号中国仓是否正常，默认为0，正常
               $flagAM = 0;//标识该料号美国仓是否正常，默认为0，正常
               if(intval($pmId) <= 0){
                  $tmpArr[1][1] = '无包材';
                  $tmpArr[2][1] = '无包材';
                  $flagCN = 1;
                  $flagAM = 1;
               }
               if($goodsWeight == 0){
                  $tmpArr[1][2] = '无重量';
                  $tmpArr[2][2] = '无重量';
                  $flagCN = 1;
                  $flagAM = 1;
               }
               //恢复拦截
               if(empty($customsNameEN)){
                  $tmpArr[1][3] = '无英文品名';
                  $tmpArr[2][3] = '无英文品名';
                  $flagCN = 1;
                  $flagAM = 1;
               }
               if(empty($hsCode)){
                  $tmpArr[1][4] = '无海关编码';
                  $tmpArr[2][4] = '无海关编码';
                  $flagCN = 1;
                  $flagAM = 1;
               }

               if(!$isHasLocation){
                  $tmpArr[1][5] = '无仓位';
                  $flagCN = 1;
               }
               if(empty($overSeaSkuLocationKVArr[$sku]['pos'])){
                  $tmpArr[2][5] = '无仓位';
                  $flagAM = 1;
               }
               //停售的先不包括在内
               //if($goodsStatus != 1 && $goodsStatus != 51){
//                  $tmpArr[6] = '停售/暂时停售';
//                  $flag = 1;
//               }
               if($flagCN == 0){
                  $tmpArr[1][0] = '正常';
               }
               if($flagAM == 0){
                  $tmpArr[2][0] = '正常';
               }
               $returnArr[$sku] = $tmpArr;
		    }
            self :: $errCode = '200';
			self :: $errMsg = "真实SPU返回成功";
			return $returnArr;
        }
        //虚拟料号
        $combineSkuDetailInfo = getSkuDetailInfoByCombineSpu($spu);
        if(!empty($combineSkuDetailInfo)){
            $skuArr = array();
            foreach($combineSkuDetailInfo as $combineSku=>$skuDetailInfoArr){
                $skuArr = array();
                foreach($skuDetailInfoArr as $sku=>$count){
                    $skuArr[] = "'".$sku."'";
                }
                $skuSqlStr = implode(',', $skuArr);
                if(!empty($skuSqlStr)){
                    $tName = 'pc_goods';
                    $select = 'spu,sku,goodsStatus,pmId,goodsWeight';
                    $where = "WHERE is_delete=0 AND sku in($skuSqlStr)";
                    $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $skuTmpArr = array();
                    foreach($skuList as $value){
                        $skuTmpArr[] = $value['sku'];
                    }
                    $skuTmpArr = array_filter($skuTmpArr);
                    $overSeaSkuLocationList = UserCacheModel::getOpenSysApi('oversea.getSkuPos',array('type'=>'getSkuArrPos','skuArr'=>json_encode($skuTmpArr)));//根据SKU数组获取对应的美国仓库的仓位信息
                    $overSeaSkuLocationKVArr = $overSeaSkuLocationList['data'];
                    $tmpArr = array();//返回的为二维数组，第一维度为1，或2,1为中国仓，2为美国仓，第二维标识状态
                    $flagCN = 0;//标识该料号中国仓是否正常，默认为0，正常
                    $flagAM = 0;//标识该料号美国仓是否正常，默认为0，正常
                    if(!empty($skuList)){//单料号
            		    foreach($skuList as $value){
            		       $spu = $value['spu'];
            		       $sku = $value['sku'];
                           $goodsStatus = $value['goodsStatus'];//状态
                           $pmId = $value['pmId'];//包材id
                           $goodsWeight = $value['goodsWeight'];//重量
                           $tName = 'pc_spu_tax_hscode';
                           $select = 'customsNameEN,hsCode';
                           $where = "WHERE spu='$spu'";
                           $spuHscodeList = OmAvailableModel::getTNameList($tName, $select ,$where);
                           $customsNameEN = $spuHscodeList[0]['customsNameEN'];//英文品名
                           $hsCode        = $spuHscodeList[0]['hsCode'];//海关编码

                           $tName = 'pc_goods_whId_location_raletion';
                           $where = "WHERE sku='$sku' AND isHasLocation=1";
                           $isHasLocation = OmAvailableModel::getTNameCount($tName, $where);//是否有仓位

                           if(intval($pmId) <= 0){
                              $tmpArr[1][1] = '无包材';
                              $tmpArr[2][1] = '无包材';
                              $flagCN = 1;
                              $flagAM = 1;
                           }
                           if($goodsWeight == 0){
                              $tmpArr[1][2] = '无重量';
                              $tmpArr[2][2] = '无重量';
                              $flagCN = 1;
                              $flagAM = 1;
                           }
                           //恢复拦截
                           if(empty($customsNameEN)){
                              $tmpArr[1][3] = '无英文品名';
                              $tmpArr[2][3] = '无英文品名';
                              $flagCN = 1;
                              $flagAM = 1;
                           }
                           if(empty($hsCode)){
                              $tmpArr[1][4] = '无海关编码';
                              $tmpArr[2][4] = '无海关编码';
                              $flagCN = 1;
                              $flagAM = 1;
                           }

                           if(!$isHasLocation){
                              $tmpArr[1][5] = '无仓位';
                              $flagCN = 1;
                           }
                           if(empty($overSeaSkuLocationKVArr[$sku]['pos'])){
                              $tmpArr[2][5] = '无仓位';
                              $flagAM = 1;
                           }
                           //停售的先不包括在内
                           //if($goodsStatus != 1 && $goodsStatus != 51){
            //                  $tmpArr[6] = '停售/暂时停售';
            //                  $flag = 1;
            //               }
            		    }
                        if($flagCN == 0){
                          $tmpArr[1][0] = '正常';
                        }
                        if($flagAM == 0){
                          $tmpArr[2][0] = '正常';
                        }
                    }
                    $returnArr[$combineSku] = $tmpArr;
                }
            }
            self :: $errCode = '200';
			self :: $errMsg = "虚拟SPU返回成功";
			return $returnArr;
        }else{
            self :: $errCode = '102';
			self :: $errMsg = "该SPU下不存在SKU";
			return false;
        }
	}

	//提供给深圳ERP的接口，根据SPU获取海关编码
    function act_getHscodeBySpuForERP() {
		$spu = isset ($_REQUEST['spu']) ? $_REQUEST['spu'] : "";
        if(empty($spu)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU为空";
			return false;
        }
        $tName = 'pc_spu_tax_hscode';
        $select = '*';
        $where = "WHERE spu='$spu' limit 1";
        $spuHscodeList = OmAvailableModel::getTNameList($tName, $select, $where);
		return $spuHscodeList[0];
	}

    //提供物流系统接口，根据spu数组返回对应的海关编码信息，即返回pc_spu_tax_hscode中的字段信息，格式为key=>value,key为spu,value为对应的信息
    function act_getHscodeInfoBySpuArr() {
		$spuArr = json_decode($_REQUEST['spuArr'],true);
        $spuArr = array_filter($spuArr);
        if(!is_array($spuArr)){
            self::$errCode = "101";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($spuArr)){
            self::$errCode = "102";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnArr = array();
        foreach($spuArr as $value){
            $tName = 'pc_spu_tax_hscode';
            $select = '*';
            $where = "WHERE spu='$value'";
            $spuHscodeList = OmAvailableModel::getTNameList($tName, $select, $where);
            $returnArr[$value] = !empty($spuHscodeList)?$spuHscodeList[0]:array();
        }
        self::$errCode = "200";
		self::$errMsg = "成功";
		return $returnArr;
	}

    //提供停售SKU接口给速卖通刊登系统,返回格式为二维数组，SPU及sku
    function act_getNoSaleSkuArr() {
		$tName = 'pc_goods';
        $select = 'spu,sku';
        $where = "WHERE is_delete=0 AND goodsStatus>1 AND goodsStatus<=50";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        self::$errCode = "200";
		self::$errMsg = "成功";
		return $skuList;
	}

    //提供全部停售的SPU接口给速卖通刊登系统,返回格式为一维数组，只有该SPU下的所有子料号均停售才算停售
    function act_getNoSaleSpuArr() {
        $returnArr = array();
		$tName = 'pc_goods';
        $select = 'spu';
        $where = "WHERE is_delete=0 AND goodsStatus=2 group by spu";//停售SPU
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);

        foreach($spuList as $value){
            $tName = 'pc_goods';
            $where = "WHERE is_delete=0 AND spu='{$value['spu']}' AND goodsStatus<>2 ";
            $onlineCount = OmAvailableModel::getTNameCount($tName, $where);//在线SKU记录数
            if(!$onlineCount){
                $returnArr[] = $value['spu'];
            }
        }
        self::$errCode = "200";
		self::$errMsg = "成功";
		return $returnArr;
	}

	//根据spu数组（json参数），返回对应SPU对应的平台及销售人（id,name,username）
    function act_getSpuSalerIUNBySpuArr() {
		$spuArr = json_decode($_REQUEST['spuArr'],true);
        $spuArr = array_filter($spuArr);
        if(!is_array($spuArr)){
            self::$errCode = "101";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($spuArr)){
            self::$errCode = "102";
			self::$errMsg = "参数数组为空";
			return;
        }
        $returnArr = array();
        foreach($spuArr as $spu){
            $tName = 'pc_spu_saler_single';
            $select = 'platformId,salerId';
            $where = "WHERE is_delete=0 and isAgree=2 and spu='$spu'";
            $spuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($spuSalerList)){
                $tName = 'pc_spu_saler_combine';
                $spuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
            }
            if(!empty($spuSalerList)){
                $tmpArr = array();//临时数组，用来存放平台id及对应销售人信息
                foreach($spuSalerList as $value){
                    $tmpArr2 = array();//临时数组2，用来存放销售人员信息
                    $tmpArr2['global_user_id'] = $value['salerId'];
                    $tName = 'power_global_user';
                    $select = 'global_user_login_name,global_user_name';
                    $where = "WHERE global_user_is_delete=0 AND global_user_id='{$value['salerId']}'";
                    $userInfo = OmAvailableModel::getTNameList($tName, $select, $where);
                    if(!empty($userInfo)){
                        $tmpArr2['global_user_login_name'] = $userInfo[0]['global_user_login_name'];
                        $tmpArr2['global_user_name'] = $userInfo[0]['global_user_name'];
                    }
                    $tmpArr[$value['platformId']] = $tmpArr2;
                }
                $returnArr[$spu] = $tmpArr;
            }else{
                $returnArr[$spu] = array();
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
	}

	//提供给深圳ERP的接口，根据SPU获取对应销售人id
    function act_getSpuSalerIdsBySpu() {
		$spu = isset ($_REQUEST['spu']) ? $_REQUEST['spu'] : "";
        if(empty($spu)){
            self :: $errCode = '101';
			self :: $errMsg = "SPU为空";
			return false;
        }
        $tName = 'pc_goods';
        $select = 'purchaseId';
        $where = "WHERE is_delete=0 and spu='$spu' limit 1";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuList)){
            $tName = 'pc_goods_combine';
            $select = 'combineUserId as purchaseId';
            $where = "WHERE is_delete=0 and combineSpu='$spu' limit 1";
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($spuList)){
				self :: $errCode = 400;
				self :: $errMsg = "无该SPU信息";
				return false;
            }
        }
        $tName = 'pc_spu_saler_single';
        $select = 'platformId,salerId';
        $where = "WHERE is_delete=0 and isAgree=2 and spu='$spu'";
        $spuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
        $spuSalerArr = array();
        if(empty($spuSalerList)){
            $tName = 'pc_spu_saler_combine';
            $spuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
        }
        if(!empty($spuSalerList)){
            $spuSalerArr['spu'] = $spu;
            $spuSalerArr['purchaseId'] = $spuList[0]['purchaseId'];
            $tmpArr = array();
            foreach($spuSalerList as $value){
                $tmpArr[$value['platformId']] = $value['salerId'];
            }
            $spuSalerArr['salerArr'] = $tmpArr;
            self :: $errCode = 200;
			self :: $errMsg = "成功";
            return $spuSalerArr;
        }
        self :: $errCode = 404;
		self :: $errMsg = "无该SPU销售人信息";
		return false;
	}

    //提供指定salerId数组及平台id,返回该平台下对应salerId对应的所有SPU
    function act_getSalersBySpuArr() {
        $salerIdArr = json_decode($_REQUEST['salerIdArr'],true);
        $spuArr = array_filter($spuArr);
        $platformId = $_REQUEST['platformId'];
        if(!is_array($salerIdArr)){
            self::$errCode = "101";
			self::$errMsg = "参数必须是Json数组";
			return;
        }
        if(empty($salerIdArr)){
            self::$errCode = "102";
			self::$errMsg = "参数数组为空";
			return;
        }
        if(intval($platformId) <= 0){
            self::$errCode = "103";
			self::$errMsg = "平台id有误";
			return;
        }
        $returnArr = array();
        foreach($salerIdArr as $value){
            $tName = 'pc_spu_saler_single';
            $select = 'spu';
            $where = "WHERE is_delete=0 and isAgree=2 AND salerId='$value' AND platformId='$platformId'";
            $spuSalerSingleList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($spuSalerSingleList)){//如果是单料号SPU
                $tmpArr = array();
                foreach($spuSalerSingleList as $valueSaler){
                    $tmpArr[] = $valueSaler['spu'];
                }
                $returnArr[$value] = $tmpArr;
                continue;//下一次循环
            }
            $tName = 'pc_spu_saler_single';
            $spuSalerCombineList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($spuSalerCombineList)){
                $tmpArr = array();
                foreach($spuSalerCombineList as $valueSaler){
                    $tmpArr[] = $valueSaler['spu'];
                }
                $returnArr[$value] = $tmpArr;
            }else{
                $returnArr[$value] = array();
            }
        }
        self::$errCode = "200";
		self::$errMsg = "成功";
		return $returnArr;
	}

    //提供指定spuArr对应的所有销售人，返回对应平台id及销售人id的数组
    function act_getCombineInfoBycombineSpu() {
        $combineSpu = $_REQUEST['combineSpu'];
        if(empty($combineSpu)){
            self::$errCode = "101";
			self::$errMsg = "combineSpu为空";
			return;
        }
        $tName = 'pc_goods_combine';
        $select = 'combineSku';
        $where = "WHERE is_delete=0 AND combineSpu='$combineSpu'";
        $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($combineSpuList)){
            self::$errCode = "404";
    		self::$errMsg = "无该combineSpu记录";
    		return false;
        }else{
            $returnArr = array();
            foreach($combineSpuList as $value){
                $combineSku = $value['combineSku'];
                $tName = 'pc_sku_combine_relation';
                $select = 'sku,count';
                $where = "WHERE combineSku='$combineSku'";
                $combineSkuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(empty($combineSkuRelationList)){
                    continue;
                }else{
                    $tmpArr = array();
                    foreach($combineSkuRelationList as $value){
                        $tmpArr[$value['sku']] = $value['count'];
                    }
                    $returnArr[$combineSku] = $tmpArr;
                }
            }
            self::$errCode = "200";
    		self::$errMsg = "成功";
    		return $returnArr;
        }
	}

    //提供指定spuArr对应的所有销售人，返回对应平台id及销售人id的数组
    function act_getOverSeaTaxBySpu() {
        $spu = !empty($_REQUEST['spu'])?$_REQUEST['spu']:'';
        $countryCode = !empty($_REQUEST['countryCode'])?$_REQUEST['countryCode']:'';
        if(empty($spu)){
            self::$errCode = "101";
			self::$errMsg = "spu为空";
			return;
        }
        if(empty($countryCode)){
            self::$errCode = "102";
			self::$errMsg = "countryCode为空";
			return;
        }
        $tName = 'pc_spu_oversea_tax';
        $select = 'tax';
        $where = "WHERE is_delete=0 AND spu='$spu' AND countryCode='$countryCode'";
        $spuOverseaTaxList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuOverseaTaxList)){
            self::$errCode = "404";
			self::$errMsg = "没有该SPU对应的countryCode记录";
			return false;
        }else{
            self::$errCode = "200";
			self::$errMsg = "成功";
			return $spuOverseaTaxList[0]['tax'];
        }
	}

    /**
	 *功能：提供接口，根据sku或combineSku数组返回对应的sku及类别
	 * */
	public function act_getCategoryBySkuArr(){
	    $skuArr = json_decode($_REQUEST['skuArr'],true);
		if (!is_array($skuArr) || empty($skuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "skuArr 不是数组或者为空";
			return false;
		}
        $returnArr = array();
        foreach($skuArr as $sku){
            $tName = 'pc_goods';
            $select = 'goodsCategory';
            $where = "WHERE is_delete=0 AND sku='$sku'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($skuList)){//如果在goods表找到了直接返回，否则去组合表中查
                $returnArr[$sku] = $skuList[0]['goodsCategory'];
                continue;
            }
            $tName = 'pc_sku_combine_relation';
            $select = 'sku';
            $where = "WHERE combineSku='$sku'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($skuList)){
                $tName = 'pc_goods';
                $select = 'goodsCategory';
                $where = "WHERE is_delete=0 AND sku='{$skuList[0]['sku']}'";
                $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($skuRelationList)){
                    $returnArr[$sku] = $skuRelationList[0]['goodsCategory'];
                }else{
                    $returnArr[$sku] = '';
                }
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，根据spu或combineSpu数组返回对应的sku及类别
	 * */
	public function act_getIsOnlineBySpuArr(){
	    $spuArr = json_decode($_REQUEST['spuArr'],true);
		if (!is_array($spuArr) || empty($spuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "spuArr 不是数组或者为空";
			return false;
		}
        $returnArr = array();
        foreach($spuArr as $spu){
            $tName = 'pc_goods';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $spuCountTotal = OmAvailableModel::getTNameCount($tName, $where);//该SPU为单料号时，SKU的总数
            if($spuCountTotal){//如果在goods表找到了直接返回，否则去组合表中查
                $tName = 'pc_goods';
                $where = "WHERE is_delete=0 AND spu='$spu' AND (goodsStatus=1 or goodsStatus>=51)";
                $spuCountOnline = OmAvailableModel::getTNameCount($tName, $where);
                if($spuCountTotal == $spuCountOnline){
                    $returnArr[$spu] = 1;//1为在线
                }else{
                    $returnArr[$spu] = 0;//0为非在线
                }
                continue;
            }
            $tName = 'pc_goods_combine';
            $select = 'combineSku';
            $where = "WHERE is_delete=0 AND combineSpu='$spu'";
            $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $combineSkuArr = array();
            foreach($combineSkuList as $value){
                $combineSkuArr[] = "'".$value['combineSku']."'";
            }
            if(!empty($combineSkuArr)){
                $combineSkuStr = implode(',', $combineSkuArr);
                $tName = 'pc_sku_combine_relation';
                $select = 'sku';
                $where = "WHERE combineSku in($combineSkuStr)";
                $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpSkuArr = array();
                foreach($skuRelationList as $value){
                    $tmpSkuArr[] = "'".$value['sku']."'";
                }
                if(!empty($tmpSkuArr)){//找对应sku的SPU
                    $tmpSkuStr = implode(',', $tmpSkuArr);
                    $tName = 'pc_goods';
                    $where = "WHERE is_delete=0 AND sku in($tmpSkuStr)";
                    $tmpSkuCountTotal = OmAvailableModel::getTNameCount($tName, $where);//改combineSpu下所有combineSku下的所有sku的总数
                    if($tmpSkuCountTotal){
                        $tName = 'pc_goods';
                        $where = "WHERE is_delete=0 AND sku in($tmpSkuStr) and (goodsStatus=1 OR goodsStatus=51)";
                        $tmpSkuCountOnline = OmAvailableModel::getTNameCount($tName, $where);
                        if($tmpSkuCountTotal == $tmpSkuCountOnline){
                            $returnArr[$spu] = 1;
                        }else{
                            $returnArr[$spu] = 0;
                        }
                    }
                }

            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，根据spu数组返回包含该combineSpu数组
	 * */
	public function act_getCombineSpuArrBySpuArr(){
	    $spuArr = json_decode($_REQUEST['spuArr'],true);
		if (!is_array($spuArr) || empty($spuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "spuArr 不是数组或者为空";
			return false;
		}
        $returnArr = array();
        foreach($spuArr as $spu){
            $tName = 'pc_goods';
            $select = 'sku';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);//该SPU为单料号时，SKU的总数
            if(!empty($skuList)){
                $tmpArr = array();
                foreach($skuList as $value){
                    $tmpArr[] = "'".$value['sku']."'";
                }
                if(!empty($tmpArr)){
                    $tmpStr = implode(',', $tmpArr);
                    $tName = 'pc_sku_combine_relation';
                    $select = 'combineSku';
                    $where = "WHERE sku in($tmpStr)";
                    $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    if(!empty($combineSkuList)){
                        $tmpArr = array();
                        foreach($combineSkuList as $value){
                            $tmpArr[] = "'".$value['combineSku']."'";
                        }
                        if(!empty($tmpArr)){
                            $tmpStr = implode(',', $tmpArr);
                            $tName = 'pc_goods_combine';
                            $select = 'combineSpu';
                            $where = "WHERE combineSku in($tmpStr)";
                            $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                            if(!empty($combineSpuList)){
                                $tmpArr = array();
                                foreach($combineSpuList as $value){
                                    $tmpArr[] = $value['combineSpu'];
                                }
                                $tmpArr = array_unique($tmpArr);
                                $returnArr[$spu] = $tmpArr;
                            }
                        }
                    }
                }
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，根据sku数组返回对应的特殊属性/特殊料号对应运输方式的数组
     *返回格式：类似 {"errCode":200,"errMsg":"\u6210\u529f","data":{"OS000443":{"canOrNot":"2","tcInfo":{"1":["87"],"2":["67","68"],"3":["41"],"4":["42"],"91":["115"],"92":["116"],"93":["117"]},"priority":2}}}
	 * */
	public function act_getSpecialPOTBySkuArr(){
	    $skuArr = json_decode($_REQUEST['skuArr'],true);
		if (!is_array($skuArr) || empty($skuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "skuArr 不是数组或者为空";
			return false;
		}
        $returnArr = array();
        foreach($skuArr as $sku){
            $tName = 'pc_goods';
            $select = 'spu';
            $where = "WHERE is_delete=0 AND sku='$sku' limit 1";
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $spu = $spuList[0]['spu'];
            if(!empty($spu)){
                $tName = 'pc_special_transport_manager_spu';//特殊料号-运输方式的优先级比特殊属性的高，所以优先选择
                $select = '*';
                $where = "WHERE spu='$spu'";
                $pstmsList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($pstmsList)){
                    $pstmsStmnId = $pstmsList[0]['stmnId'];
                    $tName = 'pc_special_transport_manager';
                    $select = '*';
                    $where = "WHERE id='$pstmsStmnId'";
                    $pstmList = OmAvailableModel::getTNameList($tName, $select, $where);
                    if(!empty($pstmList)){
                        $specialTransportManagerName = $pstmList[0]['specialTransportManagerName'];
                        $isOn = $pstmList[0]['isOn'];
                        if($isOn == 1){//启用才生效，禁用则不处理
                            $tName = 'pc_special_stmnid_transportid';
                            $select = '*';
                            $where = "WHERE stmnId='$pstmsStmnId'";
                            $psstList = OmAvailableModel::getTNameList($tName, $select, $where);
                            if(!empty($psstList)){//对应可能有多个运输方式
                                $tmpReturnArr = array();
                                $tmpReturnTCArr = array();
                                foreach($psstList as $value){
                                    $psstId      = $value['id'];
                                    $transportId = $value['transportId'];
                                    $tName = 'pc_special_stid_channel';
                                    $select = '*';
                                    $where = "WHERE stId='$psstId'";
                                    $psscList = OmAvailableModel::getTNameList($tName, $select, $where);
                                    //$tmpTCArr = array();
                                    foreach($psscList as $v){
                                        $tmpReturnTCArr[] = $v['channelId'];
                                    }
                                    //$tmpReturnTCArr[$transportId] = $tmpTCArr;//key 为 transportId, value为对应channel数组
                                }
                                $tmpReturnArr['channelIdArr'] = array_unique(array_filter($tmpReturnTCArr));
                                $tmpReturnArr['priority'] = 1;//优先级，特殊料号为1，特殊属性为2
                            }
                            $returnArr[$sku] = $tmpReturnArr;
                            continue;
                        }
                    }
                }

                $tName = 'pc_special_property_spu';//特殊属性-运输方式的优先级较低
                $select = '*';
                $where = "WHERE spu='$spu'";
                $pspsList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($pspsList)){
                    $tmpChannelArr = array();
                    foreach($pspsList as $pspsValue){
                        $propertyId = intval($pspsValue['propertyId']);
                        if($propertyId > 0){
                            $tName = 'pc_special_property';
                            $where = "WHERE isRelateTransport=1 AND isOn=1 AND id=$propertyId";
                            if(OmAvailableModel::getTNameCount($tName, $where)){//如果该特殊属性有效（关联了运输方式，并且开启了）
                                $tName = 'pc_special_prepertyid_transportid';
                                $select = '*';
                                $where = "WHERE propertyId=$propertyId";
                                $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
                                if(!empty($psptList)){
                                    $psptIdArr = array();
                                    foreach($psptList as $psptValue){
                                        $psptIdArr[] = $psptValue['id'];
                                    }
                                    $psptIdArrStr = '0';
                                    $psptIdArrStr = implode(',', $psptIdArr);
                                    $tName = 'pc_special_ptid_channel';
                                    $select = '*';
                                    $where = "WHERE ptId in($psptIdArrStr)";
                                    $pspcList = OmAvailableModel::getTNameList($tName, $select, $where);
                                    foreach($pspcList as $pspcValue){
                                        $tmpChannelArr[$propertyId][] = $pspcValue['channelId'];
                                    }
                                    $tmpChannelArr[$propertyId] = array_filter((array_unique($tmpChannelArr[$propertyId])));
                                }
                            }
                        }
                    }
                    if(!empty($tmpChannelArr)){
                        $tmpArr1 = array_shift($tmpChannelArr);
                        $intersectArr = array();
                        while($tmpChannelArr){
                            $tmpArr2 = array_shift($tmpChannelArr);
                            $intersectArr = array_intersect($tmpArr1, $tmpArr2);
                            $tmpArr1 = $intersectArr;
                        }
                        if(empty($intersectArr)){
                            $intersectArr = $tmpArr1;
                        }
                        $tmpArr = array();
                        foreach($intersectArr as $interValue){
                            if(intval($interValue) > 0){
                                $tmpArr[] = $interValue;
                            }
                        }
                        $tmpReturnArr['channelIdArr'] = $tmpArr;
                        $tmpReturnArr['priority'] = 2;//优先级，特殊料号为1，特殊属性为2
                        $returnArr[$sku] = $tmpReturnArr;
                    }
                }
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，根据类别path返回对应该path下的spuArr
     *返回格式：
	 * */
	public function act_getSpuArrByCategoryPath(){
	    $path = !empty ($_GET['path']) ? $_GET['path'] : '';
		if (empty($path)){
			self :: $errCode = 101;
			self :: $errMsg = "path 为空";
			return false;
		}
        $tName = 'pc_goods';
        $select = 'spu';
        $where = "WHERE is_delete=0 AND goodsCategory='$path' group by spu";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $returnArr = array();
        foreach($spuList as $value){
            $returnArr[] = $value['spu'];
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，根据combineSkuArr返回对应combineSku的combineSpu
     *返回格式：
	 * */
	public function act_getCombineSpuByCombineSkuArr(){
	    $combineSkuArr = !empty ($_GET['combineSkuArr']) ? $_GET['combineSkuArr'] : '';
        $combineSkuArr = json_decode($combineSkuArr, true);
		if (empty($combineSkuArr) || !is_array($combineSkuArr)){
			self :: $errCode = 101;
			self :: $errMsg = "combineSkuArr 为空或者不是combineSkuArr数组的Json字符串格式";
			return false;
		}
        $returnArr = array();
        foreach($combineSkuArr as $combineSku){
            $tName = 'pc_goods_combine';
            $select = 'combineSpu';
            $where = "WHERE is_delete=0 AND combineSku='$combineSku' limit 1";
            $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($combineSpuList[0]['combineSpu'])){
                $returnArr[$combineSku] = $combineSpuList[0]['combineSpu'];
            }
        }
        self :: $errCode = 200;
		self :: $errMsg = "成功";
		return $returnArr;
    }

    /**
	 *功能：提供接口，给深圳ERP新品入口的产品部大类信息的方法
	 * */
	public function act_getAllProductsLargeCategoryInfo(){
	   $tName = 'pc_products_large_category';
	   $select = '*';
       $where = "WHERE is_delete=0";
       $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
       self :: $errCode = 200;
       self :: $errMsg = "成功";
       return $returnArr;
    }


































    //提供给深圳ERP同步的接口
    /**
	 *功能：提供接口，给深圳ERP同步SPU报关相关信息
	 * */
	public function act_getSpuHscodeTaxCount(){
	   $tName = 'pc_spu_tax_hscode';
       $where = "WHERE 1=1";
       $spuHscodeCount = OmAvailableModel::getTNameCount($tName, $where);
       self :: $errCode = 200;
       self :: $errMsg = "成功";
       return $spuHscodeCount;
    }

    /**
	 *功能：提供接口，给深圳ERP同步SPU报关相关信息
	 * */
	public function act_getSpuHscodeTaxListBySP(){
	   $start = !empty ($_GET['start']) ? $_GET['start'] : 0;
       $per = !empty ($_GET['per']) ? $_GET['per'] : 0;

	   $tName = 'pc_spu_tax_hscode';
       $select = '*';
       $where = "order by id desc limit $start,$per";
       $spuHscodeList = OmAvailableModel::getTNameList($tName, $select, $where);
       self :: $errCode = 200;
       self :: $errMsg = "成功";
       return $spuHscodeList;
    }

    /**
	 *功能：提供接口，给深圳ERP同步产品信息
	 * */
	public function act_getGoodsInForERPTB(){
	   $tName = 'pc_goods';
       $select = '*';
       $where = "order by id desc limit 500";
       $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
       $returnArr = array();
       foreach($skuList as $value){
         $tmpArr = array();
         $tmpArr['goods_id'] = $value['id'];
         $tmpArr['goods_name'] = $value['goodsName'];
         $tmpArr['goods_sn'] = $value['sku'];
         $tmpArr['spu'] = $value['spu'];
         $tmpArr['goods_price'] = $value['goodsCost'];
         $tmpArr['goods_cost'] = $value['goodsCost'];
         $tmpArr['goods_weight'] = $value['goodsWeight'];
         $tmpArr['goods_length'] = $value['goodsLength'];
         $tmpArr['goods_width'] = $value['goodsWidth'];
         $tmpArr['goods_height'] = $value['goodsHeight'];
         $tmpArr['goods_category'] = $value['goodsCategory'];
         $tmpArr['ebay_user'] = 'vipchen';
         $tmpArr['color'] = $value['goodsColor'];
         $tmpArr['size'] = $value['goodsSize'];
         $goodsStatus = $value['goodsStatus'];
         if($goodsStatus == 1){//在线
            $tmpArr['isuse'] = 0;
         }elseif($goodsStatus == 51){//PK产品
            $tmpArr['isuse'] = 51;
         }elseif($goodsStatus == 2){//停售
            $tmpArr['isuse'] = 1;
         }elseif($goodsStatus == 3){//暂时停售
            $tmpArr['isuse'] = 3;
         }else{//其余的都做下线处理
            $tmpArr['isuse'] = 1;
         }
         $tmpArr['cguser'] = getPersonNameById($value['purchaseId']);
         if(intval($value['spu']) > 0){
        	$tmpArr['mainsku'] = $value['spu'];
         }else{
        	$tmpArr['mainsku'] = intval(ord(substr($value['spu'], 0, 1)).ord(substr($value['spu'], 1, 1)))*100000 + intval(substr($value['spu'], 2));
         }
         $tmpArr['add_time'] = $value['goodsCreatedTime'];
         $tmpArr['goods_code'] = $value['id']+1000000;
         $tmpArr['is_new'] = $value['isNew'];
         $returnArr[] = $tmpArr;
       }
       self :: $errCode = 200;
       self :: $errMsg = "成功";
       return $returnArr;
    }

    /**
	 *功能：我自己运行的接口，批量更新数据,根据需求批量更新销售是陈智兴的虚拟料号，将其销售人员改为对应真实SPU的销售人员
	 * */
	public function act_updateBatchForSalers(){
	   $tName = 'pc_spu_saler_combine';
       $select = 'spu';
       $where = "WHERE is_delete=0 and platformId=2 and isAgree=2 and salerId=60";
       $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
       foreach($combineSpuList as $value){
         $combineSpu = $value['spu'];
         $tName = 'pc_goods_combine';
         $select = 'combineSku';
         $where = "WHERE is_delete=0 and combineSpu='$combineSpu'";
         $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
         if(!empty($combineSkuList)){
            $tName = 'pc_sku_combine_relation';
             $select = 'sku';
             $where = "WHERE combineSku='{$combineSkuList[0]['combineSku']}'";
             $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
             if(!empty($skuList)){
                $tName = 'pc_goods';
                $select = 'spu';
                $where = "WHERE is_delete=0 and sku='{$skuList[0]['sku']}'";
                $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($spuList)){
                   $tName = 'pc_spu_saler_single';
                   $select = 'salerId';
                   $where = "WHERE is_delete=0 and platformId=2 and isAgree=2 and spu='{$spuList[0]['spu']}'";
                   $singleSpuSalerIdList = OmAvailableModel::getTNameList($tName, $select, $where);
                   if(!empty($singleSpuSalerIdList)){
                     $salerId = $singleSpuSalerIdList[0]['salerId'];
                     $saler = getPersonNameById($salerId);
                     $tName = 'pc_spu_saler_combine';
                     $where = "WHERE is_delete=0 and spu='$combineSpu'";
                     $dataArr = array();
                     $dataArr['salerId'] = $salerId;
                     OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
                     echo "$combineSpu 的原销售是 陈智兴，真实销售是 $saler <br />";
                   }else{
                     echo "$combineSpu 的原销售是 陈智兴，真实销售为空 <br />";
                   }
                }
             }
         }
       }
    }

    /**
	 *功能：我自己运行的接口，自由发送mq队列
	 * */
	public function act_pcForMQByZqt(){
	   $tName = 'pc_goods';
       $where = "WHERE is_delete=0 and id in(87276,87277,87278,87279)";
       OmAvailableModel::deleteTNameRow($tName, $where);
    }



}
?>
