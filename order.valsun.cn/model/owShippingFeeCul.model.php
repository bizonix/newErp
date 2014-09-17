<?php
class OwShippingFeeCulModel {
    
    public static $errMsg   = '';
    private $dbConn         = null;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * usps 固定运费查询
     */
    public function uspsShipfee_fix($l, $w, $h, $serviceName,$size){
        $sql    = "select * from usps_service where serviceName='$serviceName' and size='$size'";//echo $sql;
        $result = $this->dbConn->query($sql);
        $rules  = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        $returnData = FALSE;
        foreach ($rules as $rval){
            if ($this->chcekSize(array($l,$w,$h), $rval)) {
            	$returnData    = $rval['shipfee'];
            	continue;
            }
        }
        return $returnData;
    }
    
    /*
     * usps 套餐A
     */
    public function usps_serviceA($l, $w, $h, $weidht){
        if ($weidht <=6.79) {                                                                       //重量需小于6.79
            $usps_A     = $this->uspsShipfee_filter($l,$w, $h, 'A');                                //条件检测
            if ($usps_A) {
                $usps_sf_a = $this->uspsCulculateShipfee('A');
                return $usps_sf_a;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /*
     * usps 套餐B
     */
    public function usps_serviceB($l, $w, $h, $weidht){
        if ($weidht <= 9) {
            $result     = $this->uspsShipfee_filter($l,$w, $h, 'B');               //条件检测
            if ($result) {
                $usps_sf = $this->uspsCulculateShipfee('B');
                return $usps_sf;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /*
     * usps 套餐C
    */
    public function usps_serviceC($l, $w, $h, $weidht){
        if ($weidht <= 11.3) {
            $result     = $this->uspsShipfee_filter($l,$w, $h, 'C');               //条件检测
            if ($result) {
                $usps_sf = $this->uspsCulculateShipfee('C');
                return $usps_sf;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /*
     * 计算GROUND RESIDENTIAL
    */
    public  function ground_re($kg, $region=6){
        global $dbcon;
        $pound  = $this->Kg2Pound($kg);
        if ($region == 1) {
        	$region = 2;
        }
        $sql    = "select * from grre_region where firstweight<$pound and secondweight>=$pound and zone='$region'";//echo $sql;
        $row    = $this->dbConn->fetch_first($sql);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }
    
    /*
     * 计算GROUND COMMERCIAL
    */
    public function ground_co($kg, $region=6){
        $pound  = $this->Kg2Pound($kg);
        if ($region == 1) {
            $region = 2;
        }
        $sql    = "select * from grco_region where firstweight<$pound and secondweight>=$pound and zone='$region'";//echo $sql;
        $row    = $this->dbConn->fetch_first($sql);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }
    
    /*
     * 计算 SurePost
    */
    public function SurePost($l, $w, $h, $kg, $zone=6){
        if (($l * $w * $h) > 42600) {
            return false;
        }
        $pound  = $this->Kg2Pound($kg);
        $sql    = "select * from surepost_region where firstweight<$pound and secondweight>=$pound and zone='$zone'";//echo $sql;
        $row    = $this->dbConn->fetch_first($sql);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }
    
    /*
     * ups运费计算
     */
    public function upsShipfee($weight, $zone=6){
        $weight_lbs = $this->Kg2Pound($weight);
        $weight_lbs = ceil($weight_lbs);
        $sql = "SELECT cost FROM ow_ups_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";//echo $sql;exit;
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
            return $row ['cost'];
        } else {
            return FALSE;
        }
    }
    
    /*
     * usps 通用运费
     */
    public function uspsGeneral($weight, $zone=6){
        $weight_oz = $this->Kg2Oz($weight);
        $weight_oz = ceil($weight_oz);
        $weight_lbs = $this->Kg2Pound($weight);
        $weight_lbs = ceil($weight_lbs);
        if ($weight_oz <= 13) {                                                                         //13盎司一下按这个算
            $sql    = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_oz}' AND unit = 'oz'";
            $row    = $this->dbConn->fetch_first($sql);
            if ($row) {
                return $row['cost'];
            } else {
                return FALSE;
            }
        } else {
            $sql    = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";//echo $sql;exit;
            $row    = $this->dbConn->fetch_first($sql);
            if ($row) {
            	return $row['cost'];
            } else {
                return FALSE;
            }
        }
    }
    
    
    
    /*
     * 克转换成盎司
     */
    public function Kg2Oz($kg){
        return $kg * 35.27396194958;
    }
    
    /*
     * 千克转磅
    */
    public function Kg2Pound($kg){
        return $kg*2.2046226218488;
    }
    
    /*
     * 计算usps运费 区域价位
    */
    private function uspsShipfee_filter($l, $w, $h, $serviceName){
        $sql    = "select * from usps_service where serviceName='$serviceName'";
        $rules      = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        $returnData = FALSE;
        foreach ($rules as $rval){
            if ($this->chcekSize(array($l,$w,$h), $rval)) {
                $returnData    = true;
                continue;
            }
        }
        return $returnData;
    }
    
    /*
     * usps 计算对应套餐某个区域的运费
    */
    private function uspsCulculateShipfee($serviceName, $region=6){
        $sql    = "select * from usps_region where zone='$region' and serviceName='$serviceName'";//echo $sql;
        $row    = $this->dbConn->fetch_first($sql);
        return  $shipfee    = isset($row['shipfee']) ? $row['shipfee'] : FALSE;
    }
    
    /*
     *获取燃油附加费率
     */
    function getShipSettings(){
        $sql    = "select * from ow_shipsettings ";
        $query  = $this->dbConn->query($sql);
        $result = array();
        while ($row = mysql_fetch_assoc($query)){
            $result[$row['name']]  = $row['value'];
        }
        return $result;
    }
    
    /*
     * 对长宽高进行排列组合
     */
    private function AllPermutations($InArray, $InProcessedArray = array())
    {
        $ReturnArray = array();
        foreach($InArray as $Key=>$value)
        {
            $CopyArray = $InProcessedArray;
            $CopyArray[$Key] = $value;
            $TempArray = array_diff_key($InArray, $CopyArray);
            if (count($TempArray) == 0)
            {
                $ReturnArray[] = $CopyArray;
            }
            else
            {
                $ReturnArray = array_merge($ReturnArray, $this->AllPermutations($TempArray, $CopyArray));
            }
        }
        return $ReturnArray;
    }
    
    /*
     * check size
     */
    private function chcekSize($size, $size2){
        $sizelist   = $this->AllPermutations($size);
        foreach ($sizelist as $s){
            $s  = array_values($s);
            if ($s[0] <= $size2['length'] && $s[1] <= $size2['width'] && $s[2] <= $size2['height']) {
            	return true;
            }
        }
        return FALSE;
    }
    
    /*
     * 汇率转换 将美元转换为usd
     */
    public function translateUsd2Rmb($usd, $exchange){
        return $usd*$exchange;
    }
    
    /*
     * 转换标准运输方式名称
    */
    public function carrerMap($carrer){
        $returnData = '';
        if ($carrer == 'ground_re' || $carrer == 'ground_co') {
            $returnData    = 'UPS Ground';
        } elseif ($carrer == 'USPS') {
            $returnData     = 'USPS';
        } elseif ($carrer == 'SurePost'){
            $returnData  = 'SurePost';
        }
        return $returnData;
    }
    
    /*
     * 获得usps的扩展信息
     * $skuList     sku列表   array('sku1'=>n, 'sku2'=>m...)
     * $weight      重量 KG 
     */
    public function generateUSPSExtensionInfo($skuList, $weight){
        $returnData = array('mailclass'=>'', 'packageType'=>'');
        $weight_oz  = $this->Kg2Oz($weight);                    //重量转盎司
        
        $packageType    = 'Parcel';
        if ($weight_oz <= 13) {
        	$mailclass = 'First';
        	$packageType   = '';
        } else {
            $mailclass  = 'Priority';
        }
        
        $letterSkus = $this->getLorFSku('Letter');
        $flatSkus   = $this->getLorFSku('Flat');
        $pureSku    = array_keys($skuList);
//         print_r($pureSku);
        $letter_count   = count( array_intersect($pureSku, $letterSkus) );
        $flat_count     = count( array_intersect($pureSku, $flatSkus) );
        $skuCount       = count($skuList);
//         echo $letter_count, "==",$flat_count;
        /*
         * 若果包含letter料号 或 flat料号 但是多于一种料号 则需要拆分订单
         */
        if ( ($skuCount>1) && ( ($letter_count>0) || ($flat_count >0) ) ){
            self::$errMsg   = '包含flat/letter料号的多料号订单';
            return FALSE;
        }
        
        if ( ($letter_count > 0) && (1 == $skuCount) ) {                                           //使用letter发货
        	$packageType   = 'Letter';
        } else if ( ($flat_count>0) && (1 == $skuCount) ) {
        	$packageType   = 'Flat';
        }
        
        $returnData = array('mailclass'=>$mailclass, 'packageType'=>$packageType);
        return $returnData;
    }
    
    /*
     * 获取 USPS letter/flat Sku列表
     */
    public function getLorFSku($type){
        $returnData = array(); 
        $sql        = "select * from ow_usps_transtype where packType='$type'";
        $query      = $this->dbConn->query($sql);
        while ($row = mysql_fetch_assoc($query)){
            $returnData[]   = $row['sku'];
        }
        return $returnData;
    }
    
    
}


?>