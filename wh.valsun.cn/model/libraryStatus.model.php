<?php
/**
 * 出库状态model
 * 作者 涂兴隆
 */
class LibraryStatusModel {
    public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
    
    private static $statusinfo = NULL;
	private static $table = 'wh_storage_status';
    
    //db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
    
    /*
     * 获得所有的出库状态信息列表
     */
    public  function getAllLibStatusList($where=''){
			self::initDB();
        //if (self::$statusinfo == NULL) {
                $sql = "select * from wh_storage_status where is_delete=0 $where order by statusCode asc";
                //echo $sql;exit;
                self::$statusinfo =  self::$dbConn->fetch_array_all(self::$dbConn->query($sql));
            //}
            return self::$statusinfo;
    }
	
	/*
     * 获得所有的出库状态信息列表
     */
    public static function getLibraryStatusList($select,$where){
			self::initDB();
        //if (self::$statusinfo == NULL) {
                $sql = "select {$select} from ".self::$table." {$where}";
                self::$statusinfo =  self::$dbConn->fetch_array_all(self::$dbConn->query($sql));
          //  }
            return self::$statusinfo;
    }
	
	/*
     * 获得所有的出库状态信息列表
     */
    public static function getLibraryStatusGroupList($where = ''){
			self::initDB();
			$sql = "select * from wh_storage_status_group where is_delete=0 {$where}";
			self::$statusinfo =  self::$dbConn->fetch_array_all(self::$dbConn->query($sql));
			$groupArr = array();
			foreach(self::$statusinfo as $value){
				$groupArr[$value['groupCode']] = $value['groupName'];
			}
            return $groupArr;
    }
	
    /**
     * 获取状态码对应状态信息名称
     * @param  number $statusCode
     * @return string $statusName
     * @author czq 
     */
    public static function getStatusNameByStatusCode($statusCode,$groupId='4'){
    	self::initDB();
    	$sql 	= " SELECT statusName FROM ".self::$table." WHERE statusCode = '{$statusCode}' AND groupId = '{$groupId}'";
    	$result = self::$dbConn->fetch_first($sql);
    	if($result){
    		return $result['statusName'];
    	}else{
    		return false;
    	}
    	
    }
	/*
     * 获得所有的出库状态信息列表
     */
    public static function getLibraryStatusAllGroup($where = ''){
		self::initDB();
		$sql = "select * from wh_storage_status_group where is_delete=0 {$where}";
		self::$statusinfo =  self::$dbConn->fetch_array_all(self::$dbConn->query($sql));
		return self::$statusinfo;
    }
	
	//获取数量
	public static function getLibraryStatusNum($where){
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
	
	//获取数量
	public static function getLibraryStatusGroupNum($where = ''){
		self::initDB();
		$sql = "select * from wh_storage_status_group where is_delete=0 {$where}";
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
	
    /*
     * 根据状态id获得状态描述信息
     * $id 状态id
     */
    public function statusIttoStr($id){
        if (self::$statusinfo == NULL) {
        	$this->getAllLibStatusList();
        }
        foreach (self::$statusinfo as $stuval){
            if($stuval['statusCode'] == $id){
                return $stuval['statusName'];
            }
        }
        return '';
    }
    
    /*
     * 打印状态码到描述的转换
     */
    public static function printCodeTostr($cid){
        $code = array(
        	1001=>'待打印',
                1002=>'已经锁定',
                1003=>'已打印'
        );
        return $code[$cid];
    }
}