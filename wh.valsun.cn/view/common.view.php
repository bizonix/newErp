<?php
/*
 * 公共视图基类
 * 继承baseview类
 */
class CommonView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->smarty->assign('curusername',$_SESSION['userId']?getUserNameById($_SESSION['userId']):''); //设置当前用户名
    }
}