<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

$tName = 'pc_goods_combine';
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
    $tName = 'pc_goods_combine';
    $select = 'combineSpu,combineUserId,addTime';
    $where = "WHERE is_delete=0 limit $start,$per";
    $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
    foreach($combineSpuList as $value){
        $combineSpu = $value['combineSpu'];
        $combineUserId = $value['combineUserId'];
        $addTime = $value['addTime'];
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
            $dataAuto['isSingSpu'] = 2;
            if(strpos($combineSpu,'CB') === 0){
                $dataAuto['prefix'] = 'CB';
            }
            OmAvailableModel::addTNameRow2arr($tName, $dataAuto);
            echo "$combineSpu insert autoCreateSpu success\n";
        }else{
            echo "$combineSpu has exist autoCreateSpu\n";
        }
    }
}
?>
