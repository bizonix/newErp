<?php
/*
 * 货品资料管理
 */
class GoodsInfoView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     *显示搜索页面 
     */
    public function view_showSearchForm(){
        
        $navlist = array(array('url'=>'','title'=>'首页'),              //面包屑数据
                        array('url'=>'','title'=>'货品资料管理'),
                );
        $toplevel = 0;
        
        $this->smarty->assign('secnev','1');
        
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('toptitle', '货品资料管理');
        $this->smarty->display('searchgoodsinfo.html');
    }
}

