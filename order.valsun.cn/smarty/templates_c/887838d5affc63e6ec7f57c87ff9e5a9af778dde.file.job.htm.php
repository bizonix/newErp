<?php /* Smarty version Smarty-3.1.12, created on 2014-03-01 16:35:59
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\job.htm" */ ?>
<?php /*%%SmartyHeaderCode:1133053119bef19fa44-25263716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '887838d5affc63e6ec7f57c87ff9e5a9af778dde' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\job.htm',
      1 => 1393658410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1133053119bef19fa44-25263716',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pageStr' => 0,
    'g_jobname' => 0,
    'deptlists' => 0,
    'deptlist' => 0,
    'g_userdept' => 0,
    'g_mod' => 0,
    'g_act' => 0,
    'g_page' => 0,
    'joblists' => 0,
    'joblist' => 0,
    'runmsg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53119bef2c0ef5_59034574',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53119bef2c0ef5_59034574')) {function content_53119bef2c0ef5_59034574($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
    <div class="pathvar">
        您的位置：<a href="index.php?mod=job&act=index">岗位信息管理</a>&nbsp;&gt;&gt;&nbsp;岗位列表
    </div>
	<div class="pagination"><?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>
</div>
</div>
<div class="servar">
	<form name="form" action="" enctype="text/plain" method="get">
        <span>
            岗位名称：<input type="text" name="jobname" value="<?php echo $_smarty_tpl->tpl_vars['g_jobname']->value;?>
"/>
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
        	<input name="mod" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_mod']->value;?>
" />
            <input name="act" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_act']->value;?>
" />
            <input name="page" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_page']->value;?>
" />
            <button name="button" type="submit" id="bottom" value="search" />搜索</button>
        </span>
        <span>
            <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=job&act=add'"/>添加</button>
        </span>
	</form>
</div>
<div class="main">
    <table cellspacing="0" width="100%">
        <tr class="title">
        	<td align="center">编号</td>
            <td align="center">岗位名称</td>
            <td align="center">岗位等级</td>
            <td align="center">部门</td>
          	<td align="center">公司</td>
            <td align="center">操作</td>
        </tr> 
 		<?php  $_smarty_tpl->tpl_vars['joblist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['joblist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['joblists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['joblist']->key => $_smarty_tpl->tpl_vars['joblist']->value){
$_smarty_tpl->tpl_vars['joblist']->_loop = true;
?>
        <tr>
          <td><input name="checkbox-list" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
" /></td>
            <td align="left"> 
            	<span style="color:#00F;"><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['name'] = 'loop';
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['joblist']->value['job_level']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] = 1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total']);
?>|&nbsp;<?php endfor; endif; ?>├</span>
                <?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_name'];?>

            </td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_level'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['joblist']->value['dept_name'];?>
</td>
            <td align="center"><?php echo $_smarty_tpl->tpl_vars['joblist']->value['company_name'];?>
</td>   
            <td>
                <a href="index.php?mod=job&act=modify&jid=<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
">编辑</a> | 
                <a href="javascript:void(0)" onclick="del_job(<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['joblist']->value['jobpower_id'];?>
)" id="del-btn">删除</a>
            </td>
        </tr>  
		<?php }
if (!$_smarty_tpl->tpl_vars['joblist']->_loop) {
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

//删除岗位
function del_job(jobId,jobpowerId){
	alertify.confirm("亲,真的要删除当前岗位吗？", function (e) {
	if (e) {
		$.post("index.php?mod=job&act=delete",{"jobId":jobId,"jobpowerId":jobpowerId},function(rtn){
			if($.trim(rtn) == "ok"){              
				alertify.success("亲,删除成功!");
				//window.location.reload();
			}else {
				 alertify.error("亲,删除失败!");
		   }
		});
	}});
}

</script>        <?php }} ?>