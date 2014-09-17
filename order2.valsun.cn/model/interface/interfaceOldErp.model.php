<?php
/*
 *仓库系统相关接口操作类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfaceOldErpModel extends InterfaceModel {

	public function __construct(){
		parent::__construct();
	}

    /**
     * 获取旧系统订单信息(oldErp)
     * @param $scantime:发货时间
     * @param $ebay_account:账号
     * @param $ebay_status:状态（非必填）
	 * @return array
	 * @author zqt
     */
	public function getERPorderinfo($scantime,$ebay_account,$ebay_status=''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['scantime'] = $scantime;
        $conf['ebay_account'] = $ebay_account;
        if($ebay_status){
			$conf['ebay_status'] = $ebay_status;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data['data'];
	}

     /**
     * 将订单信息插入到老系统，POST到开放系统(oldErp)
     * @param $orderData:订单及其相关数据
	 * @return array
	 * @author zqt
     */
	public function orderErpInsertorder($orderData){
        $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['orderArr'] = json_encode($orderData);
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}

     /**
     * 从旧ERP中获取SKU的库存信息(oldErp)
     * @param $sku:SKU
	 * @return array
	 * @author zqt
     */
	public function getSkuCountWithOldErp($sku){
        $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['goods_sn'] = $sku;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}

    /**
     * 获取旧系统总待发货数量（oldErp）
     * @param $sku:SKU
	 * @return array
	 * @author zqt
     */
	public function	getpartsaleandnosendall($sku){
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
     * 更新旧系统中的 ebay_sku_statistics 数据（oldErp）
     * @param $sku:SKU
     * @param $data:信息数据
	 * @return array
	 * @author zqt
     */
	public function	updateSkuStatistics($sku,$data){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['sku'] = $sku;
        $conf['data'] = $data;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		if($data['res_code'] == 200){
			return true;
		}
		return false;
	}

    /**
     * 获取旧系统待发货数量(不包含超大订单审核通过的) （oldErp）
     * @param $sku:SKU
	 * @return array
	 * @author zqt
     */
	public function	getsaleandnosendall($sku){
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
     * 获取旧系统包含超大订单审核通过占用待发货数量，拦截数量（oldErp）
     * @param $sku:SKU
	 * @return array
	 * @author zqt
     */
	public function	get_partsalenosend($sku){
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
     * 同步旧系统的账号表到新系统（按照 id replace），并也会replace旧系统的om_account表(oldErp)
	 * @return array
	 * @author zqt
     */
	public function updateOmAccount() {
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
        if(is_array($data) && !empty($data)){
			foreach($data as $value){
				OmAvailableModel::replaceTNameRow2arr('om_account', $value);
			}
			echo 'success';
		}else{
			echo 'false';
		}
		exit;
	}

    /**
     * 同步老系统订单状态接口 POST（oldErp）
     * @param $orderId:订单编号
     * @param $ebay_status:状态
     * @param $final_status:最终状态
	 * @return array
	 * @author zqt
     */
    public function ordererpupdateStatus($orderId,$ebay_status,$final_status){
        $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['ebay_id'] = $orderId;
        $conf['ebay_status'] = $ebay_status;
        $conf['final_status'] = $final_status;
        $conf['truename'] = get_usernamebyid(get_userid());
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data['data'];
	}

    /**
     * 同步老系统文件夹权限（oldErp）
     * @param $moveFolders:可以移动的文件夹状态数组
     * @param $nameCn:用户中文名
	 * @return array
	 * @author zqt
     */
	public function erpSyncMovefolders($moveFolders,$nameCn){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['movefolders'] = $moveFolders;
        $conf['cnName'] = $nameCn;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}

    /**
     * 同步老系统平台增删改 POST（oldErp）
     * @param $new_platform:新平台
     * @param $handle:操作名称（insert,update,delete等）
     * @param $old_platform:旧平台
	 * @return array
	 * @author zqt
     */
	public function erpSyncPlatform($new_platform,$handle,$old_platform=''){
	    $conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['new_platform'] = $new_platform;
        $conf['old_platform'] = $old_platform;
        $conf['handle'] = $handle;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $data;
	}






}
?>