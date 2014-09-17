$(document).ready(function(){
	$("#whIoInvoicesTypeForm").validationEngine({autoHidePrompt:true});

	$("#submitform").click(function(){
		var statusName = $("#statusName").val();
		var statusCode = $("#statusCode").val();
		var statusGroup = $("#statusGroup").val();
		var note = $("#note").val();
		//alert(statusName);
		if(statusName==""||statusCode==""||statusGroup==""){
			return false;
		}
		$.ajax({
			type:"POST",
			url:"json.php?act=addNewStatus&mod=addNewStatus&jsonp=1",
			dataType:"json",
			data:{"statusName":statusName,"statusCode":statusCode,"statusGroup":statusGroup,"note":note},
			success:function(msg){
				//alert(msg.errCode);
				//var data = eval("("+msg+")");
				if(msg.errCode == 0){
					window.location.href = "index.php?mod=LibraryStatus&act=libraryStatusList";
				}else{
					$("#errorLog").html(msg.errMsg);
					
				}
			}
		});
	});
});