<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:35
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcScanList.html" */ ?>
<?php /*%%SmartyHeaderCode:2846351ff594bd34797-94573591%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59cd45a8aa54d7374799a6e9ed94e95771be16dd' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcScanList.html',
      1 => 1375686213,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2846351ff594bd34797-94573591',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff594bd6d166_33686792',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff594bd6d166_33686792')) {function content_51ff594bd6d166_33686792($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
            	<?php echo $_smarty_tpl->getSubTemplate ("iqcnav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
   
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
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>