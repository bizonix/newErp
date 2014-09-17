<?php
/*
*合并包裹功能
*ADD BY heminghua
*@last modified by Herman.Xi @20131213
*/
class CombinePackageModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	public static function selectList($tableName,$plateform_arr,$carrierIds,$id_array = '', $storeId = 1){
		
		self::initDB();
		$where = '';
		if($id_array){
			$where = 'AND a.id in ('.join(',',$id_array).') AND is_delete = 0 AND storeId = '.$storeId;
		}
		
		$sql = "SELECT a.id,a.platformId,a.accountId,a.transportId,a.orderStatus,a.orderType,a.calcWeight,b.userName,b.countryName,b.state,b.city,b.street 
				FROM ".$tableName." AS a LEFT JOIN ".$tableName."_userInfo as b 
				ON a.id=b.omOrderId
				WHERE a.isLock=0 AND a.is_delete=0 AND a.combinePackage=0 AND a.orderStatus=100 AND a.calcWeight<=2 AND a.orderType=101 AND a.platformId in (".join(',', $plateform_arr).") AND a.transportId in (".join(',', $carrierIds).") {$where}
				GROUP BY b.username, a.accountId, b.city, b.state, b.street, a.transportId
				HAVING count(*)>1";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	
	/*
	*@合并包裹功能方法
	*@ADD BY Herman.Xi
	*@last modified by Herman.Xi @20131213
	*/
	public static function combinePackage($tableName,$plateform_arr,$carrierIds,$id_array,$storeId = 1){
		self::initDB();
		$list = self::selectList($tableName,$plateform_arr,$carrierIds,$id_array);
		//var_dump($list); exit;
		if(!$list){
			self::$errCode = 301;
			self::$errMsg  = "没有需要合并的订单！";
			return false;
		}
		/*foreach($list as $key=>$value){
			$key = $value['id'];
		}
		var_dump($key); exit;*/
		BaseModel::begin();
		$combineNum = 0;
		foreach($list as $key=>$value){
			$where = "b.userName = '{$value['userName']}' 
					AND b.countryName = '{$value['countryName']}' 
					AND a.accountId = {$value['accountId']} 
					AND a.transportId = {$value['transportId']} 
					AND b.state='{$value['state']}' 
					AND b.city='{$value['city']}' 
					AND b.street='{$value['street']}' 
					AND a.orderType={$value['orderType']}
					AND a.calcWeight<=2 
					AND a.isLock=0 
					AND a.is_delete=0 
					AND a.combinePackage=0 
					AND a.orderStatus={$value['orderStatus']}
					AND a.orderType={$value['orderType']}
					AND is_delete = 0 AND storeId = ".$storeId;
			$records = combinePackageModel::selectRecord($tableName,$where, $id_array);
			//var_dump($records); exit;
			if(!$records){
				continue;
			}else{
				$weightlists = array();
				$orderinfo = array();
				$countryName = $records[0]['countryName'];
				$transportId = $records[0]['transportId'];
				foreach($records as $record){
					$omOrderId = $record['id'];
					$omOrderId = $record['id'];
					$orderinfo[$record['id']] = $record;
					$arrinfo = CommonModel::calcNowOrderWeight($omOrderId);
					//var_dump($arrinfo); exit;
					$realweight = $arrinfo[0];
					$realcosts = $arrinfo[2];
					$itemprices = $arrinfo[3];
					$weightlists[$omOrderId] = $realweight;
				}
				//var_dump($weightlists); exit;
				$keyarray = array();
				$keyarrays = array();
				$checkweight = 0;
				
				foreach($weightlists as $wk => $weightlist){
					$checkweight += $weightlist;
					if($checkweight>1.85){
						$keyarrays[] = $keyarray;
						$keyarray = array();
						$checkweight = $weightlist;
						$keyarray[] = $wk;
					}else{
						$keyarray[] = $wk;
					}
				}
				if(!empty($keyarray)){
					$keyarrays[] = $keyarray;
				}
				//var_dump($keyarrays); echo "<br>";
				
				foreach($keyarrays as $orderlist){
					if(count($orderlist) < 2){
						continue;
					}
					$ordervalueweight = array();
					$ordervalueactualTotal = array();
					foreach($orderlist as $orderid){
						$ordervalueweight[$orderid] = $weightlists[$orderid];
						$ordervalueactualTotal[$orderid] = $orderinfo[$orderid]['actualTotal'];
					}
					//var_dump($ordervalueactualTotal); exit;
					//var_dump($ordervalueweight); exit;
					$firstorder = array_shift($orderlist);//第一个订单编号信息
					//var_dump($firstorder);
					$combineInfo     = CommonModel::calcshippingfee(array_sum($ordervalueweight), $countryName, array_sum($ordervalueactualTotal), $transportId);//邮寄方式计算
					//var_dump($combineInfo); exit;
					$weight2fee      = calceveryweight($ordervalueweight, $combineInfo['fee']['fee']);
					//var_dump($weight2fee); exit;
					$firstweightfee  = array_shift($weight2fee);//第一个订单重量运费信息
					$data = array();
					$data['combinePackage'] = 1;
					$data['orderStatus'] = C('STATEPENDING');
					$data['orderType'] = C('STATEPENDING_CONPACK');
					$where = ' WHERE id = '.$firstorder;
					if(!OrderindexModel::updateOrder($tableName,$data,$where)){
						self::$errCode = 303;
						self::$errMsg  = "更新主订单失败！";
						BaseModel::rollback();
						return false;
					}
					foreach($orderlist as $sonorder){
						$data['combinePackage'] = 2;
						$data['orderStatus'] = C('STATEPENDING');
						$data['orderType'] = C('STATEPENDING_CONPACK');
						$where = ' WHERE id = '.$sonorder;
						if(!OrderindexModel::updateOrder($tableName,$data,$where)){
							self::$errCode = 304;
							self::$errMsg  = "更新子订单失败！";
							BaseModel::rollback();
							return false;
						}
						if(!OrderRecordModel::insertCombineRecord($firstorder,$sonorder)){
							self::$errCode = 305;
							self::$errMsg  = "插入订单合并记录失败！";
							BaseModel::rollback();
							return false;
						}
					}
					$combineNum++;
				}
			}
		}
		self::$errCode = 200;
		self::$errMsg  = "合并包裹操作成功！";
		BaseModel :: commit();
		BaseModel :: autoCommit();
		return $combineNum;
	}
	
