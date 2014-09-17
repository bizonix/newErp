<?php
class RtnPurchaseErpApiModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	/**
	 *功能:获取qc不良品列表数据
	 *@param $purid 采购
	 *@param $where 条件
	 *@param $page  页数
	 *@return 成功返回：总记录数 数据集;
	 *日期:2013/08/11
	 *作者:王民伟
	 */
	public static function getBadGoodList($purid, $where, $page) {
		self::initDB();
		$sqldata 	= "SELECT id, infoId, spu, sku, defectiveNum, processedNum, defectiveStatus, note, sellerId, startTime, lastModified, auditTime FROM qc_sample_defective_products ".$where;
		$sqlrow  	= "SELECT id FROM qc_sample_defective_products ".$where;
		$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		$limit      = " limit ".($pageindex - 1)*$pagesize.",$pagesize";
		$sqldata    = $sqldata.' ORDER BY id DESC '.$limit;
		$querydata 	= self::$dbConn->query($sqldata);
		$queryrow   = self::$dbConn->query($sqlrow);
		if($querydata){
			$rtn_data = self::$dbConn->fetch_array_all($querydata);
			if(!empty($rtn_data)){
				if($queryrow){
					$totalrows = self::$dbConn->num_rows($queryrow);//总记录数
				}
				$datalist[0] = $totalrows;
				$datalist[1] = $rtn_data;
				return $datalist;//返回总记录数、数据集
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 *功能:获取qc待定列表数据
	 *@param $purid 采购
	 *@param $where 条件
	 *@param $page  页数
	 *@return 成功返回：总记录数 数据集;
	 *日期:2013/08/11
	 *作者:王民伟
	 */
	public static function getPendGoodList($purid, $where, $page) {
		self::initDB();
		$sqldata 	= "SELECT id, infoId, spu, sku, pendingNum, pendingStatus, note, sellerId, lastModified, processedNum, startTime FROM qc_sample_pending_products ".$where;
		$sqlrow  	= "SELECT id FROM qc_sample_pending_products ".$where;
		$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		//$limit      = " limit ".($pageindex - 1)*$pagesize.",$pagesize";
		$limit 		= "";
		$sqldata    = $sqldata.' ORDER BY id DESC '.$limit;
		//echo $sqldata;
		$querydata 	= self::$dbConn->query($sqldata);
		
		$queryrow   = self::$dbConn->query($sqlrow);
		if($querydata){
			$rtn_data = self::$dbConn->fetch_array_all($querydata);
			if(!empty($rtn_data)){
				if($queryrow){
					$totalrows = self::$dbConn->num_rows($queryrow);//总记录数
				}
				$datalist[0] = $totalrows;
				$datalist[1] = $rtn_data;
				return $datalist;//返回总记录数、数据集
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 *功能:获取qc退货列表数据
	 *@param $purid 采购
	 *@param $where 条件
	 *@param $page  页数
	 *@return 成功返回：总记录数 数据集;
	 *日期:2013/08/11
	 *作者:王民伟
	 */
	public static function getReturnGoodList($purid, $where, $page) {
		self::initDB();
		$sqldata 	= "SELECT id, infoId, sku, returnNum, processedNum, returnStatus, note, sellerId, startTime, lastModified, auditTime FROM qc_sample_return_products ".$where;
		$sqlrow  	= "SELECT id FROM qc_sample_return_products ".$where;
		$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		//$limit      = " limit ".($pageindex - 1)*$pagesize.",$pagesize";
		$limit		= "";
		$sqldata    = $sqldata.' ORDER BY id DESC '.$limit;
		//echo $sqldata;
		$querydata 	= self::$dbConn->query($sqldata);
		
		$queryrow   = self::$dbConn->query($sqlrow);
		if($querydata){
			$rtn_data = self::$dbConn->fetch_array_all($querydata);
			if(!empty($rtn_data)){
				if($queryrow){
					$totalrows = self::$dbConn->num_rows($queryrow);//总记录数
				}
				$datalist[0] = $totalrows;
				$datalist[1] = $rtn_data;
				return $datalist;//返回总记录数、数据集
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
?>