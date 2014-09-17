<?php

/** 
 * 待包装
 */
class waitpackingView extends CommonView
{

    /**
     * 构造函数
     */
    public function __construct ()
    {
        parent::__construct();
    }
    
    /*
     * 待包装列表
     */
    public function view_waitPackingList(){
     
        $pagesize = 100;    //页面大小
        
        $statusar = array(PKS_WPACKING);
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
                array('url' => '', 'title' => '待包装'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待包装';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '25';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
        $this->smarty->display('waitpackinglist.htm');
    }
    
    /*
     * 包装扫描页面
     */
    public function view_packingform() {
        
        $navlist = array(           //面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => 'index.php?mod=waitpacking&act=waitPackingList', 'title' => '待包装'),
                array('url' => '', 'title' => '包装扫描'),
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '包装扫描';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '25';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3);     //二级导航
    	$this->smarty->display('packingform.htm');
    }
}

?>