<?php /* Smarty version Smarty-3.1.12, created on 2014-04-04 15:37:01
         compiled from "/data/web/subscription.valsun.cn/html/template/v1/header.html" */ ?>
<?php /*%%SmartyHeaderCode:233787093533919cec8bb91-51769252%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c8f53cecc88bb40f1a37d7d6e6fe45a040119772' => 
    array (
      0 => '/data/web/subscription.valsun.cn/html/template/v1/header.html',
      1 => 1396597012,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '233787093533919cec8bb91-51769252',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_533919cecc4d47_96844571',
  'variables' => 
  array (
    'title' => 0,
    'dy_cho' => 0,
    'gl_cho' => 0,
    '_username' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_533919cecc4d47_96844571')) {function content_533919cecc4d47_96844571($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/page.css" rel="stylesheet" type="text/css"/>
<link href="css/jquery.alert.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script language="JavaScript" src="js/user_mail_handle.js" type="text/javascript"></script>
<script language="JavaScript" src="js/admin_handle.js" type="text/javascript"></script>
<script language="JavaScript" src="js/addMailPower.js" type="text/javascript"></script>
<script language="JavaScript" src="js/jquery.easydrag.js" type="text/javascript"></script>
<script language="JavaScript" src="js/jquery.alert.js" type="text/javascript"></script>
</head>
<body>
	<div class="container" id="container">
    	<div class="content">
        	<div class="header">
            	<div class="logo">
                	邮件订阅
                </div>
                <div class="onevar">
                	<ul>
                    	<li>
                        	<a href="index.php?mod=MailShow&act=showUserMail" class="<?php echo $_smarty_tpl->tpl_vars['dy_cho']->value;?>
">订阅</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=MailManage&act=showMailPower" class="<?php echo $_smarty_tpl->tpl_vars['gl_cho']->value;?>
">管理</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                	<a href="#"><?php echo $_smarty_tpl->tpl_vars['_username']->value;?>
</a>
                    <a href="index.php?mod=public&act=logout" style="background-image:none;">退出</a>
                </div>
            </div><?php }} ?>