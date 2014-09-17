<?php
/***************************数据库连接通用类*****************************/

$CONFIG_ROW['db_rw'] = array(
	'dbhost'=>'192.168.200.162',
	'dbuser'=>'cerp',
	'dbpw'=>'123456',
);

class DBClass{
	
	public	$link = null;
	public  $error = array();
	private $rwtype = '';
	private $db_name = 'cerp';
	private $thread = '';
	private $change = false;
	private $double = 0;
	private $db_rw = array();
	private $db_mro = array();
	private $db_aro = array();
	private $crashs = array();
	
	public function __construct(){
		
		global $CONFIG_ROW;
		
		if (!isset($CONFIG_ROW)||empty($CONFIG_ROW)){
		}
		$this->db_rw = $CONFIG_ROW['db_rw'];
		$this->db_mro = $CONFIG_ROW['db_rw'];
		$this->db_aro = $CONFIG_ROW['db_rw'];
	}
	
	/**********************
	使用构造函数连接数据库
	**********************/
	function mysqlConnect($host,$root,$password){

		if(!$this->link=mysql_connect($host,$root,$password)){
			in_array($host, $this->crashs) or array_push($this->crashs, $host);
			if($this->loadBalancing()===false){
				echo mysql_error();		
				die("database connected failure");
			}
		}
		//mysql_query("SET NAMES utf8");
		if(!@mysql_select_db($this->db_name)){
			in_array($host, $this->crashs) or array_push($this->crashs, $host);
			$this->error[] = '数据库连接失败----'.mysql_error().'----'.$host.'----'.$root.'----'.$password;
			if($this->loadBalancing()===false){
				var_dump($this->rwtype,$this->crashs,$this->db_aro,$this->error);
				echo mysql_error();
				die("数据库连接失败");
			}
		}
			
	}
	
	function getRorW($sql){
		$sql = trim($sql);
		return preg_match('/^[select|show]/i', $sql) ? true : false;
	}

	function loadBalancing(){

		if($this->rwtype==='write'){
			return false;
		}
		if(count($this->crashs)>count($this->db_aro)){
			return false;
		}
		$k = array_rand($this->db_aro, 1);
		list($host,$root,$password) = array_values($this->db_aro[$k]);
		if(!in_array($host, $this->crashs)){
			$this->error[] = 'try connected again----'.$host.'----'.$root.'----'.$password;
			$this->mysqlConnect($host,$root,$password);
		}else{
			$this->error[] = 'this is down----'.$host.'----'.$root.'----'.$password;
			//$this->loadBalancing();
		}
		return true;
	}
	

	function switchDB($sql){

		$this->sql = $sql;
		$this->mysqlConnect('192.168.200.162','cerp','123456');
		/*
		$rwtype = $this->getRorW($sql) ? 'read' : 'write';
		$this->error[] = $sql;
		if($this->rwtype===''&&$rwtype==='read'){
			$k = array_rand($this->db_mro, 1);
			list($host,$root,$password) = array_values($this->db_mro[$k]);
			$this->mysqlConnect($host,$root,$password);
			$this->double = 0;
			$this->error[] = 'first read double connect----'.$rwtype.'----'.$host.'----'.$root.'----'.$password;
		}else if($this->rwtype==='write'&&$rwtype==='read'){
			$this->double++;
			$this->error[] = 'chanage write to read connect----'.$rwtype;
		}else if($rwtype!==$this->rwtype&&$this->change===true){
			$this->double = 0;
			$this->error[] = 'chanage read to write do not chanage connect----';
		}else if ($rwtype!==$this->rwtype) {
			list($host,$root,$password) = array_values($this->db_rw);
			$this->error[] = $rwtype.'----'.$host.'----'.$root.'----'.$password;
			$this->mysqlConnect($host,$root,$password);
			$this->double = 0;
			$this->change = true;
		}else if($rwtype==='write'){
			$this->error[] = 'write double connect----'.$rwtype;
		}else if($this->double===5&&$this->change===true){
			$k = array_rand($this->db_mro, 1);
			list($host,$root,$password) = array_values($this->db_mro[$k]);
			$this->mysqlConnect($host,$root,$password);
			$this->double = 0;
			$this->change = false;
			$this->error[] = 'rand read double connect----'.$rwtype.'----'.$host.'----'.$root.'----'.$password;
		}else if($this->double<3&&$this->change===true){
			$this->error[] = 'write '.$this->double.'time connect----'.$rwtype;
			$this->double++;
		}else{
			$this->error[] = 'read double connect----'.$rwtype;
		}
		/*if (function_exists('write_log')){
			write_log('systemlog_'.date("Ymd").'/'.date("H").'.txt', implode("\n", $this->error)."\n\n");	
		}
		 */
		$this->rwtype = $rwtype;
	}
	/*************************
	执行查询语句
	*************************/
	function  query($sql){
		$this->switchDB($sql);
		return @mysql_query($sql);
	}

	/*****************************************
	执行查询语句之外的操作 例如:添加，修改，删除
	*****************************************/

	function execute($sql){
		$this->switchDB($sql);
		if(@function_exists("mysql_unbuffered_query")){
			$result=mysql_unbuffered_query($sql);
		}
		else{
			$result=mysql_query($sql);
		}
		return $result;
	} 

	/*************************
	执行更新语句
	************************/
	function update($sql){
		$this->switchDB($sql);
	 	if(@function_exists("mysql_unbuffered_query"))
		{
			$result=@mysql_unbuffered_query($sql);
		}
		else{
			$result=@mysql_query($sql);
		}	
		$rows	= mysql_affected_rows($this->link);
		
		return $rows;
	}
	
	
	/**************************
	获得表的记录的行数
	*************************/
	function num_rows($result){
		if($result){
			return @mysql_num_rows($result);
		}
		else{
			return 0;
			
		}			
	}
	
	/***********************
	返回对象数据
	************************/
	function fetch_object($result){		 
			return @mysql_fetch_object($result);
	}
	
	/*************************
	返回关联数据
	*************************/
	function fetch_assoc($result){
		return @mysql_fetch_assoc($result);
	}

	/**************************
	返回关联数据
	**************************/
	function fetch_array($result,$type='MYSQL_BOTH'){
		return @mysql_fetch_array($result,$type);
	
	}
	
	/**************************
	只返回一条关联数据
	**************************/
	function fetch_one($result,$type='MYSQL_BOTH'){
		return mysql_fetch_assoc($result);
	}
	
	/*************************
	关闭相关与数据库的信息链接
	**************************/
	
	function free_result($result){
		return @mysql_free_result($result);
	}
	
	function close(){
		return @mysql_close();
	}
	
	/*********************************
	其他操作例如结果集放入数组中
	*********************************/	
	public function getResultArray($result){
		$array=array();
		$i=0;
		while($row=@mysql_fetch_assoc($result)){
			$array[$i]=$row;
			$i++;
		}
		return $array;
	}
	public function insert_id(){
		return mysql_insert_id();
	}
}


	
?>
