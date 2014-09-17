/*********开放授权管理JS*******
auth : guanyongjun
date : 2014-03-19
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=userCompetences&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var ucp_title = ucp_item = ucp_content = ucp_pid = "";
	ucp_title 	= $.trim($("#ucp_title").val());
	ucp_item	= $.trim($("#ucp_item").val());
	ucp_content	= $.trim($("#ucp_content").val());
	ucp_pid		= $.trim($("#ucp_pid").val());
	if (ucp_title == "") {
		alertify.error("开放授权名称不能为空！");
		$("#ucp_title").focus();
		return false;
	}
	if (ucp_item=="") {
		alertify.error("开放授权键名不能为空！");
		$("#ucp_item").focus();
		return false;
	}
	if (ucp_content == "") {
		alertify.error("键名授权内容不能为空！");
		$("#ucp_content").focus();
		return false;
	}
	if (ucp_pid == "") {
		alertify.error("开放授权分类不能为空！");
		$("#ucp_pid").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=userCompetences&act=addUserCompetences";
	var data = {"ucp_title":ucp_title,"ucp_item":ucp_item,"ucp_content":ucp_content,"ucp_pid":ucp_pid};
	$.post (url,data,function(res) {
		if (res.errCode == 0) {
			alertify.alert("添加成功！",function(){
				window.location.reload();
			});
		} else {
			 alertify.error(res.errMsg);
		}
	}, "jsonp");
	return false;
}

//编辑入口
function edit_check(){
	var ucp_title = ucp_item = ucp_content = ucp_pid = id = "";
	ucp_title 	= $.trim($("#ucp_title").val());
	ucp_item	= $.trim($("#ucp_item").val());
	ucp_content	= $.trim($("#ucp_content").val());
	ucp_pid		= $.trim($("#ucp_pid").val());
	id			= $.trim($("#act-id").val());
	if (ucp_title == "") {
		alertify.error("开放授权名称不能为空！");
		$("#ucp_title").focus();
		return false;
	}
	if (ucp_item == "") {
		alertify.error("开放授权键名不能为空！");
		$("#ucp_item").focus();
		return false;
	}
	if (ucp_content == "") {
		alertify.error("键名授权内容不能为空！");
		$("#ucp_content").focus();
		return false;
	}
	if (ucp_pid == "") {
		alertify.error("开放授权分类不能为空！");
		$("#ucp_pid").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=userCompetences&act=updateUserCompetences";
	var data = {"id":id,"ucp_title":ucp_title,"ucp_item":ucp_item,"ucp_content":ucp_content,"ucp_pid":ucp_pid};
	$.post (url,data,function(res) {
		if (res.errCode == 0) {
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
	var url  = web_url + "json.php?mod=userCompetences&act=delUserCompetences";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if(res.errCode == 0){
					alertify.alert("删除成功！",function(){
						window.location.reload();
					});
				}else {
					 alertify.error(res.errMsg);
				   }
			}, "jsonp");
		}
	});
}