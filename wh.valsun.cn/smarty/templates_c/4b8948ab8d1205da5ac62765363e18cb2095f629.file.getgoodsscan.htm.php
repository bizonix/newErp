<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 19:26:34
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\getgoodsscan.htm" */ ?>
<?php /*%%SmartyHeaderCode:1175753185b6a7a4fd9-23860715%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b8948ab8d1205da5ac62765363e18cb2095f629' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\getgoodsscan.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1175753185b6a7a4fd9-23860715',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53185b6a7e0899_47266680',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53185b6a7e0899_47266680')) {function content_53185b6a7e0899_47266680($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('goodsoutnav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link href="css/common.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="js/getgoods.js"></script>
<link href="css/getgoodsscan-css.css" rel="stylesheet" type="text/css" />
<div>
	<div id="showMsgDiv" style="padding-left:80px; margin-top:10px;">
		<span></span>
	</div>
    <div class="gd_divrow">
        <div class="gd_labeldiv">
           	 发货单号/配货单号:
        </div>
        <div calss="gd_input" style="dispaly:none;">
            <input id="orderidinput" onkeypress="getSkuList(event)" class="orderidinput"/>
        </div>
        <div class="clear:both;">
        </div>
    </div>
    <div class="gd_divrow " style="margin-top:5px;">
        <div class="gd_labeldiv">
        	SKU:
        </div>
        <div calss="gd_input">
            <select id="skulistselect" class="skulist">
        	</select>
        </div>
        <div class="clear:both;">
        </div>
    </div>
	<div class="gd_divrow " style="margin-top:5px;">
        <div class="gd_labeldiv">
        	扫描SKU:
        </div>
        <div calss="gd_input">
           	<input onkeypress="responseskuscan(event)" id="scanskuinput" class="orderidinput"/>
        </div>
        <div class="clear:both;">
        </div>
    </div>
	<div class="gd_divrow " style="margin-top:5px;">
        <div class="gd_labeldiv">
        	数量:
        </div>
        <div calss="gd_input">
           	<input onkeypress="scansubmit(event)" id="skunumberinput" class="orderidinput"/>
        </div>
        <div class="clear:both;">
        </div>
    </div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>