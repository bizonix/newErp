<?php /* Smarty version Smarty-3.1.12, created on 2014-03-01 16:15:03
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\orderBatchMove.htm" */ ?>
<?php /*%%SmartyHeaderCode:158045311970729db06-94289753%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3fb93151e0efd55a1d0ea695bb839d837d1a9dcf' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\orderBatchMove.htm',
      1 => 1393658410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158045311970729db06-94289753',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ostatusList' => 0,
    'va' => 0,
    'ostatus' => 0,
    'otypeList' => 0,
    'otype' => 0,
    'transportationList' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53119707372059_92333044',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53119707372059_92333044')) {function content_53119707372059_92333044($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include 'E:\\erpNew\\order.valsun.cn\\lib\\template\\smarty\\plugins\\function.html_options.php';
?><form name="modify" id="modify" style="display:none">
    <div id="orderModify">
    </div>
    <hr />
    <font size="2">料号信息:</font><br />
    <div>
    <table width="100%" align="center">
        <a id="skuModify">
        </a>
    </table>
    </div>
    <hr />
    <font size="2">买家地址:</font><br />
    <div id="userInfo">
    </div>
    <hr />
    <font size="2">运费详情:</font><br />
    <div id="freight">
    </div>
    <hr />
    <font size="2">订单详情:</font><br />
    <div id="orderMessage">
    </div>
    <hr />
    <font size="2">操作记录:</font><br />
    <div id="operationLog">
    </div>
</form>
<form id="thelineupfile" name="thelineupfile" action="index.php?mod=orderindex&act=applyTheLineEUBTrackNumber" enctype="multipart/form-data" title="线下EUB申请" method="post" style="display:none">
<table cellpadding='0' cellspacing='0' width='100%' border='0'>
	<tr>
		<td width="20%"><a href="#">上传模板</a></td>
		<td width="40%"><input name="theline_upfile" type="file" />&nbsp;</td>
		<td width="30%"><span id="thelineshowmsg"></span></td>
   </tr>
   <tr>	
		<td colspan='3'><strong>如果指定列无数据，指定列将不会更新，如果有数据，指定列将被更新成功。</strong></td>
   </tr>
</table>
</form>
<form id="batchMoveForm" name="batchMoveForm" method="post" title="批量修改" style="display:none;" action="">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>	
            <td nowrap="nowrap" scope="row" width="100%">批量修改：您已经选择了<span id="recordnum"></span>条记录，<br />请在需要批量修改的地方输入新值</div></td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="left" bgcolor="#f2f2f2">
                <span style="white-space: nowrap;">
                    <input name="f_status" id="f_status" type="checkbox" value="1" />	
                </span>
            </td>
            <td align="left" bgcolor="#f2f2f2">订单状态</td>
            <td align="left" bgcolor="#f2f2f2">
                <span>
                <select name="batch_ostatus" id="batch_ostatus" style="width:157px" onchange="changeOstatus2()">
                    <option value="">请选择</option>
                    <?php if ($_smarty_tpl->tpl_vars['ostatusList']->value){?>
                    <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ostatusList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['va']->value['statusCode']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['statusName'];?>
</option>
                    <?php } ?>
                    <?php }?>
                </select><select name="batch_otype" id="batch_otype" style="width:157px">
                    <option value="">请选择</option>
                    <?php if ($_smarty_tpl->tpl_vars['otypeList']->value){?>
                    <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['otypeList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['otype']->value==$_smarty_tpl->tpl_vars['va']->value['statusCode']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['statusName'];?>
</option>
                    <?php } ?>
                    <?php }?>
                </select>
                </span>
            </td>
        </tr>
        <tr>
        	<td align="left" bgcolor="#f2f2f2" class="left_txt">
                <span style="white-space: nowrap;">
                    <input name="f_style" id="f_style" type="checkbox" value="1" />
                </span>
            </td>
            <td align="left" bgcolor="#f2f2f2" class="left_txt">发货方式</td>
            <td align="left" bgcolor="#f2f2f2" class="left_txt">
                <span>
                <select id="batch_transport" name="batch_transport">
                <option value="">请选择</option>
                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['transportationList']->value),$_smarty_tpl);?>

                </select>
                </span>
            </td>
        </tr>
        <tr>				 
            <td align="left" colspan="3"><span id="batch_showerror"></span></td>
        </tr>
    </table>
</form>

<form id="copyOrderForm" name="copyOrderForm" method="post" action="" style="display:none;" title="复制和补寄页面">
<input type="hidden" name="send_orderid" id="send_orderid" value="" />
<fieldset id="copyOrderFieldset" style="padding:10px 10px; margin:5px 5px;">
<legend>订单复制</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  	<td>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr height="40px">
        <td width="40%">复制后的订单状态为：</td>
        <td width="60%">
        	<span>
            <select name="copy_ostatus" id="copy_ostatus" style="width:157px" onchange="changeOstatus3()">
                <option value="">请选择</option>
                <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ostatusList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['va']->value['statusCode']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['statusName'];?>
</option>
                <?php } ?>
            </select><select name="copy_otype" id="copy_otype" style="width:157px">
                <option value="">请选择</option>
                <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['otypeList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['otype']->value==$_smarty_tpl->tpl_vars['va']->value['statusCode']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['statusName'];?>
</option>
                <?php } ?>
            </select>
            </span>
        </td>
        </tr>
  <tr>
    <td>
	<span style="color:blue; font-weight:bolder;">复制为补寄：</span><input type="checkbox" name="is_sendreplacement" id="is_sendreplacement" value="1" onclick="showResendArr()" />
	</td>
    <td>
		<select name="resendArr" id="resendArr" style="display:none;">
        <!--<option value="1">补寄全部</option>
        <option value="2">补寄主体</option>
        <option value="3">补寄配件</option>-->
        </select>
		<select name="reason_noteb" id="reason_noteb" style="display:none;">
			<option value="0">=====未选择原因=====</option>
            
		</select>
		<textarea name="extral_noteb" id="extral_noteb" style="display:none; margin-top:5px; height:80px;"></textarea> 
	</td>
  </tr>
    </table>
    </td>
    <td><input name="submit" type="button" value="确认复制" id="buttoncopyorder" onclick="sendreplacement();" /></td>
  </tr>
</table>
</fieldset>
<p><span style="color:red;"><em>操作注意</em>：如果需要复制订单为补寄，选中"复制为补寄" 复选框，弹出补寄状态（补寄全部，补寄主体，补寄配件），选择其中一个并确认提交。</span></p>
</form>

<div id="scrollNav">
    <div id="toTop" class="scrollItem">
        回到顶部
    </div>
    <!--<div id="toBottom" class="scrollItem">
        回到底部
    </div>-->
</div><?php }} ?>