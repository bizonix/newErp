<?php /* Smarty version Smarty-3.1.12, created on 2013-10-25 16:01:23
         compiled from "/data/web/trans.valsun.cn/html/template/deptAdd.htm" */ ?>
<?php /*%%SmartyHeaderCode:17377868035265d139a6ea78-24494018%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f3906e19ee7926f18ada3f666407d82ec6e16641' => 
    array (
      0 => '/data/web/trans.valsun.cn/html/template/deptAdd.htm',
      1 => 1382521420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17377868035265d139a6ea78-24494018',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5265d139a97819_56167127',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5265d139a97819_56167127')) {function content_5265d139a97819_56167127($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=dept&act=index">部门信息管理</a>&nbsp;&gt;&gt;&nbsp;添加岗位
    </div>     
    </div>          
    <div class="main">
    <h1>添加部门资料</h1>
    <form onSubmit="return check()">
        <table width="90%" border="0" cellpadding="0" cellspacing="0" >     
            <tr>
              <td align="right">部门名称：</td>
                <td align="left">
                <input type="text" name="deptname" id="deptname" value="" size="35" maxlength="30"/>
                <span class="red">*</span>
                </td>
            </tr>
			 <tr>
              <td align="right">部门负责人：</td>
                <td align="left">
                <input type="text" name="principal" id="principal" value="" size="35" maxlength="30"/>
                <span class="red">*</span>
                </td>
            </tr>
            
            <tr>
                <td colspan="4" align="center">
                    <button name="button" type="submit" id="submit-btn" value="search" />提 交</button>
                    <button name="button" type="button" id="bottom" value="history" onclick="location.href='index.php?mod=dept&act=index'"/>返 回</button>
                </td>
            </tr>
        </table>
    </form>
    </div>
    <div class="bottomvar"></div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>

$("#submit-btn").click(function(){
    var deptname,principal;
	deptname	= $.trim($("#deptname").val());
    principal	= $.trim($("#principal").val());
	if(deptname == ''){
		alertify.error("亲,部门名称不能为空!");
		$("#deptname").focus();
		return false;
	}
	if(principal == ''){
		alertify.error("亲,部门负责人不能为空!");
		$("#principal").focus();
		return false;
	}
	
	$("#submit-btn").html("提交中,请稍候...");
	$.post("index.php?mod=dept&act=insert",{"deptname":deptname,"principal":principal},function(rtn){
		if($.trim(rtn) == "ok"){
			alertify.success("亲,部门添加成功,5秒后跳转到首页！"); 
			window.setTimeout(window.location.href = "index.php?mod=dept&act=index",5000);        
		}else {
			$("#submit-btn").html("提 交");
			alertify.error("亲,部门添加失败,请检查数据是否有异常！");        
		}
	});
});
function check(){
	return false;
}

</script><?php }} ?>