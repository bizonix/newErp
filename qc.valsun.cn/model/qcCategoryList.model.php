<?php
/*
*检测种类和产品分类的关系
*/
class qcCategoryListModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"qc_category_type_relation";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	
	//获取检测标准列表
	public 	static function getCategoryListNum($where){
		self::initDB();
		$sql	 =	"select count(*) count from ".self::$table." {$where} ";
		//echo $sql.'<br>';
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret[0]['count'];	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	//获取检测标准列表
	public 	static function getCategoryList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
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
	
	//获取检测标准列表2
	public 	static function getCategoryArr($where){
		self::initDB();
		$select = "id,name";
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		$arr 	 =  array();	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if(empty($ret)){
				return array();	
			}
			foreach($ret as $v){
				$arr[$v['id']] = $v['name'];	
			}
			return $arr;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
	}
	
	/*
    * 
    */		
   public static function modifySampleTypeId($set, $where){
	   self::initDB();
		$info = array();
		$sql	 =	"update ".self::$table." set {$set} {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"更新类别种类成功！";
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"更新类别种类失败！";
			return false;	
		}
	}
	
	//获取检测标准样本大小
	public 	static function getSampleSizeCodeList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		$data    = array();
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if(empty($ret)){
				self::$errCode =	"040";
				self::$errMsg  =    "empty";
				return false;
			}else{
				foreach($ret as $val){
					$path_arr = explode('-',$val['path']);
					$path_arr_count = count($path_arr);
					for($i = 0; $i<$path_arr_count; $i++){
						$data = $data['path'];
					}
				}
			}
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//检测标准样本标准列表
	public 	static function getSampleStandardList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `qc_sample_standard` {$where} ";
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

	//获取标准数量
	public 	static function getSampleStandardNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." $where";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	//获取产品一级分类
	public static function getCategory1(){
		self::initDB();
		$sql	 =	"select * from `qc_category_type_relation` WHERE pid='0'";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	public static function getCategory2($condition){
		self::initDB();
		$sql	 =	"select id,name from `qc_category_type_relation` WHERE file=2 and pid='$condition'";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	public static function getCategory3($condition){
		self::initDB();
		$sql	 =	"select id,name from `qc_category_type_relation` WHERE file=3 and pid='$condition'";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	public static function getCategory4($condition){
		self::initDB();
		$sql	 =	"select id,name from `qc_category_type_relation` WHERE file=4 and pid='$condition'";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	public static function changeCategory($condition,$category){
		self::initDB();
		$sql	 =	"UPDATE `qc_category_type_relation` SET sampleTypeId={$category} WHERE path like '{$condition}'";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			//$ret =self::$dbConn->fetch_array_all($query);
			return true;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
}
?>