<?php /* Smarty version Smarty-3.1.12, created on 2013-10-26 15:48:42
         compiled from "/data/web/trans.valsun.cn/html/template/shippingAddressAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:204468316452664b2b9ea6b6-93081922%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f63ad49e19989734389a8e47575e02c749ebeb76' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/shippingAddressAdd.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204468316452664b2b9ea6b6-93081922',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664b2ba12ac6_46540216',
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664b2ba12ac6_46540216')) {function content_52664b2ba12ac6_46540216($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=shippingAddress&act=index">发货地址管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>添加发货地址</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">发货地址中文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">发货地址英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">发货地址代码：</td>
                <td width="27%" align="left">
                  <input type="text" name="addres_code" id="addres_code" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">大卖家：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="seller" id="seller" value="" maxlength="10"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=shippingAddress&act=index'"/>返 回</button>
                </td>
            </tr>
			
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var cn_name = en_name = addres_code = seller =  "";
	cn_name 	= $.trim($("#cn_name").val());
	en_name		= $.trim($("#en_name").val());
	addres_code	= $.trim($("#addres_code").val());
	seller		= $.trim($("#seller").val());
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
	if (seller == "") {
		alertify.error("大卖家不能为空！");
		$("#seller").focus();
		return false;
	}	
	var url  = web_api + "json.php?mod=shippingAddress&act=addShippingAddress";
	var data = {"cn_name":cn_name,"en_name":en_name,"addres_code":addres_code,"seller":seller};
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