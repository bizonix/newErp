<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:48:11
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addCategory.htm" */ ?>
<?php /*%%SmartyHeaderCode:24202526616aaa72b66-39801287%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '406c50445bd347dd06870b2ca9f555f062effd4d' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addCategory.htm',
      1 => 1382433446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24202526616aaa72b66-39801287',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616aaad14e9_33137566',
  'variables' => 
  array (
    'categoryList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616aaad14e9_33137566')) {function content_526616aaad14e9_33137566($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/cate.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
                <div class="texvar">
                </div>
                <div class="pagination">
                <
                </div>
            </div>
<div class="servar products-servar">
<span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main">
            	<form id="form" name="form" method="post" action="">
								  <table class="products-action" width="100%" border="0" cellpadding="0" cellspacing="0">
								  <tr>
									<td width="25%" align="right" >
										<div align="left" id="div_one">
											<select name="pid_one" id="pid_one" onchange="select_one();" multiple="multiple" style="width:150px; height:180px;">
												<option value="0">==请选择==</option>
												<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoryList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
												<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"/><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</option>
												<?php } ?>
											</select>
									  </div>
									  </td>
									<td width="25%" align="right"  id="show_second" style="display:none">
									 <div align="left" id="div_two"></div></td>
									<td width="25%" align="right"  id="show_third" style="display:none">
									 <div align="left" id="div_three"></div></td>
									<td width="25%" align="right"  id="show_fourth" style="display:none">
									 <div align="left" id="div_four"></div></td>
									</tr>
									<tr>
										<td align="left" >分类名称:
										  <input type="text" id="txt_one" />&nbsp;&nbsp;
										  <input type="button" id="btn_one_add" class="addcate" tid="txt_one" tcate="1" value="添加"/></td>
										<td width="24%" align="left"  id="show_second_name" style="display:none">
											分类名称:
												 <input type="text" id="txt_two" />&nbsp;&nbsp;
												 <input type="button" id="btn_two_add" class="addcate" tid="txt_two" tcate="2" value="添加"  />

										</td>
										<td width="24%" align="left" id="show_third_name" style="display:none">
											分类名称:
												 <input type="text" id="txt_three" />&nbsp;&nbsp;
												 <input type="button" id="btn_three_add" class="addcate" tid="txt_three" tcate="3" value="添加" />

									   </td>
									   <td width="24%" align="left"  id="show_fourth_name" style="display:none">
											分类名称:
												 <input type="text" id="txt_four" />&nbsp;&nbsp;
												 <input type="button" id="btn_four_add" class="addcate" tid="txt_four" tcate="4" value="添加" />

									   </td>
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