<?php
/*
 * 显示订阅邮件列表
 */
class MailShowModel {
	private $dbconn 		= NULL;
	public static $errMsg 	= '';
	public static $errCode	= 0;
	
	/*
	 * 构造函数
	*/
	public function __construct(){
		global $dbConn;
		$this->dbconn 		= $dbConn;
	}
	
	/*
	 * 显示用户已订阅邮件Model
	 */
	public function showUserMail($user_id) {
		$showData 		= array();
		$showsql		= "SELECT `mail_user_list`.`user_name_id`, `mail_user_list`.`user_list_id`, `mail_list`.`list_id`, `mail_list`.`list_name`
						   FROM `mail_user_list`, `mail_list`, `mail_power`, `power_global_user`
						   WHERE `mail_user_list`.`user_name_id`='$user_id' 
						   AND `mail_user_list`.`user_list_id`=`mail_list`.`list_id` 
						   AND `mail_power`.`power_list_id` = `mail_list`.`list_id`
						   AND `mail_power`.`power_job_id`=`power_global_user`.`global_user_job` 
						   AND `mail_power`.`power_company_id` = `power_global_user`.`global_user_company` 
						   AND `mail_power`.`power_dept_id` = `power_global_user`.`global_user_dept`
                           AND `mail_user_list`.`user_name_id` = `power_global_user`.`global_user_id`
						   AND `mail_user_list`.`user_isdelete`= 0 
						   AND `mail_list`.`list_isdelete`= 0
						   AND `mail_power`.`power_isdelete` = 0";
		$show 			= $this->dbconn->query($showsql);
		$showResult		= $this->dbconn->fetch_array_all($show);
		//print_r($showResult);exit;
		foreach($showResult as $key=>$value) {
			$showData[$value['user_list_id']]	= $value;
		}
		return $showData;
	}
	
	/*
	 * 显示用户可订阅邮件Model
	 */
	public function showMailList($user_id, $where) {
		$showData 		= array();
		$showlist		= "SELECT `mail_list`.`list_id`, `mail_list`.`list_name`, `mail_list`.`list_system_id`, `mail_list`.`list_description`, `power_system`.`system_name`, `power_global_user`.`global_user_job`, `power_global_user`.`global_user_id`, `mail_power`.`power_list_id`, `mail_power`.`power_job_id` 
						   FROM `mail_list`, `mail_power`, `power_global_user`, `power_system`
						   WHERE `power_global_user`.`global_user_id`= '$user_id' 
						   AND `mail_list`.`list_id` = `mail_power`.`power_list_id` 
						   AND `mail_power`.`power_job_id`=`power_global_user`.`global_user_job` 
						   AND `mail_power`.`power_company_id` = `power_global_user`.`global_user_company` 
						   AND `mail_power`.`power_dept_id` = `power_global_user`.`global_user_dept`
						   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
						   AND `mail_power`.`power_isdelete`=0
						   AND `mail_list`.`list_isdelete`=0 $where";
		$show 			= $this->dbconn->query($showlist);
		$showResult		= $this->dbconn->fetch_array_all($show);
		if (!empty($showResult)) {
			$showData   = $showResult;
		}
		return $showData;
	}
	
