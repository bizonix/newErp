<?php /* Smarty version Smarty-3.1.12, created on 2013-10-25 16:06:56
         compiled from "/data/web/trans.valsun.cn/html/template/shipfeeQuery.htm" */ ?>
<?php /*%%SmartyHeaderCode:4636765575268efd1962532-15851672%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8bd2d13fb523f3a7934983ba777beb64ac2f58e' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/shipfeeQuery.htm',
      1 => 1382687928,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4636765575268efd1962532-15851672',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5268efd19c4723_58112921',
  'variables' => 
  array (
    'title' => 0,
    'addrlist' => 0,
    'list' => 0,
    'ship_add' => 0,
    'carrierlist' => 0,
    'ship_carrier' => 0,
    'countrylist' => 0,
    'ship_country' => 0,
    'ship_weight' => 0,
    'errMsg' => 0,
    'lists' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5268efd19c4723_58112921')) {function content_5268efd19c4723_58112921($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
" <?php if ($_smarty_tpl->tpl_vars['ship_add']->value==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['addressNameCn'];?>
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
" <?php if ($_smarty_tpl->tpl_vars['ship_carrier']->value==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
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
" <?php if ($_smarty_tpl->tpl_vars['ship_country']->value==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameEn'];?>
--<?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameCn'];?>
</option>
		<?php } ?>
		</select>
	</span>
	<span>
		重量:
		<input type="text" name="ship_weight" id="ship_weight" value = "<?php echo $_smarty_tpl->tpl_vars['ship_weight']->value;?>
" style="width:50px;ime-mode:Disabled" onkeyup="check_float(this)" onafterpaste="check_float(this)" onblur="check_float(this)"/> KG
	</span>
	<span>
        <button name="button" type="submit" id="submit-btn" value="submit" />查询运费</button>
	</span>
	</form>
</div>
<div class="main">
	
	<table cellspacing="0" width="100%">
		<?php if (empty($_smarty_tpl->tpl_vars['errMsg']->value)){?>
			<tr class="title purchase-title">
				<th>运输方式</th>
				<th>渠道</th>
				<th>分区</th>
				<th>费用</th>
				<th>折扣率</th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
			<tr>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['carriername'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['chname'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['paname'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['shipfee'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['rate'];?>
</td>
			</tr>
			<?php } ?>
		<?php }else{ ?>
			<tr><td>
			<p><?php echo $_smarty_tpl->tpl_vars['errMsg']->value;?>

			<br/><br/>
			<button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=shipfeeQuery&act=index'"/>返 回</button>
			</p>
			<td></tr>
		<?php }?>
	</table>
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