<?php
include_once	WEB_PATH."model/notice.model.php";

if(!isset($_SESSION)){
    session_start();     
}
/**
 * 名称：NoticeAct
 * 功能：查询消息纪录 视图层
 * 版本：V 1.0
 * 日期：2013/10/09
 * 作者： Ren da hai
 * */
class NoticeAct {
	static $errCode	=	0;
	static $errMsg	=	""; 
       
   	public static function act_getEmailsPage($where, $perNum, $pa="", $lang='CN') {
		$total	= NoticeModel::getNoticeList($where, $limit='', 'nt_email');	
        $page	= new Page(count($total), $perNum, $pa, $lang);
		$list	= NoticeModel::getNoticeList($where, $page->limit, 'nt_email');
		$fpage	= $page->fpage();
		return array($list, $fpage, count($total));
	}
    
   	public static function act_getSMSPage($where, $perNum, $pa="", $lang='CN') {
		$total	= NoticeModel::getNoticeList($where, $limit='', 'nt_sms');	
        $page	= new Page(count($total), $perNum, $pa, $lang);
		$list	= NoticeModel::getNoticeList($where, $page->limit, 'nt_sms');
		$fpage	= $page->fpage();
		return array($list, $fpage, count($total));
	}
    
   	public static function act_delSMS() {   	    
        if(!empty($_GET['idArr'])) {
            $idStr				= implode(",",$_GET['idArr']);
            $where				= ' and id in ('.$idStr.')';
            $data["is_delete"] 	= 1;
            $result 			= NoticeModel::update($data, $where, 'nt_sms');
            return $result;       
        } 
	}
    
   	public  static function act_delEmail() {   	    
        if(!empty($_GET['idArr'])) {
            $idStr				= implode(",",$_GET['idArr']);
            $where				= ' and id in ('.$idStr.')';
            $data["is_delete"] 	= 1;
            $result 			= NoticeModel::update($data, $where, 'nt_email');
            if($result) {
	            self::$errCode 	= "001";
	            self::$errMsg 	= "删除成功";
	            return $result;
            }     
            self::$errCode 		= "066";
            self::$errMsg 		= "删除失败";
        } 
	}   
}

?>