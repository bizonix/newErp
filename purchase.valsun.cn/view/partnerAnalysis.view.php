<?php
/**
 * PartnerAnalysisView
 * 功能：封装货期分析模块的相关操作
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */

include_once WEB_PATH.'action/partner.action.php';
class PartnerAnalysisView extends BaseView {    
  
    /**
    * 构造函数
    * @return   void
    */
    public function __construct() {
    	parent:: __construct();
    	if(isset($_GET["mod"]) && !empty($_GET["mod"])) {
            $mod=$_GET["mod"];
    	}
    	if(isset($_GET["act"]) && !empty($_GET["act"])) {
    		$act=$_GET["act"];
    	}
    	$this->smarty->assign('act',$act);//模块权限
    	$this->smarty->assign('mod',$mod);//模块权限
    	$this->smarty->caching 		= false;
    	$this->smarty->debugging 	= false;
    	$this->smarty->assign("WEB_API", WEB_API);
    	$this->smarty->assign("WEB_URL", WEB_URL);
        $_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
        $this->smarty->assign('_username',$_username);
    }
  
    /**
    * SKU货期分析
    * @return    void
    */
    public function view_skuAnalysis() {        
        $this->smarty->assign("title", "SKU货期分析");       
        $where   = '';
        $sku     = (isset($_GET['sku'])) ? post_check($_GET['sku']) : '';             
        if($sku == '') {  
            echo '无 SKU 参数！';
            return false;  
        } 
        $this->smarty->assign("second_position", '>><a href="index.php?mod=partnerAnalysis&act=partnerAnalysis&sku=$sku">SKU货期分析</a>'); 
        
        $timeStamp = time() - 60*24*3600; //去最近2个月的记录
        $where = " AND pg.sku = '$sku' AND pod.add_time > '$timeStamp' " ; 
        $field = " pod.add_time,pod.reach_time ";          
        $list = PartnerAnalysisAct::act_getSKUAnalysis($where, $field);      
        $total = count($list);
        if($total == 0) {
            echo '没有对应数据';
            return false;
        }
       
        $dateStr  = array();
        $interval = array();                         
        foreach($list as $key => $data) {            
            $dateStr[]      = "'".date("Y.m.d",$data['add_time'])."-".date("Y.m.d",$data['reach_time'])."'";            
            $interval[]     = sprintf("%.2f", ($data['reach_time'] - $data['add_time']) / 86400);       
        } 
        
        //取最近5次
        $latest_intervals = array();
        if($total > 5) {
            for($i = $total - 1; $i > $total - 6; $i--) {
                $latest_intervals[] = $interval[$i];
            } 
        } else {
            $latest_intervals = $interval;           
        }
         
        //去掉最大最小值后，计算货期均值
        sort($latest_intervals);      
        $average_interval = 0;
        $total_sum = 0;
        $count = count($latest_intervals);                  
        if($count > 3) {
            for($i = 1; $i < $count - 1; $i++) {
                $total_sum += $latest_intervals[$i];
            }
            $average_interval = $total_sum / ($count - 2);                        
        } else if($count > 0 && $count <= 3) {
            for($i = 0; $i < $count; $i++) {
                $total_sum += $latest_intervals[$i];
            }
            $average_interval = $total_sum / $count;
        }    
        $average_interval = sprintf("%.2f", $average_interval);        
        array_push($dateStr, "'average_days'");
        array_push($interval, $average_interval);        
        $this->smarty->assign("dateStr", implode(',', $dateStr));
        $this->smarty->assign("intervalStr", implode(',', $interval));                            
		$this->smarty->display('showAnalysis.htm');               
    }
    
    /**
    * 供应商货期分析
    * @return    void
    */
    public function view_partnerAnalysis() {        
        $this->smarty->assign("title", "供应商货期分析");         
        $where      = '';
        $partner_id = (isset($_GET['id'])) ? post_check($_GET['id']) : '';             
        if($partner_id == '') {  
            echo '无 id 参数！';
            return false;  
        }       
        $timeStamp = time() - 60*24*3600; //最近2个月的记录      
        $where = " AND `partner_id` = '$partner_id' AND `addtime` > '$timeStamp' ";
        $field = " `addtime`, `finishtime` ";          
        $list = PartnerAnalysisAct::act_getPartnerOrderList($where, $field);
        $total = count($list);
        if($total == 0) {
            echo '没有对应数据';
            return false;
        }
        $dateStr  = array();
        $interval = array();                         
        foreach($list as $key => $data) {            
            $dateStr[]   = "'".date("Y.m.d",$data['addtime'])."-".date("Y.m.d",$data['finishtime'])."'";            
            $interval[]  = sprintf("%.2f", ($data['finishtime'] - $data['addtime']) / 86400);       
        }  
         
        //取最近5次
        $latest_intervals = array();
        if($total > 5) {
            for($i = $total - 1; $i > $total - 6; $i--) {
                $latest_intervals[] = $interval[$i];
            } 
        } else {
            $latest_intervals = $interval;           
        }
         
        //去掉最大最小值后，计算货期均值
        sort($latest_intervals);      
        $average_interval = 0;
        $total_sum = 0;
        $count = count($latest_intervals);                  
        if($count > 3) {
            for($i = 1; $i < $count - 1; $i++) {
                $total_sum += $latest_intervals[$i];
            }
            $average_interval = $total_sum / ($count - 2);            
        } else if($count > 0 && $count <= 3) {
            for($i = 0; $i < $count; $i++) {
                $total_sum += $latest_intervals[$i];
            }
            $average_interval = $total_sum / $count;           
        }
        $average_interval = sprintf("%.2f", $average_interval);        
        array_push($dateStr, "'average_days'");
        array_push($interval, $average_interval);        
        $this->smarty->assign("dateStr", implode(',', $dateStr));
        $this->smarty->assign("intervalStr", implode(',', $interval));                        
		$this->smarty->display('showAnalysis.htm');               
    } 
}
?>