<?php
/**
 * 类名：TrackShipFeeModel
 * 功能：运德物流运费查询数据（CRUD）层
 * 版本：1.0
 * 日期：2014/07/26
 * 作者：管拥军
 */
 
class TrackShipFeeModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	
	/**
	 * TrackShipFeeModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackShipFeeModel::calcOpenShipFee()
	 * 获取物流系统开发运费计算结果
	 * @param string $addId 发货地址ID
	 * @param string $country 国家
	 * @param string $weight 重量
	 * @return  array 
	 */
	public static function calcOpenShipFee($addId, $country, $weight, $transitId, $postCode, $apiToken, $noShipId, $weightFlag){
		$res 	  			= array();
		$paramArr 			= array(
			'method' 		=> 'trans.open.ship.fee.get',
			'format' 		=> 'json',
			'v' 			=> '1.0',
			'username'	 	=> C('OPEN_SYS_USER'),
			'shipAddId'		=> $addId,
			'country'		=> $country,
			'weight'		=> $weight,
			'apiToken'		=> $apiToken,
			'transitId'		=> $transitId,
			'postCode'		=> $postCode,
			'noShipId'		=> $noShipId,
			'weightFlag'	=> $weightFlag,
		);
		$shipFeeInfo		= callOpenSystem($paramArr);
		$shipFeeInfo		= json_decode($shipFeeInfo,true);
		if(empty($shipFeeInfo['data'])) {
			self::$errCode  = 20000;
			self::$errMsg   = "Not to find the open freight, please confirm the selected conditions of this!";
			return false;			
		} else {
			$res			= $shipFeeInfo['data'];
		}
		unset($paramArr);
		return $res;
	}
}
?>