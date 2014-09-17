<?php

class addGroupModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	
	
	
	//db³õÊ¼»¯
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	public 	static function insertRecord($group_name,$group_num,$userId){
		self::initDB();
		$sql	 =	"insert into wh_storage_status_group(groupName,groupCode,creatorId,createdTime) values('{$group_name}',{$group_num},{$userId},".time().")";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
}
?>	
	