<?php
/**
 * 类名：AgreementModel
 * 功能：协议管理model
 * 版本：1.0
 * 日期：2014/09/11
 * 作者：杨世辉
 */

class AgreementModel {

	public static $dbConn;
	public static $errCode	= 0;
	public static $errMsg	= "";

	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}

	/**
	 * 构造 where
	 * @return string
	 */
	public static function getWhere() {
		$where = 'is_delete=0';
		//keyword and content
		$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : NULL;
		$content = isset($_GET['content']) ? trim($_GET['content']) : NULL;
		if ( $keyword != -1 and !is_null($keyword) ) {
			if ( $content != '') {
				$where .= " AND $keyword like '%{$content}%'";
			}
		}

		//公司名称
		$companyName = isset($_GET['companyName']) ? $_GET['companyName'] : NULL;
		if ($companyName != '' && !is_null($companyName)) {
			$where .= " AND companyName like '%{$companyName}%'";
		}

		//公司类型
		$companyType = isset($_GET['companyType']) ? $_GET['companyType'] : NULL;
		if ($companyType != -1 && !is_null($companyType)) {
			$where .= " AND companyType='$companyType'";
		}

		//时间
		$timeType = isset($_GET['timeType']) ? $_GET['timeType'] : NULL;
		$startTime = isset($_GET['startTime']) ? $_GET['startTime'] : NULL;
		$endTime = isset($_GET['endTime']) ? $_GET['endTime'] : NULL;
		if (($timeType != -1 && !is_null($timeType)) && ($startTime !='' || $endTime != '') ) {
			$fieldarr = array('addTime'=>'addTime','expiration'=>'expiration');
			$endTime = $endTime!='' ? $endTime.' 23:59:59' : '';
			$where .= $startTime != '' ? " AND {$fieldarr[$timeType]} >= '{$startTime}' " : '';
			$where .= $endTime != '' ? " AND {$fieldarr[$timeType]} <= '{$endTime}' " : '';
		}

		//状态
		$status = isset($_GET['status']) ? $_GET['status'] : NULL;
		if ($status != -1 && $status != NULL) {
			$where .= " AND status='$status'";
		}

		return $where;
	}

	/**
	 * 构造 limit
	 * @return string
	 */
	public static function getLimit() {
		$page = isset($_GET['page']) ? $_GET['page'] : 0;
		if($page > 0){
			$page = ($page-1) * 100;
		}
		$limit = " limit {$page},100";
		return $limit;
	}

	/**
	 * AgreementModel::getList()
	 * 获取列表
	 * @param string $where
	 * @param string $limit
	 * @return array
	 */
	public static function getList($where=null, $limit=null){
		try {
			self::initDB();
			$where = is_null($where) ? self::getWhere() : $where;
			$limit = is_null($limit)? self::getLimit() : $limit;
			$sqlStr = "select * from ph_agreement where {$where} ";
			//file_put_contents(WEB_PATH.'log/sql.txt', var_export($_GET,true)."\r\n\r\n", FILE_APPEND);//test
			//file_put_contents(WEB_PATH.'log/sql.txt', $sqlStr."\r\n\r\n", FILE_APPEND);//test
			//echo $sqlStr;exit;
			$sql = self::$dbConn->execute($sqlStr);
			$totalNum = self::$dbConn->num_rows($sql);
			$sql = $sqlStr."{$limit}";
			$sql = self::$dbConn->execute($sql);
			$listData = self::$dbConn->getResultArray($sql);
			$data = array("totalNum"=>$totalNum,"listData"=>$listData);
			return $data;
		} catch (Exception $e) {
			self::$errCode	= 10000;
			self::$errMsg	= $e->getMessage();
			return false;
		}
	}

	/**
	 * AgreementModel::getById()
	 * 获取一条记录
	 * @param int $id
	 * @return array
	 */
	public static function getById($id) {
		self::initDB();
		$sql = "select * from ph_agreement where id='{$id}' ";
		$sql = self::$dbConn->execute($sql);
		return self::$dbConn->fetch_one($sql);
	}

	/**
	 * AgreementModel::getByCompanyName()
	 * 获取一条记录
	 * @param string $companyName
	 * @return array
	 */
	public static function getByCompanyName($companyName) {
		self::initDB();
		$sql = "select * from ph_agreement where companyName='{$companyName}' ";
		$sql = self::$dbConn->execute($sql);
		return self::$dbConn->fetch_one($sql);
	}

	/**
	 * AgreementModel::getData()
	 * 获取一条记录
	 * @param string $where
	 * @param string $field
	 * @param string $order
	 * @return array
	 */
	public static function getData($where='',$field='*',$order='') {
		self::initDB();
		$where = $where == '' ? '' : ' where '.$where;
		$sql = "select {$field} from ph_agreement pp {$where} {$order} ";
		$sql = self::$dbConn->execute($sql);
		return self::$dbConn->getResultArray($sql);
	}

}
?>
