<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:08:19
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addProperty.htm" */ ?>
<?php /*%%SmartyHeaderCode:2039952664a6b442e32-70660899%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa9f41c32f70f3ead89336c36ef9cb10d7ab6a39' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addProperty.htm',
      1 => 1382440079,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2039952664a6b442e32-70660899',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664a6b4b8bf4_34926458',
  'variables' => 
  array (
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664a6b4b8bf4_34926458')) {function content_52664a6b4b8bf4_34926458($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
						<tr>
                            <td><span style="color:#F00;">*</span>属性名：
							<input name="propertyName" id="propertyName"/>
							</td>
						</tr>
                        <tr>
                           	    <td>
                                    <span style="color:#F00;">*</span>类别:
									<select name="sku_category" id="pid_one" onchange="select_one();">
										<option value="0">请选择</option>
										<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = CategoryModel::getCategoryList('*',"where is_delete=0 and pid=0"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</option>
										<?php } ?>
									</select>
									<span align="left" id="div_two" style="width:auto; display:none"></span>
									<span align="left" id="div_three" style="width:auto; display:none"></span>
									<span align="left" id="div_four" style="width:auto; display:none"></span>
								</td>
						</tr>
                        <tr>
                            <td><span style="width:auto; color: red;">(子类会拥有父类的属性)</span></td>
						</tr>
                        <tr>
                            <td><span style="color:#F00;">*</span>录入方式：
									<select name="isRadio" id="isRadio">
										<option value="1">单选</option>
										<option value="2">多选</option>
									</select>
							</td>
						</tr>
                        <tr>
                            <td><input type="button" value="提交" id="addPP"/>
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