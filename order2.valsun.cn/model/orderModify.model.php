<?php
/*
 * 名称：OrderModifyMod
 * 功能：订单编辑
 * 版本：v 1.0
 * 日期：2014/06/25
 * 作者：zqt
*/
class OrderModifyModel extends CommonModel{
	
	public function __construct(){
		parent::__construct();
	}
    
    /**
     * 改变订单的状态（批量订单号） 在unshiped表中
     * @param  array $ids     订单号数组
     * @param  int   $type    二级状态
     * @param  int   $status  一级状态
     * @return bool
     * @author lzx
     */
    public function updateOrderStatusById($ids, $type, $status){
    	$ids = array_filter(array_map('intval', format_array($ids)));
        $type = intval($type);
        $status = intval($status);
        return $this->sql("UPDATE ".C('DB_PREFIX')."unshipped_order SET orderStatus='{$status}',orderType='{$type}' WHERE id IN (".implode(',', $ids).")")->update();
    }
    
	/**
     * 修改订单的运输方式（批量订单号） 在unshiped表中
     * @param  array $ids         订单号数组
     * @param  int   $transportId 运输方式ID
     * @return bool
     * @author zqt
     */
    public function updateOrderShipInfoById($ids, $transportId){
    	$ids = array_filter(array_map('intval', format_array($ids)));
        $transportId = intval($transportId);
        return $this->sql("UPDATE ".C('DB_PREFIX')."unshipped_order SET transportId='{$transportId}' WHERE id IN (".implode(',', $ids).")")->update();
    }
    
    /**
     * 修改订单的用户联系信息（单个订单） 在unshiped表中
     * @param  int   $id          订单号
     * @param  array $userInfoArr 用户信息数组
     * @return bool
     * @author zqt
     */
    public function updateOrderUserContactById($id, $userInfoArr){
        $table = C('DB_PREFIX').'unshipped_order_userInfo';
		$fdata = $this->formatUpdateField($table, $userInfoArr);
		if ($fdata === false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
    	$id = intval($id);
        $rs = $this->sql("UPDATE ".$table." SET ".array2sql($userInfoArr)." WHERE omOrderId=$id")->update();
        if($rs){
            $sql = $this->getLastRunSql();
            M('orderLog')->orderOperatorLog($sql,'修改订单的用户联系信息',$id);
        }
        return $rs;
    }
    
    /**
     * 修改订单的运费（单个订单） 在unshiped表中
     * @param  int     $id         订单号
     * @param  decimal $shipingFee 用户信息数组
     * @return bool
     * @author zqt
     */
    public function updateOrderShipingFeeById($id, $actualShipping){
    	$id = intval($id);
        return $this->sql("UPDATE ".C('DB_PREFIX')."unshipped_order SET actualShipping='$actualShipping' WHERE id=$id")->update();
    }
    
    /**
     * 添加订单跟踪号
     * @param  int     $omOrderId   订单号
     * @param  string  $tracknumber 跟踪号
     * @return bool
     * @author zqt
     */
    public function insertOrderTracknumber($omOrderId, $tracknumber){
    	$omOrderId = intval($omOrderId);
        $tracknumberArr = array();
        $tracknumberArr['omOrderId']   = $omOrderId;
        $tracknumberArr['tracknumber'] = $tracknumber;
        $tracknumberArr['addUser']     = get_userid();
        $tracknumberArr['createdTime'] = time();
        return $this->sql("INSERT ".C('DB_PREFIX')."order_tracknumber SET ".array2sql($tracknumberArr))->insert();
    }
    
    /**
     * 修改订单跟踪号 (需要在 om_order_tracknumber 表中添加 id)
     * @param  int     $id          备注表记录id
     * @param  string  $tracknumber 跟踪号
     * @return bool
     * @author zqt
     */
    public function updateOrderTracknumber($id, $tracknumber){
    	$id = intval($id);
        return $this->sql("UPDATE ".C('DB_PREFIX')."order_tracknumber SET tracknumber='$tracknumber' WHERE id=$id")->update();
    }
    
    /**
     * 修改订单明细
     * @param  int     $id             订单明细id
     * @param  array   $orderDetailArr 订单明细信息数组
     * @return bool
     * @author zqt
     */
    public function updateOrderDetail($id, $orderDetailArr){
        $table = C('DB_PREFIX').'unshipped_order_detail';
		$fdata = $this->formatUpdateField($table, $orderDetailArr);
		if ($fdata === false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
        $id = intval($id);
    	//echo "UPDATE ".$table." SET ".array2sql($orderDetailArr)." WHERE id=$id";
    	return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailArr)." WHERE id=$id")->update();
    }

    /**
     * @param $platformId            platformId id
     * @param $orderDetailExtend     订单扩展明细信息数组
     */
    public function updateOrderDetailExtend($platformId,$orderDetailExtend){
        $suffix = M('Platform')->getSuffixByPlatform($platformId);
        $detailExtensionMethod = 'updateOrderDetailExtension'.ucfirst($suffix);
        if(!method_exists($this, $detailExtensionMethod)){
            self::$errMsg['103'] = $detailExtensionMethod.'扩展表不存在,请联系管理员';
        }
        return $this->$detailExtensionMethod($orderDetailExtend);
    }

