<?php
/*
*点货操作(model)
*add by heminghua
*
*/
class packageCheckModel{
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
	*插入点货数据
	*/
	public static function insertRecord($insertArr){
		self::initDB();
		//$sql	 =	"INSERT INTO wh_tallying_list(batchNum,sku,num,tallyUserId,entryUserId,entryTime,purchaseId,storeId,entryStatus) VALUES('{$batchNum}','{$sku}',{$amount},{$checkUserId},{$userNameId},".time().",{$purchaseId},1,{$entryStatus})";
        $sql    =   "INSERT INTO wh_tallying_list set ".array2sql($insertArr);
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			return true;	
		}else{
			return false;	
		}
	}
	/*
	*查找所有记录
	*/
	public static function selectList($where){
		self::initDB();
		$sql = "SELECT * FROM wh_tallying_list {$where}";
		//echo $sql;//exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	/*
	*查找所有点货员
	*/
	public static function selectUser(){
		self::initDB();
		$sql = "SELECT DISTINCT tallyUserId FROM wh_tallying_list";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	/*
	 * 更新点货表
	 * 
	 */
	public static function updateRecord($id,$num,$entryStatus){
		self::initDB();
		$sql = "UPDATE wh_tallying_list SET num=num+{$num},entryStatus={$entryStatus} where id={$id}";
		
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
	/*
	 *插入点货调整记录 
	 * 
	 */
	 public static function insertAdjustRecord($id,$num,$beforeNum,$userId){
	 	self::initDB();
		$sql = "INSERT INTO wh_tallying_adjustment(tallyListId,defference,beforeNum,userId,operationTime) values({$id},'{$num}',{$beforeNum},{$userId},".time().")";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	 
	}
	public static function selectSku($sku){
		self::initDB();
		$sql = "SELECT * FROM pc_goods where sku='{$sku}' and is_delete=0";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function updateStore($sku,$num, $storeId = 1){
		self::initDB();
		$sql = "UPDATE wh_sku_location SET arrivalInventory=arrivalInventory+{$num} where sku='{$sku}' and storeId = '{$storeId}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
	
	/*
	*查找sku库存
	*/
	public static function selectStore($sku,$storeId=1){
		self::initDB();
		$sql = "SELECT * FROM wh_sku_location where sku='{$sku}' and storeId={$storeId}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
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
		$sql = "INSERT INTO wh_sku_location(sku,arrivalInventory,storeId) values('{$sku}',{$amount},{$storeId})";
		$query	 =	self::$dbConn->query($sql);		
		if($query){	
			return true;	
		}else{
			return false;	
		}
	 
	}
	
	//删掉点货记录
	public static function deletRecord($id_arr,$type=0){
		self::initDB();
		if(!is_array($id_arr) || !is_numeric($type)){
			return false;
		}
		OmAvailableModel :: begin();	
		foreach($id_arr as $id){
			$info = self::selectList("where id={$id}");
			$num  = -$info[0]['num'];
			$sku  = $info[0]['sku'];
			$update_tallying = "UPDATE wh_tallying_list SET is_delete=1 where id={$id}";
			$update_query	 = self::$dbConn->query($update_tallying);		
			if(!$update_query){
				OmAvailableModel::rollback();
				return false;
			}
			$update_store = self::updateStore($sku,$num);
			if(!$update_store){
				OmAvailableModel::rollback();
				return false;
			}
			if($type==1){
				$update_group = "UPDATE wh_print_group SET is_delete=1 where tallyListId={$id}";
				$query_group  = self::$dbConn->query($update_group);		
				if(!$query_group){
					OmAvailableModel::rollback();
					return false;
				}
			}
			CommonModel::checkOnWaySkuNum($sku,$info[0]['num'],2);           //去掉采购系统hold住数量
		}
		OmAvailableModel::commit();
		return true;
	}
		
	//更新点货信息列表录入状态
	public static function updateEntryStatus($id_arr,$userId){
		self::initDB();
		if(!is_array($id_arr)){
			return false;
		}
		$ids   = implode(',',$id_arr);
		$sql   = "update wh_tallying_list set entryStatus=2,confirmUserId={$userId} where id in({$ids})";
		$query = self::$dbConn->query($sql);
		if($query){
			return true;	
		}else{
			return false;	
		}	
	}
	
	//删掉异常录入
	public static function updateOdd($id_arr){
		self::initDB();
		if(!is_array($id_arr)){
			return false;
		}
		$ids   = implode(',',$id_arr);
		$sql   = "update wh_tallying_list set is_delete=1 where id in({$ids})";
		$query = self::$dbConn->query($sql);
		if($query){
			return true;	
		}else{
			return false;	
		}	
	}
	
	//获取打标分组信息
	public static function getSKUByGroupId($id){
		self::initDB();		
		$sql   = "select a.id,a.batchNum,a.sku,a.num,a.ichibanNums,a.shelvesNums from wh_tallying_list as a left join wh_print_group as b on a.id=b.tallyListId where b.id='{$id}'";
		$query = self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}	
	}
	
	//更新良品数量
	public static function updateIchibanNums($num,$id){
		self::initDB();		
		$sql   = "update wh_tallying_list set ichibanNums=ichibanNums+{$num} where id={$id}";
		$query = self::$dbConn->query($sql);			
		if($query){
			return true;	
		}else{
			return false;	
		}		
	}
	
	//更新上架数
	public static function updateShelvesNums($num,$sku,$id){
		self::initDB();	
		$adjnum = abs($num);
		$sql    = "update wh_tallying_list set shelvesNums=shelvesNums+{$num} where id={$id}";
		$query  = self::$dbConn->query($sql);			
		if($query){
			$location_sql   = "update wh_sku_location set actualStock=actualStock+{$num} where sku='{$sku}' and storeId=1";
			$location_query = self::$dbConn->query($location_sql);	
			if($location_query){
				$sku_info = "select id from pc_goods where sku='$sku'";
				$sku_info = self::$dbConn->query($sku_info);
				$sku_info = self::$dbConn->fetch_array_all($sku_info);
				
				$select_position = "select id,positionId from wh_product_position_relation where pId={$sku_info[0]['id']} and nums>{$adjnum}";
				$select_position = self::$dbConn->query($select_position);
				$select_position = self::$dbConn->fetch_array_all($select_position);
				
				$update_position = "update wh_product_position_relation set nums=nums+{$num} where id='{$select_position[0]['id']}'";
				$update_position = self::$dbConn->query($update_position);
				if($update_position){
					$where   = " where sku = '{$sku}'";
					$skuinfo = whShelfModel::selectSku($where);
					/**** 插入出入库记录 *****/
					$paraArr = array(
						'sku'     	 => $sku,
						'amount'  	 => $adjnum,
						'positionId' => $select_position[0]['positionId'],
						'purchaseId' => $skuinfo['purchaseId'],
						'ioType'	 => 1,
						'ioTypeId'   => 31,
						'userId'	 => $_SESSION['userId'],
						'reason'	 => '点货调整出库',
					);
					$record = CommonModel::addIoRecores($paraArr);     //出库记录
					if($record){
						return true;
					}else{
						return false;
					}
				}
			}
		}else{
			return false;	
		}		
	}
	
	//更新采购系统推送回来重点,仓库系统异常料号更新状态 add by wangminwei 2014-04-03
	public static function updUnusualSkuStatus($orderArr){
		self::initDB();
		$upd	 	= "UPDATE wh_tallying_list SET entryStatus = 3 WHERE id IN ($orderArr)";
		$rtnUpd 	= self::$dbConn->query($upd);
		return $rtnUpd;
	}
    
    /**
     * packageCheckModel::getSkuWaitShelfNum()
     * 获取sku等待上架数量
     * @param mixed $sku
     * @return void
     */
    public static function getSkuWaitShelfNum($sku){
        self::initDB();
        $sql    =   "select sku, sum(num) nums, sum(ichibanNums) ichibanNums, sum(shelvesNums) shelvesNums from wh_tallying_list 
                        where sku='{$sku}' and tallyStatus=0 and entryStatus=0 and is_delete = 0  group by sku order by id desc";
        //echo $sql;exit;
        $res    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($res);
        if(!empty($res)){
            $waitNums1  =   $res['nums'] - intval($res['shelvesNums']); //点货数减去上架数
            $waitNums2  =   $res['ichibanNums'] - $res['shelvesNums']; //良品数减去上架数
            if($waitNums2 > 0){
                $waitNums   =   $res['nums'] > $waitNums2 ? $waitNums2 : $waitNums1;  //判断采用哪个待上架数量（主要防止以前点货数小于良品数的错误信息）
            }else{
                $waitNums   = $waitNums1;
            }
            
        }else{
            $waitNums   =   0;
        }
        return $waitNums;
    }
    
    /**
     * packageCheckModel::update_note()
     * 更新点货备注
     * @param mixed $id
     * @param mixed $note
     * @return void
     */
    public static function update_note($id, $note){
        self::initDB();
        $sql    =   "update wh_tallying_list set note='$note' where id='$id'";
        return self::$dbConn->query($sql);
    }
    
    /**
     * packageCheckModel::getTotalNums()
     * 获取点货总记录数
     * @param mixed $where
     * @return void
     */
    public static function getTotalNums($where){
        self::initDB();
        $sql    =   'select count(*) as total from wh_tallying_list '.$where;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($sql);
        return $res['total'];
    }
	
}
?>