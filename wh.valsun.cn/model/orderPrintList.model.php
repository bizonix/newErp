<?php
/*
*发货\配货单打印列表相关操作
*add by :hws
*/
class OrderPrintListModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_order_printing_list";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件配货清单
	public 	static function getPrintList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
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
	
	/*
	 * 获得记录条数
	 * $where 条件语句
	 */
	public static function getRcordNumber($where){
	    self::initDB();
	    $sql = 'select count(*) as num from wh_order_printing_list where 1 '.$where;
	    $row = self::$dbConn->fetch_first($sql);
	    return $row['num'];
	}
	
	/*
	 * 获得一个打印单的信息
	 * $id 打印单id
	 */
	public static function getPrintInfoById($id){
	    self::initDB();
	    $sql = 'select * from wh_order_printing_list where id='.$id;
	    return self::$dbConn->fetch_first($sql);
	}
	
	/*
	 * 解锁打印单
	 * $id
	 */
	public static function unlockPrint($id){
	    self::initDB();
	    $sql = "update wh_order_printing_list set status=".PR_WPRINT.' where id='.$id;
	    //echo $sql;exit;
	    $re = self::$dbConn->query($sql);
	    if ($re){
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	
	/*
	 * 批量解锁打印单
	 */
	public static function unlockAsetOfPrint($ids){
	    self::initDB();
	    $sql = "update wh_order_printing_list set status=".PR_WPRINT.' where id in ('.$ids.')';
	    //echo $sql;exit;
	    $re = self::$dbConn->query($sql);
	    if ($re){
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	
	/*
	 * 加锁打印单
	 * $id 一组id数组
	 */
	public static function lockPrint($id){
	    self::initDB();
	    $sqlstr = implode(',', $id);
	    $sql = "update wh_order_printing_list set status=".PR_LOCK.' where id in ('.$sqlstr.')';
	    //echo $sql;exit;
	    $re = self::$dbConn->query($sql);
	    if ($re){
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	
	/*
	 * 删除打印单 并将状态改为打印完成
	 */
	public static function deletePrint($id){
	    self::initDB();
	    $sql = "update wh_order_printing_list set is_delete=1, status=".PR_PRINTED.' where id='.$id;
	    //echo $sql;exit;
	    $re = self::$dbConn->query($sql);
	    if ($re){
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	
	/*
	 * 批量删除打印单
	 */
	public static function deleteAsetOfPrint($ids){
	    self::initDB();
	    $sql = "update wh_order_printing_list set is_delete=1, status=".PR_PRINTED.' where id in ('.$ids.')';
	    //echo $sql;exit;
	    $re = self::$dbConn->query($sql);
	    if ($re){
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}

}
?>