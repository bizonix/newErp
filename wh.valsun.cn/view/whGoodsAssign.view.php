<?php

/**
 * whGoodsAssignView
 * 仓库调拨
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @access public
 */
class whGoodsAssignView extends CommonView {
    /*
     * 构造函数
     */
    private $topLevel; //一级导航级别
    private $firTitle; //一级菜单名
    public  $assign_status;
    public function __construct() {
        parent::__construct();
        $this->topLevel =   5;
        $this->firTitle =   '仓库调拨';
        $this->assign_status    =   C('assign_status');
    }

    /**
     * whGoodsAssignView::view_assignList()
     * 调拨单列表
     * @return void
     */
    public function view_assignList() {
        global $memc_obj;
        $pagesize   =   100;    //页面大小
        //print_r($this->assign_status);exit;
        $whereSql   =   $this->buildWhereSql();  //拼接sql条件
        //$whereSql   =   ' and a.status = 100';   
        //echo $whereSql;exit;
        //$assign_obj =   new WhGoodsAssignModel();
        $rownumber  =   WhGoodsAssignModel::getRowAllNumber($whereSql);  //获得所有的行数       
        if($rownumber > 0){
            
            $pager  =   new Page($rownumber, $pagesize);
            $lists  =   WhGoodsAssignModel::getAssignList($whereSql, 'a.id', $pager->limit, 'a.id', 'desc');
            //print_r($lists);   exit;          
    	    foreach($lists as $key => $val){   //处理调拨单
    	       $outStore           =   WarehouseManagementModel::warehouseManagementModelList(" where id = {$val['outStoreId']}");
               //print_r($outStore);exit;
    	       $lists[$key]['outStoreId']  =   $outStore[0]['whName'];
               $inStore            =   WarehouseManagementModel::warehouseManagementModelList(" where id = {$val['inStoreId']}");
               //print_r($outStore);exit;
    	       $lists[$key]['inStoreId']   =   $inStore[0]['whName'];
               $skusinfo           =   WhGoodsAssignModel::getsAssignListDetail($val['id']);
               $lists[$key]['skuinfo']     =   $skusinfo;
               $lists[$key]['createUid']   =   $val['createUid'] ? getUserNameById($val['createUid']) : '无';
               $lists[$key]['statusTime']  =   date('Y-m-d H:i:s', $val['statusTime']);
               $lists[$key]['createTime']  =   date('Y-m-d H:i:s', $val['createTime']);
               $lists[$key]['status']      =   $this->assign_status[$val['status']];
    	    }
            
            if ($rownumber > $pagesize) {       //分页
                $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
            } else {
                $pagestr =  $pager->fpage(array(0, 2, 3));
            }
            
            $this->smarty->assign('pagestr', $pagestr);
        }
        $storeLists     =   WarehouseManagementModel::warehouseManagementModelList($where); //获取可用仓库列表  
        self::bulidNav('调拨单浏览', '调拨单列表', 51);
        //print_r($storeLists);exit;
        //print_r($lists);exit;
        $this->smarty->assign('lists', $lists);          //调拨单列表
        $this->smarty->assign('assign_status', $this->assign_status); //调拨单状态
        $this->smarty->assign('storeLists', $storeLists);
        $this->smarty->display('assignList.htm');
    }
    
    /**
     * whGoodsAssignView::view_addAssignList()
     * 新增调拨单界面 
     * @return void
     */
    public function view_addAssignList(){
        self::bulidNav('新增调拨单', '新增调拨单', 51);
        $where          =   'where status = 1';
        $storeLists     =   WarehouseManagementModel::warehouseManagementModelList($where); //获取可用仓库列表
        $userName       =   $_SESSION['userId'] ? getUserNameById($_SESSION['userId']) : 0; //获取用户名
        $user           =   array('name'=>$userName, 'uid'=>$_SESSION['userId']);
        $state          =   isset($_GET['state']) ? $_GET['state'] : '';
        $this->smarty->assign('state', $state);
        $this->smarty->assign('storeLists', $storeLists);
        $this->smarty->assign('user', $user);
        $this->smarty->display('addAssignList.htm');
    }
    
    /**
     * whGoodsAssignView::view_editAssignList()
     * 调拨单修改界面
     * @return void
     */
    public function view_editAssignList(){
        self::bulidNav('修改调拨单', '修改调拨单', 51);
        $id             =   intval(trim($_GET['id'])) ? intval(trim($_GET['id'])) : 0;
        if($id){
            $where          =   'where status = 1';
            $storeLists     =   WarehouseManagementModel::warehouseManagementModelList($where); //获取可用仓库列表
            $data           =   WhGoodsAssignModel::getAssignList(" and a.id=$id", '', '', 'a.id');
            if(!empty($data)){
                $res        =   $data[0];
                $detail     =   WhGoodsAssignModel::getsAssignListDetail($id);  //调拨单明细
                $this->smarty->assign('detail', $detail);
                $this->smarty->assign('res', $res);
                $createUser =   getUserNameById($res['createUid']);
                $this->smarty->assign('createUser', $createUser);
                //$user           =   array('name'=>$userName, 'uid'=>$_SESSION['userId']);
                $this->smarty->assign('storeLists', $storeLists);
                $this->smarty->display('editAssignList.htm');
            }
            //$userName       =   getUserNameById($_SESSION['userId']); //获取用户名
            
        }
        
    }
    
