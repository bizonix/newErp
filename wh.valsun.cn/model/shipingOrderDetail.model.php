<?php
/** 
 * @author 涂兴隆
 * 配货单明细表
 */
class ShipingOrderDetailModel
{
    private $dbconn = null;

    /**
     * 构造函数
     */
    function __construct ()
    {
    	global $dbConn;    //初始化数据库连接
    	$this->dbconn = $dbConn;
    }
    
    /*
     * 根据发货单号获取sku的列表信息
     * $orderid 发货单号
     */
    public function getSkuListByOrderId($orderid, $where=''){
        $sql = "select * from wh_shipping_orderdetail where shipOrderId=$orderid $where";
        $return_result = array();
        $result = $this->dbconn->query($sql);
        while ($row = mysql_fetch_assoc($result)){
            $return_result[$row['id']] = $row;
        }
        return $return_result;
    }
	
	/*
     * 根据发货单号获取sku的列表信息、组合料号组合打包
     * $orderid 发货单号
     */
    public function getAllSkuListByOrderId($orderid, $where='', $tag=1){
        $sql = "select * from wh_shipping_orderdetail where shipOrderId=$orderid $where";
		$query = $this->dbconn->query($sql);
		$lists = $this->dbconn->fetch_array_all($query);
		$return_result = array();
		$result = array();
		$totalnum = 0;
		$package_type = '';
		foreach ($lists AS $list){
			$skuinfo   = OmAvailableModel::getTNameList("pc_goods","goodsCategory,isPacking","where sku='{$list['sku']}'");
			if($tag==1){
				$catename  = SkuStockModel::getCategoryInfoByPath($skuinfo[0]['goodsCategory']);
			}else{
				$catename = '';
			}
			$isPacking = ($skuinfo[0]['isPacking']==2)? 'Y&nbsp;' : 'N&nbsp;';
			$iscombine = (empty($list['combineSku']))? 0 : 1;
			if($iscombine){
				$result[$list['combineSku']]['iscombine'] = $iscombine;
				$result[$list['combineSku']]['info'][] = $isPacking.'['.$list['pName'].'] '.$list['sku'].'*'.$list['amount'].$catename;
			}else{
				$result['notcombine']['iscombine'] = $iscombine;
				$result['notcombine']['info'][] = $isPacking.'['.$list['pName'].'] '.$list['sku'].'*'.$list['amount'].$catename;
			}
			$totalnum += $list['amount'];
			$package_type = $list['packageType'];
		}
		$return_result = array(
			'skuinfo'  => $result,
			'totalnum' => $totalnum,
			'packagetype' => $package_type
		);
        return $return_result;
    }
    
    /*
     * 获得某个发货单下的某个sku的详细信息
     */
    public function getSkuDetail($orderid, $sku){
        $sku = mysql_real_escape_string($sku);
        $sql = 'select * from wh_shipping_orderdetail where shipOrderId='.$orderid.' and sku='."'$sku'";
        $return_result = array();
        $q_resutl = $this->dbconn->query($sql);
        while ($row = mysql_fetch_assoc($q_resutl)){
            $return_result[$row['id']] = $row;
        }
        return $return_result;
    }
    
