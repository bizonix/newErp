<?php

/**
 * @author guanyongjun
 * @date 2013-06-14
 *
 */

class Sms{

    public static $uid = "Evelee";
    public static $pwd = "87886354";
    // var $mobile;
    // var $content;
    // var $autotime;

    public static function send_sms_get($mobile,$content,$autotime = "")
    {
        $uid	 = self::$uid;
		$pwd	 = self::$pwd;
        $content = urlencode(iconv("UTF-8","GB2312",$content));
        $url     = "http://service.winic.org/sys_port/gateway/?id=$uid&pwd=$pwd&to=$mobile&content=$content&time=$autotime";
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function send_sms_post($mobile, $content, $autotime = "")
    {
        $uid	 = self::$uid;
		$pwd	 = self::$pwd;
        $content = urlencode(iconv("UTF-8", "GB2312", $content));
        $url     = "http://service.winic.org/sys_port/gateway/?id=$uid&pwd=$pwd&to=$mobile&content=$content&time=$autotime";
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }
}

?>