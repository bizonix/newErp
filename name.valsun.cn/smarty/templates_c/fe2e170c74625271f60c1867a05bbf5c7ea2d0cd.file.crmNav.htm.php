<?php /* Smarty version Smarty-3.1.12, created on 2013-09-25 11:36:11
         compiled from "D:\wamp\www\crm.valsun.cn\html\template\v1\crmNav.htm" */ ?>
<?php /*%%SmartyHeaderCode:265152425a2b5712a3-36727534%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe2e170c74625271f60c1867a05bbf5c7ea2d0cd' => 
    array (
      0 => 'D:\\wamp\\www\\crm.valsun.cn\\html\\template\\v1\\crmNav.htm',
      1 => 1378697191,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '265152425a2b5712a3-36727534',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'navlist' => 0,
    'navval' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52425a2b5a0396_62433067',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52425a2b5a0396_62433067')) {function content_52425a2b5a0396_62433067($_smarty_tpl) {?><div class="fourvar">
    <div class="pathvar">
        您的位置
        <?php  $_smarty_tpl->tpl_vars['navval'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['navval']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['navlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['navval']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['navval']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['navval']->key => $_smarty_tpl->tpl_vars['navval']->value){
$_smarty_tpl->tpl_vars['navval']->_loop = true;
 $_smarty_tpl->tpl_vars['navval']->iteration++;
 $_smarty_tpl->tpl_vars['navval']->last = $_smarty_tpl->tpl_vars['navval']->iteration === $_smarty_tpl->tpl_vars['navval']->total;
?><a <?php if ($_smarty_tpl->tpl_vars['navval']->last){?>class="navlasthref" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['navval']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['navval']->value['title'];?>
</a><?php if ($_smarty_tpl->tpl_vars['navval']->last){?><?php }else{ ?>>><?php }?><?php } ?>
    </div>
</div><?php }} ?>