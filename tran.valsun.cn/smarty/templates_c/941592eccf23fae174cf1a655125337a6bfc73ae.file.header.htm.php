<?php /* Smarty version Smarty-3.1.12, created on 2013-10-30 11:40:36
         compiled from "/data/web/tran.valsun.cn/html/template/header.htm" */ ?>
<?php /*%%SmartyHeaderCode:170693061552707fb4e63401-00032436%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '941592eccf23fae174cf1a655125337a6bfc73ae' => 
    array (
      0 => '/data/web/tran.valsun.cn/html/template/header.htm',
      1 => 1383103635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170693061552707fb4e63401-00032436',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'mod' => 0,
    '_username' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52707fb4eb3d52_57568004',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52707fb4eb3d52_57568004')) {function content_52707fb4eb3d52_57568004($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<script type="text/javascript" src="http://misc.erp.valsun.cn/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="./public/js/command.js"></script>
<script type="text/javascript" src="./public/js/My97DatePicker/WdatePicker.js"></script>
<script src="./public/js/jquery-ui-1.9.2.custom.js"></script>
<script src="http://api.notice.valsun.cn/js/swJsNotice.js" type="text/javascript"></script>
<link href="http://misc.erp.valsun.cn/css/style.css" rel="stylesheet" type="text/css" />
<link href="http://misc.erp.valsun.cn/css/page.css" rel="stylesheet" type="text/css" />
<link href="http://misc.erp.valsun.cn/css/alertify.css" rel="stylesheet" type="text/css" />
<link href="./public/css/ui-lightness/jquery-ui-1.9.2.custom.css" rel="stylesheet">
<script type="text/javascript">
var web_api = "<?php echo @WEB_API;?>
";
</script>
</head>
<body class="tran-body">
	<div class="container">
    	<div class="content">
        	<div class="header">
            	<div class="logo">
                	运输方式管理系统
                </div>
                <div class="onevar purchase-onevar">
                	<ul>
                        <li>
                            <a href="index.php?mod=shipfeeQuery&act=index" class="FreightInquiry <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('shipfeeQuery'))){?>cho<?php }?>">运费查询</a>
                        </li>
                        <li>
                            <a href="index.php?mod=carrierManage&act=index" class="Transportation <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('carrierManage','channelManage','partitionManage','shipPrice'))){?>cho<?php }?>">运输方式管理</a>
                        </li>						
                        <li>
                            <a href="index.php?mod=countriesStandard&act=index" class="CountryList <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('countriesStandard','countriesSmall','countriesShip'))){?>cho<?php }?>" >国家列表管理</a></li>
                        </li>
                        <li>
                        	<a href="index.php?mod=shippingAddress&act=index" class="Address <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('shippingAddress'))){?>cho<?php }?>">发货地址管理</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=platForm&act=index" class="PlatformManagement <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('platForm'))){?>cho<?php }?>">平台管理</a>
                        </li>
						<li>
                        	<a href="index.php?mod=user&act=index" class="Authorize <?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?>cho<?php }?>">授权管理</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                	<a class="news-img" href="javascript:javascript:void(0)" onclick="swntc_call('<?php echo $_smarty_tpl->tpl_vars['_username']->value;?>
')">消息</a>
                	<a href="javascript:void(0);" ><?php echo $_smarty_tpl->tpl_vars['_username']->value;?>
</a><a href="index.php?mod=public&act=logout" style="background: none; font-size: 14px;" title="注销安全退出">退出</a>
                </div>
            </div>
            <div class="twovar <?php if (!in_array($_smarty_tpl->tpl_vars['mod']->value,array('countriesStandard','countriesSmall','countriesShip','user','job','dept'))){?>nothing-twovar<?php }?>">
            	<ul>
					<?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('countriesStandard','countriesSmall','countriesShip'))){?>
                    <li><a href="index.php?mod=countriesStandard&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='countriesStandard'){?>class="cho"<?php }?>>标准国家列表</a></li>
					<li><a href="index.php?mod=countriesSmall&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='countriesSmall'){?>class="cho"<?php }?>>小语种对照国家列表</a></li>
					<li><a href="index.php?mod=countriesShip&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='countriesShip'){?>class="cho"<?php }?>>运输方式对照国家列表</a></li>
					<?php }?>
					<?php if (in_array($_smarty_tpl->tpl_vars['mod']->value,array('user','job','dept'))){?>
                    <li><a href="index.php?mod=user&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='user'){?>class="cho"<?php }?>>用户管理</a></li>
					<li><a href="index.php?mod=job&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='job'){?>class="cho"<?php }?>>岗位管理</a></li>
					<li><a href="index.php?mod=dept&act=index" <?php if ($_smarty_tpl->tpl_vars['mod']->value=='dept'){?>class="cho"<?php }?>>部门管理</a></li>
					<?php }?>
                </ul>
            </div><?php }} ?>