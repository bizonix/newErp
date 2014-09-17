<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:36:00
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\updatePackingMaterials.htm" */ ?>
<?php /*%%SmartyHeaderCode:33945266158da39750-90302926%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '516d72a54798dbe9189531f71b9246b781101e45' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\updatePackingMaterials.htm',
      1 => 1382430477,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '33945266158da39750-90302926',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5266158dac0e16_18555145',
  'variables' => 
  array (
    'pmList' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5266158dac0e16_18555145')) {function content_5266158dac0e16_18555145($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/pm.js"></script>
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

            </div>
            <div class="main products-main">
            	<form id="addform" name="addform" method="post" action="index.php?mod=packingMaterials&act=updatePmOn" onsubmit="return submitUpdate()">
							<table  width="100%" cellpadding="0">
								<input name="id" type="hidden" id="id" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['id'];?>
"/>
                                <tbody>
                                    <tr class="odd">
										<td align="right">包材名</td>
										<td align="left" style="padding-left: 30px;"><input name="pmName" type="text" id="pmName" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmName'];?>
"/><span id="pmNameSpan" style="color: red;">*</span></td>
									</tr>
									<tr class="odd">
										<td align="right">包材别名</td>
										<td align="left" style="padding-left: 30px;"><input name="pmAlias" type="text" id="pmAlias" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmAlias'];?>
"/><span id="pmAliasSpan" style="color: red;">*</span></td>

									</tr>

									<tr class="odd">
										<td align="right">成本(CNY)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmCost" type="text" id="pmCost" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmCost'];?>
"/><span id="pmCostSpan" style="color: red;">*</span></td>

									</tr>
                                    <tr class="odd">
										<td align="right">长度(m)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmLength" type="text" id="pmLength" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmLength'];?>
"/><span id="pmLengthSpan" style="color: red;">*</span></td>

                                    </tr>
									<tr class="odd">
										<td align="right">宽度(m)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmWidth" type="text" id="pmWidth" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmWidth'];?>
"/><span id="pmWidthSpan" style="color: red;">*</span></td>

									</tr>
									<tr class="odd">
										<td align="right">高度(m)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmHeight" type="text" id="pmHeight" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmHeight'];?>
"/><span id="pmHeightSpan" style="color: red;">*</span></td>

									</tr>
									<tr class="odd">
										<td align="right">重量(kg)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmWeight" type="text" id="pmWeight" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmWeight'];?>
"/><span id="pmWeightSpan" style="color: red;">*</span></td>

									</tr>
                                    <tr class="odd">
										<td align="right">容积(m<sup>3</sup>)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmDimension" type="text" id="pmDimension" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmDimension'];?>
"/><span id="pmDimensionSpan" style="color: red;">*</span></td>

									</tr>
                                    <tr class="odd">
										<td align="right">包材比除数(包材类名)</td>
										<td align="left" style="padding-left: 30px;"><input name="pmDivider" type="text" id="pmDivider" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmDivider'];?>
"/><span id="pmDividerSpan" style="color: red;"></span></td>

									</tr>
                                    <tr class="odd">
										<td align="right">比值（本包材容积/包材比除数容积）</td>
										<td align="left" style="padding-left: 30px;"><input name="pmRatio" type="text" id="pmRatio" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmRatio'];?>
"/><span id="pmRatioSpan" style="color: red;"></span></td>

									</tr>
									<tr class="odd">
										<td align="right">备注</td>
										<td align="left" style="padding-left: 30px;"><input name="pmNotes" type="text" id="pmNotes" value="<?php echo $_smarty_tpl->tpl_vars['pmList']->value['pmNotes'];?>
" size="50"/><span id="pmNotesSpan" style="color: red;"></span></td>
									</tr>

									<tr >
										<td colspan="2">
											<input type="submit" value="提交" class="submit" id="submit"/>
											<input type="button" value="返回" id="back"/>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>