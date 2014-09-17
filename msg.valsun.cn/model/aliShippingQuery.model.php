<?php
class AliShippingQueryModel extends ShippingQueryModel {
    
    /*
     * 名称映射 将系统名称映射成运输方式系统的名称
     */
    public function carrierNameReflect($name){
        $result = FALSE;
        switch ($name){
        	case 'CPAM':
        	    $result = '中国邮政';
        	    break;
        	case 'EMS':
        	    $result = 'EMS';
        	    break;
        	case 'UPS':
        	    $result = 'UPS';
        	    break;
        	case 'FEDEX IE':
        	    $result = 'FEDEX';
        	    break;
        	case 'DHL':
        	    $result = 'DHL';
        	    break;
        	case 'ePacket':
        	    $result = 'EMS';
        }
        return $result;
    }
    
}

?>