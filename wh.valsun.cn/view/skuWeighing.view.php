<?php
/**
 * 料号称重
 * @author hws
 */
class SkuWeighingView extends CommonView {
    /* 构造函数*/
    public function __construct() {
        parent::__construct();
    }
	public function view_skuWeighing(){
		$navlist = array(array('url'=>'','title'=>'出库'),              //面包屑数据
				 array('url'=>'index.php?mod=skuWeighing&act=skuWeighing','title'=>'料号称重'),
				 array('url'=>'','title'=>'料号称重'),
		);
        $secnev = 3;
        $toplevel = 2;
        $secondlevel = 211;
        
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('now_time', date('Y/m/d'));
        $this->smarty->assign('secnev',  $secnev);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        $this->smarty->display('skuWeighing.htm');
	}
}
?>