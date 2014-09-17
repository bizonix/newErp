<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 14:02:27
         compiled from "/data/web/trans.valsun.cn/html/template/jobModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:6928395305265f148083a61-24394039%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '43b187d7141b7729364620592059f92f8e5eb340' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/jobModify.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6928395305265f148083a61-24394039',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265f1480fd8d4_87409157',
  'variables' => 
  array (
    'modifyjob' => 0,
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
<?php if ($_valid && !is_callable('content_5265f1480fd8d4_87409157')) {function content_5265f1480fd8d4_87409157($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=job&act=index">岗位信息管理</a>&nbsp;&gt;&gt;&nbsp;岗位编辑
    </div>     
    </div>          
    <div class="main">
    <h1>修改岗位资料</h1>
    <form id="form" action="index.php?mod=job&act=update" method="post" onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >     
            <tr>
              <td align="right">岗位名称：</td>
                <td align="left">
                <input type="text" name="jobName" id="jobName" value="<?php echo $_smarty_tpl->tpl_vars['modifyjob']->value['job_name'];?>
" size="35" maxlength="30"/>
                <span class="red">*</span>
                </td>
            </tr>
            <tr>
              <td align="right">所属上级：</td>
                <td align="left">
                    <select name="jobPower" id="jobPower">
                    	<?php  $_smarty_tpl->tpl_vars['joblist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['joblist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['joblists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['joblist']->key => $_smarty_tpl->tpl_vars['joblist']->value){
$_smarty_tpl->tpl_vars['joblist']->_loop = true;
?>
                    		<option value="<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['joblist']->value['job_id']==$_smarty_tpl->tpl_vars['modifyjob']->value['job_id']){?>selected="selected"<?php }?>>
                            	<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']);
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
?>│<?php endfor; endif; ?>├<?php echo $_smarty_tpl->tpl_vars['joblist']->value['job_name'];?>

                            </option>
                        <?php }
if (!$_smarty_tpl->tpl_vars['joblist']->_loop) {
?>
                        	<option value=""> 无权限 </option>
                        <?php } ?>
                    </select>
                    <span class="red">*</span>
                </td>
            </tr>
            <tr>
              <td align="right">所属部门：</td>
                <td align="left">
                <select name="jobDept" id="jobDept">
                    <?php  $_smarty_tpl->tpl_vars['deptlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['deptlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deptlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['deptlist']->key => $_smarty_tpl->tpl_vars['deptlist']->value){
$_smarty_tpl->tpl_vars['deptlist']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['deptlist']->value['dept_id']==$_smarty_tpl->tpl_vars['modifyjob']->value['job_dept_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_name'];?>
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
                    <input type="hidden" name="jobpowerId" value="<?php echo $_smarty_tpl->tpl_vars['modifyjob']->value['jobpower_id'];?>
"/>
                    <input type="hidden" name="jobId" value="<?php echo $_smarty_tpl->tpl_vars['modifyjob']->value['job_id'];?>
"/>
                    <button name="button" type="submit" id="bottom" value="search" />提 交</button>
                    <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=job&act=index'"/>返 回</button>
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
    var jobPower,jobDept,jobName;
	jobPower = $.trim($("#jobPower").val());
    jobDept	= $.trim($("#jobDept").val());
    jobName = $.trim($("#jobName").val());
    

	if(jobName == ''){
		alertify.error("亲,岗位名称不能为空!");
		$("#jobName").focus();
		return false;
	}
	if(jobPower == ''){
		alertify.error("亲,所属岗位不能为空!");
		$("#jobPower").focus();
		return false;
	}
	
	if(jobDept == ''){
		alertify.error("亲,所属部门不能为空!");
		$("#jobDept").focus();
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