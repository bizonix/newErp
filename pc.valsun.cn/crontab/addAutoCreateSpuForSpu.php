<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

$tName = 'pc_goods';
$where = "WHERE is_delete=0";
$countCombine = OmAvailableModel::getTNameCount($tName, $where);
$per = 5000;
$page = ceil($countCombine/$per);
echo "totalcount = $countCombine";
echo "\n";
echo "totalpage = $page";
echo "\n";
for($i=0;$i<$page;$i++){
    echo "currentPage = $i";
    echo "\n";
    $start = $per*$i;
    $tName = 'pc_goods';
    $select = 'spu,purchaseId,goodsCreatedTime';
    $where = "WHERE is_delete=0 limit $start,$per";
    $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
    foreach($combineSpuList as $value){
        $combineSpu = $value['spu'];
        $combineUserId = $value['purchaseId'];
        $addTime = $value['goodsCreatedTime'];
        if(empty($combineSpu)){
            continue;
        }
        $tName = 'pc_auto_create_spu';
        $where = "WHERE spu='$combineSpu'";
        $count1 = OmAvailableModel::getTNameCount($tName, $where);
        if(!$count1){
            $dataAuto = array();
            $dataAuto['spu'] = $combineSpu;
            $dataAuto['purchaseId'] = $combineUserId;
            $dataAuto['createdTime'] = $addTime;
            $dataAuto['status'] = 2;
            $dataAuto['isSingSpu'] = 1;
            $dataAuto['prefix'] = substr($combineSpu,0,2);
            OmAvailableModel::addTNameRow2arr($tName, $dataAuto);
            echo "$combineSpu insert autoCreateSpu success\n";
        }else{
            echo "$combineSpu has exist autoCreateSpu\n";
        }
    }
}
?>
