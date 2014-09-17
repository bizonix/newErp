<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 19:18:52
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\skuWeighing.htm" */ ?>
<?php /*%%SmartyHeaderCode:43605318599c867202-48152716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8db09670e8d401bef41c527b746b8a434c2dc639' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\skuWeighing.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '43605318599c867202-48152716',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5318599c8a13c3_74648420',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5318599c8a13c3_74648420')) {function content_5318599c8a13c3_74648420($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('goodsoutnav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link href="css/common.css" rel="stylesheet" type="text/css" />
<script src="js/jSound.js"></script>
<script src="js/sounds.js"></script>
<script language="javascript" src="js/skuWeighing.js"></script>
<script type="text/javascript" src="./js/fancybox.js"></script>
<div class="main" style="min-height:500px">
	<div style="font-size:30px;margin:20px auto auto 60px">SKU：<input type="text" id="sku" style="width:200px; height:35px;font-size:20px;"/></div>
	<div style="height:60px;font-size:24px;margin-left:60px" id="mstatus" >
	</div>
	<div style="width:450px;min-height:200px;float:left;margin-left:40px" >
		<Applet id="app" code="a.class" height=387 width=400>
			<PARAM NAME = ARCHIVE VALUE = "comm.jar" >
			<param name=myName value="kaka"> 
			<param name=mySex value="mail">
			<param name=myNum value=200630170> 
			<param name=myAge value=22>
		</Applet>
	</div>

	<div style="width:800px;min-height:200px;float:right;" >
		<div id="imgshow" style="display:none">
			<a href="javascript:void(0)" id="imgb" class="fancybox">
				<img src="" id="imgs" class="skuimg" width="160" height="160">
			</a>
		</div>
		<div id="flat_order" style=" padding: 10px 10px;font-size:24px;">					
			<fieldset id="flat_fieldset1" style=" float:left; margin-top:5px; padding:3px 0;"><legend>料号重量扫描</legend>			
				<div style="margin-top:10px">
				同步重量:				
				<input name="curweight_flat" type="text" id="curweight_flat" style="width:120px; height:35px;font-size:20px;"/>
				<input type="button" class="input_button" value="手动确认重量" onclick="skuWeight()" />
				</div>
			</fieldset>
		</div>
		
	</div>
</div>

<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>