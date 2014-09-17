<?php /* Smarty version Smarty-3.1.12, created on 2014-03-06 19:26:22
         compiled from "E:\erpNew\wh.valsun.cn\html\template\v1\packageCheck.htm" */ ?>
<?php /*%%SmartyHeaderCode:2473753185b5e848bb3-45456453%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a026c4167bbbd46a51a89e3e3efd28706613de0d' => 
    array (
      0 => 'E:\\erpNew\\wh.valsun.cn\\html\\template\\v1\\packageCheck.htm',
      1 => 1393658438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2473753185b5e848bb3-45456453',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'succeedLog' => 0,
    'errorLog' => 0,
    'curusername' => 0,
    'tally_user' => 0,
    'list' => 0,
    'checkUser' => 0,
    'data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53185b5e8b27c5_90632508',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53185b5e8b27c5_90632508')) {function content_53185b5e8b27c5_90632508($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/packageCheck.js"></script>

<?php echo $_smarty_tpl->getSubTemplate ('whNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>




<div class="servar wh-servar">
	<span>  
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button   type="button" onclick="javascript:window.location.href='./index.php?act=abnormal&mod=packageCheck';" value="" >查看录入异常</button>
    </span>
    <span>  
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button   type="button" onclick="javascript:window.location.href='./index.php?act=packageCheckList&mod=packageCheck';" value="" >点货清单</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="succeedLog" style="color: #0F0"><?php echo $_smarty_tpl->tpl_vars['succeedLog']->value;?>
</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="errorLog" style="color: #F00"><?php echo $_smarty_tpl->tpl_vars['errorLog']->value;?>
</span>
    </span>
</div>

<div class="main">
 <form id="check_form" class="navbar-form pull-left" method="POST">    
	<input type="hidden" id="hidden" name="userName" value="<?php echo $_smarty_tpl->tpl_vars['curusername']->value;?>
"/>
	<div style="float:left;width:500px;margin-left:20px">
	<table id="checkinfo" cellspacing="0" width="85%">
        <thead>
            <tr class="title">
                <td width="50%">sku</td>
                <td width="50%">数量</td>

            </tr>
        </thead>
        <tbody>
            <tr class="odd">
                <td width="50%"><input name="sku" id="r1" type="text" class="mf validate[required] text-input" value=""></td>
                <td width="50%"><input name="amount" id="n1" type="text" class="validate[required,,custom[integer],min[0]]" value="">&nbsp;&nbsp;&nbsp;</td>
                
                
                
            </tr>
        </tbody>


    
    </table>
	<br>&nbsp;<span style="width:margin:0 50px;">点货人：</span>
	<select class="validate[required]" style="width: 130px;" id="checkUser" name="checkUser">
	<option value="">请选择</option>
	<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tally_user']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['global_user_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['list']->value['global_user_id']==$_smarty_tpl->tpl_vars['checkUser']->value){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['global_user_name'];?>
</option>
	<?php } ?>
	</select>
	</div>
	<div style="float:left;" id="message"><?php echo $_smarty_tpl->tpl_vars['data']->value;?>
</div>
	<div style="clear:both;">&nbsp;</div>
	<button style="margin:0 20px;" id="addone" type="button" value="新增一行" >新增一行</button>&nbsp;&nbsp;&nbsp;<button style="margin:0 20px;" id="delone" type="button" value="减去一行" >减去一行</button>&nbsp;&nbsp;&nbsp;<button style="margin:0 50px;" type="button"  id="submitform" class="btn btn-default" value=""/>提交</button>
 </form>
</div>
<?php echo $_smarty_tpl->getSubTemplate ('footer.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>