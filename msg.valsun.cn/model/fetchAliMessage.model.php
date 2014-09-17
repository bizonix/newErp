<?php
class FetchAliMessageModel extends CurlRequest{
    public static $errMsg       = '';
    public static $errCode      = 0;
    private $dbconn             = null;

    /*
     *构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->$dbconn  = $dbConn;
    }

    /*
     *获取速卖通订单留言
     *$para 参数信息
     */
    public function fetchOrderMessage($para){
    }
}
