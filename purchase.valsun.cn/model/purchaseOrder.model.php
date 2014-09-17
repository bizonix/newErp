<?php
class PurchaseOrderModel{
	public static $dbConn;
	public static $prefix;
	public static $errCode = 0;
	public static $errMsg = "";
	public static $table;

	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
		self::$table   =  self::$prefix."order";; 
	}

	public static function error($errCode,$errMsg){
		self::$errCode=$errCode;
		self::$errMsg=$errMsg;
		return false;	
	}

	public static function getResult($fields,$table,$where=""){
		self::initDB();
		if($where==""){
			$where = 1;
		}
		$where .= " and is_delete = '0' ";
		$sql = "SELECT ".$fields." FROM ".$table." WHERE ".$where;
		$query=self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}
		return false;
	}

	public static function getUpdateExcute($table,$set,$where){
		self::initDB();
		$where = " and  ".$where;
		$sql = "UPDATE ".$table." SET ".$set."  WHERE is_delete=0 ".$where;
		//echo $sql;
		$query=self::$dbConn->query($sql);
		if($query){
			return true;
		}
		return false;
	}

	public static function queryInsertIntoExcute($table,$valFields,$valCont){
		$sql = 'insert into '.$table.'  (' . $valFields . ') values (' . $valCont . ')';
		$query=self::$dbConn->query($sql);
		if($query){
			return true;
		}
		return false;
	}

	public static function purchasehistoryprice($sku){
		self::initDB();
		$sql = "SELECT
				pd.sku,
				po.addtime,
				po.aduittime,
				pd.id,
				pd.price
				FROM
				".self::$prefix."order_detail AS pd 
				LEFT JOIN ".self::$prefix."order AS po ON pd.po_id = po.id
				WHERE
				pd.sku = '{$sku}'";				
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}
		return false;
	}

	public static function delPhOrderDetail($id){
		self::initDB();
		$sql = "UPDATE ".self::$prefix."order_detail SET is_delete = 1 WHERE id=".$id;
		$query = self::$dbConn->query($sql);
		if($query){
			return true;
		}
		return false;
	}

	public static function phOrder($id){
		return self::getOrderList("po.id=".$id);
	}

	public static function countByStatus($stat, $powerlist, $paystatus){
		self::initDB();
		$sql = "SELECT count(*) as total FROM ".self::$prefix."order WHERE is_delete=0 AND status= '{$stat}'";
		if(!empty($powerlist)){
			$sql .= " AND purchaseuser_id in ({$powerlist})";
		}
		if($paystatus != 0){
			$sql .= " AND paystatus = '{$paystatus}'";
		}
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return isset($ret[0]['total']) ? $ret[0]['total'] : '0';
		}
		self::error("01","获取采购订单列表失败！");
		return false;
	}

	//获取采购订单信息列表
	public static function	getOrderList($where="",$limit=""){
		self::initDB();
		if($where!=''){
			$where=" AND ".$where;
		}
		$where = " WHERE  po.is_delete=0 ".$where;
 		$sql   = "SELECT DISTINCT
 					po.warehouse_id,
					po.note,
					po.aduituser_id,
					po.order_type,
					po.id,
					po.recordnumber,
					po.status,
					po.addtime,
					po.finishtime,
					po.paymethod,
					po.paystatus,
					po.purchaseuser_id,
					po.partner_id,
 				    po.aduittime
				    FROM
				".self::$prefix."order AS po 
				LEFT JOIN `".self::$prefix."order_detail` AS pd ON po.id = pd.po_id
 				".$where." ORDER BY id DESC  ".$limit;
		$query=self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}
		return false;
	}

	public static function getCountOrderList($where){
		self::initDB();
		if($where!=''){
			$where=" AND ".$where;
		}
		$where 	=" WHERE  po.is_delete=0 ".$where;
		$sql 	="SELECT 
						COUNT(DISTINCT(po.id)) as totoal
				FROM
					".self::$prefix."order AS po
				LEFT JOIN `".self::$prefix."order_detail` AS pd ON po.id = pd.po_id
				LEFT JOIN pc_goods as pg ON pg.sku = pd.sku
 				".$where;
		$query 	= self::$dbConn->query($sql);
		if($query){
				$ret = self::$dbConn->fetch_array_all($query);
				$ret = $ret[0]['totoal'];
				return $ret;
			}else{
				return false;
			}
	}
	
	public static function	getOrderDetaiList($where){
		self::initDB();
		if($where!=''){
			$where=" and ".$where;
		}
		$where="WHERE is_delete=0 ".$where;
 		$sql="SELECT id,po_id,price,count,stockqty FROM  `".self::$prefix."order_detail` ".$where;
		$query=self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}
		self::error("02","获取采购订单详情列表失败！");	
		return false;
	}

	/**
	 *功能:自动生成采购订单号
	 *@param $userid 用户编号
	 *@return 成功返回：订单号;失败返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function autoCreateOrderSn($userid, $company){
		self::initDB();
		switch($company){
			case '1':
				$orderhead = 'SWB';
				break;
			case '2':
				$orderhead = 'FZ';
				break;
			case '4':
				$orderhead = 'ZG';
				break;
			default:
				$orderhead = 'SWB';
				break;
		}
		while(1){
			$recordnumber = $orderhead.date("ymd").$userid.rand(100, 999);
			if($company=='3'){
				$sql = "SELECT ordersn FROM ".C('DB_PREFIX')."stock_invoice WHERE ordersn = '{$recordnumber}' AND is_delete = 0";	
			}else{
				$sql = "SELECT recordnumber FROM ".C('DB_PREFIX')."order WHERE recordnumber = '{$recordnumber}' AND is_delete = 0";
			}
			$query = self::$dbConn->query($sql);
			if($query){
				$num = self::$dbConn->num_rows($query);
				if($num == 0){
					return $recordnumber;
					break;
				}
			}
		}
	}

	/**
	 *功能:添加采购订单主表信息---新生成
	 *@param $info 订单主表数组信息
	 *@return 成功返回:true;失败返回:false
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function insertMainOrder($info){
		self::initDB();
		self::$dbConn->begin();
		$msg    			= '';
		$recordnumber 		= $info['recordnumber'];//采购订单号
		$purchaseuser_id 	= $info['purchaseuser_id'];//采购员编号
		$warehouse_id		= $info['warehouse_id'];//仓库编号
		$partner_id   		= $info['partner_id'];//供应商编号
		$company_id   		= $info['company_id'];//公司编号
		$operator_id		= $info['operator_id'];//操作人员id
		$order_type         = $info['order_type'];
		$note               = 'MRP运算生成';
		$addtime 			= time();//添加订单时间
		$insert  = "INSERT INTO ".C('DB_PREFIX')."order ( recordnumber, purchaseuser_id,operator_id,warehouse_id, partner_id, company_id, addtime, note,order_type)";
		$insert .= " VALUES ('{$recordnumber}', '{$purchaseuser_id}','{$operator_id}','{$warehouse_id}', '{$partner_id}' ,'{$company_id}', '{$addtime}', '{$note}','{$order_type}')";
		$query   = self::$dbConn->query($insert);
		if($query){
			$num = self::$dbConn->insert_id($query);
			if($num > 0){
				$msg = 'success';//添加成功
			}else{
				$msg = 'failure';//添加失败
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "插入语句错误";
			return false;
		}
		if($msg == 'success'){
			self::$dbConn->commit();
			return true;
		}else{
			self::$dbConn->rollback();
			return false;
		}
	}

	/**
	 *功能:添加采购订单主表信息---采购补单
	 *@param $info 订单主表数组信息
	 *@return 成功返回:true;失败返回:false
	 *日期:2013/08/08
	 *作者:王民伟
	 */
	public static function insertPatchMainOrder($info,$status='',$order_type=''){
		self::initDB();
		self::$dbConn->begin();
		$msg    			= '';
		$recordnumber 		= $info['recordnumber'];//采购订单号
		$purchaseuser_id 	= $info['purchaseuser_id'];//采购员编号
		$partner_id   		= $info['partner_id'];//供应商编号
		$company_id   		= $info['company_id'];//公司编号 
		$addtime 			= time();//添加订单时间
		$finishtime 	    = time();//完成订单时间
		if($status=='' && $order_type==''){
			$status             = 4;//完成
			$order_type         = 4;//采购补单类型
		}
		$insert  = "INSERT INTO ".C('DB_PREFIX')."order ( recordnumber, purchaseuser_id, partner_id, company_id, addtime, finishtime, status, order_type)";
		$insert .= " VALUES ( '{$recordnumber}', '{$purchaseuser_id}',  '{$partner_id}' ,'{$company_id}', '{$addtime}', '{$finishtime}', '{$status}', '{$order_type}')";
		//echo '添加采购补单:'.$insert."<br/>";
		$query   = self::$dbConn->query($insert);
		if($query){
			$num = self::$dbConn->insert_id($query);
			if($num > 0){
				$msg = 'success';//添加成功
			}else{
				$msg = 'failure';//添加失败
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "插入语句错误";
			return false;
		}
		if($msg == 'success'){
			self::$dbConn->commit();
			return true;
		}else{
			self::$dbConn->rollback();
			return false;
		}
	}

	/**
	 *功能:添加采购订单主表信息---不良品退货单
	 *@param $info 订单主表数组信息
	 *@return 成功返回:true;失败返回:false
	 *日期:2013/08/08
	 *作者:王民伟
	 */
	public static function insertReturnMainOrder($info){
		self::initDB();
		self::$dbConn->begin();
		$msg    			= '';
		$recordnumber 		= $info['recordnumber'];//采购订单号
		$purchaseuser_id 	= $info['purchaseuser_id'];//采购员编号
		$partner_id   		= $info['partner_id'];//供应商编号
		$company_id   		= $info['company_id'];//公司编号
		$addtime 			= time();//添加订单时间
		$status             = 1;//未审核
		$order_type         = 2;//不良品退货类型
		$insert  = "INSERT INTO ".C('DB_PREFIX')."order ( recordnumber, purchaseuser_id, partner_id, company_id, addtime, status, order_type)";
		$insert .= " VALUES ( '{$recordnumber}', '{$purchaseuser_id}',  '{$partner_id}' ,'{$company_id}', '{$addtime}', '{$status}', '{$order_type}')";
		//echo '生成不良品清单:'.$insert."<br/>";
		$query   = self::$dbConn->query($insert);
		if($query){
			$num = self::$dbConn->insert_id($query);
			if($num > 0){
				$msg = 'success';//添加成功
			}else{
				$msg = 'failure';//添加失败
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "插入语句错误";
			return false;
		}
		if($msg == 'success'){
			self::$dbConn->commit();
			return true;
		}else{
			self::$dbConn->rollback();
			return false;
		}
	}

	/**
	 *功能:添加采购订单明细
	 *@param $recordnumber 订单号
	 *@param $info 订单明细数组信息
	 *@return 成功返回：true;失败返回:false;
	 *日期:2013/11/13
	 *作者:王民伟
	 */
	public static function insertDetailOrder($poid, $info){
		self::initDB();
		self::$dbConn->begin();
		$msg    = '';
		$sku    = $info['sku'];
		$price  = $info['price'];
		$count  = $info['count'];
		$addtime = time();//添加时间
		$isExist = "SELECT id FROM ".C('DB_PREFIX')."order_detail WHERE sku = '{$sku}' AND po_id = '{$poid}' AND is_delete = 0";
		$exist   = self::$dbConn->query($isExist);
		if($exist){
			$num = self::$dbConn->num_rows($exist);
			if($num == 0){//不可以反复添加同一个订单下的相同sku
				$insert = "INSERT INTO ".C('DB_PREFIX')."order_detail ( po_id,  sku, price, count, add_time) VALUES ('{$poid}','{$sku}', '{$price}', '{$count}', '{$addtime}')";
				//echo '添加订单明细:'.$insert."<br/>";
				$query  = self::$dbConn->query($insert);
				if($query){
					$num = self::$dbConn->insert_id($query);
					if($num > 0){
						$msg = 'success';
					}else{
						$msg = 'failure';
					}
				}else{
					self::$errCode	= "8001";
					self::$errMsg	= "插入语句错误";
					return false;
				}
			}else{
				$msg = 'success';
			}
		}
		if($msg == 'success'){
			self::$dbConn->commit();
			return true;
		}else{
			self::$dbConn->rollback();
			return false;
		}
	}

	/**
	 *功能:添加采购补单明细
	 *@param $recordnumber 订单号
	 *@param $info 订单明细数组信息
	 *@return 成功返回：true;失败返回:false;
	 *日期:2013/08/08
	 *作者:王民伟
	 */
	public static function insertPatchDetailOrder($poid, $info){
		self::initDB();
		self::$dbConn->begin();
		$msg      = '';
		$price     = $info['price'];
		$count    = $info['count'];
		$amount = $info['count'];
		$sku         = $info['sku'];
		$nowtime = time();
		$insert = "INSERT INTO ph_order_detail ( sku, po_id, price, count, stockqty, add_time, reach_time) VALUES ('{$sku}','{$poid}','{$price}', '{$count}', '{$count}', '{$nowtime}', '{$nowtime}')";
		//echo '添中订单明细:'.$insert."<br/>";
		$query  = self::$dbConn->query($insert);
		if($query){
			$num = self::$dbConn->insert_id($query);
			if($num > 0){
				$msg = 'success';
			}else{
				$msg = 'failure';
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "插入语句错误";
			return false;
		}
		if($msg == 'success'){
			self::$dbConn->commit();
			return true;
		}else{
			self::$dbConn->rollback();
			return false;
		}
	}

	/**
	 *功能:采购只能下单自己的SKU
	 *@param $purchaseuser_id 采购员编号
	 *@param $skuidlist SKU编号数组
	 *@return 成功返回：sku编号;失败返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function searchPurchaseSelfSku($purchaseuser_id, $skuidlist){
		self::initDB();
		$sql = "SELECT id FROM pc_goods WHERE purchaseId = '{$purchaseuser_id}' AND id IN({$skuidlist})";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据编号取SKU
	 *@param $skuidSKU编号
	 *@return 成功返回：sku;失败返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function getSkuById($skuid){
		self::initDB();
		$sql = "SELECT spu, sku, goodsName FROM pc_goods where id = '{$skuid}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据SKU取SKU编号
	 *@param $sku
	 *@return 成功返回：编号;失败返回:false;
	 *日期:2013/11/13
	 *作者:王民伟
	 */
	public static function getSkuIdBySku($sku){
		self::initDB();
		$sql = "SELECT id FROM pc_goods where sku = '{$sku}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data[0]['id'];
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据SKU获取单价
	 *@param $sku
	 *@return 成功返回：单价;失败返回:false;
	 *日期:2013/11/13
	 *作者:王民伟
	 */
	public static function getPriceBySku($sku){
		self::initDB();
		$sql = "SELECT goodsCost FROM pc_goods where sku = '{$sku}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data[0]['goodsCost'];
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据SKU获取所属采购员编号
	 *@param $sku
	 *@return 成功返回：编号;失败返回:false;
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	public static function getPurIdBySku($sku){
		self::initDB();
		$sql = "SELECT purchaseId FROM pc_goods where sku = '{$sku}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data[0]['purchaseId'];
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:判断采购订单号是否已存在==正常订单
	 *@param $warehouse_id 仓库编号
	 *@param $partner_id 供应商编号
	 *@param $purchaseuser_id 采购员编号
	 *@return 存在返回：订单号;不存在返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function isExistOrdersn($warehouse_id, $partner_id, $purchaseuser_id){
		global $dbConn;
		self::initDB();
		$partnerId = $this->getSamePartnerIds($partner_id);
		$partnerIds = explode(",",$partnerId);
		$sql  = "SELECT recordnumber FROM ".C('DB_PREFIX')."order WHERE status = 1 AND order_type = 1 AND warehouse_id = '{$warehouse_id}' ";
		$sql .= " AND purchaseuser_id = '{$purchaseuser_id}' AND partner_id in ( {$partnerIds} )  AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(empty($rtn_data)){
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}else{
				return $rtn_data[0]['recordnumber'];
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}


	/**
	 *功能:判断采购订单记录号是否存在==采购补单
	 *@param $warehouse_id 仓库编号
	 *@param $partner_id 供应商编号
	 *@param $purchaseuser_id 采购员编号
	 *@return 存在返回：订单号;不存在返回:false;
	 *日期:2013/08/08
	 *作者:王民伟
	 */
	public static function isExistPatchOrdersn($partner_id, $purchaseuser_id,$status_type=''){
		self::initDB();
		$nowtime = strtotime(date("Y-m-d")." 00:00:01");
		$sql  = "SELECT recordnumber FROM ".C('DB_PREFIX')."order WHERE ";
		if(empty($status_type)){
			$sql  .= " status = 4 AND order_type = 4 ";
		}else{
			$sql .= $status_type;
		}
		$sql .= " AND purchaseuser_id = '{$purchaseuser_id}' AND partner_id = '{$partner_id}' AND is_delete = 0 AND addtime > $nowtime";
		//echo $sql."<br/>";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(empty($rtn_data)){
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}else{
				return $rtn_data[0]['recordnumber'];
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:判断采购订单记录号是否存在==不良品退货单
	 *@param $warehouse_id 仓库编号
	 *@param $partner_id 供应商编号
	 *@param $purchaseuser_id 采购员编号
	 *@return 存在返回：订单号;不存在返回:false;
	 *日期:2013/08/08
	 *作者:王民伟
	 */
	public static function isExistReturnOrdersn($order_type,$partner_id, $purchaseuser_id){
		self::initDB();
		$nowtime = strtotime(date("Y-m-d")." 00:00:01");
		$sql  = "SELECT recordnumber FROM ".C('DB_PREFIX')."order WHERE status = 1 AND order_type = 2  ";
		$sql .= " AND order_type = 2 AND purchaseuser_id = '{$purchaseuser_id}' AND partner_id = '{$partner_id}' AND is_delete = 0 AND addtime > $nowtime";
		//echo $sql."<br/>";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(empty($rtn_data)){
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}else{
				return $rtn_data[0]['recordnumber'];
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据跟踪号取采购订单主表id
	 *@param $recordnumber 跟踪号
	 *@return 存在返回：id;不存在返回:false;
	 *日期:2013/11/13
	 *作者:王民伟
	 */
	public static function getOrderIdByNum($recordnumber){
		self::initDB();
		$sql  = "SELECT id FROM ".C('DB_PREFIX')."order WHERE recordnumber = '{$recordnumber}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(empty($rtn_data)){
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}else{
				return $rtn_data[0]['id'];
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}
	/**
	 *功能:判断SKU是否已存在
	 *@param $sku SKU
	 *@return 存在返回：true;不存在返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function checkSkuExist($sku){
		self::initDB();
		$sql = "SELECT id FROM pc_goods WHERE sku = '{$sku}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$num = self::$dbConn->num_rows($query);
			if($num != 0){
				return true;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据SKU返回生成订单需要的数据,如单价等等
	 *@param $skulist 支持单个或数组
	 *@return 存在返回：信息;不存在返回:false;
	 *日期:2013/08/06
	 *作者:王民伟
	 */
	public static function getPurSkuInfo($skulist, $purid){
		self::initDB();
		for ($i=0; $i < count($skulist); $i++) { 
			$sku .= "'".$skulist[$i]."',";
		}
		$res			= CommonAct::actGetPurchaseAccess(); //获取所属下的采购id
		$purid  = $res['power_ids'];
		$sku = substr($sku, 0, strlen($sku) - 1);
		$sql = "SELECT g.sku, g.goodsCost, gp.partnerId as partnerid FROM pc_goods  g ";
		$sql .= " LEFT JOIN ".C('DB_PREFIX')."goods_partner_relation gp ON g.sku = gp.sku ";
		$sql .= " WHERE g.sku IN ({$sku}) AND g.is_delete = 0  ";
		if(!empty($purid)){
			$sql .= " AND g.purchaseId IN ({$purid}) ";
		}
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:根据SKU编号、仓库编号、采购员编号获取已订单数量
	 *@param $skuid SKU编号
	 *@param $warehouse_id 仓库编号
	 *@param $purid 采购员编号
	 *@return 存在返回：已订购数量;不存在返回:false;
	 *日期:2013/08/06
	 *作者:王民伟
	 */
	public static function hasBookNum($skuid, $warehouse_id, $purid){
		self::initDB();
		$qty 	  = 0;
		$stockqty = 0;
		$sql  = "SELECT b.count as qty, b.stockqty FROM ".C('DB_PREFIX')."order AS a LEFT JOIN ".C('DB_PREFIX')."order_detail AS b ON a.id = b.po_id ";
		$sql .= "WHERE  ( a.status ='1' OR a.status = '2' OR a.status = '3') ";
		$sql .= "AND order_type ='1' AND a.purchaseuser_id = '{$purid}' AND a.warehouse_id = '{$warehouse_id}' AND a.is_delete = 0 AND b.is_delete = 0 LIMIT 1 ";
		$query = self::$dbConn->query($sql);
		if($query){
			$list = self::$dbConn->fetch_array_all($query);
			if(!empty($list)){
				foreach ($list as $key => $v) {
					$qty      += $v['qty'];//订单采购数量
					$stockqty += $v['stockqty'];//已到货入库数量
				}
				$bookqty = $qty - $stockqty;//已订购数量
				return $bookqty;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:更新SKU预警信息
	 *@param $sku SKU
	 *@param $dataarray 数组信息
	 *日期:2013/08/06
	 *作者:王民伟
	 */
	public static function updateWarnList($sku, $dataarray){
		self::initDB();
		$update  = "UPDATE ".C('DB_PREFIX')."sku_info_tmp SET everyday_sale = '{$dataarray['everyday_sale']}', waiting_send = '{$dataarray['waiting_send']}', ";
		$update .= " booknums = '{$dataarray['booknums']}', interceptnums = '{$dataarray['interceptnums']}', autointerceptnums = '{$dataarray['autointerceptnums']}', ";
		$update .= " lastupdate = '{$dataarray['lastupdate']}', is_warning = '{$dataarray['is_warning']}' WHERE sku = '{$sku}'";
		//echo "更新预警语句:".$update."\n";
		$query 	 = self::$dbConn->query($update);
		if($query){
			$num = self::$dbConn->affected_rows();
			if($num>=0){
				return true;
			}else{
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "更新语句错误";
			return false;
		}
	}

	/**
	 *功能:返回采购员负责的SKU信息
	 *@param $purid 采购员编号
	 *@param $skulist SKU
	 *日期:2013/08/06
	 *作者:王民伟
	 */
	public static function getSkuByPurId($purid, $skulist){
		self::initDB();
		$inskulist = '';
		if(!empty($skulist)){
			for ($i=0; $i < count($skulist); $i++) { 
				$inskulist .= "'".$skulist[$i]."',";
			}
		}
		$info  = "SELECT a.sku, a.purchase_days, a.alert_days, a.stock_qty, a.everyday_sale, a.autointerceptnums, a.interceptnums, a.waiting_send, b.warehouseid, b.partnerid FROM ".C('DB_PREFIX')."sku_info_tmp AS a ";
		$info .= "LEFT JOIN pc_goods AS b ON a.sku = b.sku WHERE b.purchaseId = '{$purid}' AND b.is_delete = 0 ";
		if(!empty($inskulist)){
			$info .= "AND a.sku in ({$inskulist})";
		}
		$query = self::$dbConn->query($info);
		if($query){
			$list = self::$dbConn->fetch_array_all($query);
			if(!empty($list)){
				return $list;
			}else{
				self::$errCode	= "8004";
				self::$errMsg	= "返回数据为空";
				return false;
			}
		}else{
			self::$errCode	= "8001";
			self::$errMsg	= "查询语句错误";
			return false;
		}
	}

	/**
	 *功能:到货入库检索订单号是否在在途状态下
	 *@param $skuid SKU编号
	 *日期:2013/08/07
	 *作者:王民伟
	 */
	public static function checkStockInOrder($skuid){
		self::initDB();
		$sql  = "SELECT b.id, b.count, b.qty1, b.qty2, b.qty3, b.stockqty, b.po_id FROM ".C('DB_PREFIX')."order as a LEFT JOIN ".C('DB_PREFIX')."order_detail as b ";
		$sql .= " ON a.id = b.po_id WHERE a.status = 3 AND a.order_type = 1 ";
		$sql .= " AND   a.is_delete = 0 AND b.is_delete = 0 order by a.addtime";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtn_data = self::$dbConn->fetch_array_all($query);
			if(!empty($rtn_data)){
				return $rtn_data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 *功能:更新在途订单已到货数量
	 *@param $id 编号
	 *@param $stockqty 已入库数量
	 *@param $amount 本次入库数量
	 *@param $signqty 记录第n次到货数量
	 *日期:2013/08/07
	 *作者:王民伟
	 */
	public static function updateStockInAmount($id, $stockqty, $amount, $signqty){
		self::initDB();
		switch($signqty){
			case 'qty1':
				$sign = " , qty1 = '{$amount}'";
				break;
			case 'qty2':
				$sign = " , qty2 = '{$amount}'";
				break;
			case 'qty3':
				$sign = " , qty3 = '{$amount}'";
				break;
			default:
				$sign = '';
				break;
		}
		$update = "UPDATE ".C('DB_PREFIX')."order_detail SET stockqty = '{$stockqty}'".$sign." WHERE id = '{$id}' AND is_delete = 0";
		$query  = self::$dbConn->query($update);
		if($query){
			$num = self::$dbConn->affected_rows();
			if($num>=0){
				$sql = "SELECT count, stockqty FROM ".C('DB_PREFIX')."order_detail WHERE id = '{$id}' AND is_delete = 0 ";
				$query = self::$dbConn->query($sql);
				if($query){
					$list = self::$dbConn->fetch_array_all($query);
					if($list[0]['count']==$list[0]['stockqty']){
						$reach_time = time();
						$updatetime = "UPDATE ".C('DB_PREFIX')."order_detail SET reach_time = '{$reach_time}' WHERE id = '{$id}' AND is_delete = 0";
						$querytime = self::$dbConn->query($updatetime);
					}
				}
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 *功能:返回订单号入库详情
	 *@param $poid 订单号
	 *日期:2013/08/07
	 *作者:王民伟
	 */
	public static function getOrderStockInDetail($poid){
		self::initDB();
		$sql = "SELECT stockqty, count FROM ".C('DB_PREFIX')."order_detail WHERE po_id ='{$poid}' AND is_delete = 0";
		$query = self::$dbConn->query($sql);
		if($query){
			$list = self::$dbConn->fetch_array_all($query);
			if(!empty($list)){
				return $list;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 *功能:更新订单状态
	 *@param $poid 订单号
	 *@param $status 更新后的状态
	 *日期:2013/08/07
	 *作者:王民伟
	 */
	public static function updateOrderStatus($poid, $status){
		self::initDB();
		$finishtime = time();
		$update = "UPDATE ".C('DB_PREFIX')."order SET status = '{$status}', finishtime='{$finishtime}' WHERE id = '{$poid}' AND is_delete = 0";
		//echo '订单完成入库:'.$update."<br/>";
		$query  = self::$dbConn->query($update);
		if($query){
			$num = self::$dbConn->affected_rows();
			if($num >= 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function goodsUpdateToGoodsTmp(){
		self::initDB();
		$sql = "SELECT sku FROM pc_goods ";
		$query = self::$dbConn->query($sql);
		if($query){
			$list = self::$dbConn->fetch_array_all($query);
			if(!empty($list)){
				foreach ($list as $key => $v) {
					$sku = $v['sku'];
					$sql2 = "SELECT sku FROM ph_sku_info_tmp WHERE sku = '{$sku}'";
					$query2 = self::$dbConn->query($sql2);
					if($query2){
						$list2 = self::$dbConn->fetch_array_all($query2);
						if(empty($list2)){
							$purchase_days = rand(10,30);
							$alert_days    = rand(15,25);
							$everyday_sale = rand(1,10);
							$insert = "INSERT INTO ph_sku_info_tmp(sku,purchase_days,alert_days,everyday_sale)VALUES('{$sku}','{$purchase_days}','{$alert_days}','{everyday_sale}')";
							echo $insert."\n";
							$in = self::$dbConn->query($insert);

						}
					}
				}
			}
		}
	}
	public static function getCountAdjustTransport($where=""){
		self::initDB();
		if(!empty($where)){
			$where = " and  ".$where;
		}
		$where = " WHERE is_delete = '0 ' ".$where;
		$sql = "SELECT count(*) as total FROM ".self::$prefix."adjust_transport ".$where;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			if($ret["total"]){
				return $ret["total"];
			}else{
				return false;
			}
		}
		return false;
	}
	public static function getAdjustTransport( $where="", $limit="" ){
		self::initDB();
		if(!empty($where)){
			$where = " and  ".$where;
		}
		if(!empty($limit)){
				$limit = "  ".$limit;
		}
		$where = " WHERE is_delete = '0 ' ".$where;
		$sql ="SELECT id,category,skulist,country,original_transport,current_transport,is_show  FROM ".self::$prefix."adjust_transport ".$where.$limit;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if($ret[0]["id"]){
				return $ret;
			}else{
				return false;
			}
		}
		return false;
	}
	public static function adjustTransportContent($id){
		self::initDB();
		$sql = "SELECT * FROM ".self::$prefix."adjust_transport WHERE is_delete = '0' and id='".$id."'";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
// 			var_dump($sql,$ret);
			if($ret[0]["id"]){
				return $ret;
			}else{
				return false;
			}
		}
		return false;
	}
	public static function selectOneTable($table,$fields,$where = ''){
		self::initDB();
		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		$sql = " SELECT {$fields} FROM {$table}  {$where}";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			$fieldsArr = explode(",",$fields);
			$oneField = $fieldsArr[0];
			if($ret){
				return $ret;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function updateOneTable($table,$set,$where = ''){
		self::initDB();
		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		$set = "SET {$set}" ;
		$sql = " UPDATE  {$table} {$set}  {$where}";
		$query = self::$dbConn->query($sql);
		if($query){
			$num = self::$dbConn->affected_rows(self::$dbConn->link);
			if($num>0){
				return true;
			}else{
				return false;
			}
		}
		
		return false;
		
	}
   public static function purchase_sku_conversion(){
	   	self::initDB();
	   	$table = self::$prefix."sku_conversion";
	   	$fields = "*";
	   	$where = "is_delete = '0' ORDER BY  ID DESC";
	   	$list = self::selectOneTable($table, $fields,$where);
		if($list){	
			return $list;
		}
	   	 return false;
   } 
   public static function insertIntoOne($table,$set){
   		self::initDB();
   		$sql = "INSERT INTO {$table} SET {$set}";
   		$query = self::$dbConn->query($sql);
   		if($query){
   			$num = self::$dbConn->affected_rows(self::$dbConn->link);
   			if($num>0){
   				return true;
   			}else{
   				return false;
   			}
   		}
   		
   		return false;
   }
	/**
	 *功能:根据订单编号获取采购订单主表信息
	 *@param $id
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getMainOrderInfo($id){
		self::initDB();
		$sql  	= "SELECT id, recordnumber, addtime, aduittime, aduituser_id, status, warehouse_id, purchaseuser_id, partner_id, paymethod, note";
		$sql   .= " FROM ".C('DB_PREFIX')."order WHERE id = '{$id}' AND is_delete = 0";
		$query  = self::$dbConn->query($sql);
		if($query){
			//$data = self::$dbConn->fetch_array_all($query);
			$data = self::$dbConn->fetch_one($query);
			return $data;
		}else{
			return false;
		}
	}

	/**
	 *功能:根据订单编号获取采购订单明细表信息
	 *@param $id
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getDetailOrderInfo($poid){
		self::initDB();
		$sql  	= "SELECT id,  sku, price, count, stockqty FROM ".C('DB_PREFIX')."order_detail WHERE po_id = '{$poid}' AND is_delete = 0";
		$query  = self::$dbConn->query($sql);
		if($query){
			$data = self::$dbConn->fetch_array_all($query);
			return $data;
		}else{
			return false;
		}
	}
	/**
	 *功能:根据SKU获取每日均量等相关信息
	 *@param $sku
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	 function getWarnInfoBySku($sku){
	 	self::initDB();
		$sql    = "SELECT everyday_sale, booknums, sevendays, fifteendays, thirtydays, salensend, interceptnums, autointerceptnums, auditingnums, stock_qty FROM ".C('DB_PREFIX')."sku_statistics WHERE sku = '{$sku}'";
		$query  = self::$dbConn->query($sql);
		if($query){
			$data = self::$dbConn->fetch_array_all($query);
			return $data;
		}else{
			return false;
		}
	}

	/**
	 *功能:判断采购订单明细是否已存在SKU
	 *@param $poid  订单编号
	 *@param $sku
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function orderIsExistSku($poid, $sku){
		self::initDB();
		$sql 	= "SELECT id FROM ".C('DB_PREFIX')."order_detail WHERE sku = '{$sku}' AND po_id = '{$poid}' AND is_delete = 0";
		$query  = self::$dbConn->query($sql);
		if($query){
			$data = self::$dbConn->fetch_array_all($query);
			return $data;
		}else{
			return false;
		}
	}

	/**
	 *功能:根据供应商ID获取名称
	 *@param $id  供应商编号
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getParNameById($id){
		self::initDB();
		$sql 	= "SELECT company_name FROM ph_partner WHERE id = '{$id}' AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data = self::$dbConn->fetch_array_all($query);
			if(!empty($data)){
				return $data[0]['company_name'];
			}else{
				return '';
			}
		}else{
			return '';
		}
	}
	/**
	 *功能:根据用户ID获取姓名
	 *@param $id  用户编号
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getNameById($id){
		self::initDB();
		$sql 	= "SELECT global_user_name FROM power_global_user WHERE global_user_id = '{$id}'";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data = self::$dbConn->fetch_array_all($query);
			if(!empty($data)){
				return $data[0]['global_user_name'];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/**
	 *功能:采购订单报表导出
	 *@param $data  订单编号数组
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function exportOrder($data){
		self::initDB();
		$num = 0;
		foreach($data as $id){
			$sql 	    = "SELECT a.addtime, a.recordnumber, a.partner_id, a.purchaseuser_id, b.sku, b.price, b.count  FROM ".C('DB_PREFIX')."order as a ";
			$sql       .= " JOIN ".C('DB_PREFIX')."order_detail as b ON a.id = b.po_id WHERE a.id = '{$id}' AND a.is_delete = 0 AND b.is_delete = 0 ";
			$query  	= self::$dbConn->query($sql);
			$datalist 	= array();
			if($query){
				$rtnData = self::$dbConn->fetch_array_all($query);
				if(!empty($rtnData)){
					$ii       = 0;
					foreach($rtnData as $k => $v){
						$addtime      = $v['addtime'];
						$recordnumber = $v['recordnumber'];
						$parid        = $v['partner_id'];
						$purid        = $v['purchaseuser_id'];
						$price        = $v['price'];
						$count        = $v['count'];
						$sku          = $v['sku'];
						$skuinfo      = self::getSkuById($skuid);
						$name         = $skuinfo[0]['goodsName'];
						$parname      = self::getParNameById($parid);
						$purname      = self::getNameById($purid);
						$datalist[$ii]['addtime'] 		= $addtime;
						$datalist[$ii]['recordnumber'] 	= $recordnumber;
						$datalist[$ii]['parname'] 		= $parname;
						$datalist[$ii]['purname'] 		= $purname;
						$datalist[$ii]['sku'] 			= $sku;
						$datalist[$ii]['name'] 			= $name;
						$datalist[$ii]['price'] 		= $price;
						$datalist[$ii]['count'] 		= $count;
						$datalist[$ii]['totalmoney'] 	= $price * $count;
						$ii++;
					}
					$dataArr[$num] = $datalist;
					$num++;
				}
			}
		}
		return $dataArr;
	}

	/**
	 *功能:API下单芬哲ERP成功后修改订单为在途状态
	 *@param $data  订单编号数组
	 *日期:2013/11/22
	 *作者:王民伟
	 */
	function updateDownFinejoOrderStatus($data){
		self::initDB();
		$idlist = explode(',', $data);
		$num    = count($idlist);
		for($ii = 0; $ii < $num; $ii++){
			$id .= "'".$idlist[$ii]."',";
		}
		$id     = substr($id, 0, strlen($id) - 1);
		$update = "UPDATE ".C('DB_PREFIX')."order SET status = '3' WHERE id in ({$id}) AND is_delete = 0";
		$query  = self::$dbConn->query($update);
		if($query){
			$num = self::$dbConn->affected_rows();
			if($num >= 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 *根据供应商编号获取额度、预警额度、是否签约
	 *@param $id  订单编号数组
	 *日期:2013/11/27
	 *作者:王民伟
	 */
	 public static function getParInfo($id){
		self::initDB();
		$sql = "SELECT *  FROM ph_partner WHERE   id={$id} ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if(empty($ret[0])){
				self::$errMsg = "无数据";
				return false;
			}
			return $ret;
		}
		self::$errMsg = "获取数据失败";
		return false;
	 }
}
?>
