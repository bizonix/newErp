<?php
/**
 * Pda_waveReceiveAct
 * 楼层分享
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_waveReceiveAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取配货单信息
	function act_checkWave(){
		$userId   =   $_SESSION['userId'];
		$wave     =   trim($_POST['wave']);
        $waveId   =   WhBaseModel::number_decode($wave);
        if(!$waveId){
            self::$errCode = "001";
			self::$errMsg  = "请扫描配货单号!";
			return FALSE;
        }
        $waveInfo   =   WhWaveInfoModel::get_wave_info('storey', $waveId); //获取配货单所有楼层
		if(empty($waveInfo)){
		  self::$errCode  =   '002';
          self::$errMsg   =   '配货单号'.$wave.'不存在!';
          return FALSE;
		}
        $record     =   WhWaveFloorReceiveRecordModel::select('id', array('userId'=>$userId, 'waveId'=>$waveId, 'is_delete'=>0));
        if(empty($record)){
            $data['waveId'] =   $waveId;
            $data['userId'] =   $userId;
            $data['time']   =   time();
            $msg    =   WhWaveFloorReceiveRecordModel::insert($data);
            if(!$msg){
                self::$errCode  =   '003';
                self::$errMsg   =   '插入接收记录失败!';
                return FALSE;
            }
        }
        $info   =   '该配货单需要配货楼层包括：<br />';
        $storeys=   explode(',', $waveInfo[0]['storey']);
        foreach($storeys as $v){
            $info   .=  $v.'楼<br />';
        }
        return $info;
	}
}


?>