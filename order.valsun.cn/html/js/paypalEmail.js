$(document).ready(function(){
	$("#submitform").click(function(){
		var hidden = $("#hidden").val();
		//alert(hidden);
		if(hidden=="add"){
			var obj_val = $("#emailnames").val();
			if(obj_val==""){
				$("#errorLog").html("邮箱不能为空！");
				$("#successLog").html("");
				return false;
			}
			var accounts = document.getElementById("accounts").options;
			var account = new Array();
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
			//alert(account);
			$.ajax({
				type    :"POST",
				url     :"json.php?act=addPaypalEmail&mod=paypalEmail&jsonp=1",
				dataType:"json",
				data    :{"accounts":account,"emails":obj_val},
				success :function(msg){
				
					if(msg.errCode==0){
						$("#successLog").html("添加成功！");
						window.location.href = "index.php?act=paypalEmail&mod=paypalEmail";
					}else{
						$("#errorLog").html(msg.errMsg);
						$("#successLog").html("");
					}
				}
			});
		}else if(hidden=="modify"){
			var email = $("#emailname").val();
			var enable = $("#enable").val();
			//alert(enable);
			var accounts = document.getElementById("account").options; 
			var id = $("#tid").val();
			for(var i=0;i<accounts.length;i++){
				if(accounts[i].selected == true){
					var account = accounts[i].value;
				}
			}
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
			$.ajax({
				type    :"POST",
				url     :"json.php?act=paypalEmailModify&mod=paypalEmail&jsonp=1",
				dataType:"json",
				data    :{"account":account,"email":email,"enable":enable,"id":id},
				success :function(msg){
				
					if(msg.errCode==0){
						$("#successLog").html("修改成功！");
						window.location.href = "index.php?act=paypalEmail&mod=paypalEmail";
					}else{
						$("#errorLog").html(msg.errMsg);
						$("#successLog").html("");
					}
				}
			});
		}
	});
});
function del(id){
	if(confirm("你确定要删除此条记录！")){
		$.ajax({
			type    :"POST",
			url     :"json.php?act=paypalEmailDel&mod=paypalEmail&jsonp=1",
			dataType:"json",
			data    :{"id":id},
			success :function(msg){
				if(msg.errCode==0){
					//window.location.href = "index.php?act=paypalEmail&mod=paypalEmail&data=删除成功！";
					window.location.reload();
				}else{
					$("#errorLog").html(msg.errMsg);
					$("#successLog").html("");
				}
			}
		});
	}
}