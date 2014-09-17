<?php
/**
 * 单个产品信息和组合料号信息
 * add by heman.xi @30131111
 */

class GoodsModel{
	public static $dbConn;
	private static $_instance;
	public static $materInfo;
	public static $errCode;
	public static $errMsg;
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	/*
     * 获取单个sku信息
	 *@para $sku as string
     */
	public static function getSkuinfoAPI($sku){
        require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getGoodsInfoBySku',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'sku'		=> $sku,  //All
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr , $url);
		
		$data = json_decode($result,true);
		return $data['data'];
    }
	
	/*
     * 通过API获取组合sku信息
	 *@para $sku as string
     */
	public static function getCombineSkuinfoAPI($sku){
        require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getGoodsInfoBySku',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'sku'		=> $sku,  //All
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr , $url);
		
		$data = json_decode($result,true);
		return $data['data'];
    }
	
	/*
     * 获取单个sku信息
	 *@para $sku as string
     */
	public static function getSkuList($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `pc_goods` WHERE sku = '{$sku}' and is_delete = 0 ";
		$query	         =	self::$dbConn->query($sql);	
		$goodsInfo     =	self::$dbConn->fetch_array($query);
		/*if(!$goodsInfo){
			return self::getSkuinfoAPI($sku);
		}*/
        return $goodsInfo;
    }
	
	/*
     * 根据sku获取完全信息
	 *@para $sku as string
     */
	public static function getCompleteSkuinfo($sku){
		self::initDB();
		$virtualSku = array();
		$skuInformation	=	self::getCombineSkuinfo($sku);
		//var_dump($skuInformation);
		if(isset($skuInformation['detail'])){
			foreach($skuInformation['detail'] as $key => $val1){
				$_skuinfo = self::getSkuinfo($val1['sku']);
				if($_skuinfo){
					$virtualSku[$val1['sku']] = $val1 + $_skuinfo;
				}else{
					$virtualSku[$val1['sku']] = $val1;	
				}
			}
		}else{
			return false;	
		}
        return $virtualSku;
    }
	
	/*
     * 获取单个sku信息2
	 *@para $sku as string
     */
	public static function getSkuinfo($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `pc_goods` WHERE sku = '{$sku}' and is_delete = 0 ";
		$query	         =	self::$dbConn->query($sql);
		$goodsInfo     =	self::$dbConn->fetch_array($query);
		if(!$goodsInfo){
			return false;
		}
		$goodsInfo['purchaseName'] = UserModel::getUsernameById($goodsInfo['purchaseId']);
		//echo $sku; echo "<br>";
		$autoStock = WarehouseAPIModel::getSkuStock($sku);
		if(!$autoStock){
			$autoStock = 0;	
		}
		$goodsInfo['enableCount'] = $autoStock;
		//var_dump($goodsInfo);
        return $goodsInfo;
    }
	
	/*
     * 通过单sku获取采购名称信息
	 *@para $sku as string
     */
	public static function getPurchaseInfoBySku($sku){
		self::initDB();
		$sql             = "SELECT purchaseId FROM  `pc_goods` WHERE sku = '{$sku}' and is_delete = 0 ";
		$query	         =	self::$dbConn->query($sql);
		$goodsInfo     =	self::$dbConn->fetch_array($query);
		if(empty($goodsInfo)){
			return false;
		}
        return UserModel::getUsernameById($goodsInfo['purchaseId']);
    }
	
	/*
     * 获取单个sku下SPU的信息
	 *@para $spu as string
     */
	public static function getSpuBySku($sku){
		self::initDB();
		$sql             = "SELECT spu FROM  `pc_goods` WHERE sku = '{$sku}' and is_delete = 0 ";
		$query	         =	self::$dbConn->query($sql);
		$goodsInfo     =	self::$dbConn->fetch_array($query);
		if($goodsInfo){
			return $goodsInfo['spu'];
		}
        return false;
    }
	
	public static function getSkuinfoByPurchaseId($sku, $purchaseId){
		self::initDB();
		$where = " sku = '{$sku}' and is_delete = 0 ";
		if(!empty($purchaseId)){
			$where .= " and purchaseId = '{$purchaseId}' ";
		}
		$sql="SELECT * FROM `pc_goods` WHERE {$where} ";
		$query=self::$dbConn->query($sql);
		$goodsInfo=self::$dbConn->fetch_array($query);
		return $goodsInfo;
    }
	
	/*
     * 获取sku组合料号信息
	 *@para $sku as string
     */
	 
	 public static function getCombineSkuinfo($sku){
        /*global $memc_obj;
        $skuInfo = $memc_obj->get_extral('pc_goods_combine_'.$sku);*/
		self::initDB();
		$sql             = "SELECT sku, count FROM  `pc_sku_combine_relation` WHERE combineSku = '{$sku}' ";
		$query	         =	self::$dbConn->query($sql);	
		$combineInfo     =	self::$dbConn->fetch_array_all($query);
		if(empty($combineInfo)){
			return false;
		}
        return array('detail'=>$combineInfo);
    }
	
	/*
     * 通过单料号SKU获取包含的组合料号信息
	 *@para combineSKU as string
     */
	
	public static function getCombineBySku($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `pc_sku_combine_relation` WHERE sku = '{$sku}' ";
		$query	         =	self::$dbConn->query($sql);
		$combineInfo     =	self::$dbConn->fetch_array_all($query);
		if(empty($combineInfo)){
			return array();
		}
		$combineskus = array();
		foreach($combineInfo as $value){
			$combineskus[$value['combineSku']] = intval($value['count']);
		}
        return $combineskus;
	}
	
	/*
     * 通过单料号SKU获取包含的组合料号信息,包含单料号信息
	 *@para combineSKU as string
     */
	
	public static function getCombineANDSKU($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `pc_sku_combine_relation` WHERE sku = '{$sku}' ";
		$query	         =	self::$dbConn->query($sql);
		$combineInfo     =	self::$dbConn->fetch_array_all($query);
		$combineskus = array();
		if(empty($combineInfo)){
			$combineskus[$sku] = 1;
			return $combineskus;
		}
		foreach($combineInfo as $value){
			$combineskus[$value['combineSku']] = intval($value['count']);
		}
		$combineskus[$sku] = 1;
        return $combineskus;
	}
	
	/*
     * 获取包材信息
     */
	public static function getMaterInfo(){
        global $memc_obj;
		$cacheName = md5("om_pmInfo");
		$list = $memc_obj->get_extral($cacheName);
		
		if($list){
			return $list;
		}else{
			require_once WEB_PATH."api/include/functions.php";
			$url	= 'http://gw.open.valsun.cn:88/router/rest?';
			$paramArr= array(
				/* API系统级输入参数 Start */
				'method'	=> 'pc.getPmInfoAll',  //API名称
				'format'	=> 'json',  //返回格式
				'v'			=> '1.0',   //API版本号
				'username'	=> 'purchase',
				/* API系统级参数 End */				 
	
				/* API应用级输入参数 Start*/
				/* API应用级输入参数 End*/
			);
			$result = callOpenSystem($paramArr);
			$data = json_decode($result,true);
			if(!$data['data']){
				self::$errCode = 101;
				self::$errMsg = "没取到值！";
				return false;
			}else{
				$isok = $memc_obj->set_extral($cacheName, $data['data']);
				if(!$isok){
					self::$errCode = 102;
					self::$errMsg = 'memcache缓存账号信息出错!';
					return $data['data'];
				}
				return $data['data'];
			}
		}
    }
	
	/*
     * 获取单个sku分类信息
	 *@para $path as string
     */
	public static function getCategoryInfoByPath($path){
        require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getCategoryInfoByPath',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'path'		=> $path,  //All
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		return $data['data'];
    }
	
	
	/*
     * 获取包材信息按照id_value来分布
     */
	public static function getMaterInfoByList(){
        $data = self::getMaterInfo();
		$arr = array();
		foreach($data as $v){
			$arr[$v['id']] = $v['pmName'];	
		}
		return $arr;
    }
	
	/*
     * 获取包材信息按照id_value来分布
     */
	public static function getMaterInfoByIdList(){
        $data = self::getMaterInfo();
		$arr = array();
		foreach($data as $v){
			$arr[$v['id']] = $v;	
		}
		return $arr;
    }
	
	/*
     * 通过包材id获取包材名称
     */
    public static function getMaterInfoById($MaterId){
		
        /*$materInfoList = self::getMaterInfo();
        return  $materInfoList[$MaterId];*/
		require_once WEB_PATH."api/include/functions.php";
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getPmInfoById',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'id'        => $MaterId
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		$data = json_decode($result,true);
		return $data['data'];
    }
	
	/*public static function get_realskuinfo($sku){
		//获取料号下详细信息
		$combinelists = self::getCombineSkuinfo($sku);
		//var_dump($combinelists); echo "<br>";
		if (empty($combinelists['sku'])){ //modified by Herman.Xi @ 2013-05-22
			$sku = self::getConversionSku($sku);
			//var_dump($sku);
			return array($sku=>1);
		}
		$results = array();
		if (strpos($combinelists['goods_sncombine'], ',')!==false){
			$skulists = explode(',', $combinelists['goods_sncombine']);
			foreach ($skulists AS $skulist){
				list($_sku, $snum) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array($skulist, 1);
				$_sku = self::getConversionSku($_sku);
				$results[trim($_sku)] = $snum;
			}
		}else if (strpos($combinelists['goods_sncombine'], '*')!==false){
			list($_sku, $snum) = explode('*', $combinelists['goods_sncombine']);
			$_sku = self::getConversionSku($_sku);
			$results[trim($_sku)] = $snum;
		}else{
			$sku = self::getConversionSku($sku);
			$results[trim($sku)] = 1;
		}
		return $results;
	}*/
	
	public static function get_realskuinfo($sku){
		//获取料号下详细信息
		$combinelists = self::getCombineSkuinfo($sku);
		//var_dump($combinelists); echo "<br>";
		if (!$combinelists){ //modified by Herman.Xi @ 2013-05-22
			$sku = self::getConversionSku($sku);
			//var_dump($sku);
			return array($sku=>1);
		}
		$results = array();
		foreach($combinelists['detail'] as $combinelist){
			$_sku = $combinelist['sku'];
			$snum = $combinelist['count'];
			//list($_sku, $snum) = $combinelist;
			$_sku = self::getConversionSku($_sku);
			if(isset($results[trim($_sku)])){
				$results[trim($_sku)] += $snum;
			}else{
				$results[trim($_sku)] = $snum;		
			}
		}
		return $results;
	}
	
	public static function getConversionSku($sku){
		/*add by Herman.Xi @ 2013-06-04
		新旧料号转换问题解决*/
		//require_once WEB_PATH."api/include/functions.php";
//		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
//		$paramArr= array(
//			/* API系统级输入参数 Start */
//			'method'	=> 'pc.showNewSku',  //API名称
//			'format'	=> 'json',  //返回格式
//			'v'			=> '1.0',   //API版本号
//			'username'	=> 'purchase',
//			/* API系统级参数 End */				 
//
//			/* API应用级输入参数 Start*/
//			'old_sku' => $sku
//			/* API应用级输入参数 End*/
//		);
//		$result = callOpenSystem($paramArr);
//		$data = json_decode($result,true);
//		if(empty($data['data'])){
//			return $sku;	
//		}
//		return $data['data'];
		self::initDB();
		$sku 			 = trim($sku);
		$sql             = "SELECT * FROM  `pc_sku_conversion` WHERE old_sku = '{$sku}' AND is_delete = 0 ";
		$query	         =	self::$dbConn->query($sql);
		$combineInfo     =	self::$dbConn->fetch_array_all($query);
		if(empty($combineInfo)){
			return $sku;
		}else if(count($combineInfo) > 1){
			return $sku;//如果错误的写入多条也只能返回原sku
		}else{
			return trim($combineInfo[0]['new_sku']);
		}
	}
	
}

