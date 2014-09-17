<?php

/*
 * 发货单明细表Model
 * ADD BY cmf 2014.7.22
 */
class PcGoodsModel extends WhBaseModel {
    
    /**
     * PcGoodsModel::get_sku_info()
     * 查询料号信息
     * @param array|string $select 查询字段
     * @param array $skuArr 查询的料号集合
     * @return
     */
    public static function get_sku_info($select, $skuArr){
    	self::initDB();
        $select     =   trim(array2select($select));
        //$skuArr     =   trim(array2select($skuArr));
        //$skuArr     =   str_replace('`', '\'', $skuArr);
        $skuArr     =   implode("','", $skuArr);
        if(!$select || !$skuArr){
            return FALSE;
        }
        $sql    =   "select $select from pc_goods where sku in ('$skuArr') and is_delete = 0";
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
    	return $res;
    }
}
?>
