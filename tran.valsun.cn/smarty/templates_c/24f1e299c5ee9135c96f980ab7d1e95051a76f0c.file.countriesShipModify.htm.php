<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 11:42:47
         compiled from "/data/web/trans.valsun.cn/html/template/countriesShipModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:1520686516526797790efa73-84424065%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '24f1e299c5ee9135c96f980ab7d1e95051a76f0c' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesShipModify.htm',
      1 => 1382586165,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1520686516526797790efa73-84424065',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5267977911e8c1_23831747',
  'variables' => 
  array (
    'title' => 0,
    'carrier_name' => 0,
    'countries' => 0,
    'list' => 0,
    'en_name' => 0,
    'lists' => 0,
    'ship_id' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5267977911e8c1_23831747')) {function content_5267977911e8c1_23831747($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesShip&act=index">运输方式国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>修改运输方式国家</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">运输方式国家名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="carrier_name" id="carrier_name" value="<?php echo $_smarty_tpl->tpl_vars['carrier_name']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="en_name" id="en_name">
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameEn'];?>
" <?php if ($_smarty_tpl->tpl_vars['en_name']->value==$_smarty_tpl->tpl_vars['list']->value['countryNameEn']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameEn'];?>
</option>
					<?php } ?>
					</select>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">运输方式：</td>
                <td width="27%" align="left">
				<select name="ship_id" id="ship_id">
				<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['ship_id']->value==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
</option>
				<?php } ?>
				</select>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
					<input type="hidden" id="act-id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=countriesShip&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var carrier_name = en_name = ship_id = id = "";
	carrier_name = $.trim($("#carrier_name").val());
	en_name	= $.trim($("#en_name").val());
	ship_id	 = $.trim($("#ship_id").val());
	id		= $("#act-id").val();
	if (carrier_name == "") {
		alertify.error("运输方式国家名称不能为空！");
		$("#carrier_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("标准国家英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_api + "json.php?mod=countriesShip&act=updateCountriesShip";
	var data = {"id":id,"carrier_name":carrier_name,"en_name":en_name,"ship_id":ship_id};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("修改成功！",function(){
				window.location.reload();
			});
		}else {
			 alertify.error(res.errMsg);
		   }
	}, "jsonp");
	return false;
}
</script>

<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>