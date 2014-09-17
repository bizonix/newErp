<?php /* Smarty version Smarty-3.1.12, created on 2014-07-11 14:29:57
         compiled from "/data/web/rss.valsun.cn/html/template/v1/powerShow.html" */ ?>
<?php /*%%SmartyHeaderCode:41815534653421576ea64d3-61076091%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f9fa0e10ab0ba29ce2a5a40882fc48f452dad5c3' => 
    array (
      0 => '/data/web/rss.valsun.cn/html/template/v1/powerShow.html',
      1 => 1405051096,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41815534653421576ea64d3-61076091',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53421577086215_84474633',
  'variables' => 
  array (
    'getSystem' => 0,
    'system' => 0,
    'resultInfo' => 0,
    'status' => 0,
    'powerInfo' => 0,
    'page_str' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53421577086215_84474633')) {function content_53421577086215_84474633($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="main products-main" style="border:1px solid #ccc;" id="content">
<br />
    <form name="searchMail" id="searchMail" method="post" action="index.php?mod=MailManage&act=getMailPowerByConditions">
          所属系统：
    <select name="system" id="system">
	   <option value="default">-----请选择-----</option>
	   <?php  $_smarty_tpl->tpl_vars['system'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['system']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['getSystem']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['system']->key => $_smarty_tpl->tpl_vars['system']->value){
$_smarty_tpl->tpl_vars['system']->_loop = true;
?>
	   <option name="systemId" value="<?php echo $_smarty_tpl->tpl_vars['system']->value['system_id'];?>
">--<?php echo $_smarty_tpl->tpl_vars['system']->value['system_name'];?>
--</option>
	   <?php } ?>
	</select>
	        邮件名称：<input type="text" name="mailName" id="mailName" />
	   <input type="submit" name="search" id="search" value="搜索" />
	</form>
<br />
</div>
<div class="fourvar order-fourvar">
  <input type="button" value="新增邮件" id="addBt" onClick="addMailList()">
</div>
<div class="main products-main">
  <table width="100%" cellspacing="0" border="0" cellpadding="0">
    <tbody align="left">
      <tr class="title">
        <td style="padding-left:30px;font-weight:bold;" width="15%" align="left"> 全部邮件 </td>
        <td style="font-weight:bold;" width="15%"> 所属系统 </td>
        <td style="font-weight:bold;"  width="25%" align="left"> 订阅权限 </td>
        <td width="12%"></td>
        <td width="13%"></td>
        <td style="font-weight:bold;"  width="7%"> 操作 </td>
        <td width="7%"></td>
        <td width="6%"></td>
      </tr>
      <?php if ($_smarty_tpl->tpl_vars['resultInfo']->value&&$_smarty_tpl->tpl_vars['status']->value==1){?>
      	<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['list'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['resultInfo']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total']);
?>
	      <tr>
	        <td><?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['system_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['company_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['dept_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['job_name'];?>
</td>
	        <?php if ($_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['status']==1){?>
	        <td><input type="button" value="编辑" onClick="modifyPower(<?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
);" /></td>
	        <?php }else{ ?>
	        <td></td>
	        <?php }?>
	        <?php if ($_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['status']==1){?>
	        <td><a href="index.php?mod=MailManage&act=checkPower&list_id=<?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
">查看详情</td>
	        <td><a href="#" onclick="deleteMail(<?php echo $_smarty_tpl->tpl_vars['resultInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
)">删除</td>
	        <?php }else{ ?>
	        <td></td>
	        <?php }?>
	      </tr>
   		<?php endfor; endif; ?>
      <?php }elseif($_smarty_tpl->tpl_vars['status']->value==0){?>
      	<tr align="center"><td colspan="6">结果为空！</td></tr>
      <?php }else{ ?>
      <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['list'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['powerInfo']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['list']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['list']['total']);
?>
	      <tr>
	        <td><?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['system_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['company_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['dept_name'];?>
</td>
	        <td><?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['job_name'];?>
</td>
	        <?php if ($_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['status']==1){?>
	        <td><input type="button" value="编辑" onClick="modifyPower(<?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
);" /></td>
	        <?php }else{ ?>
	        <td></td>
	        <?php }?>
	        <?php if ($_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['status']==1){?>
	        <td><a href="index.php?mod=MailManage&act=checkPower&list_id=<?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
">查看详情</td>
	        <td><a href="#" onclick="deleteMail(<?php echo $_smarty_tpl->tpl_vars['powerInfo']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_id'];?>
)">删除</td>
	        <?php }else{ ?>
	        <td></td>
	        <?php }?>
	      </tr>
      <?php endfor; endif; ?>
    <?php }?>
    </tbody>
  </table>
  <div align="center"><?php echo $_smarty_tpl->tpl_vars['page_str']->value;?>
</div>
</div>

</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
</body>
</html><?php }} ?>