<?php
/*
 * 管理订阅邮件列表以及权限
 */
class MailManageModel {
	private $dbconn = null;
	public static $errMsg = '';
	public static $errCode = 0;
	
	/*
	 * 构造函数
	 */
	public function __construct(){
		global $dbConn;
		$this->dbconn 	= $dbConn;
	}

	/*
	 * 添加邮件分类信息Model
	 */
	public function addMailList($list_name, $modtime, $list_description, $list_english_id, $systemInfo){
		$insertsql		= "INSERT INTO `mail_list`
					  	   VALUES (NULL, '$list_name', '$modtime', '$list_english_id', '$systemInfo', '$list_description',  0)";
		$result			= $this->dbconn->query($insertsql);
		if($result) {
			$getMailId	= $this->dbconn->insert_id();
			return $getMailId;
		} else {
			echo '添加邮件信息失败！';
			return FALSE;
		}
	}
	
	/*
	 * 更新邮件分类信息Model
	 */
	public function updateMail($list_id, $list_name, $modtime, $list_description, $systemInfo) {
		$updatesql		= "UPDATE `mail_list`
						   SET `list_name`='$list_name', `list_modtime`='$modtime', `list_description`='$list_description', `list_system_id` = '$systemInfo'
						   WHERE `list_id`='$list_id'";
		$updateRes		= $this->dbconn->query($updatesql);
		return $updateRes;
	}
	
	/*
	 * 添加邮件权限信息Model
	 */
	public function addMailPower($power_list_id, $power_company_id, $power_dept_id, $power_job_id, $modtime) {
		$insertpower	= "INSERT INTO `mail_power`
				 		   VALUES (NULL, '$power_list_id', '$power_company_id', '$power_dept_id', '$power_job_id', '$modtime', 0)";
		$result			= $this->dbconn->query($insertpower);
		return $result;
	}
	
	/*
	 * 提交修改权限信息Model
	 */
	 public function addMailChange($power_list_id, $power_company_id, $power_dept_id, $power_job_id, $modtime) {
		$insertsql	= "INSERT INTO `mail_power`
			 		   VALUES (NULL, '$power_list_id', '$power_company_id', '$power_dept_id', '$power_job_id', '$modtime', 0)";
		$insertResult = $this->dbconn->query($insertsql);
		return $insertResult;
	}
	
	/*
	 * 查看邮件权限详情Model
	 */
	public function checkPower($where, $list_id) {
		$checkPower 	= array();
		$checksql		= "SELECT DISTINCT `power_global_user`.`global_user_name`, `power_global_user`.`global_user_id`, `mail_list`.`list_name`, `mail_list`.`list_system_id`, `power_system`.`system_name`, `mail_power`.`power_list_id`, `power_dept`.`dept_name`, `power_dept`.`dept_id`, `power_company`.`company_name`, `power_company`.`company_id`
						   FROM `power_global_user`, `mail_user_list`, `mail_list`, `mail_power`, `power_job`, `power_dept`, `power_company`, `power_system`
						   WHERE `mail_power`.`power_job_id` = `power_job`.`job_id` 
						   AND `power_global_user`.`global_user_job` = `mail_power`.`power_job_id` 
						   AND `mail_user_list`.`user_list_id` = `mail_power`.`power_list_id` 
						   AND `mail_list`.`list_id`= `mail_power`.`power_list_id` 
						   AND `mail_power`.`power_list_id` = '$list_id'
						   AND `mail_power`.`power_dept_id` = `power_dept`.`dept_id`
						   AND `mail_user_list`.`user_name_id` = `power_global_user`.`global_user_id`
						   AND `mail_power`.`power_company_id` = `power_company`.`company_id`
						   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
						   AND `mail_user_list`.`user_isdelete` = 0
						   AND `mail_power`.`power_isdelete` = 0
						   ORDER BY `power_company`.`company_id`, `power_dept`.`dept_id` $where";
		$result			= $this->dbconn->query($checksql);
		$showResult		= $this->dbconn->fetch_array_all($result);
		if (!empty($showResult)) {
			$checkPower = $showResult;
		}
		return $checkPower;
	}
	
	/*
	 * 查询数据库邮件英文ID-Model
	 */
	public function checkEnglishId() {
		$checkEnglish   = array();
		$checksql		= "SELECT `list_english_id`
						   FROM `mail_list`
						   WHERE `list_isdelete`=0";
		$result			= $this->dbconn->query($checksql);
		$checkResult	= $this->dbconn->fetch_array_all($result);
		if(!empty($checkResult)) {
			$checkEnglish = $checkResult;
		}
		return $checkEnglish;
	}
	
