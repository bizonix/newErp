<?php
/*
 * 名称：ExpressRemarkModel
 * 功能：快递描述，包括各个操作记录和审核记录
 * 版本：v 1.0
 * 日期：2013/12/20
 * 作者：Herman.xi
 * */
class ExpressRemarkModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	'';
	public	static $Table	=	'om_express_remark';
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getExpressRemarkList($orderid){
		!self::$dbConn ? self::initDB() : null;
		if(empty($orderid)){
			self :: $errCode = "400";
			self :: $errMsg =  " orderid is empty !";
			return array(); //失败则设置错误码和错误信息， 返回false	
		}
		$sql = "select * from ".self::$Table." where omOrderId = {$orderid}";
		$query	= self::$dbConn->query($sql);
		$tinfo = self::$dbConn->fetch_array_all($query);
		$tinfoarr = array();
		for($i = 0; $i<count($tinfo); $i++){
			$descriptions = stripslashes($tinfo[$i]['description']);
			if(strpos($descriptions,"]")){
				$branddescrip = substr($descriptions,1,strpos($descriptions,"]")-1);
				$description = substr($descriptions,strpos($descriptions,"]")+1);
			}else{
				$branddescrip = '';
				$description = $tinfo[$i]['description'];
			}
			$tinfoarr[$i] = $tinfo[$i];
			$tinfoarr[$i]['branddescrip'] = $branddescrip;
			$tinfoarr[$i]['description'] = $description;
		}
		//var_dump($tinfoarr);
		if($tinfoarr){
			self :: $errCode = "200";
			self :: $errMsg =  " 获取数据成功！ ";
			return $tinfoarr; //失败则设置错误码和错误信息， 返回false
		}else{
			self :: $errCode = "001";
			self :: $errMsg  =  " 获取数据为空 ";
			return $tinfoarr;
		}
	}
	
	/*
	 * 插入快递描述信息(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function addExpressRemark($omOrderId,$data){
		!self::$dbConn ? self::initDB() : null;
		BaseModel :: begin(); //开始事务
		if(!self::deleteExpressRemark($omOrderId)){
			BaseModel :: rollback();
			self :: $errCode = "001";
			self :: $errMsg =  " 删除数据失败！ ";
			return false; //失败则设置错误码和错误信息， 返回false	
		}
		if(!empty($data)){
			//var_dump($data);
			foreach($data as $datavalue){
				$string = array2sql_extral($datavalue);
				//$string = "('".$datavalue['omOrderId']."','". $datavalue['price']."','". $datavalue['amount']."','". $datavalue['hamcodes']."','". $datavalue['isBrand']."','". $datavalue['description']."','". $datavalue['creatorId']."','". $datavalue['createdTime'] ."'),";
				$sql = "INSERT INTO ".self::$Table." SET {$string} ";
				//echo $sql;
				if(!self::$dbConn->query($sql)){
					BaseModel :: rollback();
					self :: $errCode = "002";
					self :: $errMsg =  " 插入数据失败！ ";
					return false; //失败则设置错误码和错误信息， 返回false	
				}
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
			self :: $errCode = "200";
			self :: $errMsg =  " 插入数据成功！ ";
			return true; //失败则设置错误码和错误信息， 返回false	
		}
	}
	
	/*
	 * 删除快递描述信息(最新版)
	 * last modified by Herman.Xi @20131223
	 */
	public static function deleteExpressRemark($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		$sql = "DELETE FROM ".self::$Table." WHERE omOrderId = ".$omOrderId;
		//echo $sql;
		if(!self::$dbConn->query($sql)){
			self :: $errCode = "002";
			self :: $errMsg =  " 插入数据失败！ ";
			return false; //失败则设置错误码和错误信息， 返回false	
		}else{
			self :: $errCode = "200";
			self :: $errMsg =  " 插入数据成功！ ";
			return true; //失败则设置错误码和错误信息， 返回true
		}
	}
	
}
?>