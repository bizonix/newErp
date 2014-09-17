<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 22:54:13
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\exportXls.htm" */ ?>
<?php /*%%SmartyHeaderCode:30981531197131d11f0-05654317%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87fb756f731686af2f637e77966e80df0fff1372' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\exportXls.htm',
      1 => 1394203133,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30981531197131d11f0-05654317',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_531197132a5951_05919986',
  'variables' => 
  array (
    'ebayAccountList' => 0,
    'list' => 0,
    'curStartTime' => 0,
    'curEndTime' => 0,
    'b2bAccountList' => 0,
    'chkTime' => 0,
    'innerAccountList' => 0,
    'allAccountList' => 0,
    'amazonAccountList' => 0,
    'dresslinkAccountList' => 0,
    'aliexpressAccountList' => 0,
    'key_id' => 0,
    'neweggAccountList' => 0,
    'transType' => 0,
    'combSkuData' => 0,
    'priceInfoUrl' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_531197132a5951_05919986')) {function content_531197132a5951_05919986($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script src="js/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<script src="js/jquery/ui/jquery-ui-sliderAccess.js"></script>
<script src="js/exportXls.js"></script>
<script src="js/My97DatePicker/WdatePicker.js"></script>
<div class="main order-main" align="left">
    <div id="content" >
        <div class='moduleTitle'>
        <h2>&nbsp;</h2>
        </div>
        
		<div id="accordion">
        
        <h3>ebay数据测试导出:</h3>
			<div>
				eBay帐号:
				<select name="ebay_test_account" size="10" multiple="multiple" id="ebay_test_account">
                    <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ebayAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                     <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                    <?php } ?>					
				</select>
				扫描开始时间:
				<input name="ebay_test_start" id="ebay_test_start" type="text" onblur="validate_data('ebay_test_start')" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
				扫描结束时间:
				<input name="ebay_test_end" id="ebay_test_end" type="text" onblur="validate_data('ebay_test_end')" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
				&nbsp;
				<input type="button" value="导出到xls" onclick="exportXls('ebay_test')" />
			</div>
        <h3>ebay销售漏扫描报表导出：</h3>
            <div>
                eBay帐号:
                <select name="ebay_no_scan_account" size="10" multiple="multiple" id="ebay_no_scan_account">
                    <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ebayAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                     <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                    <?php } ?>                  
                </select>
                扫描开始时间:
                <input name="ebay_no_scan_start" id="ebay_no_scan_start" type="text"  value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
                扫描结束时间:
                <input name="ebay_no_scan_end" id="ebay_no_scan_end" type="text"  value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
                &nbsp;
                <input type="button" value="导出到xls" onclick="exportXls('ebay_no_scan')" />
            </div>
        <h3>速卖通标记发货日志导出:</h3>
        <div>速卖通帐号：
            <select name="ali_tag_ship_log_account" id="ali_tag_ship_log_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['b2bAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>
        <!-- end -->  
                查询日期:
            <input name="ali_tag_ship_log_date" id="ali_tag_ship_log_date" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['chkTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('ali_tag_ship_log')" />
        </div>
        <h3>速卖通批量发货单订单格式化导出:</h3>
        <div>速卖通帐号：
            <select name="ali_batch_ship_order_format_account" id="ali_batch_ship_order_format_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['b2bAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>
        <!-- end -->  
            扫描开始时间:
            <input name="ali_batch_ship_order_format_start" id="ali_batch_ship_order_format_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="ali_batch_ship_order_format_end" id="ali_batch_ship_order_format_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('ali_batch_ship_order_format')" />
        </div>
        
        <h3>paypal 退款数据导出:</h3>
            <div>
            退款开始时间:
            <input name="paypal_refund_start" id="paypal_refund_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            退款结束时间:
            <input name="paypal_refund_end" id="paypal_refund_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('paypal_refund')" />
            </div>
        
        <h3>B2B销售报表数据新版导出：</h3>
            <div>
            帐号：
            <select name="b2b_sale_account" size="10" multiple="multiple" id="b2b_sale_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['b2bAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>
            扫描开始时间:
            <input name="b2b_sale_start" id="b2b_sale_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="b2b_sale_end" id="b2b_sale_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('b2b_sale')" />
            </div>

        <h3>国内-销售报表数据新版导出：</h3>
            <div>
                帐号：
                <select name="inner_sale_account" size="10" multiple="multiple" id="inner_sale_account">
                    <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['innerAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                    <?php } ?> 
                </select>
                扫描开始时间:
                <input name="inner_sale_start" id="inner_sale_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
                扫描结束时间:
                <input name="inner_sale_end" id="inner_sale_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />               
                <input type="button" value="导出到xls" onclick="exportXls('inner_sale')" />
            </div>

        <!--<h3>海外销售报表-新版导出:</h3>
            <div>
                eBay帐号：
                <select name="abroad_sale_account" size="6" multiple="multiple" id="abroad_sale_account">
                    <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['allAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                    <?php } ?>
                </select>
                扫描开始时间:
                <input name="abroad_sale_start" id="abroad_sale_start" class="datetime" type="text" onblur="validate_data('abroad_sale_start')" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
                扫描结束时间:
                <input name="abroad_sale_end" id="abroad_sale_end" class="datetime" type="text" onblur="validate_data('abroad_sale_end')" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
                &nbsp;
                <input type="button" value="导出到xls" onclick="exportXls('abroad_sale')" />
            </div>-->
        <h3>亚马逊销售报表导出:</h3>
            <div>
                帐号：
                <select name="amazon_sale_account" size="10" multiple="multiple" id="amazon_sale_account">
                    <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['amazonAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                    <?php } ?> 
                </select>
                扫描开始时间:
                <input name="amazon_sale_start" id="amazon_sale_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
                扫描结束时间:
                <input name="amazon_sale_end" id="amazon_sale_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />               
                <input type="button" value="导出到xls" onclick="exportXls('amazon_sale')" />
            </div>
            
        <h3>DressLink.Com销售报表运费计算数据导出：</h3>
            <div>
            eBay帐号：
            <select name="dresslink_sale_account" id="dresslink_sale_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dresslinkAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>
            </select>

            <select name="dress_type" id="dress_type"><option value="all">全部</option><option value="gift">礼品订单</option><option value="not-gift">非礼品订单</option></select>
            扫描开始时间:
            <input name="dresslink_sale_start" id="dresslink_sale_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="dresslink_sale_end" id="dresslink_sale_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('dresslink_sale')" />
            </div>
        <h3>手工退款记录导出:</h3>
            <div>
            帐号：
            <select name="hand_refund_account" size="10" multiple="multiple" id="hand_refund_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['allAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>

            扫描开始时间:
            <input name="hand_refund_start" id="hand_refund_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="hand_refund_end" id="hand_refund_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('hand_refund')" />
            </div>  			
		
        <h3>速卖通中差评数据导出:</h3>
            <div>
            速卖通帐号：
            <select name="aliexpress_app_account" size="10" multiple="multiple" id="aliexpress_app_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['aliexpressAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['key_id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value;?>
</option>
                <?php } ?>  
            </select>

            扫描开始时间:
            <input name="aliexpress_app_start" id="aliexpress_app_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="aliexpress_app_end" id="aliexpress_app_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('aliexpress_app')" />
            </div> 
		<h3>新蛋数据导出:</h3>
            <div>
            ebay帐号：
            <select name="newegg_export_account" size="10" multiple="multiple" id="newegg_export_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['neweggAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>

            扫描开始时间:
            <input name="newegg_export_start" id="newegg_export_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="newegg_export_end" id="newegg_export_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('newegg_export')" />
            </div>  			
		<h3>邮资报表导出:</h3>
            <div>
            ebay帐号：
            <select name="xlsbaobiao4_account" size="10" multiple="multiple" id="xlsbaobiao4_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['allAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>
            </select>
			<select id="mailway4" name="mailway4">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['transType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['key_id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value;?>
</option>
                <?php } ?>
				<option value="all">综合导出</option>
			</select>
            扫描开始时间:
            <input name="xlsbaobiao4_start" id="xlsbaobiao4_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="xlsbaobiao4_end" id="xlsbaobiao4_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('xlsbaobiao4')" />
            </div>  
		<h3>亚马逊入库订单导出</h3>
            <div>
            亚马逊帐号：
            <select name="amazonInStockExport_account" size="10" multiple="multiple" id="amazonInStockExport_account">
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['amazonAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
                <?php } ?>  
            </select>

            扫描开始时间:
            <input name="amazonInStockExport_start" id="amazonInStockExport_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="amazonInStockExport_end" id="amazonInStockExport_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('amazonInStockExport')" />
            </div>  			

		<h3>手工退款数据导出</h3>
            <div>
			<select id="manualRefundxls_account" style="display:none">
				<option value=""></option>
			</select>
            扫描开始时间:
            <input name="manualRefundxls_start" id="manualRefundxls_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
            扫描结束时间:
            <input name="manualRefundxls_end" id="manualRefundxls_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
            <input type="button" value="导出到xls" onclick="exportXls('manualRefundxls')" />
            </div>  			
        <h3>组合料号价格信息表导出:</h3>
            <div>
				<input type="hidden" name="combSkuPageUrl" id="combSkuPageUrl" value="<?php echo $_smarty_tpl->tpl_vars['combSkuData']->value['url'];?>
" />
				开始时间:
				<input name="combSkuPrice_start" id="combSkuPrice_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
				结束时间:
				<input name="combSkuPrice_end" id="combSkuPrice_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
				<input type="button" value="导出到xls" onclick="exportXls('combSkuPrice')" />
				<div>
				</div>
            </div>
        <h3>海外仓销售报表数据导出:</h3>
            <div>
				帐号：
				<select name="ebay_oversea_account" size="10" multiple="multiple" id="ebay_oversea_account">
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['allAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
					<?php } ?>  
				</select>
				开始时间:
				<input name="ebay_oversea_start" id="ebay_oversea_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
				结束时间:
				<input name="ebay_oversea_end" id="ebay_oversea_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
				<input type="button" value="导出到xls" onclick="exportXls('ebay_oversea')" />
				<div>
				</div>
            </div>
        <h3>paypal纠纷数据导出:</h3>
            <div>
				帐号：
				<select name="paypal_account" size="10" multiple="multiple" id="ebay_oversea_account">
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['allAccountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['list']->value['account'];?>
</option>
					<?php } ?>  
				</select>
				开始时间:
				<input name="paypal_case_start" id="paypal_case_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
				结束时间:
				<input name="paypal_case_end" id="paypal_case_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
				<input type="button" value="导出到xls" onclick="javascript:alert('此功能还未开发!')" />
				<div>
				</div>
            </div>
        <h3>新EUB跟踪号报表导出</h3>
            <div>
				<input type="button" value="导出到xls" onclick="eub_trucknumber()" />
            </div>
		</div>
        <h3>价格信息表新版导出:</h3>
            <div>
				<input type="hidden" name="priceInfoUrl" id="priceInfoUrl" value="<?php echo $_smarty_tpl->tpl_vars['priceInfoUrl']->value;?>
" />
				开始时间:
				<input name="priceInfo_start" id="priceInfo_start" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curStartTime']->value;?>
" />
				结束时间:
				<input name="priceInfo_end" id="priceInfo_end" class="datetime" type="text" value="<?php echo $_smarty_tpl->tpl_vars['curEndTime']->value;?>
" />
				<input type="button" value="导出到xls" onclick="exportXls('priceInfo')" />
				<div>
				</div>
            </div>
    	<div class="clear"></div>
    </div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>