<?php
/*
 * 运输方式系统管理登陆页
 * 涂兴隆  2013/7/18
 */

class LoginView{
    private $template_path = 'null';
    
    /*
     * 构造函数 初始化模板路径和模板对象
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 登陆页面
     */
    public function view_index(){
        if( isset($_SESSION['userId']) ){   //用户已经登陆
            header('location:index.php?mod=query&act=showform');exit;
        }else{
            //var_dump($this->tp_obj);exit;
            $this->tp_obj->set_file('loginfile',"login.html");
            $this->tp_obj->set_var('title','运输方式系统登陆');
            $this->tp_obj->parse('login', 'loginfile');
            $this->tp_obj->p('login');  //输出内容
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

