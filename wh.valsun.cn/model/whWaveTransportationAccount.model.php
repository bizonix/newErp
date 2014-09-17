<?php

/*
 * 运输方式和销售账号与打印面单关系表Model
 * ADD BY cxy 2014.8.6
 */
class WhWaveTransportationAccountModel extends WhBaseModel {
    
    /**
     * WhWaveTransportationAccountModel::select_account()
     *  根据运输方式ID和销售账号ID获取打印面单文件名
     * @author cxy
     * @param mixed $accountId 账号ID
     * @param mixed $transportId 运输方式ID
     * @return
     */
    public static function select_account($accountId,$transportId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `accountId` = '{$accountId}' and transportId = '{$transportId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
    
}    
 
 
 ?>