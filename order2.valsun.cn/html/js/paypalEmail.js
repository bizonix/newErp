$(document).ready(function(){
	$("#addemails").validationEngine({autoHidePrompt:true});
	
	$("#back").click(function(){
		history.back();
	});
	
	$("#submitform").click(function(){
		var hidden = $("#hidden").val();
		if(hidden=="add"){
			
			alertify.confirm("确定添加",function(e){
				if(e){
					var obj_val = $("#emailnames").val();
					if(obj_val==""){
						$("#errorLog").html("邮箱不能为空！");
						$("#successLog").html("");
						return false;
					}
					var accounts    = document.getElementById("accounts").options;
					var account     = new Array();
					for(var i=0;i<accounts.length;i++){
						if(accounts[i].selected == true){
							account.push(accounts[i].value);
						}
					}
					if(account.length==0){
						$("#errorLog").html("请至少选择一个账户！");
						$("#successLog").html("");
						return false;
					}
					var val_arr = obj_val.split(",");
				
					for(var i=0;i<val_arr.length;i++){
						if(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/.test(val_arr[i])){
							
						}else{
							$("#errorLog").html("邮箱格式有误！");
							$("#successLog").html("");
							return false;
						}
					}
					$.ajax({
						type    :"POST",
						url     :"index.php?act=insert&mod=paypalEmail",
						dataType:"json",
						data    :{"accounts":account,"emails":obj_val},
						success :function(msg){
							if(msg.errCode==200){
								$("#successLog").html(msg.errMsg);
								window.location.href = "index.php?act=index&mod=paypalEmail&rc=reset";
							}else{
								$("#errorLog").html(msg.errMsg);
								$("#successLog").html("");
							}
						}
					});
				}
			});
			
			
		}else if(hidden=="modify"){
			
			alertify.confirm("确定修改",function(e){
				if(e){
					var email      = $("#emailname").val();
					var enable     = $("#enable").val();
					var account    = $("#account").val(); 
					var id 		   = $("#tid").val();
					if(email==""){
						$("#errorLog").html("邮箱不能为空！");
						$("#successLog").html("");
						return false;
					}
					if(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/.test(email)){
							
					}else{
						$("#errorLog").html("邮箱格式有误！");
						$("#successLog").html("");
						return false;
					}
					window.location.href    = "index.php?mod=paypalEmail&act=update&email="+email+"&status="+enable+"&account="+account+"&id="+id;
				}
			});
			
		}
	});
});
function del(id){
	if(alertify.confirm("你确定要删除此条记录！",function(e){
		if(e){
			$.ajax({
				type    :"POST",
				url     :"index.php?act=delete&mod=paypalEmail",
				dataType:"json",
				data    :{"id":id},
				success :function(msg){
					console.log(msg);
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
						window.location.href="index.php?mod=paypalEmail&act=index&rc=reset";
					}else{
						$("#errorLog").html(msg.errMsg);
						$("#successLog").html("");
					}
				}
			});
		}
	}));
}