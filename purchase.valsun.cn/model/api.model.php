<?php
/**
 *类名：ApiModel
 *功能：对外提供API数据
 *版本：2013-09-17
 *作者：温小彬
 */
class ApiModel{
	public static $dbConn;
	public static $prefix;
	public static $errCode = 0;
	public static $errMsg = "";
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
	}
	public static function getAdjustransport(){
		!self::$dbConn ? self::initDB() : null ;
		$sql = "SELECT id,category,skulist,original_transport,current_transport,creator,is_delete,createdtime,modifytime,is_show FROM ";
		$sql .=self::$prefix."adjust_transport  WHERE is_delete = '0'";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if($ret){
				return $ret;
				self::$errCode = "1";
				self::$errMsg = "获取特殊运输成功";
				exit;
			}
		}
		self::$errCode = "0";
		self::$errMsg = "获取特殊运输失败";
		return false;
		exit;
	}
	
	/**
	 * ApiModel::getAuthCompanyList()
	 * 获取鉴权公司列表
	 * @return  array
	 */
	public static function getAuthCompanyList(){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.getApiCompany.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
                'sysName' 	=> C('AUTH_SYSNAME'),
                'sysToken' 	=> C('AUTH_SYSTOKEN')

			/* API应用级输入参数 End*/
		);
		$companyInfo	= callOpenSystem($paramArr);
		$companyInfo	= json_decode($companyInfo, true);
		$companyInfo	= is_array($companyInfo) ? $companyInfo : array();
		unset($paramArr);
		return $companyInfo;
	}
	/**
	 * ApiModel::getSkuByPurids($purIds)
	 *通过采购id获取sku
	 * @return  array
	 */
	public static function getSkuByPurids($purIds){
		self::initDB();
		$sql = "SELECT sku FROM pc_goods WHERE purchaseId IN ({$purIds})";
		$query = self::$dbConn->query($sql);
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			if(empty($res)){
				self::$errMsg = "result empty ---{$sql}";
				return false;
			}
			return $res;
		}
		self::$errMsg = "query error ---{$sql}";
		return false;
	}

	//获取SKU库存及成本 add by wangminwei 2011.11.28
	public static function getQtyAndPriceBySku($sku){
		self::initDB();
		$sql   = " SELECT a.goodsCost, b.stock_qty FROM pc_goods AS a JOIN ph_sku_statistics AS b ON a.sku = b.sku WHERE a.sku = '{$sku}' AND a.is_delete = 0"; 
		$query = self::$dbConn->query($sql);
		if($query){
			$data = self::$dbConn->fetch_array_all($query);
			if(!empty($data)){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	
}

?>