<?php
/**
 * 类名：SuperOrderAuditModel
 * 功能：超大订单审核
 * 版本：2014-09-01
 * 作者：杨世辉
*/
class SuperorderAuditModel {

	public static $dbConn;
	private static $table_name='ph_superorder_audit';//超大订单明细表
	static $errCode = '0';
	static $errMsg  = "";

	/**
	 * 初始化数据库连接
	 */
	public static function initDB() {
		global $dbConn;
		self::$dbConn =	$dbConn;
	}

	/**
	 * 方法功能：获取一条记录
	 * @param string $filed
	 * @param string $where
	 * @return boolean|array
	 */
	public static function getOne($filed=' * ',$where = ' 1 ') {
		self::initDB();
		$sql='SELECT '.$filed.' FROM `'.self::$table_name.'` WHERE '. $where;
		$result = self::$dbConn->query($sql);
		if (empty($result)) {
			self::$errCode = '001';
			self::$errMsg  = "SuperorderAuditModel getOne error";
			return false;
		}
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}

	/**
	 * 方法功能：获取列表
	 * @param string $filed
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @return boolean|array
	 */
	public static function getList($filed=' * ',$where = ' 1 ', $order = '', $limit = '') {
		self::initDB();
		$sql='SELECT '.$filed.' FROM `'.self::$table_name.'` WHERE '. $where .' '. $order .' '. $limit;
		$result = self::$dbConn->query($sql);
		if (empty($result)) {
			self::$errCode = '002';
			self::$errMsg  = "SuperorderAuditModel getList error";
			return false;
		}
		$data_result = array();
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    	$data_result[] = $row;
    	}
		return $data_result;
	}

	/**
	 * 方法功能：新增
	 * 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	 * 其中key是要插入的表的字段名称,value是要给字段赋的值；
	 * @param array $data_array
	 * @return boolean
	 */
	public static function add($data_array) {
		self::initDB();
		if(empty($data_array) || !is_array($data_array)) { //判断是否是数组
			self::$errCode = '002';
			self::$errMsg  = "SuperorderAuditModel add error1";
			return false;
		}
		$field = '';//要插入的字段
		$values = '';//要插入的值
		foreach($data_array as $key=>$v) {
			$field .=' `'.$key.'`,';//把数组内容转换为字符串格式，例如：`sku`='23423',`amount`='10'
			$values .=' \''.$v.'\',';
		}
		$field = rtrim($field,',');//去除最后一个逗号
		$values = rtrim($values,',');//去除最后一个逗号
		$sql = 'INSERT INTO `'.self::$table_name.'`('.$field.')  VALUES('.$values.')';
		//echo $sql."\n";
		$result = self::$dbConn->query($sql);
		if (empty($result)) {
			self::$errCode = '003';
			self::$errMsg  = "SuperorderAuditModel add error2";
			return false;
		}
		return true;
	}

	/**
	 * 方法功能：修改
	 * 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	 * 其中key是要修改的表的字段名称,value是要给字段重新赋的值；$where参数是条件
	 * @param unknown $data_array
	 * @param unknown $where
	 * @return boolean
	 */
	public static function update($data_array, $where) {
		self::initDB();
		if(empty($data_array) || !is_array($data_array)) { //判断是否是数组
			self::$errCode = '004';
			self::$errMsg  = "SuperorderAuditModel update error1";
			return false;
		}
		$setting = '';
		foreach($data_array as $key=>$value) {
			$setting .= ',`'.$key.'` = \''.$value.'\''; //把数组内容转换为字符串格式，例如：`dept_name`='it',`dept_principal`='admin'
		}
		$setting = ltrim($setting,',');//去除最左边的逗号
		$sql = 'UPDATE `' . self::$table_name . '` SET '.$setting . ' WHERE ' . $where;
		//echo $sql."\n";
		return self::$dbConn->query($sql);
	}

}

