<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 16:47:24
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\header.htm" */ ?>
<?php /*%%SmartyHeaderCode:8995265dbdfb91867-94935541%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b807cf6ab4d7a9b16a7c4a57ee74f4bac436e589' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\header.htm',
      1 => 1382431536,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8995265dbdfb91867-94935541',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265dbdfc67380_36603587',
  'variables' => 
  array (
    'title' => 0,
    'onevar' => 0,
    'username' => 0,
    'twovar' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5265dbdfc67380_36603587')) {function content_5265dbdfc67380_36603587($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<link href="http://misc.erp.valsun.cn/css/style.css" rel="stylesheet" type="text/css" />
<link href="http://misc.erp.valsun.cn/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="./js/easyTooltip.js"></script>
<script type="text/javascript" src="./js/ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="./js/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="./js/hoverIntent.js"></script>
<script type="text/javascript" src="./js/superfish.js"></script>
<script type="text/javascript" src="./js/custom.js"></script>
<script src="./js/jquery.validationEngine-zh_CN.js" type="text/javascript" charset="utf-8"></script>
<script src="./js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<!--<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.9.2.custom.min.css" />-->
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
</head>

<body>
	<div class="container">
    	<div class="content">
        	<div class="header">
            	<div class="logo">
                	赛维网络
                </div>
                <div class="onevar">
                	<ul>
                    	<li>
                        	<a href="index.php?mod=goods&act=getGoodsList" <?php if ($_smarty_tpl->tpl_vars['onevar']->value==1){?>class="cho"<?php }?>>产品信息</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList" <?php if ($_smarty_tpl->tpl_vars['onevar']->value==2){?>class="cho"<?php }?>>SPU管理</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=packingMaterials&act=getPmList" <?php if ($_smarty_tpl->tpl_vars['onevar']->value==3){?>class="cho"<?php }?>>包材管理</a>
                        </li>
                        <li>
                        	<a href="index.php?mod=category&act=getCategoryList" <?php if ($_smarty_tpl->tpl_vars['onevar']->value==4){?>class="cho"<?php }?>>类别管理</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                	<a href="#" onclick="myFunction"><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</a>
                    <a href="index.php?mod=login&amp;act=logout" onclick="myFunction" style="background-image:none;">退出</a>
                </div>
            </div>
            <div class="twovar">
            	<ul>
            		<?php if ($_smarty_tpl->tpl_vars['onevar']->value==1){?>
                	<li>
                    	<a href="index.php?mod=goods&act=getGoodsList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==11){?>class="cho"<?php }?>>产品信息</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=goods&act=getCombineList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==14){?>class="cho"<?php }?>>虚拟料号管理</a>
                    </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['onevar']->value==2){?>
                    <li>
                    	<a href="index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==21){?>class="cho"<?php }?>>自动生成SPU</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=autoCreateSpu&act=getAutoCreateSpuList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==22){?>class="cho"<?php }?>>生成SPU列表管理</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=autoCreateSpu&act=getSpuArchiveList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==23){?>class="cho"<?php }?>>SPU档案管理</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=spu&act=getSpuPrefixList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==24){?>class="cho"<?php }?>>SPU自动生成前缀管理</a>
                    </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['onevar']->value==3){?>
                    <li>
                    	<a href="index.php?mod=packingMaterials&act=getPmList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==31){?>class="cho"<?php }?>>包材管理</a>
                    </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['onevar']->value==4){?>
                    <li>
                    	<a href="index.php?mod=category&act=getCategoryList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==41){?>class="cho"<?php }?>>产品类别管理</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=property&act=getPropertyList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==42){?>class="cho"<?php }?>>产品选择属性管理</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=property&act=getInputList" <?php if ($_smarty_tpl->tpl_vars['twovar']->value==43){?>class="cho"<?php }?>>产品文本属性管理</a>
                    </li>
					<?php }?>
                </ul>
            </div>
		    <!--div class="threevar">
            	<ul>
                	<li>
                    	<a href="#" class="cho">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                    <li>
                    	<a href="#">三级导航</a>
                    </li>
                </ul>
            </div-->
<?php }} ?>