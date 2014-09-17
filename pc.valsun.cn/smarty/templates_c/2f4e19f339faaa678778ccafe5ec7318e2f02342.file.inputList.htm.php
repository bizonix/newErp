<?php /* Smarty version Smarty-3.1.12, created on 2013-10-22 17:47:24
         compiled from "D:\wamp\www\ftpPc.valsun.cn\html\v1\inputList.htm" */ ?>
<?php /*%%SmartyHeaderCode:19578526616c1243721-91232788%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2f4e19f339faaa678778ccafe5ec7318e2f02342' => 
    array (
      0 => 'D:\\wamp\\www\\ftpPc.valsun.cn\\html\\v1\\inputList.htm',
      1 => 1382430380,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19578526616c1243721-91232788',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_526616c12e7fe9_50900572',
  'variables' => 
  array (
    'show_page' => 0,
    'value' => 0,
    'categorySearch' => 0,
    'status' => 0,
    'inputList' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_526616c12e7fe9_50900572')) {function content_526616c12e7fe9_50900572($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" type="text/css" href="./js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />
<script type="text/javascript" src="./js/property.js"></script>
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
								  <span>属性名：
								   <input name="inputName" type="text" id="inputName" value="<?php echo $_GET['inputName'];?>
"/>
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
"/><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</option>
										<?php } ?>
									</select>
									<span align="left" id="div_two" style="width:auto; display:none"></span>
									<span align="left" id="div_three" style="width:auto; display:none"></span>
									<span align="left" id="div_four" style="width:auto; display:none"></span>
								</span>
                                   <span><button id='seachInput'/>搜索</button></span>
                                   &nbsp;
                                   <?php if ($_smarty_tpl->tpl_vars['categorySearch']->value!=''){?>
                                   <span style="color: green;">上次您搜索的类别为：<?php echo $_smarty_tpl->tpl_vars['categorySearch']->value;?>
</span>
                                   <?php }?>
                                   <span style="color: red;"><?php echo $_smarty_tpl->tpl_vars['status']->value;?>
</span>
            </div>
            <div class="servar products-servar">
                <span>
                	<a href="index.php?mod=property&act=addInput">新增属性</a>
                </span>
            </div>
            <div class="main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                    	<td>属性名</td>
						<td>录入方式</td>
						<td>关联类型</td>
                    </tr>
                    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inputList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                    <tr>
                        <input name="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" type="hidden"/>
                    	<td><a href="index.php?mod=property&act=updateInput&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['inputName'];?>
</a></td>
                        <td>文本</td>
                        <td><?php echo getAllCateNameByPath($_smarty_tpl->tpl_vars['value']->value['categoryPath']);?>
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