    /*
     * 获取发货单已配货的sku信息
     * $orderid 发货单id
     */
    public function getSkuHavedone($orderid){
        $sql = 'select * from wh_order_picking_records where shipOrderId = '.$orderid.' and is_delete=0';
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 订单配货成功以后 扣库存 和 增加配货记录
     * $data 所修要的数据数组
     */
    public function recordDataToSystem($data){
        $number = $data['num'];
        $sku = mysql_real_escape_string($data['sku']);
		$pName = mysql_real_escape_string($data['pName']);
        $orderid = $data['orderid'];
        $orderdetailid = $data['detailid'];
        $amount = $data['amount'];  //配货数
        $totalNums = $data['totalnum']; //料号总数
        $scantime = time();
        $scanuserid = $data['userid'];  //扫描用户id
        $islast = $data['islast'];
        $type = $data['orderTypeId'];   //是否为配货单
        $shiptype = $data['shiptype'];  //快递类型 分国内 国外 小包
        
        $insert_records = "
                insert into wh_order_picking_records values (null, $orderid, $orderdetailid, '$sku', '$pName', $amount,
            $totalNums, $scantime, '$scanuserid', null, null, 1, 0)
        ";      //插入配货记录到数据库

        $update_sql = "
                update wh_sku_location set actualStock=actualStock-$amount where sku = '$sku'
                ";  //更新总库存
		
		$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$pName' and storeId=1");
		if($position_info){
			$positionId = $position_info[0]['id'];
		}else{
			return false;
		}
		
		$sku_info = OmAvailableModel::getTNameList("pc_goods","id","where sku='$sku'");
        if($sku_info){
			$pId  = $sku_info[0]['id'];
		}else{
			return false;
		}

		 $update_relation_sql = "
                update wh_product_position_relation set nums=nums-$amount where pId = '$pId' and positionId='$positionId' and is_delete=0 and storeId=1
                ";  //更新具体仓位库存
		
        $this->dbconn->begin();
        
//         echo $insert_records;exit;
        $insert_result = $this->dbconn->query($insert_records); //插入配货记录
        if (!$insert_result) {
        	$this->dbconn->rollback(); //失败 回滚
        	$this->dbconn->query("SET AUTOCOMMIT=1");
        	return false;
        }
        
        $update_result = $this->dbconn->query($update_sql); //更新总库存
        if(!$update_result){
            $this->dbconn->rollback();  //失败 回滚
            $this->dbconn->query("SET AUTOCOMMIT=1");
            return false;
        }
		
		$update_res = $this->dbconn->query($update_relation_sql); //更新实际库存
        if(!$update_res){
            $this->dbconn->rollback();  //失败 回滚
            $this->dbconn->query("SET AUTOCOMMIT=1");
            return false;
        }
        
        $nextstatus = 0;
        if(1 == $shiptype){ //快递
            $nextstatus = PKS_WIQC_EX;
        } elseif (2 == $shiptype) { //小包
            $nextstatus = PKS_WIQC;
        } elseif (3 == $shiptype) { //国内快递
            $nextstatus = PKS_INLANDWWEIGHING;
        }
        
        if ($islast) {  //如果是最后一个配货料号 则还需更改发货单状态
            $up_status_sql = '';
            $up_status_sql = 'update wh_shipping_order set orderStatus='.$nextstatus.' where id='.$orderid;
			/*
			if($type==1){    //当前为发货单
                $up_status_sql = 'update wh_shipping_order set orderStatus='.$nextstatus.' where id='.$orderid;
            } else {        //当前单为配货单
                $up_status_sql = 'update wh_shipping_order set orderStatus='.PKS_PDONE.' where id='.$orderid;
            }*/
            $upst_result = $this->dbconn->query($up_status_sql);
            if (!$update_result) {  //修改状态失败
            	$this->dbconn->rollback();
            	$this->dbconn->query("SET AUTOCOMMIT=1");
            	return FALSE;
            }
        }
        
        $this->dbconn->commit();
        $this->dbconn->query("SET AUTOCOMMIT=1");
        return TRUE;
    }
    
    /*
     * 获得某个发货单下面可配货的sku列表
     *  $orderid 发货单id
     *  返回 sku数组列表
     */
    public function getSkuListInOneOrder($orderid){
        $skulist = $this->getSkuListByOrderId($orderid, 'order by pName');
        $sku_hasscan = $this->getSkuHavedone($orderid);
        foreach ($sku_hasscan as $skval) {   //去掉已经配货的sku
            if(array_key_exists($skval['shipOrderdetailId'], $skulist)){
                unset($skulist[$skval['shipOrderdetailId']]);
            }
        }
        return $skulist;
    }
    
    /*
     * 根据已配货的料号数据回滚库存
     * $skulist 已经配货的sku数组
     */
    public function rollBackStock($skulist){
        $this->dbconn->begin();
        foreach ($skulist as $skuval){
            $sql = "update wh_sku_location set actualStock = actualStock+$skuval[amount] where sku='$skuval[sku]'";
            $qres = $this->dbconn->query($sql);
            if (empty($qres)) {
            	$this->dbconn->rollback();
            	return FALSE;
            }
        }
        $this->dbconn->commit();
        return TRUE;
    }
    
    /*
     * 根据发货单id取得对应的wh_shipping_order_records表中的数据
     * $id 发货单id
     */
    public function getShippingOrderRecordsById($id){
        $sql = 'select * from wh_shipping_order_records where shipOrderId='.$id;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 更新发货单记录表 的信息
     * $str 更新sql $where 条件语句
     */
    public function updateRecords($star, $where){
        $sql = "update wh_shipping_order_records set $star where 1 $where";
        return $this->dbconn->query($sql);
    }

    /*
    *验证快递发货单号是否存在料号 add by wangminwei 2014-03-07
    *$orderid 发货单号 $sku 料号
    **/
    public function checkOrderSku($orderid, $sku){
        $num    = 0;
        $sql    = "select count(*) AS total from wh_shipping_orderdetail where shipOrderId = '{$orderid}' AND sku = '{$sku}'";
        $data   =  $this->dbconn->fetch_first($sql);
        if(!empty($data)){
            $num = $data['total'];
        }
        return $num;
    }

    /*
    *验证快递发货单号料号是否已配货 add by wangminwei 2014-03-07
    *$orderid 发货单号 $sku 料号
    **/
    public function checkSkuPickRecord($orderid, $sku, $pname){
        $num    = 0;
        $sql    = "select count(*) AS total from wh_order_picking_records where shipOrderId = '{$orderid}' AND sku = '{$sku}' AND pName = '{$pname}'";
        $data   =  $this->dbconn->fetch_first($sql);
        if(!empty($data)){
            $num = $data['total'];
        }
        return $num;
    }

    /*
    *验证发货单号配货数量是否与发货单数量一致 add by wangminwei 2014-03-07
    *$orderid 发货单号 $sku 料号
    **/
    public function checkSkuQty($orderid, $sku){
        $num    = 0;
        $sql    = "select amount from wh_shipping_orderdetail where shipOrderId = '{$orderid}' AND sku = '{$sku}'";
        $data   = $this->dbconn->fetch_first($sql);
        $num    = $data['amount'];
        if(!empty($data)){
            $num = $data['amount'];
        }
        return $num;
    }   
}

?>