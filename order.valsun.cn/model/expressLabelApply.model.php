<?php
/*
 * 快递打印 超类 必须继承
 */
class  ExpressLabelApplyModel {
    public static  $errCode    = 0;
    public static  $errMsg     = '';
    protected $dbConn   = NULL;
    
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 过滤特殊字符
    */
    public function strreplace($str){
        $str = str_replace("<", "", $str);
        $str = str_replace(">", "", $str);
        $str = str_replace("#", "", $str);
        $str = str_replace("%", "", $str);
        $str = str_replace("&", "", $str);
        $str = str_replace("^", "", $str);
        $str = str_replace("*", "", $str);
        $str = str_replace(";", "", $str);
        $str = str_replace("\"", "", $str);
        return $str;
    }
    
    /*
     * 发送请求
     */
    public function sendRequest($postData, $url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        $response = curl_exec ($ch);
        if ($response === FALSE) {
            $this->errCode  = curl_errno($ch);
            $this->errMsg   = curl_error($ch);
        	return FALSE;
        } else {
            return $response;
        }
    }
    
    /*
     * 解析xml数据
     */
    public function parseXMLResult($xml){
        $result_obj = @simplexml_load_string($xml);
        if ($result_obj === FALSE) {
            $this->errCode  = 800;
            $this->errMsg   = 'xml解析失败!';
        	return TRUE;
        } else {
            return $result_obj;
        }
    }
    
    /*
     * 生成存储目录
     */
    public function generatePathStr($orderId, $dir){
        $orderId    = intval($orderId);
        /* $str        = sprintf("%06d", $orderId);
        $str        = substr($str, -6, 6);                                          //取后六位
        $part_1     = substr($str, 0, 2);
        $part_2     = substr($str, 2, 2);
        $part_3     = substr($str, 3, 2);
        if (!is_dir(EXP_SAVE_PATH.$part_1)) {
        	mkdir(EXP_SAVE_PATH.$part_1);
        }
        if (!is_dir(EXP_SAVE_PATH.$part_1."/$part_2")) {
            mkdir(EXP_SAVE_PATH.$part_1."/$part_2");
        }
        if (!is_dir(EXP_SAVE_PATH.$part_1."/$part_2"."/$part_3")) {
            mkdir(EXP_SAVE_PATH.$part_1."/$part_2"."/$part_3");
        }
        $path       = EXP_SAVE_PATH."$part_1/$part_2/$part_3"; */
        
        return EXP_SAVE_PATH.$dir;
    }
    
    /*
     * 存储图片
     */
    public function saveLabelPic($path, $content){
        $result = file_put_contents($path, $content);
        if (FALSE === $result) {
            $this->errCode  = 801;
            $this->errMsg   = '存储label文件出错!';
        	return FALSE;
        } else {
            return $result;
        }
    }
    
    /*
     * 千克转盎司
     */
    public function kg2ounce($kg){
        return $kg * 35.27396194958;
    }
    
    /*
     * 计算所在区域编码
     */
    public function getZoneCode($postCode){
        $postCode   = substr($postCode, 0, 3);                                                  //只需取邮编前3位数字
        $sql        = "SELECT zone FROM ow_zone_postcode WHERE zip_code like '%$postCode%'";
        $getZone    = $this->dbConn->fetch_first($sql);
        if (empty($getZone)) {
        	return FALSE;
        } else {
            return $getZone['zone'];
        }
    }
    
    /*
     * 运输方式简码映射到对应的ID
     */
    public function reflectCodeToId($transName){
        switch ($transName){
            case 'UPS Ground':
                return 46;
                break;
            case 'USPS':
                return 47;
                break;
            default:
                return FALSE;
        }
    }
    
    /*
     * 获得州名简称
     * $stateName           州名
     */
    public function getStateAbbreviationName($stateName){
        $stateName  = mysql_real_escape_string($stateName);
        $sql        = "select short from state_name_shortname where en='$stateName'";
//         echo $sql;exit;
        $row        = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row['short'];
        } else {
            return FALSE;
        }
    }
    
}
