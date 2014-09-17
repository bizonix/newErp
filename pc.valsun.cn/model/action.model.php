<?php
/**
*类名：Action
*功能：操作(动作)权限表管理
*版本：2013-05-10
*作者：冯赛明
*说明：该类管理所有系统中的操作，比如要在图片系统中的某个类中新增一个操作（动作）
*/
class ActionModel{
	public static $dbConn;
	private static $table_action_info  = 'power_action';
	private static $table_action_group = 'power_action_group';
	private static $_instance;
	static $errCode = '0';
	static $errMsg  = "";

	public function __construct(){
		self::$table_action_info  = C('TABLE_ACTION_INFO');
		self::$table_action_group = C('TABLE_ACTION_GROUP');
	}	

	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	public function count(){
		$this->is_count = true;
		return $this;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	/*
	*方法功能：查询操作(动作)信息
	*/
	public function getActionInfo($filed, $where){		
		self::initDB();
		$sql='select '.$filed.' from `'.self::$table_action_info.'` LEFT JOIN `'.self::$table_action_group.'` ON action_group_id=group_id '.$where.' LIMIT 1';
		$query=self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		
		return self::$dbConn->fetch_array($query);
	}
	
	/*
	*方法功能：查询操作(动作)信息
	*/
	public function getActionLists($filed, $where, $orderby, $limit){		
		
		self::initDB();
		$sql='select '.$filed.' from `'.self::$table_action_info.'` LEFT JOIN `'.self::$table_action_group.'` ON action_group_id=group_id where '.$where.' '.$order.' '.$limit;
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		
		if ($this->$is_count===true){
			$this->$is_count = false;
			return self::$dbConn->num_rows($query);
		}
		
		return self::$dbConn->fetch_array_all($query);
	}
	
	/*
	*方法功能：获取全部(action group)列表信息
	*add by guanyongjun 2013-08-22
	*return array
	*/
	public function actionGroupList($id){		
		self::initDB();
		$sql	= "SELECT group_name FROM ".self::$table_action_group." WHERE group_system_id = {$id}";
		$query  = self::$dbConn->query($sql);
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		$result	= array();
		$ret	= self::$dbConn->fetch_array_all($query);
		for($i=0; $i<count($ret); $i++){
			array_push($result,$ret[$i]['group_name']);
		}
		return $result;
	}
}
?>