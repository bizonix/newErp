<?php
/**
 *提供单料号Mem数据(pc_goods)
 * memcache键为pc_goods_$sku
 * ZQT 2013-10-21
 */

define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/pc.valsun.cn/crontab/');
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";


initPcGoodsCategorys();
$ret = getPcCategoryInfos();
var_dump($ret);


function initPcGoodsCategorys() {

    $omAvailableAct = new OmAvailableAct();
    $where = 'WHERE is_delete=0 ';
    $pcGoodsCategoryList = $omAvailableAct->act_getTNameList('pc_goods_category', '*', $where);
    if(count($pcGoodsCategoryList) == 0){
        return false;
    }

    $expire   = 0;
    global $memc_obj;
    $key     = 'pc_goods_category_all';
    $ret     = $memc_obj->set_extral($key, $pcGoodsCategoryList, $expire);
	echo 'key='.$key."\n";
    if(!$ret) {
		echo $key;
        echo '写入缓存出错,请查看mencache相关信息';
        return false;
    }
    return true;
}



function getPcCategoryInfos(){
    $key = 'pc_goods_category_all';
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
