<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:32
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcWaitCheck.html" */ ?>
<?php /*%%SmartyHeaderCode:361651ff5948b43007-03388513%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7a36e7d0f5d9867c9c0e228bb0c037390f9c76a' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcWaitCheck.html',
      1 => 1375685505,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '361651ff5948b43007-03388513',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5948b7aed6_86273340',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5948b7aed6_86273340')) {function content_51ff5948b7aed6_86273340($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
                	领错退回：<input type="button" id="" style="width:80px;height:35px;font-size:20px; cursor:pointer;" value="退回" />
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