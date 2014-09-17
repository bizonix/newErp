<?php /* Smarty version Smarty-3.1.12, created on 2014-06-09 18:51:11
         compiled from "/data/web/re.order.valsun.cn/html/template/v1/orderindex.htm" */ ?>
<?php /*%%SmartyHeaderCode:1380776795395819646c6a5-03709784%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd73562326f2ae5bed28f4be4eedd2ccf1898cee4' => 
    array (
      0 => '/data/web/re.order.valsun.cn/html/template/v1/orderindex.htm',
      1 => 1402311061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1380776795395819646c6a5-03709784',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53958196945922_00353992',
  'variables' => 
  array (
    'plataccount' => 0,
    'pid' => 0,
    'g_platformId' => 0,
    'accountid' => 0,
    'g_accountId' => 0,
    'g_page' => 0,
    'ostatus' => 0,
    'otype' => 0,
    'searchIsNote' => 0,
    'searchReviews' => 0,
    'searchIsBuji' => 0,
    'transportation' => 0,
    'vf' => 0,
    'g_transportation' => 0,
    'searchIsLock' => 0,
    'statusmenus' => 0,
    'statusid' => 0,
    'g_ostatus' => 0,
    'typeid' => 0,
    'g_otype' => 0,
    'g_Keywords' => 0,
    'g_KeywordsType' => 0,
    'g_pnum' => 0,
    'searchOrderType' => 0,
    'searchSku' => 0,
    'searchOrderTime1' => 0,
    'searchCountry' => 0,
    'searchState' => 0,
    'searchCity' => 0,
    'searchZipCode' => 0,
    'searchTimeType' => 0,
    'searchOrderTime12' => 0,
    'searchOrderTime2' => 0,
    'status' => 0,
    'show_page' => 0,
    'omOrderList' => 0,
    'orderlist' => 0,
    'orderData' => 0,
    'AbOrderShow' => 0,
    'omOrderId' => 0,
    'orderUserInfoData' => 0,
    'orderWarehouse' => 0,
    'orderExtenData' => 0,
    'orderTracknumber' => 0,
    'orderTrack' => 0,
    'orderDetailData' => 0,
    'orderDetail' => 0,
    'skuinfos' => 0,
    'combinesku' => 0,
    'orderDetailExten' => 0,
    'realsku' => 0,
    'skudaily' => 0,
    'orderAudit' => 0,
    'orderAuditVal' => 0,
    'ssku' => 0,
    'statusMenu' => 0,
    'vk' => 0,
    'combinePackage' => 0,
    'orderNote' => 0,
    've' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53958196945922_00353992')) {function content_53958196945922_00353992($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/data/web/re.order.valsun.cn/lib/template/smarty/plugins/modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link type="text/css" rel="stylesheet" href="css/orderindex.css">
<script language="javascript" src="js/orderindex.js"></script>
<script language="javascript" src="js/orderRefund.js"></script>
<script src="./js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript" src="./js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" type="text/css" href="./js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />

<div class="fourvar order-fourvar">
    <form action="" method="get" id="getOrderList">
    <table>
        <tr>
            <td style="padding-left:17px;">
                平台：
            </td>
            <td>
                <select name="platformId" id="platformId" style="width:157px" onchange="changePlatform()">
                <option value="">全部</option>
                <?php  $_smarty_tpl->tpl_vars['accounts'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accounts']->_loop = false;
 $_smarty_tpl->tpl_vars['pid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['plataccount']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accounts']->key => $_smarty_tpl->tpl_vars['accounts']->value){
$_smarty_tpl->tpl_vars['accounts']->_loop = true;
 $_smarty_tpl->tpl_vars['pid']->value = $_smarty_tpl->tpl_vars['accounts']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['g_platformId']->value==$_smarty_tpl->tpl_vars['pid']->value){?>selected="selected"<?php }?>><?php echo get_platnamebyid($_smarty_tpl->tpl_vars['pid']->value);?>
</option>
                <?php } ?>
                </select>
            </td>
            <td style="padding-left:17px;">
                账号：
            </td>
            <td>
                <span id="selectAccountList">
                <select name="accountId" id="accountId" style="width:157px">
                <option value="">全部账号</option>
                <?php if ($_smarty_tpl->tpl_vars['g_platformId']->value){?>
                <?php  $_smarty_tpl->tpl_vars['accountid'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accountid']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['plataccount']->value[$_smarty_tpl->tpl_vars['g_platformId']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accountid']->key => $_smarty_tpl->tpl_vars['accountid']->value){
$_smarty_tpl->tpl_vars['accountid']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['accountid']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['g_accountId']->value==$_smarty_tpl->tpl_vars['accountid']->value){?>selected="selected"<?php }?>><?php echo get_accountnamebyid($_smarty_tpl->tpl_vars['accountid']->value);?>
</option>
                <?php } ?>
                <?php }?>
                </select>
                </span>
            </td>
            <td style="padding-left:15px;">
                留言：
            </td>
            <input type="hidden" name="mod" value="Order" />
            <input type="hidden" name="act" value="index" />
            <input type="hidden" name="search" value="1" />
            <input type="hidden" name="page" value="<?php echo $_smarty_tpl->tpl_vars['g_page']->value;?>
" />
            <!--<input type="hidden" id="ostatus" name="ostatus" value="<?php echo $_smarty_tpl->tpl_vars['ostatus']->value;?>
" />
            <input type="hidden" name="otype" value="<?php echo $_smarty_tpl->tpl_vars['otype']->value;?>
" />-->
            <td>
                <select name="isNote" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchIsNote']->value==1){?>selected="selected"<?php }?>>有留言</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchIsNote']->value==2){?>selected="selected"<?php }?>>无留言</option>
                </select>
            </td>
            <td style="padding-left:17px;">
                评价：
            </td>
            <td>
                <select name="reviews" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchReviews']->value==1){?>selected="selected"<?php }?>>无评论</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchReviews']->value==2){?>selected="selected"<?php }?>>好评</option>
                    <option value="3" <?php if ($_smarty_tpl->tpl_vars['searchReviews']->value==3){?>selected="selected"<?php }?>>中评</option>
                    <option value="4" <?php if ($_smarty_tpl->tpl_vars['searchReviews']->value==4){?>selected="selected"<?php }?>>差评</option>
                </select>
            </td>
            <td style="padding-left:17px;">
                补寄：
            </td>
            <td>
                <select name="isBuji" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchIsBuji']->value==1){?>selected="selected"<?php }?>>是</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchIsBuji']->value==2){?>selected="selected"<?php }?>>否</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-left:13px;">
                运输方式：
            </td>
            <td id="selectTransportation">
                <select name="transportation" id="transportation" style="width:157px">
                    <option value="">未设置运输方式</option>
                    <?php  $_smarty_tpl->tpl_vars['vf'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vf']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['transportation']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['vf']->key => $_smarty_tpl->tpl_vars['vf']->value){
$_smarty_tpl->tpl_vars['vf']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['vf']->value['id'];?>
" <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['vf']->value['id'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_smarty_tpl->tpl_vars['g_transportation']->value==$_tmp1){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['vf']->value['carrierNameCn'];?>
</option>
                    <?php } ?>
                </select>
            </td>
            <td style="padding-left:17px;">
                锁定：
            </td>
            <td>
                <select name="isLock" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchIsLock']->value==1){?>selected="selected"<?php }?>>是</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchIsLock']->value==2){?>selected="selected"<?php }?>>否</option>
                </select>
            </td>
            <td style="padding-left:17px;">
                状态：
            </td>
            <td>
                <select name="ostatus" id="ostatus" style="width:157px" onchange="changeOstatus()">
                    <option value="*">--ALL--</option>
                    <?php  $_smarty_tpl->tpl_vars['types'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['types']->_loop = false;
 $_smarty_tpl->tpl_vars['statusid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['statusmenus']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['types']->key => $_smarty_tpl->tpl_vars['types']->value){
$_smarty_tpl->tpl_vars['types']->_loop = true;
 $_smarty_tpl->tpl_vars['statusid']->value = $_smarty_tpl->tpl_vars['types']->key;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['statusid']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['statusid']->value==$_smarty_tpl->tpl_vars['g_ostatus']->value){?>selected="selected"<?php }?>><?php echo get_statusmenunamebyid($_smarty_tpl->tpl_vars['statusid']->value);?>
</option>
                    <?php } ?>
                </select>
            </td>
            <td style="padding-left:17px;">
                类别：
            </td>
            <td>
                <select name="otype" id="otype" style="width:157px">
                    <option value="*">--ALL--</option>
                    <?php if ($_smarty_tpl->tpl_vars['g_ostatus']->value){?>
                    <?php  $_smarty_tpl->tpl_vars['typeid'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['typeid']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['statusmenus']->value[$_smarty_tpl->tpl_vars['g_ostatus']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['typeid']->key => $_smarty_tpl->tpl_vars['typeid']->value){
$_smarty_tpl->tpl_vars['typeid']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['typeid']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['typeid']->value==$_smarty_tpl->tpl_vars['g_otype']->value){?>selected="selected"<?php }?>><?php echo get_statusmenunamebyid($_smarty_tpl->tpl_vars['typeid']->value);?>
</option>
                    <?php } ?>
                    <?php }?>
                </select>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="padding-left:17px;">
                关键字:
            </td>
            <td style="padding-left:19px;">
                <input name="Keywords" value="<?php echo $_smarty_tpl->tpl_vars['g_Keywords']->value;?>
" />
            </td>
            <td style="padding-left:15px;">
                <select name="KeywordsType">
                    <option value="*" style="width:157px;">请选择关键字类型</option>
                    <option value="platformUsername" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='platformUsername'){?>selected="selected"<?php }?>>买家ID</option>
                    <option value="email" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='email'){?>selected="selected"<?php }?>>买家邮箱</option>
                    <option value="recordNumber" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='recordNumber'){?>selected="selected"<?php }?>>RecordNo.</option>
                    <option value="PayPalPaymentId" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='PayPalPaymentId'){?>selected="selected"<?php }?>>paypal交易ID</option>
                    <option value="tracknumber" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='tracknumber'){?>selected="selected"<?php }?>>跟踪号</option>
                    <option value="id" <?php if ($_smarty_tpl->tpl_vars['g_KeywordsType']->value=='id'){?>selected="selected"<?php }?>>系统编号</option>
                </select>
            </td>
            <td style="padding-left:17px;">
                每页显示数量设置:
            </td>
            <td style="padding-left:15px;">
                <select id="pnum" name="pnum">
                    <option value="5"   <?php if ($_smarty_tpl->tpl_vars['g_pnum']->value==5){?>selected="selected"<?php }?>>极速浏览（每页5条）</option>
                    <option value="20"  <?php if ($_smarty_tpl->tpl_vars['g_pnum']->value==20){?>selected="selected"<?php }?><?php if ($_smarty_tpl->tpl_vars['g_pnum']->value==0){?>selected="selected"<?php }?>>高速浏览（每页20条）</option>
                    <option value="50"  <?php if ($_smarty_tpl->tpl_vars['g_pnum']->value==50){?>selected="selected"<?php }?>>普通浏览（每页50条）</option>
                    <option value="100" <?php if ($_smarty_tpl->tpl_vars['g_pnum']->value==100){?>selected="selected"<?php }?>>懒人浏览（每页100条）</option>
                </select>
            </td>
        </tr>
    </table>
    <table id="AdvancedSearch" class="advanced-search" style="<?php if (($_smarty_tpl->tpl_vars['searchOrderType']->value!='')||($_smarty_tpl->tpl_vars['searchSku']->value!='')||($_smarty_tpl->tpl_vars['searchOrderTime1']->value!='')){?>display:block<?php }?><?php if (($_smarty_tpl->tpl_vars['searchOrderType']->value=='')&&($_smarty_tpl->tpl_vars['searchSku']->value=='')&&($_smarty_tpl->tpl_vars['searchOrderTime1']->value=='')){?>display:none<?php }?>">
        <tr>
            <td style="padding-left:17px;">
                订单种类：
            </td>
            <td>
                <select name="selectOrderType">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchOrderType']->value==1){?>selected="selected"<?php }?>>单料号单件</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchOrderType']->value==2){?>selected="selected"<?php }?>>单料号多件</option>
                    <option value="3" <?php if ($_smarty_tpl->tpl_vars['searchOrderType']->value==3){?>selected="selected"<?php }?>>多料号</option>
                </select>
            </td>
            <td style="padding-left:17px;">
                SKU：
            </td>
            <td>
                <input name="sku" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchSku']->value;?>
"/>
            </td>

        </tr>
		<tr>
			<td style="padding-left:17px;">
                国家:
            </td>
            <td>
                <input name="country" value="<?php echo $_smarty_tpl->tpl_vars['searchCountry']->value;?>
" />
            </td>
			<td style="padding-left:17px;">
                州:
            </td>
            <td>
                <input name="state" value="<?php echo $_smarty_tpl->tpl_vars['searchState']->value;?>
" />
            </td>
			<td style="padding-left:17px;">
                城市:
            </td>
            <td style="padding-left:19px;">
                <input name="city" value="<?php echo $_smarty_tpl->tpl_vars['searchCity']->value;?>
" />
            </td>
			<td style="padding-left:17px;">
                邮编:
            </td>
            <td style="padding-left:19px;">
                <input name="zipCode" value="<?php echo $_smarty_tpl->tpl_vars['searchZipCode']->value;?>
" />
            </td>
		</tr>
        <tr>
            <td style="padding-left:17px;">
                <select name="searchTimeType" id="searchTimeType">
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchTimeType']->value==1){?>selected="selected"<?php }?>>付款时间</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchTimeType']->value==2){?>selected="selected"<?php }?>>扫描时间</option>
                    <option value="3" <?php if ($_smarty_tpl->tpl_vars['searchTimeType']->value==3){?>selected="selected"<?php }?>>同步时间</option>
                </select>
            </td>
            <td>
                <!--input name="OrderTime1" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime1']->value;?>
" onclick="WdatePicker()"/>-<input name="OrderTime2" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime12']->value;?>
" onclick="WdatePicker()"/-->
                <input name="OrderTime1" id="OrderTime1" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime1']->value;?>
" />-<input name="OrderTime2" id="OrderTime2" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime2']->value;?>
" />-<input id="SYNC" type="button" value="同步计数" class="order-search"/>
            </td>
        </tr>
    </table>

    <div style="padding-left:17px;">
        <input type="submit" value="搜索" class="order-search" />
        <a href="#" id="AdvancedSearch1" onclick="AdvancedSearch()" class="unfold">高级搜索</a>
        <span style="color: green;" id="countForSYNC"></span>
    </div>
    </form>
</div>
<div class="servar order-servar">
    <table>
    <tr>
    <td width="10%">
    <span>
        <label>
            <input name="allselect" id="allselect" type="checkbox" orderids="" value="" onclick="allselect()"/>全选
        </label>
    </span>
    </td>
    <td>	
    <?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='101'){?>
    <a href="#" onclick="combinePackage()">包裹合并</a>
    <?php }?>
    <?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='106'){?>
    <a href="#" onclick="cancelCombine()">取消包裹合并</a>
    <?php }?>
    <?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='103'){?>
    <a href="#" onclick="splitOverWeight()">超重拆分</a><!--  ADD BY zqt 2013.9.17 -->
    <?php }?>
    <?php if ($_GET['ostatus']!='2'){?>
    <a href="#" onclick="combineOrder()">订单合并</a> <!-- 合并订单 ADD BY chenwei 2013.9.11 -->
    <?php }?>
    <!--<a href="#">发站内信</a>-->
    <!--<a href="#">取消交易</a>-->
    <a href="javascript:void(0);" onClick="splitorder()">订单拆分</a>
    <?php if ($_GET['ostatus']=='2'||$_GET['otype']=='601'){?>
        <a href="javascript:void(0);" style="display:none" onClick="applyRefund()">PAYPAL退款</a>
    <?php }?>
        <a href="javascript:void(0);" onClick="applyRefund()">PAYPAL退款</a>
    <?php if ($_smarty_tpl->tpl_vars['ostatus']->value=='770'){?>
        <a href="javascript:void(0);" onClick="abnormalStockSplit()">缺货拆分</a>
    <?php }?>
    <a href="javascript:void(0);" onClick="handRefund()">手工退款</a>
    <a href="javascript:void(0);" onClick="handCaseRefund()">申请CASE单据</a>
    <?php if ($_GET['ostatus']=='200'&&$_GET['otype']=='201'){?>
        <a href="javascript:void(0);" onClick="superOrder();">确认超大</a>
    <?php }?>
    <?php if ($_GET['ostatus']=='900'){?>
        <a href="javascript:void(0);" onclick="temporarilySend()" >暂时不寄</a><!-- 暂不寄操作 ADD BY chenwei 2013.9.12 -->
    <?php }?>
    <a href="javascript:void(0);" onClick="unLockOrder();">解锁订单</a><!-- 解锁订单 ADD BY zyp 2013.9.14 -->
    <?php if ($_GET['ostatus']!=2){?>
   	<a href="javascript:void(0);" onclick="batchMove();">批量修改</a>
    <?php }?>
    <!--a href="javascript:void(0);" onclick="onlineeubtracknumber();" >线上EUB申请</a-->
    <!--a href="javascript:void(0);" onclick="thelineeubtracknumber();" >线下EUB申请</a-->
    
    <?php if ($_GET['ostatus']=='911'&&$_GET['otype']=='115'){?>
        <a href="#" onclick="reCulShippingWay()">海外仓运费计算</a>   <!-- 海外仓批量处理运输方式 <海外仓订单待处理> -->
    <?php }?>
    
    <?php if ($_GET['ostatus']=='911'&&$_GET['otype']=='916'){?>
        <a href="#" onclick="owPrintLabel()">海外仓标签打印</a>   <!-- 海外仓订单打印 <海外仓订单待打> -->
    <?php }?>
    
	<?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='700'){?>
	<a href="#" onclick="taoBaoRemoveOrder()">标记刷单</a><!-- 淘宝标记订单为刷单 ADD BY chenwei 2013.9.17 -->
	<?php }?>
    
        <!--a href="javascript:void(0);" onClick="partPackage();">申请配货</a-->
    
	
	<!--a href="javascript:void(0)" onclick="print_order(1)">申请打印</a-->
	
	
	<!--a href="javascript:void(0)" onclick="print_order(0)">申请打印</a-->
	
	<a href="javascript:void(0);" onClick="old_shenqingdayin();">申请打印</a>
	<?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='731'){?>
    <a href="#" onclick="export_ups_us_xml()">UPS美国专线订单导出</a>
    <?php }?>
	<select id="exportstoxls" name="exportstoxls" onchange="exportstoxls()">
		<option value="0" style="width:157px;">将订单导出到</option>
		<option value="1">常规列表导出xls</option>
		<option value="2">EUB导入格式导出</option>
		<option value="3">EUB导入料号导出</option>
	</select>
   <!-- <a href="#" >手动申请发货(需求不明)</a> -->
    </td>
    </tr>
    </table>
</div>
<div class="bottomvar">
	<span id="showSelectNum"></span>
    <?php if ($_smarty_tpl->tpl_vars['status']->value==''){?>
    <div class="pagination">
        <?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

    </div>
    <?php }?>
</div>
<div class="main order-main" align="center">
    <?php  $_smarty_tpl->tpl_vars['orderlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderlist']->_loop = false;
 $_smarty_tpl->tpl_vars['omOrderId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['omOrderList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderlist']->key => $_smarty_tpl->tpl_vars['orderlist']->value){
$_smarty_tpl->tpl_vars['orderlist']->_loop = true;
 $_smarty_tpl->tpl_vars['omOrderId']->value = $_smarty_tpl->tpl_vars['orderlist']->key;
?>
    <?php $_smarty_tpl->tpl_vars['orderData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['order'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderExtenData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['extens'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderUserInfoData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['userinfo'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderNote'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['note'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderTracknumber'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderTracknumber'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderAudit'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderAudit'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderWarehouse'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['warehouse'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['combinePackage'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['combinePackage'], null, 0);?>
    <input type="hidden" id="orderStatus_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" name="orderStatus_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['orderData']->value['orderStatus'];?>
" />
    <input type="hidden" id="orderType_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" name="orderType_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['orderData']->value['orderType'];?>
" />
    <table cellspacing="0" width="100%">
        <tr class="title">
            <td valign="middle" style="border-right:1px #999 solid;padding:0;" width="11%">
                <input class="checkclass" name="ckb" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" onclick="displayselect(this.value,0);" />
                <input type="hidden" id="invoice_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" name="invoice_<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['AbOrderShow']->value[$_smarty_tpl->tpl_vars['orderData']->value['id']];?>
" />
                <?php echo $_smarty_tpl->getSubTemplate ("editField.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </td>
            <td colspan="3" align="left">
                <span style="width:20%">账号：<?php echo get_accountnamebyid($_smarty_tpl->tpl_vars['orderData']->value['accountId']);?>
</span>
                <span style="width:20%">平台：<?php echo get_platnamebyid($_smarty_tpl->tpl_vars['orderData']->value['platformId']);?>
</span>
                <span style="width:20%">下单时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['ordersTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <span style="width:20%">重量：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['calcWeight'];?>
 KG</span> <br />
                <span style="width:20%">系统编号：<?php echo $_smarty_tpl->tpl_vars['omOrderId']->value;?>
</span>
                <span style="width:20%;">买家ID：<input value="<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['platformUsername'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <span style="width:20%">付款时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['paymentTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <span style="width:20%">包材：<?php echo get_maternamebyid($_smarty_tpl->tpl_vars['orderData']->value['pmId']);?>
</span>
                <br />
                <span style="width:20%">RecordNo.：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['recordNumber'];?>
</span>

                <span style="width:20%;">买家姓名：<input value="<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['username'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <span style="width:20%">扫描时间：<?php if ($_smarty_tpl->tpl_vars['orderWarehouse']->value['weighTime']){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderWarehouse']->value['weighTime'],"%Y-%m-%d %H:%I:%S");?>
<?php }else{ ?>--<?php }?></span>
                <span style="width:20%">交易ID：<?php if ($_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalPaymentId']){?><?php echo $_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalPaymentId'];?>
<?php }else{ ?>--<?php }?></span>
                <br />
                <span style="width:20%">发往国家：<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['countryName'];?>
</span>
                <span style="width:20%;">买家邮箱：<input value="<?php if ($_smarty_tpl->tpl_vars['orderUserInfoData']->value['email']!=''){?><?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['email'];?>
<?php }else{ ?>--<?php }?>" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <span style="width:20%">运费：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['calcShipping'];?>
</span>
                <span style="width:20%;">卖家邮箱：<input value="<?php if ($_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalEmailAddress']!=''){?><?php echo $_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalEmailAddress'];?>
<?php }else{ ?>--<?php }?>" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <br />
                <span style="width:20%">运输：<?php if ($_smarty_tpl->tpl_vars['orderData']->value['transportId']){?><?php echo get_carriernamebyid($_smarty_tpl->tpl_vars['orderData']->value['transportId']);?>
<?php }else{ ?>--<?php }?></span>
                <span style="width:20%">跟踪号：
                <?php if ($_smarty_tpl->tpl_vars['orderTracknumber']->value){?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['transportId']==6){?>
                    	<?php echo $_smarty_tpl->tpl_vars['orderTracknumber']->value[0]['tracknumber'];?>

                        <!--<input value="<?php echo $_smarty_tpl->tpl_vars['orderTracknumber']->value[0]['tracknumber'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/>-->
                    <?php }else{ ?>
                        <!--<select id="orderTracknumber">-->
                        <?php  $_smarty_tpl->tpl_vars['orderTrack'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderTrack']->_loop = false;
 $_smarty_tpl->tpl_vars['orderTrackId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderTracknumber']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderTrack']->key => $_smarty_tpl->tpl_vars['orderTrack']->value){
$_smarty_tpl->tpl_vars['orderTrack']->_loop = true;
 $_smarty_tpl->tpl_vars['orderTrackId']->value = $_smarty_tpl->tpl_vars['orderTrack']->key;
?>
                        <?php echo $_smarty_tpl->tpl_vars['orderTrack']->value['tracknumber'];?>
 |　
                        <!--<option value=""><?php echo $_smarty_tpl->tpl_vars['orderTrack']->value['tracknumber'];?>
</option>-->
                        <?php } ?>
                        <!--</select>-->
                    <?php }?>
                <?php }else{ ?>--<?php }?>
                </span>
                <span style="width:20%">金额：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['actualTotal'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['currency'];?>
</span>
                <?php if ($_smarty_tpl->tpl_vars['orderExtenData']->value['feedback']!=''){?>
                <span style="width:55%;color:red;"><span style="color:#000;margin-right:0px;">留言：</span><?php echo $_smarty_tpl->tpl_vars['orderExtenData']->value['feedback'];?>

                </span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> 
                <div style="padding:10px;">
                    <table width="100%">
                        <tr class="title">
                            <td>图片</td>
                            <!--<td>RecordNo.</td>-->
                            <td>料号</td>
                            <td>数量</td>
                            <td>售价</td>
                            <td>sfee</td>
                            <td>实际库存</td>
                            <td>待发货</td>
                            <td>被拦截</td>
                            <td>待审核</td>
                            <td>已预定</td>
                            <td>每天均量</td>
                            <td>预警</td>
                            <td>采购</td>
                            <td>成本(RMB)</td>
                            <td>链接</td>
                            <td>审核</td>
                            <td>配货</td>
                        </tr>
                        <?php  $_smarty_tpl->tpl_vars['orderDetailData'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderDetailData']->_loop = false;
 $_smarty_tpl->tpl_vars['omOrderdetailId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderlist']->value['detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['orderDetailData']->key => $_smarty_tpl->tpl_vars['orderDetailData']->value){
$_smarty_tpl->tpl_vars['orderDetailData']->_loop = true;
 $_smarty_tpl->tpl_vars['omOrderdetailId']->value = $_smarty_tpl->tpl_vars['orderDetailData']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['index']++;
?>
                        	<?php $_smarty_tpl->tpl_vars['orderDetail'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderDetailData']->value['base'], null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['orderDetailExten'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderDetailData']->value['extens'], null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['skuinfos'] = new Smarty_variable(get_orderskulist($_smarty_tpl->tpl_vars['orderDetail']->value['sku']), null, 0);?>
        				<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['index']!=0){?>
                        <tr>
                            <td colspan="17">
                                <div style=" border-bottom:#999999 dashed 1px; margin-left:5px; margin-right:5px;"><?php echo $_smarty_tpl->tpl_vars['skuinfos']->value['isCombine'];?>
</div>
                            </td>
                        </tr>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['skuinfos']->value['isCombine']>'1'){?>
                        <?php $_smarty_tpl->tpl_vars['combinesku'] = new Smarty_variable($_smarty_tpl->tpl_vars['skuinfos']->value['combinesku'], null, 0);?>
                        <tr>
                            <td class="unpicurl">
                                <a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['combinesku']->value['skupic'];?>
" data-sku="imgb-<?php echo $_smarty_tpl->tpl_vars['combinesku']->value['skupic'];?>
" class="fancybox" style="margin-left:0;">
                                	<img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['combinesku']->value['skupic'];?>
" width="60" height="60" data-sku="<?php echo $_smarty_tpl->tpl_vars['combinesku']->value['skupic'];?>
" data-spu="<?php echo $_smarty_tpl->tpl_vars['combinesku']->value['spu'];?>
">
                                </a>
                                <br />
                                <span><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['recordNumber'];?>
</span>
                            </td>
                            <td><?php echo $_smarty_tpl->tpl_vars['combinesku']->value['sku'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['amount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['itemPrice'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['shippingFee'];?>
</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="unpicurl" width="10%">
                                <a href="<?php echo get_itemurl($_smarty_tpl->tpl_vars['orderDetailExten']->value['itemId']);?>
" target="_blank" title="<?php echo $_smarty_tpl->tpl_vars['orderDetailExten']->value['itemId'];?>
"><?php echo $_smarty_tpl->tpl_vars['orderDetailExten']->value['itemTitle'];?>
</a>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <!--<font color="green" >是</font>-->
                                <font color="red" >否</font>
                            </td>
                        </tr>
                        <?php  $_smarty_tpl->tpl_vars['realsku'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['realsku']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['skuinfos']->value['realsku']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['realsku']->key => $_smarty_tpl->tpl_vars['realsku']->value){
$_smarty_tpl->tpl_vars['realsku']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['skudaily'] = new Smarty_variable(get_skudailystatus($_smarty_tpl->tpl_vars['realsku']->value['sku']), null, 0);?>
                        <tr>
                            <td class="unpicurl">组合料号</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['sku'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['amount']*$_smarty_tpl->tpl_vars['orderDetail']->value['amount'];?>
</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['enableCount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['waitingSendCount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['interceptSendCount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['waitingAuditCount'];?>
</td>
                            <td><?php echo get_reservecount($_smarty_tpl->tpl_vars['realsku']->value['sku']);?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['averageDailyCount'];?>
</td>
                            <td><?php if ($_smarty_tpl->tpl_vars['skudaily']->value['is_warning']==='1'){?><font color="red">是</font><?php }?><?php if ($_smarty_tpl->tpl_vars['skudaily']->value['is_warning']==='0'){?><font color="green">否</font><?php }?></td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['purchaseName'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['goodsCost'];?>
</td>
                            <td class="unpicurl" width="10%">&nbsp;</td>
                            <td>
                            <?php  $_smarty_tpl->tpl_vars['orderAuditVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderAuditVal']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderAudit']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderAuditVal']->key => $_smarty_tpl->tpl_vars['orderAuditVal']->value){
$_smarty_tpl->tpl_vars['orderAuditVal']->_loop = true;
?>
                            <?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['sku']==$_smarty_tpl->tpl_vars['ssku']->value){?>
                            <span><?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='1'){?><font color="green" >通过</font><?php }elseif($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='2'){?><font color="red" >拦截</font><?php }else{ ?><font color="orange" >状态有误</font><?php }?></span>
                            <?php }?>
                            <?php } ?>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php } ?>
                        <?php }else{ ?>
                        <?php  $_smarty_tpl->tpl_vars['realsku'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['realsku']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['skuinfos']->value['realsku']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['realsku']->key => $_smarty_tpl->tpl_vars['realsku']->value){
$_smarty_tpl->tpl_vars['realsku']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['skudaily'] = new Smarty_variable(get_skudailystatus($_smarty_tpl->tpl_vars['realsku']->value['sku']), null, 0);?>
                        <tr>
                            <td class="unpicurl">
                                <a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['realsku']->value['skupic'];?>
" data-sku="imgb-<?php echo $_smarty_tpl->tpl_vars['realsku']->value['skupic'];?>
" class="fancybox" style="margin-left:0;">
                                	<img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['realsku']->value['skupic'];?>
" width="60" height="60" data-sku="<?php echo $_smarty_tpl->tpl_vars['realsku']->value['skupic'];?>
" data-spu="<?php echo $_smarty_tpl->tpl_vars['realsku']->value['spu'];?>
">
                                </a>
                                <br />
                                <span><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['recordNumber'];?>
</span>
                            </td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['sku'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['amount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['itemPrice'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['orderDetail']->value['shippingFee'];?>
</td>
                            <td>skuinfo.enableCount}</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['waitingSendCount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['interceptSendCount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['waitingAuditCount'];?>
</td>
                            <td><?php echo get_reservecount($_smarty_tpl->tpl_vars['realsku']->value['sku']);?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['skudaily']->value['averageDailyCount'];?>
</td>
                            <td><?php if ($_smarty_tpl->tpl_vars['skudaily']->value['is_warning']==='1'){?><font color="red">是</font><?php }else{ ?><font color="green">否</font><?php }?></td>
                            <td><?php echo get_usernamebyid($_smarty_tpl->tpl_vars['realsku']->value['purchaseId']);?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['realsku']->value['goodsCost'];?>
</td>
                            <td class="unpicurl" width="10%">
                                <a href="<?php echo get_itemurl($_smarty_tpl->tpl_vars['orderDetailExten']->value['itemId']);?>
" target="_blank" title="<?php echo $_smarty_tpl->tpl_vars['orderDetailExten']->value['itemId'];?>
"><?php echo $_smarty_tpl->tpl_vars['orderDetailExten']->value['itemTitle'];?>
</a>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <!--<font color="green" >是</font>-->
                                <font color="red" >否</font>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php }?>
                    	<?php }
if (!$_smarty_tpl->tpl_vars['orderDetailData']->_loop) {
?>
                        <tr>
                        	<td colspan="17" ><span>订单明细为空，请核实订单的信息！</span></td>
                        </tr>
                    	<?php } ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr class="title">
            <td align="center">
                <span style="width:auto;">
                <?php  $_smarty_tpl->tpl_vars['vk'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vk']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['statusMenu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['vk']->key => $_smarty_tpl->tpl_vars['vk']->value){
$_smarty_tpl->tpl_vars['vk']->_loop = true;
?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['orderType']==''){?>
                        <?php if ($_smarty_tpl->tpl_vars['vk']->value['statusCode']==$_smarty_tpl->tpl_vars['orderData']->value['orderStatus']){?><?php echo $_smarty_tpl->tpl_vars['vk']->value['statusName'];?>
<?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['orderType']!=''){?>
                        <?php if ($_smarty_tpl->tpl_vars['vk']->value['statusCode']==$_smarty_tpl->tpl_vars['orderData']->value['orderType']){?><?php echo $_smarty_tpl->tpl_vars['vk']->value['statusName'];?>
<?php }?>
                    <?php }?>
                <?php } ?>
                </span>
            </td>
            <td colspan="2">
                <span style="width:38%">同步时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['orderAddTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <span style="width:20%">
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['combinePackage']==1&&$_smarty_tpl->tpl_vars['combinePackage']->value['son']!=''){?>该订单为#[<?php echo $_smarty_tpl->tpl_vars['combinePackage']->value['son'];?>
]#合并包裹发货<?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['combinePackage']==2&&$_smarty_tpl->tpl_vars['combinePackage']->value['main']!=''){?>该订单被<?php echo $_smarty_tpl->tpl_vars['combinePackage']->value['main'];?>
合并<?php }?>
                </span>
                <span>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isSplit']==1){?>被拆分的订单<?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isSplit']==2){?>拆分产生的订单<?php }?>
                </span>
                <span>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isCopy']==1){?>被复制订单<?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isCopy']==2){?><?php if ($_smarty_tpl->tpl_vars['orderData']->value['isBuji']==2){?>补寄订单<?php }else{ ?>复制订单<?php }?><?php }?>
                </span>
                <span><?php if ($_smarty_tpl->tpl_vars['orderData']->value['combineOrder']==2){?>合并订单<?php }?></span>
                <?php if ($_smarty_tpl->tpl_vars['orderNote']->value){?>
                <span>
                备注：
                <?php  $_smarty_tpl->tpl_vars['ve'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ve']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderNote']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ve']->key => $_smarty_tpl->tpl_vars['ve']->value){
$_smarty_tpl->tpl_vars['ve']->_loop = true;
?>
                    <?php if ($_smarty_tpl->tpl_vars['ve']->value['content']!=''){?><?php echo $_smarty_tpl->tpl_vars['ve']->value['content'];?>
&nbsp;<?php }?>
                <?php } ?>
                </span>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isLock']==1){?>
                	<span> 锁定人：<?php echo get_usernamebyid($_smarty_tpl->tpl_vars['orderData']->value['lockUser']);?>
 &nbsp;&nbsp; 锁定时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['lockTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <?php }?>
            </td>
        </tr>
    </table>
    <?php }
if (!$_smarty_tpl->tpl_vars['orderlist']->_loop) {
?>
    <span>还没有相应的订单信息哦！</span>
    <?php } ?>
</div>
<div class="bottomvar">
	<span id="showSelectNum2"></span>
    <?php if ($_smarty_tpl->tpl_vars['status']->value==''){?>
    <div class="pagination">
        <?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

    </div>
    <?php }?>
</div>
<?php echo $_smarty_tpl->getSubTemplate ('orderBatchMove.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('cancelCombine.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>