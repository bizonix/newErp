<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 18:54:19
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\spuList.htm" */ ?>
<?php /*%%SmartyHeaderCode:144625265dbe4a6c202-51830369%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b980c5359695ffb77028a01392ffdf3d5363dc2c' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\spuList.htm',
      1 => 1382439257,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '144625265dbe4a6c202-51830369',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265dbe4ca0254_58096823',
  'variables' => 
  array (
    'show_page' => 0,
    'value' => 0,
    'status' => 0,
    'spuList' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5265dbe4ca0254_58096823')) {function content_5265dbe4ca0254_58096823($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wamp\\www\\ftpPc.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" type="text/css" href="./js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />
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
								   <span>是否进系统：
								   <select name="autoStatus" id="autoStatus" >
								    <option value="0" > </option>
									<option value="1" <?php if ($_GET['status']==1){?>selected='selected'<?php }?>>No</option>
									<option value="2" <?php if ($_GET['status']==2){?>selected='selected'<?php }?>>Yes</option>
								  </select>
								   </span>
								   <span>单/虚拟料号：
								   <select name="isSingSpu" id="isSingSpu" >
								    <option value="0" > </option>
									<option value="1" <?php if ($_GET['isSingSpu']==1){?>selected='selected'<?php }?>>单料号</option>
									<option value="2" <?php if ($_GET['isSingSpu']==2){?>selected='selected'<?php }?>>虚拟料号</option>
								  </select>
								   </span>
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
                                   <span><button id='seachAutoSpuList'>搜索</button></span>
            &nbsp;
			<span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            <span style="float: right;"><a href="index.php?mod=spu&act=addAutoSpuForOld">添加旧数据进自动生成列表</a></span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
                        <tr>
                            <td>SPU</td>
							<td>申请人</td>
							<td>是否进系统</td>
							<td>添加时间</td>
							<td>单/虚拟料号</td>
							<td>操作</td>
                        </tr>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['spuList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
</td>
                                    <td><?php echo getPersonNameById($_smarty_tpl->tpl_vars['value']->value['purchaseId']);?>
</td>
									<td><?php if ($_smarty_tpl->tpl_vars['value']->value['status']==1){?><img alt="No" src="http://misc.erp.valsun.cn/img/wrong.png"/><?php }else{ ?><img alt="Yes" src="http://misc.erp.valsun.cn/img/right.png"/><?php }?></td>
                                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value['createdTime'],"Y-m-d H:i");?>
</td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['value']->value['isSingSpu']==1){?>单料号<?php }else{ ?>虚拟料号<?php }?></td>
                                    <td>
                                    <?php if ($_SESSION['userId']==$_smarty_tpl->tpl_vars['value']->value['purchaseId']){?>
                                    	<?php if ($_smarty_tpl->tpl_vars['value']->value['status']==1&&$_smarty_tpl->tpl_vars['value']->value['isSingSpu']==1){?>
                                        <input type="button" onclick="window.location.href = 'index.php?mod=autoCreateSpu&act=editAutoCreateSpuCate&spu=<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
'" value="添加档案"/>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['value']->value['isSingSpu']==1&&OmAvailableModel::isSpuAudit($_smarty_tpl->tpl_vars['value']->value['spu'])==true||$_smarty_tpl->tpl_vars['value']->value['isSingSpu']==2){?>
                                        <input type="button" onclick="window.location.href = 'index.php?mod=autoCreateSpu&act=addSku&spu=<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
'" value="添加子料号"/>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['value']->value['status']==1){?>
                                    	<input type="button" class="deleteAutoCreateSpu" spu="<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
" value="删除SPU"/>
                                    	<?php }?>
									<?php }?>
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