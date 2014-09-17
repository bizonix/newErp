<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 16:23:30
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\pcNav.htm" */ ?>
<?php /*%%SmartyHeaderCode:149925266360257a291-48182771%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5410cfb9e02a3a0e2c59c1d23cb62be00695a233' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\pcNav.htm',
      1 => 1382430167,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '149925266360257a291-48182771',
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
  'unifunc' => 'content_5266360259c759_10248153',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5266360259c759_10248153')) {function content_5266360259c759_10248153($_smarty_tpl) {?>        您的位置
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
<?php }} ?>