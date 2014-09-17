<?php
/*
 * 名称：OmEUBTrackNumberModel
 * 功能：订单记录，包括各个操作记录和审核记录
 * 版本：v 1.0
 * 日期：2013/12/20
 * 作者：Herman.xi
 * */
class OmEUBTrackNumberModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	'';
	public	static $Table	=	'om_order_tracknumber';
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function applyTheLineEUBTrackNumber($recordnumber,$tracknumber){
		!self::$dbConn ? self::initDB() : null;
		$SYSTEM_ACCOUNTS = OmAvailableModel::getPlatformAccount();
		$accountIds =array();
		foreach($SYSTEM_ACCOUNTS['Amazon'] as $id=>$account){
			$accountIds[] = $id;
		}
		//print_r($SYSTEM_ACCOUNTS['Amazon']);
		$tableName = 'om_unshipped_order';
		$where = " WHERE recordNumber='{$recordnumber}' AND accountId in ('".join("','", $accountIds)."') AND is_delete =0 AND storeId=1 ";
		$tinfo = OrderindexModel::showOnlyOrderList($tableName, $where);
		self :: $errMsg = '';
		if($tinfo){
			$omOrderId = $tinfo[0]['id'];
			$trackinfo = OrderindexModel::selectOrderTracknumber(" WHERE tracknumber = '".$tracknumber."' AND is_delete = 0 ");
			//var_dump($trackinfo);
			if($trackinfo){
				self :: $errCode = "001";
				self :: $errMsg =  "  第".$row."行已经存在跟踪".$tinfo['ebay_tracknumber']." 新跟踪号[$tracknumber]更新失败<br>";
				return false;
			}else{
				$data['omOrderId'] = $omOrderId;
				$data['tracknumber'] = $tracknumber;
				$data['addUser'] = $_SESSION['sysUserId'];
				$data['createdTime'] = time();
				$msg = OrderRecordModel::insertOrderTrackRow($data);
				//echo $msg;
				if(!$msg){
					self :: $errCode = "001";
					self :: $errMsg =  "   第".$row."行订单号[$recordnumber]添加跟踪号[$tracknumber]失败<br>";
					return false;
				}
			}
			self :: $errCode = "200";
			self :: $errMsg = "获取数据成功";
			return true; //失败则设置错误码和错误信息， 返回false
		}else{
			self :: $errCode = "001";
			self :: $errMsg  =  "   第".$row."行订单号[$recordnumber]不是亚马孙订单或者不存在系统<br>";
			return false;
		}
	}
	public static function getOrderDetailIds($omOrderId){
		self::initDB();
		//$string = array2sql($data);
		$sql = "SELECT * FROM `om_unshipped_order_detail` WHERE omOrderId={$omOrderId}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"update error";
			return false;
		}
	}
	
	/**
	 * 新EUB跟踪号报表导出的sql
	 */
	public static function getEubTruckNumberReport() {
		self::initDB();
		$sql = 'SELECT uo.`id`,oot.`tracknumber`,oot.`createdTime`, uou.`email`,uo.`recordNumber`,oa.`account`
				FROM `om_order_tracknumber` AS oot 
				LEFT JOIN `om_unshipped_order` AS uo ON oot.`omOrderId` = uo.`id` 
				LEFT JOIN `om_unshipped_order_userInfo` AS uou ON oot.`omOrderId` = uou.`omOrderId`
				LEFT JOIN `om_account` AS oa ON oot.`addUser` = oa.`id`';
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"update error";
			return false;
		}
	}
}
?>