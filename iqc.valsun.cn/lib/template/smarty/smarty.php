<?php
   // if(!defined('BASEPATH')) exit('No direct script access allowed');
	require_once('Smarty.class.php');
    class ismarty extends Smarty{
        public function __construct(){
            parent::__construct();

            //$this->cache_lifetime = 30*24*3600; //��������
            $this->caching			= false; //�Ƿ�ʹ�û��棬��Ŀ�ڵ����ڼ䣬���������û���
            $this->template_dir		= WEB_PATH.'/html/v1'; //����ģ��Ŀ¼
            $this->compile_dir		= WEB_PATH.'/html/v1/templates_c'; //���ñ���Ŀ¼
            //$this->cache_dir		= WEB_PATH.'/html/'; //�����ļ���
            $this->use_sub_dirs		= false;   //��Ŀ¼�������Ƿ��ڻ����ļ�����������Ŀ¼��
            $this->left_delimiter	= '{';
            $this->right_delimiter	= '}';
        }
    }