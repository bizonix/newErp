<?php
/*
 * ebay message 查询运输方式详情
 */
class EbayCarrierQueryModel extends ShippingQueryModel {
    
    /*
     * 名称映射 将系统名称映射成运输方式系统的名称 
     */
    public function carrierNameReflect($name){
        $result = FALSE;
        switch ($name){
            case '中国邮政挂号':
                $result = '中国邮政';
                break;
            case 'EUB':
                $result = 'EMS';
                break;
            case 'EMS':
                $result = 'EMS';
                break;
            case 'FedEx':
                $result = 'FEDEX';
                break;
            case 'DHL':
                $result = 'DHL';
                break;
            case 'UPS Ground':
                $result = 'UPS';
                break;
            case 'USPS':
                $result = '美国邮政';
                break;
            case '新加坡小包挂号':
                $result = '新加坡邮政';
                break;
            case '德国邮政挂号':
                $result = '德国邮政';
                break;
        }
        return $result;
    }
}

?>