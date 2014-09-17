/*********跟踪邮件模版JS*******
auth : guanyongjun
date : 2014-04-10
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if(type!='0' && key!=''){
		window.location.href = "index.php?mod=trackEmailTemplate&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var temp_plat = temp_name = temp_title = temp_content = "";
	temp_plat 	= $.trim($("#temp_plat").val());
	temp_name 	= $.trim($("#temp_name").val());
	temp_title	= $.trim($("#temp_title").val());
	temp_content= $.trim($("#temp_content").val());
	if(temp_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#temp_plat").focus();
		return false;
	}
	if(temp_name == "") {
		alertify.error("邮件模版名称不能为空！");
		$("#temp_name").focus();
		return false;
	}
	if(temp_title == "") {
		alertify.error("邮件模版抬头不能为空！");
		$("#temp_title").focus();
		return false;
	}
	if(temp_content == "") {
		alertify.error("邮件模版内容不能为空！");
		$("#temp_content").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailTemplate&act=addTrackEmailTemplate";
	var data = {"temp_plat":temp_plat,"temp_name":temp_name,"temp_title":temp_title,"temp_content":temp_content};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("添加成功！",function(){
				window.location.reload();
			});
		} else {
			 alertify.error(res.errMsg);
		}
	}, "jsonp");
	return false;
}

//修改入口
function edit_check(){
	var id = temp_plat = temp_name = temp_title = temp_content = "";
	id 			= $.trim($("#act-id").val());
	temp_plat 	= $.trim($("#temp_plat").val());
	temp_name 	= $.trim($("#temp_name").val());
	temp_title	= $.trim($("#temp_title").val());
	temp_content= $.trim($("#temp_content").val());
	if(temp_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#temp_plat").focus();
		return false;
	}
	if(temp_name == "") {
		alertify.error("邮件模版名称不能为空！");
		$("#temp_name").focus();
		return false;
	}
	if(temp_title == "") {
		alertify.error("邮件模版抬头不能为空！");
		$("#temp_title").focus();
		return false;
	}
	if(temp_content == "") {
		alertify.error("邮件模版内容不能为空！");
		$("#temp_content").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailTemplate&act=updateTrackEmailTemplate";
	var data = {"id":id,"temp_plat":temp_plat,"temp_name":temp_name,"temp_title":temp_title,"temp_content":temp_content};
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
	var url  = web_url + "json.php?mod=trackEmailTemplate&act=delTrackEmailTemplate";
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