	public static function selectRecord($tableName,$where, $id_array = ''){
		self::initDB();
		if($id_array){
			$where .= ' AND a.id in ('.join(',',$id_array).') ';
		}
		$sql = "SELECT a.id,a.actualTotal,a.platformId,a.accountId,a.transportId,a.orderStatus,a.orderType,a.calcWeight,b.username,b.countryName,b.state,b.city,b.street
				FROM ".$tableName." AS a LEFT JOIN ".$tableName."_userInfo as b
				ON a.id=b.omOrderId
				WHERE {$where}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	
	public static function selectRecordByOrderId($omOrderId){
		self::initDB();
		$sql = "SELECT *
				FROM om_records_combinePackage
				WHERE main_order_id = ".$omOrderId." and is_enable = 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = array();
			while($row = self :: $dbConn->fetch_array($query)){
				$ret[] = $row['split_order_id'];
			}
			return $ret;
		}else{
			return false;
		}
	}
	
	public static function selectAllRecordByOrderId($omOrderId){
		self::initDB();
		$sql = "SELECT *
				FROM om_records_combinePackage
				WHERE main_order_id = ".$omOrderId." and is_enable = 1";
		//echo $sql; echo "<br>";
		$query = self::$dbConn->query($sql);
		$ret[] = $omOrderId;
		if($query){
			//$ret = array();
			while($row = self :: $dbConn->fetch_array($query)){
				$ret[] = $row['split_order_id'];
			}
			return $ret;
		}
		return $ret;
	}
	
}
?>