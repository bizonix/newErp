<?php /* Smarty version Smarty-3.1.12, created on 2013-08-05 15:50:40
         compiled from "E:\xampp\htdocs\erpNew\iqc.valsun.cn\html\v1\iqcScan.html" */ ?>
<?php /*%%SmartyHeaderCode:2284451ff5950b7c6e3-05604265%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9a62eb36f7a15596242771f9bc694e4967dd72a8' => 
    array (
      0 => 'E:\\xampp\\htdocs\\erpNew\\iqc.valsun.cn\\html\\v1\\iqcScan.html',
      1 => 1375685866,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2284451ff5950b7c6e3-05604265',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51ff5950bceca4_03173432',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ff5950bceca4_03173432')) {function content_51ff5950bceca4_03173432($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
            <?php echo $_smarty_tpl->getSubTemplate ("iqcnav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
               
            </div>
            <div class="servar">
            	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
				<tr>
				  <td nowrap="nowrap" class='paginationActionButtons' width="27%">
				    <table width="50%" border="0" align="left">
                          <tr>
                            <td width="50%">
                              <div style="font-size:24px">检测料号:
                                <input name="sku" type="text" id="sku" onkeydown="checksku()" style="width:150px;height:30px;" class="textinput" />
                              </div>
                              <div id="mstatus" style="font-size:24px"></div>
                            </td> 
                          </tr>
                        <tr>
                            <td width="26%">
                              <div style="font-size:24px">检测类别:
                               <select id="category" name="category" onkeydown="check_category()" style="width:150px;height:30px;font-size:24px">
                               <option value="0">==请选择==</option>
                               <option value="1">功能检测</option>
                               <option value="2">量尺寸</option>
                               <option value="3">对图片</option>
                               </select>
                              </div>
                              <div id="mstatus1" style="font-size:24px"></div>
                            </td>
                        </tr>
                        <tr>
                        	<td width="26%">
                              <div style="font-size:24px">抽样数目:
                                <input name="check_num" type="text" id="check_num" onkeydown="checknum()" style="width:150px;height:30px;" class="textinput" />
                              </div>  
                              <div id="mstatus2" style="font-size:24px"></div>
							</td>
                        </tr>
                        <tr>
                            <td width="26%">
                              <div style="font-size:24px">不良品数:
                                <input name="rejects_num" type="text" id="rejects_num" onkeydown="check_rejects_num()" style="width:150px;height:30px;" class="textinput" />
                              状态:
                              <select name="rejects_status" id="rejects_status" onkeydown="check_rejects_status()">
                                <option value="0">请选择</option>
                                <option value="1">待退回</option>
                                <option value="2">待定</option>
                                </select>
                              </div>
                             <div id="mstatus3" style="font-size:24px"></div>
                            </td>
                        </tr>
                      <tr>
                            <td width="49%" valign="top">
                              <div style="font-size:24px">不良原因:
                                <textarea name="bad_reason" class="textinput" id="bad_reason" style="width:350px;height:100px;" onkeydown="check_bad_reason()"></textarea>
                              </div>
                            </td>
                      </tr>
					</table>
				 </td>
                 <td nowrap="nowrap" class='paginationActionButtons' width="27%">
                 	<table width="50%" border="0" align="left">
                    	<tr>
              				 <td>
                  				  <div id="sampling_display" style="display:none;"></div>
               				</td>
            			</tr>
                          <tr>
                            	<td width="50%"><span style="font-size:24px; color:#F00">是否修改图:
                            <input type="checkbox" name="is_rewrite" id="is_rewrite" value="1" onclick="" onkeydown="" checked="checked"/>
                        							</span>
                            	</td>
                          </tr>
                          <tr>
				        
                            <td width="27%" id="rewrite_typetd" style="display:block;"><span style="font-size:24px">修改类型:
                              <select name="rewrite_type" id="rewrite_type" onkeydown="check_rewrite_type()">
                                <option value="0">请选择</option>
                                <option value="1">修改图片</option>
                                <option value="2">修改尺寸</option>
                                <option value="3">修改描述与其他</option>
                              </select>
                            </span>
                            </td>
					  </tr>
                      
                        <tr>
                        	<td width="51%" valign="top" id="rewrite_notetd" style="display:block;"><span style="font-size:24px">修改备注:
                            <textarea name="rewrite_note" class="textinput" id="rewrite_note" style="width:350px;height:100px;" onkeydown="check_rewrite_note()"></textarea>
                        </span><input name="goods_reachid" id="goods_reachid" type="hidden" value="" />
                        	</td>
                        </tr>
                        
                        
                      
					</table>
                 
                 </td>
                 <td nowrap="nowrap" class='paginationActionButtons' width="46%">
                 	<table width="100%" border="0" align="left">
                    	  <tr>
                            	<td scope='row' align='left' valign="top" >
                                <div id="show_product_image" style="display:none;"></div>
                                </td>
                          </tr>      
					</table>
                 </td>
			   </tr>
			</table>
            </div>
            <div class="main">
            	
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>