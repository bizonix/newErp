<?php
/**
 * 类名：OpenApiModel
 * 功能：API对外接口调用model层
 * 版本：1.0
 * 日期：2014/7/21
 * 作者：管拥军
 */
 
class OpenApiModel{
	public static $dbConn;
	public static $errCode		=	0;
	public static $errMsg		=	"";
	public static $prefix;
	public static $prefixfee;
	public static $chnameArr;
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn		= $dbConn;
		self::$prefix		= C('DB_PREFIX');
	}
		
	/**
	 * OpenApiModel::getLogZip()
	 * 打包日志文件
	 * @return  json string
	 */
	public static function getLogZip(){
		require_once(WEB_PATH.'lib/pclzip.lib.php');
		$zipname 			= date('Ymd',time());
		$zipfile 			= WEB_PATH."html/temp/".$zipname.".zip";
		$obj				= new PclZip($zipfile);
		$files				= array(WEB_PATH.'log/');
		$curtime 			= date('Y-m-d H:i:s',time()); 
		//创建压缩文件
		if($obj->create($files, PCLZIP_OPT_REMOVE_PATH, WEB_PATH.'log/', PCLZIP_OPT_COMMENT, "Today's track.valun.cn log packaged!\n\npackaged time:{$curtime}")) {
			return WEB_URL.'temp/'.$zipname.'.zip';
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "打包失败！请检查相关权限";
			return false;
		}
	}
	
	/**
	 * OpenApiModel::sendMessage()
	 * 发送信息
	 * @param string $type ems手机短信，email 邮件
	 * @param string $from 发件人
	 * @param string $to 收件人
	 * @param string $content 内容
	 * @param string $title 标题
	 * @return  json string
	 */
	public static function sendMessage($type, $from, $to, $content, $title=''){
		$paramArr 			= array(
								'method' 		=> 'notice.send.message',
								'format' 		=> 'json',
								'v' 			=> '1.0',
								'username'		=> C('OPEN_SYS_USER'),
								'type'			=> $type,
								'from'			=> $from,
								'to'			=> $to,
								'content'		=> $content,
								'title'			=> urlencode($title),
								'sysName'		=> urlencode(C('AUTH_SYSNAME')),
							);
		$messageInfo		= callOpenSystem($paramArr);
		unset($paramArr);
		return $messageInfo;
	}
		
	/**
	 * OpenApiModel::getAuthCompanyList()
	 * 获取鉴权公司列表
	 * @return  array
	 */
	public static function getAuthCompanyList(){
		$paramArr = array(
			'method' 	=> 'power.user.getApiCompany.get',
			'format' 	=> 'json',
			'v' 		=> '1.0',
			'username'	=> C('OPEN_SYS_USER'),
            'sysName' 	=> C('AUTH_SYSNAME'),
            'sysToken' 	=> C('AUTH_SYSTOKEN')
		);
		$companyInfo	= callOpenSystem($paramArr);
		$companyInfo	= json_decode($companyInfo, true);
		$companyInfo	= is_array($companyInfo) ? $companyInfo : array();
		unset($paramArr);
		return $companyInfo;
	}

	/**
	 * OpenApiModel::getWebAdInfoById()
	 * 获取一个或多个网站广告内容
	 * @param int $ids 广告ID
	 * @return  array 
	 */
	public static function getWebAdInfoById($ids=0){
		self::initDB();
		$sql		= "SELECT id,topic,content FROM tracks_website_ad WHERE id IN({$ids}) AND is_enable = 0 AND is_delete = 0  ORDER BY layer ASC";
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
	 * OpenApiModel::getWebConfigAll()
	 * 获取网站所有配置信息
	 * @param int $ids 广告ID
	 * @return  array 
	 */
	public static function getWebConfigAll(){
		self::initDB();
		$data		= array();
		$sql		= "SELECT id,cKey,cValue FROM tracks_website_config WHERE is_enable = 0 AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			foreach($res as $v){
				$data[$v['cKey']]	= $v['cValue'];
			}
			return $data;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * OpenApiModel::getCountriesStandard()
	 * 获取获取全部或部分标准国家
	 * @param string $type ALL全部，CN中文，EN英文
	 * @param string $country 国家，默认空
	 * @return  array 
	 */
	public static function getCountriesStandard($type="ALL", $country="",$is_new=0){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.country.info.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'type'		=> $type,
				'country'	=> $country,
				'is_new'	=> $is_new,
			/* API应用级输入参数 End*/
		);
		$countryInfo		= callOpenSystem($paramArr);
		unset($paramArr);
		return $countryInfo;
	}
}
?>