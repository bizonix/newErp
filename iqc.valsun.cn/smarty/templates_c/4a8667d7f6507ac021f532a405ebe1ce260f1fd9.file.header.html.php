<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:28
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\header.html" */ ?>
<?php /*%%SmartyHeaderCode:308851ff5944ae43b4-35180215%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4a8667d7f6507ac021f532a405ebe1ce260f1fd9' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\header.html',
      1 => 1375684319,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '308851ff5944ae43b4-35180215',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module' => 0,
    'username' => 0,
    'secnev' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5944aff8b2_32490078',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5944aff8b2_32490078')) {function content_51ff5944aff8b2_32490078($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['module']->value;?>
--iqc管理系统</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.8.3.js"></script>
<script type="text/javascript" src="js/easyTooltip.js"></script>
<script type="text/javascript" src="js/hoverIntent.js"></script>
<script type="text/javascript" src="js/superfish.js"></script>
<script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="js/custom.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<link rel="stylesheet" href="./css/validationEngine/validationEngine.jquery.css" type="text/css"/>
<script src="./js/languages/jquery.validationEngine-zh_CN.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/general.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="css/iqc.css">
</head>

<body>
	<div class="container">
    	<div class="content">
        	<div class="header">
            	<div class="logo">
                	IQC管理系统
                </div>
                <div class="onevar">
                	<ul>
                    	<li>
                        	<a href="index.php?mod=iqc&act=iqcList">IQC检测领取</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=iqcDetect&act=iqcScan">IQC检测</a>
                        </li>
						<li>
                        	<a href="index.php?mod=iqcInfo&act=iqcScanList">IQC检测信息</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=sampleStandard&act=nowSampleType">IQC检测标准</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
					<a href="index.php?mod=login&act=logout"><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
 退出</a>
                </div>
            </div>
            <div class="twovar">
			
            	<ul>
					<?php if ($_smarty_tpl->tpl_vars['secnev']->value==1){?>
                	<li>
                    	<a href="index.php?mod=iqc&act=iqcList">等待领取SKU</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=iqc&act=iqcWaitCheck">等待检测SKU</a>
                    </li>
					<?php }elseif($_smarty_tpl->tpl_vars['secnev']->value==2){?>
                    <li>
                    	<a href="index.php?mod=iqcDetect&act=iqcScan">IQC检测</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=iqcDetect&act=backScan">IQC退件处理</a>
                    </li>
					<li>
                    	<a href="index.php?mod=iqcDetect&act=stockScan">库存不良品处理</a>
                    </li>
					<?php }elseif($_smarty_tpl->tpl_vars['secnev']->value==3){?>
                    <li>
                    	<a href="index.php?mod=iqcInfo&act=iqcScanList">IQC检测信息</a>
                    </li>
					<li>
                    	<a href="">采购审核</a>
                    </li>
                    <li>
                    	<a href="">IQC不良品信息</a>
                    </li>
					<li>
                    	<a href="">IQC待定商品信息</a>
                    </li>
					<?php }elseif($_smarty_tpl->tpl_vars['secnev']->value==4){?>
                    <li>
                    	<a href="index.php?mod=sampleStandard&act=nowSampleType">当前检测标准</a>
                    </li>
					<li>
						<a href="index.php?mod=sampleStandard&act=sampleStandardList">检测样本标准</a>
					</li>
					<?php }?>
                </ul>
			
            </div><?php }} ?>