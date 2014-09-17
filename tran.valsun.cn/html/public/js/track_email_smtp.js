/*********邮件服务配置JS*******
auth : guanyongjun
date : 2014-04-10
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if(type!='0' && key!=''){
		window.location.href = "index.php?mod=trackEmailSmtp&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var smtp_plat = smtp_count = smtp_user_name = smtp_user_pwd = smtp_host = smtp_port =  "";
	smtp_plat 		= $.trim($("#smtp_plat").val());
	smtp_count 		= $.trim($("#smtp_count").val());
	smtp_host 		= $.trim($("#smtp_host").val());
	smtp_port 		= $.trim($("#smtp_port").val());
	smtp_user_name	= $.trim($("#smtp_user_name").val());
	smtp_user_pwd	= $.trim($("#smtp_user_pwd").val());
	if(smtp_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#smtp_plat").focus();
		return false;
	}
	if(smtp_count == "") {
		alertify.error("平台帐号不能为空！");
		$("#smtp_count").focus();
		return false;
	}
	if(smtp_host == "") {
		alertify.error("服务地址不能为空！");
		$("#smtp_host").focus();
		return false;
	}
	if(smtp_port == "") {
		alertify.error("服务端口不能为空！");
		$("#smtp_port").focus();
		return false;
	}	
	if(smtp_user_name == "") {
		alertify.error("客服姓名不能为空！");
		$("#smtp_user_name").focus();
		return false;
	}
	if(smtp_user_pwd == "") {
		alertify.error("客服邮箱不能为空！");
		$("#smtp_user_pwd").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailSmtp&act=addTrackEmailSmtp";
	var data = {"smtp_plat":smtp_plat,"smtp_count":smtp_count,"smtp_user_name":smtp_user_name,"smtp_user_pwd":smtp_user_pwd,"smtp_host":smtp_host,"smtp_port":smtp_port};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("添加成功！",function(){
				$(":text").val("");
			});
		} else {
			 alertify.error(res.errMsg);
		}
	}, "jsonp");
	return false;
}

//修改入口
function edit_check(){
	var id = smtp_plat = smtp_count = smtp_user_name = smtp_user_pwd = smtp_host = smtp_port = "";
	id 			= $.trim($("#act-id").val());
	smtp_plat 	= $.trim($("#smtp_plat").val());
	smtp_count 	= $.trim($("#smtp_count").val());
	smtp_host 		= $.trim($("#smtp_host").val());
	smtp_port 		= $.trim($("#smtp_port").val());
	smtp_user_name	= $.trim($("#smtp_user_name").val());
	smtp_user_pwd= $.trim($("#smtp_user_pwd").val());
	if(smtp_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#smtp_plat").focus();
		return false;
	}
	if(smtp_count == "") {
		alertify.error("平台帐号不能为空！");
		$("#smtp_count").focus();
		return false;
	}
	if(smtp_host == "") {
		alertify.error("服务地址不能为空！");
		$("#smtp_host").focus();
		return false;
	}
	if(smtp_port == "") {
		alertify.error("服务端口不能为空！");
		$("#smtp_port").focus();
		return false;
	}
	if(smtp_user_name == "") {
		alertify.error("客服姓名不能为空！");
		$("#smtp_user_name").focus();
		return false;
	}
	if(smtp_user_pwd == "") {
		alertify.error("客服邮箱不能为空！");
		$("#smtp_user_pwd").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailSmtp&act=updateTrackEmailSmtp";
	var data = {"id":id,"smtp_plat":smtp_plat,"smtp_count":smtp_count,"smtp_user_name":smtp_user_name,"smtp_user_pwd":smtp_user_pwd,"smtp_host":smtp_host,"smtp_port":smtp_port};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("修改成功！",function(){
				window.location.reload();
			});
		} else {
			 alertify.error(res.errMsg);
		}
	}, "jsonp");
	return false;
}

//删除入口
function del_info(id){
	var url  = web_url + "json.php?mod=trackEmailSmtp&act=delTrackEmailSmtp";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if(e) {
			$.post(url,data,function(res){
				if(res.errCode == 0) {
					alertify.alert("删除成功！",function(){
						window.location.reload();
					});
				} else {
					 alertify.error(res.errMsg);
				}
			}, "jsonp");
		}
	});
}