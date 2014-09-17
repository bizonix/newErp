<?php
/*
 * 仓库系统登陆页面view类
 * 涂兴隆
 */

class LoginView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     * 用户登陆模板页面
     */
    public function view_login(){
        $this->smarty->assign('toptitle','系统登陆页面');
        $this->smarty->display('login.html');
    }
    
    /*
     * 用户退出
     */
    public function view_logout(){
        session_destroy();   //退出
        header('location:index.php?mod=login&act=login');
        exit;
    }
}

?>
