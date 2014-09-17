<?php
class CategoryModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"pc_goods_category";


	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}


	public 	static function getCategoryList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql.'<br>';
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
    
    public 	static function getCategoryCount($where){
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
	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$sql;
        //echo $sql.'<br>';
		$query	=	self::$dbConn->query($sql);
		if($query){
            publishMQ($table, $sql, C("MQSERVERADDRESS"));
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
                publishMQ($table, $sql, C("MQSERVERADDRESS"));
				return true;
			} else {
				return false;
			}
		}
		else {
			return false;
		}
	}
    
    public static function getCategoryNameByPath($path){
       self::initDB();
       $sql = "select name from `".self::$table."` WHERE path='$path'";
        //echo $sql.'<br>';
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret[0]['name'];
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		} 
    }
    
    public static function getCategoryNameById($id){
       self::initDB();
       $sql = "select name from `".self::$table."` WHERE id=$id";
        //echo $sql.'<br>';
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret[0]['name'];
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		} 
    }
    
    public static function getCategoryPathById($id){
       self::initDB();
       $sql = "select path from `".self::$table."` WHERE id=$id";
        //echo $sql.'<br>';
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret[0]['path'];
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		} 
    }
    
    public static function updateCateMem(){
        //更新mem
        $categoryList = self::getCategoryList('*',"WHERE is_delete=0");
        if(!empty($categoryList)){
            $key = 'pc_goods_category_all';
            setMemNewByKey($key, $categoryList);
            foreach($categoryList as $value){
                if(!empty($value)){
                    $key = 'pc_goods_category_'.$value['path'];
                    setMemNewByKey($key, $value);
                }         
            }
        }
        //
    }
}
?>