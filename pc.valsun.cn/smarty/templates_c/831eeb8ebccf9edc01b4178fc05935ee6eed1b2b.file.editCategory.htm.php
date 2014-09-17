<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:42:35
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\editCategory.htm" */ ?>
<?php /*%%SmartyHeaderCode:31995526616aed90f96-28050717%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '831eeb8ebccf9edc01b4178fc05935ee6eed1b2b' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\editCategory.htm',
      1 => 1382438551,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31995526616aed90f96-28050717',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616aede1483_72719012',
  'variables' => 
  array (
    'categoryid' => 0,
    'categoryfile' => 0,
    'connect' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616aede1483_72719012')) {function content_526616aede1483_72719012($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/cate.js"></script>
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
            	<table width="70%" border="0" cellpadding="0" cellspacing="0">
								<input name="categoryid" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['categoryid']->value;?>
" id='categoryid' />
								<input name="categoryfile" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['categoryfile']->value;?>
" id='categoryfile' />
								  <?php echo $_smarty_tpl->tpl_vars['connect']->value;?>

								</table>
								<br>
								<div class="products-action" align="left"><span style="white-space: nowrap;"><input type="button" value="保存数据"  id="savecate"/></span>
								<font color='#FF0000'>备注:修改二级分类所属父类,二级分类下的子类也随其改变.</font>
								</div>
								 </form>
							   </td>

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