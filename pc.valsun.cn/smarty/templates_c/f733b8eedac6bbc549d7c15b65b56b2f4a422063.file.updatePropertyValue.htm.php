<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:12:46
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updatePropertyValue.htm" */ ?>
<?php /*%%SmartyHeaderCode:3114952664ad3771776-78622247%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f733b8eedac6bbc549d7c15b65b56b2f4a422063' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updatePropertyValue.htm',
      1 => 1382440358,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3114952664ad3771776-78622247',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664ad389b3c4_06848378',
  'variables' => 
  array (
    'propertyId' => 0,
    'propertyValueList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664ad389b3c4_06848378')) {function content_52664ad389b3c4_06848378($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/property.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
                <div class="texvar">
                </div>
                <div class="pagination">
                </div>
            </div>
<div class="servar products-servar">


            </div>
            <div class="main feedback-main">
					<table class="products-action" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['propertyId']->value;?>
" id='propertyId'/>
                        <tr>
                            <td colspan="4">属性名：<?php echo OmAvailableModel::getProValNameById($_smarty_tpl->tpl_vars['propertyId']->value);?>
</td>
                        </tr>
					    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['propertyValueList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
						<tr>
                            <td>
                            属性值:
                            <input id="propertyValue<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['propertyValue'];?>
"/>
							</td>
                            <td>
                            别名：
                            <input id="propertyValueAlias<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['propertyValueAlias'];?>
"/>
							</td>
                            <td>
                            字母简写：
                            <input id="propertyValueShort<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['propertyValueShort'];?>
"/>
							</td>
                            <td>
                            <input type="button" class="updatePPV" pid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" value="修改"/>
							</td>
						</tr>
                        <?php } ?>
					</table>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>