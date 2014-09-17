<?php
/*
*打标操作(model)
*add by heminghua
*
*/
class printLabelModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	/*
	 * 插入打标记录
	 */
	public static function insertPrintGroup($tallyId,$print_num,$printerId,$time, $storeId = 1){
		self::initDB();
		$s_sql = "select * from wh_print_group where tallyListId={$tallyId} and is_delete=0";
		$s_sql = self::$dbConn->query($s_sql);	
		$s_sql = self::$dbConn->fetch_array_all($s_sql);
		
		if(!empty($s_sql)){
			return $s_sql[0]['id'];
		}
		$sql	 =	"INSERT INTO wh_print_group(tallyListId,printNum,printerId,printTime, storeId) VALUES({$tallyId},{$print_num},{$printerId},{$time}, {$storeId})";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return self::$dbConn->insert_id();	
		}else{
			return false;	
		}
	
	} 
	
	/*
	 * 标示已打标
	 */
	public static function updatePrintGroup($tallyIdArr){
		self::initDB();
        
        $log_file   =   'print_label_record/'.date('Ymd').'.txt'; //日志文件
        $date       =   date('Y-m-d H:i:s');
		$userId = $_SESSION['userId'];
		$time   = time();
		if(!is_array($tallyIdArr)){
			return false;
		}
		$ids = implode(',', $tallyIdArr);
		OmAvailableModel::begin();
        
        /** 更新点货记录**/
		$update_tallying = "update wh_tallying_list set printerId={$userId},printTime={$time} where id in(".$ids.")";
		$update_tallying = self::$dbConn->query($update_tallying);
		if(!$update_tallying){
		    $log_info      = sprintf("tallyinglistId：%s, 时间：%s, 信息:%s,返回值：%s, 参数:%s \r\n", $ids, $date, '更新点货信息打标时间失败',
                                        $update_tallying, "update wh_tallying_list set printerId={$userId},printTime={$time} where id in(".$ids.")");
            write_log($log_file, $log_info);
			OmAvailableModel::rollback();
			return false;
		}
        $log_info      = sprintf("tallyinglistId：%s, 时间：%s, 信息:%s,返回值：%s, 参数:%s \r\n", $ids, $date, '更新点货信息打标时间成功',
                                    $update_tallying, "update wh_tallying_list set printerId={$userId},printTime={$time} where id in(".$ids.")");
        write_log($log_file, $log_info);
        /** 更新点货记录 end**/
        
        /** 更新打标记录**/
//        $select_sql  = "select * from wh_print_group where tallyListId={$id} and is_delete=0 order by id desc limit 1";
//		$select_info = self::$dbConn->fetch_first($select_sql);
//		if(empty($select_info)){
//            $log_info      = sprintf("tallyinglistId：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $id, $date, '打标信息不存在',
//                                is_array($select_info) ? json_encode($select_info) : $select_info , $select_sql);
//            write_log($log_file, $log_info);
//			OmAvailableModel::rollback();
//			return false;
//		}
		$update_sql   = "update wh_print_group set status=1 where tallyListId in ({$ids}) and is_delete=0";
		$update_print = self::$dbConn->query($update_sql);
		if(!$update_print) {
            $log_info      = sprintf("tallyinglistId：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $id, $date, '更新打标记录失败',
                                is_array($update_print) ? json_encode($update_print) : $update_print , $update_sql);
            write_log($log_file, $log_info);
			OmAvailableModel::rollback();
			return false;
		}
        $log_info      = sprintf("tallyinglistId：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $id, $date, '更新打标记录成功',
                                is_array($update_print) ? json_encode($update_print) : $update_print , $update_sql);
        write_log($log_file, $log_info);
        /** 更新打标记录 end**/
        
        /** 推送QC**/
		//foreach($tallyIdArr as $id){
		$push    =   WhPushModel::pushTallyingList($ids);
        $msg     =   $push ? '推送成功!' : '推送失败！';
        $log_info      = sprintf("tallyinglistId：%s, 时间：%s,错误信息:%s,返回值：%s \r\n", $ids, $date, $msg,
                                is_array($push) ? json_encode($push) : $push);
        write_log($log_file, $log_info);
		//}	
		OmAvailableModel::commit();
		return true;
	}
	
	/*
	 * 获取分区及回邮地址
	 */
	public static function getPartionFromAddress($shipOrderId,$carrier,$countryName){
		self::initDB();

		if(strpos($carrier, '中国邮政') !== false) { 
			$print_partion_info = self::showPrintPartionLabel($shipOrderId,$carrier,$countryName);
			return  $print_partion_info; 
		} else if (strpos($carrier, '香港') !== false){
			$fromAddress = '<strong>from:Chen Xiaoming</strong>
							<span style="display:inline-block;font-weight:bold;">
							   P.O.Box No.190 FO TAN POST OFFICE
							</span>
							<span style="display:inline-block;font-weight:bold;">
								3N.T.HONGKONG
							</span>
							<span style="font-size:11px;font-weight:bold;display:inline-block;margin-left:4px;">
								made in china
							</span>';
			return array('', $fromAddress);

		} else if (strpos($carrier, '新加坡') !== false) {
			$fromAddress = '新加坡回邮地址待提供';
			return array('', $fromAddress);
		}
	}
	
	/*
	 * 根据发货单id、运输方式、国家获取返回地址
	 */
	public static function showPrintPartionLabel($shipOrderId,$carrier,$countryName){
		self::initDB();
		$result	  = array();
		$sz_array = $sz_namebiref_array = $sz_fromaddresshtml_array = array();
		$partions = CommonModel::getChannelNameByIds('all');
		foreach($partions as $partion){
			$sz_array[$partion['partitionCode']]   				 = $partion['countries'];
			$sz_namebiref_array[$partion['partitionCode']]   	 = $partion['partitionAli'];
			$sz_fromaddresshtml_array[$partion['partitionCode']] = htmlspecialchars_decode($partion['returnAddHtml']);
		}		
		//匹配回邮地址
		foreach($sz_array as $sz_key => $sz_value){
			$sz_value_arr 	 = explode("],[",$sz_value);
			$sz_value_arr[0] =  str_replace("[","",$sz_value_arr[0]);
			$sz_value_arr[count($sz_value_arr)-1] = str_replace("]","",$sz_value_arr[count($sz_value_arr)-1]);
			if (!empty($countryName) && in_array(trim($countryName),$sz_value_arr)) {
				$result 	 = array($sz_namebiref_array[$sz_key], $sz_fromaddresshtml_array[$sz_key]);
				break;
			}
		}
		//增加因缺少国家直接为福七区的逻辑 add by guanyongjun 2014/03/07
		if (!in_array($countryName,array('Australia','Australla')) && empty($result)) {
			$addUser	= empty($_SESSION['sysUserId']) ? 0 : $_SESSION['sysUserId'];
			$tName 		= 'wh_no_country_partion';
			$set		= "SET shipOrderId='{$shipOrderId}',countryName='[{$countryName}]',userId='{$addUser}',createdTime='".time()."'";
			$affectRow 	= OmAvailableModel :: insertRow($tName, $set);
			$result 	= array($sz_namebiref_array[7], $sz_fromaddresshtml_array[7]);	
		}
		return $result;
		// if(strpos($carrier, '中国邮政')!==false){
			// if(in_array(trim($countryName), array('Albania','Algeria','Argentina','Egypt','Ethiopia','Estonia','Anguilla','Austria','Bahrain','Panama','Belarus','Bulgaria','Benin','Belgium','Iceland','Bosnia and Herzegovina','Bolivia','Botswana','Burkina Faso','Burundi','Denmark','Togo','Dominica','Russian Federation','Ecuador','Falkland Islands(Malvinas)','Gambia','Colombia','Costa Rica','Greenland','Georgia','Guyana','Haiti','Djibouti','Guinea','Guinea','Ghana','Cambodia','Czech Republic','Zimbabwe','Cameroon','Qatar','Cote d Ivoire (Ivory Coast)','Kuwait','Kenya','Latvia','Lesotho','Laos','Lebanon','Lithuania','Liberia','Libya','Rwanda','Romania','Madagascar','Malta','Malawi','Mali','Mauritania','Mongolia','Bangladesh','Peru','Morocco','Mozambique','Namibia','Nepal','Niger','Nigeria','Palau','Portugal','Sweden','Senegal','Cyprus','Seychelles','Slovakia','Sudan','Suriname','Tanzania','Trinidad and Tobago','Tunisia','Turkey','Venezuela','Uganda','Uruguay','Western Sahara','Greece','Hungary','Syria','Jamaica','Armenia','Yemen','Iraq','Iran','India','Zambia','Zaire','Chad','Chile','Cuba,Republic of','Congo, Republic of the','Congo, Democratic Republic of the','Russia','Azerbaijan Republic','Azerbaijan','Dominica','Dominican Republic','Equatorial Guinea','Gabon Republic','Papua New Guinea'))/* || $ebay_carrierstyle == 1*/){
				// return array($sz_namebiref_array[7], $sz_fromaddresshtml_array[7]);				
			// }else{
				// $mailways = self::getOrderGoodsMailwayIds($shipOrderId);
				// if(in_array(2,$mailways) || in_array(5,$mailways)){
					// foreach($sz_array as $sz_key => $sz_value){
						// $sz_value_arr = explode("],[",$sz_value);
						// $sz_value_arr[0] =  str_replace("[","",$sz_value_arr[0]);
						// $sz_value_arr[count($sz_value_arr)-1] = str_replace("]","",$sz_value_arr[count($sz_value_arr)-1]);
						// if(!empty($countryName) && in_array(trim($countryName),$sz_value_arr)){
							// return array($sz_namebiref_array[$sz_key], $sz_fromaddresshtml_array[$sz_key]);
							// break;
						// }
					// }
				// }else if(in_array(1,$mailways)){
					// return array($sz_namebiref_array[7], $sz_fromaddresshtml_array[7]);	
				// }
			// }
		// }
	}
	
	/*
	 * 根据发货单id获取发货单下面料号的所有分类
	 */
	public static function getOrderGoodsMailwayIds($shipOrderId){
		self::initDB();
		$sql   = "select sku from wh_shipping_orderdetail where shipOrderId={$shipOrderId} group by sku";
		$query = self::$dbConn->query($sql);	
		$mailwayIds = array();
		if($query){
			$results = self::$dbConn->fetch_array_all($query);
			if($results){
				foreach($results as $result){
					$goods_sql = "select goodsCategory from pc_goods where sku='{$result['sku']}'";
					$goos_info = self::$dbConn->fetch_first($goods_sql);
					
					$cate_sql  = "select mailwayId from pc_goods_category where path='{$goos_info['goodsCategory']}'";
					$cate_info = self::$dbConn->fetch_first($cate_sql);
					
					$mailwayIds[] = $cate_info['mailwayId'];
				}
			}
		}
		return $mailwayIds;
	}
	
	/*
	 * 根据发货单id获取发货单下面料号的所有分类
	 */
	public static function checkprintcard($shipOrderId){
		self::initDB();
		$sql    = "select a.platformUsername,a.transportId,a.accountId,b.recordNumber from wh_shipping_order as a 
					left join wh_shipping_order_relation as b on a.id=b.shipOrderId 
					where a.id={$shipOrderId}";
		$result = self::$dbConn->fetch_first($sql);
		
		$account     = CommonModel::getAccountNameById($result['accountId']);
		$shipingname = CommonModel::getShipingNameById($result['transportId']);
		$ebayAccArr  = CommonModel::getEbayAccountList();
		
		if (!in_array($shipingname, array('中国邮政挂号','中国邮政平邮','香港小包挂号','香港小包平邮','EUB'))){
			return '';
		}
		if (in_array($account, $ebayAccArr)){
			return '';
		}
		if (!in_array($account, $ebayAccArr)&&preg_match("/[a-z]+/i", $result['recordnumber'])){
			return '';
		}
		if ($account=='dresslink.com'){
			return '';
		}
		$sql    = "SELECT street FROM wh_shipping_order WHERE platformUsername='{$result['platformUsername']}' AND orderStatus!=900";
		$query  = self::$dbConn->query($sql);
		$orders = self::$dbConn->fetch_array_all($query);

		$streets = array_unique(array_filter(multi2single('street', $orders)));
		
		if (count($streets)==1){
			return 'KP';
		}else {
			return '';
		}
	}
	
	/*
	 * 获取发货单跟踪号
	 */
	public static function getTracknumber($shipOrderId){
		self::initDB();
		$sql    = "select tracknumber from wh_order_tracknumber where shipOrderId={$shipOrderId}";
		$result = self::$dbConn->fetch_first($sql);
		if($result){
			return $result['tracknumber'];
		}else{
			return '';
		}
	}
	
	/*
	 * 获取sku信息
	 */
	public static function getSkuInfo($sku){
		self::initDB();
		$sql    = "select id,goodsName,pmId,goodsWeight,goodsCost,isPacking from pc_goods where sku='{$sku}' and is_delete=0";
		$result = self::$dbConn->fetch_first($sql);
		if($result){
			return $result;
		}else{
			return array();
		}
	}
	
	/*
	 * 根据id获取sku信息
	 */
	public static function getSkuInfoById($id){
		self::initDB();
		$sql    = "select id,sku,goodsName from pc_goods where id={$id}";
		$result = self::$dbConn->fetch_first($sql);
		if($result){
			return $result;
		}else{
			return array();
		}
	}
	
	/*
	 * 根据sku获取获取料号打印信息
	*/
	public static function getGroupInfoByTallyListId($id){
		self::initDB();
		$sql    = "select id from `wh_print_group` where tallyListId={$id} and status=1";	
		$sql	= self::$dbConn->query($sql);
		$result = self::$dbConn->fetch_array_all($sql);
		if($result){
			return $result;
		}else{
			return array();
		}
	}
	
	/*
	 * 通过运输方式获取邮寄公司
	 */
	public static function getMcFromCarrier($shipOrderId,$carrier,$countryName,$account){
		$mc = '';
		$MAILWAYCONFIG = array(0=>'EUB', 1=>'深圳', 2=>'福州', 3=>'三泰', 4=>'泉州', 5=>'义乌', 6=>'福建', 7=>'中外联', 8=>'GM', 9=>'香港', 10=>'快递');
		$flip_MAILWAYCONFIG = array_flip($MAILWAYCONFIG);
		if(strpos($carrier, '中国邮政')!==false){
			if(in_array(trim($countryName), array('Albania','Algeria','Argentina','Egypt','Ethiopia','Estonia','Anguilla','Austria','Bahrain','Panama','Belarus','Bulgaria','Benin','Belgium','Iceland','Bosnia and Herzegovina','Bolivia','Botswana','Burkina Faso','Burundi','Denmark','Togo','Dominica','Russian Federation','Ecuador','Falkland Islands(Malvinas)','Gambia','Colombia','Costa Rica','Greenland','Georgia','Guyana','Haiti','Djibouti','Guinea','Guinea','Ghana','Cambodia','Czech Republic','Zimbabwe','Cameroon','Qatar','Cote d Ivoire (Ivory Coast)','Kuwait','Kenya','Latvia','Lesotho','Laos','Lebanon','Lithuania','Liberia','Libya','Rwanda','Romania','Madagascar','Malta','Malawi','Mali','Mauritania','Mongolia','Bangladesh','Peru','Morocco','Mozambique','Namibia','Nepal','Niger','Nigeria','Palau','Portugal','Sweden','Senegal','Cyprus','Seychelles','Slovakia','Sudan','Suriname','Tanzania','Trinidad and Tobago','Tunisia','Turkey','Venezuela','Uganda','Uruguay','Western Sahara','Greece','Hungary','Syria','Jamaica','Armenia','Yemen','Iraq','Iran','India','Zambia','Zaire','Chad','Chile','Cuba,Republic of','Congo, Republic of the','Congo, Democratic Republic of the','Russia','Azerbaijan Republic','Azerbaijan','Dominica','Dominican Republic','Equatorial Guinea','Gabon Republic','Papua New Guinea')) || $ebay_carrierstyle == 1 || in_array($account, array('taotaocart','arttao','taochains','etaosky','tmallbasket','mucheer','lantao','direttao','hitao','taolink'))){
				$mc = "福建";
			}else{
				$mailways = self::getOrderGoodsMailwayIds($shipOrderId);
				if(in_array(2,$mailways) || in_array(5,$mailways) || in_array(trim($countryName), array('Saudi Arabia','Malaysia'))){
					$mc = "深圳";
				}else if(in_array(1,$mailways)){
					$mc = "福建";
				} else {
					$mc = "福建";
				}
			}
		}else if($carrier == '新加坡邮政'){
			$mc = '香港';
		}else if($carrier == '香港小包平邮'){
			$mc = '香港';
		}else if($carrier == '香港小包挂号'){
			$mc = '香港';
		}else if($carrier == 'EUB'){
			$mc = 'EUB';
		}else if($carrier == 'Global Mail'){
			$mc = 'GM';
		}else if($carrier == '德国邮政'){
			$mc = 'GM';
		}
		return @$flip_MAILWAYCONFIG[$mc];
	}
	
	/*
	 * 插入发货操作记录表
	 */
	public static function inserRecords($oidar,$printerId){
		self::initDB();
		$time = time();
		foreach($oidar as $order){			
			$string .= "('".$order."','". $printerId."','". $time."'),";
		}
		$string = trim($string,",");
		$sql    = "insert into wh_shipping_order_records(shipOrderId,printerId,printTime) values{$string}";
		$query	= self::$dbConn->query($sql);		
		if($query){	
			return true;	
		}else{
			return false;	
		}
	}
	
	//获取分区
	public static function showPartionScan($shipOrderId,$accountId,$carrier,$countryName){
		$sz_array = $sz_name_array =  array();
		$account  = CommonModel::getAccountNameById($accountId);
		$partions = CommonModel::getChannelNameByIds('all');
		foreach($partions as $partion){
			$sz_array[$partion['partitionCode']]   = $partion['countries'];
			$sz_name_array[$partion['partitionCode']]   = htmlspecialchars_decode($partion['partitionName']);
		}
		//匹配回邮地址信息
		$returnvalue = $carrier;
		foreach($sz_array as $sz_key => $sz_value){
			$sz_value_arr = explode("],[",$sz_value);
			$sz_value_arr[0] =  str_replace("[","",$sz_value_arr[0]);
			$sz_value_arr[count($sz_value_arr)-1] = str_replace("]","",$sz_value_arr[count($sz_value_arr)-1]);
			if(!empty($countryName) && in_array(trim($countryName),$sz_value_arr)){
				$returnvalue .= $sz_name_array[$sz_key];
				break;
			}
		}
		//增加因缺少国家直接为福七区的逻辑 add by guanyongjun 2014/03/07
		if (!in_array($countryName,array('Australia','Australla')) && empty($returnvalue)) {
			$returnvalue 	= $carrier.$sz_name_array[7];	
		}
		return $returnvalue;
		
		// if(strpos($carrier, '中国邮政')!==false){
			// if(in_array(trim($countryName), array('Albania','Algeria','Argentina','Egypt','Ethiopia','Estonia','Anguilla','Austria','Bahrain','Panama','Belarus','Bulgaria','Benin','Belgium','Iceland','Bosnia and Herzegovina','Bolivia','Botswana','Burkina Faso','Burundi','Denmark','Togo','Dominica','Russian Federation','Ecuador','Falkland Islands(Malvinas)','Gambia','Colombia','Costa Rica','Greenland','Georgia','Guyana','Haiti','Djibouti','Guinea','Guinea','Ghana','Cambodia','Czech Republic','Zimbabwe','Cameroon','Qatar','Cote d Ivoire (Ivory Coast)','Kuwait','Kenya','Latvia','Lesotho','Laos','Lebanon','Lithuania','Liberia','Libya','Rwanda','Romania','Madagascar','Malta','Malawi','Mali','Mauritania','Mongolia','Bangladesh','Peru','Morocco','Mozambique','Namibia','Nepal','Niger','Nigeria','Palau','Portugal','Sweden','Senegal','Cyprus','Seychelles','Slovakia','Sudan','Suriname','Tanzania','Trinidad and Tobago','Tunisia','Turkey','Venezuela','Uganda','Uruguay','Western Sahara','Greece','Hungary','Syria','Jamaica','Armenia','Yemen','Iraq','Iran','India','Zambia','Zaire','Chad','Chile','Cuba,Republic of','Congo, Republic of the','Congo, Democratic Republic of the','Russia','Azerbaijan Republic','Azerbaijan','Dominica','Dominican Republic','Equatorial Guinea','Gabon Republic','Papua New Guinea')) || in_array($account, array('taotaocart','arttao','taochains','etaosky','tmallbasket','mucheer','lantao','direttao','hitao','taolink'))){
				// return $carrier.$sz_name_array[7];
			// }else{
				// $mailways = self::getOrderGoodsMailwayIds($shipOrderId);
				// if(in_array(2,$mailways) || in_array(5,$mailways) || in_array(trim($countryName), array('Saudi Arabia','Malaysia'))){
					// $returnvalue = $carrier;
					// foreach($sz_array as $sz_key => $sz_value){
						// $sz_value_arr = explode("],[",$sz_value);
						// $sz_value_arr[0] =  str_replace("[","",$sz_value_arr[0]);
						// $sz_value_arr[count($sz_value_arr)-1] = str_replace("]","",$sz_value_arr[count($sz_value_arr)-1]);
						// if(!empty($countryName) && in_array(trim($countryName),$sz_value_arr)){
							// $returnvalue .= $sz_name_array[$sz_key];
							// break;
						// }
					// }
					// return $returnvalue;
				// }else if(in_array(1,$mailways)){
					// return $carrier.$sz_name_array[7];
				// } else {
					// return $carrier.$sz_name_array[7];
				// }
			// }
		// }
	}
	
	/*
	 * 验证配货单是否是最后一个
	 */
	public static function adjustIsLast($shipOrderId){
		self::initDB();	
		$whInfo = array();
		$relationInfo  = OmAvailableModel::getTNameList("wh_shipping_order_relation","originOrderId","where shipOrderId={$shipOrderId}");
		$relationInfo  = OmAvailableModel::getTNameList("wh_shipping_order_relation","*","where originOrderId={$relationInfo[0]['originOrderId']}");
		foreach($relationInfo as $info){
			$orderStatus = OmAvailableModel::getTNameList("wh_shipping_order","orderStatus","where id={$info['shipOrderId']}");
			if($orderStatus[0]['orderStatus']!=900){
				$orderInfo   = get_realskunum($info['shipOrderId']);
				foreach($orderInfo as $sku=>$num){
					if(isset($whInfo[$sku])){
						$whInfo[$sku] = $whInfo[$sku]+$num;
					}else{
						$whInfo[$sku] = $num;
					}
				}
			}
		}

		$OmRealskulist = CommonModel::getRealskulist($relationInfo[0]['originOrderId']);		
		foreach($OmRealskulist as $sku=>$num){
			if(array_key_exists($sku,$whInfo)){
				if($num!=$whInfo[$sku]){
					return false;
					break;
				}
			}else{
				return false;
				break;
			}
		}
		return true;
	}
	
	/*
	 * 获取部分包货子订单
	 */
	public static function getAllOriginOrderId($shipOrderId){
		self::initDB();	
		$whInfo		   = array();
		$orderIdArr    = array();
		$relationInfo  = OmAvailableModel::getTNameList("wh_shipping_order_relation","*","where shipOrderId={$shipOrderId}");
		$relationInfo  = OmAvailableModel::getTNameList("wh_shipping_order_relation","*","where originOrderId={$relationInfo[0]['originOrderId']}");

		$count 		   = count($relationInfo);
		if($count==1){
			return '';
		}else{
			foreach($relationInfo as $info){
				$orderStatus = OmAvailableModel::getTNameList("wh_shipping_order","orderStatus","where id={$info['shipOrderId']}");
				if($orderStatus[0]['orderStatus']!=900){
					if($info['shipOrderId']!=$shipOrderId){
						$orderIdArr[] = $info['shipOrderId'];
					}
				}
				
			}
			$str = implode(',',$orderIdArr);
			return $str;
		}
	}
    
    /**
     * printLabelModel::getExportData()
     * 
     * @param mixed $where
     * @return void
     */
    public static function getExportData($printer, $sku, $startdate, $enddate){
        self::initDB();
        $where  =   'a.is_delete = 0 and b.is_delete = 0';
        if(!empty($checkUser)){
			$where   .=  " and a.printerId = '{$printer}'";
		}
		
		if(!empty($sku)){
			$where   .=  " and b.sku = '{$sku}'";
		}
		if(!empty($startdate)){
			$start   =   strtotime($startdate);
			$where   .=  " and a.printTime >= '{$start}'";
		}
		if(!empty($enddate)){
			$end     =   strtotime($enddate)+86399;
			$where   .=  " and a.printTime <= '{$end}'";
		}
        $where  .=  ' group by a.tallyListId order by a.tallyListId desc';
        
        $sql    =   'select a.*, b.sku from wh_print_group a left join wh_tallying_list b on a.tallyListId = b.id where '.$where;
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
}
?>