	/*
	 * 用户添加订阅内容Model
	 */
	public function addMailList($user_name_id, $user_list_id, $user_modtime) {
		$showData 		= array();
		$show			= "SELECT `user_list_id`, `user_isdelete`
						   FROM `mail_user_list`
						   WHERE `user_list_id`='$user_list_id'
						   AND `user_name_id` = '$user_name_id'";
		$showResult		= $this->dbconn->query($show);
		$showRes		= $this->dbconn->fetch_array_all($showResult);
		if (!empty($showRes)) {
			$showData   = $showRes;
			foreach($showData as $value) {
				if($value['user_isdelete'] == 1) {
					$update			= "UPDATE `mail_user_list`
									   SET `user_isdelete`=0, `user_modtime`=$user_modtime
									   WHERE `user_list_id`='$user_list_id'
									   AND `user_name_id` = '$user_name_id'";
					$result 		= $this->dbconn->query($update);
					return $result;
				}else{
					$addList		= "UPDATE `mail_user_list`
									   SET `user_modtime`=$user_modtime
									   WHERE `user_list_id`='$user_list_id'
									   AND `user_name_id` = '$user_name_id'";
					$result 		=  $this->dbconn->query($addList);
					return $result;
				}
			}
		}else{
			$addList1	= "INSERT INTO `mail_user_list`
						   VALUES (NULL, $user_name_id, $user_list_id, $user_modtime, 0)";
			$result1 	=  $this->dbconn->query($addList1);
			return $result1;
		}
	}

	/*
	 * 获取全部系统
	 */
	public function getAllSystem() {
		$getData	= array();
		$getsql		= "SELECT `system_name`, `system_id`
					   FROM `power_system`";
		$getRes		= $this->dbconn->query($getsql);
		$result		= $this->dbconn->fetch_array_all($getRes);
		if (!empty($result)) {
			$getData = $result;
		}
		return $getData;
	}
	
	/*
	 * 用户取消订阅内容Model
	 */
	public function cancelMailList($list_id, $user_id) {
		$cancelsql		= "UPDATE `mail_user_list` 
						   SET `user_isdelete`=1
						   WHERE `user_list_id`='$list_id'
						   AND `user_name_id` = '$user_id'";
		$result 		= $this->dbconn->query($cancelsql);
		return $result;
	}
	
	/*
	 * 用户可订阅邮件分页Model
	 */
	public function userMail($where, $user_id) {
		$sql 		= "SELECT count(`mail_list`.`list_id`) as num
					   FROM `mail_list`, `mail_power`, `power_global_user`, `power_system`
					   WHERE `power_global_user`.`global_user_id`= '$user_id' 
					   AND `mail_list`.`list_id` = `mail_power`.`power_list_id` 
					   AND `mail_power`.`power_job_id`=`power_global_user`.`global_user_job` 
					   AND `mail_power`.`power_company_id` = `power_global_user`.`global_user_company` 
					   AND `mail_power`.`power_dept_id` = `power_global_user`.`global_user_dept`
					   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
					   AND `mail_power`.`power_isdelete`=0
					   AND `mail_list`.`list_isdelete`=0".$where;
		$result		= $this->dbconn->fetch_first($sql);
		return $result['num'];
	}
	
	/*
	 * 用户根据条件搜索邮件Model
	 */
	public function getUserMailByCondition($and, $where, $user_id) {
		$MailData	= array();
		$getsql		= "SELECT `mail_list`.`list_id`, `mail_list`.`list_name`, `mail_list`.`list_system_id`, `mail_list`.`list_description`, `power_system`.`system_name`, `power_global_user`.`global_user_job`, `power_global_user`.`global_user_id`, `mail_power`.`power_list_id`, `mail_power`.`power_job_id` 
					   FROM `mail_list`, `mail_power`, `power_global_user`, `power_system`
					   WHERE `power_global_user`.`global_user_id`= '$user_id' 
					   AND `mail_list`.`list_id` = `mail_power`.`power_list_id` 
					   AND `mail_power`.`power_job_id`=`power_global_user`.`global_user_job` 
					   AND `mail_power`.`power_company_id` = `power_global_user`.`global_user_company` 
					   AND `mail_power`.`power_dept_id` = `power_global_user`.`global_user_dept`
					   AND `mail_list`.`list_system_id` = `power_system`.`system_id`
					   AND `mail_power`.`power_isdelete`=0
					   AND `mail_list`.`list_isdelete`=0 $and $where";
		$getRes		= $this->dbconn->query($getsql);
		$result		= $this->dbconn->fetch_array_all($getRes);
		if (!empty($result)) {
			$MailData = $result;
		}
		return $MailData;
	}
}