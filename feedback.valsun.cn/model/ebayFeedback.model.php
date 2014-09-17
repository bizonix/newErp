<?php
/*
*iqc检测领取 
*/
class EbayFeedbackModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"fb_comment_record_ebay";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件料号列表
	public 	static function getOrderList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql;exit;
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
	public 	static function getOrderNum($where){	
		self::initDB();
		$sql	 =	"select count(*) from ".self::$table." {$where} ";	
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);		
			return $ret['count(*)'];	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	public static function orderMutilDel($id){
		self::initDB();
		$sql = "UPDATE `".self::$table."` SET is_delete=1 WHERE id='{$id}'";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		} else {
			return false;
		}
	}
	

	//获取条件料号列表
	public 	static function getRequestChangeList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `fb_request_change_ebay` {$where} ";
		//echo $sql;exit;
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
	public 	static function getRequestChangeNum($where){	
		self::initDB();
		$sql	 =	"select count(*) from `fb_request_change_ebay` {$where} ";	
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);		
			return $ret['count(*)'];	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	//获取条件料号列表
	public 	static function getEbayReasonCategoryInfo($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `fb_reasoncategory_ebay` {$where} ";
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
	
	public static function checkChangeExist($account, $userId){
		self::initDB();		
		$sql	 =	"select count(*) as num from `fb_request_change_ebay` where account='{$account}' and ebayUserId='{$userId}' and is_delete=0";	
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return (int) $ret[0]['num'];//count($ret);	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
	}
	
	public static function requestChangeAdd($data){		
		self::initDB();
		//print_r($data);
		$sql = array2sql($data);
		$sql = "INSERT INTO `fb_request_change_ebay` SET ".$sql;
		//echo $sql;
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
	
	public static function requestChangeDel($id){
		self::initDB();		
		$sql = "UPDATE `fb_request_change_ebay` SET is_delete=1 WHERE id='{$id}'";
		$query	=	self::$dbConn->query($sql);
		if($query){                             
			return true;
		} else {			
			return false;
		}
	}
	
	/**
	 * 修改本评价和相应的请求数减一，即modifyStatus=1,原子操作
	 * add by 姚晓东
	 */
	public static function requestChangeUpateStatus($data,$id,$ebay_account,$commentingUser){		
				
		$FeedbackID     = $data['FeedbackID'];
		$account        = $ebay_account;
		$ebayUserId     = $commentingUser;
		unset($data['FeedbackID']);
		self::$dbConn->begin();
		$upd = self::update($data," and FeedbackID='$FeedbackID' ");
		if ($upd) {
			$sql 	= "UPDATE `fb_request_change_ebay` SET `modifyStatus`=1 WHERE account='$account' AND ebayUserId='$ebayUserId' AND is_delete=0 AND `modifyStatus`=0 LIMIT 1";
			$query	=  self::$dbConn->query($sql);
			if($query){
				if(self::$dbConn->affected_rows()>0){
					self::$dbConn->commit();
					return true;
				}else{
					self::$dbConn->rollback();
					return false;
				}
			} else {
				self::$dbConn->rollback();
				return false;
			}
		}
	}
	
	
	/**
	 * 应对同一用户的多个评价的更改
	 * add by yxd 2014/6/16
	 */
	public static function  resetRequestChange($id){
		$sql 	= "UPDATE `fb_request_change_ebay` SET `modifyStatus`=1 WHERE id='{$id}'";
		$query	=  self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			return false;
		}
	}
	
	function act_requestChangeUpdate(){
		//调用model层获取数据
		$id = isset($_POST['id']) ? trim($_POST['id']) : '';
		if ($id == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		$data = array(
				'is_delete'		=>	1,
		);
		$ret = EbayFeedbackModel::requestChangeDel($id);
		if (!$ret) {
			self::$errCode = '002';
			self::$errMsg  = "删除失败！";
			return false;
		}
		return 'ok';
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
			//echo $sql;
			$query	=	self::$dbConn->query($sql);
			if($query){                             
				return true;
			} else {			
				return false;
			}
		}else {
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
		//echo $sql;
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
		//$sql   = "DElETE FROM `".self::$table."` ".$where;
		$sql   = "UPDATE `".self::$table."` SET is_delete=1 ".$where;
		$query = self::$dbConn->query($sql);
		if($query){                        
			return true;
		}else{		
			return false;
		}
	}	

}
?>