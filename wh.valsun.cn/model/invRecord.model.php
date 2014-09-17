<?php
/*
*盘点记录
*ADD BY hws
*/
class InvRecordModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_inventory_records";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取盘点记录列表
	public 	static function getInvRecordList($select,$where){
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
	
	//获取数量
	public 	static function getInvNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." $where";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
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
	 * sku信息
	 *@para $sku
	 */
	public static function getSkuInfo($sku){
		self::initDB();
        $sql	 =	"select actualStock from wh_sku_location where sku='$sku'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_first($sql);
			return $ret;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 * sku信息
	 *@para $sku
	 */
	public static function getSkuCost($sku){
		self::initDB();
        $sql	 =	"select goodsCost from pc_goods where sku='$sku'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_first($sql);
			return $ret;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//通过skuid和仓位名字获取对应的仓位存储信息
	public static function getSkuPosition($skuid,$pName){
		self::initDB();
        $sql	 =	"select a.nums,b.id as poid from wh_product_position_relation as a 
					left join wh_position_distribution as b on a.positionId=b.id 
					where a.pId='$skuid' and a.is_delete=0 and b.pName='{$pName}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_first($sql);
			return $ret;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
    
    /** 修改点货备注信息**/
	public static function update_note($id, $note){
		self::initDB();
        $id     =   intval(trim($id));
        $note   =   trim($note);
        if($id){
            $sql    =   "update wh_inventory_records set remark = '{$note}' where id = '{$id}'";
            $query	 =	self::$dbConn->query($sql);		
    		if($query){
    			return TRUE;
    		}else{
    			self::$errCode =	"003";
    			self::$errMsg  =	"error";
    			return FALSE;	
    		}
        }else{
            self::$errCode =	"003";
			self::$errMsg  =	"error";
			return FALSE;
        }
		
	}

}
?>