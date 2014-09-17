<?php
/**
 * 类名：WedoApiModel
 * 功能：开放业务管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class WedoApiModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "track_wedo_sn";
	private static $wedo_order	= "track_wedo_number";
		
	/**
	 * WedoApiModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * WedoApiModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT a.*,b.global_user_name,b.global_user_register_time FROM ".self::$prefix.self::$table." as a 
					LEFT JOIN `power_global_user` as b on a.gid = b.global_user_id
					WHERE $where AND a.is_delete = 0 LIMIT $start,$pagenum";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * WedoApiModel::modWedoSnModify()
	 * 返回某个渠道的信息
	 * @param integer $id 用户权限ID
	 * @return array 结果集数组
	 */
	public static function modWedoSnModify($id){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE gid = {$id} AND is_delete = 0 LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::addWedoSn()
	 * 添加用户运德跟踪号生成信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addWedoSn($data){
		self::initDB();
		$res	= 0;
		$where	= "gid = '{$data['gid']}'"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：用户运德跟踪号生成信息已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}	
	
	/**
	 * WedoApiModel::updateWedoSn()
	 * 更新用户运德跟踪号生成信息
	 * @param integer $gid 用户权限ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateWedoSn($gid, $data){
		self::initDB();
		$res	= 0;
		$where	= "gid <> {$gid} AND wedo_sn = '{$data['wedo_sn']}'"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 20002;
			self::$errMsg	= "更新失败：用户运德跟踪号生成已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE gid = {$gid}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * WedoApiModel::delWedoSn()
	 * 用户运德跟踪号生成删除
	 * @param integer $gid 权限ID
	 * @return bool
	 */
	public static function delWedoSn($gid){
		self::initDB();
		$res 	= self::getWedoSnById($gid);
		if (!$res) {
			self::$errCode	= 10000;
			self::$errMsg	= "不存在的用户权限ID";
			return false;
		}
		$sql	= "DELETE FROM `".self::$prefix.self::$table."` WHERE gid = {$gid}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10002;
				self::$errMsg	= "删除数据失败";
				return false;
			}
		} else {
            self::$errCode	= 10003;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}	

	/**
	 * WedoApiModel::getGlobalUser()
	 * 获取一个或多个统一用户信息
	 * @param integer $uid 用户ID
	 * @return array
	 */
	public static function getGlobalUser($uid=0){
		self::initDB();
		$condition	= "1";
		if (!empty($uid)) $condition .= " AND a.global_user_id = {$uid}";
		$sql 	= "SELECT a.global_user_id,a.global_user_name,b.company_name FROM `power_global_user` as a
					LEFT JOIN `power_company` as b ON a.global_user_company = b.company_id
					WHERE {$condition} AND a.global_user_is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return self::$dbConn->fetch_array_all($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::getWedoSnById()
	 * 获取某个用户运德跟踪号生成ID信息
	 * @param integer $gid 权限ID
	 * @return array
	 */
	public static function getWedoSnById($gid){
		self::initDB();
		$sql 	= "SELECT * FROM `".self::$prefix.self::$table."` WHERE gid = {$gid}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return self::$dbConn->fetch_array($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::saveWedoOrder()
	 * 运德物流订单信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function saveWedoOrder($data){
		self::initDB();
		$wedoSn	= $data['wedoSn'];
		unset($data['wedoSn']);
		$sql 	= array2sql($data);
		$zeros	= array("0","00","00");
		$sql 	= "INSERT INTO `".self::$prefix.self::$wedo_order."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rid 	= self::$dbConn->insert_id();           
			if ($rid) {
				$trackNum	= "WD".$wedoSn.$rid."CN";
				$num		= 13 - strlen($trackNum);
				if ($num>0) $trackNum	= "WD".$wedoSn.$zeros[($num-1)].$rid."CN";
				return self::updateWedoTrackNum($rid, array("trackNumber" => $trackNum));
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::modWedoNumListCount()
	 * 返回运德物流订单某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modWedoNumListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$wedo_order." WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * WedoApiModel::updateWedoTrackNum()
	 * 更新运德物流跟踪号
	 * @param integer $id 订单ID
	 * @param string $data 数据集
	 * @return bool
	 */
	public static function updateWedoTrackNum($id, $data){
		self::initDB();
		$sql  	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$wedo_order."` SET {$sql} WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * WedoApiModel::orderWedoExport()
	 * 导出运德物流订单跟踪号信息
	 * @param string $condition 导出条件
	 * @param integer $pagesize 每页数量
	 * @return string 
	 */
	public static function orderWedoExport($condition,$pagesize=10000){
		self::initDB();
		$totalnum	= 0;
		$data		= "";
		$sql		= "SELECT count(*) AS totalnum FROM	".self::$prefix.self::$wedo_order." WHERE {$condition}";
		$query		= self::$dbConn->query($sql);
		if ($query) {
			$res			= self::$dbConn->fetch_array($query);
			$totalnum		= intval($res['totalnum']);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return "fail";
		}
		if ($totalnum==0) {
			self::$errCode	= 10001;
			self::$errMsg	= "选择的时间范围类，没有数据需要导出！";
			return "fail";
		}
		$pages	= ceil($totalnum/$pagesize);
		$filename	= 'wedo_number_info_'.$_SESSION[C('USER_AUTH_SYS_ID')];;
		$statusList	= C('TRACK_STATUS_DETAIL');
		$fileurl	= WEB_URL."temp/".$filename.".xls";
		$filepath	= WEB_PATH."html/temp/".$filename.".xls";
		for ($i=0;$i<$pages;$i++) {
			$offset	= $i*$pagesize;
			$sql	= "SELECT *	FROM ".self::$prefix.self::$wedo_order." WHERE {$condition} LIMIT {$offset},{$pagesize}";
			$query	= self::$dbConn->query($sql);
			$res	= self::$dbConn->fetch_array_all($query);
			if ($i==0) 	$data	= '<?xml version="1.0" encoding="UTF-8"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40"><Styles><Style ss:ID="sDT"><NumberFormat ss:Format="Short Date"/></Style></Styles><Worksheet ss:Name="Sheet1"><Table><Row><Cell><Data ss:Type="String">订单号/交易号</Data></Cell><Cell><Data ss:Type="String">跟踪号</Data></Cell><Cell><Data ss:Type="String">发货时间</Data></Cell><Cell><Data ss:Type="String">发往国家</Data></Cell><Cell><Data ss:Type="String">店铺帐号</Data></Cell></Row>'."\n";
			foreach($res as $v) {
				$data	.= '<Row><Cell><Data ss:Type="String">'.$v['orderSn'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['trackNumber'].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.strftime("%Y-%m-%dT%H:%M:%S",$v['scanTime']).'</Data></Cell><Cell><Data ss:Type="String">'.$v['toCountry'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['platAccount'].'</Data></Cell></Row>'."\n";
			}
			if ($i==0) {
				write_w_file($filepath,$data);
			} else {
				write_a_file($filepath,$data);
			}
			$data	= "";
		}
		$data	= "</Table></Worksheet></Workbook>";
		write_a_file($filepath,$data);
		if (file_exists($filepath)) {
			return $fileurl;
		} else {	
			return "fail";
		}		
	}
}
?>