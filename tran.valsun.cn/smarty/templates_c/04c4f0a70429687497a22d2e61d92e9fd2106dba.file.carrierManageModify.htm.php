<?php /* Smarty version Smarty-3.1.12, created on 2013-10-29 15:18:30
         compiled from "/data/web/trans.valsun.cn/html/template/carrierManageModify.htm" */ ?>
<?php /*%%SmartyHeaderCode:286970821526b388f00de14-10257626%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '04c4f0a70429687497a22d2e61d92e9fd2106dba' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/carrierManageModify.htm',
      1 => 1383031105,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '286970821526b388f00de14-10257626',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526b388f04c3c6_94355637',
  'variables' => 
  array (
    'title' => 0,
    'errMsg' => 0,
    'cn_name' => 0,
    'en_name' => 0,
    'ship_type' => 0,
    'min_weight' => 0,
    'max_weight' => 0,
    'ship_day' => 0,
    'ship_note' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526b388f04c3c6_94355637')) {function content_526b388f04c3c6_94355637($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=carrierManage&act=index">运输方式管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
</div>
<div class="main">
<?php if ((!empty($_smarty_tpl->tpl_vars['errMsg']->value))){?>
	    <table width="90%" border="0" cellpadding="0" cellspacing="0" >
			<tr>
				<td>
					<p><?php echo $_smarty_tpl->tpl_vars['errMsg']->value;?>
<br/><br/>
                    <button name="button" type="button" id="history" value="history" onclick="javascript:history.back();"/>返 回</button>
					</p>
				</td>
			</tr>
		</table>
<?php }else{ ?>
    <h1>修改运输方式</h1>
    <form method="post" action="index.php?mod=carrierManage&act=update" onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">运输方式中文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="<?php echo $_smarty_tpl->tpl_vars['cn_name']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">运输方式英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="<?php echo $_smarty_tpl->tpl_vars['en_name']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">物流类型：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="ship_type" id="ship_type">
					<option value="">=请选择=</option>
					<option value="0" <?php if ($_smarty_tpl->tpl_vars['ship_type']->value==0){?>selected="selected"<?php }?>>非快递</option>
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['ship_type']->value==1){?>selected="selected"<?php }?>>快递</option>
					</select>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">重量最小值：</td>
                <td width="27%" align="left">
                  <input type="text" name="min_weight" id="min_weight" value="<?php echo $_smarty_tpl->tpl_vars['min_weight']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">重量最大值：</td>
                <td width="27%" align="left">
                  <input type="text" name="max_weight" id="max_weight" value="<?php echo $_smarty_tpl->tpl_vars['max_weight']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">递送时间：</td>
                <td width="27%" align="left">
                  <input type="text" name="ship_day" id="ship_day" value="<?php echo $_smarty_tpl->tpl_vars['ship_day']->value;?>
" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">备注：</td>
                <td width="27%" align="left">
                  <input type="text" name="ship_note" id="ship_note" value="<?php echo $_smarty_tpl->tpl_vars['ship_note']->value;?>
" maxlength="3"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
					<input type="hidden" id="act-id" name="act-id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/>
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=carrierManage&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
</div>

<script type="text/javascript">
function check(){
	var cn_name = en_name = ship_type = min_weight = max_weigth = ship_day = ship_note = "";
	cn_name = $.trim($("#cn_name").val());
	en_name	= $.trim($("#en_name").val());
	ship_type	= $.trim($("#ship_type").val());
	min_weight	= $.trim($("#min_weight").val());
	max_weight	= $.trim($("#max_weight").val());
	ship_day	= $.trim($("#ship_day").val());
	ship_note	= $.trim($("#ship_note").val());
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
	if (ship_type == "") {
		alertify.error("物流类型不能不选！");
		$("#ship_type").focus();
		return false;
	}
	return true;
}
</script>

<?php }?>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>