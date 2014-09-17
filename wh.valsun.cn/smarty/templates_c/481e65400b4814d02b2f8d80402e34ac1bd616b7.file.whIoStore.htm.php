<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 19:26:52
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\whIoStore.htm" */ ?>
<?php /*%%SmartyHeaderCode:2528653185b7c8a43e8-36502787%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '481e65400b4814d02b2f8d80402e34ac1bd616b7' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\whIoStore.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2528653185b7c8a43e8-36502787',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ioType' => 0,
    'g_ordersn' => 0,
    'ioSearchName' => 0,
    'invoiceTypeList' => 0,
    'value' => 0,
    'g_cStartTime' => 0,
    'g_cEndTime' => 0,
    'status' => 0,
    'InStoreLists' => 0,
    'InStoreList' => 0,
    'detailList' => 0,
    'auditlist' => 0,
    'audituserlist' => 0,
    'show_page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53185b7c9df157_47226703',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53185b7c9df157_47226703')) {function content_53185b7c9df157_47226703($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'E:\\erpNew\\wh.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('whNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/whIoStore.js"></script>
<script type="text/javascript" src="./js/fancybox.js"></script>
<script type="text/javascript" src="./js/My97DatePicker/WdatePicker.js"></script>
            <div class="servar wh-servar">
    				<span>
                        <input id="ioType" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ioType']->value;?>
"/>
                        <!-- TODO: <input id="userId" type="hidden" value="<?php echo $_SESSION['userId'];?>
"/> -->
    					<label for="ordersn">单据编码:</label>
    					<input name="ordersn" id="ordersn" value="<?php echo $_smarty_tpl->tpl_vars['g_ordersn']->value;?>
"/>
                        &nbsp;|&nbsp;
                        <label for="ioStatus">状态:</label>
                        <select name="ioStatus" id="ioStatus">
                            <option value='1' <?php if ($_GET['iostatus']==1){?>selected="selected"<?php }?>>待审核</option>
                            <option value='2' <?php if ($_GET['iostatus']==2){?>selected="selected"<?php }?>>审核通过</option>
                            <option value='3' <?php if ($_GET['iostatus']==3){?>selected="selected"<?php }?>>审核不通过</option>
                        </select>
                        &nbsp;|&nbsp;
                        <label for="invoiceTypeId"><?php echo $_smarty_tpl->tpl_vars['ioSearchName']->value;?>
：</label>
                        <select name="invoiceTypeId" id="invoiceTypeId">
                            <option value='0' <?php if ($_GET['select']==0){?>selected="selected"<?php }?>>请选择</option>
                            <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoiceTypeList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                            <option value='<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
' <?php if ($_GET['invoicetypeid']==$_smarty_tpl->tpl_vars['value']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['invoiceName'];?>
</option>
                            <?php } ?>
                        </select>
                        &nbsp;|&nbsp;
                        <label for="cStartTime">创建时间：</label>
                        <input onclick="WdatePicker()" id='cStartTime' name="cStartTime" value='<?php echo $_smarty_tpl->tpl_vars['g_cStartTime']->value;?>
'/>
                        &nbsp;至&nbsp;
                        <input onclick="WdatePicker()" id='cEndTime' name="cEndTime" value='<?php echo $_smarty_tpl->tpl_vars['g_cEndTime']->value;?>
' />
                        &nbsp;|&nbsp;
						<button id="search">搜索</button>
    				</span>
                    &nbsp;&nbsp;
                    <span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            </div>
            <div class="align-main main order-main products-main">
            	<?php  $_smarty_tpl->tpl_vars['InStoreList'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['InStoreList']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['InStoreLists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['InStoreList']->key => $_smarty_tpl->tpl_vars['InStoreList']->value){
$_smarty_tpl->tpl_vars['InStoreList']->_loop = true;
?>
                <table cellspacing="0" width="100%" class="products-action">
                    <tr class="title">
                        <td colspan="5">
                            <span>单号:<?php echo $_smarty_tpl->tpl_vars['InStoreList']->value['ordersn'];?>
</span>
                            <span>类型:<?php echo $_smarty_tpl->tpl_vars['InStoreList']->value['invoiceName'];?>
</span>
                            <span>申请人:<?php echo getUserNameById($_smarty_tpl->tpl_vars['InStoreList']->value['userId']);?>
</span>
                            <span>状态:<?php if ($_smarty_tpl->tpl_vars['InStoreList']->value['ioStatus']==1){?>待审核<?php }?><?php if ($_smarty_tpl->tpl_vars['InStoreList']->value['ioStatus']==2){?>审核通过<?php }?><?php if ($_smarty_tpl->tpl_vars['InStoreList']->value['ioStatus']==3){?>审核不通过<?php }?></span>
                            <span>提交时间:<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['InStoreList']->value['createdTime'],"Y-m-d H:i");?>
</span>
                            <span>付款方式:<?php echo $_smarty_tpl->tpl_vars['InStoreList']->value['paymentMethods'];?>
</span>
                            <span>仓库:<?php echo $_smarty_tpl->tpl_vars['InStoreList']->value['whName'];?>
</span>
							<span>备注:<?php echo $_smarty_tpl->tpl_vars['InStoreList']->value['note'];?>
</span>
                        </td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['detailList'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['detailList']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['InStoreList']->value['detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['detailList']->key => $_smarty_tpl->tpl_vars['detailList']->value){
$_smarty_tpl->tpl_vars['detailList']->_loop = true;
?>
                    <tr>
                        <td style="width:100px;" class="unpicurl">
							<a href="javascript:void(0)" id="imga-<?php echo $_smarty_tpl->tpl_vars['detailList']->value['sku'];?>
" class="fancybox">
								<img src="./images/ajax-loader.gif" name="skuimg" width="50" height="50" id="imgs-<?php echo $_smarty_tpl->tpl_vars['detailList']->value['sku'];?>
" spu="<?php echo $_smarty_tpl->tpl_vars['detailList']->value['spu'];?>
">
						   </a>
						</td>
                        <td width="30%"><?php echo getSKUName($_smarty_tpl->tpl_vars['detailList']->value['sku']);?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['detailList']->value['sku'];?>
</td>
                        <td>数量:<?php echo $_smarty_tpl->tpl_vars['detailList']->value['amount'];?>
</td>
                        <td>单价:<?php echo $_smarty_tpl->tpl_vars['detailList']->value['cost'];?>
 </td>
                    </tr>
                    <?php }
if (!$_smarty_tpl->tpl_vars['detailList']->_loop) {
?>
                    <tr>
                        <td colspan="5" style="width:100px;">
                        	无SKU数据
                        </td>
                    </tr>
                	<?php } ?>
                    <tr>
                        <td colspan="5">
                            <table>
                                <tr>
                                	<?php  $_smarty_tpl->tpl_vars['auditlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['auditlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['InStoreList']->value['auditlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['auditlist']->key => $_smarty_tpl->tpl_vars['auditlist']->value){
$_smarty_tpl->tpl_vars['auditlist']->_loop = true;
?>
                                    <?php if ($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']){?>
                                    <td valign="middle" class="<?php if ($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']['auditStatus']==1){?>font-pass<?php }?><?php if ($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']['auditStatus']==2){?>font-red<?php }?>"><?php echo getUserNameById($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']['auditUser']);?>
</td>
                                    <td class="pass" style="width:120px;padding-bottom:40px;" valign="middle" align="center">
                                        <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']['auditTime'],"Y-m-d H:i");?>

                                    </td>
                                    <?php if ($_smarty_tpl->tpl_vars['auditlist']->value['auditinfo']['auditStatus']==2){?>
                                    <td valign="middle" class="unpassendding"></td>
                                    <?php break 1?>
                                    <?php }?>
                                    <?php }else{ ?>
                                    <td valign="middle">
                                        <ul>
                                        <?php  $_smarty_tpl->tpl_vars['audituserlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['audituserlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['auditlist']->value['audituserlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['audituserlist']->key => $_smarty_tpl->tpl_vars['audituserlist']->value){
$_smarty_tpl->tpl_vars['audituserlist']->_loop = true;
?>
                                            <li class="font-unpass" ><?php echo getUserNameById($_smarty_tpl->tpl_vars['audituserlist']->value['auditorId']);?>
</li>
                                        <?php } ?>
                                        </ul>
                                    </td>
                                    <td class="unpass" style="width:120px;padding-bottom:37px;" valign="middle" align="center">
                                    <?php }?>
                                    <?php } ?>
                                    </td>
                                    <?php if ($_smarty_tpl->tpl_vars['InStoreList']->value['ioStatus']==2){?>
                                    <td valign="middle" class="passendding">
									<?php }?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <?php }
if (!$_smarty_tpl->tpl_vars['InStoreList']->_loop) {
?>
                	<div align="center">无数据</div>
                <?php } ?>
</div>

            <div class="bottomvar">
            	<div class="texvar">
            	</div>
            	<div class="pagination">
                	<?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript">
//页面加载完成后加载图片

$(document).ready(function() {
	var url  = "json.php?mod=common&act=getSkuImg";
	var skuArr	= $('img[name="skuimg"]'), imgurl="", spu="", sku="";
	$.each(skuArr,function(i,item){
		sku	= $(item).attr('id').substring(5);
		spu	= $(item).attr('spu');
		$.ajax({
			url: url,
			type: "POST",
			async: true,
			data	: {spu:spu,sku:sku},
			dataType: "jsonp",
			success: function(rtn){
							sku	= $(item).attr('id').substring(5);
							//console.log(rtn);
							if ($.trim(rtn.data)) {
								$("#imgs-"+sku).attr({"src":rtn.data,"width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href",rtn.data);
							} else {
								$("#imgs-"+sku).attr({"src":"./images/no_image.gif","width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href","./images/no_image.gif");
							}
				}	
			});
	});
});

</script><?php }} ?>