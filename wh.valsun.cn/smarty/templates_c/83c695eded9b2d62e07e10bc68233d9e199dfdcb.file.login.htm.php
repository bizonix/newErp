<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 17:52:05
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\login.htm" */ ?>
<?php /*%%SmartyHeaderCode:288125318432d747e59-87719957%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '83c695eded9b2d62e07e10bc68233d9e199dfdcb' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\login.htm',
      1 => 1394099381,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '288125318432d747e59-87719957',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5318432d7f8213_53262219',
  'variables' => 
  array (
    'companyInfo' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5318432d7f8213_53262219')) {function content_5318432d7f8213_53262219($_smarty_tpl) {?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录</title>
<script language="javascript" src="js/jquery-1.8.3.js"></script>
<link href="http://misc.erp.valsun.cn/css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./css/alertify/alertify.core.css" />
<link rel="stylesheet" href="./css/alertify/alertify.default.css" />
<script language="javascript" type="text/javascript" src="./js/alertify/alertify.min.js"></script>
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
            	<form onsubmit="return check();">
				<table>
                	<tr>
                    	<td>
                        	<span>用户名：</span>
                            <span style="font-size:12px; color:#F00; float:right;" id="tips-username"></span>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<input name="username" type="text" id="username"/>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<span>登录密码：</span>
                            <span style="font-size:12px; color:#F00; float:right;" id="tips-password"></span>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<input name="password" type="password" id="password"/>
                        </td>
                    </tr>
					<!--tr>
                    	<td>
                        	<span>所属公司：</span>
                            <span style="font-size:12px; color:#F00; float:right;" id="tips-password"></span>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<select name="companyId" id="companyId">
                            <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['companyInfo']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['companyId'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['companyName'];?>
</option>
                            <?php }
if (!$_smarty_tpl->tpl_vars['value']->_loop) {
?>
                            	<option value="">未获取公司列表</option>
                            <?php } ?>
                            </select>
                        </td>
                    </tr-->
                    <tr>
                    	<td class="go">
                        	<input type="submit" value="登录" id="login-btn"/>
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
                            	<input class="reset" type="reset" value="重置" id="reset-btn"/>
                            </span>
                        </td>
                    </tr>
                </table>
				</form>
            </div>
        </div>
    </div>
</body>
</html>
<script>
$(function(){
	$("#username").focus();
})

$("#login-btn").click(function(){
    var username,password;
    username = $.trim($("#username").val());
    password = $.trim($("#password").val());
	//companyId = $.trim($("#companyId").val());
	if(username.indexOf("@") == -1){
		username = username+"@sailvan.com";
	}
	if(username == ''){
		$("#tips-username").html("用户名不能为空!");
		$("#username").focus();
		return false;
	}else {
		$("#tips-username").html("");
	}
	if(password == ''){
		$("#tips-password").html("密码不能为空!");
		$("#password").focus();
		return false;
	}else {
		$("#tips-password").html("");
	}/*
	if(companyId == ''){
		$("#companyId").focus();
		return false;
	}*/
	$("#login-btn").val("登录中,请稍候...");
	$.post("index.php?mod=public&act=userLogin",{"username":username, "password":password,},function(rtn){
		if($.trim(rtn) == "ok"){
			//alertify.success("亲,登录成功,1秒后跳转到首页！"); 
			window.setTimeout(window.location.href = "index.php?mod=public&act=login",1000);        
		}else {
			$("#login-btn").val("登录");
			alertify.error("亲,登录失败,请检查帐号密码是否输入正确！");        
		}
	});
});
function check(){
	return false;
}

</script>

<?php }} ?>