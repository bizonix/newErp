<?php /* Smarty version Smarty-3.1.12, created on 2013-10-29 20:20:25
         compiled from "/data/web/trans.valsun.cn/html/template/channelManageModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:1707320082526f7c6d7124b9-45358645%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3dae7d0de4ee58f30af68b2bbc495893dc471f48' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/channelManageModify.htm',
      1 => 1383049222,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1707320082526f7c6d7124b9-45358645',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526f7c6d757430_06792997',
  'variables' => 
  array (
    'id' => 0,
    'title' => 0,
    'lists' => 0,
    'list' => 0,
    'ship_id' => 0,
    'ch_name' => 0,
    'ch_alias' => 0,
    'ch_discount' => 0,
    'ch_enabel' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526f7c6d757430_06792997')) {function content_526f7c6d757430_06792997($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=carrierManage&act=index">运输方式管理</a>&nbsp;>>&nbsp;<a href="index.php?mod=channelManage&act=index&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">渠道管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
    <h1>修改渠道</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">运输方式：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="ship_id" id="ship_id">
					<option value="">=请选择=</option>
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
                <td align="right" width="11%">渠道名称：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="ch_name" id="ch_name" value="<?php echo $_smarty_tpl->tpl_vars['ch_name']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">渠道别名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="ch_alias" id="ch_alias" value="<?php echo $_smarty_tpl->tpl_vars['ch_alias']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">渠道折扣：</td>
                <td width="27%" align="left">
                  <input type="text" name="ch_discount" id="ch_discount" value="<?php echo $_smarty_tpl->tpl_vars['ch_discount']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">是否启用：</td>
                <td width="27%" align="left">
				<select name="ch_enabel" id="ch_enabel">
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['ch_enabel']->value==0){?>selected="selected"<?php }?>>启用</option>
					<option value="0" <?php if ($_smarty_tpl->tpl_vars['ch_enabel']->value==1){?>selected="selected"<?php }?>>不启用</option>
				</select>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
					<input type="hidden" id="act-id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=channelManage&act=index&id=<?php echo $_smarty_tpl->tpl_vars['ship_id']->value;?>
'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var ch_name = ch_alias = ch_discount = ch_enabel = ship_id = id = "";
	ship_id 	= $.trim($("#ship_id").val());
	ch_name 	= $.trim($("#ch_name").val());
	ch_alias	= $.trim($("#ch_alias").val());
	ch_discount	= $.trim($("#ch_discount").val());
	ch_enabel	= $.trim($("#ch_enabel").val());
	id			= $("#act-id").val();
	if (ship_id == "" || ship_id == 0) {
		alertify.error("运输方式不能不选！");
		$("#ship_id").focus();
		return false;
	}
	if (ch_name == "") {
		alertify.error("渠道名称不能为空！");
		$("#ch_name").focus();
		return false;
	}
	if (ch_alias == "") {
		alertify.error("渠道别名不能为空！");
		$("#ch_alias").focus();
		return false;
	}
	var url  = web_api + "json.php?mod=channelManage&act=updateChannelManage";
	var data = {"id":id,"ship_id":ship_id,"ch_name":ch_name,"ch_alias":ch_alias,"ch_discount":ch_discount,"ch_enabel":ch_enabel};
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