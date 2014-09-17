<?php /* Smarty version Smarty-3.1.12, created on 2013-10-25 16:01:20
         compiled from "/data/web/trans.valsun.cn/html/template/dept.htm" */ ?>
<?php /*%%SmartyHeaderCode:10305960595265d138a975f1-37512789%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2fd3ff74789dc992e1285e47b65e2a1a414ea2b' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/dept.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10305960595265d138a975f1-37512789',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265d138adea42_95670856',
  'variables' => 
  array (
    'pageStr' => 0,
    'g_deptname' => 0,
    'g_mod' => 0,
    'g_act' => 0,
    'g_page' => 0,
    'deptlists' => 0,
    'deptlist' => 0,
    'runmsg' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5265d138adea42_95670856')) {function content_5265d138adea42_95670856($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
    <div class="pathvar">
        您的位置：<a href="index.php?mod=user&act=index">部门信息管理</a>&nbsp;&gt;&gt;&nbsp;部门列表
    </div>
	<div class="pagination"><?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>
</div>
</div>
<div class="servar">
	<form name="form" id="sform" action="" enctype="text/plain" method="get">
        <span>
            用户名：<input type="text" name="deptname" value="<?php echo $_smarty_tpl->tpl_vars['g_deptname']->value;?>
"/>
        </span>
        <span>
        	<input name="mod" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_mod']->value;?>
" />
            <input name="act" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_act']->value;?>
" />
            <input name="page" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['g_page']->value;?>
" />
            <button name="button" type="submit" id="bottom" value="search" />搜索</button>
        </span>
        <span>
            <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=dept&act=add'"/>添加</button>
        </span>
	</form>
</div>
<div class="main">
    <table cellspacing="0" width="100%">
        <tr class="title">
        	<th>编号</th>
            <th>公司</th>
            <th>部门</th>
            <th>部门负责人</th>
            <th>操作</th>
        </tr> 
 		<?php  $_smarty_tpl->tpl_vars['deptlist'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['deptlist']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deptlists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['deptlist']->key => $_smarty_tpl->tpl_vars['deptlist']->value){
$_smarty_tpl->tpl_vars['deptlist']->_loop = true;
?>
        <tr>
            <td><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['company_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_name'];?>
</td>
            <td><?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_principal'];?>
</td>
            <td>
                <a href="index.php?mod=dept&act=modify&did=<?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
">编辑</a> | 
                <a href="javascript:void(0)" onclick="del_dept(<?php echo $_smarty_tpl->tpl_vars['deptlist']->value['dept_id'];?>
)" id="del-btn">删除</a>
            </td>
        </tr>  
		<?php }
if (!$_smarty_tpl->tpl_vars['deptlist']->_loop) {
?>
        <tr>
            <td colspan="12" align="center"><?php echo $_smarty_tpl->tpl_vars['runmsg']->value;?>
</td>
        </tr> 
        <?php } ?>                
    </table>
</div>
<div class="bottomvar">
    <div class="pagination"><?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>

//删除部门
function del_dept(id){
	alertify.confirm("亲,真的要删除当前部门吗？", function (e) {
	if (e) {
		$.post("index.php?mod=dept&act=delete",{"deptId":id},function(rtn){
			if($.trim(rtn) == "ok"){              
				alertify.success("亲,删除成功!");
				//window.location.reload();
			}else {
				 alertify.error("亲,删除失败!");
		   }
		});
	}});
}

</script>      <?php }} ?>