<?php
/*
 * 运输方式查询类
 */
class ShippingQueryModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    protected $dbconn       = NULL;
    private $CarrerList      = array(
    	    '中国邮政',
            'EMS',
            'FEDEX',
            'DHL',
            'UPS',
            '美国邮政',
            '顺丰',
            '圆通',
            '申通',
            '韵达',
            '新加坡邮政',
            '德国邮政'
    );
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn->$dbconn;
    }
    
    /*
     * 获取发货状态
     * $trackSn     跟踪号
     * $carrier     运输方式简称
     * $lang        语言  en 表示英文 zh 表示中文
     */
    public function getShippingInfo($trackSn, $carrier, $lang){
        $langCode   = 10000;
        if ($lang == 'en') {
        	$langCode  = 1;
        } else {
            $langCode   = 10000;
        }
        $result = getOpenSysApi('trans.track.info.get', array('type'=>$carrier,'lan'=>$langCode, 'tid'=>$trackSn), 
                    'http://idc.gw.open.valsun.cn/router/rest?');
//         print_r($result);exit;
        if (isset($result['data'])) {
        	$result    = json_decode($result['data'], TRUE);
//         	print_r($result);
        	if ($result['ReturnValue'] == -1 ) {
        		$result   = FALSE;
        	}
        } else {
            $result     = FALSE;
        }
        
        return $result;
    }
    
    /*
     * 获取运输方式系统支持的运输方式
     */
    public function getSupportedCarrier(){
        $result = getOpenSysApi('trans.track.carrier.name.get', array('id'=>1), 'http://idc.gw.open.valsun.cn/router/rest?');
        return $result;
    }
    
    /*
     * 获得当前支持的运输方式列表
     */
    public function  getSurpportedCarrierList(){
        return $this->CarrerList;
    }
}

?>