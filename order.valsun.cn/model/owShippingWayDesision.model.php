<?php
/*
 * 根据订单的详细信息 觉得运费
 */
class OwShippingWayDesisionModel {
    public static  $errMsg     = 0 ;
    public static $errCode    = '';
    private $dbConn     = NULL;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 根据订单详细信息 觉得订单运输方式
     * $skuList sku列表 array('sku1'=>num, 'sku2'=>num,...)
     * $weight  KG为单位
     * $outside 外观 array('L'=>length, 'W'=>width, 'H'=>height)
     * $zone    运输区域
     */
    public function chooseShippingWay($skuList, $weightP, $outside, $zone){
        
        $culModel   = new OwShippingFeeCulModel();
        
        $weight = $weightP;                                         //重量 KG
        $L      = $outside['L'];                                    //长 CM
        $W      = $outside['W'];                                    //宽 CM
        $H      = $outside['H'];                                    //高 CM
        $shipsetting    = $culModel->getShipSettings();
        $rate           = $shipsetting['firerate'];                 //燃油附加费
        $home           = $shipsetting['homeshipfee'];              //住宅运输费
        $exchange       = $shipsetting['usdexchange'];              //美元rmb汇率
        
        $result = array();                                          //可成立的运输方式列表    格式 array(运输方式名称=>运费)
        
        /*
         $usps_fix   = $culModel->uspsShipfee_fix($length,$width, $hight, 'fix', 'inside');                     //usps 固定运费
        if ($usps_fix) {
        $result['usps_fix']    = $usps_fix;
        }
        
        $usps_A     = $culModel->usps_serviceA($length,$width, $hight,$weight);
        if ($usps_A) {                                                                                         //usps 套餐A
        $result['usps_A']    = $usps_A;
        }
        
        $usps_B     = $culModel->usps_serviceB($length,$width, $hight, $weight);                               //usps 套餐B
        if ($usps_B) {
        $result['usps_B']    = $usps_B;
        }
        
        $usps_C     = $culModel->usps_serviceC($length,$width, $hight, $weight);
        if ($usps_C) {
        $result['usps_C']    = $usps_C;
        }
        */
        $ground_re  = $culModel->ground_re($weight, $zone);                                                    //GROUND RESIDENTIAL
        if ($ground_re) {
            $result['ground_re']    = ($ground_re+($ground_re*$rate));                                         //运费加燃油附加费 + 住宅运送费
        }
        
        $ground_co  = $culModel->ground_co($weight, $zone);                                                    //GROUND COMMERCIAL
        if ($ground_co) {
            $result['ground_co']    = ($ground_co+($ground_co*$rate)+$home);                                   //运费加燃油附加费
        }
        
        /*
         $SurePost   = $culModel->SurePost($length, $width, $hight, $weight, $zone);                            //SurePost运费
        if ($SurePost) {
        $result['SurePost']    = ($SurePost + ($SurePost*$rate));                                          //运费加燃油附加费
        }*/
        /*
         $ups        = $culModel->upsShipfee($weight);                                                          //ups运费计算
        if ($ups) {
        $result['UPS Ground']    = $ups + ($ups*$rate) + $home;
        }
        */
        $usps_gel   = $culModel->uspsGeneral($weight, $zone);                                                  //usps通用运费计算
        if ($usps_gel) {
            $result['USPS']    = $usps_gel;
        }
        
//         	print_r($result);exit;
        $mini  = array('ship'=>'', 'fee'=>10000);
        foreach ($result as $key=>$fee){
            if ($fee < $mini['fee']) {
                $mini['ship'] = $key;
                $mini['fee']  = $fee;
            }
        }
        // 	print_r($mini);
        // 	exit;
        /*
         if($weight_oz <= 13){//重量小于13盎司直接选USPS运输方式
        $getUspsCost = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_oz}' AND unit = 'oz'";
        
        $getUspsCost = $dbcon->execute($getUspsCost);
        $getUspsCost = $dbcon->fetch_one($getUspsCost);
        $shipCost    = $getUspsCost['cost'];
        $carrier     = 'USPS';
        }else{
        $getUspsCost = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
        $getUspsCost = $dbcon->execute($getUspsCost);
        $getUspsCost = $dbcon->fetch_one($getUspsCost);
        $uspsCost    = $getUspsCost['cost'];//USPS运费
        
        $getUpsCost  = "SELECT cost FROM ow_ups_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
        $getUpsCost  = $dbcon->execute($getUpsCost);
        $getUpsCost  = $dbcon->fetch_one($getUpsCost);
        $upsCost     = $getUpsCost['cost'];//UPS运费
        $upsCost     = $upsCost*(1+0.07) + 2.8; //添加 燃油附加费 和住宅配送费
        
        if($uspsCost <= $upsCost){//运费对比
        $shipCost = $uspsCost;
        $carrier  = 'USPS';
        }else{
        $shipCost = $upsCost;
        $carrier  = 'UPS Ground';
        }
        if(empty($uspsCost)){
        $shipCost = $upsCost;
        $carrier  = 'UPS Ground';
        }
        }
        */
        /*------------- 运输方式选择 -------------- */
        
        $carrier        = $culModel->carrerMap($mini['ship']);
        $shipCost       = $mini['fee'];
        $extensionInfo  = array();
        $returnData = array (                                                   //返回的数据格式
                'shippingCode' => '',                                           //运输方式代码
                'fee' => 0,                                                     //运费
                'extensionInfo' => $extensionInfo                               //扩展信息
        );
        
        if ($carrier == 'USPS') {                                               //如果是usps 则需要计算对应的 packageType 和 mailClass
        	$extensionInfo    = $culModel->generateUSPSExtensionInfo($skuList, $weight);
        	 if (FALSE === $extensionInfo) {                                           //不满足usps的发货条件
        	     self::$errMsg = OwShippingFeeCulModel::$errMsg;
        	 	return FALSE;
        	 }
        }
        
        if($shipCost != '' && $carrier != ''){
            $returnData['shippingCode'] = $carrier;
            $returnData['fee']          = $shipCost;
            $returnData['extensionInfo']  = $extensionInfo;
        }
        return $returnData;
    }
    