    /**
     * whGoodsAssignView::view_addListProcess()
     * 仓库调拨新建处理
     * @return void
     */
    public function view_addListProcess(){
        $res    =   WhGoodsAssignAct::addList();
        $url    =   $_SERVER['HTTP_REFERER'];
        header('Location:'.$url.'&state='.$res['msg']);
    }
    

    /**
     * whGoodsAssignView::bulidNav()
     * 构建面包屑及二级菜单等相关信息
     * @param mixed $topTitle 标题
     * @param mixed $secondTitle 二级菜单名
     * @param string $secondLevel 二级菜单序号
     * @param mixed $topLevel   一级菜单序号
     * @param mixed $firstTitle 一级菜单名
     * @return void
     */
    public function bulidNav($topTitle, $secondTitle, $secondLevel = '', $topLevel = '', $firstTitle = ''){
        
        $topLevel       =   $topLevel ? $topLevel : $this->topLevel; //一级菜单的序号  0 开始
        $firstTitle     =   $firstTitle ? $firstTitle : $this->firTitle;
        		
		$navlist        =   array(  //面包屑
                        			array('url' => '', 'title' => $firstTitle),
                        			array('url' => '', 'title' => $secondTitle)
                        		);
		$secondlevel    =    $secondLevel ? $secondLevel : 51;

        $this->smarty->assign('toptitle', $topTitle);  //标题 
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $topLevel);
        $this->smarty->assign('secondlevel', $secondlevel);
    }

    /*
     * 构造sql搜索条件语句
     * 返回 sql条件语句字符串
     */
    private function buildWhereSql() {
        $where      =   ' and a.is_delete = 0 ';
        unset($_GET['mod'], $_GET['act']);
        if(empty($_GET)){
            $this->smarty->assign('status', 100);
            return $where.' and a.status = 100';
        }
        $keywords   =   $_GET['keywords'] ? trim($_GET['keywords']) : '';
        $keytype    =   $_GET['keytype'] ? trim($_GET['keytype']) : '';
        $status     =   $_GET['status'] ? intval(trim($_GET['status'])) : 100;
        $outStoreId =   $_GET['outStoreId'] ? intval(trim($_GET['outStoreId'])) : 0;
        $inStoreId  =   $_GET['inStoreId'] ? intval(trim($_GET['inStoreId'])) : 0;
        $startDate  =   $_GET['startDate'] ? strtotrim($_GET['startDate']) : 0;
        $endDate    =   $_GET['endDate'] ? trim($_GET['endDate']) : 0;
        
        if($keywords){
            if($keytype){
                $field  =   $keytype == 1 ? 'a.assignNumber' : 'b.sku';
                $where  .=  " and $field = '$keywords'";
                $this->smarty->assign('keywords', $keywords);
                $this->smarty->assign('keytype', $keytype);
            }
        }
        
        if($status && $status != 1 ){
            $where  .=  " and a.status='$status'";
            $this->smarty->assign('status', $status);
        }
        
        if($outStoreId){
            $where  .=  " and a.outStoreId='$outStoreId'";
            $this->smarty->assign('outStoreId', $outStoreId);
        }
        
        if($inStoreId){
            $where  .=  " and a.inStoreId='$inStoreId'";
            $this->smarty->assign('inStoreId', $inStoreId);
        }
        
        if($startDate){
            $startDate  =   strtotime('Y-m-d H:i:s', $startDate);
            $where  .=  " and a.createTime >='$startDate'";
            $this->smarty->assign('startDate', $startDate);
        }
        
        if($endDate){
            $endDate  =   strtotime('Y-m-d H:i:s', $endDate) + 86399;
            $where  .=  " and a.createTime <='$endDate'";
            $this->smarty->assign('endDate', $endDate);
        }
        
        return $where;
    }

	//http://www.localhost.cc/zhang/code/wh.valsun.cn/html/index.php?mod=dispatchBillQuery&act=getExpressRemark&id=1
	public function view_getExpressRemark() {
		$id	=	isset($_GET['id']) ? $_GET['id'] : '';
		if(empty($id)) {
			return false;
		}

		$data	=	CommonModel::getExpressRemark($id);
		if(empty($data)){
			echo '查询不到数据!';exit;
		}
		$total	=	0;
		foreach($data as $k => $v){
			$total	+=	$v['price'] * $v['amount'];
			$type	=	$v['type'];
		}
		$this->smarty->assign('data', $data);
		$this->smarty->assign('total', $total);
		$this->smarty->assign('type', $type);
        $this->smarty->display('expressRemark.htm');
	}
    
    public function view_export_data(){
        $export =   new WhGoodsAssignAct();
        $export->export_data();
    }
    
}