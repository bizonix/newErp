<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:13:20
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updateInput.htm" */ ?>
<?php /*%%SmartyHeaderCode:1430552664ba50bcd85-59384149%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4cf727363ecf23d399950f1ab925df66c9c608af' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updateInput.htm',
      1 => 1382440284,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1430552664ba50bcd85-59384149',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664ba51bcd18_31803156',
  'variables' => 
  array (
    'id' => 0,
    'inputName' => 0,
    'value' => 0,
    'categoryStr' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664ba51bcd18_31803156')) {function content_52664ba51bcd18_31803156($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
                            <input id="id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" type="hidden"/>
                            <td><span style="color:#F00;">*</span>属性名：
							<input id="inputName" value="<?php echo $_smarty_tpl->tpl_vars['inputName']->value;?>
"/>
							</td>
						</tr>
                        <tr>
                           	    <td>
                                    &nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span style="color:#F00;">*</span>类别：
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
                                    <span style="color: green;">修改前类型为：<?php echo $_smarty_tpl->tpl_vars['categoryStr']->value;?>
</span>
								</td>
						</tr>
                        <tr>
                            <td><input type="button" value="提交" id="updateInput"/>
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