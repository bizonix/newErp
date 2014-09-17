<?php
/**
 * WarehouseManagementAct
 * 
 * @package 仓库系统  仓库调拨Action
 * @author Gary
 * @copyright 2014
 * @access public
 */
class WhGoodsAssignAct extends Auth{
	static  $errCode   =	0;
	static  $errMsg    =	'';
    private $whGoods   =    ''; 
    
    //public function __construct(){
//    }
    

    /**
     * WhGoodsAssignAct::addList()
     * 新增调拨单列表
     * @return
     */
    function act_addList($outStoreId = '', $inStoreId = '', $createdUid='', $sku='', $num='', $ids= ''){
        if(!$ids){
            $outStoreId     =   intval($_POST['outStoreId']);
            $inStoreId      =   intval($_POST['inStoreId']);
            $createdUid     =   intval($_POST['createdUid']);
            $sku            =   $_POST['sku'] ? $_POST['sku'] : array();
            $num            =   $_POST['num'] ? $_POST['num'] : array();
        }
        if($outStoreId == $inStoreId){
            self::$errCode     =   401;
            self::$errMsg      =   '转出仓库和转入仓库不能相同!';
            return FALSE;
            exit;
        }
        if( !$outStoreId || !$inStoreId || !is_array($sku) || !is_array($num) || (count($sku) != count($num)) ){
            self::$errCode     =   402;
            self::$errMsg      =   '数据不完整!';
            return FALSE;
            exit;
        }
        $assignNumber   =   self::buildAssignNumber(); //获取调拨单编号
        //$whGoods        =   new WhGoodsAssignModel();
        TransactionBaseModel :: begin();
        
        //调拨单表中插入数据并获取插入id
        $goodsAssignId  =   WhGoodsAssignModel::addAssignList($assignNumber, $outStoreId, $inStoreId, time(), time(), $createdUid);
        if($goodsAssignId == FALSE){
            self::$errCode     =   403;
            self::$errMsg      =   '生成调拨单失败!';
            TransactionBaseModel :: rollback();
            return FALSE;
            exit;
        }
        
        $listDetails    =   self::buildDetails($sku, $num, $outStoreId, $goodsAssignId); //拼接调拨单明数据
        if($listDetails === FALSE){
            self::$errCode     =   404;
            self::$errMsg      =   '参数不正确,生成调拨明细失败!';
            TransactionBaseModel :: rollback();
            return FALSE;
            exit;
        }
        
        $info           =   WhGoodsAssignModel::addAssignListDetail($listDetails);
        if($info == FALSE){
            self::$errCode     =   405;
            self::$errMsg      =   '生成调拨明细表失败!';
            TransactionBaseModel :: rollback();
            return FALSE;
            exit;
        }
        if($ids){
            $info       =   WhGoodsAssignModel::insertAssignOrder($ids, $goodsAssignId);
            if($info == FALSE){
                self::$errCode     =   406;
                self::$errMsg      =   '插入订单关系表失败!';
                TransactionBaseModel :: rollback();
                return FALSE;
                exit;
            }
        }
        TransactionBaseModel :: commit();
		self::$errCode        =   200;
        self::$errMsg         =   '添加成功!';
        return TRUE;
    }
    
