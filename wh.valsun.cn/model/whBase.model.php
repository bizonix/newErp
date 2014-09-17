<?php
/*
 * 仓库新流程基础Model
 * ADD BY cmf 2014.7.22
 */
class WhBaseModel extends CommonModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = '';
	public static $tablename = '';

	/**
	 * 初始化DB对象
	 */
	public static function initDB() {
		if(!self :: $dbConn){
			global $dbConn;
			self :: $dbConn = $dbConn;
			mysql_query('SET NAMES UTF8');	
		}
		self :: getTableName();
	}
	
	/**
	 * 当前模型对应表名
	 */	
	public static function getTableName(){
		$classname = get_called_class();
		$classname = substr($classname, 0, strlen($classname)-5);
		$classname = preg_replace("/[A-Z]/", "_\\0", $classname);
		$classname = trim($classname, '_');
		$classname = strtolower($classname);
		self :: $tablename = $classname;
		return $classname;
	}
	
	/*
		将编号解码成ID
		@param  $number 编号 如: W000000001
		@return  返回 数字ID
		@author cmf
	*/
	public static function number_decode($number){
		$number = preg_replace('/[^0-9]/', '', $number);
		$number = ltrim($number, '0');
		return $number;
	}
	
	/*
		将数字ID编码成编号
		@param $id  数字ID
		@param $length  编码长度（不包含前缀字母）
		@param $pre  编码前缀字母
		@param $char 编码前面填充字符
		@return 返回编码如: N0000000001
		@author cmf
	*/
	public static function number_encode($id, $length= 10, $pre = 'N', $char = '0'){
		$number = $pre.str_pad($id, $length, $char, STR_PAD_LEFT);
		return $number;
	}
	
    /*
     * 查询当前模型数据列表
     * @param 查询条件 $where: 支持参数类型: 1.数字ID  2.SQL条件字符串  3:数组
     * @param 查询字段 $field: 默认'*' 返回所有字段
     * 查询成功 返回查询结果数组$data = array(0=>array('id'=>1,...))，查询失败返回 false
     */
	public static function select($where = '1=1', $fields = '*') {
		self :: initDB();
		//$wheresql[] = ' where';
//		if(!$where){
//			$where = "1=1";
//		}
		//if(is_numeric($where)){
//			$wheresql[] = 'id='.$where;
//		}else if(is_array($where)){
//			foreach($where as $key => $val){
//				$whereArr[] = $key."='".$val."'";
//			}
//			$wheresql[] = implode(' AND ', $whereArr);
//		}else{
//			$wheresql[] = $where;
//		}
        $where  =   array2where($where);
        $fields =   array2select($fields);
        
		$sql = "select $fields from ".self::$tablename.' where '.$where;
        //echo $sql;exit;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
    /*
     * 查询当前模型一条数据
     * @param 查询条件 $where: 支持参数类型: 1.数字ID  2.SQL条件字符串  3:数组
     * @param 查询字段 $field: 默认'*' 返回所有字段
     * 查询成功 返回查询结果$data = array('id'=>1,...)，查询失败返回 false
     */
	public static function find($where, $fields = '*') {
		self :: initDB();
		$wheresql[] = ' where';
		if(is_numeric($where)){
			$wheresql[] = 'id='.$where;	
		}else if(is_array($where)){
			foreach($where as $key => $val){
				$whereArr[] = $key."='".$val."'";
			}
			$wheresql[] = implode(' AND ', $whereArr);
		}else{
			$wheresql[] = $where;
		}
		$sql = "select $fields from ".self::$tablename.implode(' ', $wheresql)." limit 1";
		$result = self :: $dbConn->fetch_first($sql);
		if ($result) {
			return $result; //成功， 返回数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
    /*
     * 查询当前模型数量
     * @param 查询条件 $where: 支持参数类型: 1.数字ID  2.SQL条件字符串  3:数组
     * 查询失败返回 0
     */
	public static function count($where = '', $field = '*') {
		self :: initDB();
		if(!$where)$where = '1=1';
		$wheresql[] = ' where';
		if(is_numeric($where)){
			$wheresql[] = 'id='.$where;	
		}else if(is_array($where)){
			foreach($where as $key => $val){
				$whereArr[] = $key."='".$val."'";
			}
			$wheresql[] = implode(' AND ', $whereArr);
		}else{
			$wheresql[] = $where;
		}
		$sql = "select count(".$field.") from ".self::$tablename.implode(' ', $wheresql);
		$result = self :: $dbConn->result_first($sql);
		if ($result) {
			return $result; //成功， 返回结果
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return 0; //失败则设置错误码和错误信息， 返回0
		}
	}
	
    /*
     * 查询当前模型一条数据
     * @param 查询条件 $sql: SQL查询语句
     * 查询失败返回 false
     */
	public static function query($sql) {
		self :: initDB();
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 数据删除方法（逻辑删除）
	 * @param 删除条件 $where: 支持参数类型: 1.数字ID  2.SQL条件字符串  3:数组
	 * @param 删除状态 $delete: 1 - 删除   0 - 恢复删除
	 * 删除成功返回true, 失败返回 false
	 */
	public static function delete($where, $delete = 1) {
		$result = self :: update('is_delete='.$delete, $where);
		return $result ? true : false;
	}
	
	public static function begin(){
		self :: initDB();
		self :: $dbConn->begin();
	}
	
	public static function commit(){
		self :: initDB();
		self :: $dbConn->commit();
	}
	
	public static function rollback(){
		self :: initDB();
		self :: $dbConn->rollback();
	}
	
	/*
	 * 数据新增方法
	 * @param 数据字段 $data = array('字段名' => 值);
	 * 新增成功返回ID, 失败返回false
	 */
	public static function insert($data, $many = false) {
		self :: initDB();
		if(!$data){
			self :: $errCode = "001";
			self :: $errMsg = "参数错误";
			return false;
		}
		if($many){
			$colnum = 0;
			$curcol = 0;
			$notallow = false;
			$fields = array();
			foreach($data as $row){
				$temp_val = array();
				if(!$colnum){
					$colnum = count($row);
				}
				$curcol = count($row);
				if($curcol != $colnum){
					$notallow = true;
				}
				foreach($row as $key => $val){
					if(!$fields){
						$temp_fields[] = $key;
					}
					$temp_val[] = $val;
				}
				$fields = $temp_fields;
				$values[] = "('".implode("','", $temp_val)."')";
			}
			if($notallow){
				self :: $errCode = "003";
				self :: $errMsg = "数据结构不符";
				return false;
			}
			$sql = 'INSERT INTO `'.self::$tablename.'`(`'.implode('`,`', $fields)."`) VALUES".implode(',', $values).';';
		}else{
			foreach($data as $key => $val){
				$fields[] = $key;
				$values[] = $val;
			}
			$sql = 'INSERT INTO `'.self::$tablename.'`(`'.implode('`,`', $fields)."`) VALUES('".implode("','", $values)."')";
		}
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$id = self :: $dbConn->insert_id();
			$id = $many ? true : ($id==0 ? true : $id);
			return $id;
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "新增数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 数据更新方法
	 * @param 更新数据 $data = array('字段名' => 值);
	 * @param 更新条件 $where: 支持参数类型: 1.数字ID  2.SQL条件字符串  3:数组
	 * 更新成功返回true, 失败返回 false	 
	 */
	public static function update($data, $where) {
		self :: initDB();
		if(!$where || !$data){
			self :: $errCode = "001";
			self :: $errMsg = "参数错误";
			return false;
		}
		//if(is_array($data)){
//			foreach($data as $key => $val){
//				$sqlArr[] = $key."='".$val."'";
//			}
//		}else{
//			$sqlArr[] = $data;
//		}
//		if(is_numeric($where)){
//			$whereArr[] = 'id='.$where;
//		}else if(is_array($where)){
//			foreach($where as $key => $val){
//				$whereArr[] = $key."='".$val."'";
//			}
//		}else{
//			$whereArr[] = $where;
//		}
        $data   =   array2sql($data);
        $where  =   array2where($where);
        
		$sql = 'update '.self::$tablename.' set '.$data.' where '.$where;
        //echo $sql."<br />";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "更新数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 生成文件缓存
	 * @param $key 缓存名
	 * @param $data 缓存内容
	 * @author cmf
	 */	
    public function cache($key, $data = array(), $cachetime = 0){
    	$file = WEB_PATH.'html/temp/cache_'.md5($key).'.php';
    	if($data){
    		$newdata = array(
    			'cachetime' => $cachetime,
    			'time' => time(),
    			'data' => $data
    		);
	    	$content = json_encode($newdata);
	    	$return = file_put_contents($file, $content);
	    }else{
	    	if(file_exists($file)){
		    	if($data === NULL){
		    		@unlink($file);
		    		return true;
		    	}
	    		$content = file_get_contents($file);
	    		$cachecontent = json_decode($content, true);
	    		if($cachecontent['cachetime'] && time() - $cachecontent['time'] > $cachecontent['cachetime']){
	    			@unlink($file);
	    			return false;
	    		}
	    		$return = $cachecontent['data'];
	    	}else{
	    		$return = false;	
	    	}
	    }
	    return $return;
    }
    
    /**
     * WhBaseModel::affected_rows()
     * 返回mysql语句影响的行数。
     * @author Gary 
     * @return void
     */
    public static function affected_rows(){
        self::initDB();
        return self::$dbConn->affected_rows();
    }	

}
?>
