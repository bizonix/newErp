<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 22:54:14
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\header.htm" */ ?>
<?php /*%%SmartyHeaderCode:555153119707169189-06661947%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f214174b3e8f7d67c78d00918ac798b7d50c8c3' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\header.htm',
      1 => 1394203133,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '555153119707169189-06661947',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5311970728c7b5_62626679',
  'variables' => 
  array (
    'toptitle' => 0,
    'toplevel' => 0,
    'mod' => 0,
    '_username' => 0,
    'ostatusList' => 0,
    'list' => 0,
    'ostatus' => 0,
    'refund_total' => 0,
    'abnormal_total' => 0,
    'second_type' => 0,
    'second_count' => 0,
    'secondlevel' => 0,
    'act' => 0,
    'threelevel' => 0,
    'otype' => 0,
    'o_threelevel' => 0,
    'extral_str' => 0,
    'three_count' => 0,
    'status' => 0,
    'refund_one' => 0,
    'refund_two' => 0,
    'refund_three' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5311970728c7b5_62626679')) {function content_5311970728c7b5_62626679($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['toptitle']->value;?>
--订单系统</title>

<script type="text/javascript" src="http://misc.erp.valsun.cn/js/jquery-1.8.3.min.js"></script>
<link href="http://misc.erp.valsun.cn/css/style.css" rel="stylesheet" type="text/css" />
<link href="http://misc.erp.valsun.cn/css/page.css" rel="stylesheet" type="text/css" />

<script src="http://api.notice.valsun.cn/js/swJsNotice.js" type="text/javascript"></script>
<script src="http://misc.erp.valsun.cn/js/global.js" type="text/javascript"></script>

<link rel="stylesheet" href="./js/jquery/css/ui-lightness/jquery-ui-1.9.2.custom.min.css" />
<link rel="stylesheet" media="all" href="./js/jquery/css/ui-lightness/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" href="./css/alertify/alertify.core.css" />
<link rel="stylesheet" href="./css/alertify/alertify.default.css" />
<link rel="stylesheet" href="./css/flexselect.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="./css/validationEngine/validationEngine.jquery.css" />

<script type="text/javascript" src="./js/jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery-ui.min.js"></script>
<script src="./js/validationEngine/languages/jquery.validationEngine-zh_CN.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/validationEngine/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="./js/alertify/alertify.min.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="./js/jquery/ui/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="./js/datetimepicker.js"></script>
<script type="text/javascript" src="./js/My97DatePicker/WdatePicker.js"></script>

</head>
<body id="toppage"  class="order-body">
	<div class="container container_wh">
    	<div class="content">
        	<div class="header">
            	<div class="logo">
                	订单系统
                </div>
                <div class="onevar">
                	<ul>
                    	<li>
                        	<a href="index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=101" class="cental <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===0){?>cho<?php }?>">订单中心</a>
                        </li>
                        <!--li>
                        	<a href="#" style="padding:51px 7px 7px 7px;" class="personal <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===1){?>cho<?php }?>">个人待处理</a>
                        </li-->
                        <li>
                        	<a href="index.php?mod=orderAdd&act=addOrder" class="writein <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===2){?>cho<?php }?>">订单录入</a>
                        </li>
                        <li>
                            <a href="index.php?mod=exportXls&act=index" class="suppliermanage cental <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===5){?>cho<?php }?>">报表导出</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=omPlatform&act=getOmPlatformList" class="setup <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===3){?>cho<?php }?>">系统设置</a>
                        </li>
						<li>
                        	<a href="index.php?mod=user&act=index" class="usersetup <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?>cho<?php }?>">授权管理</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                	<a class="news-img" href="javascript:javascript:void(0)" onclick="swntc_call('<?php echo $_smarty_tpl->tpl_vars['_username']->value;?>
')">消息</a>
                	<input id='swNoticeUrl' type='hidden'  value='index.php?mod=notice&act=sendMessage'/>
					<a href="javascript:void(0)"><?php echo $_smarty_tpl->tpl_vars['_username']->value;?>
</a>
                    <a href="index.php?mod=public&act=logout" style="background: none; font-size: 14px;">退出</a>
                </div>
            </div>
            <div class="twovar">
            	<ul>
					<?php if ($_smarty_tpl->tpl_vars['toplevel']->value==0){?>
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ostatusList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['list']->value['statusCode']==660){?>
							<li>
								<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=0" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['list']->value['statusCode']){?>class="cho"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['statusName'];?>
（<span><?php echo $_smarty_tpl->tpl_vars['refund_total']->value;?>
</span>）</a>
							</li>
                        <?php }elseif($_smarty_tpl->tpl_vars['list']->value['statusCode']==770){?>
							<li>
								<a href="index.php?mod=AbnormalStock&act=abnormalStockList" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['list']->value['statusCode']){?>class="cho"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['statusName'];?>
（<span><?php echo $_smarty_tpl->tpl_vars['abnormal_total']->value;?>
</span>）</a>
							</li>
						<?php }else{ ?>
							<li>
								<a href="index.php?mod=orderindex&act=getOrderList&ostatus=<?php echo $_smarty_tpl->tpl_vars['list']->value['statusCode'];?>
&otype=<?php echo $_smarty_tpl->tpl_vars['second_type']->value[$_smarty_tpl->tpl_vars['list']->value['statusCode']];?>
" <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==$_smarty_tpl->tpl_vars['list']->value['statusCode']){?>class="cho"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['statusName'];?>
<!--（<span><?php echo $_smarty_tpl->tpl_vars['second_count']->value[$_smarty_tpl->tpl_vars['list']->value['statusCode']];?>
</span>）--></a>
							</li>
						<?php }?>
					<?php } ?>
                	
					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==1){?>
					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==2){?>
					<li>
                    	<a href="index.php?mod=orderAdd&act=addOrder" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='21'){?>class="cho"<?php }?>>订单添加</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=underLineOrderImport&act=importOrder" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='22'){?>class="cho"<?php }?>>国内通用订单导入</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=underLineOrderImport&act=aliexpressimport" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='24'){?>class="cho"<?php }?>>速卖通订单导入</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=underLineOrderImport&act=aliexpressUnderline" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='25'){?>class="cho"<?php }?>>速卖通线下订单导入</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=underLineOrderImport&act=dhgate" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='26'){?>class="cho"<?php }?>>敦煌订单导入</a>
                    </li>
                    <li>
                        <a href="index.php?mod=underLineOrderImport&act=trustImport" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='29'){?>class="cho"<?php }?>>诚信通订单导入</a>
                    </li>
                    <li>
                    	<a href="#" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='27'){?>class="cho"<?php }?>>同步异常订单导入</a>
                    </li>
					<li>
                    	<a href="index.php?mod=missingOrderAdd&act=addMissingOrder" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='28'){?>class="cho"<?php }?>>漏单添加</a>
                    </li>
					<li>
                    	<a href="index.php?mod=orderTranUps&act=index" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='29'){?>class="cho"<?php }?>>UPS美国专线跟踪号导入</a>
                    </li>
					<li>
                    	<a href="index.php?mod=underLineOrderImport&act=dresslinkOrderImport" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='210'){?>class="cho"<?php }?>>独立商城导入</a>
                    </li>
                    <li>
                        <a href="index.php?mod=underLineOrderImport&act=guoneiSaleImport" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='220'){?>class="cho"<?php }?>>国内销售部订单导入</a>
                    </li>

					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==3){?>
					<li>
                    	<a href="index.php?mod=omPlatform&act=getOmPlatformList" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='31'){?>class="cho"<?php }?>>平台管理</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=omAccount&act=getAccountList" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='32'){?>class="cho"<?php }?>>平台账号</a>
                    </li>
                    <li>
                    	<a href="index.php?act=paypalEmail&mod=paypalEmail" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='33'){?>class="cho"<?php }?>>Paypal付款邮箱</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=omBlackList&act=getOmBlackList" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='34'){?>class="cho"<?php }?>>平台黑名单</a>
                    </li>
					<!--li>
                    	<a href="index.php?mod=orderSetting&act=property" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='36'){?>class="cho"<?php }?>>订单属性</a>
                    </li-->
                    <!--<li>
                    	<a href="#" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='37'){?>class="cho"<?php }?>>权限管理</a>
                    </li>-->
					<li>
                    	<a href="index.php?mod=currency&act=currency" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='38'){?>class="cho"<?php }?>>汇率管理</a>
                    </li>
					<li>
                    	<a href="index.php?mod=StatusMenu&act=statusMenu" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='39'){?>class="cho"<?php }?>>订单流程</a>
                    </li>
                     <li>
                    	<a href="index.php?mod=orderOperationLog&act=orderOperationLogList" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='40'){?>class="cho"<?php }?>>订单操作日志查询</a>
                    </li>
					<li>
                    	<a href="index.php?mod=orderWarehouseRecord&act=orderWarehouseRecordList" <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='41'){?>class="cho"<?php }?>>订单配货记录查询</a>
                    </li>
                    <?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==5){?>
                    <li>
                        <a href="index.php?mod=exportXls&act=index" class="cho">报表导出</a>
                    </li>
					<?php }?>
					
					<?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?>
                    <li><a href="index.php?mod=user&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='user'){?>class="cho"<?php }?>>用户管理</a></li>
					<li><a href="index.php?mod=job&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='job'){?>class="cho"<?php }?>>岗位管理</a></li>
					<li><a href="index.php?mod=dept&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='dept'){?>class="cho"<?php }?>>部门管理</a></li>
					<?php }?>
                </ul>
            </div>
			<?php if (($_smarty_tpl->tpl_vars['threelevel']->value=='1'&&!empty($_smarty_tpl->tpl_vars['otype']->value))||$_smarty_tpl->tpl_vars['ostatus']->value==660){?>
		    <div class="threevar order-minwidth">
            	<ul>
					<?php if ($_smarty_tpl->tpl_vars['ostatus']->value==660){?>
                        <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['o_threelevel']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                            <li>
                                <a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=<?php echo $_smarty_tpl->tpl_vars['ostatus']->value;?>
&otype=<?php echo $_smarty_tpl->tpl_vars['list']->value['statusCode'];?>
&<?php echo $_smarty_tpl->tpl_vars['extral_str']->value[$_smarty_tpl->tpl_vars['list']->value['statusCode']];?>
" <?php if ($_smarty_tpl->tpl_vars['otype']->value==$_smarty_tpl->tpl_vars['list']->value['statusCode']){?>class="cho"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['statusName'];?>
（<span><?php echo $_smarty_tpl->tpl_vars['three_count']->value[$_smarty_tpl->tpl_vars['list']->value['statusCode']];?>
</span>）</a>
                            </li>
						
						<?php } ?>
						<!--<li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=0&orderType=1" <?php if ($_smarty_tpl->tpl_vars['status']->value=='0'){?>class="cho"<?php }?>>paypal退款待处理（<span><?php echo $_smarty_tpl->tpl_vars['refund_one']->value;?>
</span>）</a>
						</li>
						<li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=1&orderType=1" <?php if ($_smarty_tpl->tpl_vars['status']->value==1){?>class="cho"<?php }?>>paypal已退款（<span><?php echo $_smarty_tpl->tpl_vars['refund_two']->value;?>
</span>）</a>
						</li>
						<li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=2&orderType=1" <?php if ($_smarty_tpl->tpl_vars['status']->value==2){?>class="cho"<?php }?>>paypal已取消退款（<span><?php echo $_smarty_tpl->tpl_vars['refund_three']->value;?>
</span>）</a>
						</li>
                        <li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=0&orderType=2" <?php if ($_smarty_tpl->tpl_vars['status']->value==1){?>class="cho"<?php }?>>手工退款待处理（<span><?php echo $_smarty_tpl->tpl_vars['refund_two']->value;?>
</span>）</a>
						</li>
						<li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=1&orderType=2" <?php if ($_smarty_tpl->tpl_vars['status']->value==2){?>class="cho"<?php }?>>手工已退款（<span><?php echo $_smarty_tpl->tpl_vars['refund_three']->value;?>
</span>）</a>
						</li>
                        <li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=2&orderType=2" <?php if ($_smarty_tpl->tpl_vars['status']->value==2){?>class="cho"<?php }?>>手工已取消退款（<span><?php echo $_smarty_tpl->tpl_vars['refund_three']->value;?>
</span>）</a>
						</li>
                        <li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=0&orderType=3" <?php if ($_smarty_tpl->tpl_vars['status']->value==2){?>class="cho"<?php }?>>CASE待处理（<span><?php echo $_smarty_tpl->tpl_vars['refund_three']->value;?>
</span>）</a>
						</li>
                        <li>
							<a href="index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status=1&orderType=3" <?php if ($_smarty_tpl->tpl_vars['status']->value==2){?>class="cho"<?php }?>>CASE已处理（<span><?php echo $_smarty_tpl->tpl_vars['refund_three']->value;?>
</span>）</a>
						</li>-->
					<?php }else{ ?>
						<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['o_threelevel']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
								<li>
									<a href="index.php?mod=orderindex&act=getOrderList&ostatus=<?php echo $_smarty_tpl->tpl_vars['ostatus']->value;?>
&otype=<?php echo $_smarty_tpl->tpl_vars['list']->value['statusCode'];?>
" <?php if ($_smarty_tpl->tpl_vars['otype']->value==$_smarty_tpl->tpl_vars['list']->value['statusCode']){?>class="cho"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['statusName'];?>
（<span><?php echo $_smarty_tpl->tpl_vars['three_count']->value[$_smarty_tpl->tpl_vars['list']->value['statusCode']];?>
</span>）</a>
								</li>
						
						<?php } ?>
					<?php }?>
                </ul>
            </div>
			<?php }?>
            <?php }} ?>