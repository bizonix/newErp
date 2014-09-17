<?php

/**
 * 异常订单扫描
 * @author 王长先
 */
class DispatchBillScanView extends CommonView {
    /*
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
    }

    /*
     * 显示异常订单输入界面
     */
    public function view_inputForm() {
        $navlist = array(//面包屑
                array('url' => '', 'title' => '出库'),
                array('url' => '', 'title' => '异常发货扫描'),
        );
        $this->smarty->assign('navlist', $navlist);
        $toplevel = 2;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = isset($_GET['secondlevel']) ? trim($_GET['secondlevel']) : '';
        if(empty($secondlevel)){
            $secondlevel = '213';   //当前的二级菜单
        }
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->display('dispatchbillscan.htm');
    }

}