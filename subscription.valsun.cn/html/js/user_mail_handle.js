function cancelMail(obj) {
	jConfirm('亲，确定取消订阅吗？', '请确定',function(res){
	if(res == true) {	//点击确定
		$.ajax({
			type: "GET",
			url: "index.php?mod=MailShow&act=cancelMailList&list_id="+obj,
			dataType: "html",
			success: function(data){
				setTimeout('window.location.reload()');
			}
		});
	} else {	//点击取消
				
	}
	});
}
function addMail(obj) {
	jConfirm('亲，确定订阅该邮件吗？', '请确定',function(res){
	if(res == true) {	//点击确定
		$.ajax({
			type: "GET",
			url: "index.php?mod=MailShow&act=addMailList&list_id=" + obj,
			dataType: "html",
			success: function(data){
				setTimeout('window.location.reload()');
			}
		});
	} else {	//点击取消
				
	}
	});
}