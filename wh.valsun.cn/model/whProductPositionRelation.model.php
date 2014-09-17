<?php

/*
 * 仓位料号关系表Model
 * ADD BY cmf 2014.7.22
 */
class WhProductPositionRelationModel extends WhBaseModel {
	
    /**
     * WhProductPositionRelationModel::updateSkuScanNums()
     * 更新料号仓位关系表中的配货库存或者仓位库存
     * @param int $pId 产品id
     * @param int $positionId 仓位id
     * @param int $nums 更改的配货库存数量
     * @param int $type 1时为料号库存 2为配货库存 3两个字段全部更新
     * @author Gary
     * @return bool
     */
    public static function updateSkuScanNums($pId, $positionId, $nums, $type = 1){
    	self::initDB();
        $tablename  =   self::$tablename;
        $pId        =   intval($pId);
        $positionId =   intval($positionId);
        $nums       =   intval($nums);
        if( !$pId || !$positionId || !$nums || !in_array($type, array(1, 2, 3))){
            return FALSE;
        }
        if($type == 3){
            $sql	 =	"update {$tablename} set  nums = nums - {$nums}, scanNums=scanNums+{$nums} where pId = '{$pId}' and positionId = '{$positionId}' and is_delete = 0";
        }else{
            $field   =  $type == 1 ? 'nums' : 'scanNums';
    	    $sql	 =	"update {$tablename} set  {$field} = {$field} + {$nums} where pId = '{$pId}' and positionId = '{$positionId}' and is_delete = 0";    
        }
    	$query	 =	self::$dbConn->query($sql);
    	return $query;
    
    }
}
?>
