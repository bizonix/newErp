<?php
/*
 *仓库系统相关接口操作类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfaceWhModel extends InterfaceModel {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 取消交易,仓库废弃订单
	 * @param int $orderid
	 * @param int $storeId
	 * @return 
	 * @author lzx
	 */
	public function discardShippingOrder($orderid, $storeId = 1){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['oidStr'] = $orderid;
		$conf['storeId'] = $storeId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}
	
	/**
     * 获取SKU实际库存
	 * @param array $skus array(storeid=>array(sku1, sku2))
	 * @return array
	 * @author lzx
     */
	public function getSkuStock($skus){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['skuArr'] = json_encode($skus);
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data['data'];
    }
	/**
     * 获取SKU可用库存
	 * @param array $skus array(storeid=>array(sku1, sku2))
	 * 参数类型array(仓库id=>array(sku1,sku2,sku3) 或者 sku), 仓库id 1是A仓 2是B仓  
	 * @return array
	 * @author andy
     */
	public function getSkuStockByArrNew($skus){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['skuArr'] = json_encode($skus);
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data['data'];
    }
	/**
	 * 获取订单下仓库配货记录
	 * @param int $orderId
	 * @return array
	 * @author lzx
	 */
	public function getOrderPickingInfo($orderId){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['orderId'] = $orderId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;	
	}
	
	/**
	 * 获取仓库配货记录
	 * @param int $orderId
	 * @param int $sku
	 * @return array
	 * @author lzx
	 */
	public function getOrderSkuPickingRecords($orderId, $sku){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['sku'] = $sku;
		$conf['orderId'] = $orderId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}
    
    /**
	 * 获取异常订单的配货信息，参数orderId（订单系统订单号）（wh）
	 * @param int $orderId
	 * @return array 返回料号 已配货成功为1,没有配货成功（没有配或者没配够）返回为0
	 * @author zqt
	 */
	public function getAbOrderInfo($orderId){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['orderId'] = $orderId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}
    
    //拉取异常订单接口，不用传参考，返回订单系统订单数组(wh)
	public function getAbOrderList(){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data['data'];
	}

    
    /**
	 * 根据SKU获取仓位
	 * @param sku 
     * @param storeId 默认为1，深圳A仓
	 * @return {"errCode":0,"errMsg":"","data":"[{\"pName\":\"B9002\"}]"} 
	 * @author zqt
	 */
	public function getSkuPosition($sku, $storeId=1){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['sku'] = $sku;
        $conf['storeId'] = $storeId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		$data = json_decode($data['data'],true);
		return $data;
	}
    
    //拆分订单接口，参数orderId（订单系统订单号）
	public function operateAbOrder($orderId){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['orderId'] = $orderId;
        $conf['calcWeight'] = $calcWeight;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    //获取入库接口（wh）
	public function getInRecords(){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);	
	}
    
    //获取出库接口（wh）
	public function getOutRecords(){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);	
	}

	/**
	 * 通过sku获取国内对应的仓库，支持多SKU查询
	 * @param array $skus
	 * @return array 
	 * @author lzx
	 */
	public function getSkuStores($skus){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['skuArr'] = json_encode($skus);
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    /**
	 * 订单废弃发货单接口
	 * @param string $oidStr 废弃发货单的订单号，用逗号隔开
     * @param int $storeId
	 * @return array 
	 * @author zqt
	 */
	public function ordersDiscard2Wh($oidStr, $storeId=1){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['oidStr'] = $oidStr;
        $conf['storeId'] = $storeId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
}
?>