<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 09:42:38
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\dispatchbillquery.htm" */ ?>
<?php /*%%SmartyHeaderCode:22471531859957ea005-71233751%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6609a33295537e964d9b5c1a976082ba501c536d' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\dispatchbillquery.htm',
      1 => 1394113016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22471531859957ea005-71233751',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53185995998fe7_01276803',
  'variables' => 
  array (
    'storeId' => 0,
    'secondlevel' => 0,
    'keywords' => 0,
    'keytype' => 0,
    'outstatuslist' => 0,
    'status' => 0,
    'itemvar' => 0,
    'ordertimestart' => 0,
    'ordertimeend' => 0,
    'isNote' => 0,
    'orderTypeId' => 0,
    'shiptype' => 0,
    'shipingtypelist' => 0,
    'shitemval' => 0,
    'shippingList' => 0,
    'clientname' => 0,
    'salesaccountlist' => 0,
    'account' => 0,
    'accounts' => 0,
    'salesaccount' => 0,
    'hunhe' => 0,
    'platformList' => 0,
    'platform' => 0,
    'platLists' => 0,
    'platformName' => 0,
    'pagestr' => 0,
    'billlist' => 0,
    'billvalue' => 0,
    'skuval' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53185995998fe7_01276803')) {function content_53185995998fe7_01276803($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('goodsoutnav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!--script type="text/javascript" src="http://misc.erp.valsun.cn/js/global.js"--></script>
<link href="css/dispatch.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" src="js/dispatchbillquery.js"></script>
<script type="text/javascript" src="./js/fancybox.js"></script>
<script language="javascript" src="js/inventory.js"></script>
<link href="css/dispatch.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./js/css/ui-lightness/jquery-ui-1.9.2.custom.min.css" />
<link rel="stylesheet" media="all" href="./js/css/ui-lightness/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="./js/ui/jquery-ui.min.js"></script>
<script src="./js/ui/jquery-ui-timepicker-addon.js"></script>
	<?php if ($_smarty_tpl->tpl_vars['storeId']->value==1||$_smarty_tpl->tpl_vars['storeId']->value==2){?>
	<div class="servar wh-servar" style="padding:14px;">
        <a class="gdhref" href="index.php?mod=dispatchBillQuery&act=showForm&storeId=1">A仓发货单</a>
		<a class="gdhref" href="index.php?mod=dispatchBillQuery&act=showForm&storeId=2">B仓发货单</a>
    </div>
	<br/>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['secondlevel']->value==23){?>
	<div class="servar wh-servar" style="padding:14px;">
        <a class="gdhref" href="index.php?mod=GetGoods&act=GetGoodsScanPageEX">配货扫描(快递)</a>
		<a class="gdhref" href="index.php?mod=GetGoods&act=GetGoodsScanPageInland">配货扫描(国内)</a>
    </div>
	<br/>
	<?php }elseif($_smarty_tpl->tpl_vars['secondlevel']->value==24){?>
	<div class="servar wh-servar" style="padding:14px;">
        <a class="gdhref" href="index.php?mod=recheck&act=recheckForm">复核扫描(快递)</a>
		<a class="gdhref" href="index.php?mod=orderReview&act=orderReview">复核扫描(小包)</a>
    </div>
	<br/>
	<?php }elseif($_smarty_tpl->tpl_vars['secondlevel']->value==25){?>
	<div class="servar wh-servar" style="padding:14px;">
        <a class="gdhref" href="index.php?mod=waitpacking&act=packingform">包装扫描</a>
    </div>
	<br/>
	<?php }elseif($_smarty_tpl->tpl_vars['secondlevel']->value==26){?>
	<div class="servar wh-servar" style="padding:14px;">
		<a class="gdhref" href="index.php?mod=waitWeighing&act=weighingForm">称重扫描<快递></a>
		<a class="gdhref" href="index.php?mod=waitWeighing&act=weighingFormInland">称重扫描<国内></a>
		<a class="gdhref" href="index.php?mod=orderWeighing&act=orderWeighing">称重扫描<小包></a>
    </div>
	<br/>
	<?php }elseif($_smarty_tpl->tpl_vars['secondlevel']->value==28){?>
	<div class="servar wh-servar" style="padding:14px;">
        <a class="gdhref" href="index.php?mod=expressRecheck&act=recheckScan">快递复核扫描(单跟踪号)</a>
		<a class="gdhref" href="index.php?mod=expressRecheck&act=recheckScanMul">快递复核扫描(多跟踪号)</a>
		<a class="gdhref" href="index.php?mod=expressRecheck&act=trackNumberInput">跟踪号数据导入</a>
    </div>
	<br/>
	<?php }?>

	<div class="fourvar order-fourvar feedback-fourvar products-servar wh-fourvar">
	 <form id="queryform" method="get" class="queryform">
			<input type="hidden" name="mod" value="dispatchBillQuery" />
            <input type="hidden" name="act" value="showForm" />
			<input type="hidden" name="secondlevel" value="<?php echo $_smarty_tpl->tpl_vars['secondlevel']->value;?>
" />
			<input type="hidden" name="storeId" value="<?php echo $_smarty_tpl->tpl_vars['storeId']->value;?>
" />
		<table>
			<tr>
				<td style="padding-left:17px;">
					发货单查询：
				</td>
				<td>
					<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" name='keywords'/>
				</td>
				<td style="padding-left:17px;">
					类型：
				</td>
				<td>
					<select name="keytype" >
						<option <?php if ($_smarty_tpl->tpl_vars['keytype']->value==2){?>selected="selected"<?php }?> value="2">配货单号</option>
                        <option <?php if ($_smarty_tpl->tpl_vars['keytype']->value==1){?>selected="selected"<?php }?> value="1">订单号</option>
						<option <?php if ($_smarty_tpl->tpl_vars['keytype']->value==3){?>selected="selected"<?php }?> value="3">sku</option>
                    </select>
				</td>
				<td style="padding-left:17px;">
					状态：
				</td>
				<td>
					<select name="status" id="status">
                        <option value="0">请选择状态</option>
                        <?php  $_smarty_tpl->tpl_vars['itemvar'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itemvar']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['outstatuslist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itemvar']->key => $_smarty_tpl->tpl_vars['itemvar']->value){
$_smarty_tpl->tpl_vars['itemvar']->_loop = true;
?>
                            <option  <?php if ($_smarty_tpl->tpl_vars['status']->value==$_smarty_tpl->tpl_vars['itemvar']->value['statusCode']){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['itemvar']->value['statusCode'];?>
"><?php echo $_smarty_tpl->tpl_vars['itemvar']->value['statusName'];?>
</option>
                        <?php } ?>
                    </select>
				</td>
				<td style="padding-left:17px;">
					进入系统日期：
				</td>
				<td>
					<!--input value="<?php echo $_smarty_tpl->tpl_vars['ordertimestart']->value;?>
" type="text" id='ordertimestart' name='ordertimestart' onclick="WdatePicker()" /--> -
					<input type="text" name="startdate" id="startdate" value="<?php echo $_smarty_tpl->tpl_vars['ordertimestart']->value;?>
" />
				</td>
				<td>
					<!--input type="text" value="<?php echo $_smarty_tpl->tpl_vars['ordertimeend']->value;?>
" id="ordertimeend" name="ordertimeend" onclick="WdatePicker()" /-->
					<input type="text" name="enddate" id="enddate" value="<?php echo $_smarty_tpl->tpl_vars['ordertimeend']->value;?>
" />
					留言：
					<select name="isNote" id="isNote">
						<option value="">全部</option>
						<option value="1" <?php if ($_smarty_tpl->tpl_vars['isNote']->value=='1'){?>selected="selected"<?php }?>>有留言</option>
						<option value="2" <?php if ($_smarty_tpl->tpl_vars['isNote']->value=='2'){?>selected="selected"<?php }?>>无留言</option>
					</select>
					发货/配货单：
					<select name="orderTypeId" id="orderTypeId">
						<option value="1" <?php if ($_smarty_tpl->tpl_vars['orderTypeId']->value=='1'){?>selected="selected"<?php }?>>发货单</option>
						<option value="2" <?php if ($_smarty_tpl->tpl_vars['orderTypeId']->value=='2'){?>selected="selected"<?php }?>>配货单</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding-left:17px;">
					运输方式：
				</td>
				<td>
					<select name='shiptype' id='shiptype'>
                        <option value="">选择运输方式</option>
						<!--option value="200" <?php if ($_smarty_tpl->tpl_vars['shiptype']->value==200){?>selected="selected"<?php }?>>中国邮政平邮/挂号/香港小包平邮</option>
                        <option value="300" <?php if ($_smarty_tpl->tpl_vars['shiptype']->value==300){?>selected="selected"<?php }?>>EUB/Global Mail/新加坡邮政/德国邮政</option-->
						<?php  $_smarty_tpl->tpl_vars['shitemval'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shitemval']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shipingtypelist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shitemval']->key => $_smarty_tpl->tpl_vars['shitemval']->value){
$_smarty_tpl->tpl_vars['shitemval']->_loop = true;
?>
							<?php if (in_array($_smarty_tpl->tpl_vars['shitemval']->value['id'],$_smarty_tpl->tpl_vars['shippingList']->value)){?>
                            <option <?php if ($_smarty_tpl->tpl_vars['shiptype']->value==$_smarty_tpl->tpl_vars['shitemval']->value['id']){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['shitemval']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['shitemval']->value['carrierNameCn'];?>
</option>
							<?php }?>
						<?php } ?>
                    </select>
				</td>
				<td style="padding-left:17px;">
					客户购买账号：
				</td>
				<td>
					<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['clientname']->value;?>
" name='clientname' id='clientname'/>
				</td>
				<td style="padding-left:17px;">
					销售账号：
				</td>
				<td>
					<select name="salesaccount" id="acc" >
							<option value="">请选择账号</option>
							<?php  $_smarty_tpl->tpl_vars['account'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['account']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['salesaccountlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['account']->key => $_smarty_tpl->tpl_vars['account']->value){
$_smarty_tpl->tpl_vars['account']->_loop = true;
?>
								<?php if (in_array($_smarty_tpl->tpl_vars['account']->value['id'],$_smarty_tpl->tpl_vars['accounts']->value)){?>
								<option <?php if ($_smarty_tpl->tpl_vars['salesaccount']->value==$_smarty_tpl->tpl_vars['account']->value['id']){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['account']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['account']->value['account'];?>
</option>
								<?php }?>
							<?php } ?>
						</select>
				</td>
				<td style="padding-left:17px;">
					混合订单：
				</td>
				<td>
					<select name="hunhe" id="hunhe">
						<option <?php if ($_smarty_tpl->tpl_vars['hunhe']->value==0){?>selected="selected"<?php }?> value="0">请选择类型:</option>
						<option <?php if ($_smarty_tpl->tpl_vars['hunhe']->value==1){?>selected="selected"<?php }?> value="1">两件或两件以上的订单</option>
						<option <?php if ($_smarty_tpl->tpl_vars['hunhe']->value==2){?>selected="selected"<?php }?> value="2">一件物品的订单(一个或多个数量)</option>
						<!--option <?php if ($_smarty_tpl->tpl_vars['hunhe']->value==3){?>selected="selected"<?php }?> value="3">组合订单</option-->
					</select>
				</td>
				<td>
					平台：
					<select name="platformName" id="platformName" >
							<option value="">请选择平台</option>
							<?php  $_smarty_tpl->tpl_vars['platform'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['platform']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['platformList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['platform']->key => $_smarty_tpl->tpl_vars['platform']->value){
$_smarty_tpl->tpl_vars['platform']->_loop = true;
?>
								<?php if (in_array($_smarty_tpl->tpl_vars['platform']->value['id'],$_smarty_tpl->tpl_vars['platLists']->value)){?>
								<option <?php if ($_smarty_tpl->tpl_vars['platformName']->value==$_smarty_tpl->tpl_vars['platform']->value['id']){?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['platform']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['platform']->value['platform'];?>
</option>
								<?php }?>
							<?php } ?>
						</select>
					<button type="button" onclick="dosearch()" >查 询</button>
				</td>
			</tr>
		</table>
		</form>
		<div style="padding-top:5px; padding-left:18px;">
			<label>
				全选:<input style="width:20px;padding-top:15px;" onclick="chooseornot(this)" type="checkbox" />
			</label>
			<button class="btn" id="application_print" storeId="<?php echo $_GET['storeId'];?>
">申请打印</button>
			<select style="margin-left:23px;" name="printid" id="printid" onchange="goprintById()">
				<option value="">打印预览</option>
				<option value="200">标签打印50*100</option>
				<option value="205">带留言标签打印50*100</option>
				<option value="201">快递A4</option>
				<option value="303">【UPS美国专线】快递A4打印</option>
				<!--option value="202">国际EUB热敏打印</option>
				<option value="203">德国GlobalMail</option>
				<option value="204">非德国GlbalMail</option>
				<option value="206">新加坡热敏打印</option-->
				<option value="207">新加坡/EUB/GlobalMail混合打印</option>
				<option value="208">部分包货打印50*100</option>
				<option value="50">DHL快递单打印</option>
				<option value="51">EMS国际特快</option>
				<option value="52">DHLfp</option>
				<option value="53">UPS快递单打印</option>
				<option value="54">EMS新加坡</option>
				<option value="1">芬哲圆通快递打印</option>
				<option value="2">芬哲申通快递打印</option>
				<option value="3">芬哲韵达快递打印</option>
				<option value="31">芬哲天天快递打印</option>
				<option value="32">芬哲中通快递打印</option>
				<option value="33">芬哲EMS打印</option>
				<option value="34">芬哲加运美打印</option>
				<option value="35">芬哲顺丰打印</option>
				<option value="4">哲果圆通快递打印</option>
				<option value="5">哲果申通快递打印</option>
				<option value="6">哲果韵达快递打印</option>
				<option value="7">哲果顺丰快递打印</option>
				<option value="71">哲果天天快递打印</option>
				<option value="72">哲果中通快递打印</option>
				<option value="73">哲果加运美快递打印</option>
				<option value="74">哲果EMS打印</option>
				<option value="8">EB0001申通快递打印</option>
				<option value="9">EB0001速尔快递打印</option>
				<option value="10">EB0001顺丰快递打印</option>
				<option value="91">EB0001圆通快递打印</option>
				<option value="92">EB0001加运美打印</option>
				<option value="93">EB0001国通快递打印</option>
				<option value="94">EB0001EMS打印</option>
				<option value="301">Finejo快递-A4(横向打印)</option>
				<option value="302">哲果发货清单-A4打印</option>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" value="" name='appnum' id='appnum'/><button class="btn" id="more_application" storeId="<?php echo $_GET['storeId'];?>
">批量申请打印</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button class="btn" id="markUnusual">标记为异常发货单</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<select style="margin-left:23px;" name="filesid" id="filesid" onchange="exportstofiles()">
				<option value="">运单导出</option>
				<option value="1">Fedex批量处理运单</option>
				<option value="2">DHL批量处理运单</option>
			</select>
		</div>
	</div>
	<div class="bottomvar">
		<div class="pagination">
			<?php echo $_smarty_tpl->tpl_vars['pagestr']->value;?>

		</div>
	</div>
    <div class="main order-main wh-main">
		<?php  $_smarty_tpl->tpl_vars['billvalue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['billvalue']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['billlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['billvalue']->key => $_smarty_tpl->tpl_vars['billvalue']->value){
$_smarty_tpl->tpl_vars['billvalue']->_loop = true;
?>
		<table cellspacing="0" width="100%" style="text-align:left;">
			<tr class="title">
				<td style="width:40px;" align="center" valign="middle">
					<input class="checkclass" id="orderids" name="orderids" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['id'];?>
">
				</td>
				<td colspan="20">
					<span style="width:190px;overflow:hidden;">发货单号：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['id'];?>
</span>
					<span style="width:190px;overflow:hidden;">平台：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['platformName'];?>
</span>
					<span style="width:190px;overflow:hidden;">客户账号：<input style="width:118px;border:none; background-color: #f2f2f2; outline:none;" value="<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['platformUsername'];?>
" readonly/></span>
					<span style="width:190px;overflow:hidden;">销售账号：<?php if (is_array($_smarty_tpl->tpl_vars['billvalue']->value['salesaccountinfo'])){?><?php echo $_smarty_tpl->tpl_vars['billvalue']->value['salesaccountinfo']['account'];?>
<?php }?></span>
					<span style="width:190px;overflow:hidden;">生成时间：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['createdTime'];?>
</span>
					<span style="width:190px;overflow:hidden;">运输：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['shipingname'];?>
</span>
					<span style="width:190px;overflow:hidden;">发往国家：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['countryName'];?>
</span>
					<span style="width:190px;overflow:hidden;">跟踪号：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['tracknumber'];?>
</span>
					<span style="width:190px;overflow:hidden;">发货单状态：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['statusname'];?>
</span>
					<span style="width:190px;overflow:hidden;">包材：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['materName'];?>
</span>
					<span style="width:190px;overflow:hidden;">重量：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['calcWeight'];?>
</span>
					<span style="width:190px;overflow:hidden;">系统订单编号：<input value="<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['originOrder'];?>
" style="border:none; background-color:#f2f2f2; outline:none; width:100px;" readonly=""></span>
				</td>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['skuval'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['skuval']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['billvalue']->value['skulistar']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['skuval']->key => $_smarty_tpl->tpl_vars['skuval']->value){
$_smarty_tpl->tpl_vars['skuval']->_loop = true;
?>
			<tr>
				<td class="unpicurl">
					<!--a href="javascript:void(0)" id="imga-<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>
" class="fancybox">
						<img src="./images/ajax-loader.gif" name="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>
" spu="<?php echo $_smarty_tpl->tpl_vars['skuval']->value['spu'];?>
">
				   </a-->
				   <a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>
" class="fancybox">
						<img src="./images/no_image.gif" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>
"  width="60" height="60" data-spu="<?php echo $_smarty_tpl->tpl_vars['skuval']->value['spu'];?>
" data-sku="<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>
">
			   		</a>
				</td>
				<td align="left">
					<span style="margin-right:100px;width:240px;overflow:hidden;white-space:nowrap;">
						标题：<?php echo $_smarty_tpl->tpl_vars['skuval']->value['goodsName'];?>

					</span>
					<span style="margin-right:100px;width:130px;overflow:hidden;white-space:nowrap;">
						sku:<?php echo $_smarty_tpl->tpl_vars['skuval']->value['sku'];?>

					</span>
					<span style="margin-right:100px;width:50px;overflow:hidden;white-space:nowrap;">
						数量:<?php echo $_smarty_tpl->tpl_vars['skuval']->value['amount'];?>

					</span>
					<span style="margin-right:100px;width:100px;overflow:hidden;white-space:nowrap;">
						可用库存:<?php echo $_smarty_tpl->tpl_vars['skuval']->value['actualStock'];?>

					</span>
				</td>
			</tr>
			<?php }
if (!$_smarty_tpl->tpl_vars['skuval']->_loop) {
?>
				<tr><td></td><td align="center">该订单无料号明细！</td></tr>
			<?php } ?>
			<tr>
				<td colspan="2" align="left" style="background-color:#f2f2f2; padding-left:35px;">
					<span width="40%">
						地址：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['street'];?>
&nbsp;
	                        <?php echo $_smarty_tpl->tpl_vars['billvalue']->value['city'];?>
&nbsp;
	                        <?php echo $_smarty_tpl->tpl_vars['billvalue']->value['state'];?>
&nbsp;
	                        <?php echo $_smarty_tpl->tpl_vars['billvalue']->value['countryName'];?>
&nbsp;
	                </span>
	                <span width="60%" style="margin-left:70px;">
	                	<?php if ($_smarty_tpl->tpl_vars['billvalue']->value['content']!=''){?>
							留言：<?php echo $_smarty_tpl->tpl_vars['billvalue']->value['content'];?>

						<?php }?>
	                </span>
				</td>
			</tr>
		</table>
		<?php }
if (!$_smarty_tpl->tpl_vars['billvalue']->_loop) {
?>
		<table cellspacing="0" width="100%">
			<tr><td align="center">没有搜索值！</td></tr>
		</table>
		<?php } ?>
    </div>
	<div class="bottomvar">
		<div class="pagination">
			<?php echo $_smarty_tpl->tpl_vars['pagestr']->value;?>

		</div>
	</div>
</div>
<!--p id="back-top">
    <a href="#toppage"><span></span>Back to Top</a>
</p-->
<form target="_blank" action="" method="post" id="hiddenpost" style="display:none;">
	<input type="hidden" id="idsinput" name="ids" value="">
	<input type="hidden" name="express" id="expressinput" value="">
</form>
<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!--script type="text/javascript">
//页面加载完成后加载图片

$(document).ready(function() {
	var url  = "json.php?mod=common&act=getSkuImg";
	var skuArr	= $('img[name="skuimg"]'), imgurl="", spu="", sku="";
	$.each(skuArr,function(i,item){
		sku	= $(item).attr('id').substring(5);
		spu	= $(item).attr('spu');
		$.ajax({
			url: url,
			type: "POST",
			async: true,
			data	: {spu:spu,sku:sku},
			dataType: "jsonp",
			success: function(rtn){
							sku	= $(item).attr('id').substring(5);
							//console.log(rtn.errMsg);
							if ($.trim(rtn.data)) {
								$("#imgs-"+sku).attr({"src":rtn.data,"width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href",rtn.data);
							} else {
								$("#imgs-"+sku).attr({"src":"./images/no_image.gif","width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href","./images/no_image.gif");
							}
				}
			});
	});
});

</script--><?php }} ?>