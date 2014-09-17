<?php 
/* add by heminghua 
 * 未订单列表
 */
class whNoOrderModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	//搜索未订单列表
	public function selectList($where){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_abnormal_purchase_orders {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		}else{
			return false;	
		}
	}
}
?>