<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 18:12:01
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\searchSku.htm" */ ?>
<?php /*%%SmartyHeaderCode:12754531849f1e67f96-64924915%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1bfa5a5e8f14cad8f28c29287307ac4a76064ae' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\searchSku.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12754531849f1e67f96-64924915',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'total_cost' => 0,
    'whName' => 0,
    'list' => 0,
    'cate_f' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_531849f1ee8399_32992459',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_531849f1ee8399_32992459')) {function content_531849f1ee8399_32992459($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('whNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/skuStock.js"></script>
<div class="servar wh-servar">
 <div class="servar wh-servar" id="mes" style="display:none"></div>
 <span>库存总金额:<?php echo $_smarty_tpl->tpl_vars['total_cost']->value;?>
</span>
</div>
 <div class="servar ser-ware products-main wh-servar">
                <span>
                	<input name="" id="searchContent" type="text" style="width:300px;" />
                <span>
                <span class="products-action">
                	<a href="javascript:void(0);" id="searchSku">搜 索</a>
                </span>
                <div style="margin-top:10px; margin-left:-100px;">
                	<label>
                		<input name="searchtype" type="radio" value="1" checked="checked" />SKU
                    </label>
                    <label>
                		<input name="searchtype" type="radio" value="2" />仓位
                    </label>
                    <label>
                		<input name="searchtype" type="radio" value="3" />货品名称
                    </label>
                    <label>
                		<input name="searchtype" type="radio" value="4" />采购负责人
                    </label>
                </div>
				<div style="margin-top:10px; margin-left:-45px;">
					<span style="width:60px;">产品状态：</span>
					<select name="online" id="online" style="width:100px">
						<option value="">请选择</option>
						<option value="1">零库存</option>
						<option value="2">下线</option>
						<option value="3">在线</option>
					</select>
					<span style="width:60px;">仓库：</span>
					<select name="warehouse" id="warehouse" style="width:100px">
						<option value="">请选择</option>
						<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['whName']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['whName'];?>
</option>
						<?php } ?>
					</select>
				</div>
				<div style="margin-top:10px; margin-left:-45px;">
					<span style="width:60px;">新/老品：</span>
					<select name="isnew" id="isnew" style="width:100px">
						<option value="">请选择</option>
						<option value="1">新品</option>
						<option value="0">老品</option>
					</select>
					<span style="width:60px;">类别：</span>
					<select id="pid_one" style="width:100px" onchange="change_one();">
						<option value="">请选择</option>
						<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cate_f']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['name'];?>
</option>
						<?php } ?>
					</select>
					<span id="div_two">
					</span>
					<span id="div_three">
					</span>
					<span id="div_four">
					</span>
				</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>