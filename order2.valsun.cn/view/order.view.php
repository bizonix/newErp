<?php
/**
 * 订单信息查询
 * @author herman.xi
 * @modify by lzx ,date 20140526
 */
class OrderView extends BaseView {
    public $tablekey;
    public $pageclass;
    public $pageformat;
    public $omOrderList;
    public $pagination;
    public $statusList;
    public $oStatus;
    public $oType;
    public $Conversion;         //新旧料号数组集合
    public $editMenu1;          //编辑显示权限
    public $editMenu2;          //编辑显示权限
    public $competence;
    /**
     * 构造函数
     */
    public function __construct() {
    	parent::__construct();
        $this->getParameters();         //首页相关参数的设置
    }

    /**
     * 订单首页
	 * @author dy
     */
    public function view_index() {
        $this->smarty->assign('pagination',$this->pagination);
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('order-index'));
        $this->smarty->assign('ostatus',$this->oStatus);
        $this->smarty->assign('otype',$this->oType);
        $this->smarty->assign('omOrderList', $this->omOrderList);
        $this->smarty->assign('show_page', $this->pageclass->fpage($this->pageformat));
        $this->smarty->assign('tablekey',$this->tablekey);
        $this->smarty->assign('statusList',$this->statusList);
        $this->smarty->assign('conversion',$this->Conversion);
        $this->smarty->assign('editMenu1',$this->editMenu1);
        $this->smarty->assign('editMenu2',$this->editMenu2);
        $this->smarty->assign('competence',$this->competence);
        $this->smarty->display('orderindex.htm');
    }

    /**
     * 订单列表页面视图加载
	 * @author lzx
     */
    public function view_list() {
    	$transportation = M('InterfaceTran')->getCarrierList(2);
    	$transportationList = array();
    	foreach($transportation as $tranValue){
    		$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
    	}
        $this->smarty->assign('pagination',$this->pagination);
    	$this->smarty->assign('toplevel',2);
        $this->smarty->assign('ostatus',$this->oStatus);
        $this->smarty->assign('otype',$this->oType);
		$this->smarty->assign('transportation', $transportation);	//API接口数据，单独模型可以不走action
		$this->smarty->assign('transportationList', $transportationList);
		$this->smarty->assign('omOrderList', $this->omOrderList);
		$this->smarty->assign('plataccount', get_userplatacountpower(get_userid()));
        $this->smarty->assign('show_page', $this->pageclass->fpage($this->pageformat));
        $this->smarty->assign('tablekey',$this->tablekey);
        $this->smarty->assign('conversion',$this->Conversion);
        $this->smarty->assign('editMenu1',$this->editMenu1);
        $this->smarty->assign('editMenu2',$this->editMenu2);
        $this->smarty->assign('competence',$this->competence);
        $this->smarty->display('orderlist.htm');

    }
    
    public function view_showgetNum(){
    	$this->smarty->assign('toplevel',0);
    	$this->smarty->assign('ostatus',$this->oStatus);
    	$this->smarty->assign('otype',$this->oType);
    	$this->smarty->assign('plataccount', get_userplatacountpower(get_userid()));
    	$this->smarty->display('orderStatistics.htm');
    }
    
   /** 展示订单数
     */
    public function view_showNum(){
    	$platformNa   = $_REQUEST['platformId'];
    	$platformNa   = get_platnamebyid($platformNa);
    	$countData    = A('Order')->act_getOrdersByPlatForm();
    	$this->smarty->assign('toplevel',0);
    	$this->smarty->assign('ostatus',$this->oStatus);
    	$this->smarty->assign('otype',$this->oType);
		$this->smarty->assign('plataccount', get_userplatacountpower(get_userid()));
        $this->smarty->assign('tablekey',$this->tablekey);
    	$this->smarty->assign('platformNa',$platformNa);
    	$this->smarty->assign('countData',$countData);
    	$this->smarty->display('orderStatistics.htm');
    }
    /**
     * 首页订单相关参数设置
     */
    public function getParameters(){
        F('order');
        $OA = A('Order');
        $this->Conversion   = M('InterfacePc')->getSkuConversionArr();  // 一次性读取新旧料号的对应
        $orderCount         = $OA->act_getOrderCount();
        $perPage 	        = $OA->act_getPerpage();
        $this->statusList   = C('ORDER_STATUS');
        $this->tablekey	    = $OA->act_getTableKey();
        $this->pageclass 	= new Page($orderCount, $perPage, '', 'CN');
        $this->pageformat   = $orderCount>$perPage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
        $this->omOrderList  = $OA->act_getOrderList();
        $this->oStatus      = A('statusMenu')->act_getOrderStatusByGroupId();
        $oStatusId          = $_GET['ostatus'];
        $editOrderOptions   = C('EDITORDEROPTIONS');
        $this->editMenu1    = $editOrderOptions['edit1'];
        $this->editMenu2    = $editOrderOptions['edit2'];
        $competenceAll      = A('UserCompetence')->act_getCompetenceByUserId(get_userid());
        $this->competence   = explode(',',$competenceAll['visible_editorder']);
        if(!empty($oStatusId) && $oStatusId != 0){
            $this->oType    = A('statusMenu')->act_getOrderStatusByGroupId($oStatusId);
        }
        $pagination         = false;
        if(sizeof($this->omOrderList)){
            $pagination     = true;
        }
        $this->pagination   = $pagination;
    }

    /**
     * 获取二级菜单的栏目
     */
    public  function view_getotype(){
        $id = $_POST['id'];
        $this->ajaxReturn(A('statusMenu')->act_getOrderStatusByGroupId($id),A('statusMenu')->act_getErrorMsg());
    }
  	
    
}