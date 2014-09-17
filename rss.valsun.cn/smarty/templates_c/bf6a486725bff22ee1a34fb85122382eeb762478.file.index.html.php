<?php /* Smarty version Smarty-3.1.12, created on 2014-07-11 14:29:51
         compiled from "/data/web/rss.valsun.cn/html/template/v1/index.html" */ ?>
<?php /*%%SmartyHeaderCode:161724581253421571d97ac4-81746854%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf6a486725bff22ee1a34fb85122382eeb762478' => 
    array (
      0 => '/data/web/rss.valsun.cn/html/template/v1/index.html',
      1 => 1405051096,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '161724581253421571d97ac4-81746854',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53421571f2f287_38367766',
  'variables' => 
  array (
    'getSystem' => 0,
    'system' => 0,
    'showUserMail' => 0,
    'mail' => 0,
    'showMailList' => 0,
    'list' => 0,
    'getUserMail' => 0,
    'user' => 0,
    'page_str' => 0,
    'pagestr' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53421571f2f287_38367766')) {function content_53421571f2f287_38367766($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/data/web/rss.valsun.cn/lib/template/smarty/plugins/modifier.truncate.php';
?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            <div class="main products-main" style="border:1px solid #ccc;" id="content">
            <br />
            	<div>
            	<form name="searchMail" id="searchMail" method="post" action="index.php?mod=MailShow&act=getUserMailByCondition">
            		所属系统：
            		<select name="system" id="system">
	          	  		<option value="default">-----请选择-----</option>
	          			<?php  $_smarty_tpl->tpl_vars['system'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['system']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['getSystem']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['system']->key => $_smarty_tpl->tpl_vars['system']->value){
$_smarty_tpl->tpl_vars['system']->_loop = true;
?>
	              		<option name="systemId" value="<?php echo $_smarty_tpl->tpl_vars['system']->value['system_id'];?>
">--<?php echo $_smarty_tpl->tpl_vars['system']->value['system_name'];?>
--</option>
	          			<?php } ?>
	            	</select>
	            	邮件名称：<input type="text" name="mailName" id="mailName" />
	            	<input type="submit" name="search" id="search" value="搜索" />
	            </form>
            	</div>
            	<br />
                <div class="title font-18" style="font-weight:bold;padding:10px;">
                    我订阅的邮件
                </div>
                <div style="padding:10px;">
                <?php if ($_smarty_tpl->tpl_vars['showUserMail']->value){?>
                <?php  $_smarty_tpl->tpl_vars['mail'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mail']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['showUserMail']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mail']->key => $_smarty_tpl->tpl_vars['mail']->value){
$_smarty_tpl->tpl_vars['mail']->_loop = true;
?>
                    <span style="margin-right:40px;display:inline-block;" id="cancelMail">
                        <a href="#"><?php echo $_smarty_tpl->tpl_vars['mail']->value['list_name'];?>
</a>
                        <a href="#" style="color:#a2a2a2;" onclick="cancelMail(<?php echo $_smarty_tpl->tpl_vars['mail']->value['list_id'];?>
)" id="cancel">取消</a>
                    </span>
                <?php } ?>
                <?php }else{ ?>
                <span style="margin-right:40px;display:inline-block;">
                   	亲，您暂时没有订阅任何邮件哦！
                </span>
                <?php }?>
                </div>
            </div>
            <div class="main products-main" style="border:1px solid #ccc;margin-top:20px;">
                <div class="title font-18" style="font-weight:bold;padding:10px;">
                    华成云商邮件
                </div>
                <div style="padding:10px;">
                <?php if ($_smarty_tpl->tpl_vars['showMailList']->value){?>
                <?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['showMailList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
                    <span style="width:150px;line-height:23px;margin-right:40px;display:inline-block; text-align:center;" id="addMail">
                        <a href="#">
                            <span><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['list']->value['list_name'],11,'...',true);?>
</span>
                        </a>
                        <div style="color:#a2a2a2;height:25px;"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['list']->value['list_description'],11,'...',true);?>
</div>
                        <div style="color:#a2a2a2;height:25px;"><?php echo $_smarty_tpl->tpl_vars['list']->value['system_name'];?>
</div>
                        <?php if ($_smarty_tpl->tpl_vars['list']->value['issubscript']==1){?>
                        <span class="Subscription">已订阅</span>
                        <?php }else{ ?>
                        <input type="button" value="订阅"  onclick="addMail(<?php echo $_smarty_tpl->tpl_vars['list']->value['list_id'];?>
)" />
                        <?php }?>
                   	</span>
                <?php } ?>
                <?php }elseif($_smarty_tpl->tpl_vars['getUserMail']->value){?>
                <?php  $_smarty_tpl->tpl_vars['user'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['user']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['getUserMail']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['user']->key => $_smarty_tpl->tpl_vars['user']->value){
$_smarty_tpl->tpl_vars['user']->_loop = true;
?>
                	<span style="width:150px;line-height:23px;margin-right:40px;display:inline-block; text-align:center;" id="addMail">
                        <a href="#">
                            <span><?php echo $_smarty_tpl->tpl_vars['user']->value['list_name'];?>
</span>
                        </a>
                        <div style="color:#a2a2a2;height:25px;"><?php echo $_smarty_tpl->tpl_vars['user']->value['list_description'];?>
</div>
                        <div style="color:#a2a2a2;height:25px;"><?php echo $_smarty_tpl->tpl_vars['user']->value['system_name'];?>
</div>
                        <?php if ($_smarty_tpl->tpl_vars['user']->value['issubscript']==1){?>
                        <span class="Subscription">已订阅</span>
                        <?php }else{ ?>
                        <input type="button" value="订阅"  onclick="addMail(<?php echo $_smarty_tpl->tpl_vars['user']->value['list_id'];?>
)" />
                        <?php }?>
                   	</span>
                <?php } ?>
                <div class="main products-main" align="center"><?php echo $_smarty_tpl->tpl_vars['page_str']->value;?>
</div>
                <?php }else{ ?>
                <span style="margin-right:40px;display:inline-block;">
                   	亲，您暂时没有可以订阅的邮件哦！
                </span>
                <?php }?>
                </div>
            </div>
           <div class="main products-main" align="center"><?php echo $_smarty_tpl->tpl_vars['pagestr']->value;?>
</div>
        </div>
    </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html>
<?php }} ?>