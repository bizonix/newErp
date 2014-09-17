<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:46:25
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updateSkuSing.htm" */ ?>
<?php /*%%SmartyHeaderCode:2547452664c59f2c657-41227755%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd2b28065658d71dcde92f6228cbfecbc0625c3a2' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updateSkuSing.htm',
      1 => 1382438781,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2547452664c59f2c657-41227755',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52664c5a1dade7_75103504',
  'variables' => 
  array (
    'spu' => 0,
    'pid' => 0,
    'sku' => 0,
    'id' => 0,
    'goodsName' => 0,
    'goodsCost' => 0,
    'goodsWeight' => 0,
    'value' => 0,
    'pmId' => 0,
    'pmCapacity' => 0,
    'goodsNote' => 0,
    'goodsStatus' => 0,
    'isNew' => 0,
    'isPacking' => 0,
    'purchaseId' => 0,
    'goodsColor' => 0,
    'goodsSize' => 0,
    'goodsLength' => 0,
    'goodsWidth' => 0,
    'goodsHeight' => 0,
    'pathImplodeStr' => 0,
    'valuePP' => 0,
    'valuePPV1' => 0,
    'PPV' => 0,
    'ppv' => 0,
    'valuePPV2' => 0,
    'valueIN' => 0,
    'INV' => 0,
    'inv' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52664c5a1dade7_75103504')) {function content_52664c5a1dade7_75103504($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
            <form action="index.php?mod=goods&act=updateSkuSingOn" method="post" id="SpuArchiveValidation">
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%" id="tableBas">
                	<tr class="title">
                    	<td align="left" colspan="5" style="font-size:16px; font-weight:bold; padding-left:15px;">基本信息</td>
                    </tr>
                    <tr>
                    	<td class="products-action" rowspan="100" style="width:450px;">

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
                        <td><span style="color:#F00;">*</span>SKU</td>
                        <td><input class="validate[required]" name="sku" id="sku" value="<?php echo $_smarty_tpl->tpl_vars['sku']->value;?>
"/></td>
                        <input name="id" id="id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" type="hidden"/>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>描述</td>
                        <td colspan="3"><textarea class="validate[required]" cols="50" rows="3" name="goodsName"><?php echo $_smarty_tpl->tpl_vars['goodsName']->value;?>
</textarea></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>成本</td>
                        <td><input class="validate[required,custom[number],min[0.001]]" name="goodsCost" id="goodsCost" value="<?php echo $_smarty_tpl->tpl_vars['goodsCost']->value;?>
"/></td>
                        <td><span style="color:#F00;"></span>操作重量</td>
                        <td><input class="validate[option,custom[number],min[0.001]]" name="goodsWeight" id="goodsWeight" value="<?php echo $_smarty_tpl->tpl_vars['goodsWeight']->value;?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>包材</td>
                        <td>
                            <select name="pmId" id="pmId">
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
                        <td><span style="color:#F00;"></span>对应包材容量</td>
                        <td><input class="validate[option,custom[integer],min[1]]" name="pmCapacity" id="pmCapacity" value="<?php echo $_smarty_tpl->tpl_vars['pmCapacity']->value;?>
"/></td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>备注</td>
                        <td><input name="goodsNote" id="goodsNote" value="<?php echo $_smarty_tpl->tpl_vars['goodsNote']->value;?>
"/></td>
                        <td><span style="color:#F00;"></span>状态</td>
                        <td>
                            <select name="goodsStatus" id="goodsStatus">
                            	<option value="1" <?php if ($_smarty_tpl->tpl_vars['goodsStatus']->value==1){?>selected='selected'<?php }?>>零库存</option>
                            	<option value="2" <?php if ($_smarty_tpl->tpl_vars['goodsStatus']->value==2){?>selected='selected'<?php }?>>停售</option>
                            	<option value="3" <?php if ($_smarty_tpl->tpl_vars['goodsStatus']->value==3){?>selected='selected'<?php }?>>部分平台在线</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    <td><span style="color:#F00;">*</span>新/老品</td>
                        <td>
                            <select name="isNew" id="isNew">
                            	<option value="1" <?php if ($_smarty_tpl->tpl_vars['isNew']->value==1){?>selected='selected'<?php }?>>新品</option>
                            	<option value="0" <?php if ($_smarty_tpl->tpl_vars['isNew']->value==0){?>selected='selected'<?php }?>>老品</option>
                            </select>
                        </td>
                    <td><span style="color:#F00;">*</span>是否带包装</td>
                        <td>
                            <select name="isPacking" id="isPacking">
                            	<option value="1" <?php if ($_smarty_tpl->tpl_vars['isPacking']->value==1){?>selected='selected'<?php }?>>不带包装</option>
                            	<option value="2" <?php if ($_smarty_tpl->tpl_vars['isPacking']->value==2){?>selected='selected'<?php }?>>带包装</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;">*</span>供应商</td>
                        <td>
                            <select name="partnerId" id="partnerId" class="validate[required]">
                            <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = getParterInfoByPurchaseId($_smarty_tpl->tpl_vars['purchaseId']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                            	<?php if (!empty($_smarty_tpl->tpl_vars['value']->value)){?>
                            	<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['company_name'];?>
</option>
                            	<?php }?>
                            </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>颜色</td>
                        <td><input name="goodsColor" id="goodsColor" value="<?php echo $_smarty_tpl->tpl_vars['goodsColor']->value;?>
"/></td>
                        <td><span style="color:#F00;"></span>尺码</td>
                        <td><input name="goodsSize" id="goodsSize" value="<?php echo $_smarty_tpl->tpl_vars['goodsSize']->value;?>
"/></td>

                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>长</td>
                        <td><input class="validate[option,custom[number],min[0]]" name="goodsLength" id="goodsLength" value="<?php echo $_smarty_tpl->tpl_vars['goodsLength']->value;?>
"/></td>

                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>宽</td>
                        <td><input class="validate[option,custom[number],min[0]]" name="goodsWidth" id="goodsWidth" value="<?php echo $_smarty_tpl->tpl_vars['goodsWidth']->value;?>
"/></td>

                    </tr>
                    <tr>
                        <td><span style="color:#F00;"></span>高</td>
                        <td><input class="validate[option,custom[number],min[0]]" name="goodsHeight" id="goodsHeight" value="<?php echo $_smarty_tpl->tpl_vars['goodsHeight']->value;?>
"/></td>

                    </tr>
                </table>
                <table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td align="left" colspan="8" style="font-size:16px; font-weight:bold; padding-left:15px;">档案信息</td>
                    </tr>
                  <?php if ($_smarty_tpl->tpl_vars['pathImplodeStr']->value!=''){?>
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
(<?php echo $_smarty_tpl->tpl_vars['valuePPV2']->value['propertyValueShort'];?>
</label>

                                <?php } ?>
                                </td>
                            <?php }?>

						</tr>
                    <?php } ?>
                    <?php }?>
                </table>
                <table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td align="left" colspan="2" style="font-size:16px; font-weight:bold; padding-left:15px;">主观描述</td>
                    </tr>
                    <?php if ($_smarty_tpl->tpl_vars['pathImplodeStr']->value!=''){?>
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
                                <textarea disabled="disabled" cols="120" rows="2" name="inp<?php echo $_smarty_tpl->tpl_vars['valueIN']->value['id'];?>
"><?php  $_smarty_tpl->tpl_vars['inv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['inv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['INV']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['inv']->key => $_smarty_tpl->tpl_vars['inv']->value){
$_smarty_tpl->tpl_vars['inv']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['inv']->value['inputId']==$_smarty_tpl->tpl_vars['valueIN']->value['id']){?><?php echo $_smarty_tpl->tpl_vars['inv']->value['inputValue'];?>
<?php }?><?php } ?></textarea>
							</td>
						</tr>
                    <?php } ?>
                    <?php }?>
                </table>
                <div align="center" class="feedback-main">
                	<input type="submit" value="保存"/>
                    <input type="button" value="返回" id="back"/>
                </div>
            </div>
            </form>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>