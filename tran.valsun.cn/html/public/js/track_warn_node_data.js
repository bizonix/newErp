/*********预警节点数据管理JS*******
auth : guanyongjun
date : 2014-05-16
*/

//搜索入口
$("#search").click(function(){
	var type = key = hurl = surl = "";
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	hurl  = "index.php?mod=trackWarnNodeData&act=index";
	if (type!='0' && key!=''){
		surl += "&type="+type+"&key="+key;
	}
	if (surl=='') {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	window.location.href = hurl+surl;
});

//修改入口
function edit_check(){
	var nodeId 	= aging = country = is_auto = id = "";
	nodeId 	 	= $.trim($("#nodeId").val());
	country 	= $.trim($("#country").val());
	aging	 	= $.trim($("#aging").val());
	is_auto	 	= $.trim($('input[name="is_auto"]:checked').val());
	id		 	= $.trim($("#act-id").val());
	if (nodeId == "") {
		alertify.error("节点名称不能为空！");
		$("#nodeId").focus();
		return false;
	}
	if (country == "") {
		alertify.error("国家名不能为空！");
		$("#country").focus();
		return false;
	}
	if (aging == "" || aging <= 0) {
		alertify.error("节点时效不能为空或为小于等于0！");
		$("#aging").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackWarnNodeData&act=updateTrackWarnNodeData";
	var data = {"id":id,"nodeId":nodeId,"aging":aging,"country":country,"is_auto":is_auto};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("修改成功！",function(){
				window.location.reload();
			});
		}else {
			 alertify.error(res.errMsg);
		   }
	}, "jsonp");
	return false;
}

//删除入口
function del_info(id){
	var url  = web_url + "json.php?mod=trackWarnNodeData&act=delTrackWarnNodeData";
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