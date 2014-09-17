<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-3
 * Time: 下午2:47
 */
class AverageDailyCountModel extends  CommonModel{
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param $data
     * @return bool
     * 对数据进行检测
     */
    public function checkIsExists($data){
        $orderTime1 = $data['orderTime1'];
        $orderTime2 = $data['orderTime2'];

        if($orderTime1 >= $orderTime2){
            self::$errMsg[104] = '开始时间段不能大于或者等于结束的时间段';
            return true;
        }else{
            $checkdata = $this->sql("SELECT * FROM ".$this->getTableName()." WHERE is_delete = '0' AND platformId = '".$data['platformId']."' AND accountId = '".$data['accountId']."' AND sku = '".$data['sku']."'")->select();
            if(!empty($checkdata)){
                foreach($checkdata as $checkList){
                    $checkOrderTime1 = $checkList['orderTime1'];
                    $checkOrderTime2 = $checkList['orderTime2'];
                    if($orderTime1 >= $checkOrderTime1 && $orderTime1 <= $checkOrderTime2){
                        self::$errMsg[105] = 'SKU添加失败,开始时间和以前重复,请检查';
                        return true;
                    }
                    if($orderTime2 >= $checkOrderTime1 && $orderTime2 <= $checkOrderTime2){
                        self::$errMsg[105] = 'SKU添加失败,结束时间和以前重复,请检查';
                        return true;
                    }
                    if($orderTime1 <= $checkOrderTime1 && $orderTime2 >= $checkOrderTime2){
                        self::$errMsg[105] = 'SKU添加失败,开始时间到结束时间包含已经存在的该SKU的时间段';
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getAverageDailyCount(){
        return $this->sql("SELECT COUNT(*) AS count FROM {$this->getTableName()} WHERE is_delete=0")->count();
    }

    /**
     * @param int $page
     * @param int $perpage
     * @return mixed
     * 获取所有的列表信息
     */
    public function getAverageDailyCountList($page=1, $perpage=50){
        return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE  is_delete = '0' ORDER BY sku ")->page($page)->perpage($perpage)->select(array('cache', 'mysql'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAverageDailyCountListById($id){
        return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE  is_delete = '0' AND id = '".$id."'  ORDER BY sku ")->select(array('cache', 'mysql'));
    }

    /**
     * @param $platformId
     * @param $accountId
     * @param $sku
     * @return bool
     *
     * 传入 平台id 帐号id sku 检查该sku是否参与每日均量的计算
     */
    public function findAverageDailyCount($platformId,$accountId,$sku){
        $nTime = time();
        $rs    = false;
        if($this->sql("SELECT * FROM ".$this->getTableName()." WHERE  is_delete = '0' AND platformId = '".$platformId."' AND accountId = '".$accountId."' AND sku = '".$sku."' AND orderTime1 <= '".$nTime."' AND  orderTime2 >= '".$nTime."'  ORDER BY sku ")->select(array('cache', 'mysql'))){
            $rs    = true;
        }
        return $rs;
    }
}