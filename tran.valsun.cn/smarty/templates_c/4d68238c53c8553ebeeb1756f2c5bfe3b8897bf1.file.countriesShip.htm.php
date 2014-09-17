<?php /* Smarty version Smarty-3.1.12, created on 2013-10-24 08:59:54
         compiled from "/data/web/trans.valsun.cn/html/template/countriesShip.htm" */ ?>
<?php /*%%SmartyHeaderCode:1830277440526797441e4215-02025167%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d68238c53c8553ebeeb1756f2c5bfe3b8897bf1' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/countriesShip.htm',
      1 => 1382576180,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1830277440526797441e4215-02025167',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52679744231712_55987504',
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
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52679744231712_55987504')) {function content_52679744231712_55987504($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=countriesShip&act=index">运输方式国家列表管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	 </div>
	<div class="pagination">
		<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

	</div>
</div>
<div class="servar">
	<span>
		<select id="type">
			<option value='0'>请选择搜索条件</option>
			<option value='carrier_country'<?php if (($_smarty_tpl->tpl_vars['type']->value=='carrier_country')){?> selected="selected"<?php }?>>运输方式国家名</option>
			<option value='countryName'<?php if (($_smarty_tpl->tpl_vars['type']->value=='countryName')){?> selected="selected"<?php }?>>标准国家英文名</option>
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
		<a href="index.php?mod=countriesShip&act=add">添加</a>
	</span>
</div>
<div class="main">
	<table cellspacing="0" width="100%">
		<tr class="title purchase-title">
			<th>运输方式国家名称</th>
			<th>标准国家英文名称</th>
			<th>运输方式</th>
			<th>添加时间</th>
			<th>操作</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['carrier_country'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['countryName'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['list']->value['carrierNameCn'];?>
</td>
			<td><?php if (empty($_smarty_tpl->tpl_vars['list']->value['createdTime'])){?>暂无<?php }else{ ?><?php echo date('Y-m-d H:i:s',$_smarty_tpl->tpl_vars['list']->value['createdTime']);?>
<?php }?></td>
			<td><a href="index.php?mod=countriesShip&act=modify&id=<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><b>编辑</b></a> | <a href="javascript:void(0)" onclick="del_info(<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
)"><b>删除</b></a></td>
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
		window.location.href = "index.php?mod=countriesShip&act=index&type="+type+"&key="+key;
	} else {
		window.location.href = "index.php?mod=countriesShip&act=index";
	}
});
//删除入口
function del_info(id){
	var url  = web_api + "json.php?mod=countriesShip&act=delCountriesShip";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if(res.errCode == 0){
					alertify.alert("删除成功！",function(){
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