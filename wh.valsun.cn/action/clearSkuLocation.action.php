<?php
/**
 * clearSkuLocation
 */
class clearSkuLocationAct extends Auth{
	static  $errCode   =	0;
	static  $errMsg    =	'';  
    //public function __construct(){
//    }

    /**
     * WhGoodsAssignAct::process_sku()
     * 处理停售料号
     * @return
     */
    function process_sku($skus){
        $log_file   =   'clearSkuLocation/'.date('Y-m-d').'.txt';
        $skus   =   array_filter($skus);  //处理料号数组
        if(empty($skus)){
            return FALSE;
        }
        $num_arr    =   array(); //库存不为空料号
        $is_sale    =   array(); //非停售料号
        $sucess     =   array(); //清空成功料号
        $fail       =   array(); //清空失败料号
        //print_r($skus);exit;
        foreach($skus as $sku){
            $goods_count    =   CommonModel::getGoodsCount($sku); //获取旧ERP库存
            
            if($goods_count !== FALSE && $goods_count !=0){ //库存非零情况
                $num_arr[$sku] = $goods_count;
                continue;
            }
            $sku_info       =   packageCheckModel::selectSku($sku);  //获取料号信息
            if(!in_array($sku_info[0]['goodsStatus'], array(2, 3))){
                $is_sale[]  =   $sku;
                continue;  
            }
            TransactionBaseModel::begin();
            //清空料号仓位
            $info   =   whShelfModel::clearSkuLocation($sku_info[0]['id']);
            $date   =   date('Y-m-d H:i:s');
            if($info == TRUE){
                $log_info      = sprintf("料号：%s, 时间：%s, 信息:%s \r\n", $sku, $date, '新系统仓位清空成功');
                write_log($log_file, $log_info);
            }else{
                $log_info      = sprintf("料号：%s, 时间：%s, 信息:%s \r\n", $sku, $date, '新系统仓位清空失败');
                write_log($log_file, $log_info);
                $fail[] =   $sku;
                continue;
            }
            
            //同步清除老ERP仓位
            $info   =   CommonModel::clearSkuLocation($sku); //接口
            if($info['errCode'] == 200){
                $log_info      = sprintf("料号：%s, 时间：%s, 信息:%s,返回值：%s \r\n", $sku, $date, '老ERP仓位清空成功', 
                                            is_array($info) ? json_encode($info) : $info);
                write_log($log_file, $log_info);
                $sucess[]      = $sku;
            }else{
                $log_info      = sprintf("料号：%s, 时间：%s, 信息:%s,返回值：%s \r\n", $sku, $date, '老ERP仓位清空失败', 
                                       is_array($info) ? json_encode($info) : $info);
                write_log($log_file, $log_info);
                $fail[]        = $sku;
                TransactionBaseModel::rollback();
                continue;
            }
            TransactionBaseModel::commit();
        }
        return array('num_arr'=>$num_arr, 'is_sale'=>$is_sale, 'sucess'=>$sucess, 'fail'=>$fail);
    }
}
?>
