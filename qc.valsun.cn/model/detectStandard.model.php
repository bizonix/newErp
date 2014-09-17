<?php
/*
*检测标准
*/
class DetectStandardModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"qc_sample_standard_list";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取当前检测标准列表
	public 	static function getNowStandardList($select,$where){
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
	
	//获取检测类型列表
	public 	static function getSampleTypeList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `qc_sample_type` {$where} ";
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
	
	//删除记录
	public static function delete($where){
		self::initDB();
		$sql   = "DElETE FROM `".self::$table."` ".$where;
		$query = self::$dbConn->query($sql);
		if($query){                        
			return true;
		}else{		
			return false;
		}
	}
	
	//更新标准临时表
	public static function updateSampleStandard(){
		self::initDB();
		self::delete('where 1');
		$sql = "select a.minimumLimit,a.maximumLimit,a.sampleTypeId,b.sampleNum,c.Ac,c.Re,c.Al,c.Rt
				from qc_sample_standard as a left join qc_sample_size_code as b on a.sizeCodeId=b.id
				left join qc_sample_coefficient as c on a.sampleTypeId=c.sampleTypeId and b.id=c.sizeCodeId
				where a.is_open=1 and c.is_open=1";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$result = self::$dbConn->fetch_array_all($query);
			foreach($result as $res){
				$data = array();
				$data = array(
					'sampleTypeId' => $res['sampleTypeId'],
					'minimumLimit' => $res['minimumLimit'],
					'maximumLimit' => $res['maximumLimit'],
					'sampleNum'    => $res['sampleNum'],
					'Ac' 		   => $res['Ac'],
					'Re' 		   => $res['Re'],
					'Al'           => $res['Al'],
					'Rt' 		   => $res['Rt']
				);
				self::insertRow($data);
			}
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}

}
?>