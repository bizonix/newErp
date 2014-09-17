<?php
/**
 * 类名：JobAct
 * 功能：管理Job岗位信息
 * 版本：2013-08-13
 * 作者：林正祥
 */
class JobAct{

	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;
	static $_instance;
	private $is_count = false;

	public function __construct(){
		self::$debug = C('IS_DEBUG');
	}

	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function count(){
		$this->is_count = true;
		return $this;
	}

	//显示部门信息
	public function act_getJobById($jobid){

		$jobid = intval($jobid);
		if ($jobid===0){
			self::$errCode = '5806';
			self::$errMsg  = 'jobid is error';
			return array();
		}

		$jobsingle = JobModel::getInstance();
		$filed =' job_id,job_name,job_level,job_dept_id,job_pid,job_path,job_company_id,job_path,dept_name,company_name ';
		$where = " WHERE job_id='{$jobid}'";
		$jobinfo = $jobsingle->getJobInfo($filed, $where);
		return $this->_checkReturnData($jobinfo, array());
	}

	//显示部门信息
	public function act_getJobPowerById($jobid){

		$jobid = intval($jobid);
		if ($jobid===0){
			self::$errCode = '5806';
			self::$errMsg  = 'jobid is error';
			return array();
		}

		$jobsingle = JobModel::getInstance();
		$filed =' jobpower_id,jobpower_job_id,jobpower_power,jobpower_system_id ';
		$where = " WHERE jobpower_job_id='{$jobid}' AND jobpower_system_id=7";
		$jobinfo = $jobsingle->getJobPower($filed, $where);
		return $this->_checkReturnData($jobinfo, array());
	}

	/*
	*功能：外接系统获取部门信息
	*/
	public function act_getJobLists($condition=array(), $sort='', $limit=''){

		$jobmodel = new JobModel();

		$filed =' jobpower_id,job_id,job_name,job_level,job_dept_id,job_pid,job_path,job_company_id,job_path,dept_name,company_name ';
		$where = !empty($condition)&&is_array($condition) ? 'WHERE '.implode(' AND ', $condition) : '';
		//获取条数
		if ($this->is_count===true){
			$this->is_count = false;
			$jobcount = $jobmodel->count()->getJobLists($filed, $where);
			return $this->_checkReturnData($jobcount, 0);
		}
		$sort = empty($sort) ? '' : " ORDER BY {$sort} ";
		$joblists = $jobmodel->getJobLists($filed, $where, $sort, $limit);
		return $this->_checkReturnData($joblists, array());
	}

