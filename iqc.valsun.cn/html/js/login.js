function login(){   
	var username=$("#username").val();
	if(!$.trim(username))
    {
		$("#errormess").html('用户名不能为空');
		$("#username").focus();
		return false;
	}
	var password=$("#password").val();
	if(!$.trim(password))
	{
		$("#errormess").html('密码不能为空');
		$("#password").focus();
		return false;
	}	

	$.post('json.php?mod=login&act=login',{"username":username,"password":password},function (msg){
			//console.log(msg.data);
			if(typeof(msg.data.errCode) != "undefined"){
				$("#errormess").html(msg.data.errMsg);
				$("#username").focus();
				return false;
			}
			window.location.href = msg.data.url;
			//window.location.href = "index.php?mod=product&act=getPcList";
		},"jsonp");
		
	return false;
}
$(function(){
	$('#reset').click(function(){
		$("#username").val('');
		$("#password").val('');
		$("#username").focus();
	});
})

