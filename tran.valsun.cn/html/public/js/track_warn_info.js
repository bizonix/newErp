/*********跟踪号预警信息JS*******
auth : guanyongjun
date : 2013-12-02
*/

//实时跟踪
$("#track-info").click(function(){
	var carrierId  = $.trim($("#carrierId").val());
	var type  = $.trim($("#type").val());
	var key   = encodeURIComponent($.trim($("#key").val()));
	if (carrierId==0) {
		alertify.error("请选择要跟踪的运输方式！");
		$("#carrierId").focus();
		return false;
	}
	if (type!='trackNumber') {
		alertify.error("请选择条件为跟踪号！");
		$("#type").focus();
		return false;
	}
	if (key=='') {
		alertify.error("请输入正确的跟踪号！");
		$("#key").focus();
		return false;
	}
	show_track_info(carrierId, key, true, 10000);
});

//搜索入口
$("#search").click(function(){
	var data = new Array();
	var surl = condition = countryId = warnId = is_warn = carrierId = channelId = trackNumber = timeNodeStr = key = "";
	surl	 = "index.php?mod=trackWarnInfo&act=index";
	if (typeof($("#nodeListItem").val())!='undefined') {
		warnId = $("#nodeListItem").val();
		if (warnId != "") {
			is_warn	= $('#is_warn').val();
			condition += "&warnLevel="+warnId+"&is_warn="+is_warn;
		}
	}
	if (typeof($("#channelListItem").val())!='undefined') {
		channelId = $("#channelListItem").val();
		if (channelId != '0') condition += "&channelId="+channelId;
	}
	carrierId  = $.trim($("#carrierId").val());
	countryId  = $.trim($("#countryId").val());
	flag       = $.trim($("#flag").val());
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	timeNodeStr = timeStr();
	if (countryId!='0') condition += "&countryId="+countryId;
	if (timeNodeStr) condition += timeNodeStr;
	if (flag!='') condition += "&status="+flag;
	if (carrierId!='0' && key!=''){
		condition += "&carrierId="+carrierId+"&type="+type+"&key="+key;
	} else if (key!='') {
		condition += "&type="+type+"&key="+key;
	} else if (carrierId!='0') {
		condition += "&carrierId="+carrierId;
	}
	if (timeNodeStr!==false && timeNodeStr=="" && condition=="") {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	surl	+= condition;
	if (condition!="") window.location.href = surl;
});

//导出报表入口
$("#export-info").click(function(){
	var data = new Array();
	var surl = condition = countryId = warnId = is_warn = carrierId = channelId = trackNumber = timeNodeStr = key = "";
	surl	 = "json.php?mod=trackWarnExport&act=exportTrackInfo";
	if (typeof($("#nodeListItem").val())!='undefined') {
		warnId = $("#nodeListItem").val();
		if (warnId != "") {
			is_warn	= $('#is_warn').val();
			condition += "&warnLevel="+warnId+"&is_warn="+is_warn;
		}
	}
	if (typeof($("#channelListItem").val())!='undefined') {
		channelId = $("#channelListItem").val();
		if (channelId != '0') condition += "&channelId="+channelId;
	}
	carrierId	= $.trim($("#carrierId").val());
	countryId  	= $.trim($("#countryId").val());
	flag       	= $.trim($("#flag").val());
	type       	= $.trim($("#type").val());
	key        	= encodeURIComponent($.trim($("#key").val()));
	timeNodeStr = timeStr();
	if (countryId!='0') condition += "&countryId="+countryId;
	if (timeNodeStr) condition += timeNodeStr;
	if (flag!='') condition += "&status="+flag;
	if (carrierId!='0' && key!=''){
		condition += "&carrierId="+carrierId+"&type="+type+"&key="+key;
	} else if (key!='') {
		condition += "&type="+type+"&key="+key;
	} else if (carrierId!='0') {
		condition += "&carrierId="+carrierId;
	}
	if (timeNodeStr!==false && timeNodeStr=="" && condition=="") {
		alertify.error("导出条件没选或导出关键词不能为空！");
		return false;
	}
	surl	+= condition;
	if (condition!="") {
		$( "#dialog-content" ).html('<tr><td><img src="./public/img/load.gif" border=0 />&nbsp;&nbsp;正在努力为您导出跟踪号信息...</td></tr>');
		$( "#dialog-menu" ).dialog( "option", "title", "导出跟踪号信息！" );
		$( "#dialog-menu" ).dialog( "open" );
		var xhr	= $.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: surl,
			timeout	: 600000,
			success	: function (rtn){
				if(rtn.data != 'fail'){
					$( "#dialog-content" ).html("<tr><td><a href='"+ rtn.data +"' target='_blank'><font class='font-blue'>亲，数据导出成功，点我下载！</font></a></td></tr>");
				} else {
					$( "#dialog-content" ).html("<tr><td><font class='font-red'>"+ rtn.errMsg +"</font></td></tr>");
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alertify.error(XMLHttpRequest.status + "-" + textStatus);
				xhr.abort();
				$( "#dialog-menu" ).dialog( "close" );
			}
		});
	}
});

