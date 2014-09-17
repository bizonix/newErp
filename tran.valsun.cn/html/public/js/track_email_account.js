/*********客服邮件帐号JS*******
auth : guanyongjun
date : 2014-04-10
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if(type!='0' && key!=''){
		window.location.href = "index.php?mod=trackEmailAccount&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var acc_plat = acc_count = acc_user_name = acc_user_email = "";
	acc_plat 	= $.trim($("#acc_plat").val());
	acc_count 	= $.trim($("#acc_count").val());
	acc_user_name	= $.trim($("#acc_user_name").val());
	acc_user_email= $.trim($("#acc_user_email").val());
	if(acc_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#acc_plat").focus();
		return false;
	}
	if(acc_count == "") {
		alertify.error("平台帐号不能为空！");
		$("#acc_count").focus();
		return false;
	}
	if(acc_user_name == "") {
		alertify.error("客服姓名不能为空！");
		$("#acc_user_name").focus();
		return false;
	}
	if(acc_user_email == "") {
		alertify.error("客服邮箱不能为空！");
		$("#acc_user_email").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailAccount&act=addTrackEmailAccount";
	var data = {"acc_plat":acc_plat,"acc_count":acc_count,"acc_user_name":acc_user_name,"acc_user_email":acc_user_email};
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
	var id = acc_plat = acc_count = acc_user_name = acc_user_email = "";
	id 			= $.trim($("#act-id").val());
	acc_plat 	= $.trim($("#acc_plat").val());
	acc_count 	= $.trim($("#acc_count").val());
	acc_user_name	= $.trim($("#acc_user_name").val());
	acc_user_email= $.trim($("#acc_user_email").val());
	if(acc_plat == "") {
		alertify.error("平台名称不能为空！");
		$("#acc_plat").focus();
		return false;
	}
	if(acc_count == "") {
		alertify.error("平台帐号不能为空！");
		$("#acc_count").focus();
		return false;
	}
	if(acc_user_name == "") {
		alertify.error("客服姓名不能为空！");
		$("#acc_user_name").focus();
		return false;
	}
	if(acc_user_email == "") {
		alertify.error("客服邮箱不能为空！");
		$("#acc_user_email").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackEmailAccount&act=updateTrackEmailAccount";
	var data = {"id":id,"acc_plat":acc_plat,"acc_count":acc_count,"acc_user_name":acc_user_name,"acc_user_email":acc_user_email};
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
	var url  = web_url + "json.php?mod=trackEmailAccount&act=delTrackEmailAccount";
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