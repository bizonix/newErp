<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:06:58
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addSpuPrefix.htm" */ ?>
<?php /*%%SmartyHeaderCode:29498526641fb0a4d79-75158260%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff193d0d81b333c9f88b851c690f7e8e7d3acaef' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addSpuPrefix.htm',
      1 => 1382440016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29498526641fb0a4d79-75158260',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526641fb0e6154_89986010',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526641fb0e6154_89986010')) {function content_526641fb0e6154_89986010($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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


            </div>
            <div class="main feedback-main">
					<table class="products-action" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
                            <td><span style="color:#F00;">*</span>前缀：
							<input name="prefix" id="prefix"/>
							</td>
						</tr>
                        <tr>
                            <td><span style="color:#F00;">*</span>单/虚拟料号：
									<select name="isSingSpu" id="isSingSpu">
										<option value="1">单料号</option>
										<option value="2">虚拟料号</option>
									</select>
							</td>
						</tr>
						<tr>
                            <td><span style="color:#F00;">*</span>是否启用：
									<select name="isUse" id="isUse">
										<option value="1">启用</option>
										<option value="2">禁用</option>
									</select>
							</td>
						</tr>
                        <tr>
                            <td><input type="button" value="提交" id="addSpuPrefix"/>
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