//获取某个跟踪号信息
function show_track_info(tid, trackNumber, realTime, lan){
	if (!realTime) {
		var url  = web_url + "json.php?mod=trackWarnInfo&act=getTrackNumberInfo";
	} else {
		var url  = web_url + "json.php?mod=trackWarnInfo&act=trackNumberInfo";
	}
	$( "#dialog-content" ).html('<tr><td>正在努力为您加载跟踪号详细信息...</td></tr>');
	$( "#dialog-menu" ).dialog( "option", "title", "跟踪号:xxx详细跟踪信息！" );
	$( "#dialog-menu" ).dialog( "option", "buttons", [ { text: "中文", click: function() { show_track_info(tid, trackNumber, true, 10000); } },{ text: "英文", click: function() { show_track_info(tid, trackNumber, true, 1); } } ] );
	$( "#dialog-menu" ).dialog( "open" );
	var data = {"tid":tid,"trackNumber":trackNumber,"lan":lan}
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			var obj	   = rtn['data']['trackInfo'];
			var objC   = rtn['data']['countryInfo'];
			if (lan == 1) {
				var th = "<tr><td class='font-14'>time</td><td class='font-14'>place</td><td class='font-14'>event</td></tr>";
			} else {
				var th = "<tr><td class='font-14'>时间</td><td class='font-14'>处理地点</td><td class='font-14'>事件</td></tr>";
			}
			var thC	   = "<tr><td colspan='3' class='font-14'>以下为目的地国家跟踪信息</td></tr>";
			var val	   = $( "#dialog-content" ).html("");
			for (var i=0; i<obj.length; i++) {
				val	   = val + "<tr><td class='font-12'>"+obj[i]['trackTime']+"</td><td class='font-12'>" +obj[i]['postion']+"</td><td class='font-12'>"+obj[i]['event']+"</td></tr>";
			}
			if(objC.length > 0) {
				val	   = val + thC;
			}
			for (var i=0; i<objC.length; i++) {
				val	   = val + "<tr><td class='font-12'>"+objC[i]['trackTime']+"</td><td class='font-12'>" +objC[i]['postion']+"</td><td class='font-12'>"+objC[i]['event']+"</td></tr>";
			}
			$( "#dialog-content" ).html(th + val);
			$( "#dialog-menu" ).dialog( "option", "title", "跟踪号:"+ trackNumber +" 详细跟踪信息！" );
			$( "#dialog-menu" ).dialog( "open" );
		} else {
			alertify.error(rtn.errMsg);
		}
	}, "jsonp");
}

//获取某个运输方式渠道信息
function show_channel_list(carrierId){
	$("#nodeList").html("");
	if (carrierId==0) {
		$("#channelList").html("");
		return false;
	}
	var url  = web_url + "json.php?mod=transOpenApi&act=getCarrierChannel";
	var data = {"carrierId":carrierId}
	var seled= channelId = "";
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (rtn.data!="") {
				var obj		= eval(rtn.data);
				if (obj.length>0) {
					var val		= $("#channelList").html('<select id="channelListItem"  onchange="show_node_list(this.value)"><option value="0">=选择运输渠道节点=</option></select>');
					for (var i=0;i<obj.length;i++) {
						channelId	= get_url_para('channelId');
						if (channelId==rtn.data[i]['id']) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}					
						$('#channelListItem').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['channelName']+"</option>");
					}
				} else {
					$("#channelList").html("");
				}
			} else {
				$("#channelList").html("");
			}
		}else {
				alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}

//获取某个运输方式节点信息
function show_node_list(channelId){
	if (channelId==0) {
		$("#nodeList").html("");
		return false;
	}
	var carrierId  = $.trim($("#carrierId").val());
	var url  = web_url + "json.php?mod=transOpenApi&act=getTrackNodeList";
	var data = {"carrierId":carrierId,"channelId":channelId}
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (rtn.data!="") {
				var obj		= eval(rtn.data);
				if (obj.length>0) {
					var seled	= seledno = seledall = "";
					var warnId	= get_url_para('warnLevel');
					if (warnId == -1) {
						seledno	= 'selected="selected"';
					}
					if (warnId == 0 && warnId!='') {
						seledall= 'selected="selected"';
					}
					show_warn(warnId);
					var val		= $("#nodeList").html('<select id="nodeListItem" onchange="show_warn(this.value)"><option value="">默认</option><option value="-1" '+seledno+'>不预警</option><option value=0 '+seledall+'>全部预警节点</option></select>');
					for (var i=0;i<obj.length;i++) {
						var num	= i+1;
						if (warnId == num) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}
						$('#nodeListItem').append("<option value="+num+" "+seled+">"+rtn.data[i]['nodeName']+"</option>");
					}
				} else {
					$("#nodeList").html("");
				}
			} else {
				$("#nodeList").html("");
			}
		}else {
				alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}

//显示是否预警
function show_warn(id){
	if (id>0) {
		$("#dis_warn").show();
	} else {
		$("#dis_warn").hide();
	}
}

//页面后加载
$(function(){
	carrierId	= get_url_para('carrierId');
	chId		= get_url_para('channelId');
	if (carrierId!="") {
		show_channel_list(carrierId);
	}
	if (chId!="") {
		show_node_list(chId);
	}	
	select_default_inti("countryId");	
});