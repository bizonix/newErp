<?php
/*
 *打标页面和打标清单页面
 *@author heminghua
 *
 */
class printLabelView extends CommonView{
	/*
	* 构造函数
	*/
    public function __construct() {
        parent::__construct();
    }
	
	/*
	*打标页面 
	*/
	public function view_printLabel(){
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'','title'=>'打标'),
		);
        $storeId    =   intval(trim($_GET['storeId']));
        $storeId    =   $storeId ? $storeId : 1;
        $toplevel   =   1;
        $secondlevel=   $storeId == 1 ? 12 : 19;
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->assign('storeId', $storeId);
        
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$sku = isset($_GET['sku'])?$_GET['sku']:"";
		$start = isset($_GET['startdate']) ? $_GET['startdate']:"";
		$end = isset($_GET['enddate']) ? $_GET['enddate']:"";
		$entryUserId = isset($_GET['entryUserId']) ? $_GET['entryUserId']:"";
		
		if(empty($checkUser)&&empty($entryUserId)&&empty($sku)&&empty($start)&&empty($end)){
			$where = "";
		}else{
			
			if(!empty($checkUser)){
				$where[] = "tallyUserId='{$checkUser}'";
				$this->smarty->assign("checkUser",$checkUser);
			}
			if(!empty($entryUserId)){
				$where[] = "entryUserId='{$entryUserId}'";
				$this->smarty->assign("entryUserId",$entryUserId);
			}
			if(!empty($sku)){
				$where[] = "sku = '{$sku}'";
				$this->smarty->assign("sku",$sku);
			}
			if(!empty($start)&&!empty($end)){
				$starttime = strtotime($start);
				$endtime   = strtotime($end);
				$where[] = "(entryTime between {$starttime} and {$endtime})";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
			}elseif(!empty($start)&&empty($end)){
				$starttime = strtotime($start);
				$where[] = "entryTime >{$starttime}";
				$this->smarty->assign("start",$start);
			}elseif(empty($start)&&!empty($end)){
				$endtime  = strtotime($start);
				$where[] = "entryTime < {$endtime}";
				$this->smarty->assign("end",$end);
			}
			$where = " AND ".implode(" AND ",$where);
			//$where = "where ".$where;
		}
        $where  .=  ' and storeId = '.$storeId;
		$where   = "where printerId IS NULL and is_delete=0 and entryStatus=0".$where;
        //echo $where;exit;
		$pagesize = 20;
		//print_r($where);die;	
		$lists = packageCheckModel::selectList($where);
		$tempList = $lists;
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
		
		//录入员
		$entryUserIdArr = array();
		foreach ($tempList as $k1 => $v1) {
			$entryUserIdArr[] = $v1['entryUserId'];
		}
		$entryUserIdList = array_unique($entryUserIdArr);	

		$global_user_list = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where 1",'','');
		//print_r($global_user_list);
		$entryUserList   = array();
		foreach ($global_user_list as $k2 => $v2) {
			foreach ($entryUserIdList as $k3 => $v3) {			
				if ($v3 == $v2['global_user_id']) {				
					$entryUserList[] = array(
						'id' 	=> $v2['global_user_id'], 
						'name'  => $v2['global_user_name'], 
						);
				}				
			}
		}
		unset($tempList);		
		unset($entryUserIdList);
		unset($global_user_list);
		$this->smarty->assign('entryUserList', $entryUserList);

		//到货库存
		foreach($lists as $key=>$list){
			$sku_arrival = OmAvailableModel::getTNameList("wh_sku_location","arrivalInventory","where sku='{$list['sku']}'");
			$lists[$key]['arrivalInventory'] = $sku_arrival[0]['arrivalInventory'];
		}
		
		$toptitle = '打标操作';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);
		$userList = packageCheckModel::selectUser();
        $this->smarty->display('printLabel.htm');
	}
	/*
	*打标清单页面 
	*/
	public function view_printLabelList(){
	    $storeId    =   intval(trim($_GET['storeId']));
        $storeId    =   $storeId ? $storeId : 1;
        
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'index.php?mod=printLabel&act=printLabel&storeId='.$storeId,'title'=>'打标操作'),
				 array('url'=>'','title'=>'打标清单'),
		);
        
        $toplevel   =   1;
        $secondlevel=   $storeId == 1 ? 12 : 19;
        
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->assign('storeId', $storeId);
        
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$sku = isset($_GET['sku'])?$_GET['sku']:"";
		$start = isset($_GET['start']) ? $_GET['start']:"";
		$end = isset($_GET['end']) ? $_GET['end']:"";

		if(empty($checkUser)&&empty($sku)&&empty($start)&&empty($end)){
			$where = "";
		}else{
			
			if(!empty($checkUser)){
				$where[] = "printerId='{$checkUser}'";
				$this->smarty->assign("checkUser",$checkUser);
			}
			if(!empty($sku)){
				$where[] = "sku = '{$sku}'";
				$this->smarty->assign("sku",$sku);
			}
			if(!empty($start)&&!empty($end)){
				$starttime = strtotime($start." 00:00:00");
				$endtime   = strtotime($start." 23:59:59");
				$where[] = "(entryTime between {$starttime} and {$endtime})";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
			}elseif(!empty($start)&&empty($end)){
				$starttime = strtotime($start." 00:00:00");
				$where[] = "entryTime >{$starttime}";
				$this->smarty->assign("start",$start);
			}elseif(empty($start)&&!empty($end)){
				$endtime   = strtotime($start." 23:59:59");
				$where[] = "entryTime < {$endtime}";
				$this->smarty->assign("end",$end);
			}
			$where = " AND ".implode(" AND ",$where);
			//$where = "where ".$where;
		}
		$where = "where printerId IS NOT NULL and is_delete=0".$where." and storeId = '{$storeId}' order by id desc";
		$pagesize = 20;
			
		//$lists = packageCheckModel::selectList($where);
		//$nums = count($lists);
        $nums  = packageCheckModel::getTotalNums($where);
		$pager = new Page($nums,$pagesize);
		$lists = packageCheckModel::selectList($where." ".$pager->limit);
		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		
		$usermodel = UserModel::getInstance();
		//打标员
		$Marking_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=168",'','');
		$this->smarty->assign('Marking_user', $Marking_user);
		
		foreach($lists as $key=>$list){
			//到货库存
			$sku_arrival = OmAvailableModel::getTNameList("wh_sku_location","arrivalInventory","where sku='{$list['sku']}'");
			$lists[$key]['arrivalInventory'] = $sku_arrival[0]['arrivalInventory'];
		}
		
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);
		$toptitle = '打标清单列表';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$userList = packageCheckModel::selectUser();
        $this->smarty->display('printLabelList.htm');
	}
	/*
	*打印页面 
	*/
	public function view_printLabelPrint(){
		/*$max_num = isset($_GET['max_num'])?$_GET['max_num']:100;
		$idarr = isset($_GET['idarr'])?$_GET['idarr']:array();
		$lists = array();
		foreach($idarr as $key =>$id){
			$where = "where id={$id}";
			$list = packageCheckModel::selectList($where);
			$n = 0;
			$group_id = array();
			while($list[0]['num']>($max_num*$n)){
				//$group_id = printLabel::insertGroupPrint();
				$n++;
			}
			$list['group_id'] = $group_id;
			$lists[] = $list;
		}
		$this->smarty->assign("lists",$lists);
		
		*/
		include "../html/template/v1/printLabelPrint.php";
	}
	
	/*
	 * 补标页面
	 */
	public function view_suppleLabel(){
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'index.php?mod=printLabel&act=printLabel','title'=>'打标操作'),
				 array('url'=>'','title'=>'补标'),
		);
        $toplevel = 1;
        $secondlevel = 12;
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        
		$toptitle = '补标打印';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
        $this->smarty->display('suppleLabel.htm');
	}
	
	/*
	 * 补标打印页面
	 */
	public function view_printBuLabelPrint(){
		include "../html/template/v1/printBuLabelPrint.php";
	
	}

	/*
	 * 漏标打印页面
	 */
	public function view_printLabelLostPrint(){	
		include "../html/template/v1/printLabelLostPrint.php";
	}
    
    //打标数据导出
    public function view_export(){
        $exportXlsAct = new PrintLabelAct();
        $exportXlsAct->act_export();
    }

}
?>