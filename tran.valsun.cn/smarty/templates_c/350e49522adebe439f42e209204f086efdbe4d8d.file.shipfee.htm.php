<?php /* Smarty version Smarty-3.1.12, created on 2013-10-30 11:40:40
         compiled from "/data/web/tran.valsun.cn/html/template/shipfee.htm" */ ?>
<?php /*%%SmartyHeaderCode:204595240552707fb825b9e4-82653662%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '350e49522adebe439f42e209204f086efdbe4d8d' => 
    array (
      0 => '/data/web/tran.valsun.cn/html/template/shipfee.htm',
      1 => 1383103635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204595240552707fb825b9e4-82653662',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'addrlist' => 0,
    'list' => 0,
    'carrierlist' => 0,
    'countrylist' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52707fb82ac974_49728540',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52707fb82ac974_49728540')) {function content_52707fb82ac974_49728540($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=shipfeeQuery&act=index">运费查询</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="servar">
	<form name="form" action="index.php?mod=shipfeeQuery&act=query" method="post" onSubmit="return check()">
	<span>
		发货地址:
		<select name="ship_add" id="ship_add">
		<option value="">=请选择=</option>
		<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addrlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['addressNameCn'];?>
</option>
		<?php } ?>
		</select>
	</span>
	<span>
		发货方式:
		<select name="ship_carrier" id="ship_carrier">
		<option value="">=请选择=</option>
		<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carrierlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
</option>
		<?php } ?>
		</select>
	</span>
	<span>
		发往国家:
		<select name="ship_country" id="ship_country">
		<option value="">=请选择=</option>
		<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countrylist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameEn'];?>
--<?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameCn'];?>
</option>
		<?php } ?>
		</select>
	</span>
	<span>
		重量:
		<input type="text" name="ship_weight" id="ship_weight" value = "" style="width:50px;ime-mode:Disabled" onkeyup="check_float(this)" onafterpaste="check_float(this)" onblur="check_float(this)"/> KG
	</span>
	<span>
        <button name="button" type="submit" id="submit-btn" value="submit" />查询运费</button>
	</span>
	</form>
</div>

<script type="text/javascript">
function check(){
	var ship_add = ship_carrier = ship_country = ship_weight = "";
	ship_add 	 = $.trim($("#ship_add").val());
	ship_carrier = $.trim($("#ship_carrier").val());
	ship_country = $.trim($("#ship_country").val());
	ship_weight  = $.trim($("#ship_weight").val());
	if (ship_add == "") {
		alertify.error("发货地址不能不选！");
		$("#ship_add").focus();
		return false;
	}
	if (ship_country == "") {
		alertify.error("发往国家不能不选！");
		$("#ship_country").focus();
		return false;
	}
	if (ship_weight == "") {
		alertify.error("重量不能不填写！");
		$("#ship_weight").focus();
		return false;
	}
	return true;
}
</script>

<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>