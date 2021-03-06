<?php
class mysql{

	var $version = '';
	var $querynum = 0;
	var $link = null;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $dbcharset2 = '') {

		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw, 1)) {
			$halt && $this->halt('Can not connect to MySQL server');
		} else {
			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			$dbname && @mysql_select_db($dbname, $this->link);
		}

	}

	function select_db($dbname) {
		return mysql_select_db($dbname, $this->link);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function fetch_array_all($query, $result_type = MYSQL_ASSOC){
		$arr	=	array();
		while(1 && $ret	=	mysql_fetch_array($query, $result_type)){
			$arr[]	=	$ret;	
		}
		return $arr;
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {

		global $debug, $sqldebug, $sqlspenttimes;

		//$sql = $this->getStr($sql);

		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->link))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				//$this->close();
				//require DISCUZ_ROOT.'./config.inc.php';注释掉防止出现致命错误导致程序执行中断 add by guanyongjun 2013-09-23
				$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
				return $this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}


	// 对特殊字符串进行处理 add by xiaojinhua
	function getStr($str) {
		$tmpstr = trim($str);
		$tmpstr = strip_tags($tmpstr);
		$tmpstr = htmlspecialchars($tmpstr);
		//$tmpstr = addslashes($tmpstr);
		return $tmpstr;
	}

	function checkData($data){
		 if(is_array($data)){
			 foreach($data as $key => $v){
				 $data[$key] = checkData($v);
			 }
		 }else{
			 $data = getStr($data);
		 }
		 return $data;
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row = 0) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}

	function close() {
		return mysql_close($this->link);
	}
	
	//增加数据库是否链接检测，防止退出 add by guanyongjun 2013-10-11
	function ping(){ 
		if(!mysql_ping($this->link)){ 
			mysql_close($this->link); 
			$this->connect();
			return 1;
		}else {
			return 0;
		}		
	} 

	function halt($message = '', $sql = '') {
		if(!empty($sql)){
			$errorStr	=	"message : ".$message. ", sql: ".$sql."\r\n";
		}else{
			$errorStr	=	"message : ".$message."\r\n";
		}
        echo $errorStr;
		Log::write($errorStr,Log::ERR);
		throw new Exception($message);
	}
	
	/*************************
	 * 事务支持(必须是inodb或ndb引擎)
	 */
	function begin(){
		$this->query("SET AUTOCOMMIT=0");
		$this->query("BEGIN");
	}
	
	function commit(){
		$this->query("COMMIT");
	}
	
	function rollback(){
		$this->query("ROLLBACK");
	}

	// 兼容旧系统db class 的方法 add by xiaojinhua
	function execute($sql, $type = ''){
		$query = $this->query($sql, $type = '');
		return $query;
	}

	function fetch_one($query){
		$data = $this->fetch_array($query);
		return $data;
	}

	function getResultArray($query){
		$arr = $this->fetch_array_all($query);
		return $arr;
	}

	
}

?>
