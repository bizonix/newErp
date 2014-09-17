<?php
//脚本参数检验
include_once dirname(__DIR__)."/common.php";
$orderObj = M('Order');
$orderIdsArr = $orderObj->getOrderIdByOrderStatus(M('StatusMenu')->getOrderStatusByStatusCode('ORDER_WAIT_SHIP','id'));//取得待发货的不是合并包裹子订单的有效订单
//$orderIdsArr = array('2041723');
$count = count($orderIdsArr);
$skuArr = array('SV004861','19493_W','SV001439_B','10168_B_S','3800_M','3800_S','SV004580_M','SV004580_L','SV004580_M','3236_W','TK0743','5169_B_XL','7368_BE_L','9718','9116_Y_M','SV002883','9116_CA_S','5169_BL_XL','4892','5169_PU_xL','SV003966_RR_100','9116_Y_S','SV003966_RR_130','5169_BL_L','2438','SV003966_RR_90','SV002437_W_L','5169_PU_L','SV006203_BL','SV003684_W','SV006203_P','8356','19186_B','SV006647_WRE','SV004959_W_XL','SV002437_RR_L','5487_LBL','SV006203_Y','5029_P','SV003227_GR_S','14586_G_S','8962','8357','9123_XL','5316_G_XL','5487_DBL','4815','SV003288','8989','9038_BR_M','8922','18724','19744_B','SV002767_G_L','19744_B','SV003651_1_P','19744_BR','SV003338_B','SV005361_BL_2','8781','SV001439_BL','SV005361_R_3','14595_S','SV005361_S_1','19744_R','3236_B','14593_L','SV002767_G_XL','SV005361_PU_3','15618_b_m','SV003639_B','SV002437_W_S','16904_O','SV005082_SB_L','19892_DGR_XXL','3167_B','SV004742_R_L','SV002437_G_M','2328','SV000353_R_90','1927','SV000353_R_80','11786_M','SV003966_RR_120','SV003966_R_130','18920_DGR','3748_W_XXL','SV001962_L','SV003966_RR_110','18790','SV003303_B_XL','7368_LGR_M','3748_AG_XXL','9038_BR_XL','3748_B_M','4207','3748_B_XXL','347','SV004766_G','19281_PU','18059_BL','6983','SV002437_G_S','SV004959_B_XL','SV002972_B_XL','SV004766_B','19875_Y_100','SV000137_R_XL','SV004959_B_M','3600_W_M','18059_Y','SV004959_B_L','19744_RR','SV005764','19744_R','19681_m','SV003126_B_XL','SV006203_W','14740','14586_Y_S','19744_BL','TK1166','8145_6','5488_BE','19875_Y_130','SV003126_B_M','8974','16714_BL_L','TK1192','5822','SV000967_BL','5696_B','5488_R','5696_GR','18874_C','2729','3877_GR','5488_C','18838_DGR_XL','151','3600_W_l','13218_L','SV006171_RR','TK1344','SV002972_W_XL','18920_C','9083_WRE','5100_B','SV002972_BL_XL','5041_B','TK0963','18138_NB','SV005105','TK1165','18837_R','1699','19175_B','7714_B','10245_BL','SV002118_BE_XXL','19088_K','SV003854_2','2573','44','19527','19699','4215','9358_B','7715_B','19280_B','9443_B','18920_W','19280_R','19525','9358_W','SV002601_R_S','TK1069','SV004091_B','SV003004_BM','9443_DGR','7714_L','6680','7715_CA','6681','7589_P','SV003600_B','SV003004_KM','7589_G','9443_C','18269_G','18201_W','SV002501_K_L','TK0920','SV002437_RR_S','SV003269_G_L','20095_8','SV003812_2137','SV000448','19278_R','SV002554_BL_M','TK0307','19526','3349_W','SV006097_SB_L','10982','SV003269_G_M','SV003966_R_90','SV005082_SB_S','20095_3','SV003966_R_120','SV003004_DGRM','6539','SV003966_R_100','TK0581','SV005082_W_S','SV003866_XL','18874_B','20095_7','1993','SV000071_Y','SV003812_2115','7692_C','TK0309','18874_BL','SV003966_R_110','SV005738_W','18874_R','20001_C','SV003966_P_100','8842','9777','SV004917_B_L','20007','SV000449','SV000071_G','9775','TK0580','SV000446_BL','9776','TK0883','19905_C','4138_A','TK0599','SV005960_1_90','SV003966_P_110','SV000071_BL','TK0579','SV000450','3685_K','SV000353_R_95','SV003966_P_90','7504_L','3533_GR','3533_BE','19278_P','54','5099_W','7504_XL','15507','11801','SV005828_Y','8066_B','SV004345_BR','5099_CA','8066_O','TK0361','SV005503_BL_130','SV005503_C_130','SV001689_O','SV001124_US','8764','SV000729_W_100','16489_G','8066_W','606','SV000729_W_80','SV002884','7364_R','7308_GO','6189','8066_PU','3612_P_L','SV006093_B_M','7254_s','1848','20201','8066_O','SV004512_BL_M','8066_W','6160','SV003565_L','TK0586','18821_BL','SV004665_P_S','SV000451','SV005248_B_M','SV004330_A_L','SV000448','SV000071_B','SV005503_BL_90');
//var_dump($orderIdsArr);exit;
echo "\n<====[".date('Y-m-d H:i:s')."]系统共有 $count 个订单信息\n";
foreach($orderIdsArr as $OmOrderId){
    $orderIdTmpArr = array();
    $orderIdTmpArr[] = $OmOrderId;
    $orderDetailData = $orderObj->getUnshippedOrderDetailById($orderIdTmpArr);
    if(empty($orderDetailData)){
        M('OrderModify')->updateData($OmOrderId,array('is_delete'=>1));
        echo "omOrderIdd:$OmOrderId 该订单明细为空，删除"."\n\n";
    }
    foreach($orderDetailData[$OmOrderId] as $detailArr){
        $updateArr = array();
        $updateArr['sku'] = $skuArr[rand(0, count($skuArr)-1)];
        if(M('OrderModify')->updateOrderDetail($detailArr['id'], $updateArr)){
            echo '订单详情id:'.$detailArr['id'].'的 detailId'.$detailArr['id'].' SKU更新成功，新SKU为：'.$updateArr['sku']."\n\n";
        }
        if(rand(0, 1) == 1){
            echo "随机事件发生，将添加一条新的detailId \n\n";
            unset($detailArr['id']);
            $detailArr['sku'] = $skuArr[rand(0, count($skuArr)-1)];
            $orderAddObj = M('OrderAdd');
            $orderAddObj->setInsertOrderId($OmOrderId);
            if($orderAddObj->insertOrderDetail($detailArr)){
                M('OrderModify')->updateData($OmOrderId,array('orderAttribute'=>3));
                echo '添加一条新的订单详情id:'.$orderAddObj->getInsertOrderDetailId().' 新SKU为：'.$detailArr['sku']."\n\n";
            }
        }                        
    }
}


//print_r($orderData);exit;

echo "\n<====[".date('Y-m-d H:i:s')."]系统【结束】, 本次共 $count 条数据\n";
################################## end 这里可以扩展时间分页  ##################################
?>