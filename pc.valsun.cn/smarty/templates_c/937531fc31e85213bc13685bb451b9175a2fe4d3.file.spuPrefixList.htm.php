<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:14:31
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\spuPrefixList.htm" */ ?>
<?php /*%%SmartyHeaderCode:14560526616ce859738-19892781%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '937531fc31e85213bc13685bb451b9175a2fe4d3' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\spuPrefixList.htm',
      1 => 1382430422,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14560526616ce859738-19892781',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616ce8d6909_90158920',
  'variables' => 
  array (
    'show_page' => 0,
    'status' => 0,
    'spuPrefixList' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616ce8d6909_90158920')) {function content_526616ce8d6909_90158920($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
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
            </div>
            <div class="servar products-servar">
                <span>
                	<a href="index.php?mod=spu&act=addSpuPrefix">新增前缀</a>
                </span>
                &nbsp;
                <span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            </div>
            <div class="main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td>前缀</td>
                        <td>单/虚拟料号</td>
                        <td>是否启用</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['spuPrefixList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                    <tr>
                    	<td><a href="index.php?mod=spu&act=updateSpuPrefix&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['prefix'];?>
</a></td>
                        <td><?php if ($_smarty_tpl->tpl_vars['value']->value['isSingSpu']==1){?>单料号<?php }else{ ?>虚拟料号<?php }?></td>
                        <td><?php if ($_smarty_tpl->tpl_vars['value']->value['isUse']==1){?><img alt="启用" src="http://misc.erp.valsun.cn/img/right.png"/><?php }else{ ?><img alt="禁用" src="http://misc.erp.valsun.cn/img/wrong.png"/><?php }?></td>
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