/*********用户开放授权管理JS*******
auth : guanyongjun
date : 2014-03-19
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=userCompetence&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var gid = competence = "";
	gid		= $.trim($("#gid").val());
	competence	= $.trim($("#competence").val());
	if (gid == "") {
		alertify.error("授权用户不能为空！");
		$("#gid").focus();
		return false;
	}
	if (competence=="") {
		alertify.error("授权内容不能为空！");
		$("#competence").focus();
		return false;
	}	
	var url  = web_url + "json.php?mod=userCompetence&act=addUserCompetence";
	var data = {"gid":gid,"competence":competence};
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
	var gid = competence = "";
	gid		= $.trim($("#gid").val());
	competence	= $.trim($("#competence").val());
	if (gid == "") {
		alertify.error("授权用户不能为空！");
		$("#gid").focus();
		return false;
	}
	if (competence=="") {
		alertify.error("授权内容不能为空！");
		$("#competence").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=userCompetence&act=updateUserCompetence";
	var data = {"gid":gid,"competence":competence};
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
	var url  = web_url + "json.php?mod=userCompetence&act=delUserCompetence";
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

//页面后加载
$(function(){
	select_default_inti("gid");
});