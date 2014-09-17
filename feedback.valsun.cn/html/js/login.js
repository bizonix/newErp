{literal}
$("#login-btn").click(function(){
    var username,password;
    username = $.trim($("#username").val());
    password = $.trim($("#password").val());
	if(username == ''){
		$("#tips-username").html("用户名不能为空!");
		$("#username").focus();
		return false;
	}else {
		$("#tips-username").html("");
	}
	if(password == ''){
		$("#tips-password").html("密码不能为空!");
		$("#password").focus();
		return false;
	}else {
		$("#tips-password").html("");
	}
	$("#login-btn").val("登录中,请稍候...");
	$.post("index.php?mod=public&act=userLogin",{"username":username, "password":password},function(rtn){
		if($.trim(rtn) == "ok"){
			alertify.success("亲,登录成功,5秒后跳转到首页！"); 
			window.setTimeout(window.location.href = "index.php?mod=public&act=login",5000);        
		}else {
			$("#login-btn").val("登录");
			alertify.error("亲,登录失败,请检查帐号密码是否输入正确！");        
		}
	});
});
function check(){
	return false;
}
{/literal}