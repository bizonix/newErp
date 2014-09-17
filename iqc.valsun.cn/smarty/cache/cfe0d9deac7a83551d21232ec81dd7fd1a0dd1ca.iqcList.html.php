<?php /*%%SmartyHeaderCode:1234351ff592eb57367-44585434%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfe0d9deac7a83551d21232ec81dd7fd1a0dd1ca' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcList.html',
      1 => 1375684975,
      2 => 'file',
    ),
    '4a8667d7f6507ac021f532a405ebe1ce260f1fd9' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\header.html',
      1 => 1375684319,
      2 => 'file',
    ),
    'd147caae16e2e3634cfe17c607fb131f13b714ab' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcnav.html',
      1 => 1375684602,
      2 => 'file',
    ),
    'dd751c181d45aee90a2be8bbfe20ce948b36fbb6' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\footer.html',
      1 => 1375171425,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1234351ff592eb57367-44585434',
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5eae485513_93667798',
  'has_nocache_code' => false,
  'cache_lifetime' => 120,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5eae485513_93667798')) {function content_51ff5eae485513_93667798($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SKU等待领取--iqc管理系统</title>
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
					<a href="index.php?mod=login&act=logout">hws 退出</a>
                </div>
            </div>
            <div class="twovar">
			
            	<ul>
					                	<li>
                    	<a href="index.php?mod=iqc&act=iqcList">等待领取SKU</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=iqc&act=iqcWaitCheck">等待检测SKU</a>
                    </li>
					                </ul>
			
            </div>
<div class="fourvar">
            	<div class="pathvar">
	您的位置：
	        <span><a href='index.php?mod=iqc&act=iqcList'>iqc检测领取</a></span>
            <span>>></span>
            <span>等待领取</span>
    </div>
                <div class="texvar">
                    <input name="" type="text" size="3" />
                    <a href="#" class="enter">GO</a>
                </div>
                <div class="pagination">
                	<ul>
                    	<li>
                        	<a href="#">上一页</a>
                        </li>
                        <li>
                        	<a href="#">1</a>
                        </li>
                        <li>
                        	<a href="#">2</a>
                        </li>
                        <li>
                        	<a href="#">3</a>
                        </li>
                        <li>
                        	<a href="#">下一页</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="servar">
            	<span>
                	批量搜索：<input type="text" value="" style="width:150px;height:30px;font-size:20px;" />
                </span>
                <span>
                	<input type="button" id='' style="width:50px;height:35px;font-size:20px; cursor:pointer;" value="搜索" />
                </span>
                <span>
                	<input type="button" id='' style="width:50px;height:35px;font-size:20px; cursor:pointer;" value="领取" />
                </span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>
                	<input type="button" id='' style="width:100px;height:35px;font-size:20px; cursor:pointer;" value="异常删除" />
                </span>
            </div>
            <div class="main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td><input type="checkbox" class="checkall" /></td>
                        <td>料号</td>
						<td>到货数</td>
						<td>录入时间</td>
						<td>打印人员</td>
						<td>描 述</td>
						<td>采 购</td>
						<td>仓 位</td>
						<td>IQC领货人</td>
                    </tr>
					
                    <tr class="odd">
                            <td><input type="checkbox" name="carrierName" value=""/></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                    </tr>
                <!-- END list -->
                </table>
            </div>
            <div class="bottomvar">
            	<div class="texvar">
                	<input name="" type="text" size="3" />
                	<a href="#" class="enter">GO</a>
            	</div>
            	<div class="pagination">
                	<ul>
                    	<li>
                        	<a href="#">上一页</a>
                    	</li>
                    	<li>
                        	<a href="#">1</a>
                    	</li>
                    	<li>
                        	<a href="#">2</a>
                    	</li>
                    	<li>
                        	<a href="#">3</a>
                    	</li>
                    	<li>
                        	<a href="#">下一页</a>
                    	</li>
                	</ul>
            	</div>
            </div>
<div class="footer">
        		<p>版权所有Copyright©深圳市赛维网络科技有限公司 粤IPC备12055809</p>
        	</div>
        </div>
    </div>
</body>
</html><?php }} ?>