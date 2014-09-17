<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 21:37:38
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\showUserCompense.htm" */ ?>
<?php /*%%SmartyHeaderCode:179145319cba20f70b2-92705220%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0f33c32d609e0ece762a67f28d98ab66d1e9e596' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\showUserCompense.htm',
      1 => 1393658410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '179145319cba20f70b2-92705220',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uid' => 0,
    'uidUser' => 0,
    'arr_all_platform_account' => 0,
    'viewaccountlist' => 0,
    'pid' => 0,
    'visible_platform_account' => 0,
    'platformName' => 0,
    'acountlists' => 0,
    'viewaccount' => 0,
    'aid' => 0,
    'StatusMenu' => 0,
    'key_visible_movefolder' => 0,
    'statusId' => 0,
    'statusvalue' => 0,
    'statusGroupLists' => 0,
    'statusCode' => 0,
    'statusGroupList' => 0,
    'statusGroup' => 0,
    'visible_showfolder' => 0,
    'editorder_options' => 0,
    'optionId' => 0,
    'visible_editorder' => 0,
    'optionvalue' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5319cba229dd99_23082575',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5319cba229dd99_23082575')) {function content_5319cba229dd99_23082575($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_checkboxes')) include 'E:\\erpNew\\order.valsun.cn\\lib\\template\\smarty\\plugins\\function.html_checkboxes.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript" src="./js/usercompense.js"></script>
<!--<div class="servar">
	
</div>-->
<div class="main">
    <div id="accordion">
    	<h3>平台账号权限控制-<?php $_smarty_tpl->tpl_vars['uidUser'] = new Smarty_variable(UserModel::getUsernameById($_smarty_tpl->tpl_vars['uid']->value), null, 0);?><?php echo $_smarty_tpl->tpl_vars['uidUser']->value;?>
</h3>
        <div>
        <form name="powerFrom" id="powerFrom" action="index.php?mod=omAccount&act=showUserCompense" method="post">
            <input type="hidden" name="action" value="accountpower" />
            <input type="hidden" id="uid" name="uid" value="<?php echo $_smarty_tpl->tpl_vars['uid']->value;?>
" />
            <?php  $_smarty_tpl->tpl_vars['viewaccountlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['viewaccountlist']->_loop = false;
 $_smarty_tpl->tpl_vars['pid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['arr_all_platform_account']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['viewaccountlist']->key => $_smarty_tpl->tpl_vars['viewaccountlist']->value){
$_smarty_tpl->tpl_vars['viewaccountlist']->_loop = true;
 $_smarty_tpl->tpl_vars['pid']->value = $_smarty_tpl->tpl_vars['viewaccountlist']->key;
?>
                <?php $_smarty_tpl->tpl_vars['platformName'] = new Smarty_variable($_smarty_tpl->tpl_vars['viewaccountlist']->value['platform'], null, 0);?>
                <?php $_smarty_tpl->tpl_vars['acountlists'] = new Smarty_variable($_smarty_tpl->tpl_vars['viewaccountlist']->value['acountlists'], null, 0);?>
                <input type="checkbox" id="checkboxes_platform_<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
" name="checkboxes_platform[]" <?php if ($_smarty_tpl->tpl_vars['visible_platform_account']->value[$_smarty_tpl->tpl_vars['pid']->value]){?>checked='checked'<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
" onclick="platformCheckBox(<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
);" />&nbsp;<b><?php echo $_smarty_tpl->tpl_vars['platformName']->value;?>
</b><br>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php  $_smarty_tpl->tpl_vars['viewaccount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['viewaccount']->_loop = false;
 $_smarty_tpl->tpl_vars['aid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['acountlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['viewaccount']->key => $_smarty_tpl->tpl_vars['viewaccount']->value){
$_smarty_tpl->tpl_vars['viewaccount']->_loop = true;
 $_smarty_tpl->tpl_vars['aid']->value = $_smarty_tpl->tpl_vars['viewaccount']->key;
?>
                	<?php if ($_smarty_tpl->tpl_vars['viewaccount']->value!=''){?>
                	<input type="checkbox" id="checkboxes_account_<?php echo $_smarty_tpl->tpl_vars['aid']->value;?>
" name="checkboxes_account_<?php echo $_smarty_tpl->tpl_vars['pid']->value;?>
[]" <?php if (in_array($_smarty_tpl->tpl_vars['aid']->value,$_smarty_tpl->tpl_vars['visible_platform_account']->value[$_smarty_tpl->tpl_vars['pid']->value])){?>checked='checked'<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['aid']->value;?>
" />&nbsp;<?php echo $_smarty_tpl->tpl_vars['viewaccount']->value;?>
&nbsp;&nbsp;
                    <?php }?>
                <?php } ?>
                <br>
			<?php } ?>
              <!--<div id="tabs-2">
                <p>待开发权限.</p>
              </div>-->
        	<input type="submit" value="update" />
        </form>
        </div>
    	<h3>文件夹移动权限设置</h3>
        <div>
            <!--<form name="powerFrom" id="powerFrom" action="index.php?mod=omAccount&act=showUserCompense" method="post">
            <input type="hidden" name="action" value="movefolder" />
            <input type="hidden" name="uid" value="<?php echo $_smarty_tpl->tpl_vars['uid']->value;?>
" />-->
            <!--<p><?php echo smarty_function_html_checkboxes(array('id'=>"checkboxes_movefolder",'name'=>"checkboxes_movefolder",'options'=>$_smarty_tpl->tpl_vars['StatusMenu']->value,'checked'=>$_smarty_tpl->tpl_vars['key_visible_movefolder']->value,'separator'=>"&nbsp;&nbsp;"),$_smarty_tpl);?>
</p>-->
            <table cellpadding="0" cellspacing="0" style="border:0;">
            <tr>
            <td>
            移出
            </td>
            <td>
            移入
            </td>
            </tr>
            <tr>
            <td>
            <select id="select_movefolder" name="select_movefolder" size="14">
            	<?php  $_smarty_tpl->tpl_vars['statusvalue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['statusvalue']->_loop = false;
 $_smarty_tpl->tpl_vars['statusId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['StatusMenu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['statusvalue']->key => $_smarty_tpl->tpl_vars['statusvalue']->value){
$_smarty_tpl->tpl_vars['statusvalue']->_loop = true;
 $_smarty_tpl->tpl_vars['statusId']->value = $_smarty_tpl->tpl_vars['statusvalue']->key;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['statusId']->value;?>
" onclick="showInfolderList(<?php echo $_smarty_tpl->tpl_vars['statusId']->value;?>
);"><?php echo $_smarty_tpl->tpl_vars['statusvalue']->value;?>
</option>
                <?php } ?>
            	
            </select>
            </td>
            <td>
            <!--<div id="infolderlist" style="text-align:right; border:#CCC solid 1px;">-->
            	<?php  $_smarty_tpl->tpl_vars['statusvalue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['statusvalue']->_loop = false;
 $_smarty_tpl->tpl_vars['statusId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['StatusMenu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['statusvalue']->key => $_smarty_tpl->tpl_vars['statusvalue']->value){
$_smarty_tpl->tpl_vars['statusvalue']->_loop = true;
 $_smarty_tpl->tpl_vars['statusId']->value = $_smarty_tpl->tpl_vars['statusvalue']->key;
?>
                <input type="checkbox" id="checkboxes_movefolder<?php echo $_smarty_tpl->tpl_vars['statusId']->value;?>
" onclick="clickmovefolder(<?php echo $_smarty_tpl->tpl_vars['statusId']->value;?>
);" name="checkboxes_movefolder" value="<?php echo $_smarty_tpl->tpl_vars['statusId']->value;?>
" />&nbsp;<?php echo $_smarty_tpl->tpl_vars['statusvalue']->value;?>
&nbsp;&nbsp;
                <?php } ?>
            </td>
            </tr>
            </table>
            <!--</div>-->
            <!--<input type="submit" value="update" />
            </form>-->
        </div>

        <h3>文件夹显示权限设置</h3>
        <div> 
            <p>            
            

            <?php  $_smarty_tpl->tpl_vars['statusGroupList'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['statusGroupList']->_loop = false;
 $_smarty_tpl->tpl_vars['statusCode'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['statusGroupLists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['statusGroupList']->key => $_smarty_tpl->tpl_vars['statusGroupList']->value){
$_smarty_tpl->tpl_vars['statusGroupList']->_loop = true;
 $_smarty_tpl->tpl_vars['statusCode']->value = $_smarty_tpl->tpl_vars['statusGroupList']->key;
?>
            <input type="checkbox" id="checkboxes_showfolder<?php echo $_smarty_tpl->tpl_vars['statusCode']->value;?>
"  name="checkboxes_showfolder0" subCode="<?php echo $_smarty_tpl->tpl_vars['statusGroupList']->value['subCode'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['statusCode']->value;?>
" onclick="clickMainCheckBox(<?php echo $_smarty_tpl->tpl_vars['statusCode']->value;?>
);" />&nbsp;<b><?php echo $_smarty_tpl->tpl_vars['statusGroupList']->value['name'];?>
</b><br>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php  $_smarty_tpl->tpl_vars['statusGroup'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['statusGroup']->_loop = false;
 $_smarty_tpl->tpl_vars['statusCode2'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['statusGroupList']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['statusGroup']->key => $_smarty_tpl->tpl_vars['statusGroup']->value){
$_smarty_tpl->tpl_vars['statusGroup']->_loop = true;
 $_smarty_tpl->tpl_vars['statusCode2']->value = $_smarty_tpl->tpl_vars['statusGroup']->key;
?>
                <input type="checkbox"  id="checkboxes_showfolder<?php echo $_smarty_tpl->tpl_vars['statusGroup']->value['statusCode'];?>
" onclick="clickSubCheckBox(<?php echo $_smarty_tpl->tpl_vars['statusCode']->value;?>
);" name="checkboxes_showfolder" groupId="<?php echo $_smarty_tpl->tpl_vars['statusCode']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['statusGroup']->value['statusCode'];?>
" <?php if (in_array($_smarty_tpl->tpl_vars['statusGroup']->value['statusCode'],$_smarty_tpl->tpl_vars['visible_showfolder']->value)){?>checked='checked'<?php }?>/>&nbsp;<?php echo $_smarty_tpl->tpl_vars['statusGroup']->value['statusName'];?>
&nbsp;&nbsp;&nbsp;
                <?php } ?>
                <br><br>
            <?php } ?>

            </p>
            <input type="button" value="提交" onclick="updateShowFolders();"/>
        </div>

        <h3>订单编辑权限设置</h3>
        <div>
            <p>
            <?php  $_smarty_tpl->tpl_vars['optionvalue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['optionvalue']->_loop = false;
 $_smarty_tpl->tpl_vars['optionId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['editorder_options']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['optionvalue']->key => $_smarty_tpl->tpl_vars['optionvalue']->value){
$_smarty_tpl->tpl_vars['optionvalue']->_loop = true;
 $_smarty_tpl->tpl_vars['optionId']->value = $_smarty_tpl->tpl_vars['optionvalue']->key;
?>
            <input type="checkbox" id="checkboxes_orderoptions<?php echo $_smarty_tpl->tpl_vars['optionId']->value;?>
" name="checkboxes_orderoptions" value="<?php echo $_smarty_tpl->tpl_vars['optionId']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['optionId']->value,$_smarty_tpl->tpl_vars['visible_editorder']->value)){?>checked='checked'<?php }?> />&nbsp;<?php echo $_smarty_tpl->tpl_vars['optionvalue']->value;?>
&nbsp;&nbsp;
            <?php } ?>
            </p>
            <input type="button" value="提交" onclick="updateOrderOptions();"/>
        </div>
    </div>
    <!--<table cellspacing="0" width="100%">
        <tr class="title">
            <td align="left">平台账号权限控制</td>
        </tr>
        <tr class="odd">
            <td>
                
            </td>
        </tr>
        <tr class="title">
            <td align="left">文件夹移动权限设置</td>
        </tr>
        <tr class="odd">
        	
        </tr>
    </table>-->
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>