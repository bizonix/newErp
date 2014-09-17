<?php /* Smarty version Smarty-3.1.12, created on 2014-03-31 11:52:28
         compiled from "D:\Workspace\PHP\mail_subscription\html\template\v1\powerShow.html" */ ?>
<?php /*%%SmartyHeaderCode:1699953293c0d1f99e0-87301813%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e52529687b8cfc069e1d717f22de83cf98e178e4' => 
    array (
      0 => 'D:\\Workspace\\PHP\\mail_subscription\\html\\template\\v1\\powerShow.html',
      1 => 1396236863,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1699953293c0d1f99e0-87301813',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53293c0d259564_84598294',
  'variables' => 
  array (
    'powerInfo' => 0,
    'page_str' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53293c0d259564_84598294')) {function content_53293c0d259564_84598294($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar order-fourvar">
  <input type="button" value="新增邮件" id="addBt" onClick="addMailList()">
</div>
<div class="main products-main">
  <table width="100%" cellspacing="0" border="0" cellpadding="0">
    <tbody align="left">
      <tr class="title">
        <td style="padding-left:30px;font-weight:bold;" width="30%" align="left"> 全部邮件 </td>
        <td style="font-weight:bold;"  width="30%" align="left"> 订阅权限 </td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td style="font-weight:bold;"  width="10%"> 操作 </td>
        <td width="5%"></td>
        <td width="5%"></td>
      </tr>
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