	/*
	 * 删除邮件权限Model
	 */
	public function deletePower($list_id) {
		$deletesql  = "UPDATE `mail_power`
					   SET `power_isdelete`=1
					   WHERE `power_list_id`='$list_id'";
		$result		= $this->dbconn->query($deletesql);
		if(!empty($result)) {
			return $result;
		}else{
			echo '删除邮件出错，请重新操作！';
			return FALSE;
		}
	}
	
	/*
	 * 查看全部邮件及其订阅权限Model
	 */
	public function showMailPower($where, $and){
		$showPower 		= array();
		$powersql		= "SELECT DISTINCT `mail_list`.`list_id`, `mail_list`.`list_name`, `mail_list`.`list_description`,`mail_list`.`list_system_id`, `mail_list`.`list_english_id`, `power_system`.`system_name`, `mail_power`.`power_list_id`, `mail_power`.`power_company_id`, `mail_power`.`power_dept_id`, `mail_power`.`power_job_id`, `power_company`.`company_name`, `power_company`.`company_id`, `power_dept`.`dept_name`, `power_dept`.`dept_id`, `power_job`.`job_name`, `power_job`.`job_id`
						   FROM `mail_list`, `mail_power`, `power_company`, `power_dept`, `power_job`, `power_system`
						   WHERE `mail_power`.`power_company_id` = `power_company`.`company_id` 
						   AND `mail_power`.`power_dept_id` = `power_dept`.`dept_id` 
						   AND `power_job`.`job_id` = `mail_power`.`power_job_id` 
						   AND `mail_power`.`power_list_id` = `mail_list`.`list_id`
						   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
						   AND `mail_list`.`list_isdelete` = 0
						   AND `mail_power`.`power_isdelete`= 0 $and
						   ORDER BY `mail_list`.`list_id`, `mail_power`.`power_company_id`, `mail_power`.`power_dept_id`, `mail_power`.`power_job_id` $where";
		$result			= $this->dbconn->query($powersql);
		$showResult		= $this->dbconn->fetch_array_all($result);
		if (!empty($showResult)) {
			$showPower  = $showResult;
		}
		return $showPower;
	}
	
	/*
	 * 删除邮件Model
	 */
	public function deleteMail($list_id) {
		$deleteList			= "UPDATE `mail_list`
							   SET `list_isdelete`=1
							   WHERE `list_id`='$list_id'";
		$deleteResult		= $this->dbconn->query($deleteList);
		return $deleteResult;
	}
	
	/*
	 * 获取相同邮件的权限信息
	 */
	public function showSameListPower($where){
		$show			= array();
		$showPower 		= array();
		$listid			= array();
		$powersql		= "SELECT `list_id`, `list_name`
						   FROM `mail_list`
						   WHERE `mail_list`.`list_isdelete`=0 $where";
		$result			= $this->dbconn->query($powersql);
		$showResult		= $this->dbconn->fetch_array_all($result);
		if (!empty($showResult)) {
			$show 		= $showResult;
			foreach($show as $key=>$item) {
				if($show[$key]['list_id'] != $show[$key - 1]['list_id']) {
					$listid[] 	= $item['list_id'];
				}
			}
		}
		foreach ($listid as $keyvar=>$itemvar) {
			$showInfo	= "SELECT `mail_list`.`list_id`, `mail_list`.`list_system_id`, `power_system`.`system_name`, `mail_list`.`list_name`, `power_company`.`company_id`, `power_company`.`company_name`, `power_dept`.`dept_id`, `power_dept`.`dept_name`, `power_job`.`job_id`, `power_job`.`job_name`
						   FROM (`mail_list`, `power_system`, `power_company`, `power_dept`, `power_job`)
						   LEFT JOIN `mail_power` AS a 
						   ON (`power_company`.`company_id` = a.`power_company_id` AND `power_dept`.`dept_id` = a.`power_dept_id` AND `power_job`.`job_id` = a.`power_job_id`)
						   WHERE a.`power_list_id` = '$itemvar' 
						   AND `mail_list`.`list_id` = '$itemvar' 
						   AND `mail_list`.`list_isdelete` = 0 
						   AND a.`power_isdelete`= 0
						   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
						   ORDER BY a.`power_list_id`, a.`power_company_id`, a.`power_dept_id`, a.`power_job_id`";
			$infoResult = $this->dbconn->query($showInfo);
			$showInfoResult = $this->dbconn->fetch_array_all($infoResult);
			if (!empty($showInfoResult)) {
				$showPower[]  = $showInfoResult;
			}
		}
		return $showPower;	
}
	