    /**
     * WhGoodsAssignAct::act_editList()
     * 调拨单修改
     * @return void
     */
    public function act_editList(){
        $outStoreId     =   intval($_POST['outStoreId']);
        $inStoreId      =   intval($_POST['inStoreId']);
        $createdUid     =   intval($_POST['createdUid']);
        $sku            =   $_POST['sku'] ? $_POST['sku'] : array();
        $num            =   $_POST['num'] ? $_POST['num'] : array();
        $id             =   intval(trim($_POST['id'])) ? intval(trim($_POST['id'])) : 0;
        //print_r($id);exit;
        if($outStoreId == $inStoreId){
            self::$errCode     =   401;
            self::$errMsg      =   '转出仓库和转入仓库不能相同!';
            return $res;
            exit;
        }
        
        if( !$outStoreId || !$inStoreId || !is_array($sku) || !is_array($num) || (count($sku) != count($num)) || !$id){
            self::$errCode     =   402;
            self::$errMsg      =   '数据不完整!';
            return $res;
            exit;
        }
        //$assignNumber          =   self::buildAssignNumber(); //获取调拨单编号
        
        TransactionBaseModel :: begin();
        
        //删除原来的调拨明细
        $where                 =    array('goodsAssignId'=>$id);
        $update                =    array('is_delete'=>1);
        $info                  =    WhGoodsAssignModel::updateAssignDetail($where, $update);
        if(!$info){
            self::$errCode     =   403;
            self::$errMsg      =   '调拨明细清除不成功!调拨单修改失败！';
            TransactionBaseModel :: rollback();
            return $res;
            exit;
        }
        
        //更新调拨单信息
        $where                 =   array('id'=>$id);
        $update                =   array('inStoreId'=>$inStoreId, 'outStoreId'=>$outStoreId);
        $info                  =   WhGoodsAssignModel::updateAssignListStatus($where, $update);
        
        $listDetails           =   self::buildDetails($sku, $num, $outStoreId, $id); //拼接调拨单明数据
        if($listDetails === FALSE){
            self::$errCode     =   404;
            self::$errMsg      =   '参数不正确,生成调拨明细失败!';
            TransactionBaseModel :: rollback();
            return $res;
            exit;
        }
        
        $info           =   WhGoodsAssignModel::addAssignListDetail($listDetails);
        if($info == FALSE){
            self::$errCode     =   405;
            self::$errMsg      =   '生成调拨明细表失败!';
            TransactionBaseModel :: rollback();
            return $res;
            exit;
        }
        
        TransactionBaseModel :: commit();
		self::$errCode         =   200;
        self::$errMsg          =   '修改成功!';
        return $res;
    }
    
    /**
     * WhGoodsAssignAct::printAssignList()
     * 标记调拨单为待配货
     * @return void
     */
    public function act_printAssignList(){
        $ids            =   is_array($_POST['ids']) ? $_POST['ids'] : array();
        if(!empty($ids)){
            $idNums     =   count($ids);
            $ids        =   implode(',', $ids);
            $where1     =   " and a.id in($ids) and a.status = 100";
            $assignList1=   WhGoodsAssignModel::getRowAllNumber($where1);  //获取选定的调拨单中所有待处理状态的总数
            $where2     =   " and a.id in($ids) and a.status = 103"; 
            $assignList2=   WhGoodsAssignModel::getRowAllNumber($where2);  //获取选定的调拨单中所有待打印调拨出库单的总数
            if( ($assignList1 != $idNums) && ($assignList2 != $idNums)){
                self::$errCode  =   401;
                self::$errMsg   =   '请选择待处理或待打印出库单状态的调拨单!';
                return FALSE;
            }
            $status     =   $assignList1 == $idNums ? 101 : 104;    //下级状态对应编号
            $info       =   WhGoodsAssignModel::updateAssignStatus($ids, $status, $_SESSION['userId']);
            if($info){
                self::$errCode  =   200;
                self::$errMsg   =   '状态更新成功!';
                return TRUE;
            }else{
                self::$errCode  =   402;
                self::$errMsg   =   '状态更新失败!';
                return FALSE;
            }
        }else{  
                self::$errCode  =   403;
                self::$errMsg   =   '数据不正确!';
                return FALSE;
        }
    }
  
    /**
     * WhGoodsAssignAct::bulidAssignNumber()
     * 生成调拨单编号 
     * @return void
     */
    function buildAssignNumber(){
        //$whGoods    =   new WhGoodsAssignModel();
        //print_r($this->whGoods->getMaxNumber());exit;
        $maxNumber  =   WhGoodsAssignModel::getMaxNumber();
        $maxNumber  =   $maxNumber ? substr($maxNumber, 2) : 0;
        $maxNumber  +=  1;
        $maxNumber  =   str_pad($maxNumber, 8, 0, STR_PAD_LEFT);
        return 'AN'.$maxNumber;
    }
    

