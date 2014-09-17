<?php /* Smarty version Smarty-3.1.12, created on 2014-03-31 11:52:32
         compiled from "D:\Workspace\PHP\mail_subscription\html\template\v1\modifyPower.html" */ ?>
<?php /*%%SmartyHeaderCode:2472532ab9f5527bb6-67097073%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3be7ee8369311d72810c713de95928691a26322' => 
    array (
      0 => 'D:\\Workspace\\PHP\\mail_subscription\\html\\template\\v1\\modifyPower.html',
      1 => 1396236858,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2472532ab9f5527bb6-67097073',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_532ab9f5609dc5_05972336',
  'variables' => 
  array (
    'list_id' => 0,
    'mailName' => 0,
    'mailDescript' => 0,
    'mailEnglish' => 0,
    'getMailPower' => 0,
    'addId' => 0,
    'addVar' => 0,
    'showCompany' => 0,
    'company' => 0,
    'power' => 0,
    'addDept' => 0,
    'getDept' => 0,
    'list' => 0,
    'dept' => 0,
    'delete' => 0,
    'remove' => 0,
    'addJob' => 0,
    'jobList' => 0,
    'getJob' => 0,
    'joblist' => 0,
    'job' => 0,
    'getjob' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_532ab9f5609dc5_05972336')) {function content_532ab9f5609dc5_05972336($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="main products-main align-main" style="border:1px solid #ccc;">
  <div class="title font-18" style="font-weight:bold;padding:10px;"> 新增邮件 </div>
  <div style="padding:10px;">
  <form name="addMail" method="post" action="index.php?mod=MailManage&act=modifyMailPower&list_id=<?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
" onsubmit="return checkDatabasePower();">
    <table style="border:none;margin:0 auto;">
      <tbody>
        <tr>
          <td> 邮件名称： </td>
          <td><input type="text" name="mail_name" id="mail_name" value="<?php echo $_smarty_tpl->tpl_vars['mailName']->value;?>
" onblur="checkName(this);" /><span id="inform"></span>
          </td>
        </tr>
        <tr>
          <td> 邮件描述： </td>
          <td><input type="text" name="mail_descript" id="mail_descript" value="<?php echo $_smarty_tpl->tpl_vars['mailDescript']->value;?>
" onblur="checkDescript(this);" /><span id="descript"></span>
          </td>
        </tr>
        <tr>
          <td> 邮件英文ID： </td>
          <td><input type="text" name="mail_english" id="mail_english" value="<?php echo $_smarty_tpl->tpl_vars['mailEnglish']->value;?>
" readonly /><span id="english"> 此项不可更改</span>
          </td>
        </tr>
        <div id="showAll_0">
        <tr style="vertical-align:top">
          <td> 邮件权限： </td>
          <td>
          <table style="border:0;" id="show">
          <?php  $_smarty_tpl->tpl_vars['power'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['power']->_loop = false;
 $_smarty_tpl->tpl_vars['powerList'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['getMailPower']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['power']->key => $_smarty_tpl->tpl_vars['power']->value){
$_smarty_tpl->tpl_vars['power']->_loop = true;
 $_smarty_tpl->tpl_vars['powerList']->value = $_smarty_tpl->tpl_vars['power']->key;
?>
          	<tr id="id_<?php echo $_smarty_tpl->tpl_vars['addId']->value++;?>
">
          		<td>
          			<select name="company[]" id="company_<?php echo $_smarty_tpl->tpl_vars['addVar']->value++;?>
" onChange="showDept(this);" class="company">
			          <?php  $_smarty_tpl->tpl_vars['company'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['company']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['showCompany']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['company']->key => $_smarty_tpl->tpl_vars['company']->value){
$_smarty_tpl->tpl_vars['company']->_loop = true;
?>
			              <option <?php if ($_smarty_tpl->tpl_vars['company']->value['company_id']==$_smarty_tpl->tpl_vars['power']->value['company_id']){?> selected="selected" <?php }?> value="<?php echo $_smarty_tpl->tpl_vars['company']->value['company_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['company']->value['company_name'];?>
</option>
			          <?php } ?>
            		</select>
          			<select name="dept[]" id="dept_<?php echo $_smarty_tpl->tpl_vars['addDept']->value++;?>
" onChange="showJob(this);checkPowerRepeat(this);" class="dept">
			          <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['getDept']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
			          	<?php  $_smarty_tpl->tpl_vars['dept'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['dept']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['dept']->key => $_smarty_tpl->tpl_vars['dept']->value){
$_smarty_tpl->tpl_vars['dept']->_loop = true;
?>
			          		<option <?php if ($_smarty_tpl->tpl_vars['dept']->value['company_id']==$_smarty_tpl->tpl_vars['power']->value['company_id']&&$_smarty_tpl->tpl_vars['dept']->value['dept_id']==$_smarty_tpl->tpl_vars['power']->value['dept_id']){?> selected="selected" <?php }?> value="<?php echo $_smarty_tpl->tpl_vars['dept']->value['company_id'];?>
_<?php echo $_smarty_tpl->tpl_vars['dept']->value['dept_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['dept']->value['dept_name'];?>
</option>
			          	<?php } ?>
			          <?php } ?>
            		</select>
          		</td>
          		<td id='delete_<?php echo $_smarty_tpl->tpl_vars['delete']->value++;?>
'><a href="#" onclick="removejob(<?php echo $_smarty_tpl->tpl_vars['remove']->value++;?>
);">删除</a></td>
          	</tr>
          	<tr id="showJob_<?php echo $_smarty_tpl->tpl_vars['addJob']->value++;?>
">
          		<td id="job_<?php echo $_smarty_tpl->tpl_vars['jobList']->value++;?>
">
          			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['joblist'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['getJob']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['joblist']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
          			<?php if ($_smarty_tpl->tpl_vars['joblist']->value==$_smarty_tpl->tpl_vars['power']->value['dept_id']){?>
	          			<?php  $_smarty_tpl->tpl_vars['job'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['job']->_loop = false;
 $_smarty_tpl->tpl_vars['getjob'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['job']->key => $_smarty_tpl->tpl_vars['job']->value){
$_smarty_tpl->tpl_vars['job']->_loop = true;
 $_smarty_tpl->tpl_vars['getjob']->value = $_smarty_tpl->tpl_vars['job']->key;
?>
	          			<label>
	          				<input <?php if (in_array($_smarty_tpl->tpl_vars['job']->value['job_id'],$_smarty_tpl->tpl_vars['power']->value['job_id'])){?> checked="checked" <?php }?> type="checkbox" class="checkJob" name="jobs[]" value="<?php echo $_smarty_tpl->tpl_vars['job']->value['company_id'];?>
_<?php echo $_smarty_tpl->tpl_vars['job']->value['dept_id'];?>
_<?php echo $_smarty_tpl->tpl_vars['job']->value['job_id'];?>
" /><?php echo $_smarty_tpl->tpl_vars['job']->value['job_name'];?>

	          			</label>
	          			<?php if ((($_smarty_tpl->tpl_vars['getjob']->value+1)%5==0)){?> 
  							<br />
         		   		<?php }?>
	          			<?php } ?>
	          		<?php }?>
          			<?php } ?>
          		</td>
          		<td></td>
          	</tr>
          	<?php } ?>
          </table>
          </td>
        </tr>
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