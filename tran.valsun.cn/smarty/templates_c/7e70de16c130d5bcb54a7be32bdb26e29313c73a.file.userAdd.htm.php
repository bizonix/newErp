<?php /* Smarty version Smarty-3.1.12, created on 2013-10-30 14:14:30
         compiled from "/data/web/tran.valsun.cn/html/template/userAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:18984433005270a3c65ebf91-29772453%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e70de16c130d5bcb54a7be32bdb26e29313c73a' => 
    array (
      0 => '/data/web/tran.valsun.cn/html/template/userAdd.htm',
      1 => 1383103635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18984433005270a3c65ebf91-29772453',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'joblists' => 0,
    'joblist' => 0,
    'modifyuser' => 0,
    'deptlists' => 0,
    'deptlist' => 0,
    'userself' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5270a3c6653e43_63534971',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5270a3c6653e43_63534971')) {function content_5270a3c6653e43_63534971($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/data/web/tran.valsun.cn/lib/template/smarty/plugins/modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
        您的位置：<a href="index.php?mod=user&act=index">用户信息管理</a>&nbsp;&gt;&gt;&nbsp;添加用户
    </div>    
    </div>          
    <div class="main">
    <h1>添加用户</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">姓名：</td>
                <td width="27%" align="left">
                  <input type="text" name="username" id="username" value=""/>
                  <span class="red">*</span>
                </td>
              <td align="right" width="13%">密码：</td>
                <td width="49%" align="left">
                <input type="password" name="password" id="password" value=""/>
                <span style="color:red;">用户密码，不低于六位</span>
                </td>
            </tr>
            <tr>
                <td align="right" width="11%">工号：</td>
                <td align="left">
                    <input type="text" name="jobno" id="jobno" value=""/>
                </td>
              <td align="right">联系电话：</td>
                <td align="left">
                    <input type="text" name="phone" id="phone" value="" maxlength="20"/>
                </td>
            </tr>
            <tr>
                <td align="right">Email：</td>
                <td colspan="3" align="left">
                    <input type="text" name="email" id="email" value="" size="35" maxlength="80"/>
                </td>
            </tr>
            <tr>
                <td align="right">是否独立权限：</td>
                <td align="left">
                    <label><input name="user_independence" type="radio" value="1"/>独立</label>
                    <label><input name="user_independence" type="radio" value="0" checked/>共享</label>
                <span class="red">*</span>
                </td>
              <td align="right" width="13%">用户状态：</td>
                <td align="left">
                    <label><input name="user_status" type="radio" value="1" checked/>有效</label>
                    <label><input name="user_status" type="radio" value="0"/>无效</label>
                <span class="red">*</span>
                </td>
            </tr>
            <tr>
                <td align="right">所属岗位权限：</td>
                <td align="left">
                <select name="userjob" id="userjob">
                    <option value=""> 请选择岗位 </option>
                    <?php  $_smarty_tpl->tpl_vars['joblist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['joblist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['joblists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['joblist']->key => $_smarty_tpl->tpl_vars['joblist']->value){
$_smarty_tpl->tpl_vars['joblist']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['joblist']->value['jobpower_id'];?>
|<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
|<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_path'];?>
" <?php if ($_smarty_tpl->tpl_vars['joblist']->value['job_id']==$_smarty_tpl->tpl_vars['modifyuser']->value['user_job']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_name'];?>
</option>
                    <?php }
if (!$_smarty_tpl->tpl_vars['joblist']->_loop) {
?>
                    <option value="" selected="selected"> 无岗位权限 </option>
                    <?php } ?>
                </select>
                <span class="red">*</span>
                </td>
              <td align="right">所属部门：</td>
                <td align="left">                
                <select name="userdept" id="userdept">
                    <option value=""> 请选择部门 </option>
                    <?php  $_smarty_tpl->tpl_vars['deptlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['deptlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deptlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['deptlist']->key => $_smarty_tpl->tpl_vars['deptlist']->value){
$_smarty_tpl->tpl_vars['deptlist']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['deptlist']->value['dept_id']==$_smarty_tpl->tpl_vars['userself']->value['user_dept']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_name'];?>
</option>
                    <?php }
if (!$_smarty_tpl->tpl_vars['deptlist']->_loop) {
?>
                    <option value=""> 无部门 </option>
                    <?php } ?>
                </select>
                <span class="red">*</span>
                </td>
            </tr>
             <tr>
                <td align="right">Token授权日期：</td>
                <td align="left">
                <input name="grantDate" readonly="true" id="grantDate" size="20" onclick="WdatePicker()" type="text" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['modifyuser']->value['user_token_grant_date'],'Y-m-d');?>
"/>
                <span class="red">*</span>
                </td>
               <td align="right">Token有效天数：</td>
                <td align="left">
                <input name="effectiveDate" id="effectiveDate" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_token_effective_date'];?>
"/>
                <span class="red">*</span>
                </td>
            </tr>                
            <tr>
                <td colspan="4" align="center">
                    <button name="button" type="submit" id="submit-btn" value="search" />提 交</button>
                    <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=user&act=index'"/>返 回</button>
                </td>
            </tr>
        </table>
    </form>
    </div>
    <div class="bottomvar"></div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>

$("#submit-btn").click(function(){
    var username,password,jobno,phone,email,independence,stat,userjob,userdept,grantDate,effectiveDate;
	username = $.trim($("#username").val());
    password = $.trim($("#password").val());
    jobno 	 = $.trim($("#jobno").val());
    phone	 = $.trim($("#phone").val());
    email	 = $.trim($("#email").val());
    independence 	= $('input[name="user_independence"]:checked').val();
    stat	 = $('input[name="user_status"]:checked').val();
    userjob	 = $.trim($("#userjob").val());
    userdept = $.trim($("#userdept").val());
    grantDate		= $.trim($("#grantDate").val());
    effectiveDate	= $.trim($("#effectiveDate").val());
	if(username == ''){
		alertify.error("亲,用户名不能为空!");
		$("#username").focus();
		return false;
	}
	if(password == '' || password.length < 5){
		alertify.error("亲,密码不能为空且长度不能低于6位!");
		$("#password").focus();
		return false;
	}
	if(userjob == ''){
		alertify.error("亲,所属岗位不能为空!");
		$("#userjob").focus();
		return false;
	}
	if(userdept == ''){
		alertify.error("亲,所属部门不能为空!");
		$("#userdept").focus();
		return false;
	}
	if(grantDate == ''){
		alertify.error("亲,Token授权日期不能为空!");
		$("#grantDate").focus();
		return false;
	}
	if(effectiveDate == ''){
		alertify.error("亲,Token有效天数不能为空!");
		$("#effectiveDate").focus();
		return false;
	}
	$("#submit-btn").html("提交中,请稍候...");
	$.post("index.php?mod=user&act=insert",{"username":username,"password":password,"jobno":jobno,"phone":phone,"email":email,"independence":independence,"stat":stat,"userjob":userjob,"userdept":userdept,"grantDate":grantDate,"effectiveDate":effectiveDate},function(rtn){
		if($.trim(rtn) == "ok"){
			alertify.success("亲,帐号添加成功,5秒后跳转到首页！"); 
			window.setTimeout(window.location.href = "index.php?mod=user&act=index",5000);        
		}else {
			$("#submit-btn").html("提 交");
			alertify.error("亲,帐号添加失败,请检查数据是否有异常！");        
		}
	});
});
function check(){
	return false;
}

</script><?php }} ?>