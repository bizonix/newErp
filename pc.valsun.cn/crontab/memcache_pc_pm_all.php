<?php
/**
 *提供单料号Mem数据(pc_goods)
 * memcache键为pc_goods_$sku
 * ZQT 2013-10-21
 */

define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/pc.valsun.cn/crontab/');
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";


initPcPm();
$ret = getPcPmInfo();
var_dump($ret);


function initPcPm() {

    $omAvailableAct = new OmAvailableAct();
    $where = 'WHERE is_delete=0 ';
    $pcPmList = $omAvailableAct->act_getTNameList('pc_packing_material', '*', $where);
    if(count($pcPmList) == 0){
        return false;
    }

    $expire   = 0;
    global $memc_obj;
    $key     = 'pc_pm_all';
    $ret     = $memc_obj->set_extral($key, $pcPmList, $expire);
	echo 'key='.$key."\n";
    if(!$ret) {
		echo $key;
        echo '写入缓存出错,请查看mencache相关信息';
        return false;
    }
    return true;
}



function getPcPmInfo(){
    global $memc_obj;
    $ret = $memc_obj->get_extral('pc_pm_all');
    if($ret) {
        return $ret;
    } else {
        echo '缓存中无此数据！';

    }
    return '';
}




?>
