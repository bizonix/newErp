<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 21:35:02
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\header.htm" */ ?>
<?php /*%%SmartyHeaderCode:25251531849f200c595-14786850%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0fe4a1ab8d5c5e0642dae37955f0c6af1221a3c1' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\header.htm',
      1 => 1394112878,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25251531849f200c595-14786850',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_531849f212e659_70937006',
  'variables' => 
  array (
    'toptitle' => 0,
    'toplevel' => 0,
    'mod' => 0,
    '_userCNname' => 0,
    'act' => 0,
    'secondlevel' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_531849f212e659_70937006')) {function content_531849f212e659_70937006($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['toptitle']->value;?>
--仓库系统</title>
<link href="http://misc.erp.valsun.cn/css/style.css" rel="stylesheet" type="text/css" />
<link href="http://misc.erp.valsun.cn/css/page.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="js/css/ui-lightness/jquery-ui-1.9.2.custom.min.css" />
<script language="javascript" src="js/jquery-1.8.3.js"></script>
<script language="javascript" src="js/ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="./js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" href="./css/validationEngine/validationEngine.jquery.css" type="text/css"/>
<script src="./js/languages/jquery.validationEngine-zh_CN.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script language="javascript" src="./js/bootstrap.min.js"></script>
<link rel="stylesheet" href="./css/alertify/alertify.core.css" />
<link rel="stylesheet" href="./css/alertify/alertify.default.css" />
<script language="javascript" type="text/javascript" src="./js/alertify/alertify.min.js"></script>
<script type="text/javascript" src="./js/My97DatePicker/WdatePicker.js"></script>
<script src="http://api.notice.valsun.cn/js/swJsNotice.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="./js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />

</head>
<body id="toppage">
	<div class="container container_wh">
    	<div class="content main-position">
        	<div class="header">
            	<div class="logo">
                	仓库系统
                </div>
                <div class="onevar">
                    <ul class="wh_toul">
                    	<li>
                        	<a class=" house <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===0){?>cho<?php }?>"  href="index.php?mod=skuStock&act=searchSku">&nbsp;&nbsp;库存管理&nbsp;&nbsp;</a>
                        </li>
                        <li>
                            <a class=" outhouse <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===2){?>cho<?php }?>"  href="index.php?mod=dispatchBillQuery&act=showForm&storeId=1">&nbsp;&nbsp;出库业务&nbsp;&nbsp;</a>
                        </li>
                        <li>
                        	<a class=" inhouse <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===1){?>cho<?php }?>"  href="index.php?act=packageCheck&mod=packageCheck">&nbsp;&nbsp;入库业务&nbsp;&nbsp;</a>
                        </li>
						<li>
                            <a class=" receipt <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===3){?>cho<?php }?>"  href="index.php?mod=whIoStore&act=getWhOutStoreList">&nbsp;&nbsp;单据业务&nbsp;&nbsp;</a>
                        </li>
                        <li>
                            <a class=" housesetup <?php if ($_smarty_tpl->tpl_vars['toplevel']->value===4){?>cho<?php }?>"  href="index.php?mod=warehouseManagement&act=whStore">&nbsp;&nbsp;仓库设置&nbsp;&nbsp;</a>
                        </li>
						<li>
                        	<a href="index.php?mod=user&act=index" class=" systemsettings <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?> cho<?php }?>">系统设置</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
					<a class="news-img" href="javascript:javascript:void(0)" onclick="swntc_call('<?php echo $_smarty_tpl->tpl_vars['_userCNname']->value;?>
')">消息</a>
                	<a href="javascript:void(0)"><?php echo $_smarty_tpl->tpl_vars['_userCNname']->value;?>
</a>
                    <a href="index.php?mod=public&act=logout" style="background: none; font-size: 14px;">退出</a>
                </div>
            </div>
            <div class="twovar">

            	<ul>
					<?php if ($_smarty_tpl->tpl_vars['toplevel']->value==0){?>
					<?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?>
                    <li><a href="index.php?mod=user&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='user'){?>class="cho"<?php }?>>用户管理</a></li>
					<li><a href="index.php?mod=job&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='job'){?>class="cho"<?php }?>>岗位管理</a></li>
					<li><a href="index.php?mod=dept&act=index" <?php if ($_smarty_tpl->tpl_vars['act']->value=='index'&&$_smarty_tpl->tpl_vars['mod']->value=='dept'){?>class="cho"<?php }?>>部门管理</a></li>
					<?php }else{ ?>
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='01'){?>class="cho"<?php }?> href="index.php?mod=skuStock&act=searchSku">货品资料</a>
			        </li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='34'){?>class="cho"<?php }?> href="index.php?mod=whIoRecords&act=getWhIoRecordsList&ioType=1">出库记录</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='35'){?>class="cho"<?php }?> href="index.php?mod=whIoRecords&act=getWhIoRecordsList&ioType=2">入库记录</a>
					</li>
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='04'){?>class="cho"<?php }?> href="index.php?mod=inventory&act=inventory">盘点管理</a>
			        </li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='09'){?>class="cho"<?php }?> href="index.php?mod=WhRecManage&act=getWhRecManageList">收货管理</a>
					</li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='02'){?>class="cho"<?php }?> href="index.php?mod=whExportManage&act=whExportManageList">仓库报表管理</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='03'){?>class="cho"<?php }?> href="index.php?mod=packageCheck&act=showPackage">点货清单</a>
					</li>
					<?php }?>
					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==1){?>
                    <!--<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='10'){?>class="cho"<?php }?> href="#">入库清单</a>
					</li>-->
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='11'){?>class="cho"<?php }?> href="index.php?mod=packageCheck&act=packageCheck">点货操作</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='12'){?>class="cho"<?php }?> href="index.php?mod=printLabel&act=printLabel">打标操作</a>
					</li>
					<!--<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='13'){?>class="cho"<?php }?> href="#">IQC</a>
					</li>-->
					<!--li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='14'){?>class="cho"<?php }?> href="index.php?mod=whShelf&act=whShelf">上架操作</a>
					</li-->
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='15'){?>class="cho"<?php }?> href="index.php?mod=whNoOrder&act=whNoOrder">未订单列表</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='16'){?>class="cho"<?php }?> href="index.php?mod=postOfficeReturned&act=returned">邮局退回</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='17'){?>class="cho"<?php }?> href="index.php?mod=pasteLabel&act=pasteLabel">贴标录入</a>
					</li>
					
					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==2){?>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='21'){?>class="cho"  <?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&storeId=1">发货单</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='22'){?>class="cho"	<?php }?> href="index.php?mod=orderWaitforPrint&act=printList">打印发货单</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='23'){?>class="cho"  <?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=23&status=402">待配货</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='24'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=24&status=403">待复核</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='25'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=25&status=404">待包装</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='26'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=26&status=405">待称重</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='27'){?>class="cho"	<?php }?> href="index.php?mod=orderPartion&act=orderPartion">待分区</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='28'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=28&status=605">快递待复核</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='29'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=29&status=900">废弃发货单</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='212'){?>class="cho"	<?php }?> href="index.php?mod=dispatchBillQuery&act=showForm&secondlevel=212&status=901">异常发货单</a></li>
					<li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='210'){?>class="cho"	<?php }?> href="index.php?mod=pdaManagement&act=inquiry">pda操作查询</a></li>
                    <li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='211'){?>class="cho"	<?php }?> href="index.php?mod=skuWeighing&act=skuWeighing">料号称重</a></li>
                    <li><a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='213'){?>class="cho" <?php }?> href="index.php?mod=dispatchBillScan&act=inputForm">异常发货单扫描</a></li>
					<?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==3){?>
                    <!--
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='30'){?>class="cho"<?php }?> href="index.php?mod=internalIoSell&act=internalBuyList">内部使用申请单</a>
			        </li>
                    -->
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='31'){?>class="cho"<?php }?> href="index.php?mod=internalIoSell&act=internalUseIostoreList">内部使用</a>
			        </li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='32'){?>class="cho"<?php }?> href="index.php?mod=whIoStore&act=getWhOutStoreList">出库单</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='33'){?>class="cho"<?php }?> href="index.php?mod=whIoStore&act=getWhInStoreList">入库单</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='34'){?>class="cho"<?php }?> href="index.php?mod=whIoStore&act=getAuditOutStoreList">待审核出库单</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='35'){?>class="cho"<?php }?> href="index.php?mod=whIoStore&act=getAuditInStoreList">待审核入库单</a>
					</li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='011'){?>class="cho"<?php }?> href="index.php?mod=WhAudit&act=getWhAuditList">审核管理</a>
					</li>
                    <li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='012'){?>class="cho"<?php }?> href="index.php?mod=WhAudit&act=getWhAuditRecords">审核记录</a>
					</li>
					
                    <?php }elseif($_smarty_tpl->tpl_vars['toplevel']->value==4){?>
					<!--<li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='02'){?>class="cho"<?php }?> href="index.php?mod=goodsInfo&act=showSearchForm">货品资料</a>
			        </li>-->
			        <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='05'){?>class="cho"<?php }?> href="index.php?mod=warehouseManagement&act=whStore">仓库列表</a>
			        </li>
			        <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='06'){?>class="cho"<?php }?> href="index.php?mod=warehouseManagement&act=whIoTypeList">出入库类型</a>
			        </li>
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='07'){?>class="cho"<?php }?> href="index.php?mod=warehouseManagement&act=whIoInvoicesTypeList">单据类型</a>
			        </li>
                    <li>
			            <a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='08'){?>class="cho"<?php }?> href="index.php?mod=LibraryStatus&act=libraryStatusList">状态管理</a>
			        </li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='010'){?>class="cho"<?php }?> href="index.php?mod=position&act=positionList">A仓管理</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='011'){?>class="cho"<?php }?> href="index.php?mod=position&act=positionListB">B仓管理</a>
					</li>
					<li>
						<a <?php if ($_smarty_tpl->tpl_vars['secondlevel']->value=='46'){?>class="cho"<?php }?> href="index.php?mod=locationPrint&act=locationPrint">仓位打印</a>
					</li>
					<?php }?>
					
                </ul>

            </div>
<?php }} ?>