<?php
class SpuModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"pc_auto_create_spu";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	
	public 	static function getSpuList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
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
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	

	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
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

	public 	static function getSpuListNum(){
		self::initDB();
		$sql	 =	"select * from ".self::$table." order by id asc ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	
		}
	}
	
	//获取自动生成shu数据
	public 	static function getAutoCreateSku($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	
		}
	}
	
	//插入生成sku表
	public static function insertSkuRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO ".self::$table." SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}

	//插入库存表
	public static function insertOnHandleRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `pc_onhandle` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	
	public 	static function getCombList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `pc_goods_combine` {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	
		}
	}
	
	//插入组合表
	public static function insertCombRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `pc_goods_combine` SET ".$sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	
	//插入组合关系表
	public static function insertCombRelation($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `pc_sku_combine_relation` SET ".$sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	
	//获取组合料号信息
	public 	static function getSpuCombList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `pc_goods_combine` {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	
		}
	}
    
    //获取组合料号信息
	public 	static function getSkuListBySpu($tName, $spu){
		self::initDB();
		$sql	 =	"select sku from $tName where spu='$spu' ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
            $tmpArr = array();
            foreach($ret as $value){
                $tmpArr[] = $value['sku'];
            }
            $str = implode('<br/>',$tmpArr);
			return $str;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	
		}
	}
}
?>