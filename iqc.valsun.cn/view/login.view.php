<?php
/*
 * iqc系统管理登陆页
 */
class LoginView extends BaseView{
	
    /*
     * 登陆页面
     */
    public function view_index(){
        if( isset($_SESSION['userId']) ){   //用户已经登陆
            header('location:index.php?mod=iqc&act=iqcList');exit;
        }else{
			$this->smarty->assign('title','iqc系统登陆');
			$this->smarty->display('login.html');
        }
    }
    
    /*
     * 退出登陆
     */
    public function view_logout(){
        session_destroy();   //退出
        header('location:index.php?mod=login&act=index');
    }
}

