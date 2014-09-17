<?php
/*
 *邮件推送时间设置
 */
class MailPushAlarmModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn         = NULL;
    const monday    = 1;
    const Tuesday   = 2;
    const Wednesday = 4;
    const Thursday  = 8;
    const Friday    = 16;
    const Saturday  = 32;
    const Sunday    = 64;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    
    /*
     * 生成权限代码数组
     */
    public function culDaysSetting($days){
        $finalInt   = 0;
        foreach ($days as $d){
            if ($d>0 && $d<8) {                             //必须是1-7直接的数
                $dayBin    = $this->dayNum2binNum($d);
            	$finalInt  = ($finalInt | $dayBin);
            }
        }
        return $finalInt;
    }
    
    /*
     * 更新一个账号的推送设置信息
     */
    public function updateSettings($data){
        $mode   = $data['mode'];
        $time   = $data['time'];
        $days   = $data['days'];
        $account= $data['account'];
        $setting    = $this->getSettings($account);
        $account    = mysql_real_escape_string($account);
        $time       = mysql_real_escape_string($time);
        
        if (FALSE === $setting) {                                   //还没有相关记录 则插入记录
        	$insetSql  = "insert into msg_mailPushAlarm (account, mode, time, days) values ('$account', '$mode', '$time', '$days')";
        	$insertQ   = $this->dbConn->query($insetSql);
        	if (FALSE === $insertQ) {
        		self::$errMsg = '写入设置信息出错!';
        		return FALSE;
        	}
        	return TRUE;
        } else {                                                    //更新记录
            $updateSql  = "update msg_mailPushAlarm set mode='$mode', time='$time', days='$days' where account='$account'";
            $updateQ    = $this->dbConn->query($updateSql);
            if (FALSE === $updateQ) {
                self::$errMsg = '写入设置信息出错!';
                return FALSE;
            }
            return TRUE;
        }
    }
    
    /*
     * 检测一个账号是否有存在过设置信息
     */
    public function getSettings($account){
        $account    = mysql_real_escape_string($account);
        $sql        = "select * from msg_mailPushAlarm where account='$account'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 星期数组转成二进制数组
     */
    public function dayNum2binNum($daynum){
        switch ($daynum){
            case 1:
                return self::monday;
            case 2:
                return self::Tuesday;
            case 3:
                return self::Wednesday;
            case 4:
                return self::Thursday;
            case 5:
                return self::Friday;
            case 6:
                return self::Saturday;
            case 7:
                return self::Sunday;
        }
    }
    
    /*
     * 获得一个账号的推送设置信息
     * 返回值
     * 没有设置信息 返回 false
     * 有设置信息 返回格式
     * array(
     *  'mode'=>0/1,
     *  'time'=>2014-02-05,
     *  'days'=>array(1,2,3,...)
     * )
     */
    public function getSettingInfo($account){
        $returnData = array('mode'=>0, 'time'=>'', 'days'=>array());
        $setting    = $this->getSettings($account);
        if (FALSE === $setting) {
        	return $returnData;
        }
        $returnData['mode'] = $setting['mode'];
        $returnData['time'] = $setting['time'];
        $days               = array();
        $settingDay         = intval($setting['days']);
        if ($settingDay & self::monday ) {
        	$days[]    = 1;
        }
        if ($settingDay & self::Tuesday) {
            $days[]    = 2;
        }
        if ($settingDay & self::Wednesday) {
            $days[]    = 3;
        }
        if ($settingDay & self::Thursday) {
            $days[]    = 4;
        }
        if ($settingDay & self::Friday) {
            $days[]    = 5;
        }
        if ($settingDay & self::Saturday) {
            $days[]    = 6;
        }
        if ($settingDay & self::Sunday) {
            $days[]    = 7;
        }
        $returnData['days'] = $days;
        return $returnData;
    }
    
    /*
     * 返回一个账号列表中当前可以发推送邮件的账号
     */
    public function decisionPushAccount($account){
        $pushAccount    = array();
        $timeStr        = date('Y-m-d', time());
        $currentDay     = date('w');                                    //当前是星期几 0-6 0代表星期天
        $currentDay     = self::phpDays2SysDays($currentDay);           //转换成系统表示格式 1-7的形式
        foreach ($account as $ac){
            $setInfo    = $this->getSettingInfo($ac);
            if ($setInfo['mode'] == 1) {                                //单次模式
            	if ($timeStr == $setInfo['time']) {                     //设定运行的时间和当前的时间一致
            		$pushAccount[]    = $ac;
            	}
            } else if ($setInfo['mode'] == 2) {                         //多次模式
            	if (in_array($currentDay, $setInfo['days'])) {
            		$pushAccount[]    = $ac;
            	}
            }
        }
        
        return $pushAccount;
    }
    
    /*
     * php 星期 到 本系统 星期映射
     */
    public static function phpDays2SysDays($phpDay){
        if ($phpDay == 0) {
        	return 7;
        } else {
            return $phpDay;
        }
    }
}
