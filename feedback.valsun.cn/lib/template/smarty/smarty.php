<?php
   // if(!defined('BASEPATH')) exit('No direct script access allowed');
	require_once('Smarty.class.php');
    class ismarty extends Smarty{
        public function __construct(){
            parent::__construct();

            //$this->cache_lifetime = 30*24*3600; //更新周期
            $this->caching			= false; //是否使用缓存，项目在调试期间，不建议启用缓存
            $this->template_dir		= WEB_PATH.'/html/v1'; //设置模板目录
            $this->compile_dir		= WEB_PATH.'/html/v1/templates_c'; //设置编译目录
            //$this->cache_dir		= WEB_PATH.'/html/'; //缓存文件夹
            $this->use_sub_dirs		= false;   //子目录变量（是否在缓存文件夹中生成子目录）
            $this->left_delimiter	= '{';
            $this->right_delimiter	= '}';
        }
    }