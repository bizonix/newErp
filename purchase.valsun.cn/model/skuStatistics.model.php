<?php
/**
 *类名：SkuStatisticsAct
 *功能：sku统计数据model
 *版本：2014-09-01
 *作者：杨世辉
 */
class SkuStatisticsModel {

	public static $dbConn;
	private static $table_name = 'ph_sku_statistics';//sku统计数据表
	private static $table_name2 = 'ow_stock';//海外仓库存统计表
	static $errCode = '0';
	static $errMsg  = "";

	public static function initDB() {
		global $dbConn;
		self::$dbConn = $dbConn;
	}

	/**
	 * 方法功能:获取统计数据
	 * @param string $fileds
	 * @param string $where
	 * @param string $orderby
	 * @param string $limit
	 * @return array
	 */
	public static function getInfo($fileds, $where, $order='', $limit='') {
		self::initDB();
		$sql = 'SELECT '. $fileds .' FROM `'.self::$table_name.'` '. $where .' '. $order .' '.$limit;
		$result = self::$dbConn->query($sql);
		if (empty($result)){
			self::$errCode = '001';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		$data_result = array();
		$fileds = explode(',', $fileds);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if (in_array('sku', $fileds)) {
				$sku = trim($row['sku']);
				unset($row['sku']);
				$data_result[$sku] = $row;
			} else {
				$data_result[] = $row;
			}
		}
		return $data_result;
	}

	/**
	 * 方法功能:获取海外仓统计数据
	 * @param string $fileds
	 * @param string $where
	 * @param string $orderby
	 * @param string $limit
	 * @return array
	 */
	public static function getOwInfo($fileds, $where, $order='', $limit='') {
		self::initDB();
		$sql = 'SELECT '. $fileds .' FROM `'.self::$table_name2.'` '. $where .' '. $order .' '.$limit;
		$result = self::$dbConn->query($sql);
		if (empty($result)){
			self::$errCode = '001';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		$data_result = array();
		$fileds = explode(',', $fileds);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if (in_array('sku', $fileds)) {
				$sku = trim($row['sku']);
				unset($row['sku']);
				$data_result[$sku] = $row;
			} else {
				$data_result[] = $row;
			}
		}
		return $data_result;
	}


}