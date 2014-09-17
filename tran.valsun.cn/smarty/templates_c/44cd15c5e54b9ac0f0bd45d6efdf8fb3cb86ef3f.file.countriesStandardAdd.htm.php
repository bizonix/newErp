<?php /* Smarty version Smarty-3.1.12, created on 2013-10-23 20:05:00
         compiled from "/data/web/trans.valsun.cn/html/template/countriesStandardAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:143720244052677330ed8998-52476141%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44cd15c5e54b9ac0f0bd45d6efdf8fb3cb86ef3f' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesStandardAdd.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '143720244052677330ed8998-52476141',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52677330f01513_84539301',
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52677330f01513_84539301')) {function content_52677330f01513_84539301($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesStandard&act=index">标准国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>添加标准国家</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">标准国家中文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家简称：</td>
                <td width="27%" align="left">
                  <input type="text" name="short_name" id="short_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=countriesStandard&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var cn_name = en_name = short_name = "";
	cn_name = $.trim($("#cn_name").val());
	en_name	= $.trim($("#en_name").val());
	short_name	= $.trim($("#short_name").val());
	if (cn_name == "") {
		alertify.error("中文名称不能为空！");
		$("#cn_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_api + "json.php?mod=countriesStandard&act=addCountriesStandard";
	var data = {"cn_name":cn_name,"en_name":en_name,"short_name":short_name};
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