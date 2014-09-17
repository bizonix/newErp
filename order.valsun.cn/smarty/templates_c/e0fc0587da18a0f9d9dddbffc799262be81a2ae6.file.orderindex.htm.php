<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 21:36:21
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\orderindex.htm" */ ?>
<?php /*%%SmartyHeaderCode:22926531197067ff051-54150652%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e0fc0587da18a0f9d9dddbffc799262be81a2ae6' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\orderindex.htm',
      1 => 1393813909,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22926531197067ff051-54150652',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5311970714c639_26047770',
  'variables' => 
  array (
    'platform' => 0,
    'va' => 0,
    'searchPlatformId' => 0,
    'accountList' => 0,
    'searchAccountId' => 0,
    'ostatus' => 0,
    'otype' => 0,
    'searchIsNote' => 0,
    'searchReviews' => 0,
    'searchIsBuji' => 0,
    'searchIsTracknumber' => 0,
    'searchTransportationType' => 0,
    'transportation' => 0,
    'vf' => 0,
    'searchTransportation' => 0,
    'searchIsLock' => 0,
    'ostatusList' => 0,
    'otypeList' => 0,
    'searchKeywords' => 0,
    'searchKeywordsType' => 0,
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
    'account' => 0,
    'omOrderId' => 0,
    'orderUserInfoData' => 0,
    'pm' => 0,
    'orderExtenData' => 0,
    'transportationList' => 0,
    'orderTracknumber' => 0,
    'orderTrack' => 0,
    'orderDetail' => 0,
    'orderDetailData' => 0,
    'explodesku' => 0,
    'skuinfo' => 0,
    'skusellinfo' => 0,
    'orderDetailExtenData' => 0,
    'ebaylistingurl' => 0,
    'virtualSku' => 0,
    'orderAudit' => 0,
    'orderAuditVal' => 0,
    'AbOrderListArr' => 0,
    'ssku' => 0,
    'sskuinfo' => 0,
    'sskusellinfo' => 0,
    'statusMenu' => 0,
    'vk' => 0,
    'combinePackage' => 0,
    'orderNote' => 0,
    've' => 0,
    'lockUser' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5311970714c639_26047770')) {function content_5311970714c639_26047770($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'E:\\erpNew\\order.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link type="text/css" rel="stylesheet" href="css/orderindex.css">
<script language="javascript" src="js/orderindex.js"></script>
<script language="javascript" src="js/orderRefund.js"></script>
<script src="./js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript" src="./js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" type="text/css" href="./js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />

<div class="fourvar order-fourvar">
    <form action="index.php?mod=orderindex&act=getOrderList&search=1" method="get" id="getOrderList">
    <table>
        <tr>
            <td style="padding-left:17px;">
                平台：
            </td>
            <td>
                <select name="platformId" id="platformId" style="width:157px" onchange="changePlatform()">
                <option value="">全部</option>
                <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['platform']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['searchPlatformId']->value==$_smarty_tpl->tpl_vars['va']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['platform'];?>
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
                <?php if ($_smarty_tpl->tpl_vars['searchPlatformId']->value){?>
                <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accountList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['searchAccountId']->value==$_smarty_tpl->tpl_vars['va']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['account'];?>
</option>
                <?php } ?>
                <?php }?>
                </select>
                </span>
            </td>
            <td style="padding-left:15px;">
                留言：
            </td>
            <input type="hidden" name="mod" value="orderindex" />
            <input type="hidden" name="act" value="getOrderList" />
            <input type="hidden" name="search" value="1" />
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
            <!--<td style="padding-left:17px;">
                跟踪号：
            </td>
            <td>
                <select name="isTracknumber" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchIsTracknumber']->value==1){?>selected="selected"<?php }?>>有</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchIsTracknumber']->value==2){?>selected="selected"<?php }?>>无</option>
                </select>
            </td>-->
        </tr>
        <tr>
            <td style="padding-left:17px;">
                运输类型：
            </td>
            <td>
                <select name="transportationType" id="transportationType" onchange="changeTransportation()" style="width:157px">
                    <option value="">全部</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchTransportationType']->value==1){?>selected="selected"<?php }?>>快递</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchTransportationType']->value==2){?>selected="selected"<?php }?>>非快递</option>
                </select>
            </td>
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
<?php $_tmp1=ob_get_clean();?><?php if ($_smarty_tpl->tpl_vars['searchTransportation']->value==$_tmp1){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['vf']->value['carrierNameCn'];?>
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
                    <option value="">--ALL--</option>
                    <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ostatusList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['va']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['va']->value['statusCode']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['va']->value['statusName'];?>
</option>
                    <?php } ?>
                </select>
            </td>
            <td style="padding-left:17px;">
                类别：
            </td>
            <td>
                <select name="otype" id="otype" style="width:157px">
                    <option value="">--ALL--</option>
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
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="padding-left:17px;">
                关键字:
            </td>
            <td style="padding-left:19px;">
                <input name="Keywords" value="<?php echo $_smarty_tpl->tpl_vars['searchKeywords']->value;?>
" />
            </td>
            <td style="padding-left:15px;">
                <select name="KeywordsType">
                    <option value="0" style="width:157px;">请选择关键字类型</option>
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==1){?>selected="selected"<?php }?>>买家ID</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==2){?>selected="selected"<?php }?>>买家邮箱</option>
                    <option value="3" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==3){?>selected="selected"<?php }?>>RecordNo.</option>
                    <option value="4" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==4){?>selected="selected"<?php }?>>paypal交易ID</option>
                    <option value="5" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==5){?>selected="selected"<?php }?>>跟踪号</option>
                    <option value="6" <?php if ($_smarty_tpl->tpl_vars['searchKeywordsType']->value==6){?>selected="selected"<?php }?>>系统编号</option>
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
                <select name="searchTimeType">
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['searchTimeType']->value==1){?>selected="selected"<?php }?>>付款时间</option>
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['searchTimeType']->value==2){?>selected="selected"<?php }?>>扫描时间</option>
                </select>
            </td>
            <td>
                <!--input name="OrderTime1" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime1']->value;?>
" onclick="WdatePicker()"/>-<input name="OrderTime2" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime12']->value;?>
" onclick="WdatePicker()"/-->
                <input name="OrderTime1" id="OrderTime1" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime1']->value;?>
" />-<input name="OrderTime2" id="OrderTime2" type="text" value="<?php echo $_smarty_tpl->tpl_vars['searchOrderTime2']->value;?>
" />
            </td>
        </tr>
    </table>

    <div style="padding-left:17px;">
        <input type="submit" value="搜索" class="order-search" />
        <a href="#" id="AdvancedSearch1" onclick="AdvancedSearch()" class="unfold">高级搜索</a>
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
    
        <a href="javascript:void(0);" onClick="applyRefund()">PAYPAL退款</a>
    
    <?php if ($_smarty_tpl->tpl_vars['ostatus']->value=='770'){?>
        <a href="javascript:void(0);" onClick="abnormalStockSplit()">缺货拆分</a>
    <?php }?>
    <a href="javascript:void(0);" onClick="handRefund()">手工退款</a>
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
    <a href="javascript:void(0);" onclick="onlineeubtracknumber();" >线上EUB申请</a>
    <a href="javascript:void(0);" onclick="thelineeubtracknumber();" >线下EUB申请</a>
	<?php if ($_GET['ostatus']=='100'&&$_GET['otype']=='700'){?>
	<a href="#" onclick="taoBaoRemoveOrder()">标记刷单</a><!-- 淘宝标记订单为刷单 ADD BY chenwei 2013.9.17 -->
	<?php }?>
    <?php if ($_GET['ostatus']==200&&$_GET['otype']==203){?>
        <a href="javascript:void(0);" onClick="partPackage();">申请配货</a>
    <?php }?>
	<?php if ($_GET['ostatus']==100||($_GET['ostatus']==200&&$_GET['otype']==204)||$_GET['otype']==552||$_GET['otype']==802){?>
	<a href="javascript:void(0)" onclick="print_order(1)">申请打印</a>
	<?php }?>
	<?php if ($_GET['ostatus']==200&&$_GET['otype']==203){?>
	<a href="javascript:void(0)" onclick="print_order(0)">申请打印</a>
	<?php }?>
	
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
    <?php $_smarty_tpl->tpl_vars['orderData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderData'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderExtenData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderExtenData'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderUserInfoData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderUserInfoData'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderNote'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderNote'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderTracknumber'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderTracknumber'], null, 0);?>
    <?php $_smarty_tpl->tpl_vars['orderAudit'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderlist']->value['orderAudit'], null, 0);?>
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
                <span style="width:20%">账号：<?php echo $_smarty_tpl->tpl_vars['account']->value[$_smarty_tpl->tpl_vars['orderData']->value['accountId']];?>

                </span>
                <span style="width:20%">平台：
                <?php  $_smarty_tpl->tpl_vars['va'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['va']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['platform']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['va']->key => $_smarty_tpl->tpl_vars['va']->value){
$_smarty_tpl->tpl_vars['va']->_loop = true;
?>
                <?php if ($_smarty_tpl->tpl_vars['va']->value['id']==$_smarty_tpl->tpl_vars['orderData']->value['platformId']){?><?php echo $_smarty_tpl->tpl_vars['va']->value['platform'];?>
<?php }?>
                <?php } ?>
                </span>
                <span style="width:20%">下单时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['ordersTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <span style="width:20%">重量：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['calcWeight'];?>
 KG</span>
                <br />
                <span style="width:20%">系统编号：<?php echo $_smarty_tpl->tpl_vars['omOrderId']->value;?>
</span>
                <span style="width:20%;">买家ID：<input value="<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['platformUsername'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <span style="width:20%">付款时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['paymentTime'],"%Y-%m-%d %H:%I:%S");?>
</span>
                <span style="width:20%">包材：<?php if ($_smarty_tpl->tpl_vars['pm']->value[$_smarty_tpl->tpl_vars['orderData']->value['pmId']]){?><?php echo $_smarty_tpl->tpl_vars['pm']->value[$_smarty_tpl->tpl_vars['orderData']->value['pmId']];?>
<?php }else{ ?>--<?php }?></span>
                <br />
                <span style="width:20%">RecordNo.：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['recordNumber'];?>
</span>

                <span style="width:20%;">买家姓名：<input value="<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['username'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span>
                <span style="width:20%">扫描时间：--</span>
                <span style="width:20%">交易ID：<?php if ($_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalPaymentId']){?><?php echo $_smarty_tpl->tpl_vars['orderExtenData']->value['PayPalPaymentId'];?>
<?php }else{ ?>--<?php }?></span>
                <br />
                <span style="width:20%">发往国家：<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['countryName'];?>
</span>
                <span style="width:20%;">买家邮箱：<input value="<?php if ($_smarty_tpl->tpl_vars['orderUserInfoData']->value['email']!=''){?><?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['email'];?>
<?php }?>" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/></span> 
                <span style="width:20%">运费：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['calcShipping'];?>
</span>
                <br />
                <span style="width:20%">运输：<?php if ($_smarty_tpl->tpl_vars['transportationList']->value[$_smarty_tpl->tpl_vars['orderData']->value['transportId']]){?><?php echo $_smarty_tpl->tpl_vars['transportationList']->value[$_smarty_tpl->tpl_vars['orderData']->value['transportId']];?>
<?php }else{ ?>--<?php }?></span>
                <span style="width:20%">跟踪号：
                <?php if ($_smarty_tpl->tpl_vars['orderTracknumber']->value){?>
                    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['transportId']==6){?>
                        <input value="<?php echo $_smarty_tpl->tpl_vars['orderTracknumber']->value[0]['tracknumber'];?>
" style="width:168px;border:none; background-color: #f2f2f2; outline:none;" readonly/>
                    <?php }else{ ?>
                        <select id="orderTracknumber">
                        <?php  $_smarty_tpl->tpl_vars['orderTrack'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderTrack']->_loop = false;
 $_smarty_tpl->tpl_vars['orderTrackId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderTracknumber']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderTrack']->key => $_smarty_tpl->tpl_vars['orderTrack']->value){
$_smarty_tpl->tpl_vars['orderTrack']->_loop = true;
 $_smarty_tpl->tpl_vars['orderTrackId']->value = $_smarty_tpl->tpl_vars['orderTrack']->key;
?>
                        <option value=""><?php echo $_smarty_tpl->tpl_vars['orderTrack']->value['tracknumber'];?>
</option>
                        <?php } ?>
                        </select>
                    <?php }?>
                <?php }else{ ?>--<?php }?>
                </span>
                <span style="width:20%">金额：<?php echo $_smarty_tpl->tpl_vars['orderData']->value['actualTotal'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['orderUserInfoData']->value['currency'];?>
</span>
                <?php if ($_smarty_tpl->tpl_vars['orderExtenData']->value['feedback']!=''){?>
                <span style="width:100%">留言：
                <input value="<?php echo $_smarty_tpl->tpl_vars['orderExtenData']->value['feedback'];?>
" style="color:#009d9b;width:1000px;border:none; background-color: #f2f2f2; outline:none;" readonly/>
                </span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> 
                <div style="padding:10px;">
                    <table width="100%">
                        <tr class="title">
                            <td>
                                图片
                            </td>
                            <!--<td>
                                RecordNo.
                            </td>-->
                            <td>
                                料号
                            </td>
                            <td>
                                数量
                            </td>
                            <td>
                                售价
                            </td>
                            <td>
                                sfee
                            </td>
                            <td>
                                实际库存
                            </td>
                            <td>
                                待发货
                            </td>
                            <td>
                                被拦截
                            </td>
                            <td>
                                待审核
                            </td>
                            <td>
                                已预定
                            </td>
                            <td>
                                每天均量
                            </td>
                            <td>
                            	预警
                            </td>
                            <td>
                                采购
                            </td>
                            <td>
                                成本(RMB)
                            </td>
                            <td>
                                链接
                            </td>
                            <td>
                            	审核
                            </td>
                            <td>
                            	配货
                            </td>
                        </tr>
                        <?php  $_smarty_tpl->tpl_vars['orderDetail'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderDetail']->_loop = false;
 $_smarty_tpl->tpl_vars['omOrderdetailId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderlist']->value['orderDetail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['orderDetail']->key => $_smarty_tpl->tpl_vars['orderDetail']->value){
$_smarty_tpl->tpl_vars['orderDetail']->_loop = true;
 $_smarty_tpl->tpl_vars['omOrderdetailId']->value = $_smarty_tpl->tpl_vars['orderDetail']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['index']++;
?>
                        <?php $_smarty_tpl->tpl_vars['orderDetailData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderDetail']->value['orderDetailData'], null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['orderDetailExtenData'] = new Smarty_variable($_smarty_tpl->tpl_vars['orderDetail']->value['orderDetailExtenData'], null, 0);?>
                        <?php if ($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']){?>
                            <?php $_smarty_tpl->tpl_vars['skuinfo'] = new Smarty_variable(GoodsModel::getSkuinfo($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']), null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['skusellinfo'] = new Smarty_variable(PurchaseAPIModel::getSkuDailyStatus($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']), null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['virtualSku'] = new Smarty_variable(GoodsModel::getCompleteSkuinfo($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']), null, 0);?>
                        <?php }?>
        				<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['index']!=0){?>
                        <tr>
                            <td colspan="17">
                                <div style=" border-bottom:#999999 dashed 1px; margin-left:5px; margin-right:5px;"></div>
                            </td>
                        </tr>
                        <?php }?>
                        <tr>
                            <td class="unpicurl">
                                <?php $_smarty_tpl->tpl_vars['explodesku'] = new Smarty_variable(func_explode($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']), null, 0);?>
                                <a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" data-sku="imgb-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" class="fancybox" style="margin-left:0;">
                                <img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" width="60" height="60" data-sku="<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" data-spu="<?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['spu'];?>
">
                                </a>
                                <br />
                                <span><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['recordNumber'];?>
</span>
                            </td>
                            <td>
                                <?php if ($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']){?><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['sku'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['orderDetailData']->value['amount']){?><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['amount'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['orderDetailData']->value['itemPrice']){?><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['itemPrice'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['orderDetailData']->value['shippingFee']){?><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['shippingFee'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skuinfo']->value['enableCount']){?><?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['enableCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['waitingSendCount']){?><?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['waitingSendCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['interceptSendCount']){?><?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['interceptSendCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['waitingAuditCount']){?><?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['waitingAuditCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['enableCount']){?><?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['enableCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['averageDailyCount']){?><?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['averageDailyCount'];?>
<?php }else{ ?>0<?php }?>
                            </td>
                            <td>
                            	<?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['is_warning']==='1'){?><font color="red">是</font><?php }?><?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['is_warning']==='0'){?><font color="green">否</font><?php }?>
                            </td>
                            <td>
                                <?php if ($_smarty_tpl->tpl_vars['skuinfo']->value['purchaseName']){?><?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['purchaseName'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                                <?php if ($_smarty_tpl->tpl_vars['skuinfo']->value['goodsCost']){?><?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['goodsCost'];?>
<?php }else{ ?>--<?php }?>
                            </td>
                            <td class="unpicurl" width="10%">
                                <?php if ($_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemTitle']){?><a href="<?php if ($_smarty_tpl->tpl_vars['orderData']->value['platformId']==1){?>http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
<?php echo $_smarty_tpl->tpl_vars['ebaylistingurl']->value;?>
<?php }elseif($_smarty_tpl->tpl_vars['orderData']->value['platformId']==2){?>http://www.aliexpress.com/item/New-1mm-Silver-Metallic-Caviar-Beads-Studs-Nail-Art-Glitter-Nail-Decoration-13229/<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
.html<?php }elseif($_smarty_tpl->tpl_vars['orderData']->value['platformId']==11){?>http://www.amazon.com/gp/product/<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
<?php }?>" target="_blank">(<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
)&nbsp;<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemTitle'];?>
</a><?php }else{ ?>--<?php }?>
                            </td>
                            <td>
                            <?php if (!$_smarty_tpl->tpl_vars['virtualSku']->value){?>
                            <?php  $_smarty_tpl->tpl_vars['orderAuditVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderAuditVal']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderAudit']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['orderAuditVal']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['orderAuditVal']->key => $_smarty_tpl->tpl_vars['orderAuditVal']->value){
$_smarty_tpl->tpl_vars['orderAuditVal']->_loop = true;
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration++;
 $_smarty_tpl->tpl_vars['orderAuditVal']->last = $_smarty_tpl->tpl_vars['orderAuditVal']->iteration === $_smarty_tpl->tpl_vars['orderAuditVal']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['auditVal']['last'] = $_smarty_tpl->tpl_vars['orderAuditVal']->last;
?>
                            <?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['sku']==$_smarty_tpl->tpl_vars['orderDetailData']->value['sku']){?>
                            <span><?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='1'){?><font color="green" >通过</font><?php }elseif($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='2'){?><font color="red" >拦截</font><?php }else{ ?><font color="orange" >状态有误</font><?php }?></span>
                            <?php }?>
                            <?php } ?>
                            <?php }?>
                            </td>
                            <td>
                                <?php if ($_smarty_tpl->tpl_vars['AbOrderListArr']->value[$_smarty_tpl->tpl_vars['orderData']->value['id']][$_smarty_tpl->tpl_vars['orderDetailData']->value['sku']]==1){?>
                                <font color="green" >是</font>
                                <?php }else{ ?>
                                <font color="red" >否</font>
                                <?php }?>
                            </td>
                        </tr>
						<?php if ($_smarty_tpl->tpl_vars['virtualSku']->value){?>
                        <?php  $_smarty_tpl->tpl_vars['sskuinfo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sskuinfo']->_loop = false;
 $_smarty_tpl->tpl_vars['ssku'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['virtualSku']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sskuinfo']->key => $_smarty_tpl->tpl_vars['sskuinfo']->value){
$_smarty_tpl->tpl_vars['sskuinfo']->_loop = true;
 $_smarty_tpl->tpl_vars['ssku']->value = $_smarty_tpl->tpl_vars['sskuinfo']->key;
?>
                            <?php $_smarty_tpl->tpl_vars['sskusellinfo'] = new Smarty_variable(PurchaseAPIModel::getSkuDailyStatus($_smarty_tpl->tpl_vars['ssku']->value), null, 0);?>
                            <tr>
                                <td class="unpicurl">
                                	组合料号
                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['ssku']->value;?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['count']*$_smarty_tpl->tpl_vars['orderDetailData']->value['amount'];?>

                                </td>
                                <td>&nbsp;
                                    
                                </td>
                                <td>&nbsp;
                                    
                                </td>
                                <td>
                                	<?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['enableCount'];?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['waitingSendCount'];?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['interceptSendCount'];?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['waitingAuditCount'];?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['enableCount'];?>

                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['averageDailyCount'];?>

                                </td>
                                <td>
                                    <?php if ($_smarty_tpl->tpl_vars['sskusellinfo']->value['is_warning']==='1'){?><font color="red">是</font><?php }?><?php if ($_smarty_tpl->tpl_vars['sskusellinfo']->value['is_warning']==='0'){?><font color="green">否</font><?php }?>
                                </td>
                                <td>
                                    <?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['purchaseName'];?>

                                </td>
                                <td>
                                    <?php if ($_smarty_tpl->tpl_vars['sskuinfo']->value['goodsCost']){?><?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['goodsCost'];?>
<?php }else{ ?>--<?php }?>
                                </td>
                                <td class="unpicurl" width="10%">&nbsp;
                                    
                                </td>
                                <td>
                                <?php  $_smarty_tpl->tpl_vars['orderAuditVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderAuditVal']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderAudit']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['orderAuditVal']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['orderAuditVal']->key => $_smarty_tpl->tpl_vars['orderAuditVal']->value){
$_smarty_tpl->tpl_vars['orderAuditVal']->_loop = true;
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration++;
 $_smarty_tpl->tpl_vars['orderAuditVal']->last = $_smarty_tpl->tpl_vars['orderAuditVal']->iteration === $_smarty_tpl->tpl_vars['orderAuditVal']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['auditVal']['last'] = $_smarty_tpl->tpl_vars['orderAuditVal']->last;
?>
                                <?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['sku']==$_smarty_tpl->tpl_vars['ssku']->value){?>
                                <span><?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='1'){?><font color="green" >通过</font><?php }elseif($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='2'){?><font color="red" >拦截</font><?php }else{ ?><font color="orange" >状态有误</font><?php }?></span>
                                <?php }?>
                                <?php } ?>
                                </td>
                                <td>&nbsp;
                                
                            	</td>
                            </tr>
                        <?php } ?>
                        <?php }?>
        <!--tr align="left">
            <td class="unpicurl" align="center">
                <?php $_smarty_tpl->tpl_vars['explodesku'] = new Smarty_variable(func_explode($_smarty_tpl->tpl_vars['orderDetailData']->value['sku']), null, 0);?>
                <a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" data-sku="imgb-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" class="fancybox" style="margin-left:0;">
					<img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" width="60" height="60" data-sku="<?php echo $_smarty_tpl->tpl_vars['explodesku']->value;?>
" data-spu="<?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['spu'];?>
">
			   	</a>
                <br />
                <span style="width:100%;margin-right:0; color:#009d9b;"><?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['recordNumber'];?>
</span>    
            </td>

            <td style="width:1010px;">
                <span style="width:18%;margin-right:5px;"><?php if ($_smarty_tpl->tpl_vars['virtualSku']->value){?>组合料号：<?php }else{ ?>料号：<?php }?><input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['sku'];?>
" style="color:#06F;cursor:pointer;width:auto;border:none; background-color: #fff; outline:none;" onclick="javascrip:window.open('index.php?act=skuInfo&mod=skuInfo&sku=<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['sku'];?>
')" readonly/></span>
                <?php if (!$_smarty_tpl->tpl_vars['virtualSku']->value){?>
                <span style="width:9%;margin-right:5px;">数量:<input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['amount'];?>
" style="color:#009d9b;width:40px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:11%;margin-right:5px;">售价：<input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['itemPrice'];?>
" style="width:67px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:15%;margin-right:5px;">sfee:<input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['shippingFee'];?>
" style="width:53px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['skuinfo']->value['purchaseName']){?>
                <?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['is_warning']==1){?><font color="red">预警</font><?php }?>
                <span style="width:9%;margin-right:5px;">实际库存:<input value="<?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['enableCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">待发货:<input value="<?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['waitingSendCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">被拦截:<input value="<?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['interceptSendCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">待审核:<input value="<?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['waitingAuditCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">已预订:<input value="<?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['enableCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">每天均量:<input value="<?php echo $_smarty_tpl->tpl_vars['skusellinfo']->value['averageDailyCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:9%;margin-right:5px;">消耗库存天数:<input value="<?php if ($_smarty_tpl->tpl_vars['sskusellinfo']->value['AverageDailyCount']>0){?><?php echo ceil($_smarty_tpl->tpl_vars['orderDetailData']->value['amount']/$_smarty_tpl->tpl_vars['skusellinfo']->value['AverageDailyCount']);?>
<?php }else{ ?>0<?php }?>" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:10%;margin-right:5px;">采购:<input value="<?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['purchaseName'];?>
" style="width:60px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <span style="width:15%;margin-right:0;">成本:<input value="<?php echo $_smarty_tpl->tpl_vars['skuinfo']->value['goodsCost'];?>
 RMB" style="width:80px;border:none; background-color: #fff; outline:none;" readonly/></span>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['virtualSku']->value){?>
                    <?php  $_smarty_tpl->tpl_vars['sskuinfo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sskuinfo']->_loop = false;
 $_smarty_tpl->tpl_vars['ssku'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['virtualSku']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sskuinfo']->key => $_smarty_tpl->tpl_vars['sskuinfo']->value){
$_smarty_tpl->tpl_vars['sskuinfo']->_loop = true;
 $_smarty_tpl->tpl_vars['ssku']->value = $_smarty_tpl->tpl_vars['sskuinfo']->key;
?>
                    	<?php $_smarty_tpl->tpl_vars['sskusellinfo'] = new Smarty_variable(PurchaseAPIModel::getSkuDailyStatus($_smarty_tpl->tpl_vars['ssku']->value), null, 0);?>
                        <br />
                        <span style="width:18%;margin-right:5px;">sku:<input value="<?php echo $_smarty_tpl->tpl_vars['ssku']->value;?>
" style="width:auto;border:none; background-color: #fff;color:#06F;cursor:pointer; outline:none;" onclick="javascrip:window.open('index.php?act=skuInfo&mod=skuInfo&sku=<?php echo $_smarty_tpl->tpl_vars['ssku']->value;?>
')" readonly/></span>
                        <span style="width:9%;margin-right:5px;">数量:<input value="<?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['count']*$_smarty_tpl->tpl_vars['orderDetailData']->value['amount'];?>
" style="width:40px;border:none; color:#009d9b;background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:11%;margin-right:5px;">售价：<input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['itemPrice'];?>
" style="width:67px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:15%;margin-right:5px;">sfee：<input value="<?php echo $_smarty_tpl->tpl_vars['orderDetailData']->value['shippingFee'];?>
" style="width:53px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <?php if ($_smarty_tpl->tpl_vars['skusellinfo']->value['is_warning']==1){?><font color="red">预警</font><?php }?>
                        <span style="width:9%;margin-right:5px;">实际库存:<input value="<?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['enableCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">待发货:<input value="<?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['waitingSendCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">被拦截:<input value="<?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['interceptSendCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">待审核:<input value="<?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['waitingAuditCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">已预订:<input value="<?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['enableCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">每天均量:<input value="<?php echo $_smarty_tpl->tpl_vars['sskusellinfo']->value['averageDailyCount'];?>
" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:9%;margin-right:5px;">消耗库存天数:<input value="<?php if ($_smarty_tpl->tpl_vars['sskusellinfo']->value['AverageDailyCount']>0){?><?php echo ceil($_smarty_tpl->tpl_vars['sskuinfo']->value['count']*$_smarty_tpl->tpl_vars['orderDetailData']->value['amount']/$_smarty_tpl->tpl_vars['sskusellinfo']->value['AverageDailyCount']);?>
<?php }else{ ?>0<?php }?>" style="width:28px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:10%;margin-right:5px;">采购:<input value="<?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['purchaseName'];?>
" style="width:60px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <span style="width:15%;margin-right:0;">成本:<input value="<?php echo $_smarty_tpl->tpl_vars['sskuinfo']->value['goodsCost'];?>
 RMB" style="width:80px;border:none; background-color: #fff; outline:none;" readonly/></span>
                        <?php  $_smarty_tpl->tpl_vars['orderAuditVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderAuditVal']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderAudit']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['orderAuditVal']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['orderAuditVal']->key => $_smarty_tpl->tpl_vars['orderAuditVal']->value){
$_smarty_tpl->tpl_vars['orderAuditVal']->_loop = true;
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration++;
 $_smarty_tpl->tpl_vars['orderAuditVal']->last = $_smarty_tpl->tpl_vars['orderAuditVal']->iteration === $_smarty_tpl->tpl_vars['orderAuditVal']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['auditVal']['last'] = $_smarty_tpl->tpl_vars['orderAuditVal']->last;
?>
                        <?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['sku']==$_smarty_tpl->tpl_vars['ssku']->value){?>
                        <span style="width:30%">采购审核：<?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='1'){?><font color="green" >通过</font><?php }elseif($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='2'){?><font color="red" >拦截</font><?php }else{ ?><font color="orange" >状态有误</font><?php }?></span>
                        <?php }?>
                        <?php } ?>
                    <?php } ?>
                <?php }?>
                
                
                    
                    <?php  $_smarty_tpl->tpl_vars['orderAuditVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderAuditVal']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderAudit']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['orderAuditVal']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['orderAuditVal']->key => $_smarty_tpl->tpl_vars['orderAuditVal']->value){
$_smarty_tpl->tpl_vars['orderAuditVal']->_loop = true;
 $_smarty_tpl->tpl_vars['orderAuditVal']->iteration++;
 $_smarty_tpl->tpl_vars['orderAuditVal']->last = $_smarty_tpl->tpl_vars['orderAuditVal']->iteration === $_smarty_tpl->tpl_vars['orderAuditVal']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['auditVal']['last'] = $_smarty_tpl->tpl_vars['orderAuditVal']->last;
?>
                    <?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['sku']==$_smarty_tpl->tpl_vars['orderDetailData']->value['sku']){?>
                        
                        <span style="width:30%">采购审核：<?php if ($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='1'){?><font color="green" >通过</font><?php }elseif($_smarty_tpl->tpl_vars['orderAuditVal']->value['auditStatus']=='2'){?><font color="red" >拦截</font><?php }else{ ?><font color="orange" >状态有误</font><?php }?></span>
                    
                    <?php }?>
                    
                        
                    <?php } ?>
                
                
            </td>
            <td class="unpicurl" style="width:13%;">
                <?php if ($_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemTitle']){?><a href="<?php if ($_smarty_tpl->tpl_vars['orderData']->value['platformId']==1){?>http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
<?php echo $_smarty_tpl->tpl_vars['ebaylistingurl']->value;?>
<?php }elseif($_smarty_tpl->tpl_vars['orderData']->value['platformId']==2){?>http://www.aliexpress.com/item/New-1mm-Silver-Metallic-Caviar-Beads-Studs-Nail-Art-Glitter-Nail-Decoration-13229/<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
.html<?php }elseif($_smarty_tpl->tpl_vars['orderData']->value['platformId']==11){?>http://www.amazon.com/gp/product/<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
<?php }?>" target="_blank">(<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemId'];?>
)&nbsp;<?php echo $_smarty_tpl->tpl_vars['orderDetailExtenData']->value['itemTitle'];?>
</a><?php }else{ ?>--<?php }?>
            </td>
        </tr-->
                    <?php }
if (!$_smarty_tpl->tpl_vars['orderDetail']->_loop) {
?>
                    <tr>
                    <td colspan="17" >
                        <span>订单明细为空，请核实订单的信息！</span>
                    </td>
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
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['combinePackage']==1&&$_smarty_tpl->tpl_vars['combinePackage']->value['son']!=''){?>
                    该订单为#[<?php echo $_smarty_tpl->tpl_vars['combinePackage']->value['son'];?>
]#合并包裹发货
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['combinePackage']==2&&$_smarty_tpl->tpl_vars['combinePackage']->value['main']!=''){?>
                   该订单被<?php echo $_smarty_tpl->tpl_vars['combinePackage']->value['main'];?>
合并
                <?php }?>
                </span>
                <span>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isSplit']==1){?>
                    被拆分的订单
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isSplit']==2){?>
                    拆分产生的订单
                <?php }?>
                </span>
                <span>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isCopy']==1){?>
                    被复制订单
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isCopy']==2){?>
                	<?php if ($_smarty_tpl->tpl_vars['orderData']->value['isBuji']==2){?>
                    补寄订单
                    <?php }else{ ?>
                    复制订单
                    <?php }?>
                <?php }?>
                </span>
                <span>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['combineOrder']==2){?>
                    合并订单
                <?php }?>
                </span>
                <?php if ($_smarty_tpl->tpl_vars['orderNote']->value){?>
                <span>
                备注：
                <?php  $_smarty_tpl->tpl_vars['ve'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ve']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderNote']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ve']->key => $_smarty_tpl->tpl_vars['ve']->value){
$_smarty_tpl->tpl_vars['ve']->_loop = true;
?>
                    <?php if ($_smarty_tpl->tpl_vars['ve']->value['content']!=''){?>
                    <?php echo $_smarty_tpl->tpl_vars['ve']->value['content'];?>
&nbsp;
                    <?php }?>
                <?php } ?>
                </span>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orderData']->value['isLock']==1){?> <span> 锁定人：<?php $_smarty_tpl->tpl_vars['lockUser'] = new Smarty_variable(UserModel::getUsernameById($_smarty_tpl->tpl_vars['orderData']->value['lockUser']), null, 0);?><?php echo $_smarty_tpl->tpl_vars['lockUser']->value;?>
 &nbsp;&nbsp; 锁定时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['orderData']->value['lockTime'],"%Y-%m-%d %H:%I:%S");?>
</span> <?php }?>
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