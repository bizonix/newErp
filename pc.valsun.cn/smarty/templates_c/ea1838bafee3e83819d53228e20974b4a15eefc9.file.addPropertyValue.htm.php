<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:50:59
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addPropertyValue.htm" */ ?>
<?php /*%%SmartyHeaderCode:2239852664a83ebf8b5-44040414%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea1838bafee3e83819d53228e20974b4a15eefc9' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addPropertyValue.htm',
      1 => 1382430337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2239852664a83ebf8b5-44040414',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'propertyId' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664a83f11110_93730017',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664a83f11110_93730017')) {function content_52664a83f11110_93730017($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
            <div class="main products-main">
            	<form id="formAddPPV" name="form" method="post" action="index.php?mod=property&act=addPropertyValueOn" id="propertyValidation">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
                            <input name="propertyId" value="<?php echo $_smarty_tpl->tpl_vars['propertyId']->value;?>
" type="hidden"/>
                            <td>属性名：
							<?php echo OmAvailableModel::getProValNameById($_smarty_tpl->tpl_vars['propertyId']->value);?>
</td>
						</tr>
                        <tr>
                            <td><span style="color:#F00;">*</span>&nbsp;属性值：
							<input class="validate[required]" name="propertyValue" id="propertyValue"/></td>
						</tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;别名：
							<input name="propertyValueAlias" id="propertyValueAlias"/></td>
						</tr>
                        <tr>
                            <td>字母简写：
							<input name="propertyValueShort" id="propertyValueShort"/></td>
						</tr>
                        <tr>
                            <td><input type="submit" value="提交"/>
							<input type="button" value="返回" id="back"/></td>
						</tr>
					</table>

				</form>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>