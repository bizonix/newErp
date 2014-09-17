<?php
/*
 * 对外提供对应邮件收件人Api接口
 */
class MailApiModel {
	/*
	 * 查询邮件订阅组收件人列表
	 */
	private $dbconn 		= null;
	public static $errMsg 	= '';
	public static $errCode 	= 0;

	public function __construct() {
		global $dbConn;
		$this->dbconn 		= $dbConn;
	}

	/*
	 * 获取邮件权限详情
	 */
	public function checkPower($list_english_id) {
		$getUserList 	= array();
		$sql			= "SELECT DISTINCT `power_global_user`.`global_user_name`, `power_global_user`.`global_user_id`, `power_global_user`.`global_user_email`
						   FROM `power_global_user`, `mail_user_list`, `mail_list`, `mail_power`, `power_job`, `power_dept`, `power_company`
						   WHERE `mail_power`.`power_job_id` = `power_job`.`job_id`
						   AND `power_global_user`.`global_user_job` = `mail_power`.`power_job_id`
						   AND `mail_user_list`.`user_list_id` = `mail_power`.`power_list_id`
						   AND `mail_list`.`list_id`= `mail_power`.`power_list_id`
						   AND `mail_list`.`list_english_id` = '$list_english_id'
						   AND `mail_power`.`power_dept_id` = `power_dept`.`dept_id`
						   AND `mail_user_list`.`user_name_id` = `power_global_user`.`global_user_id`
						   AND `mail_power`.`power_company_id` = `power_company`.`company_id`
						   AND `mail_user_list`.`user_isdelete` = 0
						   ORDER BY `power_dept`.`dept_id`, `power_company`.`company_id`";
		$query			= $this->dbconn->query($sql);
		$ret			= $this->dbconn->fetch_array_all($query);
		if(!empty($ret)) {
			return $ret;
		}
	}

	/**
	 * MailApiModel::sendMessage()
	 * 发送信息
	 * @param string $type ems手机短信，email 邮件
	 * @param string $from 发件人
	 * @param string $to 收件人
	 * @param string $content 内容
	 * @param string $title 标题
	 * @return  json string
	 */
	public static function sendMessage($type, $from, $to, $content, $title=''){
		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' 		=> 'notice.send.message',
				'format' 		=> 'json',
				'v' 			=> '1.0',
				'username'	 	=> C('OPEN_SYS_USER'),
				/* API系统级参数 End */
				/* API应用级输入参数 Start*/
				'type'			=> $type,
				'from'			=> $from,
				'to'			=> $to,
				'content'		=> $content,
				'title'			=> urlencode($title),
				'sysName'		=> urlencode(C('AUTH_SYSNAME')),
				/* API应用级输入参数 End*/
		);
		$messageInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		return $messageInfo;
	}
}