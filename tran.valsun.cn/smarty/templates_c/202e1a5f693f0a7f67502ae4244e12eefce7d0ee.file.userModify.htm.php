<?php /* Smarty version Smarty-3.1.12, created on 2013-10-23 17:46:51
         compiled from "/data/web/trans.valsun.cn/html/template/userModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:7688889815264806562c0a2-13646636%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '202e1a5f693f0a7f67502ae4244e12eefce7d0ee' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/userModify.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7688889815264806562c0a2-13646636',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526480656f8783_56038711',
  'variables' => 
  array (
    'modifyuser' => 0,
    'joblists' => 0,
    'joblist' => 0,
    'deptlists' => 0,
    'deptlist' => 0,
    'basepowers' => 0,
    'groupname' => 0,
    '_actlist' => 0,
    'actlist' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526480656f8783_56038711')) {function content_526480656f8783_56038711($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/data/web/trans.valsun.cn/lib/template/smarty/plugins/modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=user&act=index">用户信息管理</a>&nbsp;&gt;&gt;&nbsp;用户编辑
    </div>     
    </div>          
    <div class="main">
    <h1>修改用户资料</h1>
    <form id="form" action="index.php?mod=user&act=update" method="post" onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">姓名：</td>
                <td width="27%" align="left">
                  <input type="text" name="username" id="username" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_name'];?>
"/>
                  <span class="red">*</span>
                </td>
              <td align="right" width="13%">密码：</td>
                <td width="49%" align="left">
                <input type="password" name="password" id="password" value=""/>
                <span style="color:red;">密码为空，则不修改密码</span>
                </td>
            </tr>
            <tr>
                <td align="right" width="11%">工号：</td>
                <td align="left">
                    <input type="text" name="jobno" id="jobno" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_job_no'];?>
"/>
                </td>
              <td align="right">联系电话：</td>
                <td align="left">
                    <input type="text" name="phone" id="phone" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_phone'];?>
" maxlength="20"/>
                </td>
            </tr>
            <tr>
                <td align="right">Email：</td>
                <td colspan="3" align="left">
                    <input type="text" name="email" id="email" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_email'];?>
" size="35" maxlength="80"/>
                </td>
            </tr>
            <tr>
                <td align="right">是否独立权限：</td>
                <td align="left">
                    <label><input name="user_independence" type="radio" value="1"<?php if ($_smarty_tpl->tpl_vars['modifyuser']->value['user_independence']=='1'){?> checked<?php }?>/>独立</label>
                    <label><input name="user_independence" type="radio" value="0"<?php if ($_smarty_tpl->tpl_vars['modifyuser']->value['user_independence']=='0'){?> checked<?php }?>/>共享</label>
                <span class="red">*</span>
                </td>
              <td align="right" width="13%">用户状态：</td>
                <td align="left">
                    <label><input name="user_status" type="radio" value="1"<?php if ($_smarty_tpl->tpl_vars['modifyuser']->value['user_status']=='1'){?> checked<?php }?>/>有效</label>
                    <label><input name="user_status" type="radio" value="0"<?php if ($_smarty_tpl->tpl_vars['modifyuser']->value['user_status']=='0'){?> checked<?php }?>/>无效</label>
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
" <?php if ($_smarty_tpl->tpl_vars['deptlist']->value['dept_id']==$_smarty_tpl->tpl_vars['modifyuser']->value['user_dept']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_name'];?>
</option>
                    <?php }
if (!$_smarty_tpl->tpl_vars['deptlist']->_loop) {
?>
                    <option value=""> 无岗位权限 </option>
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
                <td align="right">用户token：</td>
                <td align="left">
                <input name="userToken" id="userToken" disabled="disabled" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_token'];?>
" size="35" maxlength="32"/>
                <span style="color:red;">不可编辑</span>
                </td>
              <td align="right">用户注册时间：</td>
                <td align="left">
                <input type="text" disabled="disabled" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['modifyuser']->value['user_register_time'],'Y-m-d');?>
"  size="20"/>
                <span style="color:red;">不可编辑</span></td>
            </tr>                
            <tr>            	
                <td colspan="4">                
                <table width="100%" height="100%" border="0" class="action">
                    <tr>
                        <td align="center" width="10%">ActionGroup</td><td align="center">Action</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['_actlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_actlist']->_loop = false;
 $_smarty_tpl->tpl_vars['groupname'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['basepowers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_actlist']->key => $_smarty_tpl->tpl_vars['_actlist']->value){
$_smarty_tpl->tpl_vars['_actlist']->_loop = true;
 $_smarty_tpl->tpl_vars['groupname']->value = $_smarty_tpl->tpl_vars['_actlist']->key;
?>
                    <tr>
                        <td align="left">
                        <label title="<?php echo $_smarty_tpl->tpl_vars['groupname']->value;?>
"><input id="ActionGroup" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['groupname']->value;?>
" style="vertical-align:middle"/><?php echo $_smarty_tpl->tpl_vars['_actlist']->value['groupdesc'];?>
<br/><?php echo $_smarty_tpl->tpl_vars['groupname']->value;?>
</label></td>
                        <td align="left">
							<?php  $_smarty_tpl->tpl_vars['actlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['actlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_actlist']->value['action']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['actlist']->key => $_smarty_tpl->tpl_vars['actlist']->value){
$_smarty_tpl->tpl_vars['actlist']->_loop = true;
?>
                       	  <label title="<?php echo $_smarty_tpl->tpl_vars['actlist']->value['actionname'];?>
"><input type="checkbox" id="action" <?php if ($_smarty_tpl->tpl_vars['actlist']->value['actioncheck']=='1'){?>checked<?php }?> style="vertical-align:middle" name="<?php echo $_smarty_tpl->tpl_vars['groupname']->value;?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['actlist']->value['actionname'];?>
"/><?php echo $_smarty_tpl->tpl_vars['actlist']->value['actionname'];?>
(<?php echo $_smarty_tpl->tpl_vars['actlist']->value['actiondesc'];?>
)</label>
                            <?php } ?>
                      </td>
                  </tr>
                    <?php }
if (!$_smarty_tpl->tpl_vars['_actlist']->_loop) {
?>
                   <tr><td colspan=2>you can't modify the permissions!</td></tr>
                    <?php } ?>
                </table>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">
					<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['modifyuser']->value['user_token'];?>
" name="usertoken" id="usertoken"/>
                    <button name="button" type="submit" id="submit-btn"/>提 交</button>
                    <button name="button" type="button" id="bottom" onclick="location.href='index.php?mod=user&act=index'"/>返 回</button>
                </td>
            </tr>
        </table>
    </form>
    </div>
    <div class="bottomvar"></div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>

function check(){
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
    userToken		= $.trim($("#userToken").val());

	if(username == ''){
		alertify.error("亲,用户名不能为空!");
		$("#username").focus();
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
	return true;
}

$(document).ready( function() { 
  $("#ActionGroup").live('click',function(){
	  if($(this).attr("checked"))
	  {
		$(this).parent().parent().nextAll().find('input').attr('checked','checked');
	  }else
	  {
		  $(this).parent().parent().nextAll().find('input').removeAttr("checked");
	  }
  });  
  
  $("#action").live('click',function(){
	  if(!$(this).attr("checked"))
	  {
		$(this).parent().parent().prevAll().find('input').removeAttr("checked");
	  }
  });	
});
</script><?php }} ?>