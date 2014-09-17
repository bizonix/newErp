<?php

/** 
 * @author 涂兴隆
 * 
 */
class waitWeighingView extends CommonView
{

    /**
     * 构造函数
     */
    public function __construct ()
    {
        parent::__construct();
    }
    
    /*
     * 待称重列表
     */
    public function view_waitWeighingList(){
        $pagesize = 100;    //页面大小
        
        $statusar = array(PKS_WWEIGHING, PKS_WWEIGHING_EX, PKS_INLANDWWEIGHING);
        $statusstr = implode(',', $statusar);
        
        $packing_obj = new PackingOrderModel();
        $count = $packing_obj->getRecordsNumByStatus($statusar);      //获得当前状态为待包装的发货单总数量
        
        $pager = new Page($count, $pagesize);    //分页对象
        
        $billlist = $packing_obj->getBillList(' and orderStatus in ('.$statusstr.') order by po.id '.$pager->limit);
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
                array('url' => '', 'title' => '待包装称重'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待包装称重';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '26';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        $this->smarty->display('waitweighinglist.htm');
    }
    
    /*
     * 显示称重扫描
     */
    public function view_weighingForm(){
        
        $wo_obj = new WhouseOperatorModel();
        $packinguser = $wo_obj->getPackingUserList();   //包装员列表
        $this->smarty->assign('packinguser', $packinguser);
        
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '待包装称重'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待包装称重';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '26';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        $this->smarty->display('weighingform.htm');
    }
    
    /*
     * 显示称重扫描 <芬哲>
    */
    public function view_weighingFormInland(){
    
        $wo_obj = new WhouseOperatorModel();
        $packinguser = $wo_obj->getPackingUserList();   //包装员列表
        $this->smarty->assign('packinguser', $packinguser);
    

        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '待称重(芬哲)'),
        );
        $this->smarty->assign('navlist', $navlist);
    
        $toptitle = '待称重';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
    
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
    
        $secondlevel = '26';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
    
        $this->smarty->assign('secnev', 3);     //二级导航
        $this->smarty->display('weighingformfz.htm');
    }
}

?>