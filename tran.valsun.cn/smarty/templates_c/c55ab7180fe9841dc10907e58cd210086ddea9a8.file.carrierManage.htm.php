<?php /* Smarty version Smarty-3.1.12, created on 2013-10-30 11:40:36
         compiled from "/data/web/tran.valsun.cn/html/template/carrierManage.htm" */ ?>
<?php /*%%SmartyHeaderCode:50311425852707fb4df8715-03425637%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c55ab7180fe9841dc10907e58cd210086ddea9a8' => 
    array (
      0 => '/data/web/tran.valsun.cn/html/template/carrierManage.htm',
      1 => 1383103635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '50311425852707fb4df8715-03425637',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'pageStr' => 0,
    'type' => 0,
    'key' => 0,
    'lists' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52707fb4e606c6_26177477',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52707fb4e606c6_26177477')) {function content_52707fb4e606c6_26177477($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=carrierManage&act=index">运输方式管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
	<div class="pagination">
		<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

	</div>
</div>
<div class="servar">
	<span>
		<select id="type">
			<option value='0'>请选择搜索条件</option>
			<option value='carrierNameCn'<?php if (($_smarty_tpl->tpl_vars['type']->value=='carrierNameCn')){?> selected="selected"<?php }?>>运输方式中文名</option>
			<option value='carrierNameEn'<?php if (($_smarty_tpl->tpl_vars['type']->value=='carrierNameEn')){?> selected="selected"<?php }?>>运输方式英文名</option>
		</select>
	</span>
	<span>
		<input type="text" id="key" value = "<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"/>
	</span>
	<span>
		<a href="javascript:void(0);" id="search">搜 索</a>
	</span>
	<span>
		<a href="index.php?mod=carrierManage&act=add">添加</a>
	</span>
</div>
<div class="main">
	<table cellspacing="0" width="100%">
		<tr class="title purchase-title">
			<th>中文名称</th>
			<th>英文名称</th>
			<th>物流类型</th>
			<th>重量范围最小值</th>
			<th>重量范围最大值</th>
			<th>递送时间</th>
			<th>备注</th>
			<th>状态</th>
			<th>添加时间</th>
			<th>操作</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameEn'];?>
</td>
			<td><?php if (empty($_smarty_tpl->tpl_vars['list']->value['type'])){?>非快递<?php }else{ ?>快递<?php }?></td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['weightMin'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['weightMax'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['timecount'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['note'];?>
</td>
			<td><?php if (empty($_smarty_tpl->tpl_vars['list']->value['is_delete'])){?>启用<?php }else{ ?>禁用<?php }?></td>
			<td><?php if (empty($_smarty_tpl->tpl_vars['list']->value['createdTime'])){?>no<?php }else{ ?><?php echo date('Y-m-d H:i:s',$_smarty_tpl->tpl_vars['list']->value['createdTime']);?>
<?php }?></td>
			<td><a href="index.php?mod=channelManage&act=index&id=<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><b>渠道</b></a> | <a href="index.php?mod=carrierManage&act=modify&id=<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><b>编辑</b></a> | <a href="javascript:void(0)" onclick="del_info(<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['list']->value['is_delete'];?>
)"><b><?php if (empty($_smarty_tpl->tpl_vars['list']->value['is_delete'])){?>禁用<?php }else{ ?>启用<?php }?></b></a></td>
		</tr>
		<?php } ?>
	</table>
</div>
<div class="bottomvar">
	<div class="pagination">
		<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

	</div>
</div>

<script type="text/javascript">
//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=carrierManage&act=index&type="+type+"&key="+key;
	} else {
		window.location.href = "index.php?mod=carrierManage&act=index";
	}
});
//删除入口
function del_info(id,status){
	var url  = web_api + "json.php?mod=carrierManage&act=delCarrierManage";
	var tip_del = "";
	if (status == 0){
		tip_del = "禁用";
		status = 1;
	} else {
		tip_del = "启用";
		status = 0;
	}
	var data = {"id":id,"status":status};
	alertify.confirm("真的要"+ tip_del +"此运输方式吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if(res.errCode == 0){
					alertify.alert("操作成功！",function(){
						window.location.reload();
					});
				}else {
					 alertify.error(res.errMsg);
				   }
			}, "jsonp");
		}
	});
}
</script>

<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>