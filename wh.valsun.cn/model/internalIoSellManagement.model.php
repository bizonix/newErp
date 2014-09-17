<?php
/*
 * 仓库内部销售出入库管理 internalIoSellManagement.model.php
 * ADD BY chenwei 2013.8.23
 */
class InternalIoSellManagementModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table1			=	"wh_invoice_type"; //单据类型列表-基础表
	static  $table2			=	"wh_payment_methods"; //付款方式类型列表-基础表
	static  $table3			=	"wh_sku_location";//SKU 仓位信息列表"
	static  $table4			=	"wh_iostore";//出入库单据表
	static  $table5			=	"wh_iostoredetail";//出入库单据明细表
	static  $table6			=	"pc_goods";//SKU信息表
	static  $table7			=	"wh_product_position_relation";//产品与SKU仓位信息关联列表
	static  $table8			=	"wh_position_distribution";//仓库位置分布管理表
		
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
     * 分页总数
     */
	public 	static function getPageNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table4." {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"4444";
			self::$errMsg  =	"mysql:".$sql." error";
			return false;	
		}
	}
		
	/*
     * 内部使用组的 单据类型 数据显示
     */
	public 	static function invoiceTypeList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table1." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 单据类型付款方式联动 
     */
	public 	static function changeCategoriesSkip(){
		self::initDB();
		$sql	 =	"select * from ".self::$table2;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 库存是否足够验证 
     */
	public 	static function skuInventoryVerdict($where){
		self::initDB();
		//SKU转换ID
		$sql	 =	"select id from ".self::$table6." {$where}";
		$query	 =	self::$dbConn->query($sql);	
		$ret     =  self::$dbConn->fetch_array_all($query);
		//SKU ID求出多仓选择
		$sql2    = "select positionId,nums from ".self::$table7." where pId = {$ret[0]['id']} and type = 1 and is_delete != 1 order by nums desc limit 1";	
		$query2	 =	self::$dbConn->query($sql2);
		$ret2    =  self::$dbConn->fetch_array_all($query2);
		if(!empty($ret2)){
			//仓库ID转换仓库名称
			$sql3	 = "select pName from ".self::$table8." where is_enable = 1 and type = 1 and id = {$ret2[0]['positionId']}"; 
			$query3	 =	self::$dbConn->query($sql3);
		    $ret3    =  self::$dbConn->fetch_array_all($query3);
			$ret2[0]['positionName']	 = $ret3[0]['pName'];
			return $ret2;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." null";
			return false;
		}							
	}
	
	/*
     * wh_iostore 出入库单
     */
	public 	static function iostoreList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table4." {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * wh_iostoredetail  出入库单据明细
     */
	public 	static function iostoredetailList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table5." {$where} and is_delete = 0";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 审核通过 扣除/增加库存
     */
	public 	static function internalIoSellApproved($where){
		self::initDB();
		self::$dbConn->begin();
		$ioInventory = array();
		$IoRecordArr = array();
		$sql	 =	"UPDATE ".self::$table4." SET ioStatus = 2,endTime = ".time().",operatorId = ".$_SESSION['userId']." {$where}";		
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			//单据信息
			$sql1	 =	"select * from ".self::$table4." {$where}";	
			$ret1 	 =  self::$dbConn->fetch_first($sql1);
			if(empty($ret1)){
				self::$dbConn->rollback();
				self :: $errCode = "2222";
				self :: $errMsg = "未找到单据信息";
				return false;
			}
			
			//单据明细	
			$sql2	 =	"select * from ".self::$table5." WHERE iostoreId =".$ret1['id'];
			$query2	 =	self::$dbConn->query($sql2);
			$ret2 	 =  self::$dbConn->fetch_array_all($query2);
			if(empty($ret2)){
				self::$dbConn->rollback();
				self :: $errCode = "3333";
				self :: $errMsg = "未找到单据SKU明细";
				return false;
			}
			//出入库记录、扣库存
			$WhIoRecordsAct = new WhIoRecordsAct();		
			$is_ok = true;	
			
			foreach($ret2 as $ioArr){
				//加减库存
				$ioInventory['sku'] 		= $ioArr['sku'];//sku
				//sku转换成 ID
				$skuIdSql	 =	"select id from ".self::$table6." where sku = '".$ioArr['sku']."' and is_delete != 1 limit 1";
				$skuIdSql	 =	self::$dbConn->query($skuIdSql);		
				$skuIdSql    =  self::$dbConn->fetch_array_all($skuIdSql);
				$ioInventory['pId'] 		= $skuIdSql[0]['id'];//skuID
				$ioInventory['positionId'] 	= $ioArr['positionId'];//仓位ID
				$ioInventory['amount'] 		= $ioArr['amount'];//数量
				$ioInventory['ioType'] 		= $ret1['ioType'];//出入库
				$ioInventory['storeId']	    = 1;//仓库，默认为1
				
				$ioInventoryList = $WhIoRecordsAct->act_updateActualStock($ioInventory);
				//echo $ioInventoryList; exit;
				if($ioInventoryList == 1){
					//出入库记录
					$IoRecordArr['ordersn'] = $ret1['ordersn'];//单据号
					$IoRecordArr['sku'] 	= $ioArr['sku'];//sku
					$IoRecordArr['amount'] 	= $ioArr['amount'];//数量
					$IoRecordArr['purchaseId'] 	= $ioArr['purchaseId'];//采购ID
					$IoRecordArr['ioType']  = $ret1['ioType'];//出入库
					if($ret1['invoiceTypeId'] == 1){//私人购买申请单
					$IoRecordArr['ioTypeId'] = 7;//出入库类型id
						$IoRecordArr['reason'] = "私人购买申请单".$ret1['note'];//出库原因
					}else if($ret1['invoiceTypeId'] == 2){//私人购买退货单
						$IoRecordArr['ioTypeId'] = 15;
						$IoRecordArr['reason'] = "私人购买退货单".$ret1['note'];//出库原因
					}else if($ret1['invoiceTypeId'] == 3){//部门耗材申请单
						$IoRecordArr['ioTypeId'] = 8;
						$IoRecordArr['reason'] = "部门耗材申请单".$ret1['note'];//出库原因
					}else if($ret1['invoiceTypeId'] == 4){//部门借用申请单
						$IoRecordArr['ioTypeId'] = 18;
						$IoRecordArr['reason'] = "部门借用申请单".$ret1['note'];//出库原因
					}else if($ret1['invoiceTypeId'] == 5){//部门归还申请单
						$IoRecordArr['ioTypeId'] = 19;
						$IoRecordArr['reason'] = "部门归还申请单".$ret1['note'];//出库原因
					}else{
						$IoRecordArr['ioTypeId'] = 0;
						$IoRecordArr['reason'] = "";//出库原因
					}
					$IoRecordArr['userId'] = $ret1['operatorId'];//审核人ID
					$IoRecordArr['storeId'] = 1;//仓库，默认为1
					$IoRecordList = $WhIoRecordsAct->act_addOneIoRecoresForWh($IoRecordArr);
					if($IoRecordList != 1){		
						self::$dbConn->rollback();
						$is_ok = false;							
						return false;
					}					
				}else{
					self::$dbConn->rollback();
					return false;
				}
				
				if($is_ok){
					self::$dbConn->commit();
					return true;
				}else{
					return false;
				}		
				
			}
						
		}else{
			self::$dbConn->rollback();
			self :: $errCode = "1111";
			self :: $errMsg = "单据更新失败!";
			return false;
		}

	}
	
	/*
     * 拒绝
     */
	public 	static function internalIoSellAbandon($where){
		self::initDB();
		$sql	 =	"UPDATE ".self::$table4." SET ioStatus = 3,endTime = ".time().",operatorId = ".$_SESSION['userId']." {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			return true;	
		}else{
			self::$errCode =	"4444";
			self::$errMsg  =	"mysql:".$sql." error";
			return false;	
		}
		
	}
	
	/*
	 * 获取SKU详细信息：参数 $sku
	 */
	public static function getSkuInfo($sku){
		self::initDB();
		$skuInfoArr = array();
		$goodsSql	    =	"select goodsCost from ".self::$table6." where sku = '{$sku}' and is_delete = 0";
		$goodsQuery	    =	self::$dbConn->query($goodsSql);	
		$goodsRet       =   self::$dbConn->fetch_array_all($goodsQuery);
		if(!empty($goodsRet)){
			
		}else{
			self :: $errCode = "4444";
			self :: $errMsg  = "mysql:".$goodsSql." null";
			return false;
		}
		
		
		
		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}		
	}
	
	/*
     *  验证SKU是否存在
     */
	public 	static function skuVerify($where){
		self::initDB();
		$sql	 =	"select goodsCost,purchaseId from ".self::$table6." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if(!empty($ret)){
				//获取采购人名称
				$usermodel     = UserModel::getInstance();
				$whereStr	   = "where a.global_user_id=".$ret[0]['purchaseId'];         
				$cgUser	       = $usermodel->getGlobalUserLists('global_user_name',$whereStr,'','');//$cgUser[0]['global_user_name'];	
				$ret[0]['purchaseName'] = $cgUser[0]['global_user_name'];
				return $ret;
			}else{
				self :: $errCode = "4444";
				self :: $errMsg = "mysql:".$sql." null";
				return false;
			}	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 仓位ID转换
     */
	public 	static function positionIdToName($whereStr){
		self::initDB();
		$sql	 = "select pName from ".self::$table8." {$whereStr}"; 
		$query	 =	self::$dbConn->query($sql);
		if($query){		
		    $ret    =  self::$dbConn->fetch_array_all($query);
			return $ret[0]['pName'];
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." null";
			return false;
		}							
	}
	
}
?>
