<?php
/**
 *类名：SkuStatisticsAct
 *功能：sku统计数据接口
 *版本：2014-09-01
 *作者：杨世辉
 */
class SkuStatisticsAct {

	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;

	public function __construct(){
		self::$debug = C('IS_DEBUG');
	}

	/**
	 * 获取统计数据
	 */
	public function act_getStatisticsInfo() {
		global $dbconn;
		$skuArr = isset($_REQUEST['skuArr']) ? json_decode($_REQUEST['skuArr'], TRUE) : '';
		if (empty($skuArr)) {
			self::$errCode = '001';
			self::$errMsg  = 'skuArr param error';
			return array();
		}
		$fields = implode(',', array('sku','purchaseDays','alertDays'));
		$where = 'WHERE sku IN (\''. implode("','", $skuArr) .'\')';
		$result = SkuStatisticsModel::getInfo($fields, $where);
		return $this->_checkReturnData($result, array());
	}

	/**
	 * 获取可用库存及天数
	 */
	public function act_getStockDays() {
		global $dbconn;
		$skuArr = isset($_REQUEST['skuArr']) ? json_decode($_REQUEST['skuArr'], TRUE) : '';
		if (empty($skuArr)) {
			self::$errCode = '001';
			self::$errMsg  = 'skuArr param error';
			return array();
		}
		$fields = implode(',', array('sku','everyday_sale','stock_qty','ow_stock','salensend'));
		$where = 'WHERE sku IN (\''. implode("','", $skuArr) .'\')';
		$szdata = SkuStatisticsModel::getInfo($fields, $where);
		$szskuarr = empty($szdata) ? array() : array_keys($szdata);

		$fields = implode(',', array('sku','everyday_sale','virtual_stock'));
		$where = 'WHERE sku IN (\''. implode("','", $skuArr) .'\')';
		$owdata = SkuStatisticsModel::getOwInfo($fields, $where);
		$owskuarr = empty($owdata) ? array() : array_keys($owdata);

		$res = array();
		$skuarray = array_unique(array_merge($szskuarr, $owskuarr));
		if (!empty($skuarray)) {
			foreach ($skuarray as $sku) {
				$sz_stock = $ow_stock = 0;
				$sz_days = $ow_days = 0;
				if (!empty($szdata[$sku]) ) {
					$sz_stock = $szdata[$sku]['stock_qty'] + $szdata[$sku]['ow_stock'] - $szdata[$sku]['salensend'];
					$sz_days  = $szdata[$sku]['everyday_sale'] == 0 ? 0 : round_num($sz_stock / $szdata[$sku]['everyday_sale'], 2);
				}
				if (!empty($owdata[$sku])) {
					$ow_stock = $owdata[$sku]['virtual_stock'];
					$ow_days  = $owdata[$sku]['everyday_sale'] == 0 ? 0 : round_num($ow_stock / $owdata[$sku]['everyday_sale'],2);
				}
				$res[$sku]['sz_stock'] = $sz_stock;
				$res[$sku]['sz_days'] = $sz_days;
				$res[$sku]['ow_stock'] = $ow_stock;
				$res[$sku]['ow_days'] = $ow_days;
			}
		}
		return $this->_checkReturnData($res, array());
	}

	private function _checkReturnData($data, $errreturn){
		if ($data===FALSE){
			self::$errCode = SkuStatisticsModel::$errCode;
			self::$errMsg  = SkuStatisticsModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = '002';
			self::$errMsg  = 'There is no data!';
			if (self::$debug===TRUE){
				self::$errMsg .= 'The SQL is '.SkuStatisticsModel::$errMsg;
			}
			return $errreturn;
		}else {
			self::$errCode = '200';
			self::$errMsg  = 'success';
			return $data;
		}
	}

}