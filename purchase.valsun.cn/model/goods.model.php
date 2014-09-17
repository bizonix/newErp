<?php
 
class GoodsModel{

	public static $dbConn;
	public static $prefix;
	public static $errCode		= 0;
	public static $errMsg		= "";
	private static $detailtab	= "sku_info_tmp";
	private static $showtab		= "goods";
	private static $warehous	= "store";
	private static $usertab		= "power_global_user";


	function initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}

	public function getSkuInfo($limit){
		self::initDB();
		$sql = "SELECT * FROM pc_goods  WHERE is_delete = 0 ";	
		if(isset($limit)){
			$sql .= $limit;
		}

		$query  = self::$dbConn->query($sql);
		$skuInfoArr = self::$dbConn->fetch_array_all($query);
		return $skuInfoArr;
	}

	public function getTotalNum(){
		self::initDB();
		$sql = "SELECT count(*) as total FROM pc_goods  WHERE is_delete = 0 ";	
		$query  = self::$dbConn->query($sql);
		$num = self::$dbConn->fetch_array($query);
		return $num["total"];
	}

	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$flag	= strpos($where,"skus"); 	
		if($flag===false){
            $field  = " id, sku, spu, goodsName, goodsCategory, goodsStatus, goodsCost ,goodsWeight, goods_unit, purchaseId, isNew, isPass ";
			$sql	= "SELECT $field FROM ".self::$prefix.self::$showtab." WHERE $where ORDER BY id ASC LIMIT $start,$pagenum";
		}else {
            $field  = " a.id, a.sku, a.spu, a.goodsName, a.goodsCategory, a.goodsStatus, a.goodsCost, a.goodsWeight, a.goods_unit, a.purchaseId, a.isNew, a.isPass ";
			$sql	= "SELECT $field FROM ".self::$prefix.self::$showtab." AS a LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.sku = b.sku WHERE $where GROUP BY a.id ORDER BY a.id ASC LIMIT $start,$pagenum";
		}
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
	 * GoodsModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$flag	= strpos($where,"skus");
		if($flag===false){
			$sql	= "SELECT count(*) FROM ".self::$prefix.self::$showtab." WHERE $where";
		}else {
			$sql	= "SELECT count(*) FROM ".self::$prefix.self::$showtab." AS a LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON a.sku = b.sku WHERE $where";
		}
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
	 * GoodsModel::modPurchaseList()
	 * 列出所有用户
	 * @param string $where 查询条件
	 * @return array 结果集数组
	 */
	public static function modPurchaseList($where){
		self::initDB();
		$sql		= "SELECT global_user_id as id,global_user_login_name as username FROM ".self::$usertab." WHERE $where";
		$query		= self::$dbConn->query($sql);
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
	 * GoodsModel::modPurchaseDetail()
	 * 列出某个用户
	 * @param integer $id 采购ID
	 * @return string 采购名
	 */
	public static function modPurchaseDetail($id){
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
	
    /**
	 * GoodsModel::auditSku()
	 * 批量审核SKU
	 * @param array $idArr ID数组
	 * @return  bool
	 */
    public static function auditSku($idArr){
		self::initDB();
        if(!is_array($idArr)){
            self::$errCode	= "0001";
			self::$errMsg	= "参数传递非法";
			return false;
        }
        $ids	= array();
		foreach($idArr as $v){
			$ids[]	= intval($v);
		}
		$ids	= implode(",",$ids);
		$sql	= "UPDATE ".self::$prefix.self::$showtab." SET auditerId = '66',isPass = '1',auditTime = '".time()."' WHERE id IN($ids)";
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
	 * GoodsModel::moveSku()
	 * 批量移交料号SKU
	 * @param array $idArr ID数组
	 * @return  bool
	 */
    public static function moveSku($idArr, $purchase){
		self::initDB();
        if(!is_array($idArr)){
            self::$errCode	= "0001";
			self::$errMsg	= "参数传递非法";
			return false;
        }
		$ids	= array();
		foreach($idArr as $v){
			$ids[]	= intval($v);
		}
		$ids	= implode(",",$ids);
		$sql	= "UPDATE ".self::$prefix.self::$showtab." SET purchaseId = '{$purchase}' WHERE id IN($ids)";
		$query	= self::$dbConn->query($sql);
		if($query)
		{
			return true;
		}else {
            self::$errCode	= "0002";
			self::$errMsg	= "批量移交失败";
			return false;
		}
	}
	/**
	 * 功能：获取货品资料信息
    * @param $where
    * @param $count 是否是返回记录条数
    * @return void 
	 * */
	function getGoodsList($where,$count=0,$limit='  '){
		self::initDB();
		if(!empty($where)){
			$where = " AND ".$where;  
		}
		$sql = '';
		if(empty($count)){
			$sql .= "SELECT g.id,g.sku,g.spu,g.goodsName, g.goodsWeight, g.goodsCost, g.goodsCategory, g.goodsStatus, g.isNew, g.purchaseId ";
		}else if ($count == 1){
			$sql .= "SELECT count(*) AS total  ";
		}
		$sql .= "  FROM  pc_goods as g";
		$sql .= "  WHERE g.is_delete = 0  ".$where."  ".$limit;
		$query = self::$dbConn->query($sql);
		if($query){
				$ret	= self::$dbConn->fetch_array_all($query);
				if(!empty($ret)){
					return $ret;
				}
				self::$errCode	= "0023";
				self::$errMsg	= "获取货品资料信息失败1";
				return false;
		}
            self::$errCode	= "0002";
			self::$errMsg	= "获取货品资料信息失败2";
			return false;
	}
	/**
	 * 功能：读取仓位表信息
	 * @param str $where
	 * @return void
	 */
	public 	static function warehousList($where){
		self::initDB();
		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		$sql	 =	"SELECT * FROM ".self::$prefix.self::$warehous." {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	/**
	 * 功能：获取库存总金额
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
	public static function getTotal(){
		self::initDB();
		return true;
		/*
		$sql = "SELECT (g.goodsCost*gd.stock_qty ) AS total FROM ".self::$prefix.self::$showtab." AS g LEFT JOIN ".self::$prefix.self::$detailtab." AS gd ";
		$sql .= "  ON  g.sku = gd.sku WHERE g.is_delete = 0 AND gd.is_delete = 0 ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self :: $errCode = "0258";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
		 */
	}
	/**
	 * 功能：通过名字获取采购员的id
	 * @param str $name
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
	public static function purchaseIdByName($name){
		self::initDB();
		$sql = "SELECT global_user_id FROM  ".self::$usertab;
		$sql .= "  WHERE global_user_is_delete =0 AND global_user_status = 1 AND  global_user_name = '{$name}'   LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret['global_user_id'];
		}else{
			self :: $errCode = "0278";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	/**
	 * 功能：通过名字获取供应商的id
	 * @param str $name
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
	public static function partnerIdByName($name){
		self::initDB();
		$sql = "SELECT id FROM ".self::$prefix."partner  WHERE is_delete = 0 AND username ='{$name}'   LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret['id'];
		}else{
			self :: $errCode = "0298";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	/**
	 * 功能：通过供应商的id获取名字
	 * @param str $id
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
	public static function partnerNameById($id){
		if(empty($id)){
			self :: $errCode = "0312";
			self :: $errMsg = "id empty";
			return false;
		}
		self::initDB();
		$sql = "SELECT username FROM ".self::$prefix."partner  WHERE is_delete = 0 AND  id = {$id}   LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			if(empty($ret)){
				self :: $errMsg = "mysql:".$sql." empty";
				return false;
			}
			return $ret['username'];
		}else{
			self :: $errCode = "0298";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	/**
	 * 功能：通过采购员的id获取名字
	 * @param str $id
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
	public static function purchaseNameById($id){
		if(empty($id)){
			self :: $errCode = "0312";
			self :: $errMsg = "id empty";
			return false;
		}
		self::initDB();
		$sql = "SELECT global_user_name  FROM ".self::$usertab;
		$sql .= "  WHERE global_user_is_delete =0 AND global_user_status = 1  AND  global_user_id = {$id}   LIMIT 1";
		$query	 =	 self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret['global_user_name'];
		}else{
			self :: $errCode = "0338";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/**
	 * 功能：通过 sku 获取 供应商名字
	 * @param str $sku
	 * @return void
	 * @author wxb
	 * @date 2013/11/13
	 */
	public static  function partnerIdBySku($sku){
		self::initDB();
		$sql = "SELECT partnerId  FROM ".self::$prefix."goods_partner_relation WHERE sku = '{$sku}' LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			if(empty($ret)){
				self :: $errMsg = "mysql:".$sql." empty";
				return false;
			}
			return $ret['partnerId'];
		}else{
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}	
	}
	/**
	 * 功能：通过 供应商id 获取sku 
	 * @param str $sku
	 * @return void
	 * @author wxb
	 * @date 2013/11/13
	 */
	public static  function skuByParId($sku){
		self::initDB();
		$sql = "SELECT sku FROM ".self::$prefix."goods_partner_relation WHERE partnerId = {$sku} LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			if(empty($ret)){
				self :: $errMsg = "mysql:".$sql." empty";
				return false;
			}
			return $ret['sku'];
		}else{
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	/**
	 * 功能：通过sku获取spu
	 * @param str $sku
	 * @return void
	 * @author wxb
	 * @date 2013/11/15
	 */
	public static function getSpuBySku($sku){
		self::initDB();
		$sql = "SELECT spu FROM ".self::$prefix."goods WHERE sku = '{$sku}' LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			if(empty($ret)){
				self :: $errMsg = "mysql:".$sql." empty";
				return false;
			}
			return $ret['spu'];
		}else{
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
}
?>
