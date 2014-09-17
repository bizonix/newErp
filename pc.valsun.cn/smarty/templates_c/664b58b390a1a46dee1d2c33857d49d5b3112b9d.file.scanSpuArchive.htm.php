<?php /* Smarty version Smarty-3.1.12, created on 2013-10-23 09:25:35
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\scanSpuArchive.htm" */ ?>
<?php /*%%SmartyHeaderCode:3000452663fb07f1227-47207000%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '664b58b390a1a46dee1d2c33857d49d5b3112b9d' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\scanSpuArchive.htm',
      1 => 1382491408,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3000452663fb07f1227-47207000',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52663fb09c1171_48129952',
  'variables' => 
  array (
    'spu' => 0,
    'pid' => 0,
    'spuName' => 0,
    'referMonthSales' => 0,
    'spuCalWeight' => 0,
    'spuPurchasePrice' => 0,
    'spuLowestPrice' => 0,
    'isPacking' => 0,
    'value' => 0,
    'pmId' => 0,
    'spuNote' => 0,
    'spuStatus' => 0,
    'spuLength' => 0,
    'spuWidth' => 0,
    'spuHeight' => 0,
    'lowestUrl' => 0,
    'bidUrl' => 0,
    'Link' => 0,
    'link' => 0,
    'valuePP' => 0,
    'valuePPV1' => 0,
    'PPV' => 0,
    'ppv' => 0,
    'valuePPV2' => 0,
    'valueIN' => 0,
    'INV' => 0,
    'inv' => 0,
    'auditStatus' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52663fb09c1171_48129952')) {function content_52663fb09c1171_48129952($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
                <div class="texvar">
                </div>
                <div class="pagination">
                </div>
            </div>
			<div class="servar products-servar">
			<span style="color: red;" id="error"></span>
            </div>
            <div class="main products-main basic-main">
            	<table cellspacing="0" width="100%" id="tableBas">
                	<tr class="title">
                    	<td align="left" colspan="5" style="font-size:16px; font-weight:bold; padding-left:15px;">基本信息</td>
                    </tr>
                    <tr>
                    	<td class="products-action" rowspan="100" style="width:450px;">
						<img src="css/images/Testera.jpg" width="338" height="288" />
                    	</td>
                        <td width="8%"><span style="color:#F00;">*</span>SPU</td>
                        <td width="15%">
                            <input value="<?php echo $_smarty_tpl->tpl_vars['spu']->value;?>
" disabled="disabled"/>
							<input name="spu" id="spu" value="<?php echo $_smarty_tpl->tpl_vars['spu']->value;?>
" type="hidden"/>
                        </td>
                        <td style="width:90px;"><span style="color:#F00;">*</span>产品类别</td>
                        <td>
                            <input value="<?php echo getAllCateNameByPath($_smarty_tpl->tpl_vars['pid']->value);?>
" disabled="disabled"/>
							<input name="pid" id="pid" value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
" type="hidden"/>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>描述</td>
                        <td colspan="3"><textarea cols="50" rows="3" name="spuName" disabled="disabled"><?php echo $_smarty_tpl->tpl_vars['spuName']->value;?>
</textarea></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>参考月销量</td>
                        <td><input name="referMonthSales" id="referMonthSales" value="<?php echo $_smarty_tpl->tpl_vars['referMonthSales']->value;?>
"  disabled="disabled"/></td>
                        <td><span style="color:#F00;">*</span>估算重量</td>
                        <td><input name="spuCalWeight" id="spuCalWeight" value="<?php echo $_smarty_tpl->tpl_vars['spuCalWeight']->value;?>
"  disabled="disabled"/></td>
                    </tr>
                    <tr>

                        <td><span style="color:#F00;">*</span>采购价</td>
                        <td><input name="spuPurchasePrice" id="spuPurchasePrice" value="<?php echo $_smarty_tpl->tpl_vars['spuPurchasePrice']->value;?>
"  disabled="disabled"/></td>
                        <td><span style="color:#F00;">*</span>市场最低价</td>
                        <td><input name="spuLowestPrice" id="spuLowestPrice" value="<?php echo $_smarty_tpl->tpl_vars['spuLowestPrice']->value;?>
"  disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>是否带包装</td>
                        <td>
                            <select name="isPacking" id="isPacking" disabled="disabled">
                            	<option value="1" <?php if ($_smarty_tpl->tpl_vars['isPacking']->value==1){?>selected='selected'<?php }?>>不带包装</option>
                            	<option value="2" <?php if ($_smarty_tpl->tpl_vars['isPacking']->value==2){?>selected='selected'<?php }?>>带包装</option>
                            </select>
                        </td>
                        <td><span style="color:#F00;"></span>包材</td>
                        <td>
                            <select name="pmId" id="pmId" disabled="disabled">
                                <option value="0"></option>
                                <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = OmAvailableModel::getTNameList('pc_packing_material','id,pmName',"WHERE is_delete=0"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                            	<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['pmId']->value==$_smarty_tpl->tpl_vars['value']->value['id']){?>selected='selected'<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['pmName'];?>
</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>备注</td>
                        <td><input name="spuNote" id="spuNote" value="<?php echo $_smarty_tpl->tpl_vars['spuNote']->value;?>
" disabled="disabled"/></td>
                        <td><span style="color:#F00;"></span>状态</td>
                        <td>
                            <select name="spuStatus" id="spuStatus" disabled="disabled">
                            	<option value="1" <?php if ($_smarty_tpl->tpl_vars['spuStatus']->value==1){?>selected='selected'<?php }?>>下线</option>
                            	<option value="2" <?php if ($_smarty_tpl->tpl_vars['spuStatus']->value==2){?>selected='selected'<?php }?>>上线</option>
                            	<option value="3" <?php if ($_smarty_tpl->tpl_vars['spuStatus']->value==3){?>selected='selected'<?php }?>>部分停售</option>
                            	<option value="4" <?php if ($_smarty_tpl->tpl_vars['spuStatus']->value==4){?>selected='selected'<?php }?>>停售</option>
                            	<option value="5" <?php if ($_smarty_tpl->tpl_vars['spuStatus']->value==5){?>selected='selected'<?php }?>>部分下线</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>长</td>
                        <td><input name="spuLength" id="spuLength" value="<?php echo $_smarty_tpl->tpl_vars['spuLength']->value;?>
"  disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>宽</td>
                        <td><input name="spuWidth" id="spuWidth" value="<?php echo $_smarty_tpl->tpl_vars['spuWidth']->value;?>
"  disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>高</td>
                        <td><input name="spuHeight" id="spuHeight" value="<?php echo $_smarty_tpl->tpl_vars['spuHeight']->value;?>
"  disabled="disabled"/></td>
                    </tr>
                    <tr><td><span style='color:#F00;'>*</span>参考网页：</td><td><input  disabled="disabled" name='lowestUrl' id='lowestUrl' value="<?php echo $_smarty_tpl->tpl_vars['lowestUrl']->value;?>
"/></td> <td class="products-action"><input id="goLinkUrl" type="button" value="GO" style="width: 45px;" onclick="window.open('http://<?php echo $_smarty_tpl->tpl_vars['lowestUrl']->value;?>
')"/></td><td><input value="最低价" disabled="disabled"/></td></tr>
                    <tr><td><span style='color:#F00;'>*</span>参考网页：</td><td><input  disabled="disabled" name='bidUrl' id='bidUrl' value="<?php echo $_smarty_tpl->tpl_vars['bidUrl']->value;?>
"/></td> <td class="products-action"><input id="goLinkUrl" type="button" value="GO" style="width: 45px;" onclick="window.open('http://<?php echo $_smarty_tpl->tpl_vars['bidUrl']->value;?>
')"/></td><td><input value="竞价listing" disabled="disabled"/></td></tr>
                    <?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['Link']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?>
                    <tr><td><span style='color:#F00;'>&nbsp;</span>参考网页：</td><td><input disabled="disabled" name='linkUrl[]' id='linkUrl' value="<?php echo $_smarty_tpl->tpl_vars['link']->value['linkUrl'];?>
"/></td> <td class="products-action"><input id="goLinkUrl" type="button" value="GO" style="width: 45px;" onclick="window.open('http://<?php echo $_smarty_tpl->tpl_vars['link']->value['linkUrl'];?>
')"/></td><td><input  disabled="disabled" name='linkNote[]' id='linkNote' value="<?php echo $_smarty_tpl->tpl_vars['link']->value['linkNote'];?>
"/></td></tr>
                    <?php } ?>
                </table>
                <table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td align="left" colspan="8" style="font-size:16px; font-weight:bold; padding-left:15px;">档案信息</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['valuePP'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['valuePP']->_loop = false;
 $_from = OmAvailableModel::getTNameList('pc_archive_property','*',"WHERE categoryPath IN (".((string)$_smarty_tpl->tpl_vars['pathImplodeStr']->value).")"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['valuePP']->key => $_smarty_tpl->tpl_vars['valuePP']->value){
$_smarty_tpl->tpl_vars['valuePP']->_loop = true;
?>
						<tr>
                            <td style="width: 40px; padding-left:20px;">
                            <?php echo $_smarty_tpl->tpl_vars['valuePP']->value['propertyName'];?>

                            </td>
                            <?php if ($_smarty_tpl->tpl_vars['valuePP']->value['isRadio']==1){?>
                                <td>
                                <select name="pro<?php echo $_smarty_tpl->tpl_vars['valuePP']->value['id'];?>
" id="pro<?php echo $_smarty_tpl->tpl_vars['valuePP']->value['id'];?>
"  disabled="disabled">
                                    <option value="0"></option>
                                    <?php  $_smarty_tpl->tpl_vars['valuePPV1'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['valuePPV1']->_loop = false;
 $_from = OmAvailableModel::getTNameList('pc_archive_property_value','*',"WHERE propertyId='".((string)$_smarty_tpl->tpl_vars['valuePP']->value['id'])."'"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['valuePPV1']->key => $_smarty_tpl->tpl_vars['valuePPV1']->value){
$_smarty_tpl->tpl_vars['valuePPV1']->_loop = true;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['valuePPV1']->value['id'];?>
" <?php  $_smarty_tpl->tpl_vars['ppv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ppv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PPV']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ppv']->key => $_smarty_tpl->tpl_vars['ppv']->value){
$_smarty_tpl->tpl_vars['ppv']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['ppv']->value['propertyId']==$_smarty_tpl->tpl_vars['valuePP']->value['id']&&$_smarty_tpl->tpl_vars['ppv']->value['propertyValueId']==$_smarty_tpl->tpl_vars['valuePPV1']->value['id']){?>selected='selected'<?php }?><?php } ?>><?php echo $_smarty_tpl->tpl_vars['valuePPV1']->value['propertyValue'];?>
</option>
                                    <?php } ?>
                                </select>
                                </td>
                            <?php }else{ ?>
                                <td>
                                <?php  $_smarty_tpl->tpl_vars['valuePPV2'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['valuePPV2']->_loop = false;
 $_from = OmAvailableModel::getTNameList('pc_archive_property_value','*',"WHERE propertyId='".((string)$_smarty_tpl->tpl_vars['valuePP']->value['id'])."'"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['valuePPV2']->key => $_smarty_tpl->tpl_vars['valuePPV2']->value){
$_smarty_tpl->tpl_vars['valuePPV2']->_loop = true;
?>
                                    <input disabled="disabled" style="width: 12px;" value="<?php echo $_smarty_tpl->tpl_vars['valuePPV2']->value['id'];?>
" name="pro<?php echo $_smarty_tpl->tpl_vars['valuePP']->value['id'];?>
[]" type="checkbox" id="pro<?php echo $_smarty_tpl->tpl_vars['valuePPV2']->value['id'];?>
" <?php  $_smarty_tpl->tpl_vars['ppv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ppv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['PPV']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ppv']->key => $_smarty_tpl->tpl_vars['ppv']->value){
$_smarty_tpl->tpl_vars['ppv']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['ppv']->value['propertyId']==$_smarty_tpl->tpl_vars['valuePP']->value['id']&&$_smarty_tpl->tpl_vars['ppv']->value['propertyValueId']==$_smarty_tpl->tpl_vars['valuePPV2']->value['id']){?>checked='checked'<?php }?><?php } ?>/>

                                    <label for="pro<?php echo $_smarty_tpl->tpl_vars['valuePPV2']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['valuePPV2']->value['propertyValue'];?>
</label>

                                <?php } ?>
                                </td>
                            <?php }?>

						</tr>
                    <?php } ?>
                </table>
                <table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td align="left" colspan="2" style="font-size:16px; font-weight:bold; padding-left:15px;">主观描述</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['valueIN'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['valueIN']->_loop = false;
 $_from = OmAvailableModel::getTNameList('pc_archive_input','*',"WHERE categoryPath IN (".((string)$_smarty_tpl->tpl_vars['pathImplodeStr']->value).")"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['valueIN']->key => $_smarty_tpl->tpl_vars['valueIN']->value){
$_smarty_tpl->tpl_vars['valueIN']->_loop = true;
?>
						<tr>
                            <td valign="top" width="6%" style="padding-left:20px;">
                                <?php echo $_smarty_tpl->tpl_vars['valueIN']->value['inputName'];?>

                            </td>
                            <td>
                                <textarea  disabled="disabled" cols="120" rows="2" name="inp<?php echo $_smarty_tpl->tpl_vars['valueIN']->value['id'];?>
"><?php  $_smarty_tpl->tpl_vars['inv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['inv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['INV']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['inv']->key => $_smarty_tpl->tpl_vars['inv']->value){
$_smarty_tpl->tpl_vars['inv']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['inv']->value['inputId']==$_smarty_tpl->tpl_vars['valueIN']->value['id']){?><?php echo $_smarty_tpl->tpl_vars['inv']->value['inputValue'];?>
<?php }?><?php } ?></textarea>
							</td>
						</tr>
                    <?php } ?>

                </table>
                <div align="center" class="products-action">
					<?php if ($_smarty_tpl->tpl_vars['auditStatus']->value==1){?>
					<input type="button" value="审核通过" id="audit2"/>
					<input type="button" value="审核不通过" id="audit3"/>
					<?php }?>
					<input type="button" value="返回" id="back"/>
				</div>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>