	/*
	 * 显示公司信息
	 */
	public function showCompany() {
		$showCompanyData	= array();
		$show			= "SELECT `company_id`, `company_name`
						   FROM `power_company`
						   ORDER BY `company_id`";
		$showCompany	= $this->dbconn->query($show);
		$showResult		= $this->dbconn->fetch_array_all($showCompany);
		if (!empty($showResult)) {
			$showCompanyData   = $showResult;
		}
		return $showCompanyData;
	}
	
	/*
	 * 显示部门
	*/
	public function showDept($company_id) {
		$showDeptData = array();
		$show			= "SELECT `power_company`.`company_id`, `power_company`.`company_name`, `power_dept`.`dept_id`, `power_dept`.`dept_name`, `power_dept`.`dept_company_id`
						   FROM `power_company`, `power_dept`
						   WHERE `power_dept`.`dept_company_id`='$company_id' 
						   AND `power_company`.`company_id`='$company_id'
						   ORDER BY `power_dept`.`dept_id`";
		$showDept	= $this->dbconn->query($show);
		$showResult		= $this->dbconn->fetch_array_all($showDept);
		if (!empty($showResult)) {
			$showDeptData   = $showResult;
		}
		return $showDeptData;
	}
	
	/*
	 * 显示岗位
	*/
	public function showJob($company_id, $dept_id) {
		$showJobData = array();
		$show		= "SELECT `power_company`.`company_id`, `power_dept`.`dept_id`, `power_job`.`job_id`, `power_job`.`job_name`
					   FROM `power_company`, `power_dept`, `power_job`
					   WHERE `power_company`.`company_id`='$company_id' 
					   AND `power_dept`.`dept_company_id`='$company_id' 
					   AND `power_dept`.`dept_id`='$dept_id' 
					   AND `power_job`.`job_dept_id`='$dept_id'
   					   ORDER BY `power_job`.`job_id`";
		$showJob	= $this->dbconn->query($show);
		$showResult	= $this->dbconn->fetch_array_all($showJob);
		if (!empty($showResult)) {
			$showJobData   = $showResult;
		}
		return $showJobData;
	}
	
	/*
	 * 根据岗位显示人员Model
	 */
	public function showUser($company, $dept, $job) {
		$showData	= array();
		$selectsql	= "SELECT `global_user_id`, `global_user_name`
					   FROM `power_global_user`
					   WHERE `global_user_company` = '$company'
					   AND `global_user_dept` = '$dept'
					   AND `global_user_job` = '$job'
					   AND `global_user_is_delete` = 0";
		$selectRes	= $this->dbconn->query($selectsql);
		$showResult	= $this->dbconn->fetch_array_all($selectRes);
		if (!empty($showResult)) {
			$showData   = $showResult;
		}
		return $showData;
	}
	
	/*
	 * 检查新增订阅用户是否有权限
	 */
	public function checkUserPower($list_id, $company, $dept, $job) {
		$powerdata	= array();
		$select		= "SELECT `power_id`
					   FROM `mail_power`
					   WHERE `power_company_id` = '$company'
					   AND `power_dept_id` = '$dept'
					   AND `power_job_id` = '$job'
					   AND `power_list_id` = '$list_id'
					   AND `power_isdelete` = 0";
		$selectRes	= $this->dbconn->query($select);
		$showResult		= $this->dbconn->fetch_array_all($selectRes);
		if (!empty($showResult)) {
			$powerdata   = $showResult;
		}
		return $powerdata;
	}
	
	/*
	 * 查看邮件权限分页处理
	*/
	public function pageSubject($where) {
		$sql 		= "SELECT count(*) AS num
					   FROM `mail_list`
					   WHERE `mail_list`.`list_isdelete`=0 $where";
		$result		= $this->dbconn->fetch_first($sql);
		return $result['num'];
	}
	
	/*
	 * 邮件详情分页处理
	 */
	public function mailDetail($where, $list_id) {
		$sql 		= "SELECT count(`user_name_id`) AS num
					   FROM `mail_user_list`
					   WHERE `user_isdelete`=0 
					   AND `user_list_id`='$list_id'".$where;
		$result		= $this->dbconn->fetch_first($sql);
		return $result['num'];
	}
}