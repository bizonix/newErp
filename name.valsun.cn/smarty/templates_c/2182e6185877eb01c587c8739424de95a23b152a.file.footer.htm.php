<?php /* Smarty version Smarty-3.1.12, created on 2013-09-25 11:36:11
         compiled from "D:\wamp\www\crm.valsun.cn\html\template\v1\footer.htm" */ ?>
<?php /*%%SmartyHeaderCode:2124052425a2b5b3423-68315155%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2182e6185877eb01c587c8739424de95a23b152a' => 
    array (
      0 => 'D:\\wamp\\www\\crm.valsun.cn\\html\\template\\v1\\footer.htm',
      1 => 1380010787,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2124052425a2b5b3423-68315155',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52425a2b5c1b98_21748741',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52425a2b5c1b98_21748741')) {function content_52425a2b5c1b98_21748741($_smarty_tpl) {?>            <div class="footer">
        		<p>版权所有Copyright©深圳市赛维网络科技有限公司 粤IPC备12055809</p>
        	</div>
        </div>
    </div>
<div id="dialog-competence" title="添加/修改用户颗粒权限" style="display:none">
  <p>以下内容均为必填项<hr/></p>
  <p>
  <table>
  <tr><td colspan=3>所属平台：<select id="list-pf" onchange="pf_change()"><option value=0>全部平台</option></select></td></tr>
  <tr><td>选择用户:<br/><select id="list-acc" multiple="multiple" size="4"></select></td><td><img src="./images/arrow_left.png"  id="sel-yes" border="0" title="确认选择" style="cursor:pointer"/><br/><img src="./images/arrow_right.png"  id="sel-no" border="0" title="清空选择" style="cursor:pointer"/></td><td>可见帐号设置:<br/><textarea name="visible-account" id="visible-account" rows="3" readonly="true"></textarea></td></tr>
  <tr><td colspan=3>按住 CTRL+鼠标单击 多选用户到可见帐号</td></tr>
  </table>
  </p>
</div>
<script type="text/javascript">
var web_api	= "<?php echo @WEB_API;?>
";
var user_id = 0;
alertify.labels.ok     = "确定";
alertify.labels.cancel = "取消";
</script>
<script src="js/user_compense.js"></script>
</body>
</html>
<?php }} ?>