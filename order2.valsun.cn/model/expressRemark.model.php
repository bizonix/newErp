<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 14-8-15
 */
class ExpressRemarkModel extends CommonModel{
    private $_orderid = 0;

    public function __construct(){
        parent::__construct();
    }

    public function getRemarkById($id){
        $table      = C('DB_PREFIX').'declaration_content';
        $sql = " SELECT * from {$table} WHERE omOrderId = '{$id}'";
        return $this->sql($sql)->select();
    }

    public function updateRemark($id,$data,$spu){
        $table      = C('DB_PREFIX').'declaration_content';
        $fdata      = $this->formatUpdateField($table,$data);
        if ($fdata === false){
            self::$errMsg = $this->validatemsg;
            return false;
        }

        $where = " where omOrderId = '{$id}' AND spu = '{$spu}' ";
        return $this->sql("UPDATE ".$table." SET ".array2sql($fdata)." $where")->update();
    }

    /**
     * @param $omOrderId
     * @param $spu
     * @return bool
     * 检测这个订单的SPU是否在数据库存在
     */
    public function isExistOmOrderId($omOrderId,$spu){
        $table  = C('DB_PREFIX').'declaration_content';
        //return "SELECT id FROM {$table} WHERE omOrderId = '{$omOrderId}' AND spu = '{$spu}' ";
        $id     = $this->sql("SELECT id FROM {$table} WHERE omOrderId = '{$omOrderId}' AND spu = '{$spu}' ")->select();
        if($id){
            return true;
        }else{
            return false;
        }
    }

    public function setInsertOrderId($id){
        $this->_orderid = $id;
    }

    /**
     * 插入订单快递描述的方法，key为fedexRemark,现在只有独立商城会用到
     * 以下为demo
     * @param array $data 为一个二维数组，至少是一条快递描述记录
     * @return bool
     * @author zqt
     * @modify 20140807 修改方法名，同时订单大数组键改为了declarationContent,表也换了
     */
    public function insertOrderDeclarationContent($data){
        $data = array_filter($data);
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单跟踪号是非必须的
            return true;
        }
        //检测订单号是否插入成功
        if ($this->_orderid==0){
            return false;
        }else{
            foreach($data as $key=>$value){
                $data[$key]['omOrderId'] = $this->_orderid;
                $data[$key]['datetime'] = time();
            }
        }
        $table = C('DB_PREFIX').'declaration_content';

        foreach($data as $value){
            $fdata = $this->formatInsertField($table, $value);
            if ($fdata===false){
                self::$errMsg = $this->validatemsg;
                return false;
            }
            if(!$this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert()){
                return false;
            }
        }
        return true;
    }

    /**
     * @param $omOrderId   订单的Id
     * @param $skuArr      订单的SKU 数组格式 {sku=>{amount=>1,price=>1}}
     * @return bool
     */
    public function insertDeclarationRemark($omOrderId,$skuArr){
        $spuList  = array();
        $spuClump = array();
        $declaration = array();
        if(is_array($skuArr)){
            foreach($skuArr as $sku=>$amountPrice){
                $orderSkuList   = get_orderskulist($sku);
                $realSku        = $orderSkuList['realsku'];
                if(is_array($realSku)){
                    foreach($realSku as $v){
                        $spu = $v['spu'];
                        if(!empty($spu)){
                            $spuList[]      = $spu;
                            $spuClump[$spu] = $amountPrice;
                        }
                    }
                }
            }
        }else{
            self::$errMsg['104'] = '参数传递SKU格式为数组';
        }
        $spuList = array_unique($spuList);
        $spuList = json_encode($spuList);
        $data = M('InterfacePc')->getHscodeInfoBySpuArr($spuList);
        if(is_array($data)){
            foreach($data as $i=>$list){
                $spu                            = $list['spu'];
                $declaration[$i]['spu']         = $spu;
                $declaration[$i]['cnTitle']     = empty($list['customsName'])?' ':$list['materialEN'];
                $declaration[$i]['enTitle']     = empty($list['customsNameEN'])?' ':$list['materialEN'];
                $declaration[$i]['brand']       = ' ';                   //品牌未获取到
                $declaration[$i]['material']    = empty($list['materialEN'])?' ':$list['materialEN'];
                $declaration[$i]['cnMaterial']  = empty($list['materialCN'])?' ':$list['materialCN'];
                $declaration[$i]['hamcodes']    = empty($list['hsCode'])?' ':$list['hsCode'];
                $declaration[$i]['unit']        = 'pics';                  //单位未能正确的读取到从产品中心
                $declaration[$i]['amount']      = $spuClump[$spu]['amount'];
                $declaration[$i]['price']       = $spuClump[$spu]['price'];
            }
        }
        if(sizeof($declaration)){
            $this->setInsertOrderId($omOrderId);
            if(!$this->insertOrderDeclarationContent($declaration)){
                self::$errMsg['104']  = '插入失败,请检查传递参数';
            }
            
            return true;
        }
        
        return false;
    }
}