<?php /*%%SmartyHeaderCode:3211451ff5ba016bfb6-23533698%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59cd45a8aa54d7374799a6e9ed94e95771be16dd' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcScanList.html',
      1 => 1375686213,
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
  'nocache_hash' => '3211451ff5ba016bfb6-23533698',
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5eabd76101_92984129',
  'has_nocache_code' => false,
  'cache_lifetime' => 120,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5eabd76101_92984129')) {function content_51ff5eabd76101_92984129($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
					                </ul>
			
            </div>
<div class="fourvar">
            	<div class="pathvar">
	您的位置：
	        <span><a href='index.php?mod=iqcInfo&act=iqcScanList'>iqc检测信息</a></span>
            <span>>></span>
            <span>iqc已检测信息</span>
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
                	查找：SKU:<input type="text" value="" style="" />
                </span>
                <span>
					状态：<select name="rejects_status" id="rejects_status" onkeydown="check_rejects_status()">
						    <option value="0" >请选择</option>
						    <option value="1" >待退回</option>
						    <option value="2" >待定</option>
						    </select>               	
                </span>
				<span>
					修改类型：<select name="edit_category" id="edit_category">
						    <option value="">请选择</option>
						    <option value="1" >修改图片</option>
						    <option value="2" >修改尺寸</option>
							<option value="3" >修改描述与其它</option>
							<option value="4" >所有修改信息</option>
						    </select>              	
                </span>
                <span>
                	 检测开始时间：<input type="text" value="" style="" />
					 结束时间： <input type="text" value="" style="" />
                </span>
				<br><br>
				<span>
					条件：A.是否合并：<input name="is_combine" id="is_combine" type="checkbox" value="1" />&nbsp;&nbsp;
						  B.不良品：<input name="is_bad" id="is_bad" type="checkbox" value="1" />             	
                </span>
				<br><br>
                <span>
                	<input type="button" id='' style="width:50px;height:35px;font-size:20px; cursor:pointer;" value="搜索" />
					<input type="button" id='' style="width:150px;height:35px;font-size:20px; cursor:pointer;" value="导出到ELS" />
                </span>
            </div>
            <div class="main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td>SKU</td>
                        <td>名称</td>
						<td>抽检数</td>
						<td>检测类别</td>
						<td>状态</td>
						<td>不良数</td>
						<td>到货数</td>
						<td>不良原因</td>
						<td>修改信息</td>
						<td>检测人</td>
						<td>检测时间</td>
						<td>采购审核状态</td>
						<td>采购已审核动作</td>
						<td>iqc审核状态</td>
						<td>iqc已审核动作</td>
                    </tr>
                    <tr class="odd">
                            <td></td>
                           
                    </tr>
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