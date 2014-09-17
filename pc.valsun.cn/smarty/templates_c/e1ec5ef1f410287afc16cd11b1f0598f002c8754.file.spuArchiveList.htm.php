<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:56:15
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\spuArchiveList.htm" */ ?>
<?php /*%%SmartyHeaderCode:10137526616d10f8e21-79562059%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e1ec5ef1f410287afc16cd11b1f0598f002c8754' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\spuArchiveList.htm',
      1 => 1382439373,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10137526616d10f8e21-79562059',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616d124ef17_38368080',
  'variables' => 
  array (
    'show_page' => 0,
    'value' => 0,
    'categorySearch' => 0,
    'status' => 0,
    'spuArchiveList' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616d124ef17_38368080')) {function content_526616d124ef17_38368080($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wamp\\www\\ftpPc.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/goodslist.js"></script>
<div class="fourvar">
            	<div class="pathvar">
                <?php echo $_smarty_tpl->getSubTemplate ('pcNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                </div>
                <div class="texvar">
                </div>
                <div class="pagination">
                <?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

                </div>
            </div>
			<div class="servar products-servar">
            					  <span>SPU：
								   <input name="spu" type="text" id="spu" value="<?php echo $_GET['spu'];?>
"/>
								   </span>
								   <span>审核状态：
								   <select name="auditStatus" id="auditStatus" >
								    <option value="0" > </option>
									<option value="1" <?php if ($_GET['auditStatus']==1){?>selected='selected'<?php }?>>待审核</option>
									<option value="2" <?php if ($_GET['auditStatus']==2){?>selected='selected'<?php }?>>审核通过</option>
								  	<option value="3" <?php if ($_GET['auditStatus']==3){?>selected='selected'<?php }?>>审核不通过</option>
								  </select>
                                  <span>采购员：
								   <select name="purchaseId" id="purchaseId" >
								    <option value="0" > </option>
								    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = Auth::getApiPurchaseUsers(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['userId'];?>
" <?php if ($_GET['purchaseId']==$_smarty_tpl->tpl_vars['value']->value['userId']){?>selected='selected'<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['userName'];?>
</option>
									<?php } ?>
								  </select>
								   </span>
								   </span>
								   <span>类别&nbsp;&nbsp;
									<select name="sku_category" id="pid_one" onchange="select_one();">
										<option value="0">请选择</option>
										<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = CategoryModel::getCategoryList('*',"where is_delete=0 and pid=0"); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</option>
										<?php } ?>
									</select>
									<span align="left" id="div_two" style="width:auto; display:none"></span>
									<span align="left" id="div_three" style="width:auto; display:none"></span>
									<span align="left" id="div_four" style="width:auto; display:none"></span>
								</span>
                                   <span><button id='seachSpuArchive'>搜索</button></span>
                                   <?php if ($_smarty_tpl->tpl_vars['categorySearch']->value!=''){?>
                                   <span style="color: green;">上次您搜索的类别为：<?php echo $_smarty_tpl->tpl_vars['categorySearch']->value;?>
</span>
                                   <?php }?>
            &nbsp;
			<span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
                        <tr>
                            <td>SPU</td>
							<td>采购员</td>
							<td>类型</td>
							<td>在线状态</td>
							<td>审核状态</td>
							<td>添加时间</td>
							<td>单/虚拟料号</td>
							<td>操作</td>
                        </tr>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['spuArchiveList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
</td>
                                    <td><?php echo getPersonNameById($_smarty_tpl->tpl_vars['value']->value['purchaseId']);?>
</td>
									<td><?php echo getAllCateNameByPath($_smarty_tpl->tpl_vars['value']->value['categoryPath']);?>
</td>
                                    <td>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['spuStatus']==1){?>下线<?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['spuStatus']==2){?>上线<?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['spuStatus']==3){?>部分停售<?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['spuStatus']==4){?>停售<?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['spuStatus']==5){?>部分下线<?php }?>
                                    </td>
                                    <td>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['auditStatus']==1){?>待审核<?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['auditStatus']==2){?><img alt="审核通过" src="http://misc.erp.valsun.cn/img/right.png"/><?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['value']->value['auditStatus']==3){?><img alt="审核不通过" src="http://misc.erp.valsun.cn/img/wrong.png"/><?php }?>
                                    </td>
                                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value['spuCreatedTime'],"Y-m-d H:i");?>
</td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['value']->value['isSingSpu']==1){?>单料号<?php }else{ ?>虚拟料号<?php }?></td>
                                    <td>
										<input type="button" onclick="window.location.href = 'index.php?mod=autoCreateSpu&act=scanSpuArchive&spu=<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
'" value="查看"/>
                                        <input type="button" onclick="window.location.href = 'index.php?mod=autoCreateSpu&act=updateSpuArchive&spu=<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
'" value="编辑"/>

                                    </td>
                                </tr>
                     <?php } ?>
                </table>
            </div>
            <div class="bottomvar">
            	<div class="texvar">

            	</div>
            	<div class="pagination">
					<?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>