<?php
/**
 * WhGoodsAssignModel
 * 仓库调拨
 * @package 仓库系统
 * @author Gary(yym)
 * @copyright 2014
 * @access public
 */
class WhGoodsAssignModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		//mysql_query('SET NAMES UTF8');
	}
    
	/*
     * 根据条件获得所有的调拨单列表 $wheresql sql条件语句
     */
    public static function getAssignList ($where, $order_by, $limit, $group_by, $asort = 'desc')
    {
   	    self :: initDB();
        
        $sql        =   'select a.* from wh_store_goods_assign as a left join wh_store_goods_assign_detail as b 
                            on a.id = b.goodsAssignId and b.is_delete = 0 left join wh_position_distribution as c on b.positionId = c.id
                            left join pc_goods as d on d.sku = b.sku where 1 ';
        //echo $sql;exit;
        if($where){
            $sql    .=  $where;
        }
        if($group_by){
            $sql    .=  " group by $group_by";
        }
        if($order_by){
            $sql    .=  " order by $order_by $asort";
        }
        if($limit){
            $sql    .=  " $limit";
        }
        //echo $sql;exit;
        $sql        =   self::$dbConn->query($sql);
        $list       =   self::$dbConn->fetch_array_all($sql);
        return $list;
    }
	
	/**
     * WhGoodsAssignModel::addAssignList()
     * 插入调拨单列表数据
     * @param mixed $tName
     * @param mixed $sql
     * @return void
     */
    public static function addAssignList($assignNumber, $outStoreId, $inStoreId, $statusTime, $createTime, $createUid){
   	    self :: initDB();
        $sql    =   "INSERT INTO `wh_store_goods_assign` (
                        `assignNumber`, `outStoreId`, `inStoreId`, `statusTime`, `createTime`,`createUid`)
                VALUES ('{$assignNumber}', '{$outStoreId}', '{$inStoreId}', '{$statusTime}', '{$createTime}', '{$createUid}')";
        //echo $sql;exit;
        //$sql    =   addslashes($sql);
        return self::$dbConn->query($sql) ? self::$dbConn->insert_id() : FALSE; 
    }
    
    /**
     * WhGoodsAssignModel::addAssignList()
     * 插入调拨单明细表数据
     * @param mixed $tName
     * @param mixed $sql
     * @return void
     */
    public static function addAssignListDetail($string){
        self :: initDB();
        $sql    =   "INSERT INTO `wh_store_goods_assign_detail` (
                        `goodsAssignId`, `sku`, `num`, `positionId`) VALUES ".$string;
        //echo $sql;exit;
        return self::$dbConn->query($sql) ? self::$dbConn->insert_id() : FALSE; 
    }
    
    /**
     * WhGoodsAssignModel::getMaxId()
     * 获取最大的调拨单编号
     * @return void
     */
    public static function getMaxNumber(){
        self :: initDB();
        $sql    =   "select assignNumber from wh_store_goods_assign order by id desc limit 1";
        $res    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_row($res);
        
        return empty($res) ? 0 : $res['0'];
    }
    
    /**
     * WhGoodsAssignModel::getSkuLocation()
     * 获取sku所在仓库的仓位id
     * @param string $sku
     * @param int $storeId
     * @return void
     */
    public static function getSkuLocation($sku, $storeId, $nums = 0){
        self :: initDB();
        $sql    =   "select a.positionId from wh_product_position_relation a left join pc_goods b on b.is_delete = 0 
                        where b.sku = '$sku' and a.pId = b.id and a.storeId = $storeId and a.is_delete = 0";
        if($nums){
            $sql    .=  " and a.nums>=$nums";
        }
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $location   =   self::$dbConn->fetch_row($sql);
        //print_r($location);exit;
        return empty($location) ? 0 : $location[0];
    }
    
    /**
     * WhGoodsAssignModel::getRowAllNumber()
     * 获取记录总数
     * @return void
     */
    public static function getRowAllNumber($where){
        self :: initDB();
		$sql      = 'select a.id from wh_store_goods_assign as a left join wh_store_goods_assign_detail as b on a.id = b.goodsAssignId
                            and b.is_delete = 0 left join wh_position_distribution as c on b.positionId = c.id left join pc_goods as d
                            on d.sku = b.sku where 1 '.$where.' group by a.id';
        //echo $sql;exit;
		$query    = self::$dbConn->query($sql);
        $res      = self::$dbConn->fetch_array_all($query);
        return empty($res) ? 0 : count($res);
    }
    
    /**
     * WhGoodsAssignModel::getsAssignListDetail()
     * 获取调拨单下的所有SKU和仓位信息
     * @param mixed $goodsAssignId
     * @return void
     */
    public static function getsAssignListDetail($goodsAssignId){
        self :: initDB();
        $sql        = 'select a.*, b.goodsName, b.sku, b.spu, c.pName from wh_store_goods_assign_detail as a left join pc_goods b on a.sku =b.sku
                        and b.is_delete = 0 left join wh_position_distribution c on c.id=a.positionId where a.is_delete = 0 and
                        a.goodsAssignId = '.$goodsAssignId;
        $sql        = self::$dbConn->query($sql);
        $res        = self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    
    /**
     * WhGoodsAssignModel::updateAssignStatus()
     * 更新调拨单状态
     * @return void
     */
    public static function updateAssignStatus($ids, $status, $printUid){
        self :: initDB();
        if( !$ids && !$status && !$printUid){
            return FALSE;
        }
        $time   =   time();
        $sql    =   "update wh_store_goods_assign set status={$status}, statusTime={$time}, 
                        printUid={$printUid}, printTime={$time} where id in ($ids)";
        return self::$dbConn->query($sql);
    }
	
	/**
	 *根据sku、仓库id获取仓位信息
	 */
	public static function getSkuPositions($sku,$storeId) {
		self::initDB();
		$sql     =  "select b.pName from `wh_product_position_relation` as a 
					left join `wh_position_distribution` as b on a.positionId=b.id 
					left join `pc_goods` as c on a.pId=c.id where a.type=1 and a.is_delete=0 and c.sku='{$sku}' and a.storeId='{$storeId}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	
	/**
	 *根据sku获取仓库信息
	 */
	public static function getSkuStores($sku) {
		self::initDB();
		$skuinfo = get_realskuinfo($sku);
		if(empty($skuinfo)){
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
		foreach($skuinfo as $sku=>$num){
			$sql     =  "select a.storeId,b.whName from `wh_sku_location` as a 
					left join `wh_store` as b on a.storeId=b.id 
					where a.sku='$sku'";
			$query	 =	self::$dbConn->query($sql);		
			if($query){
				$ret =self::$dbConn->fetch_array_all($query);
				return $ret;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"error";
				return false;	
			}
		}
	}

    /**
     * WhGoodsAssignModel::getDetail()
     * 查询指定调拨单明细记录
     * @param mixed $assignId
     * @param mixed $where
     * @return
     */
    public static function getDetail($assignId, $where){
		self::initDB();
		$sql	 =	"select a.*, b.pName, b.storeId from wh_store_goods_assign_detail a left join wh_position_distribution b on a.positionId = b.id
                         where a.goodsAssignId=$assignId ".$where;
        //echo $sql;exit();
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
    
	/**
	 * WhGoodsAssignModel::getOrderGroup()
	 * 根据条件获取调拨单信息
	 * @param mixed $select
	 * @param mixed $where
	 * @return
	 */
	public static function getOrderGroup($select, $array){
		self::initDB();
		$sql	 =	"select {$select} from wh_store_goods_assign where ".array2sql($array);
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
    
    //更新调拨单明细
    public static function updateAssignDetail($where, $update){
        self::initDB();
        $where   =  array2sql($where);
        //$update  =  array2sql($update);
        $where   =  str_replace(',', ' and ', $where);
        
        $up      =  '';
        foreach($update as $key=>$val){
            $up  .= "$key = $val,"; 
        }
        trim($up, ',');
        $up      =  implode(',', array_filter(explode(',', $up)));
        
		$sql	 =	"update wh_store_goods_assign_detail set {$up} where $where";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		return $query;
    }
    
    //更新调拨单状态
    public static function updateAssignListStatus($where, $update){
		self::initDB();
        //$time    =  time();
        $where   =  array2sql($where);
        $where   =  str_replace(',', ' and ', $where);
        $update  =  array2sql($update);
		$sql	 =	"update wh_store_goods_assign set $update where $where";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		return $query;
	}
    
    //检测该仓库是否有该料号
    public static function checkSku($sku, $storeId){
        self::initDB();
        $sql    =   "select a.id from wh_product_position_relation a left join pc_goods b on a.pId = b.id where b.sku ='{$sku}' and a.storeId='{$storeId}' and a.is_delete = 0";
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return empty($res) ? FALSE : TRUE;  
    }
    
    //更新wh_sku_location表
    public function updateSkuLocation($where, $update){
        self::initDB();
        $where   =  array2sql($where);
        $where   =  str_replace(',', ' and ', $where);
        //$update  =  array2sql($update);
        $up      =  '';
        foreach($update as $key=>$val){
            $up  .= "$key = $val,"; 
        }
        trim($up, ',');
        $up      =  implode(',', array_filter(explode(',', $up)));
		$sql	 =	"update wh_sku_location set $up where $where";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		return $query;
    }
    
    //wh_product_position_relation
    public function updateProdcutPosition($where, $update){
        self::initDB();
        $where   =  array2sql($where);
        $where   =  str_replace(',', ' and ', $where);
        $up      =  '';
        foreach($update as $key=>$val){
            $up  .= "$key = $val,"; 
        }
        rtrim($up, ',');
        $up      =  implode(',', array_filter(explode(',', $up)));
		$sql	 =	"update wh_product_position_relation set $up where $where";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		return $query;
    }
    
    //获取料号调拨库存
    public function getAssignStock($sku){
        self::initDB();
		$sql	 =	"select assignStock from  wh_sku_location where sku='{$sku}'";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);
        $res     =  self::$dbConn->fetch_array($query);		
		return empty($res) ? 0 : $res['assignStock'];
    }
    
    /**
     * WhGoodsAssignModel::getAssignOrderIds()
     * 获取调拨单下对应的所有订单号
     * @param int $goodsAssignId
     */
    public static function getAssignOrderIds($goodsAssignId){
        self::initDB();
        $goodsAssignId = intval($goodsAssignId);
        if(!$goodsAssignId){
            return FALSE;
        }
		$sql	 =	"select orderId from  wh_assign_order_relation where goodsAssignId='{$goodsAssignId}' and is_delete =0";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);
        $res     =  self::$dbConn->fetch_array_all($query);		
		return empty($res) ? 0 : $res;
    }
    
    /**
     * WhGoodsAssignModel::insertAssignOrder()
     * 插入调拨单订单关系表
     * @param mixed $ids
     * @param mixed $goodsAssignId
     * @return
     */
    public static function insertAssignOrder($ids, $goodsAssignId){
        self::initDB();
        $goodsAssignId = intval($goodsAssignId);
        if(!$goodsAssignId){
            return FALSE;
        }
		$sql	 =	"insert into wh_assign_order_relation (goodsAssignId, orderId) values ($goodsAssignId, '$ids')";
        //echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);	
		return $query;
    }
    
    /**
     * WhGoodsAssignModel::getCombineSku()
     * 获取组合料号下子料号信息
     * @param mixed $sku
     * @return void
     */
    public static function getCombineSku($sku){
        self::initDB();
        $sql    =   "select sku, count from pc_sku_combine_relation where combineSku = '$sku'";
        $res    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($res);
        return $res;
    }
    
    /**
     * WhGoodsAssignModel::getAssignOrderById()
     * 根据订单id获取记录
     * @param mixed $ebay_id
     * @return void
     */
    public static function getAssignOrderById($ebay_id){
        self::initDB();
        $sql    =   "select id from wh_assign_order_relation where orderId like '%$ebay_id%'";
        $res    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($res);
        return $res;
    }
    
    /**
     * WhGoodsAssignModel::insertAssignDetail()
     * 插入仓库调拨详细表数据
     * @param int $goodsAssignId
     * @param strint $sku
     * @param int $sku_num
     * @param int $positionId
     * @param int $outStoreId
     * @return void
     */
    public static function insertAssignDetail($goodsAssignId, $sku, $sku_num, $positionId){
        self::initDB();
        $sql    =   "insert into wh_store_goods_assign_detail (`goodsAssignId`, `sku`, `num`, `positionId`) values ('$goodsAssignId', '$sku', '$sku_num', '$positionId')";
        //echo $sql;exit;
        return self::$dbConn->query($sql);
    }
    
}
?>
