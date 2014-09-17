<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 09:18:55
         compiled from "/data/web/trans.valsun.cn/html/template/countriesStandardModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:19102572965267733707f369-65270727%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b6e67c269b3f863ca3d681fb93e90ac887292a0' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesStandardModify.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19102572965267733707f369-65270727',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526773370ae396_03765503',
  'variables' => 
  array (
    'title' => 0,
    'cn_name' => 0,
    'en_name' => 0,
    'short_name' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526773370ae396_03765503')) {function content_526773370ae396_03765503($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesStandard&act=index">标准国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>修改标准国家</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">标准国家中文名：</td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="<?php echo $_smarty_tpl->tpl_vars['cn_name']->value;?>
" maxlength="20"/>
                  <span class="red">*</span>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家英文名：</td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="<?php echo $_smarty_tpl->tpl_vars['en_name']->value;?>
" maxlength="20"/>
                  <span class="red">*</span>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家简称：</td>
                <td width="27%" align="left">
                  <input type="text" name="short_name" id="short_name" value="<?php echo $_smarty_tpl->tpl_vars['short_name']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
					<input type="hidden" id="act-id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
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
	id		= $("#act-id").val();
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
	var url  = web_api + "json.php?mod=countriesStandard&act=updateCountriesStandard";
	var data = {"id":id,"cn_name":cn_name,"en_name":en_name,"short_name":short_name};
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