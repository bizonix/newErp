<?php
/**
 * Pda_contactPositionAct
 * 关联新旧仓位
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_contactPositionAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
    function act_check_position(){
        $position   =   trim($_POST['position']);
        $return     =   array();
        if($position){
            $positionInfo   =   WhPositionDistributionModel::get_position_info('pName', '', $position);
            if(empty($positionInfo)){
                self::$errCode  =   201;
                self::$errMsg   =   '没有该仓位信息!';
                return FALSE;
            }
            $positionId     =   $positionInfo[0]['pName'];
            $relation       =   WhOldPositionRelationModel::select(array('old_positionId'=>$positionId), 'id');
            if(!empty($relation)){
                self::$errCode  =   203;
                self::$errMsg   =   '该仓位已关联新仓位!';
                return FALSE;
            }
        }else{
            self::$errCode  =   202;
            self::$errMsg   =   '请输入旧仓位！';
            return FALSE;
        }
        self::$errCode  =   200;
        return $positionId;
    }
    
    function act_submitPosition(){
        $old_positionId     =   trim($_POST['old_positionId']);
        $new_position       =   trim($_POST['new_position']);
        if(!$old_positionId){
            self::$errCode  =   204;
            self::$errMsg   =   '请扫描旧仓位!';
            return FALSE;
        }
        if(!$new_position){
            self::$errCode  =   205;
            self::$errMsg   =   '请扫描新仓位！';
            return FALSE;
        }
        //$new_position   =   WhPositionDistributionModel::get_position_info('id', '', $new_position);
//        if(empty($new_position)){
//            self::$errCode  =   206;
//            self::$errMsg   =   '没有该新仓位信息！';
//            return FALSE;   
//        }
        $relation       =   WhOldPositionRelationModel::select(array('new_positionId'=>$new_position), 'id');
        if(!empty($relation)){
            self::$errCode  =   203;
            self::$errMsg   =   '该仓位已关联旧仓位!';
            return FALSE;
        }
        $insert_data        =   array(
                                    'old_positionId'    =>  $old_positionId,
                                    'new_positionId'    =>  $new_position
                                );
        $insert_id          =   WhOldPositionRelationModel::insert_data($insert_data);
        if($insert_id){
            self::$errCode  =   200;
            return $insert_id;
        }else{
            self::$errCode  =   207;
            self::$errMsg   =   '插入关系表失败!';
            return FALSE;
        }
    }
}


?>