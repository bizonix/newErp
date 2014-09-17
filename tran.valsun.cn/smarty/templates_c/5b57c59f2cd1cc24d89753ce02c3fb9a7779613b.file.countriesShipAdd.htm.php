<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 11:44:07
         compiled from "/data/web/trans.valsun.cn/html/template/countriesShipAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:180888455526797ac0bf059-54826581%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b57c59f2cd1cc24d89753ce02c3fb9a7779613b' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesShipAdd.htm',
      1 => 1382586243,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '180888455526797ac0bf059-54826581',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526797ac0e9c26_53953911',
  'variables' => 
  array (
    'title' => 0,
    'countries' => 0,
    'list' => 0,
    'lists' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526797ac0e9c26_53953911')) {function content_526797ac0e9c26_53953911($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesShip&act=index">运输方式国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>添加运输方式国家</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">运输方式国家名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="carrier_name" id="carrier_name" value="" maxlength="20"/>
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
"><?php echo $_smarty_tpl->tpl_vars['list']->value['countryNameEn'];?>
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
"><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
</option>
				<?php } ?>
				</select>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=countriesShip&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var carrier_name = en_name = ship_id = "";
	carrier_name = $.trim($("#carrier_name").val());
	en_name	= $.trim($("#en_name").val());
	ship_id	= $.trim($("#ship_id").val());
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
	var url  = web_api + "json.php?mod=countriesShip&act=addCountriesShip";
	var data = {"carrier_name":carrier_name,"en_name":en_name,"ship_id":ship_id};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("添加成功！",function(){
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