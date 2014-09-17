<?php
/**
 *提供单料号Mem数据(pc_goods)
 * memcache键为pc_goods_$sku
 * ZQT 2013-10-21
 */

define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/pc.valsun.cn/crontab/');
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";


initPcGoodsCategory();
$ret = getPcCategoryInfo('470');
var_dump($ret);


function initPcGoodsCategory() {

    $omAvailableAct = new OmAvailableAct();
    $where = 'WHERE is_delete=0 ';
    $pcGoodsCategoryList = $omAvailableAct->act_getTNameList('pc_goods_category', '*', $where);
    if(count($pcGoodsCategoryList) == 0){
        return false;
    }

    $expire   = 0;
    global $memc_obj;
    foreach ($pcGoodsCategoryList as $value) {
        $id      = $value['id'];
        $key     = 'pc_goods_category_id_'.$id;
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



function getPcCategoryInfo($id){
    $id = isset($id) ? trim($id) : '';
    if($id == '') {
        return '';
    }
    $key = 'pc_goods_category_id_'.$id;
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
