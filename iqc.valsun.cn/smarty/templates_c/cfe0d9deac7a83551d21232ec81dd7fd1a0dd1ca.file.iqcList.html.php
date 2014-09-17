<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:28
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcList.html" */ ?>
<?php /*%%SmartyHeaderCode:635551ff5944a767a3-32062497%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfe0d9deac7a83551d21232ec81dd7fd1a0dd1ca' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcList.html',
      1 => 1375684975,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '635551ff5944a767a3-32062497',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5944ad7108_05806178',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5944ad7108_05806178')) {function content_51ff5944ad7108_05806178($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>