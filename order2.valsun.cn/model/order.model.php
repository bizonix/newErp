<?php
/*
 * 名称：OrderModel
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * @modify by lzx ,date 20140528
 */
class OrderModel extends CommonModel{

	protected $tablekey = '';

	public function __construct(){
		parent::__construct();
	}

	/**
	 * 获取订单列表，包括完结数据和历史数据
	 * 只支持主表订单表查询
	 * 待开发 还有多表管理查询
	 * @param array $conditions
	 * @param string $sort
	 * @param int $page
	 * @param int $perpage
	 * @return array 订单列表
	 * @author lzx
	 */
	public function getOrderList($conditions, $page=1, $perpage=50, $sort='ORDER BY id DESC'){
		$sql = $this->getOrderSQL($conditions);
		preg_match("/^SELECT\s*`([a-z]*)`\.\*\s*FROM/", $sql, $match);
		$sort = preg_replace("/(ORDER\s*BY)\s*([a-z,0-9]*)\s*([ADESC]{3,4})/i", "\$1 `{$match[1]}`.\$2 \$3", $sort);
		$orderlists = $this->sql($sql)->sort($sort)->page($page)->perpage($perpage)->select();
		if (empty($orderlists)){
			return array();
		}


		############################## start 获取订单详情和扩展信息  ##############################
		$_orderlists = $orderids = $suffixids = array();
		foreach ($orderlists AS $orderlist){
			array_push($orderids, $orderlist['id']);
			$suffixids[$orderlist['platformId']][] = $orderlist['id'];
			$_orderlists[$orderlist['id']] = $orderlist;
			$_orderlists[$orderlist['id']]['accountname'] = '';
		}
		$orderextens 		= $this->getOrderExtensionList($suffixids);
		$orderuserinfo 		= $this->getOrderUserInfoList($orderids);
		$orderwarehouse 	= $this->getOrderWarehouseList($orderids);
		$orderdetail		= $this->getOrderDetailList($suffixids);
		$ordernote			= $this->getOrderNoteList($orderids);
		$ordertracknumber  	= $this->getOrderTracknumberList($orderids);
		$orderalllists = array();
		foreach ($orderids AS $id){
			$orderalllists[$id]['order']			= $_orderlists[$id];
			$orderalllists[$id]['orderExtension']	= $orderextens[$id];
			$orderalllists[$id]['orderUserInfo'] 	= $orderuserinfo[$id];
			$orderalllists[$id]['orderNote'] 		= $ordernote[$id];
			$orderalllists[$id]['orderWarehouse'] 	= $orderwarehouse[$id];
			$orderalllists[$id]['orderTracknumber']	= $ordertracknumber[$id];
			$orderalllists[$id]['orderDetail'] 		= $orderdetail[$id];
		}
		unset($orderlists, $_orderlists, $orderextens, $orderuserinfo, $orderdetail, $ordernote, $orderwarehouse, $ordertracknumber);
		############################## end   获取订单详情和扩展信息   ##############################
		return $orderalllists;
	}

	/**
	 * 获取订单数量，包括完结数据和历史数据（如果条件修改需要和上面的函数一起考虑）
	 * 只支持主表订单表查询
	 * 待开发 还有多表管理查询
	 * @param array $conditions
	 * @param string $sort
	 * @param int $page
	 * @param int $perpage
	 * @return array 订单列表
	 * @author lzx
	 */
	public function getOrderCount($conditions){
		return $this->sql($this->replaceSql2Count($this->getOrderSQL($conditions)))->count();
	}
	/**
	 * 获取某一个sku的占用库存(统计的订单状态是发货中，待发货的)
	 * @param unknown_type $conditions
	 */
	public function getSkuHoldingStockNumbers($sku){
		$ORDER_STATUS = C('ORDER_STATUS');
		$StatusMenu = M('StatusMenu');
		$ORDER_WAIT_SHIP = $StatusMenu->getOrderStatusByStatusCode('ORDER_WAIT_SHIP','id');
		$ORDER_SHIPPING = $StatusMenu->getOrderStatusByStatusCode('ORDER_SHIPPING','id');
		
		$sql = "select sum(od.amount) as cnt from om_unshipped_order_detail od 
		left join om_unshipped_order uo on od.omOrderId=uo.id 
		where uo.orderStatus in (".$ORDER_WAIT_SHIP.",".$ORDER_SHIPPING.") and od.sku='".$sku."'";
		$arr = $this->sql($sql)->select();
		return intval($arr[0]['cnt']);
	}
	/**
	 * 根据订单id数组获取对应的扩展信息
	 * @param array $ids
	 * @return array 扩展信息数组
	 * @author lzx
	 */
	public function getOrderExtensionList($suffixids){
		$orderexts = array();
		foreach ($suffixids AS $pid=>$ids){
			$suffix = M('Platform')->getSuffixByPlatform($pid);
			$extlist = $this->sql("SELECT * FROM ".C('DB_PREFIX')."{$this->tablekey}_order_extension_{$suffix} WHERE omOrderId IN (".implode(',', $ids).")")->limit(count($ids))->key('omOrderId')->select();
			$orderexts = $orderexts+$extlist;
		}
		return $orderexts;
	}

