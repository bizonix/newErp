<?php
/**
 * 类名：TrackEmailStatModel
 * 功能：跟踪邮件数据（CRUD）层
 * 版本：2.0
 * 日期：2014/07/11
 * 作者：管拥军
 */
 
class TrackEmailStatModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "email_stat";
	
	/**
	 * TrackEmailStatModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackEmailStatModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$idArr	= array();
		$ids	= '';
		$sql	= "SELECT id FROM ".self::$prefix.self::$table." AS a
					WHERE {$where} AND a.is_delete = 0
					ORDER BY id DESC
					LIMIT {$start},{$pagenum}";
		$query	= self::$dbConn->query($sql);
		$res	= self::$dbConn->fetch_array_all($query);
		foreach($res as $v) {
			$idArr[]		= $v['id'];
		}
		if(empty($idArr)) {
			self::$errCode	= 10001;
			self::$errMsg	= "获取数据失败";
			return false;
		} else {
			$ids			= implode(",",$idArr);
		}
		$sql 				= "SELECT * FROM ".self::$prefix.self::$table." WHERE id IN({$ids}) ORDER BY id DESC";
		$query				= self::$dbConn->query($sql);
		if($query) {
			$res			= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackEmailStatModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 				= "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query				= self::$dbConn->query($sql);
		if($query) {
			$data			= self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackEmailStatModel::trackEmailInfo()
	 * 获取某个运输方式需要推送的跟踪邮件列表
	 * @param int $carrier 运输方式ID
	 * @param int $platForm 平台名称
	 * @param int $markTime 标记发货时间
	 * @param int $page 页码
	 * @param int $pagenum 每页多少条
	 * @return array
	 */
	public static function trackEmailInfo($carrier=61, $platForm='aliexpress', $markTime=0, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT 
					a.trackNumber,a.toUserId,a.toUserEmail,a.toMarkTime,a.platAccount,a.toEmailSend,
					b.userEmail,a.recordId,b.userName,
					c.smtpHost,c.smtpPort,c.smtpUser,c.smtpPwd
					FROM trans_track_number AS a
					INNER JOIN trans_email_account AS b ON a.platAccount = b.platAccount
					INNER JOIN trans_smtp_account AS c ON a.platAccount = c.platAccount
					WHERE a.carrierId = '{$carrier}' AND a.toMarkTime > '{$markTime}' AND a.platForm = '{$platForm}' AND a.toEmailSend = 0 AND a.is_delete = 0 AND b.is_delete = 0 ORDER BY a.id ASC LIMIT {$start},{$pagenum}";
		echo "\n\n".$sql."\n\n";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}

	/**
	 * TrackEmailStatModel::trackEmailTemplat()
	 * 获取某个平台跟踪邮件模版
	 * @param int $platForm 平台名称
	 * @return array
	 */
	public static function trackEmailTemplat($platForm){
		self::initDB();
		$sql 		= "SELECT title,content FROM trans_email_template WHERE platForm = '{$platForm}' AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackEmailStatModel::sendTrackEmail()
	 * 发送跟踪邮件
	 * @param array $data 邮件内容
	 * @return bool
	 */
	public static function sendTrackEmail($data){
		require_once(WEB_PATH.'lib/ses/SimpleEmailService.php');
		require_once(WEB_PATH.'lib/ses/SimpleEmailServiceRequest.php');
		require_once(WEB_PATH.'lib/ses/SimpleEmailServiceMessage.php');
		$smtpUser		= $data['smtpUser'];
		$smtpPwd		= $data['smtpPwd'];
		$smtpHost		= $data['smtpHost'];
		$title			= $data['title'];
		$content		= $data['content'];
		$toUserEmail	= $data['toUserEmail'];
		$toUserId		= $data['toUserId'];
		$userEmail		= $data['userEmail'];
		$userName		= $data['userName'];
		$trackNum		= $data['trackNumber'];
		$retryCount		= $data['retryCount'];
		// print_r($data);
		// exit;
		//初始化邮件发送
		$ses 			= new SimpleEmailService($smtpUser, $smtpPwd);
		$m 				= new SimpleEmailServiceMessage();
		$m->addTo(array("{$toUserId} <{$toUserEmail}>")); //收件人
		$m->setFrom("{$userName} <{$userEmail}>"); //发件人
		$m->setSubject($title); // 邮件标题
		$m->setMessageFromString(NULL, $content); //内容
		//设置标题和内容编码
		$m->setSubjectCharset('UTF-8');
		$m->setMessageCharset('UTF-8');
		$res		= $ses->sendEmail($m);
		//发送邮件
		if(!empty($res['MessageId'])) {
			$flag 	= TransOpenApiModel::updateTrackNumber($trackNum, array("toEmailSend"=>1));
			if($flag) {
				$res['sendFlag'] = 1;
			} else {
				$res['sendFlag'] = 2;
			}
		} else {
			$where				= "trackNumber = '{$trackNum}' AND retryCount>='{$retryCount}' AND is_success = 0"; 
			$result				= self::checkRetryCount($where);
			$counts				= isset($result['retryCount']) ? intval($result['retryCount']) : 0;
			if($counts >= $retryCount) {
				$flag 			= TransOpenApiModel::updateTrackNumber($trackNum, array("toEmailSend"=>2));
			}
			$res['sendFlag'] 	= 0;
		}
		return $res;
	}
	
	/**
	 * TrackEmailStatModel::saveTrackEmail()
	 * 保存跟踪邮件
	 * @param array $data 邮件内容
	 * @return bool
	 */
	public static function saveTrackEmail($data){
		self::initDB();
		$res			= 0;
		$trackNumber	= $data['trackNumber'];
		$where			= "trackNumber = '{$trackNumber}'"; 
        $res			= self::modListCount($where);
		if($res > 0) {
			unset($data['addTime']);
			$data['lastTime']	= time();
			$sql	= array2sql($data);
			$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql.",retryCount = retryCount+1 WHERE trackNumber = '{$trackNumber}'";
		} else {
			$sql	= array2sql($data);
			$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql;
		}
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return 0;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return 0;
		}
	}
	
	/**
	 * TrackEmailStatModel::updateTrackEmail()
	 * 更新跟踪邮件信息
	 * @param string $tid 跟踪号
	 * @param array $data 数据集
	 * @return bool
	 */
	public static function updateTrackEmail($tid, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE trackNumber = '{$tid}'";; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TrackEmailStatModel::checkRetryCount()
	 * 检查邮件发送重试次数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function checkRetryCount($where){
		self::initDB();
		$sql 		= "SELECT retryCount,trackNumber FROM ".self::$prefix.self::$table." WHERE {$where} AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
}
?>