	private function _checkReturnData($data, $errreturn){
		if ($data===false){
			self::$errCode = JobModel::$errCode;
			self::$errMsg  = JobModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = 5806;
			self::$errMsg  = 'There is no data!';
			if (self::$debug===true){
				self::$errMsg .= 'The SQL is '.JobModel::$errMsg;
			}
			return $errreturn;
		}else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
	/**
	 * JobAct::act_insert()
	 * 新增岗位act
	 * @return bool
	 */
	public function act_insert(){
		if(!isset($_POST['jobName']) || trim($_POST['jobName']) == ''){
			exit("岗位名填写非法!");
		}
		if(!isset($_POST['jobPower']) || trim($_POST['jobPower']) == ''){
			exit("所属上级非法!");
		}
		if(!isset($_POST['jobDept']) || trim($_POST['jobDept']) == '' || !intval($_POST['jobDept'])){
			exit("所属部门非法!");
		}
		$jobName	= post_check(trim($_POST['jobName']));
		$jobPower	= explode("|",post_check(trim($_POST['jobPower'])));
		$jobDept	= intval($_POST['jobDept']);
		$newJob		= array(
						'jobName'      => $jobName,//岗位名称，类型 varchar(30)，必须项
						//'jobLevel'     => '2',//岗位等级，类型 tinyint(1)，必须项
						'jobDeptId'    => $jobDept,//该岗位所属部门编号,类型tinyint(3)，必须项
						'jobPid'       => $jobPower[1],//该岗位直接主管岗位编号,类型tinyint(3)，必须项
						'jobCompanyId' => '1',//岗位对应的公司编号,类型int(5)，必须项
						//'jobPath'      => $jobPower[2],//岗位对应的公司编号,类型varchar(30)，必须项
					);
		$result		= JobModel::jobInsert($newJob);
		if(!is_numeric($result)){
			echo $result;
			exit;
		}
		$power		= array();
		$usersingle = UserModel::getInstance();//获取当前用户信息
		$filed 		= ' a.*,b.job_name,c.dept_name,d.company_name';
		$where 		= " WHERE a.user_id='{$_SESSION[C('USER_AUTH_ID')]}' ";
		$userinfo	= $usersingle->getUserInfo($filed, $where);
		$groupname	= ActionModel::actionGroupList("7");//读取系统的actiongroup列表
		foreach($groupname as $v){
			if(is_array($_POST["{$v}"]) && isset($_POST["{$v}"])){
				array_push($power,"\"{$v}\":".json_encode($_POST["{$v}"]));
			}else {
				//array_push($power,"\"{$v}\":[]");
			}
		}
		$power		= implode(",",$power);
		$power		= "{".$power."}";
		$newJobpower=array(
					'jobpowerPower' => json_decode($power, true),//岗位权限(以json存储格式)，类型 text，可选项
					'jobpowerMenu'  => json_decode('["31"]', true),//拥有的菜单权限(以json存储格式)，类型 text，可选项
					'jobpowerJobId' => $result,//所属岗位编号,类型tinyint(3)，必须项
					);
		$result		= JobModel::jobPowerInsert($newJobpower);
		return $result;
    }

	/**
	 * JobAct::act_update()
	 * 修改岗位act
	 * @return bool
	 */
	public function act_update(){
		if(!isset($_POST['jobName']) || trim($_POST['jobName']) == ''){
			exit("岗位名填写非法!");
		}
		if(!isset($_POST['jobPower']) || trim($_POST['jobPower']) == '' || !intval($_POST['jobPower'])){
			exit("所属上级非法!");
		}
		if(!isset($_POST['jobDept']) || trim($_POST['jobDept']) == '' || !intval($_POST['jobDept'])){
			exit("所属部门非法!");
		}
		if(!isset($_POST['jobId']) || trim($_POST['jobId']) == '' || !intval($_POST['jobId'])){
			exit("岗位ID非法!");
		}
		if(!isset($_POST['jobpowerId']) || trim($_POST['jobpowerId']) == '' || !intval($_POST['jobpowerId'])){
			exit("岗位权限ID非法!");
		}
		$jobName	= post_check(trim($_POST['jobName']));
		$jobPower	= intval(trim($_POST['jobPower']));
		$jobDept	= intval($_POST['jobDept']);
		$jobId		= intval($_POST['jobId']);
		$jobpowerId	= intval($_POST['jobpowerId']);
		$newJob		= array(
						'jobId'        => $jobId,//岗位编号，类型 int(5)，必须项
						'jobName'      => $jobName,//岗位名称，类型 varchar(30)，必须项
						//'jobLevel'     => '2',//岗位等级，类型 tinyint(1)，必须项
						'jobDeptId'    => $jobDept,//该岗位所属部门编号,类型tinyint(3)，必须项
						'jobPid'       => $jobPower,//该岗位直接主管岗位编号,类型tinyint(3)，必须项
						'jobCompanyId' => '1',//岗位对应的公司编号,类型int(5)，必须项
						//'jobPath'      => $jobPower[2],//岗位对应的公司编号,类型varchar(30)，必须项
					);
		$result		= JobModel::jobUpdate($newJob);
		if($result===false){
			exit;
		}
		$power		= array();
		$usersingle = UserModel::getInstance();//获取当前用户信息
		$filed 		= ' a.*,b.job_name,c.dept_name,d.company_name';
		$where 		= " WHERE a.user_id='{$_SESSION[C('USER_AUTH_ID')]}' ";
		$userinfo	= $usersingle->getUserInfo($filed, $where);
		$groupname	= ActionModel::actionGroupList("7");//读取系统的actiongroup列表
		foreach($groupname as $v){
			if(is_array($_POST["{$v}"]) && isset($_POST["{$v}"])){
				array_push($power,"\"{$v}\":".json_encode($_POST["{$v}"]));
			}else {
				//array_push($power,"\"{$v}\":[]");
			}
		}
		$power		= implode(",",$power);
		$power		= "{".$power."}";
		$newJobpower=array(
					'jobpowerId'    => $jobpowerId,//岗位权限编号，类型 int(5)，必须项
					'jobpowerPower' => json_decode($power, true),//岗位权限(以array存储格式)，类型 text，可选项
					'jobpowerMenu'  => json_decode('["31"]', true),//拥有的菜单权限(以json存储格式)，类型 text，可选项
					//'jobpowerJobId' => '2',//所属岗位编号,类型tinyint(3)，可选项
					);
		$result		= JobModel::jobPowerUpdate($newJobpower);
		return $result;
    }

	/**
	 * JobAct::act_delete()
	 * 删除岗位act
	 * @return bool
	 */
	public function act_delete(){
		$jobId		= intval($_POST['jobId']);
		$jobpowerId	= intval($_POST['jobpowerId']);
		if(!$jobId || !$jobpowerId){
			return "参数错误！";
			exit;
		}
		$result		= JobModel::jobDelete($jobId,$jobpowerId);
		return $result;
	}

}
?>