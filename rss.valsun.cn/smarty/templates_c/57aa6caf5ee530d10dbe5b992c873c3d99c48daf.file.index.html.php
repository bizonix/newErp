<?php /* Smarty version Smarty-3.1.12, created on 2014-03-31 10:25:49
         compiled from "D:\Workspace\PHP\mail_subscription\html\template\v1\index.html" */ ?>
<?php /*%%SmartyHeaderCode:34475328f5c4e5f0c8-39272987%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '57aa6caf5ee530d10dbe5b992c873c3d99c48daf' => 
    array (
      0 => 'D:\\Workspace\\PHP\\mail_subscription\\html\\template\\v1\\index.html',
      1 => 1396232747,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '34475328f5c4e5f0c8-39272987',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5328f5c4ee23b2_15412783',
  'variables' => 
  array (
    'showUserMail' => 0,
    'mail' => 0,
    'showMailList' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5328f5c4ee23b2_15412783')) {function content_5328f5c4ee23b2_15412783($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("mailNavLocation.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            <div class="main products-main" style="border:1px solid #ccc;" id="content">
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
                            <span><?php echo $_smarty_tpl->tpl_vars['list']->value['list_name'];?>
</span>
                        </a>
                        <div style="color:#a2a2a2;height:45px;"><?php echo $_smarty_tpl->tpl_vars['list']->value['list_description'];?>
</div>
                        <?php if ($_smarty_tpl->tpl_vars['list']->value['issubscript']==1){?>
                        <span class="Subscription">已订阅</span>
                        <?php }else{ ?>
                        <input type="button" value="订阅"  onclick="addMail(<?php echo $_smarty_tpl->tpl_vars['list']->value['list_id'];?>
)" />
                        <?php }?>
                   	</span>
                <?php } ?>
                <?php }else{ ?>
                <span style="margin-right:40px;display:inline-block;">
                   	亲，您暂时没有可以订阅的邮件哦！
                </span>
                <?php }?>
                </div>
            </div>
        </div>
    </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</body>
</html>
<?php }} ?>