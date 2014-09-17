<?php
/**
 * 类名：TrackInquiryModel
 * 功能：运德物流系统查询数据（CRUD）层
 * 版本：1.0
 * 日期：2014/01/17
 * 作者：管拥军
 */
 
class TrackInquiryModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	public static $logFile		= "";
	private static $tab_stat	= "access_statistics";
	private static $tab_ban		= "access_ban";
	private static $tab_noban	= "access_noban";
	private static $tab_wode	= "track_number_detail_61";
	
	
	/**
	 * TrackInquiryModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}

	/**
	 * TrackInquiryModel::showWedoInfo()
	 * 获取运德物流跟踪号信息
	 * @param array $tracknum 跟踪号
	 * @return array 结果集数组
	 */
	public static function showWedoInfo($tracknum){
		self::initDB();
		$sql 		= "SELECT * FROM `".self::$prefix.self::$tab_wode."` WHERE trackNumber = '{$tracknum}'"; 
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
	 * TrackInquiryModel::saveWedoInfo()
	 * 生成运德物流跟踪号信息
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function saveWedoInfo($data){
		self::initDB();
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `".self::$prefix.self::$tab_wode."` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TrackInquiryModel::updateStatInfo()
	 * 更新访问统计信息
	 * @param int $ipNum ip数值
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateStatInfo($ipNum, $data){
		self::initDB();
		$res		= self::showIpStat($ipNum);
		$sql 		= array2sql($data);
		if($res) {
			$sql 	= "UPDATE `".self::$prefix.self::$tab_stat."` SET ".$sql." WHERE ipNum = '{$ipNum}'"; 
		} else {
			$sql 	= "INSERT INTO `".self::$prefix.self::$tab_stat."` SET ".$sql; 
		}
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "更新数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TrackInquiryModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 	= "SELECT count(*) FROM ".self::$prefix.self::$tab_wode." WHERE $where";
		$query	= self::$dbConn->query($sql);
		$res	= self::$dbConn->fetch_row($query);
		if($res) {
			return $res[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackInquiryModel::showIpStat()
	 * 返回某个IP访问详情
	 * @param int $ipNum ip整数型
	 * @return array  
	 */
	public static function showIpStat($ipNum){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$tab_stat." WHERE ipNum = '{$ipNum}'";
		$query	= self::$dbConn->query($sql);
		$res	= self::$dbConn->fetch_array($query);
		if($res) {
			return $res;
		} else {
			return array();
		}
	}
	
	/**
	 * TrackInquiryModel::trackNameList()
	 * 获取跟踪运输方式列表
	 * @param int $id 待定
	 * @return json string
	 */
	public static function trackNameList($id=10000){
		$data		= array();
		$paramArr 	= array(
			'method' 	=> 'trans.track.carrier.name.get',   //API名称
			'format' 	=> 'json',   //返回格式
			'v' 		=> '1.0',    //API版本号
			'username'	=> C('OPEN_SYS_USER'), 
			'id'	 	=> $id, 
		);
		$res		= callOpenSystem($paramArr);
		unset($paramArr);
		$res		= json_decode($res, true);
		$trackNames	= C("TRACK_NAME");
		if(isset($res['data'])) {
			array_push($data, array("trackNameEN"=>'运德物流', "trackName"=>$trackNames['运德物流']));
			foreach ($res['data'] as $v) {
				if(in_array($v['trackName'], array('圆通', '顺丰', '申通', '韵达', '敦豪小包'))) continue;
				array_push($data, array("trackNameEN"=>$v['trackName'], "trackName"=>$trackNames[$v['trackName']]));
			}
		} else {
			array_push($data, array("trackNameEN"=>"no data", "trackName"=>"no data"));
		}
		return $data;
	}
	
	/**
	 * TrackInquiryModel::trackInfo()
	 * 查询跟踪信息
	 * @param string $carrier 运输方式名称
	 * @param string $tracknum 跟踪号
	 * @param string $lan 语言，默认10000
	 * @return json string
	 */
	public static function trackInfo($carrier, $tracknum, $lan=10000){
		$data 			= array();
		//获取追踪信息
		$paramArr 		= array(
			'method' 	=> 'trans.tracknum.simple.info.get',   //API名称
			'format' 	=> 'json',   //返回格式
			'v' 		=> '1.0',    //API版本号
			'username'	=> C('OPEN_SYS_USER'), 
			'tracknum'	=> $tracknum, 
		);
		$trackInfos		= callOpenSystem($paramArr);
		unset($paramArr);
		$res			= json_decode($trackInfos, true);
		$scanTime 		= isset($res['data'][0]['scanTime']) ? $res['data'][0]['scanTime'] : '';
		$orderSn 		= isset($res['data'][0]['orderSn']) ? $res['data'][0]['orderSn'] : '';
		$toCountry 		= isset($res['data'][0]['toCountry']) ? $res['data'][0]['toCountry'] : '';
		$trackEn 		= !empty($res['data'][0]['trackEn']) ? $res['data'][0]['trackEn'] : '';
		$userName		= isset($res['data'][0]['userName']) ? $res['data'][0]['userName'] : '';
		$userEmail		= isset($res['data'][0]['userEmail']) ? $res['data'][0]['userEmail'] : '';
		$platForm		= isset($res['data'][0]['platForm']) ? $res['data'][0]['platForm'] : '';
		$addCode		= isset($res['data'][0]['addCode']) ? $res['data'][0]['addCode'] : '';
		if(in_array($toCountry,array('CHINA','China'),true)) {
			$fromCounty		= '';
			$toCountry		= '';
			$toCity 		= '';
			$realWeight		= '';
			$scanTime		= '';
			$userName		= '';
			$userEmail		= '';
			$platForm		= '';
		} else {
			$fromCounty		= 'China';
			$toCity 		= isset($res['data'][0]['toCity']) ? $res['data'][0]['toCity'] : '';
			$realWeight		= isset($res['data'][0]['weight']) ? $res['data'][0]['weight'] : '';
		}
		$data['trackInfo']	= array();
		if(empty($scanTime)) {
			$fromCounty = '';
			$userName		= '';
			$userEmail		= '';
			$platForm		= '';
		} else {
			if(in_array($addCode,array('China','ShenZhen'),true)) {
				//加入公司发货时间节点信息
				array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Order entry processing center', "trackTime"=>date('Y-m-d H:i:s', $scanTime-(6*3600)+rand(60,300)), "stat"=>0)); //进入ERP系统节点
				array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Awaiting packaging', "trackTime"=>date('Y-m-d H:i:s', $scanTime-(2*3600)+rand(60,300)), "stat"=>0)); //等待配货节点
				array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Departure Scan', "trackTime"=>date('Y-m-d H:i:s', $scanTime), "stat"=>0));
			}
		}
		$paramArr 		= array(
			'method' 	=> 'trans.track.info.get',   //API名称
			'format' 	=> 'json',   //返回格式
			'v' 		=> '1.0',    //API版本号
			'username' 	=> C('OPEN_SYS_USER'), 
			'type'		=> $carrier, 
			'tid'	 	=> $tracknum, 
			'lan'	 	=> $lan, 
		);
		$res		= callOpenSystem($paramArr);
		unset($paramArr);
		$res		= json_decode($res, true);
		$res		= json_decode($res['data'], true);
		$status		= isset($res['Response_Info']) ? $res['Response_Info']['status'] : 0;
		$file_cz	= "";
		$file_fh	= "";
		if(isset($res['trackingEventList'])) {
			$times	= strtotime("-2 months".' 00:00:01');
			if($scanTime >= $times) {
				$file_cz 	= WEB_PIC_URL."cz/".date('Y/m/d', $scanTime)."/cz_".$orderSn.".jpg";
				$file_fh 	= WEB_PIC_URL."fh/".date('Y/m/d', $scanTime)."/fh_".$orderSn.".jpg";
			}
			foreach ($res['trackingEventList'] as $v) {
				$event		= rawurlencode($v['details']);
				array_push($data['trackInfo'], array("postion"=>$v['place'], "event"=>$event, "trackTime"=>$v['date'], "stat"=>$status));
			}
		} else {
			//暂时注释掉
			// if($res['ReturnValue'] == '-1') {
				// array_push($data['trackInfo'], array("postion"=>"No data", "event"=>"No data", "trackTime"=>"No data", "stat"=>$status));
			// } else {
				// array_push($data['trackInfo'], array("postion"=>"server", "event"=>"Time out", "trackTime"=>date('Y-m-d H:i:s', time()), "stat"=>$status));
			// }
		}			
		if(count($data['trackInfo']) == 0) {
			array_push($data['trackInfo'], array("postion"=>"No data", "event"=>"No data", "trackTime"=>"No data", "stat"=>$status));
		}
		if(in_array($data['trackInfo'][0]['postion'],array('server','No data'),true)) {
			$data['extInfo']	= array("fromCounty"=>'', "toCounty"=>'', "toCity"=>'', "realWeight"=>'', "file_cz"=>'', "file_fh"=>'', "trackEn"=>$trackEn, "userName"=>$userName, "userEmail"=>$userEmail, "platForm"=>$platForm, "addCode"=>$addCode);
		} else {
			$data['extInfo']	= array("fromCounty"=>$fromCounty, "toCounty"=>$toCountry, "toCity"=>$toCity, "realWeight"=>$realWeight, "trackEn"=>$trackEn, "userName"=>$userName, "userEmail"=>$userEmail, "platForm"=>$platForm, "addCode"=>$addCode, "file_cz"=>$file_cz, "file_fh"=>$file_fh);
		}
		return $data;
	}
	
	/**
	 * TrackInquiryModel::trackInfoEn()
	 * 查询目的地跟踪信息
	 * @param string $carrier 运输方式名称
	 * @param string $tracknum 跟踪号
	 * @param string $lan 语言，默认10000
	 * @return json string
	 */
	public static function trackInfoEn($carrier, $tracknum, $lan=10000){
		$data 			= array();
		// 抓取目的国跟踪信息
		$data['trackInfoEn'] = array();
		$paramArr 		= array(
			'method' 	=> 'trans.track.info.get',   //API名称
			'format' 	=> 'json',   //返回格式
			'v' 		=> '1.0',    //API版本号
			'username' 	=> C('OPEN_SYS_USER'), 
			'type'		=> rawurlencode($carrier), 
			'tid'	 	=> $tracknum, 
			'lan'	 	=> $lan, 
		);
		$res		= callOpenSystem($paramArr);
		unset($paramArr);
		$res		= json_decode($res, true);
		$res		= json_decode($res['data'], true);
		$status		= isset($res['Response_Info']) ? $res['Response_Info']['status'] : 0;
		if(isset($res['trackingEventList'])) {
			foreach ($res['trackingEventList'] as $v) {
				$event	= rawurlencode($v['details']);
				array_push($data['trackInfoEn'], array("postion"=>$v['place'], "event"=>$event, "trackTime"=>$v['date'], "stat"=>$status));
			}
		} else {
			if($res['ReturnValue'] == '-1') {
				array_push($data['trackInfoEn'], array("postion"=>"No data", "event"=>"No data", "trackTime"=>"No data", "stat"=>$status));
			} else {
				array_push($data['trackInfoEn'], array("postion"=>"server", "event"=>"Time out", "trackTime"=>date('Y-m-d H:i:s', time()), "stat"=>$status));
			}
		}	
		if(count($data['trackInfoEn']) == 0) {
			array_push($data['trackInfoEn'], array("postion"=>"No data", "event"=>"No data", "trackTime"=>"No data", "stat"=>$status));
		}	
		return $data;
	}
	
	/**
	 * TrackInquiryModel::trackWedoInfo()
	 * 查询运德物流跟踪信息
	 * @param string $tracknum 跟踪号
	 * @param string $lan 语言，默认10000
	 * @return json string
	 */
	public static function trackWedoInfo($tracknum, $lan=10000){
		$status 		= 0;
		$data			= array();
		$file_cz		= '';
		$file_fh		= '';
		//自家运德物流跟踪号
		if(!preg_match("/^((WD[A-Z]{1}\w{8}CN))$/", $tracknum)) {
			//获取物流系统跟踪号信息
			$tracknums			= 'WD'.str_pad($tracknum,9,"0",STR_PAD_LEFT).'CN';
			$paramArr 			= array(
									'method' 	=> 'trans.tracknum.simple.info.get',   //API名称
									'format' 	=> 'json',   
									 'v' 		=> '1.0',   
									'username'	=> C('OPEN_SYS_USER'), 
									'tracknum'	=> $tracknums, 
									'is_wedo'	=> 0, 
								);
			$trackInfos			= callOpenSystem($paramArr);
			unset($paramArr);
			$res				= json_decode($trackInfos, true);
			$scanTime 			= isset($res['data'][0]['scanTime']) ? $res['data'][0]['scanTime'] : '';
			$fhTime 			= isset($res['data'][0]['fhTime']) ? $res['data'][0]['fhTime'] : '';
			$fromCounty			= 'China';
			$toCounty			= isset($res['data'][0]['toCountry']) ? $res['data'][0]['toCountry'] : 'NULL';
			$toCity				= isset($res['data'][0]['toCity']) ? $res['data'][0]['toCity'] : 'NULL';
			$realWeight			= isset($res['data'][0]['weight']) ? $res['data'][0]['weight'] : 0;
			$carrier			= isset($res['data'][0]['carrierId']) ? $res['data'][0]['carrierId'] : 0;
			$accountAccount		= isset($res['data'][0]['platAccount']) ? $res['data'][0]['platAccount'] : '';
			$recordNumber		= isset($res['data'][0]['recordId']) ? $res['data'][0]['recordId'] : 0;
			$userName			= isset($res['data'][0]['userName']) ? $res['data'][0]['userName'] : '';
			$userEmail			= isset($res['data'][0]['userEmail']) ? $res['data'][0]['userEmail'] : '';
			$platForm			= isset($res['data'][0]['platForm']) ? $res['data'][0]['platForm'] : '';
			$addCode			= isset($res['data'][0]['addCode']) ? $res['data'][0]['addCode'] : '';
			if(empty($scanTime)) {			
				//获取ERP追踪信息
				$paramArr 			= array(
					//'method' => 'trans.order.info.get',   //新订单系统API名称
					'method'		=> 'trans.erp.orderinfo.get',   //老ERP API名称
					'format'		=> 'json',   //返回格式
					'v' 			=> '1.0',    //API版本号
					'username'		=> C('OPEN_SYS_USER'), 
					'ids'			=> $tracknum, 
				);
				$trackOrderInfo		= callOpenSystem($paramArr, 'local');
				unset($paramArr);
				//老ERP API名称
				$res				= json_decode($trackOrderInfo, true);
				$scanTime 			= isset($res[0]['scantime']) ? $res[0]['scantime'] : '';
				$fhTime 			= isset($res[0]['fhTime']) ? $res[0]['fhTime'] : '';
				$fromCounty			= 'China';
				$toCounty			= isset($res[0]['ebay_countryname']) ? $res[0]['ebay_countryname'] : 'NULL';
				$toCity				= isset($res[0]['ebay_city']) ? $res[0]['ebay_city'] : 'NULL';
				$realWeight			= isset($res[0]['realWeight']) ? $res[0]['realWeight'] : 0;
				$carrier			= isset($res[0]['ebay_carrier']) ? $res[0]['ebay_carrier'] : '';
				$accountAccount		= isset($res[0]['ebay_account']) ? $res[0]['ebay_account'] : '';
				$recordNumber		= isset($res[0]['recordnumber']) ? $res[0]['recordnumber'] : '';
				$userName			= isset($res[0]['userName']) ? $res[0]['userName'] : '';
				$userEmail			= isset($res[0]['userEmail']) ? $res[0]['userEmail'] : '';
				$platForm			= isset($res[0]['platForm']) ? $res[0]['platForm'] : '';
				$addCode			= isset($res[0]['addCode']) ? $res[0]['addCode'] : 'China';
			}
			//新订单系统API 接口
			// $res				= json_decode($trackOrderInfo, true); 
			// $scanTime 		= isset($res['data'][0]['scantime']) ? $res['data'][0]['scantime'] : '';
			// $toCounty		= isset($res['data'][0]['ebay_couny']) ? $res['data'][0]['ebay_couny'] : 'NULL';
			// $carrier			= isset($res['data'][0]['ebay_carrier']) ? $res['data'][0]['ebay_carrier'] : '';
			// $accountAccount	= isset($res['data'][0]['ebay_account']) ? $res['data'][0]['ebay_account'] : '';
			// $recordNumber	= isset($res['data'][0]['recordNumber']) ? $res['data'][0]['recordNumber'] : '';
			//if(empty($scanTime) || !in_array($carrier, array('1'))) {   //新订单系统API接口
		} else {
			$paramArr 			= array(
									'method' 	=> 'trans.tracknum.simple.info.get',   //API名称
									'format' 	=> 'json',   //返回格式
									'v' 		=> '1.0',    //API版本号
									'username'	=> C('OPEN_SYS_USER'), 
									'tracknum'	=> $tracknum, 
									'is_wedo'	=> 1, 
								);
			$trackInfos			= callOpenSystem($paramArr);
			unset($paramArr);
			$res				= json_decode($trackInfos, true);
			$scanTime 			= isset($res['data'][0]['scanTime']) ? $res['data'][0]['scanTime'] : '';
			$orderSn 			= isset($res['data'][0]['orderSn']) ? $res['data'][0]['orderSn'] : '';
			$carrier			= "中国邮政平邮";
			$userName			= isset($res['data'][0]['userName']) ? $res['data'][0]['userName'] : '';
			$userEmail			= isset($res['data'][0]['userEmail']) ? $res['data'][0]['userEmail'] : '';
			$platForm			= isset($res['data'][0]['platForm']) ? $res['data'][0]['platForm'] : '';
			$addCode			= isset($res['data'][0]['addCode']) ? $res['data'][0]['addCode'] : '';
		}
		$data['trackInfo']		= array();
		$data['extInfo']		= array();
		//老ERP api接口
		// if(!in_array($carrier, array('中国邮政平邮', '中国邮政挂号', '俄速通平邮', '俄速通挂号'))) {
			// array_push($data['extInfo'], array("scanTime"=>'', "fromCounty"=>'', "toCounty"=>'', "toCity"=>'', "realWeight"=>'', "file_cz"=>'', "file_fh"=>''));
			// array_push($data['trackInfo'], array("postion"=>"No data", "event"=>"Does not support this tracking number inquiries!", "trackTime"=>"No data", "stat"=>$status));
		// }
		//if(empty($scanTime) || !in_array($carrier, array('中国邮政平邮', '中国邮政挂号', '俄速通平邮', '俄速通挂号'))) {    
		if(empty($scanTime)) {    
			array_push($data['extInfo'], array("scanTime"=>'', "fromCounty"=>'', "toCounty"=>'', "toCity"=>'', "realWeight"=>'', "file_cz"=>'', "file_fh"=>'',"userName"=>'',"userEmail"=>'',"platForm"=>'','addCode'=>''));
			array_push($data['trackInfo'], array("postion"=>"No data", "event"=>"System Interface exceptions,Please try again!", "trackTime"=>"No data", "stat"=>$status));
		} else {
			$wharr	  		= array();
			//获取新仓库系统的拍照复核照片
			if(!preg_match("/^((WD[A-Z]{1}\w{8}CN))$/", $tracknum)) {
				$paramArr 	= array(
					'method' 			=> 'wh.getWhOrderId',   //API名称
					'format' 			=> 'json',   //返回格式
						 'v'			=> '1.0',    //API版本号
					'username'			=> C('OPEN_SYS_USER'), 
					'accountAccount'	=> $accountAccount, 
					'recordNumber'		=> $recordNumber, 
				);
				$whInfo		= callOpenSystem($paramArr);
				unset($paramArr);
				$res		= json_decode($whInfo, true);
				if(isset($res['data']['orderId'])) {
					$wharr	= $res['data'];
				}
			}
			$paramArr = array(
				'method' 	=> 'trans.track.node.list.get',   //API名称
				'format' 	=> 'json',   //返回格式
					 'v' 	=> '1.0',    //API版本号
				'username'	=> C('OPEN_SYS_USER'), 
				'carrierId'	=> 61, 
				'channelId'	=> 85, 
			);
			$trackNodeInfo	= callOpenSystem($paramArr);
			unset($paramArr);
			$res			= json_decode($trackNodeInfo, true);
			$upday			= 0;
			$extInfo		= array("scanTime"=>$scanTime, "fhTime"=>$fhTime, "fromCounty"=>$fromCounty, "toCounty"=>$toCounty, "toCity"=>$toCity, "realWeight"=>$realWeight,"userName"=>$userName,"userEmail"=>$userEmail,"platForm"=>$platForm,"addCode"=>$addCode);
			foreach($res['data'] as $v) {
				$postion	= empty($v['nodePlace']) ? $toCounty : $v['nodePlace'];
				$trackinfo 	= array("postion"=>$postion, "event"=>$v['nodeKey'], "upday"=>$upday);
				$data		= self::randWedoInfo($extInfo, $tracknum, $v['nodeDays'], $trackinfo, $wharr);
				$upday		+= $v['nodeDays'];
			}
		}
		if(count($data) == 0) {
			array_push($data['extInfo'], array("scanTime"=>'', "fromCounty"=>'', "toCounty"=>'', "toCity"=>'', "realWeight"=>'', "file_cz"=>'', "file_fh"=>'',"userName"=>'',"userEmail"=>'',"platForm"=>'',"addCode"=>''));
			array_push($data['trackInfo'], array("postion"=>"No data", "event"=>"No data yet, please try again after two hours!", "trackTime"=>"No data", "stat"=>$status));
		}
		return $data;
	}
	
	/**
	 * TrackInquiryModel::randWedoInfo()
	 * 触发运德物流跟踪信息
	 * @param string $tracknum 跟踪号
	 * @param string $extInfo 扩展信息
	 * @param int $days 时间间隔
	 * @param array $trackinfo 追踪信息
	 * @return json string
	 */
	public static function randWedoInfo($extInfo, $tracknum, $days, $trackinfo, $wharr=array()){
		$data		= array();
		$times		= time();
		$file_cz	= '';
		$file_fh	= '';
		$scanTime	= $extInfo['scanTime'];
		$fhTime		= empty($extInfo['fhTime']) ? $scanTime : $extInfo['fhTime'];
		$upday		= $trackinfo['upday'];
		$dates		= $scanTime + ($upday+$days)*3600 - rand(3600, 86400);
		$postion	= $trackinfo['postion'];
		$event		= $trackinfo['event'];
		$status		= 2;
		$count		= self::modListCount("trackNumber = '{$tracknum}' AND event = '{$event}'");
		if($scanTime <= ($times-($upday+$days)*3600)) {
			if($count == 0) {
				$wodeInfo	= array("trackNumber"=>$tracknum, "postion"=>$postion, "event"=>$event, "trackTime"=>$dates, "addTime"=>$times);
				self::saveWedoInfo($wodeInfo);
			}
		}		
		$result		= self::showWedoInfo($tracknum);
		$file_cz	= "";
		$file_fh	= "";
		$times		= strtotime("-3 months".' 00:00:01');
		//开放给第三方用户的运德物流跟踪号跟踪信息不显示图片
		if(!preg_match("/^((WD[A-Z]{1}\w{8}CN))$/", $tracknum)) {
			if($scanTime >= $times) {
				$file_cz	= WEB_PIC_URL."cz/".date('Y/m/d', $scanTime)."/cz_".$tracknum.".jpg";
			}
			if($fhTime >= $times) {
				$file_fh 	= WEB_PIC_URL."fh/".date('Y/m/d', $fhTime)."/fh_".$tracknum.".jpg";
			}
			if(!empty($wharr['orderId'])) {
				$file_cz 	= WEB_PIC_URL."cz/".$wharr['scanTime']."/cz_".$wharr['orderId'].".jpg";
				$file_fh 	= WEB_PIC_URL."fh/".$wharr['scanTime']."/fh_".$wharr['orderId'].".jpg";
			}
		}
		//加入目的地国家、城市、包裹实重、发件国、平台名称、客服姓名、客服邮箱
		$data['extInfo']	= array("fromCounty"=>$extInfo['fromCounty'], "toCounty"=>$extInfo['toCounty'], "toCity"=>$extInfo['toCity'], "realWeight"=>$extInfo['realWeight'], "userName"=>$extInfo['userName'], "userEmail"=>$extInfo['userEmail'], "platForm"=>$extInfo['platForm'], "addCode"=>$extInfo['addCode'], "file_cz"=>$file_cz, "file_fh"=>$file_fh);
		$data['trackInfo']	= array();
		//加入公司发货时间节点信息
		array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Order entry processing center', "trackTime"=>date('Y-m-d H:i:s', $scanTime-(6*3600)+rand(60,300)), "stat"=>0)); //进入ERP系统节点
		array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Awaiting packaging', "trackTime"=>date('Y-m-d H:i:s', $scanTime-(2*3600)+rand(60,300)), "stat"=>0)); //等待配货节点
		array_push($data['trackInfo'], array("postion"=>'SHENZHEN OF CHINA', "event"=>'Departure Scan', "trackTime"=>date('Y-m-d H:i:s', $scanTime), "stat"=>0));
		foreach($result as $key=>$v) {
			array_push($data['trackInfo'], array("postion"=>$v['postion'], "event"=>$v['event'], "trackTime"=>date('Y-m-d H:i:s', $v['trackTime']), "stat"=>$status));
		}
		return $data;
	}
}
?>