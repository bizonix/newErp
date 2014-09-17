<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:29:15
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updateSpuPrefix.htm" */ ?>
<?php /*%%SmartyHeaderCode:97195266456b104bb7-42442562%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'afb9b0742e7c3bcfc009e48dbc0cc7c34380c117' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updateSpuPrefix.htm',
      1 => 1382430492,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97195266456b104bb7-42442562',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'id' => 0,
    'prefix' => 0,
    'isSingSpu' => 0,
    'isUse' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5266456b2726f0_90597771',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5266456b2726f0_90597771')) {function content_5266456b2726f0_90597771($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
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
<span style="color:#F00;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main products-main">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<input id="id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" type="hidden"/>
						<tr>
                            <td><span style="color:#F00;">*</span>前缀：
							<input name="prefix" id="prefix" value='<?php echo $_smarty_tpl->tpl_vars['prefix']->value;?>
'/>
							</td>
						</tr>
                        <tr>
                            <td><span style="color:#F00;">*</span>单/虚拟料号：
									<select name="isSingSpu" id="isSingSpu">
										<option value="1" <?php if ($_smarty_tpl->tpl_vars['isSingSpu']->value==1){?>selected='selected'<?php }?>>单料号</option>
										<option value="2" <?php if ($_smarty_tpl->tpl_vars['isSingSpu']->value==2){?>selected='selected'<?php }?>>虚拟料号</option>
									</select>
							</td>
						</tr>
						<tr>
                            <td><span style="color:#F00;">*</span>是否启用：
									<select name="isUse" id="isUse">
										<option value="1" <?php if ($_smarty_tpl->tpl_vars['isUse']->value==1){?>selected='selected'<?php }?>>启用</option>
										<option value="2" <?php if ($_smarty_tpl->tpl_vars['isUse']->value==2){?>selected='selected'<?php }?>>禁用</option>
									</select>
							</td>
						</tr>
                        <tr>
                            <td><input type="button" value="提交" id="updateSpuPrefix"/>
							<input type="button" value="返回" id="back"/></td>
						</tr>
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