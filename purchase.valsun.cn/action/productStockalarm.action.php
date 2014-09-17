<?php
  
require_once "/data/web/purchase.valsun.cn/action/skuAanalyze.action.php";
class ProductStockalarmAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * ProductStockalarmAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= ProductStockalarmModel::modList($where, $page, $pagenum);
        return $res;
    }

	/**
	 * ProductStockalarmAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= ProductStockalarmModel::modListCount($where);
		self::$errCode  = ProductStockalarmModel::$errCode;
        self::$errMsg   = ProductStockalarmModel::$errMsg;
        return $res;
    }

	/**
	 * ProductStockalarmAct::act_updateWarn()
	 * 更新选择料号的预警信息
	 * @param array skuArr 料号数组
	 * @return bool 
	 */
	public function act_updateWarn() {
		$sku	= isset($_POST['sku']) ? $_POST['sku'] : "";
		$gid	= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		if (empty($sku)) {
			self::$errCode  = 10000;
			self::$errMsg   = "料号参数非法！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 10002;
			self::$errMsg   = "您无权更新缓存！";
			return false;
		}
		$res			= ProductStockalarmModel::updateWarn($sku);
		self::$errCode  = ProductStockalarmModel::$errCode;
        self::$errMsg   = ProductStockalarmModel::$errMsg;
        return $res;
    }
	
	/**
	 * ProductStockalarmAct::act_updateWarnNew()
	 * 更新选择料号的预警信息
	 * @param array skuArr 料号数组
	 * @return bool 
	 */
	public function act_updateWarnNew() {
		$sku	= isset($_POST['sku']) ? $_POST['sku'] : "";
		$gid	= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		if (empty($sku)) {
			self::$errCode  = 10000;
			self::$errMsg   = "料号参数非法！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 10002;
			self::$errMsg   = "您无权更新缓存！";
			return false;
		}
		$res			= ProductStockalarmModel::updateWarnNew($sku);
		self::$errCode  = ProductStockalarmModel::$errCode;
        self::$errMsg   = ProductStockalarmModel::$errMsg;
        return $res;
    }
	
	/**
	 * ProductStockalarmAct::act_updateWarnOld()
	 * 更新选择料号的预警信息
	 * @param array skuArr 料号数组
	 * @return bool 
	 */
	public function act_updateWarnOld() {
		$skuArr		= isset($_POST['skuList']) ? $_POST['skuList'] : "";
		$gid		= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		if (empty($skuArr) || !is_array($skuArr)) {
			self::$errCode  = 10000;
			self::$errMsg   = "料号参数非法！";
			return false;
		}
		if (!count($skuArr)) {
			self::$errCode  = 10001;
			self::$errMsg   = "料号不能为空数组！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 10002;
			self::$errMsg   = "您无权更新缓存！";
			return false;
		}
		$res			= ProductStockalarmModel::updateWarn($gid, $skuArr);
		self::$errCode  = ProductStockalarmModel::$errCode;
        self::$errMsg   = ProductStockalarmModel::$errMsg;
        return $res;
    }	
	
}
?>
