<?php /* Smarty version Smarty-3.1.12, created on 2013-09-25 11:36:15
         compiled from "D:\wamp\www\crm.valsun.cn\html\template\v1\login.htm" */ ?>
<?php /*%%SmartyHeaderCode:858952425a2f1548c7-09701116%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '559ec716041d53b9df9be7ed19763722827fd6a2' => 
    array (
      0 => 'D:\\wamp\\www\\crm.valsun.cn\\html\\template\\v1\\login.htm',
      1 => 1380074411,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '858952425a2f1548c7-09701116',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52425a2f1a1106_31324554',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52425a2f1a1106_31324554')) {function content_52425a2f1a1106_31324554($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./js/jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/login.js"></script>
</head>

<body class="loginbody">
	<div class="loginmain">
    	<div class="box">
        	<div class="loginlogo">
            	<p>
                	华成平台
                </p>
            </div>
            <div class="userlogin">
            	<table>
                	<tr>
                    	<td>
                        	<p>用户名：</p>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<input name="username" id="username" type="text" />
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<p>登录密码：</p>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<input name="password" id="password" type="password" />
                        </td>
                    </tr>
					<tr>
						<td>
							<span id="errormess" class="error"></span>
						</td>
					</tr>
                    <tr>
                    	<td class="go">							
                        	<a href="#" onclick="login()">登录</a>
                        </td>
                    </tr>
                    <tr>
                    	<td class="remenber">
                        	<span class="right">
                        		<input name="" type="checkbox" value="" id="re" />
                                <label for="re" class="rem">
                                	记住我
                                </label>
                            </span>
                            <span>
                            	<a href="#" id="reset">重置</a>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php }} ?>