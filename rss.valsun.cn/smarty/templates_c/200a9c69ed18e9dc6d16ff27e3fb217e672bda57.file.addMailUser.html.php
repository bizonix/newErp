<?php /* Smarty version Smarty-3.1.12, created on 2014-04-07 09:22:12
         compiled from "/data/web/subscription.valsun.cn/html/template/v1/addMailUser.html" */ ?>
<?php /*%%SmartyHeaderCode:1570905140533cfca2bf87c6-54440346%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '200a9c69ed18e9dc6d16ff27e3fb217e672bda57' => 
    array (
      0 => '/data/web/subscription.valsun.cn/html/template/v1/addMailUser.html',
      1 => 1396597012,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1570905140533cfca2bf87c6-54440346',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_533cfca2c47b24_01664169',
  'variables' => 
  array (
    'list_id' => 0,
    'mailName' => 0,
    'mailDescript' => 0,
    'mailEnglish' => 0,
    'mailSystem' => 0,
    'showCompany' => 0,
    'company' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_533cfca2c47b24_01664169')) {function content_533cfca2c47b24_01664169($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="main products-main align-main" style="border:1px solid #ccc;">
  <div class="title font-18" style="font-weight:bold;padding:10px;"> 新增邮件 </div>
  <div style="padding:10px;">
  <form name="addMail" method="post" action="index.php?mod=MailManage&act=addMailUser&list_id=<?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
">
    <table style="border:none;margin:0 auto;">
      <tbody>
        <tr>
          <td> 邮件名称： </td>
          <td><?php echo $_smarty_tpl->tpl_vars['mailName']->value;?>
</td>
        </tr>
        <tr>
          <td> 邮件描述： </td>
          <td><?php echo $_smarty_tpl->tpl_vars['mailDescript']->value;?>
</td>
        </tr>
        <tr>
          <td> 邮件英文ID： </td>
          <td><?php echo $_smarty_tpl->tpl_vars['mailEnglish']->value;?>
</td>
        </tr>
        <tr>
        	<td>所属系统：</td>
        	<td><?php echo $_smarty_tpl->tpl_vars['mailSystem']->value;?>
</td>
        </tr>
        <div id="showAll_0">
        <tr>
          <td style="vertical-align:top"> 新增订阅用户： </td>
          <td>
          <table style="border:0;" id="show">
          <tr>
	          <td>
	          <select name="company[]" id="company_0" onChange="showDept(this);" class="company">
	          	  <option value="default">-----请选择-----</option>
	          <?php  $_smarty_tpl->tpl_vars['company'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['company']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['showCompany']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['company']->key => $_smarty_tpl->tpl_vars['company']->value){
$_smarty_tpl->tpl_vars['company']->_loop = true;
?>
	              <option value="<?php echo $_smarty_tpl->tpl_vars['company']->value['company_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['company']->value['company_name'];?>
</option>
	          <?php } ?>
	            </select>
	            <select name="dept[]" id="dept_0" onChange="showUserJob(this);" class="dept">
	              <option value="default">-----请选择-----</option>
	            </select>
	            <select name="jobs[]" id="job_0" onChange="checkUserPower(this);showUser(this);" class="job">
	              <option value="default">-----请选择-----</option>
	            </select>
	            </td>
            	<td></td>
           </tr>
           <tr>
           	<td name="users[]"><span id="user_0"></span>
           	</td>
           </tr>
          </table>
          </td>
        </div>
        </table>
        <table style="border:none;margin:0 auto;">
        <tr>
          <td></td>
          <td><input type="submit" name="submit" value="提交" />
          </td>
        </tr>
        </table>
        </form>
      </tbody>
  </div>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
</body>
</html><?php }} ?>