<?php
/**
 * 类名：PaymentRecordModel
 * 功能：采购订单财务付款记录（CRUD）层
 * 版本：1.0
 * 日期：2013/8/14
 * 作者：管拥军
 */
 
class PaymentRecordModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $detailtab	= "company";
	private static $showtab		= "pay_order";
	private static $ordertab	= "order";
	//private static $usertab		= "user";
	private static $usertab		="power_global_user";
		
	/**
	 * PaymentRecordModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * PaymentRecordModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql = "SELECT
				d.global_user_login_name as username,
				c.recordnumber,
				b.company,
				a.id,
				a.order_id,
				a.paymethod,
				a.purchaseuser_id,
				a.record_num,
				a.operator_id,
				a.pay_time,
				a.note,
				a.company_id
				FROM
				".self::$prefix.self::$showtab." AS a
				LEFT JOIN ".self::$prefix.self::$ordertab." AS c ON a.order_id = c.id
				LEFT JOIN ".self::$usertab." AS d ON a.purchaseuser_id = d.global_user_id 
				LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.company_id = b.id WHERE $where ORDER BY a.id ASC LIMIT $start,$pagenum";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * PaymentRecordModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*) FROM
				".self::$prefix.self::$showtab." AS a
				LEFT JOIN ".self::$prefix.self::$ordertab." AS c ON a.order_id = c.id
				LEFT JOIN ".self::$usertab." AS d ON a.purchaseuser_id = d.global_user_id 
				LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.company_id =b.id WHERE $where";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql))
		{
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * PaymentRecordModel::modUserDetail()
	 * 列出某个用户名
	 * @param integer $id 用户ID
	 * @return string 用户名
	 */
	public static function modUserDetail($id){
		self::initDB();
		$sql		= "SELECT global_user_id as id,global_user_login_name as username FROM ".self::$usertab." WHERE global_user_id = '{$id}' LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret[0]['username'];
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
}
?>