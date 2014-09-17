<?php
/**
 * 类名：TrackWarnExportModel
 * 功能：运输方式跟踪号报表导出数据（CRUD）层
 * 版本：1.0
 * 日期：2014/01/02
 * 作者：管拥军
 */
 
class TrackWarnExportModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table				= "track_number";
	private static $tab_track_carrier	= "carrier";
	private static $tab_track_channel	= "channels";
		
	/**
	 * TrackWarnExportModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
			
	/**
	 * TrackWarnExportModel::exportTrackNumberInfo()
	 * 导出跟踪号信息
	 * @param string $condition 导出条件
	 * @param integer $pagesize 每页数量
	 * @return string 
	 */
	public static function exportTrackNumberInfo($condition, $pagesize=10000){
		self::initDB();
		set_time_limit(600);
		ignore_user_abort(false);
		$totalnum	= 0;
		$data		= "";
		$sql		= "SELECT count(*) AS totalnum FROM
						".self::$prefix.self::$table." AS a
						LEFT JOIN ".self::$prefix.self::$tab_track_channel." AS b ON a.channelId = b.id
						LEFT JOIN ".self::$prefix.self::$tab_track_carrier." AS c ON a.carrierId = c.id
						WHERE {$condition}";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res			= self::$dbConn->fetch_array($query);
			$totalnum		= intval($res['totalnum']);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
		$pages		= ceil($totalnum/$pagesize);
		$filename	= 'track_number_info_'.$_SESSION[C('USER_AUTH_SYS_ID')];
		$statusList	= C('TRACK_STATUS_DETAIL');
		$fileurl	= WEB_URL."temp/".date('Ymd')."/".$filename.".xls";
		$filepath	= WEB_PATH."html/temp/".date('Ymd')."/".$filename.".xls";
		for($i=0; $i<$pages; $i++) {
			$offset	= $i*$pagesize;
			$idArr	= array();
			$ids	= '';
			$sql	= "SELECT
						a.id
						FROM
						".self::$prefix.self::$table." AS a
						LEFT JOIN ".self::$prefix.self::$tab_track_channel." AS b ON a.channelId = b.id
						LEFT JOIN ".self::$prefix.self::$tab_track_carrier." AS c ON a.carrierId = c.id
						WHERE {$condition} LIMIT {$offset},{$pagesize}";
			$query	= self::$dbConn->query($sql);
			$res	= self::$dbConn->fetch_array_all($query);
			foreach($res as $v) {
				array_push($idArr, $v['id']);
			}
			if(empty($idArr)) {
				self::$errCode	= 10001;
				self::$errMsg	= "获取数据失败";
				return false;
			} else {
				$ids			= implode(",",$idArr);
			}
			$sql				= "SELECT
									c.carrierNameCn,
									b.channelName,
									a.orderSn,
									a.recordId,
									a.carrierId,
									a.channelId,
									a.platAccount,
									a.platForm,
									a.trackNumber,
									a.weight,
									a.cost,
									a.scanTime,
									a.toCountry,
									a.lastEvent,
									a.lastPostion,
									a.lastTime,
									a.status
									FROM
									".self::$prefix.self::$table." AS a
									LEFT JOIN ".self::$prefix.self::$tab_track_channel." AS b ON a.channelId = b.id
									LEFT JOIN ".self::$prefix.self::$tab_track_carrier." AS c ON a.carrierId = c.id
									WHERE a.id IN({$ids})
									ORDER BY scanTime DESC";
			$query				= self::$dbConn->query($sql);
			$res				= self::$dbConn->fetch_array_all($query);
			if($i == 0)	$data	= '<?xml version="1.0" encoding="UTF-8"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40"><Styles><Style ss:ID="sDT"><NumberFormat ss:Format="Short Date"/></Style></Styles><Worksheet ss:Name="Sheet1"><Table><Row><Cell><Data ss:Type="String">运输方式</Data></Cell><Cell><Data ss:Type="String">渠道</Data></Cell><Cell><Data ss:Type="String">订单编号</Data></Cell><Cell><Data ss:Type="String">订单号</Data></Cell><Cell><Data ss:Type="String">跟踪号</Data></Cell><Cell><Data ss:Type="String">重量</Data></Cell><Cell><Data ss:Type="String">价格</Data></Cell><Cell><Data ss:Type="String">发货时间</Data></Cell><Cell><Data ss:Type="String">发往国家</Data></Cell><Cell><Data ss:Type="String">跟踪事件</Data></Cell><Cell><Data ss:Type="String">跟踪位置</Data></Cell><Cell><Data ss:Type="String">跟踪时间</Data></Cell><Cell><Data ss:Type="String">跟踪状态</Data></Cell><Cell><Data ss:Type="String">收寄日期</Data></Cell><Cell><Data ss:Type="String">收寄地点</Data></Cell><Cell><Data ss:Type="String">互封日期</Data></Cell><Cell><Data ss:Type="String">互封地点</Data></Cell><Cell><Data ss:Type="String">直封日期</Data></Cell><Cell><Data ss:Type="String">直封地点</Data></Cell><Cell><Data ss:Type="String">平台帐号</Data></Cell><Cell><Data ss:Type="String">平台名称</Data></Cell></Row>'."\n";
			foreach($res as $v) {
				$nodeList	= TransOpenApiModel::getTrackNodeList($v['carrierId'], $v['channelId']);
				$detail		= TransOpenApiModel::getTrackInfoLocal($v['trackNumber'], $v['carrierId']);
				$j			= 0;
				$dates		= array();
				$pos		= array();
				foreach ($nodeList as $n) {
					foreach ($detail as $val) {
						$keys	= explode(" ",$n['nodeKey']);
						foreach ($keys as $key) {
							if(strpos($val['event'],$key) !== false && !in_array($val['event'],array('未妥投'))) {
								$dates[$j]	= !empty($val['trackTime']) ? strftime("%Y-%m-%dT%H:%M:%S",$val['trackTime']) : '';
								$pos[$j]	= $val['postion'];
								break;
							}
						}
					}
					$j++;
				}
				$data	.= '<Row><Cell><Data ss:Type="String">'.$v['carrierNameCn'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['channelName'].'</Data></Cell><Cell><Data ss:Type="Number">'.$v['orderSn'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['recordId'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['trackNumber'].'</Data></Cell><Cell><Data ss:Type="Number">'.$v['weight'].'</Data></Cell><Cell><Data ss:Type="Number">'.$v['cost'].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.strftime("%Y-%m-%dT%H:%M:%S",$v['scanTime']).'</Data></Cell><Cell><Data ss:Type="String">'.$v['toCountry'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['lastEvent'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['lastPostion'].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.strftime("%Y-%m-%dT%H:%M:%S",$v['lastTime']).'</Data></Cell><Cell><Data ss:Type="String">'.$statusList[$v['status']].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.$dates[0].'</Data></Cell><Cell><Data ss:Type="String">'.$pos[0].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.$dates[1].'</Data></Cell><Cell><Data ss:Type="String">'.$pos[1].'</Data></Cell><Cell ss:StyleID="sDT"><Data ss:Type="DateTime">'.$dates[2].'</Data></Cell><Cell><Data ss:Type="String">'.$pos[2].'</Data></Cell><Cell><Data ss:Type="String">'.$v['platAccount'].'</Data></Cell><Cell><Data ss:Type="String">'.$v['platForm'].'</Data></Cell></Row>'."\n";
			}
			if($i == 0) {
				write_w_file($filepath,$data);
			} else {
				write_a_file($filepath,$data);
			}
			$data	= "";
			unset($res);
			sleep(3);
		}
		$data		= "</Table></Worksheet></Workbook>";
		write_a_file($filepath,$data);
		$zipFile	= self::getXlsZip($filepath, $filename);
		if($zipFile) {
			$fileurl 	= WEB_URL.$zipFile;
			$filepath	= WEB_PATH."html/".$zipFile;
		}
		if(file_exists($filepath)) {
			return $fileurl;
		} else {	
			return "fail";
		}
	}
	
	/**
	 * TrackWarnExportModel::getXlsZip()
	 * @param string $filePath 文件路径
	 * @param string $fileName 文件名
	 * 打包文件
	 * @return  json string
	 */
	public static function getXlsZip($filePath, $fileName){
		$zipfile 	= str_replace(".xls",".zip",$filePath);
		require_once(WEB_PATH.'lib/pclzip.lib.php');
		$obj		= new PclZip($zipfile);
		$files		= array($filePath);
		$curtime 	= date('Y-m-d H:i:s',time()); 
		//创建压缩文件
		if($obj->create($files, PCLZIP_OPT_REMOVE_PATH, WEB_PATH.'html/temp/'.date('Ymd').'/', PCLZIP_OPT_COMMENT, "Today's tran.valsun.cn trackNumber info packaged!\n\npackaged time:{$curtime}")) {
			@unlink($filePath);
			return 'temp/'.date('Ymd').'/'.$fileName.'.zip';
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "打包失败！请检查相关权限";
			return false;
		}
	}
	
	/**
	 * TrackWarnExportModel::exportXls()
	 * 导出xls文件
	 * @param array $res 结果值
	 * @return string 文件路径
	 */
	private function exportXls($tharr, $res){
		$data  	= array();
		$tdarr 	= array();
		$dates 	= array();
		$pos   	= array();
		$filename	= 'track_number_info_'.date('Y-m-d',time()).'_'.$_SESSION[C('USER_AUTH_SYS_ID')];;
		$statusList	= C('TRACK_STATUS_DETAIL');
		$fileurl	= WEB_URL."temp/".$filename.".xls";
		$filepath	= WEB_PATH."html/temp/".$filename.".xls";
		array_push($data, $tharr);
		foreach ($res as $v) {
			$nodeList	= TransOpenApiModel::getTrackNodeList($v['carrierId'], $v['channelId']);
			$detail		= TransOpenApiModel::getTrackInfoLocal($v['trackNumber'], $v['carrierId']);
			$i			= 0;
			foreach ($nodeList as $n) {
				foreach ($detail as $val) {
						$keys	= explode(" ",$n['nodeKey']);
						foreach ($keys as $key) {
							if(strpos($val['event'],$key) !== false && !in_array($val['event'],array('未妥投'))) {
								$dates[$i]	= !empty($val['trackTime']) ? date('Y-m-d H:i:s',$val['trackTime']) : '';
								$pos[$i]	= $val['postion'];
								break;
							}
						}
				}
				$i++;
			}
			$tdarr	= array(
						$v['carrierNameCn'],
						$v['channelName'],
						$v['orderSn'],
						$v['recordId'],
						$v['trackNumber'],
						$v['weight'],
						$v['cost'],
						date('Y-m-d H:i:s',$v['scanTime']),
						$v['toCountry'],
						$v['lastEvent'],
						$v['lastPostion'],
						date('Y-m-d H:i:s',$v['lastTime']),
						$statusList[$v['status']],
						$dates[0],
						$pos[0],
						$dates[1],
						$pos[1],
						$dates[2],
						$pos[2],
						$v['platAccount'],
						$v['platForm'],
					);
			array_push($data, $tdarr);
		}		
		require_once WEB_PATH."lib/php-export-data.class.php";
		$excel = new ExportDataExcel('file');
		$excel->filename = $filepath; 
		$excel->initialize();
		foreach($data as $row) {
			$excel->addRow($row);
		}  
		$excel->finalize(); 
		unset($data);
		if(file_exists($filepath)) {
			return $fileurl;
		} else {	
			return "fail";
		}
	}	
}
?>