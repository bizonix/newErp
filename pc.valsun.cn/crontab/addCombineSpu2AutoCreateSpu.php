<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟
//添加旧料号到autoCreateSpu表中


$tName = 'pc_goods_combine';
$select = '*';
$where = "WHERE is_delete=0 AND combineSpu like'B%' group by combineSpu";//类似B_001形式的combineSpu
$combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
foreach($combineSpuList as $value){
    $tName = 'pc_auto_create_spu';
    $where = "WHERE is_delete=0 AND spu='{$value['combineSpu']}'";
    $count = OmAvailableModel::getTNameCount($tName, $where);
    if(!$count){
        $dataArr = array();
        $dataArr['spu'] = $value['combineSpu'];
        $dataArr['purchaseId'] = !empty($value['combineUserId'])?$value['combineUserId']:0;
        $dataArr['createdTime'] = $value['addTime'];
        $dataArr['status'] = 2;
        $dataArr['prefix'] = 'B';
        $dataArr['isSingSpu'] = 2;
        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        echo "{$value['combineSpu']} 添加到autoCreateSpu表中成功 \n";
    }
}




?>
