<?php
/**
 * 海外仓备货单相关信息
 * Enter description here ...
 * @author 王民伟
 *
 */
class OwOrderModel {
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	private static $_instance;
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * 获取海外备货单未审核状态下同个操作人生成的订单号
	 * Enter description here ...
	 * @param unknown_type $operId
	 */
	public static function getOwOrderNum($operId){
		self::initDB();
		$sql    = "SELECT id,recordnumber FROM ph_ow_order WHERE status = 1 ";
		$sql   .= " AND operator_id = '{$operId}' AND is_delete = 0 order by id desc";
		$sql 	= self::$dbConn->query($sql);
		$order 	= self::$dbConn->fetch_one($sql);
		return $order;
	}
	
	/**
	 * 根据海外备货单获取编号
	 * Enter description here ...
	 * @param unknown_type $operId
	 */
	public static function getOwPoid($recordnumber){
		self::initDB();
		$sql    = "SELECT id FROM ph_ow_order WHERE recordnumber = '{$recordnumber}' AND is_delete = 0";
		$sql 	= self::$dbConn->query($sql);
		$order 	= self::$dbConn->fetch_one($sql);
		return $order['id'];
	}
	
	/**
	 * 获取料号获取对应采购员编号
	 * Enter description here ...
	 * @param unknown_type $operId
	 */
	public static function getPurchaseIdBySku($sku){
		self::initDB();
		$sql 	= "SELECT purchaseId from pc_goods where sku = '{$sku}' and is_delete=0";
		$sql 	= self::$dbConn->query($sql);
		$id 	= self::$dbConn->fetch_one($sql);
		return $id['purchaseId'];
	}
	
	/**
	 * 获取料号获取对应供应商编号
	 * Enter description here ...
	 * @param unknown_type $operId
	 */
	public static function getPartnerId($sku){
		self::initDB();
		$sql 	= "SELECT a.partnerId ,b.company_name as companyname from ph_user_partner_relation as a left join ph_partner as b on a.partnerId=b.id where a.sku = '{$sku}' ";
		$sql 	= self::$dbConn->execute($sql); 
		$info 	= self::$dbConn->fetch_one($sql);
		return $info;
	}
	
	/**
	 * 判断料号最近半个月是否有下过备货单
	 */
	public static function orderExistSku($sku){
		self::initDB();
		$nowTime    = time();
		$halfMoth   = time() - 15 * 24 * 60 * 60;//半个月
		$sql    	= "SELECT b.sku FROM ph_ow_order AS a JOIN ph_ow_order_detail AS b ON a.id = po_id WHERE a.addtime BETWEEN '{$halfMoth}' AND '{$nowTime}' AND b.sku = '{$sku}' AND b.is_delete = 0";
		$sql 		= self::$dbConn->query($sql);
		$exist 		= self::$dbConn->fetch_one($sql);
		if(!empty($exist)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 批量更新外备货单明细表料号供应商
	 * Enter description here ...
	 */
	public static function batchUpdSkuParId(){
		self::initDB();
		$sql 	= "SELECT sku FROM ph_ow_order_detail";
		$sql 	= self::$dbConn->query($sql);
		$data 	= self::$dbConn->fetch_array_all($sql);
		foreach($data as $k => $v){
			$sku 	 	= $v['sku'];
			$parInfo 	= self::getPartnerId($sku);
			$parId   	= $parInfo['partnerId'];
			$upd 		= "UPDATE ph_ow_order_detail SET parid = '{$parId}' WHERE sku = '{$sku}'";
			self::$dbConn->query($upd);
		}
	}
	
	/**
	 * 获取海外仓料号总个数
	 */
	public static function getNewOwSkuInfoCount(){
		self::initDB();
		$rtnData 	= array();
		$totalnum   = 0;
		$sql 		= "SELECT COUNT(*) AS totalnum FROM ow_stock AS a JOIN pc_goods AS b ON a.sku = b.sku JOIN power_global_user AS c ON c.global_user_id = b.purchaseId ";
		$sql       .= "JOIN ow_new_sku_move AS d ON d.sku = b.sku ";
		$query  	= self::$dbConn->query($sql);
		$data   	= self::$dbConn->fetch_one($query);
		if(!empty($data)){
			$totalnum = $data['totalnum'];
		}
		return $totalnum;
	}
	
	/**
	 * 获取海外仓新品料号，返回数据更新新品基础信息到海外仓系统
	 */
	public static function getNewOwSkuInfo($page, $pagenum){
		self::initDB();
		$rtnData 	= array();
		$start      = ($page - 1) * 200;
		$pagenum    = 200;
		$sql 		= "SELECT b.*, c.global_user_name AS cguser FROM ow_stock AS a JOIN pc_goods AS b ON a.sku = b.sku JOIN power_global_user AS c ON c.global_user_id = b.purchaseId ";
		$sql       .= "JOIN ow_new_sku_move AS d ON d.sku = b.sku ";
		$sql       .= "limit $start, $pagenum ";
		$query  	= self::$dbConn->query($sql);
		$data   	= self::$dbConn->fetch_array_all($query);
		if(!empty($data)){
			$rtnData = $data;
		}
		return $rtnData;
	}

}

?>
