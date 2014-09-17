<?php
/*
 * 管理海外仓料号信息
 */
class OwSkuInfoManageModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 获得一组sku的仓位信息
     * $skuList sku数组 array('sku1','sku2', ...)
     */
    public function getAsetOfSkusLocation($skuList){
        $skuList    = array_map('mysql_real_escape_string', $skuList);
        $returnData = array();
        foreach ($skuList as $sku){
            $sql    = "select position from ow_stock where sku='$sku'";
            $row    = $this->dbConn->fetch_first($sql);
            if ($row) {
            	$returnData[$sku]  = $row['position'];
            } else {
                $returnData[$sku]  = '';
            }
        }
        return $returnData;
    }
}

?>