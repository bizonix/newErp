<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:48:43
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\autoCreateSpu.htm" */ ?>
<?php /*%%SmartyHeaderCode:160295265e5e8b6f6c6-08848977%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3d6520cfa6cc385d5bd92c2b30b2cdebe6c42751' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\autoCreateSpu.htm',
      1 => 1382438921,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '160295265e5e8b6f6c6-08848977',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265e5e8bcf5c2_07698276',
  'variables' => 
  array (
    'spuPrefixList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5265e5e8bcf5c2_07698276')) {function content_5265e5e8bcf5c2_07698276($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
            </div>
            <div class="main products-main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td align="left" colspan="2" style="font-size:16px; font-weight:bold; padding-left:15px;">
                        生成SPU
                        <span id="error" style="color: red;"></span>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" width="10%">SPU前缀：</td>
                        <td align="left">
                        	<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['spuPrefixList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                        	<label><input name="prefix" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['prefix'];?>
"/><?php echo $_smarty_tpl->tpl_vars['value']->value['prefix'];?>
</label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">SPU：</td>
                        <td align="left">
                            <input id="createSpuText" name="createSpuText" type="text" value="" readonly="readonly" />
                            <input id="sort" name="sort" type="hidden" value="" />
                            <input id="prefixTmp" name="prefixTmp" type="hidden" value=""/>
                            <input id="isSingSpu" name="isSingSpu" type="hidden" value=""/>
                        </td>
                    </tr>
                </table>
                <div align="center" style="padding-top:17px;" class="products-action">
                	<input type="button" id="createSpu" value="自动生成"/>
                    <input type="button" id="addSpu" value="添加"/>
                    <input type="button" id="back" value="返回"/>
                </div>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>