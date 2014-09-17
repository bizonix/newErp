<?php
/*
*贴标录入操作(model)
*add by hws
*
*/
class PasteLabelModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	*查找分组号信息
	*/
	public static function selectListById($id){
		self::initDB();
		$sql = "SELECT * FROM wh_print_group where id={$id} and is_delete=0 and status=1 and labelUserId is null";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	/*
	*插入贴标数据
	*/
	public static function insertRecord($id,$labelUserId){
		self::initDB();
		$time = time();
		$sql = "UPDATE wh_print_group SET labelTime={$time},labelUserId={$labelUserId},labelNum=printNum where id={$id}";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){	
			return true;	
		}else{
			return false;	
		}
	}
	/*
	*查找所有记录
	*/
	public static function selectList($where){
		self::initDB();
		$sql = "SELECT a.*,b.batchNum,b.sku FROM wh_print_group as a left join wh_tallying_list as b on a.tallyListId=b.id {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	/*
	 * 删除打标记录
	 */
	public static function delRecord($id){
		self::initDB();
		$sql = "UPDATE wh_print_group SET is_delete=1 where id={$id}";	
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
	
	/*
	 * 清空贴标记录
	 */
	public static function clearRecord($id){
		self::initDB();
		$sql = "UPDATE wh_print_group SET labelTime='',labelUserId=NULL,labelNum='' where id={$id}";	
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
	
	/*
	 * 修改贴标记录
	 */
	public static function editRecord($id,$labelUserId){
		self::initDB();
		$sql = "UPDATE wh_print_group SET labelUserId='{$labelUserId}' where id={$id}";	
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
}
?>