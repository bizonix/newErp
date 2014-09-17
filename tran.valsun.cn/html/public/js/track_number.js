/*********跟踪号管理JS*******
auth : guanyongjun
date : 2014-06-05
*/

//搜索入口
$("#search").click(function(){
	var surl 		= "";
	var carrierId 	= $.trim($("#carrierId").val());
	var channelId 	= $.trim($("#nodeListItem").val());
	var selectId 	= $.trim($("#selectId").val());
	var country 	= $.trim($("#country").val());
	var type  		= $.trim($("#type").val());
	var key   		= encodeURIComponent($.trim($("#key").val()));
	var hurl  		= "index.php?mod=trackNumber&act=index";
	if(carrierId != '0') {
		surl += "&carrierId="+carrierId;
	}
	if(channelId != '0') {
		surl += "&channelId="+channelId;
	}
	if(selectId >= 0) {
		surl += "&selectId="+selectId;
	}
	if(country != '') {
		surl += "&country="+country;
	}
	if(type != '0' && key != ''){
		surl += "&type="+type+"&key="+key;
	}
	if(surl=='') {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	window.location.href = hurl+surl;
});

//获取某个运输方式渠道信息
function show_channel_list(carrierId){
	if(carrierId == 0) {
		$("#nodeList").html("");
		return false;
	}
	var url  	= web_url + "json.php?mod=transOpenApi&act=getCarrierChannel";
	var data 	= {"carrierId":carrierId}
	var seled	= channelId = "";
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			if(rtn.data != "") {
				var obj		= eval(rtn.data);
				if(obj.length > 0) {
					var val	= $("#nodeList").html('<select id="nodeListItem" name="nodeListItem"><option value="0">=选择渠道节点=</option></select>');
					for(var i=0; i<obj.length; i++) {
						channelId	= get_url_para('channelId');
						if(channelId == rtn.data[i]['id']) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}
						$('#nodeListItem').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['channelName']+"</option>");
					}
				} else {
					$("#nodeList").html("");
				}
			} else {
				$("#nodeList").html("");
			}
		} else {
				alertify.error(rtn.errMsg);
		}
	}, "jsonp");
}

//添加入口
function add_check(){
	var channelId	= $.trim($("#nodeListItem").val());
	var trackNumber = $.trim($("#trackNumber").val());
	var country	 	= $.trim($("#country").val());
	var carrierId	= $.trim($("#carrierId").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(trackNumber == "" || !(/^[A-Z0-9]{1,30}$/.test(trackNumber))) {
		alertify.error("跟踪号不能为空且填写需正确！");
		$("#trackNumber").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackNumber&act=addTrackNumber";
	var data = {"trackNumber":trackNumber,"carrierId":carrierId,"channelId":channelId,"country":country};
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
	var channelId	= $.trim($("#nodeListItem").val());
	var trackNumber = $.trim($("#trackNumber").val());
	var country 	= $.trim($("#country").val());
	var carrierId	= $.trim($("#carrierId").val());
	var id		 	= $.trim($("#act-id").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(trackNumber == "" || !(/^[A-Z0-9]{1,30}$/.test(trackNumber))) {
		alertify.error("跟踪号不能为空且填写需正确！");
		$("#trackNumber").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackNumber&act=updateTrackNumber";
	var data = {"id":id,"trackNumber":trackNumber,"carrierId":carrierId,"channelId":channelId,"country":country};
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
	var url  = web_url + "json.php?mod=trackNumber&act=delTrackNumber";
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

//批量上传跟踪号入口
function file_upload(){
	var country 	= $.trim($("#country").val());
	var carrierId	= $.trim($("#carrierId").val());
	var channelId	= $.trim($("#nodeListItem").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}	
}

//页面后加载
$(function(){
	var carrierId	= get_url_para('carrierId');
	if(carrierId != "") {
		show_channel_list(carrierId);
	}
	select_default_inti("country");	
});