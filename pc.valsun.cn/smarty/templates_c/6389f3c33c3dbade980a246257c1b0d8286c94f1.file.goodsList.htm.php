<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:08:50
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\goodsList.htm" */ ?>
<?php /*%%SmartyHeaderCode:20645526616b96a4ff3-36946124%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6389f3c33c3dbade980a246257c1b0d8286c94f1' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\goodsList.htm',
      1 => 1382432919,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20645526616b96a4ff3-36946124',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616b983b8b3_14494234',
  'variables' => 
  array (
    'show_page' => 0,
    'value' => 0,
    'categorySearch' => 0,
    'productList' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616b983b8b3_14494234')) {function content_526616b983b8b3_14494234($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
								  <span>查找：
								   <input name="seachdata" type="text" id="seachdata" value="<?php echo $_GET['seachdata'];?>
"/>
								   <select name="searchs" id="searchs" >
									<option value="1" <?php if ($_GET['searchs']==1){?>selected='selected'<?php }?>>SPU</option>
									<option value="2" <?php if ($_GET['searchs']==2){?>selected='selected'<?php }?>>SKU</option>
								  </select>
								   </span>
								   <span>新/老品：
								   <select name="isNew" id="isNew" >
								    <option value="0" ></option>
									<option value="1" <?php if ($_GET['isNew']==1){?>selected='selected'<?php }?>>新品</option>
									<option value="2" <?php if ($_GET['isNew']==2){?>selected='selected'<?php }?>>老品</option>
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
								<span>类别&nbsp;&nbsp;
									<select name="sku_category" id="pid_one" onchange="select_one();">
										<option value="0"></option>
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
                                   <span><button id='seachGoods'/>搜索</button></span>
                                   &nbsp;
                                   <?php if ($_smarty_tpl->tpl_vars['categorySearch']->value!=''){?>
                                   <span style="color: green;">上次您搜索的类别为：<?php echo $_smarty_tpl->tpl_vars['categorySearch']->value;?>
</span>
                                   <?php }?>
                                   <span style="color: red;" id="error"><?php echo $_GET['status'];?>
</span>
            </div>
            <div class="main feedback-main">
            	<table class="products-action" cellspacing="0" width="100%">
                   <tr class="title">
					    <td>产品图片</td>
                        <td width="25%">产品名称</td>
                        <td>SPU</td>
                        <td>SKU</td>
						<td>产品类别</td>
						<td>产品成本</td>
						<td>包材</td>
						<td>重量</td>
						<td>采购负责人</td>
						<td>新/老品</td>
                        <td>供应商</td>
						<td>操作</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['productList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                                <tr id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
">
									<td>
									<a class="fancybox" href="http://192.168.200.200:9998/imgs/<?php echo get_sku_imgName($_smarty_tpl->tpl_vars['value']->value['sku']);?>
-G.jpg" target="_blank">
									<img  src="http://192.168.200.200:9998/imgs/<?php echo get_sku_imgName($_smarty_tpl->tpl_vars['value']->value['sku']);?>
-G_thumnail.jpg" width="50" height="50" style="border-style:solid;border-width:0" />
									</a>
									</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsName'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
</td>
									<td><?php echo getAllCateNameByPath($_smarty_tpl->tpl_vars['value']->value['goodsCategory']);?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsCost'];?>
</td>
									<td><?php echo PackingMaterialsModel::getPmNameById($_smarty_tpl->tpl_vars['value']->value['pmId']);?>
</td>
									<td><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsWeight'];?>
</td>
									<td><?php echo getPersonNameById($_smarty_tpl->tpl_vars['value']->value['purchaseId']);?>
</td>
									<td><?php if ($_smarty_tpl->tpl_vars['value']->value['isNew']==0){?>老品<?php }else{ ?>新品<?php }?></td>
                                    <td><?php echo OmAvailableModel::getParterNameBySku($_smarty_tpl->tpl_vars['value']->value['sku']);?>
</td>
									<td>
									<input type="button" value="修改" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="mod"/>
									<input type="button" value="删除" tid="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="del"/>
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

<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox({
				helpers: {
					title : {
						type : 'outside'
					},
					overlay : {
						speedOut : 0
					}
				}
			});

		});
</script><?php }} ?>