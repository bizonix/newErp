<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 18:12:02
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\whNav.htm" */ ?>
<?php /*%%SmartyHeaderCode:22944531849f213f0c7-60159314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ee472ecd76ca5f44a741409b21820f559fbaeff8' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\whNav.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22944531849f213f0c7-60159314',
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
  'unifunc' => 'content_531849f2156da9_85166079',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_531849f2156da9_85166079')) {function content_531849f2156da9_85166079($_smarty_tpl) {?><div class="fourvar">
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