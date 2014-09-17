<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:04:19
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\editAutoCreateSpuCate.htm" */ ?>
<?php /*%%SmartyHeaderCode:2412752663c55b09db8-59208202%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '434b7748055915c276c7e26a81f69fc41138c0ae' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\editAutoCreateSpuCate.htm',
      1 => 1382439855,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2412752663c55b09db8-59208202',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52663c55b72394_37986392',
  'variables' => 
  array (
    'spu' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52663c55b72394_37986392')) {function content_52663c55b72394_37986392($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
            </div>
			<div class="servar products-servar">
			<span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main">
					<table class="products-action" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
                            <td>SPU：
                            <input value="<?php echo $_smarty_tpl->tpl_vars['spu']->value;?>
" disabled="disabled"/>
							<input name="spu" id="spu" value="<?php echo $_smarty_tpl->tpl_vars['spu']->value;?>
" type="hidden"/>
							</td>
						</tr>
                        <tr>
                           	    <td>
                                    类别:
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
                            <td><input type="button" value="提交" id="editAutoCreateSpu"/>
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