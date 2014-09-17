<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 11:32:11
         compiled from "/data/web/trans.valsun.cn/html/template/countriesSmallAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:13033578925267879f668b30-99197843%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5242de73c1bd2027347fb8c6446f938880dcab9a' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesSmallAdd.htm',
      1 => 1382585525,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13033578925267879f668b30-99197843',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5267879f6918a3_06810525',
  'variables' => 
  array (
    'title' => 0,
    'lists' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5267879f6918a3_06810525')) {function content_5267879f6918a3_06810525($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesSmall&act=index">小语种国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>添加小语种国家</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">小语种国家名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="small_name" id="small_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">标准国家英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="en_name" id="en_name">
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
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
                <td align="right" width="11%">转换语种类型码：</td>
                <td width="27%" align="left">
				<select name = "code_name" id = "code_name">
				<option value = "1">1:西班牙转英文</option>
				<option value = "2">2:法国转英文</option>
				<option value = "3">3:德文转英文</option>
				<option value = "4">4:俄文转英文</option>
				<option value = "5">5:意大利文转英文</option>
				<option value = "6">6:拉丁文转英文</option>
				<option value = "7">7:阿拉伯文转英文</option>
				<option value = "8">8:日文转英文</option>
				<option value = "9">9:韩文转英文</option>
				<option value = "10">10:泰文转英文</option>
				<option value = "11">11:葡萄牙语转英文</option>
				</select>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=countriesSmall&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var small_name = en_name = code_name = "";
	small_name = $.trim($("#small_name").val());
	en_name	= $.trim($("#en_name").val());
	code_name	= $.trim($("#code_name").val());
	if (small_name == "") {
		alertify.error("小语种国家名称不能为空！");
		$("#small_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("标准国家英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_api + "json.php?mod=countriesSmall&act=addCountriesSmall";
	var data = {"small_name":small_name,"en_name":en_name,"code_name":code_name};
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