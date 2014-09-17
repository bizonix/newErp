<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-3
 * Time: 下午2:45
 */
class AverageDailyCountAct extends CheckAct{
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }

    public function act_insert(){
        $data = array();
        $data['platformId'] = $_POST['platformId'];
        $data['accountId']  = $_POST['accountId'];
        $data['sku']        = $_POST['sku'];
        $data['orderTime1'] = strtotime($_POST['OrderTime1']);
        $data['orderTime2'] = strtotime($_POST['OrderTime2']);
        $data['updateTime'] = time();
        $data['operatorId'] = get_userid();

        return M('AverageDailyCount')->insertData($data);
    }

    public function act_update(){
        $id             	= isset($_POST['id']) ? trim($_POST['id']) : '';
        $data['platformId'] = $_POST['platformId'];
        $data['accountId']  = $_POST['accountId'];
        $data['sku']        = $_POST['sku'];
        $data['orderTime1'] = strtotime($_POST['OrderTime1']);
        $data['orderTime2'] = strtotime($_POST['OrderTime2']);
        $data['updateTime'] = time();
        $data['operatorId'] = get_userid();
        $data['is_delete']  = 0;
        return M('AverageDailyCount')->updateData($id,$data);
    }

    public function act_delete(){
        $id    = isset($_GET['id']) ? trim($_GET['id']) : '';
        return M('AverageDailyCount')->deleteData($id);
    }

    /**
     * @return mixed
     */
    public function act_getAverageDailyCountList(){
        return M('AverageDailyCount')->getAverageDailyCountList($this->page,$this->perpage);
    }

    public function act_getAverageDailyCountListById(){
        $id   = $_GET['id'];
        $data = M('AverageDailyCount')->getAverageDailyCountListById($id);
        if(!empty($data)) $data = $data[0];
        return $data;
    }

    public function act_getAverageDailyCount(){
        return M('AverageDailyCount')->getAverageDailyCount();
    }
}