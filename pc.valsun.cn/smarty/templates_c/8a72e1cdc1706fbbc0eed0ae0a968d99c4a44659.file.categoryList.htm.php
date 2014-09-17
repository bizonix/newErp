<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:40:58
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\categoryList.htm" */ ?>
<?php /*%%SmartyHeaderCode:228265266163414de05-17374450%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8a72e1cdc1706fbbc0eed0ae0a968d99c4a44659' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\categoryList.htm',
      1 => 1382438456,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '228265266163414de05-17374450',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52661634228078_50365984',
  'variables' => 
  array (
    'show_page' => 0,
    'categoryList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52661634228078_50365984')) {function content_52661634228078_50365984($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/cate.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
                <div class="texvar">
                </div>
                <div class="pagination">
                <?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

                </div>
            </div>
<div class="servar products-servar">
            	<span>
                	<a href="index.php?mod=category&act=addCategory">增加类别</a>
                </span>
                <span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
                        <td>编号</td>
                        <td>名称</td>
                        <td>所属类级</td>
						<td>父类</td>
						<td>操作</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoryList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr class="odd" id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
">
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['file'];?>
</td>
									<td><?php echo getAllCateNameByPath(CategoryModel::getCategoryPathById($_smarty_tpl->tpl_vars['value']->value['pid']));?>
</td>
									<td>
									<input type="button" value="修改" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="mod"/>
									<input type="button" value="删除" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="del"/>
									</td>
                                </tr>
                                <?php } ?>
                </table>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
					<?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>