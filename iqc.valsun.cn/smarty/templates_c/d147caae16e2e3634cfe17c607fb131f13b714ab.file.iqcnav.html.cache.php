<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:06
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcnav.html" */ ?>
<?php /*%%SmartyHeaderCode:2945351ff592ebc6e52-24903083%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd147caae16e2e3634cfe17c607fb131f13b714ab' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcnav.html',
      1 => 1375684602,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2945351ff592ebc6e52-24903083',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'navarr' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff592ebd8029_73023152',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff592ebd8029_73023152')) {function content_51ff592ebd8029_73023152($_smarty_tpl) {?><div class="pathvar">
	您的位置：
	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['navarr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
        <span><?php echo $_smarty_tpl->tpl_vars['row']->value;?>
</span>
    <?php } ?>
</div><?php }} ?>