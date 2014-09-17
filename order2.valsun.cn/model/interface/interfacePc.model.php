<?php
/*
 *产品中心相关接口操作类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfacePcModel extends InterfaceModel {
	
	public function __construct(){
		parent::__construct();
	}
    
	/**
     * 获取单个sku信息
	 * @param string $sku 
	 * @return array
	 * @author lzx
     */
	public function getSkuInfo($sku){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['sku'] = $sku;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    /**
     * 获取单个sku重量，支持虚拟SKU
	 * @param string $sku 
	 * @return array
	 * @author lzx
     */
	public function getSkuWeight($sku){
	   $ret = $this->getSkuInfo($sku);
       if(empty($ret)) return '0';              //可能传递过来的根本不是一个SKU
       $skuInfo = $ret['skuInfo'];
       $returnWeight = 0;
       if(empty($skuInfo)) return '0';
       foreach($skuInfo as $value){
         $skuDetail = $value['skuDetail'];
         $amount = $value['amount'];
         $returnWeight += $skuDetail['goodsWeight'] * $amount;
       }
       return $returnWeight;
    }
    
	/**
     * 获取包材信息
	 * @return array
	 * @author lzx
     */
	public function getMaterList(){
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
     * 根据包材id获取包材信息
     * @param int $mid
	 * @return array
	 * @author lzx
     */
	public function getMaterInfoById($mid){
        $materlist = $this->key('id')->getMaterList();
        return isset($materlist[$mid]) ? $materlist[$mid] : false;
    }
    
    /**
     * 获取所有的料号转换记录数组
	 * @return array('old_sku'=>'new_sku','old_sku'=>'new_sku'，……)
	 * @author zqt
     */
	public function getSkuConversionArr(){
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
     * 根据SPU数组获取对应的SPU海关编码及税率信息
	 * @return array
	 * @author zqt
     */
	public function getHscodeInfoBySpuArr($spuArr){
        $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['spuArr'] = $spuArr;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
    /**
     * 根据SPU数组获取对应的SPU海关编码及税率信息
	 * @return array
	 * @author zqt
     */
	public function getSpecialPOTBySkuArr($skuArr){
        $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['skuArr'] = $skuArr;
		$result = callOpenSystem($conf);
        
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
}
?>