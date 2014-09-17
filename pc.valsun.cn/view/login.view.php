<?php
class LoginView extends BaseView{

    public function view_index(){
		$this->smarty->assign('title','产品中心登陆');
		$this->smarty->display('login.htm');
    }

    /*
     * 退出登陆
     */
    public function view_logout(){
        session_destroy();   //退出
        header('location:index.php?mod=login&act=index');
    }

}
?>