    /**
     * WhGoodsAssignAct::buildDetails()
     * 格式化调拨明细表中的sku及数量 
     * @param array $sku
     * @param array $num
     * @param int $outStoreId
     * @param int $goodsAssignId
     * @return void
     */
    function buildDetails($sku, $num, $outStoreId, $goodsAssignId){
        if( !is_array($sku) || !is_array($num) || !intval($outStoreId) ){
            return false;
        }
        $string     =   '';
        //$whGoods    =   new WhGoodsAssignModel();
        foreach($sku as $k => $v){
            $location   =   WhGoodsAssignModel::getSkuLocation($v, $outStoreId);
            //print_r($location);exit;
            $string     .=  "('{$goodsAssignId}', '$v', '$num[$k]', '{$location}'), ";
        }
        return trim($string, ', ');
    }
    
    /**
     * WhGoodsAssignAct::act_checkSku()
     * 增加调拨单时检测sku是否存在 
     * @return void
     */
    public function act_checkSku(){
        $sku        =   trim($_POST['sku']) ? trim($_POST['sku']) : '';
        $outStoreId =   intval(trim($_POST['outStoreId'])) ? intval(trim($_POST['outStoreId'])) : '';
        if( !$sku || !$outStoreId){
            self::$errCode  = '001';
            self::$errMsg   = '参数不完整';
            return FALSE;
        }
        $info       =   WhGoodsAssignModel::checkSku($sku, $outStoreId);  //获取对应仓库的仓位库存
        if(empty($info)){
            self::$errCode  = '002';
            self::$errMsg   = '转出仓库没有该料号的仓位库存!';
            return FALSE;
        }
        $actualStock=   whShelfModel::selectSkuNums($sku, $outStoreId); //获取对应仓库的实际料号库存
        if(empty($actualStock)){
            self::$errCode  = '003';
            self::$errMsg   = '转出仓库没有该料号的总库存!';
            return FALSE;
        }
        self::$errCode  = '200';
        return TRUE;  
    }
    
    /**
     * WhGoodsAssignAct::act_checkSkuNum()
     * 检测sku数量是否在库存之内
     * @return void
     */
    public function act_checkSkuNum(){
        $sku        =   trim($_POST['sku']) ? trim($_POST['sku']) : '';
        $outStoreId =   intval(trim($_POST['outStoreId'])) ? intval(trim($_POST['outStoreId'])) : '';
        $num        =   intval(trim($_POST['num'])) ? intval(trim($_POST['num'])) : '';
        if( !$sku || !$outStoreId || !$num ){
            self::$errCode  = '001';
            self::$errMsg   = '参数不完整';
            return FALSE;
        }
        $actualStock        =   whShelfModel::selectSkuNums($sku, $outStoreId); //获取对应仓库的总库存
        //var_dump($actualStock);exit;
        if(!empty($actualStock)){
            if($actualStock['actualStock'] >= $num){
                self::$errCode  = '200';
                return TRUE;
            }else{
                self::$errCode  = '002';
                self::$errMsg   = '该仓库料号库存不足!';
                return FALSE;
            }
        }else{
            self::$errCode  = '003';
            self::$errMsg   = '转出仓库不存在该料号!';
            return FALSE;
        }  
    }
    
