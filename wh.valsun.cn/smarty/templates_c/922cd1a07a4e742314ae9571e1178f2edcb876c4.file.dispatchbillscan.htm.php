<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 20:13:53
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\dispatchbillscan.htm" */ ?>
<?php /*%%SmartyHeaderCode:2224453186106b349d4-12237314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '922cd1a07a4e742314ae9571e1178f2edcb876c4' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\dispatchbillscan.htm',
      1 => 1394108030,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2224453186106b349d4-12237314',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53186106b6e674_32441970',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53186106b6e674_32441970')) {function content_53186106b6e674_32441970($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('goodsoutnav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link href="css/common.css" rel="stylesheet" type="text/css" />
<script src="js/jSound.js"></script>
<script src="js/sounds.js"></script>
<script language="javascript" src="js/dispatchBillScan.js"></script>
<script type="text/javascript" src="./js/fancybox.js"></script>
<div class="main" style="min-height:500px">
    <div style="font-size:30px;margin:20px auto auto 60px">发货单号：<input type="text" id="billId" style="width:200px; height:35px;font-size:20px;"/></div>
    <div style="height:60px;font-size:24px;margin-left:60px" id="mstatus" >
    </div>
</div>

<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>