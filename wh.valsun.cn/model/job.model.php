<?php
/**
 *类名：JobModel
 *功能：岗位管理表管理
 *版本：2013-08-13
 *作者：林正祥
 */
class JobModel{
	
	private static $dbConn;
	private static $table_job_info;			//岗位信息表
	private static $table_job_power;		//岗位权限表
	private static $table_dept_info;		//部门表
	private static $table_company_info;		//公司表
    private static $sysName 	= 'Warehouse';
    private static $sysToken 	= 'b5fa8197fd7a8727484e696ab547d031';
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $_instance;
	private $is_count = false;
	
	public function __construct(){
		self::$table_job_info 	  = C('TABLE_JOB_INFO');
		self::$table_job_power 	  = C('TABLE_JOB_POWER');
		self::$table_dept_info 	  = C('TABLE_DEPT_INFO');
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
	*方法功能：获取岗位信息
	*/
	public function getJobInfo($filed, $where){
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_job_info.' 
				LEFT JOIN '.self::$table_dept_info.' ON dept_id=job_dept_id
				LEFT JOIN '.self::$table_company_info.' ON job_company_id=company_id
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
	*方法功能：获取岗位权限信息
	*/
	public function getJobPower($filed, $where){
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_job_power.' '.$where.' LIMIT 1';
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
	public function getJobLists($filed, $where, $order='', $limit=''){
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_job_info.' 
				LEFT JOIN '.self::$table_dept_info.' ON dept_id=job_dept_id
				LEFT JOIN '.self::$table_job_power.' ON jobpower_job_id=job_id
				LEFT JOIN '.self::$table_company_info.' ON job_company_id=company_id
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
	 * JobModel::jobInsert()
	 * 新增岗位信息接口
	 * add by 管拥军 2013-08-30
	 * @return  json string
	 */
    public static function jobInsert($newJob){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newJob	= json_encode($newJob);
		$newJob	= base64_encode($newJob);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.addApiJob.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiJob',
				'newJob'  => $newJob,
                'sysName' => self::$sysName,
                'sysToken'=> self::$sysToken				
			/* API应用级输入参数 End*/
		);
		$newJob	= callOpenSystem($paramArr);
		unset($paramArr);
		$newJob	= json_decode($newJob,true);
		if($newJob['jobId']){
			return $newJob['jobId'];
		}else {
			echo $newJob['errMsg'];
			return false;
		}		
	}
	
	/**
	 * JobModel::jobPowerInsert()
	 * 新增岗位权限信息接口
	 * add by 管拥军 2013-08-30
	 * @return  json string
	 */
    public static function jobPowerInsert($newJobpower){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newJobpower	= json_encode($newJobpower);
		$newJobpower	= base64_encode($newJobpower);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.addApiJobPower.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiJobPower',
				'newJobpower' => $newJobpower,
                'sysName' => self::$sysName,
                'sysToken'=> self::$sysToken				
			/* API应用级输入参数 End*/
		);
		$newJobpower	= callOpenSystem($paramArr);
		unset($paramArr);
		$newJobpower	= json_decode($newJobpower,true);
		if($newJobpower['jobpowerId']){
			return "ok";
		}else {
			echo $newJobpower['errMsg'];
			return false;
		}		
	}
	
	/**
	 * JobModel::jobUpdate()
	 * 修改岗位信息接口
	 * add by 管拥军 2013-08-30
	 * @return  json string
	 */
    public static function jobUpdate($newJob){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newJob	= json_encode($newJob);
		$newJob	= base64_encode($newJob);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.updateApiJob.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  	=> 'updateApiJob',
				'newJob' 	=> $newJob,
                'sysName' 	=> self::$sysName,
                'sysToken' 	=> self::$sysToken				
			/* API应用级输入参数 End*/
		);
		$newJob	= callOpenSystem($paramArr);
		// echo $newJob;  
		// exit;
		unset($paramArr);
		$newJob	= json_decode($newJob,true);
		if($newJob['errCode']=='0'){
			return true;
		}else {
			echo $newJob['errMsg'];
			return false;
		}		
	}
	
	/**
	 * JobModel::jobPowerUpdate()
	 * 修改岗位权限信息接口
	 * add by 管拥军 2013-08-30
	 * @return  json string
	 */
    public static function jobPowerUpdate($newJobpower){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newJobpower	= json_encode($newJobpower);
		$newJobpower	= base64_encode($newJobpower);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.updateApiJobPower.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'updateApiJobPower',
				'newJobpower' => $newJobpower,
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken				
			/* API应用级输入参数 End*/
		);
		$newJobpower	= callOpenSystem($paramArr);
		unset($paramArr);
		$newJobpower	= json_decode($newJobpower,true);
		//exit;
		if($newJobpower['errCode']=='0'){
			return "ok";
		}else {
			echo $newJobpower['errMsg'];
			return false;
		}		
	}
	
	/**
	 * JobModel::jobDelete()
	 * 删除岗位权限信息接口
	 * add by 管拥军 2013-08-30
	 * @return  bool
	 */
    public static function jobDelete($jobId,$jobPowerId){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		//删除岗位信息
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.deleteApiJob.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action' => 'deleteApiJob',
				'jobId' => $jobId,
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$deleteJob 	= callOpenSystem($paramArr);
		unset($paramArr);
		$deleteJob 	= json_decode($deleteJob, true);
		if($deleteJob['errCode']=='0'){
			//echo "deleteJob ok";
		}else {
			echo $deleteJob ['errMsg'];
			exit;
		}
		//删除岗位权限信息
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.deleteApiJobPower.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action' => 'deleteApiJobPower',
				'jobpowerId' => $jobPowerId,
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$deleteJobpower  	= callOpenSystem($paramArr);
		unset($paramArr);
		$deleteJobpower		= json_decode($deleteJobpower, true);
		if($deleteJobpower['errCode']=='0'){
			return "ok";
		}else {
			echo $deleteJobpower['errMsg'];
			return false;
		}
	}
}
?>
	