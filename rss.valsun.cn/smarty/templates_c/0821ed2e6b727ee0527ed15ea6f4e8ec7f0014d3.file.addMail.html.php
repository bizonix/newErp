<?php /* Smarty version Smarty-3.1.12, created on 2014-04-04 15:42:46
         compiled from "/data/web/subscription.valsun.cn/html/template/v1/addMail.html" */ ?>
<?php /*%%SmartyHeaderCode:63515653533919dfbd3282-54677630%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0821ed2e6b727ee0527ed15ea6f4e8ec7f0014d3' => 
    array (
      0 => '/data/web/subscription.valsun.cn/html/template/v1/addMail.html',
      1 => 1396597012,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63515653533919dfbd3282-54677630',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_533919dfc261d2_52361133',
  'variables' => 
  array (
    'getSystem' => 0,
    'system' => 0,
    'showCompany' => 0,
    'company' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_533919dfc261d2_52361133')) {function content_533919dfc261d2_52361133($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="main products-main align-main" style="border:1px solid #ccc;">
  <div class="title font-18" style="font-weight:bold;padding:10px;"> 新增邮件 </div>
  <div style="padding:10px;">
  <form name="addMail" method="post" action="index.php?mod=MailManage&act=addMailList" onsubmit="return checkDatabasePower();">
    <table style="border:none;margin:0 auto;">
      <tbody>
        <tr>
          <td> 邮件名称： </td>
          <td><input type="text" name="mail_name" id="mail_name" onblur="checkName(this);" /><span id="inform"></span>
          </td>
        </tr>
        <tr>
          <td> 邮件描述： </td>
          <td><input type="text" name="mail_descript" id="mail_descript" onblur="checkDescript(this);" /><span id="descript"></span>
          </td>
        </tr>
        <tr>
          <td> 邮件英文ID： </td>
          <td><input type="text" name="mail_english" id="mail_english" onblur="checkEnglish(this);" /><span id="english"></span>
          </td>
        </tr>
        <tr>
        	<td>所属系统：</td>
        	<td>
            	<select name="system" id="system">
	          	  	<option value="default">-----请选择-----</option>
	          		<?php  $_smarty_tpl->tpl_vars['system'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['system']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['getSystem']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['system']->key => $_smarty_tpl->tpl_vars['system']->value){
$_smarty_tpl->tpl_vars['system']->_loop = true;
?>
	              	<option value="<?php echo $_smarty_tpl->tpl_vars['system']->value['system_id'];?>
">--<?php echo $_smarty_tpl->tpl_vars['system']->value['system_name'];?>
--</option>
	          		<?php } ?>
	            </select>
	        </td>
        </tr>
        <div id="showAll_0">
        <tr>
          <td style="vertical-align:top"> 邮件权限： </td>
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
	            <select name="dept[]" id="dept_0" onChange="showJob(this);checkPowerRepeat(this);" class="dept">
	              <option value="default">-----请选择-----</option>
	            </select>
	            </td>
            	<td></td>
           </tr>
        <tr id="showJob_0">
          <td name="jobs[]" id="job_0">
          </td>
        </tr>
          </table>
          </td>
        </div>
        </table>
        <table style="border:none;margin:0 auto;">
        <tr>
          <td></td>
          <td><a href="#" onclick="addJobPower();">新增更多岗位</a> </td>
        </tr>
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