<?php
/**
 * 类名：StockInvoiceModel
 * 功能：备货单(CRUD)数据层
 * 版本：1.0
 * 日期：2013/8/9
 * 作者：管拥军
 */
 class StockInvoiceModel{
	public static $dbConn;
	public static $prefix;
	public static $errCode		= 0;
	public static $errMsg		= "";
	private static $detailtab	= "stock_invoice_detail";
	private static $showtab		= "stock_invoice";
	
	/**
	 * StockInvoiceModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * StockInvoiceModel::modList()
	 * 列出符合条件的备货单并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$flag	= strpos($where,"sku");
		if($flag !== false){
			$sql	= "SELECT a.* FROM ".self::$prefix.self::$showtab." AS a LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.ordersn = b.ordersn WHERE $where AND a.is_delete = 0 GROUP BY a.id ORDER BY a.id DESC LIMIT $start,$pagenum";
		}else {
			$sql	= "SELECT * FROM ".self::$prefix.self::$showtab." WHERE $where AND is_delete = 0 ORDER BY id DESC LIMIT $start,$pagenum";
		}
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * StockInvoiceModel::modDetailList()
	 * 列出某个备货单的详情
	 * @param string $where 查询条件
	 * @return array 结果集数组
	 */
	public static function modDetailList($where){
		self::initDB();
		$sql	=	"SELECT * FROM ".self::$prefix.self::$detailtab." WHERE $where";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * StockInvoiceModel::modDetailStock()
	 * 列出某个备货单摘要信息
	 * @param string $where 查询条件
	 * @return array 结果集数组
	 */
	public static function modDetailStock($where){
		self::initDB();
		$sql	=	"SELECT * FROM ".self::$prefix.self::$showtab." WHERE $where";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * StockInvoiceModel::modListCount()
	 * 列出符合条件的数据总条数
	 * @param string $where
	 * @return integer 数据总条数
	 */
	public static function modListCount($where){
		self::initDB();
		$flag	= strpos($where,"sku");
		if($flag !== false){
			$sql	= "SELECT count(*) FROM ".self::$prefix.self::$showtab." AS a LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.ordersn = b.ordersn WHERE $where AND a.is_delete = 0";
		}else {
			$sql	= "SELECT count(*) FROM ".self::$prefix.self::$showtab." WHERE $where  AND is_delete = 0";
		}
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql))
		{
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * StockInvoiceModel::modSumCount()
	 * 统计某个备货单的总成本
	 * @param string $where
	 * @return float 总成本
	 */
	public static function modSumCount($where){
		self::initDB();
		$sql	= "SELECT SUM(cost*count) as totalcost FROM ".self::$prefix.self::$detailtab." WHERE $where";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql))
		{
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
    /**
     * StockInvoiceModel::audit()
	 * 批量审核一个或多个备货单
	 * @return bool
	 */
    public static function audit($idArr){
		self::initDB();
        if(!is_array($idArr)){
            self::$errCode	= "0001";
			self::$errMsg	= "参数传递非法";
			return false;
        }
        $ids    = implode("','",$idArr);
		$ids	= "'".$ids."'";
		$sql	= "UPDATE ".self::$prefix.self::$showtab." SET audituser = 'vipchen',status = '2',audittime = '".time()."' WHERE ordersn IN($ids)";
		$query	= self::$dbConn->query($sql);
		if($query)
		{
			return true;
		}else {
            self::$errCode	= "0002";
			self::$errMsg	= "批量审核失败";
			return false;
		}
	}

	/**
	 * StockInvoiceModel::del()
	 * 批量删除一个或多个备货单
	 * @return bool
	 */
	public static function del($idArr){
		self::initDB();
        if(!is_array($idArr)){
            self::$errCode	= "0001";
			self::$errMsg	= "参数传递非法";
			return false;
        }
        $ids    = implode("','",$idArr);
		$ids	= "'".$ids."'";
		$sql	= "UPDATE ".self::$prefix.self::$showtab." SET is_delete = '1' WHERE ordersn IN($ids)";
		$query	= self::$dbConn->query($sql);
		if($query)
		{
			return true;
		}else {
            self::$errCode	= "0002";
			self::$errMsg	= "批量删除失败";
			return false;
		}
	}
	
	/**
     * StockInvoiceModel::updateStock()
	 * 更新备货单摘要信息
	 * @return bool
	 */
    public static function updateStock($ordersn,$note){
		self::initDB();
		$note	= post_check($note);
		$sql	= "UPDATE ".self::$prefix.self::$showtab." SET note = '{$note}' WHERE ordersn = '{$ordersn}'";
		$query	= self::$dbConn->query($sql);
		if($query)
		{
			return true;
		}else {
            self::$errCode	= "0003";
			self::$errMsg	= "更新摘要信息失败";
			return false;
		}
	}
	
	/**
     * StockInvoiceModel::updateStockDetail()
	 * 批量更新备货单详细信息
	 * @return bool
	 */
    public static function updateStockDetail($data){
		self::initDB();
		$errormsg	= "";
		$detailstr	= "";
		$condition	= array();
		foreach($data as $v){
			$count	= $v['count'];
			$cost	= $v['cost'];
			$id		= $v['id'];
			if(!empty($count)){
				array_push($condition,"count='{$count}'");
			}
			if(!empty($cost)){
				array_push($condition,"cost='{$cost}'");
			}
			if(count($condition)==0){
				self::$errCode	= "0004";
				self::$errMsg	= "没有什么内容需要修改的";
				return false;
			}else {
				$detailstr		= implode(",",$condition);
			}			
			$sql = "UPDATE ".self::$prefix.self::$detailtab." SET $detailstr WHERE id={$id}";
			$query	= self::$dbConn->query($sql);
			if(!$query)
			{
				$errmsg	.= "更新备货单{$id}详细信息失败<br/>";
			}
		}
		if(empty($errormsg)){
			return true;
		}else {
			self::$errCode	= "0004";
			self::$errMsg	= $errmsg;
			return false;
		}
	}
}
?>