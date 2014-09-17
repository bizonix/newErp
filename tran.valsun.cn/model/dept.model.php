<?php
/**
 *类名：Dept
 *功能：处理部门表信息
 *版本：2013-08-12
 *作者：林正祥
 */

class DeptModel{
	
	private static $dbConn;
	private static $table_dept_info;//表名
	private static $table_company_info;//表名
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $_instance;
	private $is_count = false;
	
	public function __construct(){
		self::$table_dept_info 	= C('TABLE_DEPT_INFO');
		self::$table_company_info = C('TABLE_COMPANY_INFO');
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
	*方法功能：获取部门编号和名称与公司名称
	*/
	public function getDeptInfo($filed, $where)
	{
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_dept_info.'
				LEFT JOIN '.self::$table_company_info.' ON dept_company_id=company_id
				'.$where.' LIMIT 1';
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		
		return self::$dbConn->fetch_array($query);
	}
	
	/*
	*方法功能：获取部门编号和名称
	*/
	public function getDeptLists($filed, $where, $order='', $limit=''){
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_dept_info.'
				LEFT JOIN '.self::$table_company_info.' ON dept_company_id=company_id 
				'.$where.' '.$order.' '.$limit;
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		
		if ($this->is_count===true){
			$this->is_count = false;
			return self::$dbConn->num_rows($query);
		}
		
		return self::$dbConn->fetch_array_all($query);
	}
	
	/**
	 * DeptModel::deptInsert()
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-31
	 * @return  string
	 */
    public static function deptInsert($newDept){
		$newDept	= json_encode($newDept);
		$newDept	= base64_encode($newDept);
		$paramArr 	= array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.addApiDept.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiDept',
				'newDept' => $newDept,  
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$addApiDeptInfo		= callOpenSystem($paramArr);
		unset($paramArr);
		$addApiDeptInfo		= json_decode($addApiDeptInfo,true);
		if($addApiDeptInfo['deptId']){
			return "ok";
		}else {
			return $addApiDeptInfo['errMsg'];
		}
	}
	
	/**
	 * DeptModel::deptUpdate()
	 * 修改部门走开放系统
	 * add by 管拥军 2013-08-31
	 * @return  string
	 */
    public static function deptUpdate($newDept){
		$newDept	= json_encode($newDept);
		$newDept	= base64_encode($newDept);
		$paramArr 	= array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.updateApiDept.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'updateApiDept',
				'newDept' => $newDept,  
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$updateApiDeptInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$updateApiDeptInfo 	= json_decode($updateApiDeptInfo, true);
		if($updateApiDeptInfo['errCode']=='0'){
			return "ok";
		}else {
			return $updateApiDeptInfo['errMsg'];
		}
	}
	
	/**
	 * DeptModel::deptDelete()
	 * 删除部门走开放系统
	 * add by 管拥军 2013-08-31
	 * @return  string
	 */
    public static function deptDelete($deptId){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.deleteApiDept.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'	=> 'deleteApiDept',
				'deptId'	=> $deptId,				
                'sysName' 	=> C('AUTH_SYSNAME'),
                'sysToken' 	=> C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$deleteApiDeptInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$deleteApiDeptInfo	= json_decode($deleteApiDeptInfo,true);
		if($deleteApiDeptInfo['errCode']=='0'){
			return "ok";
		}else {
			return $deleteApiDeptInfo['errMsg'];
		}
	}
}
?>