	/**
	 * 根据订单id数组获取对应的用户信息
	 * @param array $ids
	 * @return array 用户信息数组
	 * @author lzx
	 */
	public function getOrderUserInfoList($ids){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."{$this->tablekey}_order_userInfo WHERE omOrderId IN (".implode(',', $ids).")")->limit(count($ids))->key('omOrderId')->select();
	}

	/**
	 * 根据订单id数组获取对应的备注信息
	 * @param array $ids
	 * @return array 备注信息数组
	 * @author lzx
	 */
	public function getOrderNoteList($ids){
		$ordernotes = array();
		foreach ($ids AS $id){
			$ordernotes[$id] = $this->sql("SELECT * FROM ".C('DB_PREFIX')."order_notes WHERE omOrderId={$id}")->limit('*')->select();
		}
		return $ordernotes;
	}

	/**
	 * 根据订单id数组获取对应的跟踪号信息
	 * @param array $ids
	 * @return array 跟踪号信息数组
	 * @author lzx
	 */
	public function getOrderTracknumberList($ids){
		$ordertracknumbers = array();
		foreach ($ids AS $id){
			$ordertracknumbers[$id] = $this->sql("SELECT * FROM ".C('DB_PREFIX')."order_tracknumber WHERE omOrderId={$id}")->limit('*')->select();
		}
		return $ordertracknumbers;
	}

	/**
	 * 根据订单id数组获取对应的仓库信息
	 * @param array $ids
	 * @return array 仓库信息数组
	 * @author lzx
	 */
	public function getOrderWarehouseList($ids){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."{$this->tablekey}_order_warehouse WHERE omOrderId IN (".implode(',', $ids).")")->limit(count($ids))->key('omOrderId')->select();
	}
    
    /**
	 * 根据称重时间戳获取对应的未发货订单id记录
	 * @param int start 称重开始时间戳
     * @param int end   称重结束时间戳
	 * @return array 对应unshippedOrderId数组
	 * @author lzx
	 */
	public function getOrderWarehouseOmorderIdsByWeighTime($table='unshipped', $start=0, $end=0){
	    $this->tablekey = $table;
		return $this->sql("SELECT omOrderId FROM ".C('DB_PREFIX')."{$this->tablekey}_order_warehouse WHERE weighTime>='$start' AND weighTime<='$end'")->limit('*')->select();
	}
    
    /**
	 * 根据id数组字符串，状态数组字符串，账号id数组字符串获取对应的未发货订单id记录
	 * @param string $idsStr id数组字符串
     * @param string $orderStatusStr 订单状态数组字符串
     * @param string $accountIdStr 账号Id数组字符串
	 * @return array 对应unshippedOrderId数组
	 * @author lzx
	 */
	public function getOrderIdsByISA($table='unshipped', $idsStr='0', $orderStatusStr='0', $accountIdStr='0'){
	    $this->tablekey = $table;
		return $this->sql("SELECT id FROM ".C('DB_PREFIX')."{$this->tablekey}_order WHERE is_delete=0 AND id IN($idsStr) AND orderStatus IN($orderStatusStr) AND accountId IN($accountIdStr)")->limit('*')->select();
	}

	/**
	 * 根据订单id数组获取对应的订单详情信息
	 * @param array $ids
	 * @return array 仓库信息数组
	 * @author lzx
	 */
	public function getOrderDetailList($suffixids){
		$orderdetails = array();
		foreach ($suffixids AS $pid=>$ids){
			$suffix = M('Platform')->getSuffixByPlatform($pid);
			foreach ($ids AS $id){
				$orderdetail = array();
				$detaillist = $this->sql("SELECT * FROM ".C('DB_PREFIX')."{$this->tablekey}_order_detail WHERE omOrderId={$id} AND is_delete = 0")->limit('*')->key('id')->select();
				if(!empty($detaillist)){
					$dids = array_keys($detaillist);
					//获取扩展信息
					$detailextlist = $this->sql("SELECT * FROM ".C('DB_PREFIX')."{$this->tablekey}_order_detail_extension_{$suffix} WHERE omOrderdetailId IN (".implode(',', $dids).")")->limit(count($dids))->key('omOrderdetailId')->select();
					foreach ($dids AS $did){
						$orderdetail[$did]['orderDetail'] 	 		= $detaillist[$did];
						$orderdetail[$did]['orderDetailExtension']  = $detailextlist[$did];
					}
				}else {
					$orderdetail[$did]['orderDetail'] 	 		= array();
					$orderdetail[$did]['orderDetailExtension']  = array();
				}
				$orderdetails[$id] = $orderdetail;
			}
			unset($orderdetail, $detaillist, $detailextlist);
		}
		return $orderdetails;
	}

	/**
	 * 根据订单id数组获取对应的订单状态
	 * @param array $ids
	 * @return array
	 * @author lzx
	 */
	public function getOrderStatusById($ids){
		$ids = array_map('intval', $ids);
		return $this->sql("SELECT orderType FROM ".C('DB_PREFIX')."unshipped_order WHERE id IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
	}

	/**
	 * 根据订单id数组获取对应的订单
	 * @param array $ids
	 * @return array
	 * @author lzx
	 */
	public function getUnshippedOrderById($ids){
		$ids = array_map('intval', $ids);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."unshipped_order WHERE id IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
	}

	/**
	 * 根据订单id数组获取对应的订单明细
	 * @param array $ids
	 * @return array
	 * @author lzx
	 */
	public function getUnshippedOrderDetailById($ids){
		$ids = array_map('intval', $ids);
		$detailList = $this->sql("SELECT * FROM ".C('DB_PREFIX')."unshipped_order_detail WHERE omOrderId IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
		$orderDetails = array();
		foreach($detailList as $detail){
			$orderDetails[$detail['omOrderId']][] = $detail;
		}
		return $orderDetails;
	}

	/**
	 * 根据订单id数组获取对应的完整订单信息
	 * @param array $ids
	 * @return array
	 * @author lzx
	 */
	public function getFullUnshippedOrderById($ids){
		$ids = array_map('intval', $ids);
		$this->tablekey = 'unshipped';
		$orderlists = $this->sql("SELECT * FROM ".C('DB_PREFIX')."unshipped_order WHERE id IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
		if (empty($orderlists)){
			return array();
		}
		############################## start 获取订单详情和扩展信息  ##############################
		$_orderlists = $orderids = $suffixids = array();
		foreach ($orderlists AS $orderlist){
			array_push($orderids, $orderlist['id']);
			$suffixids[$orderlist['platformId']][] = $orderlist['id'];
			$_orderlists[$orderlist['id']] = $orderlist;
		}
		$orderextens 		= $this->getOrderExtensionList($suffixids);
		$orderuserinfo 		= $this->getOrderUserInfoList($orderids);
		$orderwarehouse 	= $this->getOrderWarehouseList($orderids);
		$orderdetail		= $this->getOrderDetailList($suffixids);
		$ordernote			= $this->getOrderNoteList($orderids);
		$ordertracknumber  	= $this->getOrderTracknumberList($orderids);
		$orderfulllists = array();
		foreach ($orderids AS $id){
			$orderfulllists[$id]['order']			= $_orderlists[$id];
			$orderfulllists[$id]['orderExtension']	= $orderextens[$id];
			$orderfulllists[$id]['orderUserInfo'] 	= $orderuserinfo[$id];
			$orderfulllists[$id]['orderNote'] 		= $ordernote[$id];
			$orderfulllists[$id]['orderWarehouse'] 	= $orderwarehouse[$id];
			$orderfulllists[$id]['orderTracknumber']	= $ordertracknumber[$id];
			$orderfulllists[$id]['orderDetail'] 		= $orderdetail[$id];
		}
		unset($orderlists, $_orderlists, $orderextens, $orderuserinfo, $orderdetail, $ordernote, $orderwarehouse, $ordertracknumber);
		############################## end   获取订单详情和扩展信息   ##############################
		return $orderfulllists;
	}
	
	/**
	 * 根据订单id数组获取对应的完整的已发货订单信息
	 * @param array $ids
	 * @return array
	 * @author czq
	 */
	public function getFullshippedOrderById($ids){
		$ids = array_map('intval', $ids);
		$this->tablekey = 'shipped';
		$orderlists = $this->sql("SELECT * FROM ".C('DB_PREFIX')."shipped_order WHERE id IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
		if (empty($orderlists)){
			return array();
		}
		############################## start 获取订单详情和扩展信息  ##############################
		$_orderlists = $orderids = $suffixids = array();
		foreach ($orderlists AS $orderlist){
		array_push($orderids, $orderlist['id']);
		$suffixids[$orderlist['platformId']][] = $orderlist['id'];
				$_orderlists[$orderlist['id']] = $orderlist;
		}
		$orderextens 		= $this->getOrderExtensionList($suffixids);
		$orderuserinfo 		= $this->getOrderUserInfoList($orderids);
		$orderwarehouse 	= $this->getOrderWarehouseList($orderids);
		$orderdetail		= $this->getOrderDetailList($suffixids);
		$ordernote			= $this->getOrderNoteList($orderids);
		$ordertracknumber  	= $this->getOrderTracknumberList($orderids);
		$orderfulllists = array();
		foreach ($orderids AS $id){
			$orderfulllists[$id]['order']				= $_orderlists[$id];
			$orderfulllists[$id]['orderExtension']		= $orderextens[$id];
			$orderfulllists[$id]['orderUserInfo'] 		= $orderuserinfo[$id];
			$orderfulllists[$id]['orderNote'] 			= $ordernote[$id];
			$orderfulllists[$id]['orderWarehouse'] 		= $orderwarehouse[$id];
			$orderfulllists[$id]['orderTracknumber']	= $ordertracknumber[$id];
			$orderfulllists[$id]['orderDetail'] 		= $orderdetail[$id];
		}
		unset($orderlists, $_orderlists, $orderextens, $orderuserinfo, $orderdetail, $ordernote, $orderwarehouse, $ordertracknumber);
		############################## end   获取订单详情和扩展信息   ##############################
		return $orderfulllists;
		}
	
	/**
	 * 根据订单id数组获取对应的完整订单信息
	 * @param array $ids
	 * @return array
	 * @author lzx
	 */
	public function getOrderById($tabale, $ids){

		$this->tablekey = $tabale;
		$ids = array_map('intval', $ids);
		$tablelist = $this->getOrderTable();
		if (!in_array($tablelist['order'], $this->getOrderTableList())){
			self::$errMsg[10024] = get_promptmsg(10024, 0, $tablelist['order']);
			return false;
		}
		$orderlists = $this->sql("SELECT * FROM {$tablelist['order']} WHERE id IN (".implode(',', $ids).") AND is_delete=0")->limit('*')->select();
		if (empty($orderlists)){
			return array();
		}
		############################## start 获取订单详情和扩展信息  ##############################
		$_orderlists = $orderids = $suffixids = array();
		foreach ($orderlists AS $orderlist){
			array_push($orderids, $orderlist['id']);
			$suffixids[$orderlist['platformId']][] = $orderlist['id'];
			$_orderlists[$orderlist['id']] = $orderlist;
		}
		$orderextens 		= $this->getOrderExtensionList($suffixids);
		$orderuserinfo 		= $this->getOrderUserInfoList($orderids);
		$orderwarehouse 	= $this->getOrderWarehouseList($orderids);
		$orderdetail		= $this->getOrderDetailList($suffixids);
		$ordernote			= $this->getOrderNoteList($orderids);
		$ordertracknumber  	= $this->getOrderTracknumberList($orderids);
		$orderfulllists = array();
		foreach ($orderids AS $id){
			$orderfulllists[$id]['order']				= $_orderlists[$id];
			$orderfulllists[$id]['orderExtension']		= $orderextens[$id];
			$orderfulllists[$id]['orderUserInfo'] 		= $orderuserinfo[$id];
			$orderfulllists[$id]['orderNote'] 			= $ordernote[$id];
			$orderfulllists[$id]['orderWarehouse'] 		= $orderwarehouse[$id];
			$orderfulllists[$id]['orderTracknumber']	= $ordertracknumber[$id];
			$orderfulllists[$id]['orderDetail'] 		= $orderdetail[$id];
		}
		unset($orderlists, $_orderlists, $orderextens, $orderuserinfo, $orderdetail, $ordernote, $orderwarehouse, $ordertracknumber);
		############################## end   获取订单详情和扩展信息   ##############################
		return $orderfulllists;
	}

	/**
	 * 根据完结时间自动选择分表  需要增加数据表是否存在的验证----待开发
	 * @param array $timestamp 0|array
	 * @return string 表的关键字名
	 * @author lzx
	 */
	private function getOrderTableKey($timestamp=0){
		if ($timestamp==0){
			return 'unshipped';
		}
		$tmouth = strtotime(date("Y-m-d", strtotime("-3 month"))." 00:00:01");
		$nowtime = time();
		foreach ($timestamp AS $oper=>$worth){
			if ($oper=='$b'){
				list($starttime, $endtime) = array_map('intval', explode('-', $timestamp));
				if ($starttime>=$tmouth){
					return "shipped";
				}
				if (date("Ym", $starttime)!=date("Ym", $endtime)){
					self::$errMsg[10022] = get_promptmsg(10022, date("Y-m-d H:i", $starttime), date("Y-m-d H:i", $endtime));
					return false;
				}else{
					return "shipped".date("Ym", $starttime);
				}
			}else if ($oper=='$gt') {
				$starttime = intval($worth);
				return $starttime>=$tmouth ? "shipped" : "shipped".date("Ym", $starttime);
			}else if ($oper=='$lt') {
				$endtime = intval($worth);
				return $endtime>=$tmouth ? "shipped" : "shipped".date("Ym", $endtime);
			}
		}
		self::$errMsg[10023] = get_promptmsg(10023);
		return false;
	}

	/**
	 * 	om_shipped_order
		om_shipped_order_detail
		om_shipped_order_detail_extension_aliexpress
		om_shipped_order_detail_extension_amazon
		om_shipped_order_detail_extension_cndl
		om_shipped_order_detail_extension_domestic
		om_shipped_order_detail_extension_ebay
		om_shipped_order_detail_extension_newegg
		om_shipped_order_detail_extension_tmall
		om_shipped_order_extension_aliexpress
		om_shipped_order_extension_amazon
		om_shipped_order_extension_cndl
		om_shipped_order_extension_domestic
		om_shipped_order_extension_ebay
		om_shipped_order_extension_newegg
		om_shipped_order_extension_tmall
		om_shipped_order_userInfo
		om_shipped_order_warehouse
	 * 根据key取得表名
	 * @return string 表名
	 * @author lzx
	 */
	private function getOrderTable(){
		return array('order'		=>C('DB_PREFIX')."{$this->tablekey}_order",
					 'detail'		=>C('DB_PREFIX')."{$this->tablekey}_order_detail",
					 'userinfo'		=>C('DB_PREFIX')."{$this->tablekey}_order_userInfo",
					 'warehouse'	=>C('DB_PREFIX')."{$this->tablekey}_order_warehouse",
					 'trackcd'		=>C('DB_PREFIX')."order_tracknumber",
					 //'orderextcd'	=>C('DB_PREFIX')."{$this->tablekey}_order_extension_ebay",
				);
	}

	/**
	 * 获取所有订单相关的表名，包括未完结、已完结、备份表
	 * @return array 所有表名
	 * @author lzx
	 */
	private function getOrderTableList(){
		$ordertables = array();
		foreach ($this->sql('SHOW TABLES')->select(array('cache', 'mysql'), 8*3600) AS $tables){
			//om_shipped_order 匹配格式
			if (preg_match("/^om_[a-z,0-9]*_order$/", $tables['Tables_in_test_order'])>0) array_push($ordertables, $tables['Tables_in_test_order']);
		}
		return $ordertables;
	}

	/**
	 * 根据查询条件组装查询SQL语句
	 * @param array $conditions
	 * @return string 查询语句
	 * @author lzx
	 */
	private function getOrderSQL($conditions){
		$this->tablekey = $this->getOrderTableKey(isset($conditions['order']['completeTime']) ? $conditions['order']['completeTime'] : 0);
		if (!$this->tablekey){
			return false;
		}
		$tablelist = $this->getOrderTable();
		if (!in_array($tablelist['order'], $this->getOrderTableList())){
			self::$errMsg[10024] = get_promptmsg(10024, json_encode($conditions['order']['completeTime']), $tablelist['order']);
			return false;
		}
		$mainkey = '';
		$wherearray = array();



		foreach ($conditions AS $key=>$condition){
			$formatwhere = $this->formatWhereField($tablelist[$key], $condition);

			if (!$formatwhere){
				return false;
			}
			if ($key=='order'){
				$mainkey = $key;
				$sql = "SELECT `{$key}`.* FROM `{$tablelist[$key]}` AS `{$key}` ";
			}else{
				$sql .= " LEFT JOIN `{$tablelist[$key]}` AS `{$key}` ON `{$mainkey}`.id=`{$key}`.omOrderId ";
			}

			$wherearray = array_merge($wherearray, array2where($formatwhere, "`{$key}`"));
		}


		return "{$sql} WHERE ".implode(' AND ', $wherearray).(isset($conditions['detail']) ? "GROUP BY `{$mainkey}`.id" : '');
	}

    /**
	 * 根据userId和platformId,获取该用户的信息记录，om_buyerinfo
	 */
	public function  getBuyerinfo($userid, $platformId){
	    $table = C('DB_PREFIX').'buyerinfo';
        $user_info = $this->sql("SELECT * FROM $table where platformId={$platformId} and platformUsername='$userid'")->select(array('cache', 'mysql'));
		return $user_info;
	}

	/*
	 * 获取表名的关键词
	 * @return string 表名关键词
	 * @author czq
	 */
	public function getTableKey(){
		return isset($this->tablekey) ? $this->tablekey : false;
	}

	/**
     * 获取补寄类型信息
     * @retrun array 补寄类型
     * @author czq
     */
    public function getSendReplacement(){
    	$typeInfo = $this->sql("SELECT * FROM ".C('DB_PREFIX')."sendReplacement_type")->limit('*')->select();
    	$ret = array();
    	foreach($typeInfo as $type){
    		$ret[$type['id']] = $type['typeName'];
    	}
    	return $ret;
    }

    /**
     * 根据id获取补寄类型名称
     * @param unknown $id
     * @return boolean
     *@author czq
     */
    public function getSendReplacementTypeById($id){
    	$typeInfo = $this->sql("SELECT typeName FROM ".C('DB_PREFIX')."sendReplacement_type WHERE id={$id}")->limit('1')->select();
    	if($typeInfo){
    		return $typeInfo[0]['typeName'];
    	}
    	return false;
    }

    /*
     * 获取补寄理由信息
     * @return 补寄理由信息
     * @author czq
    */
    public function getSendReplacementReason(){
    	$resonInfo = $this->sql("SELECT * FROM ".C('DB_PREFIX')."sendReplacement_reason")->limit('*')->select();
    	$ret = array();
    	foreach($resonInfo as $reason){
    		$ret[$reason['id']] = $reason['reason'];
    	}
    	return $ret;
    }


    public function act_getUserInfo($id){
        $userInfo = $this->sql("SELECT * FROM ".C('DB_PREFIX')."unshipped_order_userInfo where omOrderId = '".$id."'")->limit('*')->select();
        return $userInfo;
    }
    /**
     * 根据id获取补寄理由
     * @param unknown $id
     * @return boolean
     *@author czq
     */
    public function getSendReplacementReasonById($id){
    	$resonInfo = $this->sql("SELECT reason FROM ".C('DB_PREFIX')."sendReplacement_reason WHERE id={$id}")->limit('1')->select();
    	if($resonInfo){
    		return $resonInfo[0]['reason'];
    	}
    	return false;
    }

    /**
     * 获取可以合并包裹信息
     * @param array $plateform_arr
     * @param array $carrierIds
     * @param string $id_array
     * @param number $storeId
     * @return array $ret
     *@author czq
     */
    public function getCombieList($plateform_arr,$carrierIds, $storeId = 1,$id_array = ''){
    	$tableName = C('DB_PREFIX').'unshipped_order';
    	$where = '';
    	if($id_array){
    		$where = 'AND a.id in ('.join(',',$id_array).') AND is_delete = 0 AND storeId = '.$storeId;
    	}
    	$sql = "SELECT a.id,a.platformId,a.accountId,a.transportId,a.orderStatus,a.orderType,a.calcWeight,b.userName,b.countryName,b.state,b.city,b.street
				 FROM ".$tableName." AS a LEFT JOIN ".$tableName."_userInfo as b
				 ON a.id=b.omOrderId WHERE a.isLock=0 AND a.is_delete=0 AND a.combinePackage=0 AND a.orderStatus=100 AND a.calcWeight<=2 AND a.orderType=101 AND a.platformId in (".join(',', $plateform_arr).") AND a.transportId in (".join(',', $carrierIds).") {$where}
    	 		 GROUP BY b.username, b.city, b.state, b.street
    	         HAVING count(*)>1";
    	$ret = $this->sql($sql)->limit('*')->select();
    	if($ret){
    		return $ret;
    	}else{
    		return false;
    	}
    }

 	/**
     * 获取相同信息的包裹进行合并
     * @param array $userInfo
     * @param string $id_array
     * @return $ret|boolean
     * @author czq
     */
    public function getCombineOrders($userInfo, $storeId=1,$id_array=''){
    	$tableName = C('DB_PREFIX').'unshipped_order';
    	/*$where = " b.userName = '{$userInfo['userName']}'
	    				AND a.storeId ='{$storeId}'
	    				AND a.isLock=0
    					AND a.combinePackage=0
    					AND a.is_delete = 0";*/
    	$where = " b.userName = '{$userInfo['userName']}'
    						AND b.countryName = '{$userInfo['countryName']}'
    						AND a.accountId = '{$userInfo['accountId']}'
							AND a.transportId = '{$userInfo['transportId']}'
							AND b.state='{$userInfo['state']}'
    						AND b.city='{$userInfo['city']}'
    						AND b.street='{$userInfo['street']}'
    						AND a.orderType='{$userInfo['orderType']}'
    						AND a.orderStatus='{$userInfo['orderStatus']}'
    						AND a.storeId ='{$storeId}'
    						AND a.isLock=0
    						AND a.combinePackage=0
    						AND a.is_delete = 0";
    	if($id_array){
    		$where .= ' AND a.id in ('.join(',',$id_array).') ';
    	}
    	$sql = "SELECT a.id,a.actualTotal,a.platformId,a.accountId,a.transportId,a.orderStatus,a.orderType,a.calcWeight,b.username,b.countryName,b.state,b.city,b.street
						FROM ".$tableName." AS a LEFT JOIN ".$tableName."_userInfo as b
    					ON a.id=b.omOrderId
    					WHERE {$where}";
    	$ret = $this->sql($sql)->limit('*')->select();
    	if($ret){
    		return $ret;
    	}else{
    		return false;
    	}
    }

    /**
     * 获取需取消的订单包裹
     * @return array $ret
     * @author czq
     */
    public function getCancelCombineOrder($str){
    	$sql = "SELECT id,combinePackage FROM ".C('DB_PREFIX')."unshipped_order WHERE combinePackage !=0 AND id in ({$str})";
    	$ret = $this->sql($sql)->limit('*')->select();
    	if($ret){
    		return $ret;
    	}else{
    		return false;
    	}
    }
    /**
     * 通过主订单从合并关系表获取子订单id
     * @param int $mainid
     * @return array $ret|boolean
     * @author czq
     */
    public function getSonOrder($mainid){
    	$sql = "SELECT split_order_id FROM ".C('DB_PREFIX')."records_combinePackage WHERE main_order_id={$mainid} AND is_enable=1";
    	$ret = $this->sql($sql)->limit('*')->select();
    	if($ret){
    		return $ret;
    	}else{
    		return false;
    	}
    }

   /**
    * 通过子订单id获取合并关系表中的主订单id
    * @param int $sonid
    * @return boolean
    * @author czq
    */
    public function getMainOrder($sonid){
    	$sql = "SELECT main_order_id FROM ".C('DB_PREFIX')."records_combinePackage WHERE split_order_id={$sonid}";
    	$ret = $this->sql($sql)->limit('*')->select();
    	if($ret){
    		return $ret[0]['main_order_id'];
    	}else{
    		return false;
    	}
    }

    /**
     * 获取订单下的全部真实料号
     * @param number $id
     * @param number $type
     * @param number $storeId
     * @return boolean|$skuinfos array
     * @author czq
     */
    public function getRealskulist($id, $type = 1, $storeId = 1){
    	if($type == 1){
    		$table = 'unshipped';
    	}else if($type == 2){
    		$table = 'shipped';
    	}else{
    		return false;
    	}
    	$orders = $this->getOrderById($table, array($id));
    	$order  = $orders[$id]['order'];
    	if($order){
    		$skuinfos = array();
    		$omOrderId 		= $order['id'];
    		$combinePackage = $order['combinePackage'];
    		//获取订单明细
    		$orderdetails = $orders[$id]['detail']['base'];
    		foreach ($orderdetails AS $_k => $odlist){
    			$sku = trim($odlist['sku']);
    			$amount = $odlist['amount'];
    			/**预留，待实现**/
    			$sku_arr = GoodsModel::get_realskuinfo($sku);
    			foreach($sku_arr as $or_sku => $or_nums){
    				if(isset($skuinfos[$or_sku])){
    					$skuinfos[$or_sku]+=$or_nums * $amount;
    				}else{
    					$skuinfos[$or_sku]=$or_nums * $amount;
    				}
    			}
    		}

    		if($combinePackage == 1){
    				$sonCombineOrders = $this->selectSonOrder($id);
    					foreach($sonCombineOrders as $sonOrder){
    						$_skuinfos = $this->getRealskulist($sonOrder['split_order_id'],$type,$storeId);
    						foreach($_skuinfos as $_sku => $_nums){
    							if(isset($_skuinfos[$_sku])){
    								$skuinfos[$_sku]+=$_nums;
    							}else{
    								$skuinfos[$_sku]=$_nums;
    							}
    						}
    					}
    		}
    		return $skuinfos;
    	}
    	return false;
	}

	/**
	 * 检测订单抓取号是否存在
	 * @param string $orderid
	 * @return bool
	 * @author lzx
	 */
	public function checkEbayOrderidExists($orderid){
    	return $this->sql("SELECT COUNT(*) AS count FROM ".C('DB_PREFIX')."ebay_order_ids WHERE ebay_orderid='{$orderid}'")->count()>0 ? true : false;
	}
	
	/**
	 * 根据条件检查订单是否存在
	 * @param array condition (键值对形式)
	 * @return bool
	 * @author yxd
	 */
	public function checkExistsByCondition($condition){
		$condition    = implode(" and ", array2where($condition));
		return  $this->sql("SELECT COUNT(*) AS count FROM ".C('DB_PREFIX')."unshipped_order WHERE ".$condition)->count()>0 ? true : false;
	}
	/**
	 * 根据平台和入系统时间获取订单信息
	 * @param array condition (键值对形式)
	 * @return bool
	 * @author yxd
	 */
	public function getOrdersByPlatForm($condition){ 
		$condition    = implode(" and ", array2where($condition));
		
		return  $this->sql("SELECT platformId,accountId,orderStatus,orderType FROM ".C('DB_PREFIX')."unshipped_order WHERE $condition ")->limit("*")->select();
	
	}
	
	/**
	 * 检测订单抓取号是否有效
	 * @param string $orderid
	 * @return int
	 * @author lzx
	 */
	public function checkEbayOrderidValid($orderid){
		$spiderid = $this->sql("SELECT spiderstatus FROM ".C('DB_PREFIX')."ebay_order_ids WHERE ebay_orderid='{$orderid}'")->limit(1)->select();
    	return isset($spiderid[0]['spiderstatus']) ? $spiderid[0]['spiderstatus'] : false;
	}
	
	/**
	 * 获取需要标记发货的订单id
	 * @return array $orderIds
	 * @author czq
	 */
	public function getMarkOrders($accountId){
		$sql = "SELECT omOrderId FROM ".C('DB_PREFIX')."mark_shipping WHERE account='{$accountId}' AND status=0";
		$ret = $this->sql($sql)->limit(100)->select();  //一次只标记100个
		if($ret){
			$orderIds = array();
			foreach($ret as $orders){
				$orderIds[] = $orders['omOrderId'];
			}
			return $orderIds;
		}
		return false;
	}
	
	/**
	 * 获取需要上传跟踪号的订单id
	 * @param number $accountId
	 * @param number $platformId
	 * @return array $orderIds; 
	 * @author czq
	 */
	public function getUploadTrackOrderIds($accountId,$platformId){
		$tableName 	= C('DB_PREFIX').'shipped_order';
		$sql		= "	SELECT id
						FROM ".$tableName." 
						WHERE platformId = 1
						AND	accountId = '{$accountId}'
						AND is_delete = 0
						AND storeId = 1
						AND markTime = 0";
		$ret = $this->sql($sql)->limit('*')->select();
		if($ret){
			$orderIds = array();
			foreach($ret as $orders){
				$orderIds[] = $orders['id'];
			}
			return $orderIds;
		}
		
	}
	
	public function checkMarkOrder($id){
		return $this->sql("SELECT COUNT(*) AS count FROM ".C('DB_PREFIX')."mark_shipping WHERE omOrderId='{$id}' AND status = 0 ")->count()>0 ? true : false;
	}
    
    /**
	 * 根据accountId和称重时获取未标记发货的订单信息（速卖通自动标记发货中用到）
	 * @param int $accountId
	 * @param int $startTime
	 * @return array
	 * @author zqt
	 */
	public function getUnMarkShippingOrdersByAS($accountId,$startTime){
		$sql  = "select id,recordNumber,transportId,combinePackage,orderStatus 
				from om_shipped_order as a 
				left join om_shipped_order_warehouse as b 
				on a.id = b.omOrderId
				where a.accountId = '$accountId' 
				and a.orderStatus ='".C("STATESHIPPED")."'
				and a.orderType = '".C("STATEHASSHIPPED_CONV")."' 
				and b.weighTime > $startTime and (a.ShippedTime ='' or a.ShippedTime is null) ";
		return $this->sql($sql)->sort('order by b.weighTime')->limit('*')->select();
	}
    
    /**
	 * 根据recordNumber获取已发货的订单信息（速卖通自动标记发货中用到）
	 * @param int $accountId
	 * @param int $startTime
	 * @return array 
	 * @author zqt
	 */
	public function getUnMarkShippingOrdersByReNum($recordNumber){
		$sql = "SELECT id,recordNumber,transportId,combinePackage ,orderStatus, ShippedTime, combineOrder 
				FROM om_shipped_order 
				WHERE recordNumber='$recordNumber' 
				AND orderStatus ='".C("STATESHIPPED")."'
				AND orderType = '".C("STATEHASSHIPPED_CONV")."' ";
		return $this->sql($sql)->limit('*')->select();
	}
    
    /**
	 * 根据main_order_id主订单下的分订单号记录
	 * @param int $mainOrderId
	 * @return array 
	 * @author zqt
	 */
	public function getSplitOrderIdByMainOrderId($mainOrderId){
		$sql = "SELECT split_order_id 
				FROM om_records_combineOrder 
				WHERE is_enable=1 
				AND main_order_id='$mainOrderId' ";
		return $this->sql($sql)->limit('*')->select();
	}
    
    /**
	 * 根据id获取对应的该id下的shippedOrder订单下的recordNumber信息
	 * @param int $id
	 * @return array 
	 * @author zqt
	 */
	public function getShippedOrderRecordNumberById($id){
		$sql = "SELECT recordNumber 
				FROM om_shipped_order 
				WHERE is_delete=0 
				AND id='$id' ";
		return $this->sql($sql)->limit('*')->select();
	}
	
	/**
	 * 根据国家简称获取国家信息列表
	 * @param string $countrySn
	 * @return array
	 * @author czq
	 */
	public function getCountrieInfoBySn($countrySn){
		$countrySn = trim($countrySn);
		$sql = "select * from om_country_list where regions_jc='{$countrySn}'";
		$countrysInfo = $this->sql($sql)->limit('*')->select();
        return $countrysInfo[0];
    }
    
    /**
	 * 根据orderStatus在unshipped表中取得对应的有效的订单的订单id,剔除掉合并包裹的子订单combinePackage=2
	 * @param int orderStatus
	 * @return array
	 * @author zqt
	 */
	public function getOrderIdByOrderStatus($orderStatus=0){
	    $returnArr = array();
		$$orderStatus = intval($orderStatus);
		$sql = "select id from ".C('DB_PREFIX')."unshipped_order where is_delete=0 AND orderStatus=$orderStatus and combinePackage<>2";
		$orderIdList = $this->sql($sql)->limit('*')->select();
        if(!empty($orderIdList)){
            foreach($orderIdList as $value){
                $returnArr[] = $value['id'];
            }
        }
        return array_unique($returnArr);
    }
    
    /**
	 * 根据合并包裹主订单查询其子订单的订单号
	 * @param int orderStatus
	 * @return array
	 * @author zqt
	 */
	public function getECombinePackageOrderIdByMOrderId($mOrderId){
	    $returnArr = array();
		$mOrderId = intval($mOrderId);
		$sql = "select split_order_id from ".C('DB_PREFIX')."records_combinePackage where is_enable=1 AND main_order_id=$mOrderId";
		$orderIdList = $this->sql($sql)->limit('*')->select();
        if(!empty($orderIdList)){
            foreach($orderIdList as $value){
                $returnArr[] = $value['split_order_id'];
            }
        }
        return array_unique($returnArr);
    }
    
    /**
	 * 检测订单抓取号是否存在
	 * @param string $orderid
	 * @return bool
	 * @author lzx
	 */
	public function checkOrderCalcInfoExists($omOrderId){
    	return $this->sql("SELECT COUNT(*) AS count FROM ".C('DB_PREFIX')."order_calculation WHERE omOrderId='{$omOrderId}'")->count()>0 ? true : false;
	}
    
    /**
	 * 根据id获取对应的该id下的估算信息
	 * @param int $omOrderId
	 * @return array 
	 * @author zqt
	 */
	public function getOrderCalcListById($omOrderId){
	    $omOrderId = intval($omOrderId);
		$sql = "select * from ".C('DB_PREFIX')."order_calculation where omOrderId=$omOrderId";
		return $this->sql($sql)->limit(1)->select();
	}
    
    /**
	 * 根据omOrderId获取超大审核记录表中的相关记录信息
	 * @param int $omOrderId
	 * @return array 
	 * @author zqt
	 */
	public function getOrderAuditListById($omOrderId){
	    $omOrderId = intval($omOrderId);
		$sql = "select * from ".C('DB_PREFIX')."records_order_audit where omOrderId=$omOrderId";
		return $this->sql($sql)->limit('*')->select();
	}
	public  function getSplittedMainOrderId($orderid=0){
		$result = $this->sql('select main_order_id from om_records_splitOrder where split_order_id='.$orderid.' ')->limit(1)->select();
		if(empty($result)) return $orderid;
		
		return $result[0]['main_order_id'];
	}
    public function getOrderAuditListByDetailId($omOrderDetailId){
        $omOrderDetailId = intval($omOrderDetailId);
        $sql = "select * from ".C('DB_PREFIX')."records_order_audit where omOrderdetailId=$omOrderDetailId";
        return $this->sql($sql)->limit('*')->select();
    }
    
}