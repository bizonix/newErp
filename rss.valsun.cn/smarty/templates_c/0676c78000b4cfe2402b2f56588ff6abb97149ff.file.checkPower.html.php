<?php /* Smarty version Smarty-3.1.12, created on 2014-03-31 11:52:35
         compiled from "D:\Workspace\PHP\mail_subscription\html\template\v1\checkPower.html" */ ?>
<?php /*%%SmartyHeaderCode:1885153313c20edc8c5-14800198%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0676c78000b4cfe2402b2f56588ff6abb97149ff' => 
    array (
      0 => 'D:\\Workspace\\PHP\\mail_subscription\\html\\template\\v1\\checkPower.html',
      1 => 1396236890,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1885153313c20edc8c5-14800198',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53313c21016828_01421719',
  'variables' => 
  array (
    'checkPower' => 0,
    'page_str' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53313c21016828_01421719')) {function content_53313c21016828_01421719($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar order-fourvar">
  <input type="button" value="新增邮件" id="addBt" onClick="addMailList()">
</div>
<div class="main products-main">
  <table width="100%" cellspacing="0" border="0" cellpadding="0">
    <tbody align="left">
      <tr class="title">
        <td style="padding-left:30px;font-weight:bold;" width="30%" align="left"> 全部邮件 </td>
        <td style="font-weight:bold;"  width="30%" align="left"> 订阅人员 </td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td  width="20%"></td>
      </tr>
      <?php if ($_smarty_tpl->tpl_vars['checkPower']->value){?>
      <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['list'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['list']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['name'] = 'list';
$_smarty_tpl->tpl_vars['smarty']->value['section']['list']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['checkPower']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
        <td><?php echo $_smarty_tpl->tpl_vars['checkPower']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['list_name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['checkPower']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['company_name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['checkPower']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['dept_name'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['checkPower']->value[$_smarty_tpl->getVariable('smarty')->value['section']['list']['index']]['global_user_name'];?>
</td>
      </tr>
    <?php endfor; endif; ?>
    <?php }else{ ?>
    <tr><td>暂时还没有人订阅该邮件哦！</td></tr>
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