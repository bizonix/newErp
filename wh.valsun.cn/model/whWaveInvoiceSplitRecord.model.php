<?php

/*
 * 发货单拆分记录表Model
 * ADD BY cxy 2014.8.13
 */
class WhWaveInvoiceSplitRecordModel extends WhBaseModel {
    /**
     * WhWaveInvoiceSplitRecordModel::getInvoiceSplitBySku()
     * 根据发货单号和SKU获取信息
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @param mixed $sku SKU
     * @return
     */
    public static function getInvoiceSplitBySku($shipOrderId,$sku){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from wh_wave_invoice_split_record where `oldShipOrderId` = '{$shipOrderId}' and sku ='{$sku}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
    /**
     * WhWaveInvoiceSplitRecordModel::getRealSkuByCombineSku()
     * 根据虚拟料号回去真实的SKU
     * @author cxy
     * @param mixed $combineSku
     * @return array
     */
    public static function getRealSkuByCombineSku($combineSku){
        self::initDB();
        $sql       = "select * from pc_sku_combine_relation where `combineSku` = '{$combineSku}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;  
    }
    /**
     * WhWaveInvoiceSplitRecordModel::selectInvoiceSplitByShipOrderId()
     * 根据发货单号查询拆分记录
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @param integer $isShortage 是否缺货
     * @return
     */
    public function selectInvoiceSplitByShipOrderId($shipOrderId,$isShortage = 1){
         self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from wh_wave_invoice_split_record where `oldShipOrderId` = '{$shipOrderId}' and is_delete = 0 and  isShortage= {$isShortage}";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;
    }

}    
 
 
 ?>