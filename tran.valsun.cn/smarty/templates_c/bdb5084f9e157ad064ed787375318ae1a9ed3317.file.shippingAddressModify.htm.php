<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 11:49:48
         compiled from "/data/web/trans.valsun.cn/html/template/shippingAddressModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:85049528252664b2fe7dce2-54205124%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bdb5084f9e157ad064ed787375318ae1a9ed3317' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/shippingAddressModify.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85049528252664b2fe7dce2-54205124',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664b2feac7c4_28228149',
  'variables' => 
  array (
    'title' => 0,
    'cn_name' => 0,
    'en_name' => 0,
    'addres_code' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664b2feac7c4_28228149')) {function content_52664b2feac7c4_28228149($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=shippingAddress&act=index">发货地址管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>修改平台</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">平台中文名：</td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="<?php echo $_smarty_tpl->tpl_vars['cn_name']->value;?>
" maxlength="20"/>
                  <span class="red">*</span>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">平台英文名：</td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="<?php echo $_smarty_tpl->tpl_vars['en_name']->value;?>
" maxlength="20"/>
                  <span class="red">*</span>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">发货地址代码：</td>
                <td width="27%" align="left">
                  <input type="text" name="addres_code" id="addres_code" value="<?php echo $_smarty_tpl->tpl_vars['addres_code']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
					<input type="hidden" id="act-id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=shippingAddress&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var cn_name = en_name = addres_code = "";
	cn_name = $.trim($("#cn_name").val());
	en_name	= $.trim($("#en_name").val());
	addres_code	= $.trim($("#addres_code").val());
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
	var url  = web_api + "json.php?mod=shippingAddress&act=updateShippingAddress";
	var data = {"id":id,"cn_name":cn_name,"en_name":en_name,"addres_code":addres_code};
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