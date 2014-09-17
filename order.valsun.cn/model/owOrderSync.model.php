<?php
/*
 * 海外仓数据同步
 */
class OwOrderSyncModel  {
    public static $errMsg     = '';
    public static $errCode    = 0;
    private $dbConn;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 推送数据到海外仓
     * $submitData = array(
     *   'orderInfo'    => $row,
     *   'userInfo'     => $UserInfo,
     *   'transInfo'    => $transInfo,
     *   'skuList'      => $skuList
     *  );
     */
    public function pushPrintedOrderToUsWh($orderId, $submitData){
        $owOrderMg      = new OwOrderManageModel();
        $dataJson       = json_encode($submitData);
        $signature      = md5($dataJson);                                                          //数据摘要
        $postData       = array(
                'digitalSignature'=>$signature,
                'data'            => $dataJson
        );
        $url    = "http://oversea.valsun.cn/api/sync_printed_order.php";
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                                                        //设置链接
        curl_setopt($ch, CURLOPT_POST, 1);                                                          //设置为post
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                                                //设置是否返回信息
        $response   = curl_exec($ch);                                                               //接收返回信息
        if (FALSE === $response) {
//             $errCode    = curl_errno($ch);
            $errString  = curl_error($ch);
            self::$errMsg   = $errString;
            return FALSE;
        }
//         echo $response;
//         print_r($response);exit;
        $unsData    = json_decode($response, TRUE);
        if (json_last_error() !== JSON_ERROR_NONE) {                                                //json数据出错
            self::$errMsg   = "返回数据格式出错!";
            return FALSE;
        }
        
        $status = $unsData['status'];
        $errMsg = $unsData['msg'];
        if ($status != 'Success') {
            self::$errMsg   = "同步错误! CODE: $status === $errMsg ";
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    
}
