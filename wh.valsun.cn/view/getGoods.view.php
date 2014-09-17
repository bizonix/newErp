<?php
/** 
 * 配货逻辑
 * @author 涂兴隆
 * 
 */
class GetGoodsView extends CommonView {

    /**
     * 构造函数
     */
    public function __construct (){
        parent::__construct();
    }
    
    /*
     * 待配货单列表
     */
    public function view_OrderList(){
        
        $pagesize = 100;    //页面大小
        
        $packing_obj = new PackingOrderModel();
        $count = $packing_obj->getRecordsNumByStatus(array(PKS_WGETGOODS));      //获得当前状态为待配货的发货单和配货单总数量
        
        $pager = new Page($count, $pagesize);    //分页对象
        
        $billlist = $packing_obj->getBillList(' and orderStatus='.PKS_WGETGOODS.' order by po.id '.$pager->limit);
        $this->smarty->assign('billlist', $billlist);
        
		$ShipingTypeList = CommonModel::getShipingTypeListKeyId();
		$count = count($billlist);
		for($i=0;$i<$count;$i++){
			$billlist[$i]['shipingname'] = isset($ShipingTypeList[$billlist[$i]['transportId']])?$ShipingTypeList[$billlist[$i]['transportId']]:'';
		}
		
		$acc_id_arr = array();
		foreach($billlist as $key=>$valbil){
			if(!in_array($valbil['accountId'],$acc_id_arr)){
				array_push($acc_id_arr,$valbil['accountId']);
			}
		}
		$salesaccountinfo = CommonModel::getAccountInfo($acc_id_arr);
		$this->smarty->assign('salesaccountinfo', $salesaccountinfo);
   
        if ($count > $pagesize) {       //分页链接
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '待配货'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待配货订单';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '23';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        
        $this->smarty->display('gotgoodslist.htm');    //输出页面
    }
    
    /*
     * 配货扫描页面快递
     */
    public function view_GetGoodsScanPageEX(){
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '配货扫描(快递)'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '配货扫描<快递>';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '23';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        
        $this->smarty->display('getgoodsscan.htm');
    }
    
    /*
     * 配货扫描页面非快递
     */
    public function view_GetGoodsScanPageSm(){
    
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '配货扫描(非快递)'),
        );
        $this->smarty->assign('navlist', $navlist);
    
        $toptitle = '配货扫描<非快递>';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
    
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
    
        $secondlevel = '23';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
    
        $this->smarty->assign('secnev', 3);     //二级导航
    
        $this->smarty->display('getgoodsscansm.htm');
    }
    
    /*
     * 配货扫描页面 国内
     */
    public function view_GetGoodsScanPageInland(){
    
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '配货扫描(国内)'),
        );
        $this->smarty->assign('navlist', $navlist);
    
        $toptitle = '配货扫描<国内>';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
    
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
    
        $secondlevel = '23';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
    
        $this->smarty->assign('secnev', 3);     //二级导航
    
        $this->smarty->display('getgoodsscaninland.htm');
    }
}

?>