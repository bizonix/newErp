<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:15:26
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updateCombine.htm" */ ?>
<?php /*%%SmartyHeaderCode:2860452664d9315ca60-52086399%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '923a4192da8a6becebf844a9c0137225c3d62047' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updateCombine.htm',
      1 => 1382440524,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2860452664d9315ca60-52086399',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664d93244fa3_30432969',
  'variables' => 
  array (
    'combine' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664d93244fa3_30432969')) {function content_52664d93244fa3_30432969($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
			&nbsp;
			<span style="color: red;" id="error"></span>
            </div>
            <form action="index.php?mod=goods&act=updateCombineOn" method="post" id="CombineValidation">
            <div class="main products-main basic-main">
            	<table cellspacing="0" width="100%" id="updateCom">
                	<tr class="title">
                    	<td align="left" colspan="5" style="font-size:16px; font-weight:bold; padding-left:15px;">基本信息</td>
                    </tr>
                    <tr>
                        <td class="products-action" rowspan="100" style="width:450px;">

                    	</td>
                        <td width="4%"><span style="color:#F00;">*</span>SPU</td>
                        <td width="10%">
                            <input value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineSpu'];?>
" disabled="disabled"/>
                            <input name="id" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['id'];?>
" type="hidden"/>
                            <input name="combineSpu" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineSpu'];?>
" type="hidden"/>
                        </td>
                    </tr>
                    <tr>
                        <td ><span style="color:#F00;">*</span>SKU</td>
                        <td><input class="validate[required]" name="combineSku" id="combineSku" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineSku'];?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>成本</td>
                        <td><input class="validate[required,custom[number],min[0.001]] text-input" name="combineCost" id="combineCost" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineCost'];?>
"/></td>

                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>重量</td>
                        <td><input class="validate[required,custom[number],min[0.001]] text-input" name="combineWeight" id="combineWeight" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineWeight'];?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>长</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineLength" id="combineLength" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineLength'];?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>宽</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineWidth" id="combineWidth" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineWidth'];?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>高</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineHeight" id="combineHeight" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineHeight'];?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>备注</td>
                        <td><input name="combineNote" id="combineNote" value="<?php echo $_smarty_tpl->tpl_vars['combine']->value['combineNote'];?>
"/></td>
                    </tr>
                    <tr><td colspan="4"><input type="button" id="addElement2" value="添加真实料号"/></td></tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = OmAvailableModel::getTrueSkuForCombine($_smarty_tpl->tpl_vars['combine']->value['combineSku']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                    <tr><td><span style='color:#F00;'>*</span>料号</td><td><input name='sku[]' value="<?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
"/></td> <td width="4%"><span style='color:#F00;'>*</span>数量</td><td><input name='count[]' class="validate[option,custom[integer],min[1]]" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['count'];?>
"/></td></tr>
                    <?php } ?>
                </table>
                <div align="center" class="">
                	<input type="submit" value="保存"/>
                    <input type="button" value="返回" id="back"/>
                </div>
            </div>
            </form>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>