    /*
     * 计算一组sku在包装后的包裹的长宽高
     * 参数 $skuList $skuList结构  array{'sku1'=>数量, 'sku2'=>数量, ..., 'skuN'=>数量 }  需要包装的sku列表  改中不能包含组合料号
     * 长度 取sku列表中长度最长的值
     * 宽度 取sku列表中最宽的值
     * 高度 是全部sku的高度累加值
     * 返回值
     * array{
     *  'L'=>长,
     *  'W'=>宽,
     *  'H'=>高
     * }
     */
    public function culPackageLWH($skuList){
        //     print_r($skuList);
        $returnData = array('L'=>0,'W'=>0, 'H'=>0);
        foreach ($skuList as $sku=>$num){
            $tempSkuInfo    = $this->getGoodsInfo($sku, array('goodsLength', 'goodsWidth', 'goodsHeight'));
//                     print_r($tempSkuInfo);
            //         echo "---\n";
            $tempL          = isset($tempSkuInfo['goodsLength']) ? floatval($tempSkuInfo['goodsLength']) : 0;
            $tempW          = isset($tempSkuInfo['goodsWidth'])  ? floatval($tempSkuInfo['goodsWidth'])  : 0;
            $tempH          = isset($tempSkuInfo['goodsHeight']) ? floatval($tempSkuInfo['goodsHeight']) : 0;
            if ($returnData['L'] < $tempL){
                $returnData['L']    = $tempL;
            }
            if ($returnData['W'] < $tempW) {
                $returnData['W']    = $tempW;
            }
            $returnData['H']       += $tempH * $num;
        }
//             print_r($returnData);exit;
        return $returnData;
    }
    
    /*
     * 查询某个sku的基本信息
    * 参数 sku
    * fieldList  要查询的字段
    */
    public function getGoodsInfo($sku, $fieldList){
        $returnData     = array();
        $fieldListSql   = implode(', ', $fieldList);
        $sql            = "select $fieldListSql from pc_goods where sku = '$sku'";
        $row            = $this->dbConn->fetch_first($sql);
        if ($row) {
            $returnData    = $row;
        }
        return $row;
    }
    
}

?>