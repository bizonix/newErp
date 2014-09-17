<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:36:19
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\addAutoSpuForOld.htm" */ ?>
<?php /*%%SmartyHeaderCode:288825266460ab42888-61250957%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b61de1e5f7f2ba16644a519695333e09495f3632' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\addAutoSpuForOld.htm',
      1 => 1382434576,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '288825266460ab42888-61250957',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5266460ab8d6b1_17857425',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5266460ab8d6b1_17857425')) {function content_5266460ab8d6b1_17857425($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
<span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
						<tr>
                            <td><span style="color:#F00;">*</span>SPU：
							<input name="spu" id="spu"/>
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
                            <td><input type="button" value="提交" id="addAutoSpuForOld"/>
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