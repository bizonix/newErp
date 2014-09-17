<?php
/**
 *�ṩ���Ϻ�Mem���(pc_goods_combine)
 * memcache��Ϊpc_goods_$sku
 * ZQT 2013-10-21
 */

define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/pc.valsun.cn/crontab/');
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";


initPcGoodsCombine();
$ret = getPcGoodsCombineInfo('CB001569_G');
var_dump($ret);


function initPcGoodsCombine() {

    $omAvailableAct = new OmAvailableAct();
    $where = 'WHERE is_delete=0 ';
    $pcGoodsCombineList = $omAvailableAct->act_getTNameList('pc_goods_combine', '*', $where);
    if(count($pcGoodsCombineList) == 0){
        return false;
    }

    $expire   = 0;
    global $memc_obj;
    foreach ($pcGoodsCombineList as $value) {
        $combineSku      = $value['combineSku'];
        $relationArr = array();
        $relationList = $omAvailableAct->act_getTNameList('pc_sku_combine_relation', 'sku,count', "WHERE combineSku='$combineSku'");
        if(!empty($relationList)){
            foreach($relationList as $tmp){
                $relationArr[] = $tmp;
            }
        }
        $value['detail'] = $relationArr;
        $key     = 'pc_goods_combine_'.$combineSku;
        $ret     = $memc_obj->set_extral($key, $value, $expire);
    	echo 'key='.$key."\n";
        if(!$ret) {
    		echo $key;
            echo '写入缓存出错,请查看mencache相关信息';
            return false;
        }
    }
    return true;
}



function getPcGoodsCombineInfo($combineSku){
    $combineSku = isset($combineSku) ? trim($combineSku) : '';
    if($combineSku == '') {
        return '';
    }
    $key = 'pc_goods_combine_'.$combineSku;
    global $memc_obj;
    $ret = $memc_obj->get_extral($key);
    if($ret) {
        return $ret;
    } else {
        echo '缓存中无此数据！';

    }
    return '';
}




?>
