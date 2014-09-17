<?php
/**
 * 快递发货单号的箱子打印
 * @author chenxianyu
 * add 2014-9-3
 */
class expressBoxView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_index(){
     //   echo 1111;
        $orderids     = trim($_GET['ebay_id']);
        $id_all = explode(',',$orderids);
        $array  = array();
        foreach($id_all as $id){
            $result = WhWaveTrackingBoxModel::select_by_shipOrderId($id);
            $array[$id] = $result;
        }
      //  print_r($array);
  		$toptitle = '快递箱号打印';        //顶部链接
        $this->smarty->assign('box_arr', $array);   
        $this->smarty->display('expressBox.htm');
    }
    
}
?>