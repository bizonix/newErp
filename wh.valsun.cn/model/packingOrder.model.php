<?php

/**
 * 配货单处理
 * 作者 涂兴隆
 */
class PackingOrderModel
{

    private $dbconn = null;
    
    /*
     * 构造函数
     */
    public function __construct ()
    {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 根据条件获得所有的配货单列表 $wheresql sql条件语句
     */
    public function getBillList ($wheresql)
    {
        $sql = "
                select po.id, po.username, po.platformUsername, po.countryName, po.email, po.state, po.city, po.street, po.transportId , po.accountId,
                po.orderStatus, po.createdTime,  po.calcWeight,  po.pmId,  po.platformId, sr.weighTime , por.originOrderId , pn.content  from wh_shipping_order as po left join 
                wh_shipping_order_relation as por on po.id = por.shipOrderId left join  wh_shipping_order_records as sr on po.id=sr.shipOrderId left join 
				wh_shipping_orderdetail as pd on po.id = pd.shipOrderId left join wh_shipping_order_note_record as pn on po.id = pn.shipOrderId and pn.is_delete = 0 LEFT JOIN wh_order_tracknumber AS ot ON po.id = ot.shipOrderId where 1 $wheresql;
               ";
        $staus_obj = new LibraryStatusModel();
        $list = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        foreach ($list as &$val) {
            //$val['shipingname'] = CommonModel::getShipingNameById($val['transportId']);
            $val['createdTime'] = date('Y-m-d H:i:s', $val['createdTime']);
            $val['weighTime'] = date('Y-m-d H:i:s', $val['weighTime']);
            $val['statusname'] = $staus_obj->statusIttoStr($val['orderStatus']);
        }
        return $list;
    }
    
    /*
     * 根据条件获得配货单总数 $where 条件语句字符串 返回 unsigned int 结果行数
     */
    public function getRowAllNumber ($where)
    {
        $sql = "select count(*) as num from wh_shipping_order as po LEFT JOIN wh_shipping_order_relation as por on po.id = por.shipOrderId 
				LEFT JOIN  wh_shipping_order_records as sr on po.id=sr.shipOrderId LEFT JOIN 
				wh_shipping_orderdetail as pd on po.id = pd.shipOrderId LEFT JOIN wh_shipping_order_note_record as pn on po.id = pn.shipOrderId and pn.is_delete = 0 LEFT JOIN wh_order_tracknumber AS ot ON po.id = ot.shipOrderId where 1 $where";     
    	$query = $this->dbconn->query($sql);
		$ret   = $this->dbconn->fetch_array_all($query);
        return count($ret);
    }
    
    /*
     * 获得指定状态的发货单数量 $status int 发货单状态 $type const int 订单类型
     */
    public function getShipingCountByStatusAndType ($status, $type = PKT_NORMAL)
    {
        $sql = 'select count(*) as num from wh_shipping_order where orderStatus=' .
                 $status . ' and orderAttributes=' . $type;
        // echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 获取指定状态的发货单/配货单数量 $status 订单状态码数组
     */
    public function getRecordsNumByStatus ($status)
    {
        $str = implode(',', $status);
        $sql = "select count(*) as num from wh_shipping_order where orderStatus in ($str)";
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据订单id获得发货单信息 $orderid 订单编号 $where 附加条件语句
     */
    public function getOrderInfoById ($orderid, $where = '')
    {
        $sql = 'select * from wh_shipping_order where 1 ' . $where . ' and id=' .
                 $orderid;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 录入重量信息 $data 要录入信息 成功返回 true 失败返回false
     */
    public function recordWeightInfo ($data)
    {
        $weight = $data['weight'];
        $userid = $data['userid'];
        $orderid = $data['orderid'];
        $status = $data['status'];
        $time = time();
        
        $weight_sql = "
                update wh_shipping_order_records set weighStaffId=$userid, actualWeight=$weight, weighTime=$time 
                where shipOrderId = $orderid
                ";
        
        $cstatus_sql = "
                update wh_shipping_order set orderStatus=$status,calcWeight=$weight,orderWeight='$weight' where id=$orderid
                ";
        $this->dbconn->begin();
        $upwei_re = $this->dbconn->query($weight_sql);
        if (empty($upwei_re)) { // 执行失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=0');
            return FALSE;
        }
        $upstatus_re = $this->dbconn->query($cstatus_sql);
        if (empty($upstatus_re)) { // 失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=0');
            return FALSE;
        }
        $this->dbconn->commit(); // 成功 提交
        $this->dbconn->query('SET AUTOCOMMIT=0');
        return TRUE;
    }
    
    /*
     * 根据发互动id获得对应的订单id $orderid 发货单/配货单号码
     */
    public function getSellOrderidByOrderid ($orderid)
    {
        $sql = 'select * from wh_shipping_order_relation where shipOrderId=' .
                 $orderid;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 芬哲称重扫描
     */
    public function recordWeightInfoFZ ($data)
    {
        $weight = $data['weight'];
        $userid = $data['userid'];
        $orderid = $data['orderid'];
        $express = $data['express'];
        $express = mysql_real_escape_string($express);
        $status = $data['status'];
        $time = time();
        $storid = $data['storid'];
        
        $weight_sql = "
        update wh_shipping_order_records set weighStaffId=$userid, actualWeight=$weight, weighTime=$time
        where shipOrderId = $orderid
        "; // 录入重量信息
        
        $cstatus_sql = "
        update wh_shipping_order set orderStatus=$status where id=$orderid
        "; // 更改状态
        
        $express_sql = "insert into wh_order_tracknumber values ('$express', $orderid, $time, $storid, 0)";
        
        $this->dbconn->begin();
        
        $upwei_re = $this->dbconn->query($weight_sql);
        if (empty($upwei_re)) { // 执行失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=0');
            return FALSE;
        }
        
        $upstatus_re = $this->dbconn->query($cstatus_sql);
        if (empty($upstatus_re)) { // 失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=0');
            return FALSE;
        }
        
        $express_insert = $this->dbconn->query($express_sql);
        if (empty($express_insert)) { // 失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=0');
            return FALSE;
        }
        
        $this->dbconn->commit(); // 成功 提交
        $this->dbconn->query('SET AUTOCOMMIT=0');
        return TRUE;
    }
    
    /*
     * 获得某个发货单id集合的所有发货单信息
     * $idlist 发货单id数组
     */
    public function getOrderInfoByIdList($idlist, $where=''){
        $str = implode(',', $idlist);
        $sql = "
                select so.id, so.platformUsername,so.accountId, so.zipCode, so.username, so.countryName, so.countrySn, so.state, so.city, so.street, so.address2, so.address3, so.landline, so.phone
                ,so.createdTime, sor.recordNumber,sor.originOrderId from wh_shipping_order as so join wh_shipping_order_relation as sor on so.id=sor.shipOrderId  where 1 and so.id in ($str) 
                group by so.id $where
        ";
        $orderlist = array();
        $q_order   = $this->dbconn->query($sql);
		$orderlist = $this->dbconn->fetch_array_all($q_order);
		$total = 0;
		foreach($orderlist as &$list){
			$list['countryZh'] = CommonModel::getCountryNameCn($list['countryName']);
			$remarks    	   = CommonModel::getExpressRemark($list['id']);//得到快递备注            
            $accountId         = CommonModel::getAccountNameById($list['accountId']);//店铺账号
            
            $list['appname']   = $accountId['account'];//账号昵称
            if(!empty($remarks)){                           
    			foreach($remarks as $remark){
    				$total += ($remark['amount']*$remark['price']);
    			}
            }
			$list['remarkTotal'] = $total;
			$list['remark']      = $remarks;
		}
        return $orderlist;
    }
    
    /*
     * 根据订单列表获取订单详细sku信息
     * $orderlist
     */
    public function buildOrderinfo(&$orderlist) {
    	foreach ($orderlist as &$orderval){
    	    $sql  = "select a.*,b.actualStock,b.arrivalInventory,c.goodsName,c.spu from wh_shipping_orderdetail as a left join wh_sku_location as b 
					on a.sku=b.sku left join pc_goods as c on a.sku=c.sku where a.shipOrderId=".$orderval['id'].' and a.is_delete = 0';
    	    $orderval['skulistar']= $this->dbconn->fetch_array_all($this->dbconn->query($sql));
			//$orderval['line_num'] = ceil(count($orderval['skulistar'])/3);
		}
    }
    
    /*
     * 批量更改发货单为待处理的状态改为待配货状态
     * $orderids 带配货的id
     */
    public function changeStatusToWaitGetGoods($orderids){
        $sql = "update wh_shipping_order set orderStatus=".PKS_WGETGOODS." where id in ($orderids) and orderStatus=".PKS_WPRINT;
        $this->dbconn->begin();
        $qresult = $this->dbconn->query($sql);
        if (empty($qresult)) {  //更新失败
        	$this->dbconn->rollback();
        	$this->dbconn->query('SET AUTOCOMMIT=1');
        	return FALSE;
        }
        $this->dbconn->commit();
        $this->dbconn->query('SET AUTOCOMMIT=1');
        return TRUE;
    }
    
    /*
     * 更新shippingorder表数据
     * $str 更新字符串  $where where条件
     */
    public function updateShipingorder($str, $where){
        $sql = "update wh_shipping_order set $str where 1 ".$where;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获得一组待打印的发货单信息
     * $ids string 1,2,3..此种形式
     */
    public function getaSetOfOrderInfo($ids){
        $sql = "select a.*,b.sku,c.recordNumber from wh_shipping_order as a join wh_shipping_orderdetail as b 
				on a.id=b.shipOrderId join wh_shipping_order_relation as c on a.id=c.shipOrderId  where a.id in ($ids) group by a.id order by b.pName,b.sku
                ";
        //echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 根据发货单id获得某个发货单的备注信息
     * $orderid 发货单id
     */
    public function getOrderNotesInfo($orderid){
        $sql = 'select * from  wh_order_notes where shipOrderId='.$orderid;
        $res = $this->dbconn->fetch_first($sql);
		if($res){
			return $res['content'];
		}else{
			return '';
		}
    }
	
	/*
     * 批量更改发货单为待处理的状态改为待配货状态
     * $orderids 待打印的id
     */
    public function changeStatusToWaiting($orderids){
        $sql = "update wh_shipping_order set orderStatus=400 where id in ($orderids) and orderStatus=401";
        $this->dbconn->begin();
        $qresult = $this->dbconn->query($sql);
        if (empty($qresult)) {  //更新失败
        	$this->dbconn->rollback();
        	$this->dbconn->query('SET AUTOCOMMIT=1');
        	return FALSE;
        }
        $this->dbconn->commit();
        $this->dbconn->query('SET AUTOCOMMIT=1');
        return TRUE;
    }
	
	
	/*
     * 批量更改发货单为待配货的状态改为异常
     * $orderids 待配货的id
     */
    public function changeStatusToUnusual($orderids){
        $sql = "update wh_shipping_order set orderStatus=901 where id in ($orderids)";
        $qresult = $this->dbconn->query($sql);
        if (empty($qresult)) {  //更新失败
        	return FALSE;
        }
        return TRUE;
    }
	
	/*
     * 批量更改发货单为待处理的状态改为B仓待提货
     * $orderids 带配货的id
     */
    public function changeStatusToWaitGetGoodsB($orderids){
        $sql = "update wh_shipping_order set orderStatus=".PKS_WGETGOODSB." where id in ($orderids) and orderStatus=".PKS_WPRINT;
        $this->dbconn->begin();
        $qresult = $this->dbconn->query($sql);
        if (empty($qresult)) {  //更新失败
        	$this->dbconn->rollback();
        	$this->dbconn->query('SET AUTOCOMMIT=1');
        	return FALSE;
        }
        $this->dbconn->commit();
        $this->dbconn->query('SET AUTOCOMMIT=1');
        return TRUE;
    }
}