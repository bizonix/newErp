<?php /* Smarty version Smarty-3.1.12, created on 2014-03-01 16:35:45
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\user.htm" */ ?>
<?php /*%%SmartyHeaderCode:3168953119be1575032-36832650%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b00685afb8a0353754882d419e8d4275320b024d' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\user.htm',
      1 => 1393658410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3168953119be1575032-36832650',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pageStr' => 0,
    'g_username' => 0,
    'g_userjob' => 0,
    'joblists' => 0,
    'joblist' => 0,
    'deptlists' => 0,
    'deptlist' => 0,
    'g_userdept' => 0,
    'g_userindependence' => 0,
    'g_userstatus' => 0,
    'g_mod' => 0,
    'g_act' => 0,
    'g_page' => 0,
    'userlists' => 0,
    'userlist' => 0,
    'runmsg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53119be16b5835_18437309',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53119be16b5835_18437309')) {function content_53119be16b5835_18437309($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'E:\\erpNew\\order.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
    <div class="pathvar">
        您的位置：<a href="index.php?mod=user&act=index">用户信息管理</a>&nbsp;&gt;&gt;&nbsp;用户列表
    </div>
	<div class="pagination"><?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>
</div>
</div>
<div class="servar">
	<form name="form" id="sform" action="" enctype="text/plain" method="get">
        <span>
            用户名：<input type="text" name="username" value="<?php echo $_smarty_tpl->tpl_vars['g_username']->value;?>
"/>
        </span>
        <span>
            所属岗位：
            <select name="userjob" id="userjob">
                <option value=""> 请选择岗位 </option>
				<?php $_smarty_tpl->tpl_vars['g_userjob'] = new Smarty_variable(explode("|",$_smarty_tpl->tpl_vars['g_userjob']->value), null, 0);?>
				<?php $_smarty_tpl->tpl_vars['g_userjob'] = new Smarty_variable($_smarty_tpl->tpl_vars['g_userjob']->value[1], null, 0);?>
                <?php  $_smarty_tpl->tpl_vars['joblist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['joblist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['joblists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['joblist']->key => $_smarty_tpl->tpl_vars['joblist']->value){
$_smarty_tpl->tpl_vars['joblist']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['joblist']->value['jobpower_id'];?>
|<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['joblist']->value['job_id']==$_smarty_tpl->tpl_vars['g_userjob']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_name'];?>
</option>
                <?php }
if (!$_smarty_tpl->tpl_vars['joblist']->_loop) {
?>
                <option value="" selected="selected"> 无岗位权限 </option>
                <?php } ?>
            </select>
        </span>
        <span>
            所属部门：
            <select name="userdept" id="userdept">
                <option value=""> 请选择部门 </option>
                <?php  $_smarty_tpl->tpl_vars['deptlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['deptlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deptlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['deptlist']->key => $_smarty_tpl->tpl_vars['deptlist']->value){
$_smarty_tpl->tpl_vars['deptlist']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['deptlist']->value['dept_id']==$_smarty_tpl->tpl_vars['g_userdept']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_name'];?>
</option>
                <?php }
if (!$_smarty_tpl->tpl_vars['deptlist']->_loop) {
?>
                <option value=""> 无岗位权限 </option>
                <?php } ?>
            </select>
        </span>
        <span>
            权限类别：
            <select name="userindependence">
                <option selected value="*">- 全部 -</option>
                <option value="1" <?php if ($_smarty_tpl->tpl_vars['g_userindependence']->value=='1'){?>selected="selected"<?php }?>>- 独立 -</option>
                <option value="0" <?php if ($_smarty_tpl->tpl_vars['g_userindependence']->value=='0'){?>selected="selected"<?php }?>>- 共享 -</option>
            </select>
        </span>
        <span>
            用户状态：
            <select name="userstatus">
                <option selected value="*">- 全部 -</option>
                <option value="1" <?php if ($_smarty_tpl->tpl_vars['g_userstatus']->value=='1'){?>selected="selected"<?php }?>>- 有效 -</option>
                <option value="0" <?php if ($_smarty_tpl->tpl_vars['g_userstatus']->value=='0'){?>selected="selected"<?php }?>>- 无效 -</option>
                <option value="2" <?php if ($_smarty_tpl->tpl_vars['g_userstatus']->value=='2'){?>selected="selected"<?php }?>>- 同步中 -</option>
            </select>
        </span>
        <span>
        	<input name="mod" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_mod']->value;?>
" />
            <input name="act" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_act']->value;?>
" />
            <input name="page" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_page']->value;?>
" />
            <button name="button" type="submit" id="bottom" value="search" />搜索</button>
        </span>
        <!--<span>
            <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=user&act=add'"/>添加</button>
        </span>-->
		<!--<span>
            <button name="button" type="button" id="bottom" value="user_competence" onclick="user_competence()"/>添加可见账号权限</button>
        </span>-->
	</form>
</div>

<div class="main">
    <table cellspacing="0" width="100%">
        <tr class="title">
        	<td><input id="inverse-check" type="checkbox"></input></td>
            <td>用户名</td>
            <td>姓名</td>
            <td>电话</td>
            <td>岗位</td>       
            <td>部门</td> 
            <td>公司</td>    
            <td>上次登陆时间</td>    
            <td width="80">权限类别</td>
            <td width="80">用户状态</td>            
            <td width="120">Token授权日期</td>
            <td width="120">Token有效天数</td>
            <td width="100">操作</td>
        </tr> 
 		<?php  $_smarty_tpl->tpl_vars['userlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['userlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['userlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['userlist']->key => $_smarty_tpl->tpl_vars['userlist']->value){
$_smarty_tpl->tpl_vars['userlist']->_loop = true;
?>
        <tr>
            <td><input name="checkbox-list" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['userlist']->value['global_user_id'];?>
" /></td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['user_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['global_user_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['user_phone'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['job_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['dept_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['company_name'];?>
</td>
            <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['userlist']->value['user_lastUpdateTime'],"Y-m-d H:i:s");?>
</td>
            <td><?php if ($_smarty_tpl->tpl_vars['userlist']->value['user_independence']=='1'){?>独立<?php }else{ ?>共享<?php }?></td>
            <td><?php if ($_smarty_tpl->tpl_vars['userlist']->value['user_status']=='1'){?>有效<?php }elseif($_smarty_tpl->tpl_vars['userlist']->value['user_status']=='2'){?>同步中..<?php }else{ ?>无效<?php }?></td>
            <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['userlist']->value['user_token_grant_date'],"Y-m-d");?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['userlist']->value['user_token_effective_date'];?>
</td>
            <td>
                <!--<a href="javascript:void(0)" onclick="user_competence_show(<?php echo $_smarty_tpl->tpl_vars['userlist']->value['user_id'];?>
)">修改</a> | -->
                <a href="index.php?mod=omAccount&act=showUserCompense&uid=<?php echo $_smarty_tpl->tpl_vars['userlist']->value['global_user_id'];?>
">权限</a><!-- | 
                <a href="javascript:void(0)" onclick="del_user(<?php echo $_smarty_tpl->tpl_vars['userlist']->value['user_id'];?>
)" id="del-btn">删除</a>-->
            </td>
        </tr>  
		<?php }
if (!$_smarty_tpl->tpl_vars['userlist']->_loop) {
?>
        <tr>
            <td colspan="12" align="center"><?php echo $_smarty_tpl->tpl_vars['runmsg']->value;?>
</td>
        </tr> 
        <?php } ?>                
    </table>
</div>
<div class="bottomvar">
    <div class="pagination"><?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>

//删除用户
function del_user(uid){
	alertify.error('功能未开放!');
	return false;
	alertify.confirm("亲,真的要删除当前用户吗？", function (e) {
	if (e) {
		$.post("index.php?mod=user&act=delete",{"userid":uid},function(rtn){
			if($.trim(rtn) == "ok"){              
				alertify.success("亲,删除成功!");
				//window.location.reload();
			}else {
				 alertify.error("亲,删除失败!");
		   }
		});
	}});
}

</script>   <?php }} ?>