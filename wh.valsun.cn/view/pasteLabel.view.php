<?php
/**
 *贴标信息
 * @author hws
 */
class PasteLabelView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     *录入页面
     */
    public function view_pasteLabel(){
        
        $navlist    =   array(array('url'=>'','title'=>'入库'),              //面包屑数据
                         array('url'=>'','title'=>'贴标录入'),
                );
        $storeId    =   intval(trim($_GET['storeId']));
        $storeId    =   $storeId ? $storeId : 1;
        $toplevel   =   1;
        $secondlevel=   $storeId == 1 ? "17" : 20;
		
		$usermodel = UserModel::getInstance();
		//贴标员
		$tally_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=130",'','');
		$this->smarty->assign('tally_user', $tally_user);
		
        $toptitle = '贴标操作';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->assign('storeId', $storeId);
        $this->smarty->display('pasteLabel.htm');
    }
	 /*
     *贴标清单页面
     */
	public function view_labelingList(){
		$navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
				 array('url'=>'index.php?mod=pasteLabel&act=pasteLabel','title'=>'贴标录入'),
				 array('url'=>'','title'=>'贴标列表'),
		);
        $toplevel = 1;
        $secondlevel = 17;
		
        $checkUser = isset($_GET['checkUser'])?$_GET['checkUser']:"";
		$sku       = isset($_GET['sku'])?$_GET['sku']:"";
		$status    = isset($_GET['status'])?$_GET['status']:0;
		$start     = isset($_GET['startdate'])?$_GET['startdate']:"";
		$end       = isset($_GET['enddate'])?$_GET['enddate']:"";
		
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
		$this->smarty->assign("status",$status);
		if(empty($checkUser)&&empty($sku)&&empty($start)&&empty($end)&&empty($status)){
			$where ="where a.is_delete=0 and a.status=1 order by a.id desc";
		}else{		
			if(!empty($checkUser)){
				$where[] = "a.labelUserId='{$checkUser}'";
				$this->smarty->assign("labelingUserId",$checkUser);
			}
			if(!empty($status)){
				if($status==1){
					$where[] = "a.labelUserId is NULL";
				}
				if($status==2){
					$where[] = "a.labelUserId is not NULL";
				}	
			}
			if(!empty($sku)){
				$where[] = "b.sku = '{$sku}'";
				$this->smarty->assign("sku",$sku);	
			}
			if(!empty($start)&&!empty($end)){
				$starttime = strtotime($start);
				$endtime = strtotime($end);
				$where[] = "(a.labelTime between {$starttime} and {$endtime})";
				$this->smarty->assign("start",$start);
				$this->smarty->assign("end",$end);
			}elseif(!empty($start)&&empty($end)){
				$starttime = strtotime($start);
				$where[] = "a.labelTime >{$starttime}";
				$this->smarty->assign("start",$start);
			}elseif(empty($start)&&!empty($end)){
				$endtime = strtotime($end);
				$where[] = "a.labelTime < {$endtime}";
				$this->smarty->assign("end",$end);
			}
			$where = implode(" AND ",$where);
			$where = " where a.is_delete=0 and a.status=1 and ".$where." order by a.id desc";

		}
		
		$lists = PasteLabelModel::selectList($where);		
		$pagesize = 20;
		$nums = count($lists);
		$pager = new Page($nums,$pagesize);
		$lists = PasteLabelModel::selectList($where." ".$pager->limit);
		if ($nums > $pagesize) {       //分页
			$pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $pager->fpage(array(0, 2, 3));
		}
		$usermodel = UserModel::getInstance();
		//贴标员
		$tallyUser = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=130",'','');
		$this->smarty->assign('tallyUser', $tallyUser);
		$toptitle = '贴标列表';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('pagestr', $pagestr);
		$this->smarty->assign("lists",$lists);		
        $this->smarty->display('labelingList.htm');
	}
	
	public function view_export(){
        $exportXlsAct = new PasteLabelAct();
        $exportXlsAct->act_export();
    }
	
}
?>