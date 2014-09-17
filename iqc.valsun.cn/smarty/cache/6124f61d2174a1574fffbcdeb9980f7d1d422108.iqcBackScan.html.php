<?php /*%%SmartyHeaderCode:3040551ff5d18db2503-29468052%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6124f61d2174a1574fffbcdeb9980f7d1d422108' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcBackScan.html',
      1 => 1375685887,
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
  'nocache_hash' => '3040551ff5d18db2503-29468052',
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5d18dfdfc3_34496961',
  'cache_lifetime' => 120,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5d18dfdfc3_34496961')) {function content_51ff5d18dfdfc3_34496961($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>iqc退件处理--iqc管理系统</title>
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
                    	<a href="index.php?mod=iqcDetect&act=iqcScan">IQC检测</a>
                    </li>
                    <li>
                    	<a href="index.php?mod=iqcDetect&act=backScan">IQC退件处理</a>
                    </li>
					<li>
                    	<a href="index.php?mod=iqcDetect&act=stockScan">库存不良品处理</a>
                    </li>
					                </ul>
			
            </div>
<div class="fourvar">
            <div class="pathvar">
	您的位置：
	        <span><a href='index.php?mod=iqcDetect&act=iqcScan'>iqc检测</a></span>
            <span>>></span>
            <span>iqc退件处理</span>
    </div>    
            </div>
            <div class="servar">
            	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td width="24%">
						  <div style="font-size:24px">检测料号:
				            <input name="sku" type="text" id="sku" onkeydown="checksku()" style="width:150px;height:30px;" class="textinput" />
				          </div>
						  <div id="mstatus" style="font-size:24px"></div>						</td>
					    <td width="76%">&nbsp;</td>
				      </tr>
					</table>
				 </td>
			   </tr>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td width="24%">
						  <div style="font-size:24px">全检数目:
				            <input name="check_num" type="text" id="check_num" onkeydown="checknum()" style="width:150px;height:30px;" class="textinput" />
				         </div>					</td>
			            <td width="76%"><div style="font-size:10px">注意：这里的全检数目就是退回总数，需全测。</div>&nbsp;</td>
				      </tr>
					</table>
				 </td>
			   </tr>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td>
						  <div style="font-size:24px">不良品数:
				            <input name="rejects_num" type="text" id="rejects_num" onkeydown="check_rejects_num()" style="width:150px;height:30px;" class="textinput" />							
                            状态:
						  <select name="rejects_status" id="rejects_status" onkeydown="check_rejects_status()">
						    <option value="0">请选择</option>
						    <option value="1">待退回</option>
						    <option value="2">待定</option>
						    </select>
						  </div>						</td>
			          </tr>
					</table>
				 </td>
			   </tr>			   
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons'>
				    <table width="100%" border="0" align="center">
				      <tr>
				        <td valign="top">
						  <div style="font-size:24px">不良原因:
				            <textarea name="bad_reason" class="textinput" id="bad_reason" style="width:350px;height:100px;" onkeydown="check_bad_reason()"></textarea>
				          </div>						</td>
			          </tr>
					</table>
				 </td>
			   </tr>

			</table>
            </div>
            <div class="main">
            	
            </div>
<div class="footer">
        		<p>版权所有Copyright©深圳市赛维网络科技有限公司 粤IPC备12055809</p>
        	</div>
        </div>
    </div>
</body>
</html><?php }} ?>