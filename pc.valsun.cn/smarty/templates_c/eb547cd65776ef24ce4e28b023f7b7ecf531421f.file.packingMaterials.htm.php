<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 19:18:09
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\packingMaterials.htm" */ ?>
<?php /*%%SmartyHeaderCode:32271526615853b2e03-20166873%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eb547cd65776ef24ce4e28b023f7b7ecf531421f' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\packingMaterials.htm',
      1 => 1382440687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32271526615853b2e03-20166873',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526615854971f9_77400035',
  'variables' => 
  array (
    'show_page' => 0,
    'status' => 0,
    'pmList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526615854971f9_77400035')) {function content_526615854971f9_77400035($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/pm.js"></script>
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

                <a id="add" class="add" href="javascript:void(0)">添加包材</a>
            	&nbsp;
            	<span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
                         <td>包材名</td>
                         <td>包材别名</td>
                         <td>成本(CNY)</td>
						 <td>长度(m)</td>
						 <td>宽度(m)</td>
                         <td>高度(m)</td>
                         <td>重量(kg)</td>
                         <td>容积(m<sup>3</sup>)</td>
                         <td>包材比除数</td>
                         <td>比值</td>
                         <td>备注</td>
                         <td>操作</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pmList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr class="odd" id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
">
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmName'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmAlias'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmCost'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmLength'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmWidth'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmHeight'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmWeight'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmDimension'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmDivider'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmRatio'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['pmNotes'];?>
</td>
									<td>
                                    <input type="button" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="mod" value="修改"/>
                                    <input type="button" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="del" value="删除"/>
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