    /**
     * WhGoodsAssignAct::act_getErpOrders()
     * 获取ERP推送过来的B仓订单并生成调拨单
     * 
     */
    public function act_getErpOrders(){
        //$ids        =   $_GET['ids'] ? trim($_GET['ids']) : 0;
        $orderinfo  =   trim($_GET['orderinfo']) ? json_decode(trim($_GET['orderinfo']), TRUE) : 0; //订单信息
        $user       =   trim($_GET['createUser']) ? trim($_GET['createUser']) : 0;
        if( !$orderinfo || !$user){
            self::$errCode  = '001';
            self::$errMsg   = 'Param is NULL!';
            return FALSE;
        }
        $outStoreId =   2;
        $inStoreId  =   1;
        $createUid  =   getUserIdByName($user);
        $createUid  =   $createUid ? $createUid : 0;
        $ebay_ids   =   array();
        $realSkuInfo=   array();
        foreach($orderinfo as $key=>$val){
            $is_exist   =   WhGoodsAssignModel::getAssignOrderById($key); //查看该订单是否已有记录
            if(empty($is_exist)){
                $ebay_ids[] =   $key;
                foreach($val as $skuinfo){
                    $sku    =   $skuinfo['sku'];
                    $num    =   $skuinfo['ebay_amount'];
                    if(isset($realSkuInfo[$sku])){
                        $realSkuInfo[$sku]  =   $realSkuInfo[$sku] + $num;
                    }else{
                        $realSkuInfo[$sku]  =   $num;
                    }
                }
            }
        }
        unset($orderinfo);
        if(empty($ebay_ids)){
            self::$errCode        =   200;
            self::$errMsg         =   '添加成功!';
            return true;
        }
        $ebay_ids   =   implode(',', $ebay_ids);
        //print_r($realSkuInfo);exit;
        $sku        =   array_keys($realSkuInfo);
        $num        =   array_values($realSkuInfo);
        $info       =   self::act_addList($outStoreId, $inStoreId, $createUid, $sku, $num, $ebay_ids);
        return $info;
    }
    
    /**
     * WhGoodsAssignAct::export_data()
     * 导出调拨数据 
     * @return void
     */
    public function export_data(){
        $ids    =   trim($_GET['ids']);
        if($ids){
            $assignList =   WhGoodsAssignModel::getAssignList(" and a.id in ($ids)", '', '', 'a.id');
            $assign_status    =   C('assign_status');
            //print_r($assign_status);exit;
            $name   =   'assignList'.date('Y-m-d').".xls";
            //$name   =   iconv('UTF-8', 'gb2312//ignore', $name);
            $excel  =   new ExportDataExcel('browser', $name);
            $excel->initialize();
            $tharr = array("调拨单号","SKU","产品名称","转出仓库","转入仓库","需求数量","配货数量",'出库复核数量', '接收数量', '生成人员', '生成时间', '调拨单状态', '状态变更时间');
            $excel->addRow($tharr);
            
            if(!empty($assignList)){
                foreach($assignList as $assign){
                    $outStore   =   WarehouseManagementModel::warehouseManagementModelList(" where id = {$assign['outStoreId']}");
                    $outStore   =   $outStore[0]['whName'];
                    $inStore    =   WarehouseManagementModel::warehouseManagementModelList(" where id = {$assign['inStoreId']}");
                    $inStore    =   $inStore[0]['whName'];
                    $maker      =   getUserNameById($assign['createUid']);
                    $make_date  =   date('Y-m-d H:i:s', $assign['createTime']);
                    $state_date =   date('Y-m-d H:i:s', $assign['statusTime']);
                    $status     =   $assign_status[$assign['status']];
                    
                    $details    =   WhGoodsAssignModel::getsAssignListDetail($assign['id']);
                    foreach($details as $k => $val){
                        $tdarr  =   array(
                                        $k == 0 ? $assign['assignNumber'] : '',
                                        $val['sku'],
                                        $val['goodsName'],
                                        $outStore,
                                        $inStore,
                                        $val['num'],
                                        $val['assignNum'],
                                        $val['outCheckNum'],
                                        $val['inCheckNum'],
                                        $k == 0 ? $maker : '',
                                        $k == 0 ? $make_date : '',
                                        $k == 0 ? $status : '',
                                        $k == 0 ? $state_date : ''
                                    );
                                    //print_r($tdarr);exit;
                        $excel->addRow($tdarr);
                    }
                }
            }
            $excel->finalize();
            exit;
        }
    }	
}
?>
