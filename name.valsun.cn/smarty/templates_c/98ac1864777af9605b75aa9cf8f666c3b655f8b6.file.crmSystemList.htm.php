<?php /* Smarty version Smarty-3.1.12, created on 2013-09-26 11:46:33
         compiled from "D:\wamp\www\crm.valsun.cn\html\template\v1\crmSystemList.htm" */ ?>
<?php /*%%SmartyHeaderCode:2235752425a2b43c647-73243649%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98ac1864777af9605b75aa9cf8f666c3b655f8b6' => 
    array (
      0 => 'D:\\wamp\\www\\crm.valsun.cn\\html\\template\\v1\\crmSystemList.htm',
      1 => 1380167191,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2235752425a2b43c647-73243649',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52425a2b528aa1_35499735',
  'variables' => 
  array (
    'show_page' => 0,
    'keyWordsType' => 0,
    'keyWords' => 0,
    'sortType' => 0,
    'choose_status' => 0,
    'invoiceNameArr' => 0,
    'keyChoose' => 0,
    'chooseList' => 0,
    'crmListArr' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52425a2b528aa1_35499735')) {function content_52425a2b528aa1_35499735($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wamp\\www\\crm.valsun.cn\\lib\\template\\smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ('header.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('crmNav.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link type="text/css" rel="stylesheet" href="css/crmSystemList.css">
<script src="./js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script src="./js/crmSystemList.js" type="text/javascript"></script>
<div class="fourvar">
    <div class="texvar">
    </div>
    <div class="pagination">
    <?php echo $_smarty_tpl->tpl_vars['show_page']->value;?>

    </div>
</div>
<div class="fourvar order-fourvar">
	<form name="crmFrom" id="crmFrom" action="index.php?mod=crmSystem&act=crmSystemList" method="post">                   	
        <table>
            <tr>
                <td style="padding-left:17px;">
                    关键字:
                </td>
                <td style="padding-left:15px;">
                    <select name="keyWordsType">
                        <option value="clientname" <?php if ($_smarty_tpl->tpl_vars['keyWordsType']->value=='clientname'){?>selected="selected"<?php }?>>姓名/客户ID</option>
                        <option value="email" <?php if ($_smarty_tpl->tpl_vars['keyWordsType']->value=='email'){?>selected="selected"<?php }?>>邮件</option>
                        <option value="phone" <?php if ($_smarty_tpl->tpl_vars['keyWordsType']->value=='phone'){?>selected="selected"<?php }?>>客户电话</option>
                        <option value="country" <?php if ($_smarty_tpl->tpl_vars['keyWordsType']->value=='country'){?>selected="selected"<?php }?>>所在国家</option>
                        <option value="salesaccount" <?php if ($_smarty_tpl->tpl_vars['keyWordsType']->value=='salesaccount'){?>selected="selected"<?php }?>>销售账号</option>
                    </select>
                </td>
                <td style="padding-left:19px;">
                    <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['keyWords']->value;?>
" name='keyWords' id='keyWords' />
                </td>
                <td style="padding-left:17px;">
                   排序:
                </td>
                <td style="padding-left:15px;">
                    <select name="sortType" id="sortType">
                        <option value="totalpayDesc" <?php if ($_smarty_tpl->tpl_vars['sortType']->value=='totalpayDesc'){?>selected="selected"<?php }?>>按总金额降序</option>
                        <option value="totalpayAsc" <?php if ($_smarty_tpl->tpl_vars['sortType']->value=='totalpayAsc'){?>selected="selected"<?php }?>>按总金额升序</option>
                        <option value="totaltimesDesc" <?php if ($_smarty_tpl->tpl_vars['sortType']->value=='totaltimesDesc'){?>selected="selected"<?php }?>>按购买次数额降序</option>
                        <option value="totaltimesAsc" <?php if ($_smarty_tpl->tpl_vars['sortType']->value=='totaltimesAsc'){?>selected="selected"<?php }?>>按购买次数额升序</option>
                    </select>
                </td>
                <td style="padding-left:17px;">
                    Account:
                </td>
                <td style="padding-left:15px;">
                    <select name="salesAccountList" id="salesAccountList">
                    	<option value="" <?php if ($_smarty_tpl->tpl_vars['choose_status']->value==0){?> selected="selected"<?php }?> >未设置</option>
                        <?php  $_smarty_tpl->tpl_vars['chooseList'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['chooseList']->_loop = false;
 $_smarty_tpl->tpl_vars['keyChoose'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['invoiceNameArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['chooseList']->key => $_smarty_tpl->tpl_vars['chooseList']->value){
$_smarty_tpl->tpl_vars['chooseList']->_loop = true;
 $_smarty_tpl->tpl_vars['keyChoose']->value = $_smarty_tpl->tpl_vars['chooseList']->key;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['choose_status']->value==$_smarty_tpl->tpl_vars['keyChoose']->value){?> selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['keyChoose']->value;?>
" ><?php echo $_smarty_tpl->tpl_vars['chooseList']->value;?>
</option>
                        <?php } ?>
                    </select>
                </td>
                <td style="padding-left:17px;">
                    平台:
                </td>
                <td style="padding-left:15px;">
                    <select name="platformList" id="platformList">
                    	<option value="" <?php if ($_smarty_tpl->tpl_vars['choose_status']->value==0){?> selected="selected"<?php }?> >未设置</option>
                        <?php  $_smarty_tpl->tpl_vars['chooseList'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['chooseList']->_loop = false;
 $_smarty_tpl->tpl_vars['keyChoose'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['invoiceNameArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['chooseList']->key => $_smarty_tpl->tpl_vars['chooseList']->value){
$_smarty_tpl->tpl_vars['chooseList']->_loop = true;
 $_smarty_tpl->tpl_vars['keyChoose']->value = $_smarty_tpl->tpl_vars['chooseList']->key;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['choose_status']->value==$_smarty_tpl->tpl_vars['keyChoose']->value){?> selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['keyChoose']->value;?>
" ><?php echo $_smarty_tpl->tpl_vars['chooseList']->value;?>
</option>
                        <?php } ?>
                    </select>
                </td>
                <td style="padding-left:15px;">
                    <button class="btn" type="submit">查找</button>
                    <button class="btn" id="exportExcelButton" type="button" />xls导出</button>
                    <!-- 老erp系统无此功能
                    <button class="btn" id="exportExcelButton" type="button" />邮箱txt格式下载</button>
                    -->
                </td>
            </tr>
        </table>
     </form>
</div>
            <div class="main">
            	<table cellspacing="0" width="100%">
                	<tr class="title">
                   		<td><input onclick="chooseornot(this)" type="checkbox"/></td>
                        <td>ID</td>
						<td>姓名/客户ID</td>
                        <td>邮件</td>
                        <td>客户电话</td>
                        <td>所在国家</td>
                        <td>总购买金额</td>
                        <td>总购买次数</td>
                        <td>销售账号</td>
                        <td>平台</td>
                        <td>最新购买时间</td>
                    </tr>
					<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_smarty_tpl->tpl_vars['key_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['crmListArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['key_id']->value = $_smarty_tpl->tpl_vars['list']->key;
?>
                    <tr class="odd">
                    	<td><input class="checkclass" id="orderids" name="orderids" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
"></td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['list']->value['clientname'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['email'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['phone'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['country'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['totalpay'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['totaltimes'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['salesaccount'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['list']->value['platform'];?>
</td>
                        <td><?php if (empty($_smarty_tpl->tpl_vars['list']->value['lastbuytime'])){?> <?php }else{ ?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['list']->value['lastbuytime'],"%Y-%m-%d %H:%M:%S");?>
<?php }?></td>
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