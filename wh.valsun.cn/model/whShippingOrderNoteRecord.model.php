<?php
/**
 * WhShippingOrderNoteRecordModel
 * 发货单备注记录表
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class WhShippingOrderNoteRecordModel extends WhBaseModel {
	/**
	 * WhShippingNoteRecordModel::insert_note()
	 * 插入发货单备注信息 
	 * @param mixed $shipOrderId
	 * @param mixed $notesArr
	 * @return void
	 */
	public static function insert_note($shipOrderId, $notesArr){
        self::initDB();
        $shipOrderId    =   intval($shipOrderId);
        if(!$shipOrderId || empty($notesArr) || !is_array($notesArr)){
            return FALSE;
        }
        $data   =   '';
        foreach($notesArr as $note){
            $content=   mysql_real_escape_string($note['content']);
            $data   .=  "('{$shipOrderId}', '{$content}', '{$note['userId']}', '{$note['createdTime']}', '{$note['storeId']}', '{$note['noteTypeForWh']}', '{$note['is_delete']}'),";
        }
        $data   =   trim($data, ',');
        $sql    =   'insert into '.self::$tablename.' (shipOrderId, content, userId, createdTime, storeId, noteType, is_delete) values '.$data;
        //echo $sql;exit;
        $info   =   self::$dbConn->query($sql);
        return $info;
	}
    
    /**
     * WhShippingOrderNoteRecordModel::get_order_note_by_waveId()
     * 通过配货单Id获取配货备注 
     * @param mixed $waveId
     * @return void
     */
    public static function get_order_note_by_waveId($waveId){
        self::initDB();
        $waveId =   intval($waveId);
        $sql    =   'select a.content from wh_shipping_order_note_record a left join wh_wave_shipping_relation b
                        on a.shipOrderId = b.shipOrderId where b.waveId = '.$waveId.' and (noteType = "2" or noteType = "1,2") and b.is_delete = 0 and a.is_delete = 0';
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
}
?>
