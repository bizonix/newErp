<?php

if(!isset($_SESSION)){
    session_start();
}

include_once WEB_PATH.'action/user.action.php';
/**
 * 名称：LoginView
 * 功能： 消息系统登入 视图
 * 版本：V 1.0
 * 日期：2013/10/12
 * 作者： wxb
 * */

class loginView {
	 public $smarty;

     public function __construct() {
     	require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
     	$this->smarty = new Smarty;
     	$this->smarty->template_dir 	= WEB_PATH.'html/template/';
     	$this->smarty->compile_dir 		= WEB_PATH.'smarty/templates_c/';
     	$this->smarty->config_dir 		= WEB_PATH.'smarty/configs/';
     	$this->smarty->cache_dir 		= WEB_PATH.'smarty/cache/';
     	$this->smarty->debugging 		= false;
     	$this->smarty->caching 			= false;
     	$this->smarty->cache_lifetime 	= 120;
     }

    public function view_index() {
    	$this->smarty->assign("title", "Notice System");
    	$this->smarty->display('login.htm');
    }

    public function view_logout() {
    	session_destroy();
    	header('Location: index.php?mod=public&act=userLogin');
    }
}
?>