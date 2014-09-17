<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 16:06:48
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcBackScan.html" */ ?>
<?php /*%%SmartyHeaderCode:3040551ff5d18db2503-29468052%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6124f61d2174a1574fffbcdeb9980f7d1d422108' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcBackScan.html',
      1 => 1375685887,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3040551ff5d18db2503-29468052',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5d18dea160_20673967',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5d18dea160_20673967')) {function content_51ff5d18dea160_20673967($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

<div class="fourvar">
            <?php echo $_smarty_tpl->getSubTemplate ("iqcnav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>
    
            </div>
            <div class="servar">
            	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td width="24%">
						  <div style="font-size:24px">检测料号:
				            <input name="sku" type="text" id="sku" onkeydown="checksku()" style="width:150px;height:30px;" class="textinput" />
				          </div>
						  <div id="mstatus" style="font-size:24px"></div>						</td>
					    <td width="76%">&nbsp;</td>
				      </tr>
					</table>
				 </td>
			   </tr>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td width="24%">
						  <div style="font-size:24px">全检数目:
				            <input name="check_num" type="text" id="check_num" onkeydown="checknum()" style="width:150px;height:30px;" class="textinput" />
				         </div>					</td>
			            <td width="76%"><div style="font-size:10px">注意：这里的全检数目就是退回总数，需全测。</div>&nbsp;</td>
				      </tr>
					</table>
				 </td>
			   </tr>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td>
						  <div style="font-size:24px">不良品数:
				            <input name="rejects_num" type="text" id="rejects_num" onkeydown="check_rejects_num()" style="width:150px;height:30px;" class="textinput" />							
                            状态:
						  <select name="rejects_status" id="rejects_status" onkeydown="check_rejects_status()">
						    <option value="0">请选择</option>
						    <option value="1">待退回</option>
						    <option value="2">待定</option>
						    </select>
						  </div>						</td>
			          </tr>
					</table>
				 </td>
			   </tr>			   
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td valign="top">
						  <div style="font-size:24px">不良原因:
				            <textarea name="bad_reason" class="textinput" id="bad_reason" style="width:350px;height:100px;" onkeydown="check_bad_reason()"></textarea>
				          </div>						</td>
			          </tr>
					</table>
				 </td>
			   </tr>

			</table>
            </div>
            <div class="main">
            	
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>
<?php }} ?>