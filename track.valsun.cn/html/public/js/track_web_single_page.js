/*********网站单页管理JS*******
auth : guanyongjun
date : 2014-07-16
*/

//搜索入口
$("#search").click(function(){
	var type  = $.trim($("#type").val());
	var key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=webSinglePage&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var topic = content = layer = is_enable = "";
	content		= KE.html();
	topic		= $.trim($("#topic").val());
	is_enable	= $.trim($("#is_enable").val());
	layer		= $.trim($("#layer").val());
	if (topic == "") {
		alertify.error("名称不能为空！");
		$("#topic").focus();
		return false;
	}	
	if (KE.isEmpty()) {
		alertify.error("内容不能为空！");
		KE.focus();
		return false;
	}
	if (layer == "") {
		alertify.error("排序不能为空！");
		$("#layer").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=webSinglePage&act=addWebSinglePage";
	var data = {"topic":topic,"content":content,"is_enable":is_enable,"layer":layer};
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
	var topic = content = is_enable = layer = id = "";
	id	 			= $.trim($("#act-id").val());
	topic			= $.trim($("#topic").val());
	content			= KE.html();
	is_enable		= $.trim($("#is_enable").val());
	layer			= $.trim($("#layer").val());
	if (topic == "") {
		alertify.error("名称不能为空！");
		$("#topic").focus();
		return false;
	}	
	if (KE.isEmpty()) {
		alertify.error("内容不能为空！");
		KE.focus();
		return false;
	}
	if (layer == "") {
		alertify.error("排序不能为空！");
		$("#layer").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=webSinglePage&act=updateWebSinglePage";
	var data = {"id":id,"topic":topic,"content":content,"is_enable":is_enable,"layer":layer};
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
	var url  = web_url + "json.php?mod=webSinglePage&act=delWebSinglePage";
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

//加载可视化编辑器
var KE;
KindEditor.ready(function(K) {
	KE = K.create('textarea[name="content"]', {
	});
});

//页面后加载
$(function(){
	// select_default_inti("topic");
});