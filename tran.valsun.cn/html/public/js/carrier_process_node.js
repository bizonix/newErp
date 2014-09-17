/*********运输方式处理节点管理JS*******
auth : guanyongjun
date : 2014-07-08
*/

//搜索入口
$("#search").click(function(){
	var type = key = carrierId = hurl = surl = "";
	carrierId 	= $.trim($("#carrierId").val());
	type  		= $.trim($("#type").val());
	key   		= encodeURIComponent($.trim($("#key").val()));
	hurl  		= "index.php?mod=carrierProNode&act=index";
	if(carrierId != '0') {
		surl += "&carrierId="+carrierId;
	}
	if(type != '0' && key != ''){
		surl += "&type="+type+"&key="+key;
	}
	if(surl == '') {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	window.location.href = hurl+surl;
});

//添加入口
function add_check(){
	var carrierId = nodeTitle = nodeKey = "";
	nodeTitle		= $.trim($("#nodeTitle").val());
	nodeKey			= $.trim($("#nodeKey").val());
	carrierId		= $.trim($("#carrierId").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(nodeTitle == "") {
		alertify.error("节点名称不能为空！");
		$("#nodeTitle").focus();
		return false;
	}
	if(nodeKey == "" || !(/^([\S]+\s?)*[\S]$/.test(nodeKey))) {
		alertify.error("节点处理关键词不能为空且填写必须正确！");
		$("#nodeKey").focus();
		return false;
	}		
	var url  = web_url + "json.php?mod=carrierProNode&act=addCarrierProNode";
	var data = {"carrierId":carrierId,"nodeTitle":nodeTitle,"nodeKey":nodeKey};
	$.post(url,data,function(res) {
		if(res.errCode == 0) {
			alertify.alert("添加成功！",function() {
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
	var carrierId = nodeTitle = nodeKey = id = "";
	nodeTitle		= $.trim($("#nodeTitle").val());
	nodeKey			= $.trim($("#nodeKey").val());
	carrierId		= $.trim($("#carrierId").val());
	id		 		= $.trim($("#act-id").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(nodeTitle == "") {
		alertify.error("节点名称不能为空！");
		$("#nodeTitle").focus();
		return false;
	}
	if(nodeKey == "" || !(/^([\S]+\s?)*[\S]$/.test(nodeKey))) {
		alertify.error("节点处理关键词不能为空且填写必须正确！");
		$("#nodeKey").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=carrierProNode&act=updateCarrierProNode";
	var data = {"id":id,"carrierId":carrierId,"nodeTitle":nodeTitle,"nodeKey":nodeKey};
	$.post(url,data,function(res) {
		if(res.errCode == 0) {
			alertify.alert("修改成功！",function() {
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
	var url  = web_url + "json.php?mod=carrierProNode&act=delCarrierProNode";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function(e){
		if(e) {
			$.post(url,data,function(res) {
				if(res.errCode == 0) {
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
	select_default_inti("carrierId");	
});