<?php
/*
 *未订单料号列表
 *@author heminghua
 *
 */
class whNoOrderView extends CommonView{
	/*
	* 构造函数
	*/
    public function __construct() {
        parent::__construct();
    }
	public function view_whNoOrder(){
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'','title'=>'未订单列表'),
		);
        $toplevel = 1;
        $secondlevel = "15";
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
		$sku = isset($_POST['sku'])?$_POST['sku']:"";
		$isConfirm = isset($_POST['isConfirm'])?$_POST['isConfirm']:"";
		$start = isset($_POST['start'])?$_POST['start'] : date("Y-m-d");
		$end = isset($_POST['end'])?$_POST['end'] : date("Y-m-d");

		if($sku==""&&$isConfirm==""&&$start==""&&$end==""){
			$where = "";
		}else{
			if($sku!=""){
				$where[] = "sku='{$sku}'";
				$this->smarty->assign("sku",$sku);
			}
			if($isConfirm!=""){
				$confirm = ($isConfirm==1)?1:0;
				$where[] = "isConfirm='{$confirm}'";
				$this->smarty->assign("isConfirm",$isConfirm);
			}
			if($start !="" && $end !=""){
				$time_start = strtotime($start." 00:00:00");
				$time_end = strtotime($end." 23:59:59");
				$where[] = "createdTime between {$time_start} and {$time_end}";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
				
			}elseif($start !="" && $end ==""){
				$time_start = strtotime($start." 00:00:00");
				$where[] = "createdTime >{$time_start}";
				$this->smarty->assign("start",$start);
			}elseif($start =="" && $end !=""){
				$time_end = strtotime($end." 23:59:59");
				$where[] = "createdTime <{$time_end}";
				$this->smarty->assign("end",$end);
				
			}
			$where = implode(" and ",$where);
			$where = " where ".$where;
		}
		$pagesize = 30;
		$lists = whNoOrderModel::selectList($where);
		$nums = count($lists);
		$pager = new Page($nums,$pagesize);
		$lists = whNoOrderModel::selectList($where." ".$pager->limit);
		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		
		$usermodel = UserModel::getInstance();		
		$count = count($lists);
		for($i=0;$i<$count;$i++){
			//入库人
			$creator_user_info 	  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$lists[$i]['creatorId']}'",'','limit 1');
			$lists[$i]['creator'] = $creator_user_info[0]['global_user_name'];			
			//采购人
			$purchase_user_info    = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$lists[$i]['purchaseId']}'",'','limit 1');
			$lists[$i]['purchase'] = $purchase_user_info[0]['global_user_name'];	
			//确认人
			$confirmUser_user_info    = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$lists[$i]['confirmUserId']}'",'','limit 1');
			$lists[$i]['confirmUser'] = $confirmUser_user_info[0]['global_user_name'];
		}
		
		$toptitle = '未订单列表';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);
		$this->smarty->display("whNoOrder.htm");
	}
}
?>