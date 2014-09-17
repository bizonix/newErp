<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 19:26:49
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\whStoreList.htm" */ ?>
<?php /*%%SmartyHeaderCode:1974153185b79a34c13-97296046%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b058fba6d403a8c1654f4641371f3ae721f5afc' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\whStoreList.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1974153185b79a34c13-97296046',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'succeedLog' => 0,
    'errorLog' => 0,
    'warehouseManagementArrList' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53185b79ac8e31_33610906',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53185b79ac8e31_33610906')) {function content_53185b79ac8e31_33610906($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('warehouseSubnav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/whStoreList.js"></script>
<div class="servar wh-servar">
    <span>  
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button  id='warehouseAdd' />新增仓库</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #0F0"><?php echo $_smarty_tpl->tpl_vars['succeedLog']->value;?>
</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #F00"><?php echo $_smarty_tpl->tpl_vars['errorLog']->value;?>
</span>
    </span>
</div>
<div class="main">
    <table cellspacing="0" width="100%">
        <thead>
            <tr class="title">
                <td width="20%">仓库名称</td>
                <td width="20%">仓库编码</td>
                <td width="20%">仓库位置</td>
				<td width="20%">仓库分布(仓库标签)</td>
                <td width="20%" align="center">操作</td>
                <!--td width="50%"></td-->
            </tr>
        </thead>
        <!-- BEGIN list -->
        <thead>
        <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['warehouseManagementArrList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
            <tr class="odd">	
                 <input type="hidden" name="key_id" value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" />
                 <td <?php if ($_smarty_tpl->tpl_vars['list']->value['status']==0){?>style="color:#999"<?php }?> width="20%"><?php echo $_smarty_tpl->tpl_vars['list']->value['whName'];?>
</td>
                 <td <?php if ($_smarty_tpl->tpl_vars['list']->value['status']==0){?>style="color:#999"<?php }?> width="20%"><?php echo $_smarty_tpl->tpl_vars['list']->value['whCode'];?>
</td>
                 <td <?php if ($_smarty_tpl->tpl_vars['list']->value['status']==0){?>style="color:#999"<?php }?> width="20%"><?php echo $_smarty_tpl->tpl_vars['list']->value['whAddress'];?>
</td>
                 <td <?php if ($_smarty_tpl->tpl_vars['list']->value['status']==0){?>style="color:#999"<?php }?> width="20%"><?php echo $_smarty_tpl->tpl_vars['list']->value['whLocation'];?>
</td>
				 <td width="20%" align="center">
	                 <a href="index.php?mod=warehouseManagement&act=warehouseEdit&editId=<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
">
                	 <button  id='warehouseEdit' value="" />修改</button>
                 	 </a> 
                     &nbsp;
                     &nbsp;
                    <input type="button" id = 'isEnabled' name = 'isEnabled' onclick="isEnabled('<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
',<?php echo $_smarty_tpl->tpl_vars['list']->value['status'];?>
)" <?php if ($_smarty_tpl->tpl_vars['list']->value['status']==0){?>value="启用"<?php }elseif($_smarty_tpl->tpl_vars['list']->value['status']==1){?>value="停用"<?php }?> class="input_button" />	              
                 </td>
                 <!--td width="50%"></td-->                
            </tr>
         <?php } ?>
        </thead>
    <!-- END list -->
    </table>
</div>
<div class="bottomvar">
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>