<?php
/*
 * 公共视图基类
 * 继承baseview类
 */
class Pda_commonView extends Pda_baseView{
	/*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign('curusername',$_SESSION['userId']?getUserNameById($_SESSION['userId']):''); //设置当前用户名
    }
    
}