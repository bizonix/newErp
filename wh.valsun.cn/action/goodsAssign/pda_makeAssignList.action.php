<?php
/**
 * Pda_makeAssignListAct
 * pda生成调拨单操作
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @access public
 */
class Pda_makeAssignListAct extends Auth{
    static $errCode =   0;
    static $errMsg  =   '';
    function __construct(){
        parent::__construct();
    }
    
    /**
     * Pda_makeAssignListAct::act_getSkuInfo()
     * 检测输入的调拨SKU信息
     * @return
     */
    public function act_getSkuInfo(){
        $sku    =   trim($_POST['sku']);
        if(!$sku){
            self::$errCode  =   '001';
            self::$errMsg   =   '请输入SKU';
            return FALSE;
        }
        $sku        =   get_goodsSn($sku);
        $checkSku   =   WhGoodsAssignModel::checkSku($sku, 1);
        if(empty($checkSku)){
            self::$errCode  =   '002';
            self::$errMsg   =   'A仓库没有该料号信息!';
            return FALSE;
        }
        self::$errCode  =   200;
        self::$errMsg   =   '请输入调拨数量';
        return $data[]    =   $sku; 
    }
    
    /**
     * Pda_makeAssignListAct::act_checkNum()
     * 检测输入的料号数量并提交
     * @return void
     */
    public function act_checkSkuNum(){
        $sku    =   trim($_POST['sku']);
        $sku    =   get_goodsSn($sku);
        $sku_num=   intval(trim($_POST['sku_num']));
        if(!$sku_num || !$sku){
            self::$errCode  =   '001';
            self::$errMsg   =   '信息不完整!';
            return FALSE;
        }
        
        if($sku_num <0 ){
            self::$errCode  =   '002';
            self::$errMsg   =   '调拨数量不能小于0!';
            return FALSE;
        }
        
        $actualStock        =   whShelfModel::selectSkuNums($sku, 1); //获取A仓库的总库存
        if($actualStock < $sku_num){
            self::$errCode  =   '003';
            self::$errMsg   =   '调拨数量不能该料号的实际库存!';
            return FALSE;
        }
        
        $location   =   WhGoodsAssignModel::getSkuLocation($sku, 1, $sku_num);
        if(!$location){
            self::$errCode  =   '004';
            self::$errMsg   =   '没有满足调拨数量的仓位!';
            return FALSE;
        }
        $res    =   WhGoodsAssignModel::getDetail('0', " and a.sku ='{$sku}'"); //检测是否有未生成调拨单的该料号信息
        if(empty($res)){
            $info   =   WhGoodsAssignModel::insertAssignDetail(0, $sku, $sku_num, $location); //插入调拨详情表
        }else{
            $nums   =   $res['num'] + $sku_num; //调拨数量总和
            $info   =   WhGoodsAssignModel::updateAssignDetail(array('id'=>$res['id']), array('num'=>$nums));
        }
        
        
        if($info){
            self::$errCode  =   '200';
            self::$errMsg   =   '请输入下一料号!';
            return TRUE;
        }else{
            self::$errCode  =   '005';
            self::$errMsg   =   '插入料号失败!';
            return FALSE;
        }
    }
    
    /**
     * Pda_makeAssignListAct::act_makeAssignList()
     * 生成调拨单编号并更新调拨明细表 
     * @return void
     */
    public function act_makeAssignList(){
        $res    =   WhGoodsAssignModel::getDetail('0', ''); //检测是否有未生成调拨单的料号信息
        if(empty($res)){
            self::$errCode  =   '001';
            self::$errMsg   =   '没有可以生成调拨单的料号信息!';
            return FALSE;
        }
        $outStoreId         =   1;
        $inStoreId          =   2;
        $createdUid         =   $_SESSION['userId'];    
        
        $whGoodsAssignAct   =   new WhGoodsAssignAct;
        $assignNumber       =   $whGoodsAssignAct->buildAssignNumber(); //获取调拨单编号

        TransactionBaseModel :: begin();
        
        //调拨单表中插入数据并获取插入id
        $goodsAssignId  =   WhGoodsAssignModel::addAssignList($assignNumber, $outStoreId, $inStoreId, time(), time(), $createdUid);
        if($goodsAssignId == FALSE){
            self::$errCode     =   002;
            self::$errMsg      =   '生成调拨单失败!';
            return FALSE;
        }
        
        $where  =   array('goodsAssignId'=>0, 'is_delete'=> 0);
        $update =   array('goodsAssignId'=>$goodsAssignId);
        $info   =   WhGoodsAssignModel::updateAssignDetail($where, $update);
        if($info){
            TransactionBaseModel :: commit();
            self::$errCode     =   '200';
            self::$errMsg      =   '生成调拨单【'.$assignNumber.'】!';
            return TRUE;
        }else{
            TransactionBaseModel :: commit();
            self::$errCode     =   '003';
            self::$errMsg      =   '生成调拨单失败!';
            return FALSE;
        }
        
        
    } 
}


?>