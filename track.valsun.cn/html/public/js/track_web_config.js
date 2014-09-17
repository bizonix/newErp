/*********网站后台配置管理JS*******
auth : guanyongjun
date : 2014-07-16
*/

//搜索入口
$("#search").click(function(){
	var type  = $.trim($("#type").val());
	var key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=webConfig&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var cKey = cValue = is_enable = "";
	cKey		= $.trim($("#cKey").val());
	cValue		= $.trim($("#cValue").val());
	is_enable	= $.trim($("#is_enable").val());
	if (cKey == "" || !(/^([A-Z]+_?)*[A-Z]$/.test(cKey))) {
		alertify.error("配置名称不能为空且填写必须正确！");
		$("#cKey").focus();
		return false;
	}	
	if (cValue == "") {
		alertify.error("配置内容不能为空！");
		$("#cValue").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=webConfig&act=addWebConfig";
	var data = {"cKey":cKey,"cValue":cValue,"is_enable":is_enable};
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
	var cKey = cValue = is_enable = id = "";
	id	 			= $.trim($("#act-id").val());
	cKey			= $.trim($("#cKey").val());
	cValue			= $.trim($("#cValue").val());
	is_enable		= $.trim($("#is_enable").val());
	if (cKey == "" || !(/^([A-Z]+_?)*[A-Z]$/.test(cKey))) {
		alertify.error("配置名称不能为空且填写必须正确！");
		$("#cKey").focus();
		return false;
	}	
	if (cValue == "") {
		alertify.error("配置内容不能为空！");
		$("#cValue").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=webConfig&act=updateWebConfig";
	var data = {"id":id,"cKey":cKey,"cValue":cValue,"is_enable":is_enable};
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
	var url  = web_url + "json.php?mod=webConfig&act=delWebConfig";
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
	select_default_inti("cKey");
});