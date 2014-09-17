<?php
/**
 * 面单打印界面
 * @author Gary
 * @date 2014-08-05
 * @version 1.0
 */
class OrderPrintView extends CommonView {
    private static $toplevel    =   2; //一级列表排序
    private static $secondlevel =   40; //二级列表排序
    private static $navlist     =   array(); //面包屑数组
    private static $toptitle    =   '面单打印'; //标题

    /**
     * OrderPrintView::view_index()
     * 面单首页
     * @author Gary
     * @return void
     */
    public function view_index(){
        $navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => '面单打印'),
		);
 	    self::bulidNav($navlist);
        $this->smarty->display('orderPrint.htm');
    }
    /**
     * OrderPrintView::view_orderPrint()
     * 打印发货单面单及拍照界面
     * @return void
     */
    public function view_orderPrint(){
        $navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => '面单打印及拍照'),
		);
 	    self::bulidNav($navlist);
        $printIndex     =   intval($_GET['printIndex']);
        $pageIndex      =   trim($_GET['pageIndex']);
        $now_time       =   date('Y/m/d');
        $this->smarty->assign('printIndex', $printIndex);
        $this->smarty->assign('pageIndex', $pageIndex);
        $this->smarty->assign('now_time', $now_time);
        $this->smarty->display('orderPrint_photo.htm');
    }

    /**
     * whGoodsAssignView::bulidNav()
     * 构建面包屑及二级菜单等相关信息
     * @param array $navlist 标题
     * @return void
     */
    public function bulidNav($navlist, $toptitle = ''){
        
        $toptitle   =   $toptitle ? $toptitle : self::$toptitle;

        $this->smarty->assign('toptitle', $topTitle);  //标题 
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', self::$toplevel);
        $this->smarty->assign('secondlevel', self::$secondlevel);
    }
}