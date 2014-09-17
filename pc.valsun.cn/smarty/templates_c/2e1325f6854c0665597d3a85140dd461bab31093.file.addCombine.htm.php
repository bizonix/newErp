<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:18:33
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addCombine.htm" */ ?>
<?php /*%%SmartyHeaderCode:8027526642e904b585-51367740%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e1325f6854c0665597d3a85140dd461bab31093' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addCombine.htm',
      1 => 1382430324,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8027526642e904b585-51367740',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'combineSpu' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526642e90a8f91_18854318',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526642e90a8f91_18854318')) {function content_526642e90a8f91_18854318($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
			<span style="color: red;" id="error"></span>
            </div>
            <form action="index.php?mod=goods&act=addCombineOn" method="post" id="CombineValidation">
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
                            <input value="<?php echo $_smarty_tpl->tpl_vars['combineSpu']->value;?>
" disabled="disabled"/>
                            <input value="<?php echo $_smarty_tpl->tpl_vars['combineSpu']->value;?>
" name="combineSpu" type="hidden"/>
                        </td>
                    </tr>
                    <tr>
                        <td ><span style="color:#F00;">*</span>SKU</td>
                        <td><input class="validate[required]" name="combineSku" id="combineSku" value="<?php echo $_smarty_tpl->tpl_vars['combineSpu']->value;?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>成本</td>
                        <td><input class="validate[required,custom[number],min[0.001]] text-input" name="combineCost" id="combineCost" value=""/></td>

                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>重量</td>
                        <td><input class="validate[required,custom[number],min[0.001]] text-input" name="combineWeight" id="combineWeight" value=""/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>长</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineLength" id="combineLength" value=""/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>宽</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineWidth" id="combineWidth" value=""/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>高</td>
                        <td><input class="validate[option,custom[number],min[0]] text-input" name="combineHeight" id="combineHeight" value=""/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>备注</td>
                        <td><input name="combineNote" id="combineNote" value=""/></td>
                    </tr>
                    <tr><td colspan="4"><input type="button" id="addElement2" value="添加真实料号"/></td></tr>
                    <tr><td><span style='color:#F00;'>*</span>料号</td><td><input class="validate[required]" name='sku[]' value="<?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
"/></td> <td width="4%"><span style='color:#F00;'>*</span>数量</td><td><input class="validate[required,custom[integer],min[1]]" name='count[]' value="<?php echo $_smarty_tpl->tpl_vars['value']->value['count'];?>
"/></td></tr>
                </table>
                <div align="center" class="products-action">
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