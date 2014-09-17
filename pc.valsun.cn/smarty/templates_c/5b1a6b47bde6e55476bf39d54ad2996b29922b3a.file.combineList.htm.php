<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:31:02
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\combineList.htm" */ ?>
<?php /*%%SmartyHeaderCode:12050526616c33b48c1-12220060%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b1a6b47bde6e55476bf39d54ad2996b29922b3a' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\combineList.htm',
      1 => 1382437859,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12050526616c33b48c1-12220060',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616c3586904_20926076',
  'variables' => 
  array (
    'show_page' => 0,
    'combineList' => 0,
    'value' => 0,
    'vv' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616c3586904_20926076')) {function content_526616c3586904_20926076($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wamp\\www\\ftpPc.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
								  <span>SPU：
								   <input name="combineSpu" type="combineSpu" id="combineSpu" value="<?php echo $_GET['combineSpu'];?>
"/>
								   </span>
                                   <span><button id='searchCombineList'/>搜索</button></span>
                                   &nbsp;
                                   <span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main" >
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
					    <td>SPU</td>
                        <td>SKU</td>
                        <td>成本</td>
						<td>重量</td>
                        <td>真实料号</td>
						<td>长</td>
						<td>宽</td>
						<td>高</td>
						<td>备注</td>
						<td>组合人</td>
						<td>添加时间</td>
                        <td>操作</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combineList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
">
									<td>
									   <?php echo $_smarty_tpl->tpl_vars['value']->value['combineSpu'];?>

									</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineSku'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineCost'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineWeight'];?>
</td>
                                    <td>
                                    <?php  $_smarty_tpl->tpl_vars['vv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vv']->_loop = false;
 $_from = OmAvailableModel::getTrueSkuForCombine($_smarty_tpl->tpl_vars['value']->value['combineSku']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['vv']->key => $_smarty_tpl->tpl_vars['vv']->value){
$_smarty_tpl->tpl_vars['vv']->_loop = true;
?>
                                      <?php echo $_smarty_tpl->tpl_vars['vv']->value['sku'];?>

                                    <?php } ?>
                                    </td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineLength'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineWidth'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineHeight'];?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['combineNote'];?>
</td>
									<td><?php echo getPersonNameById($_smarty_tpl->tpl_vars['value']->value['combineUserId']);?>
</td>
                                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value['addTime'],"Y-m-d H:i");?>
</td>
									<td>
                                        <input type="button" onclick="window.location.href = 'index.php?mod=goods&act=updateCombine&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
'" value="修改"/>
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