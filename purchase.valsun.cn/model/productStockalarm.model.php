<?php
/**
 * 类名：ProductStockalarmModel
 * 功能：采购下单预警数据（CRUD）层
 * 版本：1.0
 * 日期：2013/11/11
 * 作者：管拥军
 */
 
class ProductStockalarmModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "goods";
	private static $tab_skuInfo	= "sku_statistics";
		
	/**
	 * ProductStockalarmModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * ProductStockalarmModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$dailyNum  = isset($_GET['dailyNum']) ? intval($_GET['dailyNum']) : ''; //均量排序 
		if($dailyNum == 1 ){// 降序
			$orderby = "order by c.everyday_sale desc";
		}else if($dailyNum == 2 ){// 升序
			$orderby = "order by c.everyday_sale asc";
		}else{
			$orderby = "order by d.partnerId asc ,a.sku desc"; 
		}
		$bookNum = isset($_GET['bookNum']) ? intval($_GET['bookNum']) : null;
		if($bookNum == 1){
			$bcontion = "and c.booknums>0";
		}else{
			$bcontion = "";
		}
		$sql	= "SELECT
					a.goodsName,
					a.sku,
					a.spu,
					a.goodsCost,
					a.goodsWeight,
					a.goodsNote,
					a.goodsCategory,
					a.isNew,
					a.goodsStatus as status,
					b.global_user_id,
					b.global_user_name,
					c.everyday_sale,
					c.newBookNum as booknums,
					c.sevendays,
					c.fifteendays,
					c.thirtydays,
					c.first_sale,
					c.last_sale,
					c.salensend,
					c.interceptnums,
					c.autointerceptnums,
					c.auditingnums,
					c.lastupdate,
					c.thumb,
					c.stock_qty,
					c.purchaseDays,
					c.alertDays,
					c.ow_stock,
					c.it_stock,
					c.is_alert as is_warning,					
					c.reach_days,
					c.addReachtime,
					c.stockoutDays,
					c.out_mark,
					c.totalmonthnum
					FROM
					pc_goods AS a
					left JOIN power_global_user AS b ON a.purchaseId = b.global_user_id
					left JOIN ph_sku_statistics AS c ON a.sku = c.sku
					left join ph_user_partner_relation as d on a.sku=d.sku
					WHERE $where AND a.is_delete = 0
					{$bcontion}
					{$orderby}
					LIMIT $start,$pagenum";
		//echo $sql;
					//c.is_alert as is_warning					
					//c.is_warning	
		if($_GET['debug'] == 1){
			echo $sql;
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ProductStockalarmModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT
				count(*)
				FROM
				pc_goods AS a
				INNER JOIN power_global_user AS b ON a.purchaseId = b.global_user_id
				INNER JOIN ".self::$prefix.self::$tab_skuInfo." AS c ON a.sku = c.sku		
				WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ProductStockalarmModel::isWarnInfo()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function isWarnInfo($sku){
		self::initDB();
		$sql 	= "SELECT count(*) FROM ".self::$prefix.self::$tab_skuInfo." WHERE sku = '{$sku}'";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	=	self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ProductStockalarmModel::updateWarn()
	 * 更新选择料号的预警信息
	 * @param string sku 料号
	 * @return bool 
	 */
	public static function updateWarn($sku){
		$res	= CommonModel::getSkuInfo($sku);
		$res	= json_decode($res, true);
		$data	= array();
		if (is_array($res)) {
			foreach ($res as $v) {
				$data['factory']		= $v['factory'];
				$data['purchaseuser']	= $v['purchaseuser'];
				$data['everyday_sale']	= $v['everyday_sale'];
				$data['storeid']		= $v['storeid'];
				$data['booknums']		= $v['booknums'];
				$data['sevendays']		= $v['sevendays'];
				$data['fifteendays']	= $v['fifteendays'];
				$data['thirtydays']		= $v['thirtydays'];
				$data['first_sale']		= $v['first_sale'];
				$data['last_sale']		= $v['last_sale'];
				$data['salensend']		= $v['salensend'];
				$data['interceptnums']	= $v['interceptnums'];
				$data['autointerceptnums']	= $v['autointerceptnums'];
				$data['auditingnums']	= $v['auditingnums'];
				$data['lastupdate']		= $v['lastupdate'];
				$data['is_warning']		= $v['is_warning'];
				$data['stock_qty']		= $v['goods_count'];
				$data['purchaseDays']	= $v['purchasedays'];
				$data['alertDays']		= $v['goods_days'];
				$data['ow_stock']		= $v['ow_stock'];
				$data['it_stock']		= $v['it_stock'];
			}
			$res	= self::updateWarnInfo($sku, $data);
			if	($res) {
				return "料号：{$sku} 缓存更新成功！";
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "料号：{$sku} 缓存更新失败！";
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "料号：{$sku} 缓存更新失败，数据格式有问题！";
			return false;
		}
	}
	
	/**
	 * ProductStockalarmModel::updateWarnNew()
	 * 新更新选择料号的预警信息
	 * @param array skuArr 料号数组
	 * @param int gid 采购员ID
	 * @return bool 
	 */
	public static function updateWarnNew($sku){
		self::initDB();
		$res	= CommonModel::getSkuInfo($sku);
		print_r($res);
		exit;
		$res	= json_decode($res, true);
		$days7 	= 0.7;
		$days15 = 0.2;
		$days30 = 0.1;
		$dataarray	 		= array();
		$run_starttime 		= time();
		$first_sale 		= CommonModel::getSkuFirstSaleTime($sku);
		$last_sale 			= CommonModel::getSkuLastSaleTime($sku);
		$stock_qty 			= CommonModel::getSkuStockqty($sku); // 实际库存
		$everyday_sale 		= $res[0]['everyday_sale']; // 每日均量
		$purchase_days 		= $res[0]['purchasedays']; // 采购天数
		$alert_days			= $res[0]['goods_days']; // 预警天数
		$warehouse_id 		= 76; // 仓库编号
		$partner_id 		= 0; // 供应商编号
		$salensend 			= CommonModel::getSkuSalensend($sku); // 待发货数量
		$interceptnums 		= CommonModel::getSkuInterceptnums($sku); // 拦截数量
		$autointerceptnums 	= CommonModel::getSkuAutointerceptnums($sku); // 自动拦截数量
		$auditingnums 		= CommonModel::getSkuAuditingnums($sku); // 审核数量
		$hasbooknum = PurchaseOrderModel::hasBookNum ($skuid, $warehouse_id, $purid ); // 已订购数量
		$hasbooknum = !empty($hasbooknum) ? $hasbooknum : 0;
		if ($first_sale > 0) {
			$time 			= time() - $first_sale;
			$saleday 		= ceil( $time / (3600 * 24) ); // 至今距离第一次卖出时间天数
			$thirtycheck 	= time() - 30 * 24 * 3600; // 一个月前
			$totalqty 		= $stock_qty + $hasbooknum; // 总库存=实际库存+已订购数量
			$hasuseqty 		= $totalqty - $salensend - $interceptnums - $auditingnums - $autointerceptnums; // 可用库存数量
			if ($saleday > 30) {
				if ($last_sale > $thirtycheck) { // 最近一次卖出时间已经超过一个月
					$end1 	= strtotime( date ( 'Y-m-d' ) . '23:59:59' );
					$start1 = $end1 - 7 * 24 * 3600;
					$qty1 	= CommonModel::getSkuSaleProducts($start1, $end1, $sku, $everyday_sale); // getSaleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale);//取1~7天销售量

					$end2 	= $start1;
					$start2 = $end1 - 15 * 4 * 600;
					$qty2 	= CommonModel::getSkuSaleProducts($start2, $end2, $sku, $everyday_sale); // getSaleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale);//取1~7天销售量

					$end3 	= $start2;
					$start3 = $end1 - 30 * 24 * 3600;
					$qty3 	= CommonModel::getSkuSaleProducts($start3, $end3, $sku, $everyday_sale); // getSaleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale);//取1~7天销售量

					$everyday_sale 	= $qty1 / 7 * $days7 + $qty2 / 8 * $days15 + $qty3 / 15 * $days30; // 每日均量计算
					$needqty 		= ceil( $everyday_sale * $alert_days ) + $interceptnums; // 库存预警警数量
					$dataarray['everyday_sale']		= $everyday_sale > 0.005 ? round ( $everyday_sale, 2 ) : 0;
					$dataarray['booknums'] 			= $hasbooknum;
					$dataarray['salensend'] 		= $salensend;
					$dataarray['auditingnums'] 		= $auditingnums;
					$dataarray['interceptnums'] 	= $interceptnums;
					$dataarray['autointerceptnums']	= $autointerceptnums;
					$dataarray['is_warning'] = $hasuseqty < 1 || $hasuseqty < $needqty ? 1 : 0;
				}else{
					$dataarray['everyday_sale'] = 0;
					$dataarray['booknums'] = $hasbooknum;
					$dataarray['salensend'] = $salensend;
					$dataarray['auditingnums'] = $auditingnums;
					$dataarray['interceptnums'] = $interceptnums;
					$dataarray['autointerceptnums'] = $autointerceptnums;
					$dataarray['is_warning'] = $hasuseqty < 0 ? 1 : 0;
				}
			}else{
				$end 	= strtotime( date ( 'Y-m-d' ) . '23:59:59' );
				$start 	= $end - ($saleday + 1) * 24 * 3600;
				$qty 	= 20; // getSaleNum($start, $end, $sku, $warehouse_id, $everyday_sale);
				$everyday_sale 	= $qty / $saleday;
				$needqty 		= ceil( $everyday_sale * $alert_days ) + $interceptnums; // 计算产品库存报警数量
				$dataarray['everyday_sale'] 	= round( $everyday_sale, 2 );
				$dataarray['booknums'] 			= $hasbooknum;
				$dataarray['salensend'] 		= $salensend;
				$dataarray['auditingnums'] 		= $auditingnums;
				$dataarray['interceptnums'] 	= $interceptnums;
				$dataarray['autointerceptnums'] = $autointerceptnums;
				$dataarray['is_warning'] 		= $hasuseqty < 1 || $hasuseqty < $needqty ? 1 : 0;
			}
		} else {
			$dataarray['everyday_sale']			= 0;
			$dataarray['booknums']				= $hasbooknum;
			$dataarray['salensend'] 			= 0;
			$dataarray['auditingnums'] 		 	= 0;
			$dataarray['interceptnums'] 		= 0;
			$dataarray['autointerceptnums'] 	= 0;
			$dataarray['is_warning'] 			= 0;
		}
		if ($needqty <= 0) {
			$dataarray['is_warning'] 			= 0;
		}
		$dataarray['lastupdate'] 				= time();
		$dataarray['factory'] 					= $res[0]['factory'];
		$dataarray['purchaseuser'] 				= $res[0]['purchaseuser'];
		$dataarray['storeid'] 					= $res[0]['storeid'];
		$dataarray['sevendays'] 				= $res[0]['sevendays'];
		$dataarray['fifteendays'] 				= $res[0]['fifteendays'];
		$dataarray['thirtydays'] 				= $res[0]['thirtydays'];
		$dataarray['ow_stock'] 					= $res[0]['ow_count'];
		$dataarray['it_stock'] 					= $res[0]['it_count'];
		$dataarray['stock_qty'] 				= $res[0]['goods_count'];
		$dataarray['purchaseDays'] 				= $res[0]['purchasedays'];
		$dataarray['alertDays'] 				= $res[0]['goods_days'];
		$dataarray['purchaseId'] 				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$res 									= self::updateWarnInfo($sku, $dataarray);
		$run_endtime 	= time();
		$speed_time 	= $run_endtime - $run_starttime;
		if ($res) {
			return "料号：{$sku} 缓存更新成功！";
		} else {
			self::$errCode	= 10001;
			self::$errMsg	= "料号：{$sku} 缓存更新失败！";
			return false;
		}
	}
	
	/**
	 * ProductStockalarmModel::updateWarnOld()
	 * 更新选择料号的预警信息
	 * @param array skuArr 料号数组
	 * @param int gid 采购员I
	 * @return bool 
	 */
	public static function updateWarnOld($gid, $skuArr){
		self::initDB();
		foreach ($skuArr as $v) {
			$res	= CommonModel::getSkuInfo($v);
			$res	= json_decode($res, true);
			$days7 	= 0.7;
			$days15 = 0.2;
			$days30 = 0.1;
			$dataarray	 		= array ();
			$run_starttime 		= time ();
			$first_sale 		= $res[0]['first_sale'];
			//$first_sale = get_firstSaleTime ($sku);
			//echo 'API返回第一次售出时间:'.$first_sale."<br/>";
			//$last_sale = get_lastSaleTime($sku);
			$last_sale 			= $res[0]['last_sale'];
			$stock_qty 			= $res[0]['stock_qty']; // 实际库存
			$everyday_sale 		= $res[0]['everyday_sale']; // 每日均量
			$purchase_days 		= $res[0]['purchasedays']; // 采购天数
			$alert_days			= $res[0]['goods_days']; // 预警天数
			$warehouse_id 		= 76; // 仓库编号
			$partner_id 		= 0; // 供应商编号
			$salensend 			= $res[0]['salensend']; // 待发货数量
			$interceptnums 		= $res[0]['interceptnums']; // 拦截数量
			$autointerceptnums 	= $res[0]['autointerceptnums']; // 自动拦截数量
			$end = '1375290061';
			$start = '1354294861';
			//$rtnnum = get_saleNum ( $start, $end, $sku, 76, $everyday_sale );
			// echo 'API返回销量:'.$rtnnum."<br/>";
			$auditingnums 		= $res['auditingnums']; // 审核数量
			//$hasbooknum = PurchaseOrderModel::hasBookNum ( $skuid, $warehouse_id, $purid ); // 已订购数量
			$hasbooknum = !empty($hasbooknum) ? $hasbooknum : 0;
			if ($first_sale > 0) {
				$time 			= time () - $first_sale;
				$saleday 		= ceil ( $time / (3600 * 24) ); // 至今距离第一次卖出时间天数
				$thirtycheck 	= time () - 30 * 24 * 3600; // 一个月前
				$totalqty 		= $stock_qty + $hasbooknum; // 总库存=实际库存+已订购数量
				$hasuseqty 		= $totalqty - $salensend - $interceptnums - $auditingnums - $autointerceptnums; // 可用库存数量
				// $saleday = 35; // 测试
				// $thirtycheck = '1243705586';
				if ($saleday > 30) {
					if ($last_sale > $thirtycheck) { // 最近一次卖出时间已经超过一个月
						$end1 	= strtotime ( date ( 'Y-m-d' ) . '23:59:59' );
						$start1 = $end1 - 7 * 24 * 3600;
						$qty1 	= 5; // getSaleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale);//取1~7天销售量

						$end2 	= $start1;
						$start2 = $end1 - 15 * 4 * 600;
						$qty2 	= 12; // getSaleNum($start2, $end2, $sku, $warehouse_id, $everyday_sale);//取7~15天销售量

						$end3 	= $start2;
						$start3 = $end1 - 30 * 24 * 3600;
						$qty3 	= 20; // getSaleNum($start3, $end3, $sku, $warehouse_id, $everyday_sale);//取16~30天销售量

						$everyday_sale 	= $qty1 / 7 * $days7 + $qty2 / 8 * $days15 + $qty3 / 15 * $days30; // 每日均量计算
						$needqty 		= ceil ( $everyday_sale * $alert_days ) + $interceptnums; // 库存预警警数量
						$dataarray['everyday_sale']		= $everyday_sale > 0.005 ? round ( $everyday_sale, 2 ) : 0;
						$dataarray['booknums'] 			= $hasbooknum;
						$dataarray['salensend'] 		= $salensend;
						$dataarray['auditingnums'] 		= $auditingnums;
						$dataarray['interceptnums'] 		= $interceptnums;
						$dataarray['autointerceptnums']	= $autointerceptnums;
						$dataarray['is_warning'] = $hasuseqty < 1 || $hasuseqty < $needqty ? 1 : 0;
					} else {
						$dataarray['everyday_sale'] = 0;
						$dataarray['booknums'] = $hasbooknum;
						$dataarray['salensend'] = $salensend;
						$dataarray['auditingnums'] = $auditingnums;
						$dataarray['interceptnums'] = $interceptnums;
						$dataarray['autointerceptnums'] = $autointerceptnums;
						$dataarray['is_warning'] = $hasuseqty < 0 ? 1 : 0;
					}
				} else {
					$end 	= strtotime ( date ( 'Y-m-d' ) . '23:59:59' );
					$start 	= $end - ($saleday + 1) * 24 * 3600;
					$qty 	= 20; // getSaleNum($start, $end, $sku, $warehouse_id, $everyday_sale);
					$everyday_sale 	= $qty / $saleday;
					$needqty 		= ceil ( $everyday_sale * $alert_days ) + $interceptnums; // 计算产品库存报警数量
					$dataarray['everyday_sale'] 	= round ( $everyday_sale, 2 );
					$dataarray['booknums'] 			= $hasbooknum;
					$dataarray['salensend'] 		= $salensend;
					$dataarray['auditingnums'] 		= $auditingnums;
					$dataarray['interceptnums'] 	= $interceptnums;
					$dataarray['autointerceptnums'] = $autointerceptnums;
					$dataarray['is_warning'] 		= $hasuseqty < 1 || $hasuseqty < $needqty ? 1 : 0;
				}
			} else {
				$dataarray['everyday_sale']			= 0;
				$dataarray['booknums']				= $hasbooknum;
				$dataarray['salensend'] 			= 0;
				$dataarray['auditingnums'] 		 	= 0;
				$dataarray['interceptnums'] 		= 0;
				$dataarray['autointerceptnums'] 	= 0;
				$dataarray['is_warning'] 			= 0;
			}
			if ($needqty <= 0) {
				$dataarray['is_warning'] 			= 0;
			}
			$dataarray['lastupdate'] 				= time();
			$dataarray['factory'] 					= $res[0]['factory'];
			$dataarray['purchaseuser'] 				= $res[0]['purchaseuser'];
			$dataarray['storeid'] 					= $res[0]['storeid'];
			$dataarray['sevendays'] 				= $res[0]['sevendays'];
			$dataarray['fifteendays'] 				= $res[0]['fifteendays'];
			$dataarray['thirtydays'] 				= $res[0]['thirtydays'];
			$dataarray['ow_stock'] 					= $res[0]['ow_count'];
			$dataarray['it_stock'] 					= $res[0]['it_count'];
			$dataarray['stock_qty'] 				= $res[0]['goods_count'];
			$dataarray['purchaseDays'] 				= $res[0]['purchasedays'];
			$dataarray['alertDays'] 				= $res[0]['goods_days'];
			$dataarray['purchaseId'] 				= $_SESSION[C('USER_AUTH_SYS_ID')];
			$rtnupdatedata 							= self::updateWarnInfo($v, $dataarray);
		}
		$run_endtime 	= time ();
		$speed_time 	= $run_endtime - $run_starttime;
		return $rtnupdatedata;
	}

	/**
	 * ProductStockalarmModel::updateWarnInfo()
	 * 更新选择料号的预警信息
	 * @param array $dataarray 预警信息
	 * @param string $sku 料号
	 * @return bool 
	 */
	public static function updateWarnInfo($sku, $dataarray){
		self::initDB();
		$res	= self::isWarnInfo($sku);
		if($res) {
			$sql 	= array2sql($dataarray);
			$sql 	= "UPDATE `".self::$prefix.self::$tab_skuInfo."` SET ".$sql." WHERE sku = '{$sku}'";
		} else {
			$dataarray['sku']	= $sku;
			$sql 	= array2sql($dataarray);
			$sql 	= "INSERT INTO `".self::$prefix.self::$tab_skuInfo."` SET ".$sql;
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}		
	}
}
?>
