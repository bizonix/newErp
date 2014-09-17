<?php
/*
*iqc检测领取 
*/
class contentModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"fb_content_template";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件料号列表
	public 	static function getContentList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." as a left join power_global_user as b on a.addUserId = b.global_user_id {$where} ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取数量
	public 	static function getContentNum($where){	
		self::initDB();
		$sql	 =	"select id from ".self::$table." as a left join power_global_user as b on a.addUserId = b.global_user_id {$where} ";	
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 * @where as String
	 */
	public static function update($data,$where = ""){
		self::initDB();
		$field = "";
		if(!is_array($field)){
 			foreach($data as $k => $v){
				$field .= ",`".$k."` = '".$v."'";
			}
			$field	= ltrim($field,",");
			$sql	= "UPDATE `".self::$table."` SET ".$field." WHERE 1 ".$where;
			$query	=	self::$dbConn->query($sql);
			if($query){                             
				return true;
			} else {			
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//删除记录
	public static function delete($where){
		self::initDB();	
		$sql   = "UPDATE `".self::$table."` SET is_delete=1 ".$where;
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){                        
			return true;
		}else{
			return false;
		}
	}
	
	//
	public static function checkContentExsit($content){
		self::initDB();
		$content = trim($content);
		$sql	 =	"select id from ".self::$table." where content = '$content' and is_delete = 0 limit 1";	
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);			
			return (int) $ret[0]['id'];//count($ret);	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
	}
	
}
?>