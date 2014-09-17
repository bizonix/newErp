<?php
/**
 *点货信息
 * @author heminghua
 */
class packageCheckView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     *点货页面 
     */
    public function view_packageCheck(){
        
        $navlist    =   array(array('url'=>'','title'=>'入库'),              //面包屑数据
                         array('url'=>'','title'=>'点货操作'),
                );
        $toplevel   =   1;
        $storeId    =   isset($_GET['storeId']) ? $_GET['storeId'] : 1;
        $storeId    =   intval($storeId) ? intval($storeId) : 1;
        $secondlevel=   $storeId == 1 ? "11" : 18;
        //$userName = $_SESSION['username'];
		
		$usermodel  = UserModel::getInstance();
		//点货员
		$tally_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=209",'','');
		$this->smarty->assign('tally_user', $tally_user);
		
        $toptitle = '点货操作';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->assign('storeId',  $storeId);
		
		$data = isset($_GET['data'])?$_GET['data']:"";
		$this->smarty->assign("data",urldecode($data));
        //$this->smarty->assign('toptitle', '货品资料管理');
        $this->smarty->display('packageCheck.htm');
    }
	
	 /*
     *点货清单页面
     */
	public function view_packageCheckList(){
	    $storeId  = intval(trim($_GET['storeId']));
        $storeId  = $storeId ? $storeId : 1;
        
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'index.php?mod=packageCheck&act=packageCheck&storeId='.$storeId,'title'=>'点货操作'),
				 array('url'=>'','title'=>'点货清单'),
		);
        $toplevel = 1;
        
        $secondlevel = $storeId == 1 ? 11 : 18;
		
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$sku = isset($_GET['sku'])?$_GET['sku']:"";
		$start     = isset($_GET['startdate'])?$_GET['startdate']:"";
		$end       = isset($_GET['enddate'])?$_GET['enddate']:"";
        
        $this->smarty->assign('storeId',  $storeId);
        
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
		if(empty($checkUser)&&empty($sku)&&empty($start)&&empty($end)){
			$where ="where is_delete=0";
		}else{
			
			if(!empty($checkUser)){
				$where[] = "tallyUserId='{$checkUser}'";
				$this->smarty->assign("tallyUserId",$checkUser);
			}
			if(!empty($sku)){
				$where[] = "sku = '{$sku}'";
				$this->smarty->assign("sku",$sku);
				
			}
			if(!empty($start)&&!empty($end)){
				$starttime = strtotime($start);
				$endtime = strtotime($end);
				$where[] = "(entryTime between {$starttime} and {$endtime})";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
			}elseif(!empty($start)&&empty($end)){
				$starttime = strtotime($start);
				$where[] = "entryTime >{$starttime}";
				$this->smarty->assign("start",$start);
			}elseif(empty($start)&&!empty($end)){
				$endtime = strtotime($end);
				$where[] = "entryTime < {$endtime}";
				$this->smarty->assign("end",$end);
			}
			$where = implode(" AND ",$where);
			$where = " where is_delete=0 and ".$where;

		}
        $where .= " and storeId ='{$storeId}' order by id desc";
		$nums = packageCheckModel::getTotalNums($where);		
		$pagesize = 20;
		//$nums  = count($lists);
		$pager = new Page($nums,$pagesize);
		$lists = packageCheckModel::selectList($where." ".$pager->limit);
		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		$userList = packageCheckModel::selectUser();
		$usermodel = UserModel::getInstance();
		foreach($lists as $key=>$list){
			//到货库存
			$sku_arrival = OmAvailableModel::getTNameList("wh_sku_location","arrivalInventory","where sku='{$list['sku']}' and storeId='{$storeId}'");
			$lists[$key]['arrivalInventory'] = $sku_arrival[0]['arrivalInventory'];
            //获取原始点货数量
            $before_num  = OmAvailableModel::getTNameList("wh_tallying_adjustment","beforeNum","where tallyListId='{$list['id']}' order by id asc limit 1");
            $lists[$key]['before_num']  =   empty($before_num) ? $list['num'] : $before_num[0]['beforeNum'];
		}
		//点货员
		$tallyUser = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=209",'','');
		$this->smarty->assign('tallyUser', $tallyUser);
		$toptitle = '点货清单列表';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);		
        $this->smarty->display('packageCheckList.htm');
	} 
	
	 /*
     *点货清单页面
     */
	public function view_showPackage(){
		$navlist = array(array('url'=>'','title'=>'库存管理'),              //面包屑数据
				 array('url'=>'','title'=>'点货清单'),
		);
        $toplevel = 0;
        $secondlevel = 03;
		
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$purchase  = isset($_GET['purchase'])?$_GET['purchase']:"";
		$sku 	   = isset($_GET['sku'])?$_GET['sku']:"";
		$status    = isset($_GET['status'])?$_GET['status']:3;
		$start     = isset($_GET['startdate'])?$_GET['startdate']:"";
		$end       = isset($_GET['enddate'])?$_GET['enddate']:"";
       
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
		if(empty($checkUser)&&empty($purchase)&&empty($sku)&&empty($start)&&empty($end)&&($status==200)){
			$where ="where is_delete=0";
		}else{
			
			if(!empty($checkUser)){
				$where[] = "tallyUserId='{$checkUser}'";
				$this->smarty->assign("tallyUserId",$checkUser);
			}
			if(!empty($purchase)){
				$where[] = "purchaseId='{$purchase}'";
				$this->smarty->assign("purchase",$purchase);
			}
			if(!empty($sku)){
				$where[] = "sku = '{$sku}'";
				$this->smarty->assign("sku",$sku);
				
			}
			if($status!=200){
				$where[] = "entryStatus = '{$status}'";
				$this->smarty->assign("status",$status);
				
			}
			if(!empty($start)&&!empty($end)){
				$starttime = strtotime($start);
				$endtime = strtotime($end);
				$where[] = "(entryTime between {$starttime} and {$endtime})";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
			}elseif(!empty($start)&&empty($end)){
				$starttime = strtotime($start);
				$where[] = "entryTime >{$starttime}";
				$this->smarty->assign("start",$start);
			}elseif(empty($start)&&!empty($end)){
				$endtime = strtotime($end);
				$where[] = "entryTime < {$endtime}";
				$this->smarty->assign("end",$end);
			}
			$where = implode(" AND ",$where);
			$where = " where is_delete=0 and ".$where;

		}
        $where  = $where.' order by id desc';
		$nums   = packageCheckModel::getTotalNums($where);		
		$pagesize = 50;
		//$nums = count($lists);
		$pager  = new Page($nums,$pagesize);
		$lists  = packageCheckModel::selectList($where." ".$pager->limit);
		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		$userList = packageCheckModel::selectUser();
		$usermodel = UserModel::getInstance();
		foreach($lists as $key=>$list){
			//到货库存
			$sku_arrival = OmAvailableModel::getTNameList("wh_sku_location","arrivalInventory","where sku='{$list['sku']}'");
			$lists[$key]['arrivalInventory'] = $sku_arrival[0]['arrivalInventory'];
            //获取原始点货数量
            $before_num  = OmAvailableModel::getTNameList("wh_tallying_adjustment","beforeNum","where tallyListId='{$list['id']}' order by id asc limit 1");
            $lists[$key]['before_num']  =   empty($before_num) ? $list['num'] : $before_num[0]['beforeNum'];
		}
		//点货员
		$tallyUser = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=209",'','');
		$this->smarty->assign('tallyUser', $tallyUser);
		
		//采购员
		$purchaseList = CommonModel::getPurchaseList();
		$this->smarty->assign('purchaseList', $purchaseList);
        
         /** 添加贴标时间、QC检测时间、上架时间 add by GARY(yym)**/
        $this->smarty->registerPlugin('function','getSkuTime','getSkuTime');
        //$this->smarty->registerPlugin('function','getSkuInputTime','getSkuInputTime');
        /** end**/
        
		$toptitle = '点货清单列表';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);		
        $this->smarty->display('packageCheckList1.htm');
	} 
	
	/*
	*异常录入页面 
	*/
	public function view_abnormal(){
	    $storeId     = intval(trim($_GET['storeId']));
        $storeId     = $storeId ? $storeId : 1; //仓库ID
        
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'index.php?mod=packageCheck&act=packageCheck&storeId='.$storeId,'title'=>'点货操作'),
				 array('url'=>'','title'=>'异常录入'),
		);
        $toplevel = 1;
        
        $secondlevel = $storeId == 1 ? "11" : 18;
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->assign('storeId', $storeId);
        
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$status    = isset($_GET['status'])?$_GET['status']:1;
		$sku       = isset($_GET['sku'])?$_GET['sku']:"";
		$start 	   = isset($_GET['start']) ? $_GET['start']:"";
		$end	   = isset($_GET['end']) ? $_GET['end']:"";
		
		$where[] = "entryStatus='{$status}'";
		$this->smarty->assign("status",$status);
		if(!empty($checkUser)){
			$where[] = "tallyUserId='{$checkUser}'";
			$this->smarty->assign("checkUser",$checkUser);
		}
		if(!empty($sku)){
			$where[] = "sku = '{$sku}'";
			$this->smarty->assign("sku",$sku);
		}
		
		if(!empty($start)){
			$start_time = strtotime($start." 00:00:00");
			$where[] = "entryTime >={$start_time}";
			$this->smarty->assign("start",$start);
		}
		if(!empty($end)){
			$end_time = strtotime($end." 23:59:59");
			$where[] = "entryTime <={$end_time}";
			$this->smarty->assign("end",$end);
		}
		
		$where = " AND ".implode(" AND ",$where);

		$where = "where is_delete=0 and entryStatus!=0".$where;
        $where .= " and storeId ='{$storeId}'";
		$pagesize = 200;
//print_r($where);exit;
		$lists = packageCheckModel::selectList($where);
		$nums = count($lists);
		$pager = new Page($nums,$pagesize);
		$lists = packageCheckModel::selectList($where." ".$pager->limit);

		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		
		$usermodel = UserModel::getInstance();
		//点货员
		$Marking_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=209",'','');
		$this->smarty->assign('Marking_user', $Marking_user);
		
		$toptitle = '异常录入';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);
		$userList = packageCheckModel::selectUser();
        $this->smarty->display('abnormal.htm');
	}
	
	public function view_export(){
        $exportXlsAct = new packageCheckAct();
        $exportXlsAct->act_export();
    }
}
?>