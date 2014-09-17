<?php
/**
 * 分区扫描
 * @author heminghua
 */
class pda_orderPartionView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_pda_orderPartion(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
        $userId = $_SESSION['userId'];
		$type = isset($_GET['type'])?trim($_GET['type']):"";
		if($type=="pack"){
			$this->smarty->assign("successLog","打包成功！");
			$this->smarty->assign("lists",array());
		}
		if($type=="scan"){
			$partion = isset($_GET['partion']) ? urldecode(trim($_GET['partion'])) : "";
			$orderid = isset($_GET['orderid']) ? trim($_GET['orderid']) : "";
			
			$where = " where partion='{$partion}' and scanUserId={$userId} and packageid is null";
			$lists = orderPartionModel::selectData($where);
			$lists[0]['partion'] = $partion;
			$this->smarty->assign("successLog","扫描成功！"."(".$orderid.")");
			$this->smarty->assign("orderid",$orderid);
			$this->smarty->assign("partion",$partion);
			$this->smarty->assign("lists",$lists);
		}
		
		
		/*$channel_list = $memc_obj->get_extral('trans_system_channelinfo');
		
		//print_r($channel_list);
		$this->smarty->assign("channel_list",$channel_list);
		
		
		$partion_list = $memc_obj->get_extral('trans_system_carrierinfo');
		
		
		
		foreach($partion_list as $key=> $value_arr){
			foreach($value_arr as $key1=> $value){
				$partion[$key1] = $value;
				$carrierId = $key;
				$carrier_list = $memc_obj->get_extral('trans_system_carrier');
				//print_r($carrier_list);
				foreach($carrier_list as $carrier_value){
					if($carrierId == $carrier_value['id']){
						$partion[$key1]['carrierName'] = $carrier_value['carrierNameCn'];
					}
					
				}
			}
		}
		//print_r($partion);
		$this->smarty->assign("partion",$partion);*/
		$where = " where scanUserId='{$userId}' and packageid is null";
        $ret = orderPartionModel::selectUserPartion($where);

		/*
		foreach($ret as $key=>$ret_value){
			foreach($partion_list as $key=> $value_arr){
				foreach($value_arr as $key1=> $value){
					if($ret_value['partionId']==$value['partionId']){
						$ret[$key]['partionName'] = $value['partitionName'];
					}
					
				}
			}
		}*/
		$toptitle = '分区扫描';
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle); 
		$this->smarty->assign("partionuser",$ret);
        $this->smarty->display('pda_orderPartion.htm');
	}
}