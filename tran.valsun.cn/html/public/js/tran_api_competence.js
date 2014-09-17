/*********API开放授权管理JS*******
auth : guanyongjun
date : 2014-03-19
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=apiCompetence&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var apiUid = apiName = apiMaxCount = apiEnable = "";
	var check_arr = [],apiArr = []; 
	apiUid		= $.trim($("#apiUid").val());
	apiName		= $.trim($("#apiName").val());
	apiMaxCount	= $.trim($("#apiMaxCount").val());
	apiEnable	= $.trim($("#apiEnable").val());
	apiArr		= $('input[id="apiValue"]:checked');
	if (apiUid == "") {
		alertify.error("授权用户不能为空！");
		$("#apiUid").focus();
		return false;
	}
	if (apiName == "" || !(/^([A-Za-z]+_?)*[A-Za-z]$/.test(apiName))) {
		alertify.error("API名称不能为空且填写必须正确！");
		$("#apiName").focus();
		return false;
	}
	if (apiArr.length == 0) {
		alertify.error("API授权内容不能不选!");
		return false;
	} else {
		$.each(apiArr,function(i,item){
			check_arr.push($(item).val());
		});
	}
	if (apiMaxCount == "") {
		alertify.error("API调用次数不能为空！");
		$("#apiMaxCount").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=apiCompetence&act=addApiCompetence";
	var data = {"apiUid":apiUid,"apiName":apiName,"apiValue":check_arr,"apiMaxCount":apiMaxCount,"apiEnable":apiEnable};
	$.post(url,data,function(res) {
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
	var apiUid = apiName = apiMaxCount = apiEnable = id = "";
	var check_arr = [],apiArr = [];
	id	 			= $.trim($("#act-id").val());
	apiUid			= $.trim($("#apiUid").val());
	apiName			= $.trim($("#apiName").val());
	apiMaxCount		= $.trim($("#apiMaxCount").val());
	apiEnable		= $.trim($("#apiEnable").val());
	apiTokenExpire	= $.trim($("#apiTokenExpire").val());
	apiArr		= $('input[id="apiValue"]:checked');
	if (apiUid == "") {
		alertify.error("授权用户不能为空！");
		$("#apiUid").focus();
		return false;
	}
	if (apiName == "" || !(/^([A-Za-z]+_?)*[A-Za-z]$/.test(apiName))) {
		alertify.error("API名称不能为空且填写必须正确！");
		$("#apiName").focus();
		return false;
	}
	if (apiArr.length == 0) {
		alertify.error("API授权内容不能不选!");
		return false;
	} else {
		$.each(apiArr,function(i,item){
			check_arr.push($(item).val());
		});
	}
	if (apiMaxCount == "") {
		alertify.error("API调用次数不能为空！");
		$("#apiMaxCount").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=apiCompetence&act=updateApiCompetence";
	var data = {"id":id,"apiUid":apiUid,"apiName":apiName,"apiValue":check_arr,"apiMaxCount":apiMaxCount,"apiEnable":apiEnable,"apiTokenExpire":apiTokenExpire};
	$.post(url,data,function(res) {
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
	var url  = web_url + "json.php?mod=apiCompetence&act=delApiCompetence";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if(res.errCode == 0){
					alertify.alert("删除成功！",function() {
						window.location.reload();
					});
				} else {
					alertify.error(res.errMsg);
				}
			}, "jsonp");
		}
	});
}

//页面后加载
$(function(){
	select_default_inti("apiUid");
});