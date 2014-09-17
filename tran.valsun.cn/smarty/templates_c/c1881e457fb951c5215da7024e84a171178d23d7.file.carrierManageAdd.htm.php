<?php /* Smarty version Smarty-3.1.12, created on 2013-10-30 14:18:28
         compiled from "/data/web/tran.valsun.cn/html/template/carrierManageAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:12833141195270a4b40f5305-47265768%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1881e457fb951c5215da7024e84a171178d23d7' => 
    array (
      0 => '/data/web/tran.valsun.cn/html/template/carrierManageAdd.htm',
      1 => 1383103635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12833141195270a4b40f5305-47265768',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'errMsg' => 0,
    'platFormlist' => 0,
    'list' => 0,
    'addrlist' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5270a4b413bc35_86028495',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5270a4b413bc35_86028495')) {function content_5270a4b413bc35_86028495($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=carrierManage&act=add'"/>返 回</button>
					</p>
				</td>
			</tr>
		</table>
	<?php }else{ ?>
    <h1>添加运输方式</h1>
    <form method="post" action="index.php?mod=carrierManage&act=insert" onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="right" width="11%">运输方式中文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="cn_name" id="cn_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">运输方式英文名：<span class="red">*</span></td>
                <td width="27%" align="left">
                  <input type="text" name="en_name" id="en_name" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">所属平台：<span class="red">*</span></td>
                <td width="27%" align="left">
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['platFormlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <label title="<?php echo $_smarty_tpl->tpl_vars['list']->value['platformNameEn'];?>
"><input type="checkbox" id="plat_name" style="vertical-align:middle" name="plat_name[]" value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"/><?php echo $_smarty_tpl->tpl_vars['list']->value['platformNameCn'];?>
</label>
					<?php } ?>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">发货地址：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="ship_add" id="ship_add">
					<option value="">=请选择=</option>
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addrlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['addressNameCn'];?>
</option>
					<?php } ?>
					</select>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">物流类型：<span class="red">*</span></td>
                <td width="27%" align="left">
					<select name="ship_type" id="ship_type">
					<option value="">=请选择=</option>
					<option value="0">非快递</option>
					<option value="1">快递</option>
					</select>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">重量最小值：</td>
                <td width="27%" align="left">
                  <input type="text" name="min_weight" id="min_weight" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">重量最大值：</td>
                <td width="27%" align="left">
                  <input type="text" name="max_weight" id="max_weight" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">递送时间：</td>
                <td width="27%" align="left">
                  <input type="text" name="ship_day" id="ship_day" value="" maxlength="20"/>
                </td>
			</tr>
			<tr>
                <td align="right" width="11%">备注：</td>
                <td width="27%" align="left">
                  <input type="text" name="ship_note" id="ship_note" value="" maxlength="30"/>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center">
                    <button name="button" type="submit" id="submit-btn" value="submit" />提 交</button>
                    <button name="button" type="button" id="history" value="history" onclick="location.href='index.php?mod=carrierManage&act=index'"/>返 回</button>
                </td>
            </tr>
		</table>
	</form>
	<script type="text/javascript">
	function check(){
		var cn_name = en_name = ship_add = plArr = ship_type = min_weight = max_weigth = ship_day = ship_note = "";
		cn_name = $.trim($("#cn_name").val());
		en_name	= $.trim($("#en_name").val());
		ship_add	= $.trim($("#ship_add").val());
		ship_type	= $.trim($("#ship_type").val());
		min_weight	= $.trim($("#min_weight").val());
		max_weight	= $.trim($("#max_weight").val());
		ship_day	= $.trim($("#ship_day").val());
		ship_note	= $.trim($("#ship_note").val());
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
		var plArr = $('input[id="plat_name"]:checked');
		if(plArr.length == 0){
			alertify.error("所属平台不能不选!");
			return false;
		}
		if (ship_add == "") {
			alertify.error("发货地址不能不选！");
			$("#ship_add").focus();
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
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>