    public function updateOrderDetailExtensionAliexpress($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_aliexpress';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function updateOrderDetailExtensionAmazon($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_amazon';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function updateOrderDetailExtensionDomestic($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_domestic';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function updateOrderDetailExtensionEbay($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_ebay';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function updateOrderDetailExtensionNewegg($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_newegg';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function updateOrderDetailExtensionTmall($orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_tmall';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    /**
     * 添加订单备注
     * @param  int     $omOrderId           订单号
     * @param  string  $content             备注
     * @param  string  $noteTypeForWh       留言的类型  1特殊包装 2特殊配货 1,2两种都是 其他待扩展
     * @param  int     $storeId             仓库Id,默认为1
     * @return bool
     * @author zqt
     */
    public function insertOrderNote($omOrderId, $content,$noteTypeForWh, $storeId=1){
    	$omOrderId = intval($omOrderId);
        $noteArr = array();
        $noteArr['omOrderId']       = $omOrderId;
        $noteArr['content']         = addslashes($content);
        $noteArr['noteTypeForWh']   = $noteTypeForWh;
        $noteArr['userId']          = get_userid();
        $noteArr['createdTime']     = time();
        $noteArr['storeId']         = $storeId;
        //$noteArr['isPrint']         = $isPrint;
        $table = C('DB_PREFIX').'order_notes';
		$fdata = $this->formatInsertField($table, $noteArr);
		if ($fdata === false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
        return $this->sql("INSERT ".$table." SET ".array2sql($noteArr))->insert();
    }
    
    /**
     * 修改订单备注
     * @param  int     $id       订单备注id
     * @param  string  $content  备注
     * @param  int     $storeId  仓库Id,默认为1
     * @param  int     $isPrint  是否打印在标签上，默认为0，不打印，1为打印
     * @return bool
     * @author zqt
     */
    public function updateOrderNote($id, $content, $storeId=1, $isPrint=0){
        $id = intval($id);
        $noteArr = array();
        $noteArr['content'] = $content;
        $noteArr['storeId'] = $storeId;
        $noteArr['isPrint'] = $isPrint;
        $table = C('DB_PREFIX').'order_notes';
		$fdata = $this->formatUpdateField($table, $noteArr);
		if ($fdata === false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
    	return $this->sql("UPDATE ".$table." SET ".array2sql($fdata)." WHERE id=$id")->update();
    }
    
    /**
     * 通用更新update表
     * @param number $id 订单id
     * @param array  $data 需要更新的数据
     * @see CommonModel::updateData()
     */
    public function updateData($id,$data){
    	$table = C('DB_PREFIX').'unshipped_order';
    	$fdata = $this->formatUpdateField($table, $data);
    	if ($fdata === false){
    		self::$errMsg = $this->validatemsg;
    		return false;
    	}
        $rs = "UPDATE ".$table." SET ".array2sql($fdata)." WHERE id = '{$id}' AND is_delete = 0";
        $this->sql($rs)->update();
        if($rs){
            $sql = $this->getLastRunSql();
            M('orderLog')->orderOperatorLog($sql,'修改订单表',$id);
        }
    	return $rs;
    }
    
    /**
     * 更新发货表
     * @param number $id 订单id
     * @param array  $data 需要更新的数据
     */
    public function updateShippedOrer($id,$data){
    	$table = C('DB_PREFIX').'shipped_order';
    	$fdata = $this->formatUpdateField($table, $data);
    	if ($fdata === false){
    		self::$errMsg = $this->validatemsg;
    		return false;
    	}
    	return $this->sql("UPDATE ".$table." SET ".array2sql($fdata)." WHERE id = '{$id}' AND is_delete = 0 ")->update();
    }
    
    /**
     * 更新om_mark_shipping表
     * @param number $id
     * @param array $data
     * @param array $where
     * @return boolean
     * @author czq
     */
    public function updateMarkOrder($id,$data,$where){
    	$table = C('DB_PREFIX').'mark_shipping';
    	$fdata = $this->formatUpdateField($table, $data);
    	if ($fdata === false){
    		self::$errMsg = $this->validatemsg;
    		return false;
    	}
    	$where = " where omOrderId = '{$id}'";
    	if(!empty($where)){
    		foreach($where as $key=>$value){
    			$where .= " AND {$key} = '{$value}' ";
    		}
    	}
    	return $this->sql("UPDATE ".$table." SET ".array2sql($fdata)." $where");
    }
	/**
     * 更新订单表信息
     * @param number $id
     * @param array $data
     * @param array $where
     * @return boolean
     * @author andy
     */
    public function updateOrderInfo($id,$data,$where=array()){
    	$table = C('DB_PREFIX').'unshipped_order';
    	$fdata = $this->formatUpdateField($table, $data);
    	if ($fdata === false){
    		self::$errMsg = $this->validatemsg;
    		return false;
    	}
    	$where_str = " where id = '{$id}'";
    	if(!empty($where)){
    		foreach($where as $key=>$value){
    			$where_str .= " AND {$key} = '{$value}' ";
    		}
    	}
    	return $this->sql("UPDATE ".$table." SET ".array2sql($fdata)." $where_str")->update();
    }
    
    public function getOrderLogs($omOrderId){
        $table      = C('DB_PREFIX').'order_logs';
        $logList    = $this->sql("SELECT * FROM {$table} WHERE omOrderId = {$omOrderId} ORDER BY id DESC")->limit('*')->select();
        return $logList;
    }

    public function InsertOrderDetail($orderDetail){
        $table = C('DB_PREFIX').'unshipped_order_detail';
        $fdata = $this->formatInsertField($table, $orderDetail);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;
            return false;
        }
        $this->sql("INSERT ".$table." SET ".array2sql($fdata))->insert();
        return $this->getLastInsertId();
    }

    public function InsertOrderDetailExtend($platformId,$detailId,$orderDetailExtend){
        $suffix = M('Platform')->getSuffixByPlatform($platformId);
        $detailExtensionMethod = 'InsertOrderDetailExtension'.ucfirst($suffix);
        if(!method_exists($this, $detailExtensionMethod)){
            self::$errMsg['103'] = $detailExtensionMethod.'扩展表不存在,请联系管理员';
        }
        $this->$detailExtensionMethod($detailId,$orderDetailExtend);
    }

    public function InsertOrderDetailExtensionAmazon($detailId,$orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_amazon';
        $orderDetailExtend['omOrderdetailId'] = $detailId;
        $orderDetailExtend['itemURL'] = ' ';
        $orderDetailExtend['giftMessageText'] = ' ';
        $orderDetailExtend['giftWrapLevel'] = ' ';
        $orderDetailExtend['giftWrapPrice'] = 0;
        $orderDetailExtend['giftWrapTax'] = ' ';
        $orderDetailExtend['itemTax'] = ' ';
        $orderDetailExtend['shippingTax'] = ' ';
        $orderDetailExtend['shippingPrice'] = ' ';
        $orderDetailExtend['shippingDiscount'] = ' ';
        $orderDetailExtend['promotionDiscount'] = 0;
        $orderDetailExtend['promotionIds'] = ' ';
        $orderDetailExtend['invoiceRequirement'] = ' ';
        $orderDetailExtend['buyerSelectedInvoiceCategory'] = ' ';
        $orderDetailExtend['invoiceTitle'] = ' ';
        $orderDetailExtend['invoiceInformation'] = ' ';
        $orderDetailExtend['conditionId'] = 'New';
        $orderDetailExtend['conditionSubtypeId'] = 'New';
        $orderDetailExtend['conditionNote'] = 'not found';

        $fdata = $this->formatInsertField($table, $orderDetailExtend);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;
            return false;
        }

        return $this->sql("INSERT ".$table." SET ".array2sql($fdata))->insert();
    }

    public function InsertDetailExtensionDomestic($detailId,$orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_domestic';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function InsertOrderDetailExtensionEbay($detailId,$orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_ebay';

        $orderDetailExtend['omOrderdetailId'] = $detailId;
        $orderDetailExtend['transactionID'] = '360863697815';
        $orderDetailExtend['itemURL'] = '0';
        $orderDetailExtend['conditionID'] = '1000';
        $orderDetailExtend['conditionDisplayName'] = 'Brand New';
        $orderDetailExtend['finalValueFeeCurrency'] = 'GBP';
        $orderDetailExtend['finalValueFee'] = '0.59';
        $orderDetailExtend['handlingFee'] = '0.59';

        $fdata = $this->formatInsertField($table, $orderDetailExtend);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;
            return false;
        }

        echo "INSERT ".$table." SET ".array2sql($fdata);
        //return $this->sql("INSERT ".$table." SET ".array2sql($fdata))->insert();
    }

    public function InsertDetailExtensionNewegg($detailId,$orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_newegg';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    public function InsertDetailExtensionTmall($detailId,$orderDetailExtend){
        $table = C('DB_PREFIX').'unshipped_order_detail_extension_tmall';
        $omOrderdetailId = $orderDetailExtend['omOrderdetailId'];
        unset($orderDetailExtend['omOrderdetailId']);
        return $this->sql("UPDATE ".$table." SET ".array2sql($orderDetailExtend)." WHERE omOrderdetailId=$omOrderdetailId")->update();
    }

    /**
     * @param $detailId
     * 删除订单详细
     */
    public function delOrderDetail($detailId){
            $table = C('DB_PREFIX').'unshipped_order_detail';
            return $this->sql("UPDATE ".$table." SET is_delete = 1 WHERE id = {$detailId}")->update();
    }
}
?>