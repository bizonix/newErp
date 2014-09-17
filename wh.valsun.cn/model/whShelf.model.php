<?php 
/*
 * 上架操作
 */
class whShelfModel{
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
	 * 查找料号信息
	 */
	public static function selectSku($where){
		self::initDB();
		$sql	 =	"SELECT * FROM pc_goods {$where}";
		
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res[0];	
		}else{
			return false;	
		}
	
	}
	public static function selectSkuNums($sku,$storeId=1){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_sku_location WHERE sku='{$sku}' and storeId = {$storeId}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		}else{
			return false;	
		}
	
	}
	
	public static function selectList($where){
		self::initDB();
		$sql = "SELECT * FROM wh_tallying_list {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	public static function selectPosition($where){
		self::initDB();
		$sql = "SELECT * FROM wh_position_distribution {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	
	public static function selectRelation($where){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_product_position_relation {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		}else{
			return false;	
		}
	
	}
	
	/*
	*更新总库存
	*/
	public static function updateStoreNum($num,$where){
		self::initDB();
		$sql	 =	"UPDATE wh_sku_location SET actualStock=actualStock+{$num},arrivalInventory=arrivalInventory-{$num} {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self::$errCode = 334;
			self::$errMsg = "更新库存失败！";
			return false;	
		}
	
	}
	
	/*
	*更新总库存
	*/
	public static function updateStoreNumOnly($num,$where){
		self::initDB();
		$sql	 =	"UPDATE wh_sku_location SET actualStock=actualStock+{$num} {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self::$errCode = 334;
			self::$errMsg = "更新库存失败！";
			return false;	
		}
	
	}
	
	/*
	*更新指定库存
	*/
	public static function updateProductPositionRelation($num,$where){
		self::initDB();
		$sql	 =	"UPDATE wh_product_position_relation SET nums=nums+{$num} {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self::$errCode = 334;
			self::$errMsg = "更新库存失败！";
			return false;	
		}
	
	}
	
	/*
	*插入未订单记录
	*/
	public static function insertNoOrder($sku,$nums,$totalNums,$purchaseId,$userId,$note){
		self::initDB();
		$sql	 =	"INSERT INTO wh_abnormal_purchase_orders(sku,nums,totalNums,purchaseId,creatorId,createdTime,note) VALUES ('{$sku}',{$nums},{$totalNums},{$purchaseId},{$userId},".time().",'{$note}')";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self::$errCode = 335;
			self::$errMsg = "插入未订单记录失败！";
			return false;	
		}
	}
	
	//更新上架状态shelvesNums=shelvesNums+
	public static function updateTallyStatus($id,$num){
		self::initDB();
		$time = time();
		$sql  =	"UPDATE wh_tallying_list SET tallyStatus=1,finishTime={$time},shelvesNums=shelvesNums+{$num} WHERE id={$id}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}
	
	}
	
	//更新待定状态
	public static function updateEntryStatus($batchNum, $status = 3){
		self::initDB();
		$time = time();
		$sql  =	"UPDATE wh_tallying_list SET entryStatus={$status} WHERE batchNum=$batchNum AND is_delete = 0 ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}
	
	}
	
	public static function updateShelfNum($id,$num){
		self::initDB();
		$sql	 =	"UPDATE wh_tallying_list SET shelvesNums=shelvesNums+{$num} WHERE id={$id}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}
	
	}
	
	/*
	 *插入仓位信息列表
	 * 
	 */
	 public static function insertStore($sku,$amount,$storeId=1){
	 	self::initDB();
		$sql = "INSERT INTO wh_sku_location(sku,actualStock,storeId) values('{$sku}',{$amount},{$storeId})";
		$query	 =	self::$dbConn->query($sql);		
		if($query){	
			return true;	
		}else{
			return false;	
		}
	 
	}
    
    //将上架失败的sku及数量存入临时表中。
    public static function insertFailSku($sku, $num, $uid, $status, $key){
        self::initDB();
        $sql    =   "select id from wh_whself_temp where rand_key ='{$key}'";
        //$sql    =   addslashes($sql); //转义sql语句
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($sql);
        $time   =   time();
        if(empty($res)){
            $sql=   "insert into wh_whself_temp (sku, num, uid, status, rand_key, time) values ('{$sku}', '{$num}', '{$uid}', '{$stauts}', '{$key}', $time)";
            return self::$dbConn->query($sql);
        }
        return FALSE;
    }
    
    /**
     * whShelfModel::getWhselfTempRecord()
     * 获取上架临时表中数据
     * @param mixed $sku
     * @param mixed $num
     * @param mixed $uid
     * @return void
     */
    public static function getWhselfTempRecord($sku, $num){
        self::initDB();
        $sql    =   "select `rand_key`,`num` from wh_whself_temp where sku='{$sku}' and num='{$num}' and status=0";
        //$sql    =   addslashes($sql); //转义sql语句
        //echo    $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($sql);
        return $res;
    }
    
     /**
     * whShelfModel::updateFailSku()
     * 更新临时表中数据状态
     */
    public static function updateFailSku($key, $update){
        self::initDB();
        $sql    =   "update wh_whself_temp set ".array2sql($update)." where rand_key = '{$key}'";
        //$sql    =   addslashes($sql); //转义sql语句
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        return $sql;
    }
    
    /**
     * whShelfModel::insertRelation()
     * 插入料号仓位关系表数据 
     * @param int $pId
     * @param int $positionId
     * @param int $nums
     * @param int $storeId
     * @return void
     */
    public static function insertRelation($pId, $positionId, $nums, $storeId = 1, $type = 1){
        $pId    =   intval($pId);
        $positionId =   intval($positionId);
        $nums       =   intval($nums);
        $storeId    =   intval($storeId);
        self::initDB();
        $res    =   self::selectRelationShip($pId, $positionId); //有记录则不再插入数据
        if(!empty($res)){
            return $res[0]['id'];
        }else{
            $sql    =   "insert into wh_product_position_relation (pId, positionId, nums, storeId, type)
                            values ('{$pId}', '{$positionId}', '{$nums}', '{$storeId}', '{$type}')";
            self::$dbConn->query($sql);
            return self::$dbConn->insert_id();
        }
    }
    
    /**
     * whShelfModel::selectRelationShip()
     * 检索料号仓位关系表 
     * @param int $pid
     * @param int $positionId
     * @param int $storeId
     * @param int $id  主键自增id
     * @return void
     */
    public static function selectRelationShip($pId, $positionId, $storeId=1, $id=''){
        self::initDB();
        $pId    =   intval($pId);
        $positionId =   intval($positionId);
        $storeId    =   intval($storeId);
        $id         =   intval($id);
        $where      =   'where is_delete = 0';
        if($id){
            $where  .=  " and id = '{$id}'";
        }
        if($pId){
            $where  .=  " and pId = '{$pId}'";
        }
        if($positionId){
            $where  .=  " and positionId = '{$positionId}'";
        }
        if($storeId){
            $where  .=  " and storeId = '{$storeId}'";
        }
        $sql        =   "select * from wh_product_position_relation ".$where;
        //echo $sql;exit;
        $sql        =   self::$dbConn->query($sql);
        $res        =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    
    /**
     * 清空料号仓位
    **/
    public static function clearSkuLocation($skuId){
        self::initDB();
        $skuId  =   intval(trim($skuId));
        $sql    =   "update wh_product_position_relation set is_delete = 1 where pId = '$skuId'";
        return self::$dbConn->query($sql);
    }
    
    /**
     * whShelfModel::selectPositionInfo()
     * 搜索仓位id
     * @return void
     */
    public static function selectPositionInfo($select, $where){
        self::initDB();
        $select =   array2select($select);
        $where  =   array2where($where);
        $sql    =   'select '.$select.' from wh_position_distribution where '.$where;
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($sql);
        return $res;
    }
    
    /**
     * whShelfModel::updateProductPositionRelation()
     * 更新产品仓位表(新)
     * @param array $update
     * @param array $where
     * @return void
     */
    public static function updateProductPositionRelation_new($update, $where){
        self::initDB();
        $sql    =   'update wh_product_position_relation set '.array2sql($update).' where '.array2where($where);
        //echo $sql;exit;
        return self::$dbConn->query($sql);
    }
    
    /**
     * whShelfModel::selectRelationShipBySpu()
     * 通过spu检测料号仓位关系表记录 
     * @param string $spu
     * @return array
     */
    public static function selectRelationShipBySpu($spu){
        self::initDB();
        $sql    =   'select a.id from wh_product_position_relation a left join pc_goods b on a.pId = b.id
                        where b.sku = "'.$spu.'" and b.is_delete = 0';
        $sql    =   self::$dbConn->query($sql);
        return self::$dbConn->fetch_array_all($sql);
    }
    
    /**
     * whShelfModel::selectSkuInfo()
     * 查找料号信息 
     * @param array or string $select
     * @param array $where
     * @return void
     */
    public static function selectSkuInfo($select, $where){
        self::initDB();
        $select =   array2select($select);
        $where  =   array2where($where);
        $sql    =   'select '.$select.' from pc_goods where is_delete = 0 and '.$where;
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    
    /**
     * whShelfModel::selectTallyingList()
     * 获取点货信息
     * @param array or string $seelct
     * @param array $where
     */
    public static function selectTallyingList($select, $where){
        self::initDB();
        $select =   array2select($select);
        $where  =   array2where($where);
        $where  .=  $where ? ' and is_delete = 0' : ' is_delete = 0';
        $sql    =   "select {$select} from wh_tallying_list where ".$where;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
	
}
?>