/*********跟踪号统计JS*******
auth : gyj
date : 2013-12-02
*/

//统计入口
$("#search").click(function(){
	trackStat('table');
});

//统计类型入口
function trackStat(type) {
	var carrierId = channelId = timeNodeStr = statType = is_warn = countryId = 0;
	carrierId  = $.trim($("#carrierId").val());
	countryId  = $.trim($("#countryId").val());
	statType   = $.trim($("#statType").val());
	is_warn    = $.trim($("#is_warn").val());
	if (typeof($("#channelListItem").val())!='undefined') {
		channelId = $("#channelListItem").val();
	}
	if (carrierId==0) {
		alertify.error("请选择要运输方式！");
		$("#carrierId").focus();
		return false;
	}
	if (statType==0) {
		alertify.error("请选择统计类型!");
		$("#statType").focus();
		return false;
	}
	timeNodeStr = timeStr();
	if (!timeNodeStr) {
		return false;
	}
	$("#pic_body").html('');
	var url = data = pic_body = "";
	if (statType=='todayWarnPer') {
		$("#viewTable").html('');
		type = 'pic';
		pic_body	= $("#pic_body").html();
		var maxcount	= 4;
		url	 = web_url + "json.php?mod=transOpenApi&act=getRandTrackNodeCount";
		data = {"carrierId":carrierId};
		$.post(url,data,function(rtn){
			if(rtn.errCode == 0){
				maxcount	= rtn.data;
			}
			for(var i=0;i<maxcount;i++) {
				pic_body	= pic_body + '<span class="stat_pic"><div id="container'+i+'"><img src="./public/img/load.gif" />正在努力载入图形信息...</div></span>';	
			}
			$("#pic_body").html(pic_body);
		},"jsonp");		
	} else {
		$("#pic_body").html('<div id="container"><img src="./public/img/load.gif" />正在努力载入图形信息...</div>');
	}	
	if (type=="table") {
		$("#viewTable").html("<img src='./public/img/load.gif'/>正在努力载入表格信息...");
		url	 = web_url+"json.php?mod=trackWarnStat&act=viewTable"+timeNodeStr;
	} else {
		url	 = web_url+"json.php?mod=trackWarnStat&act=viewPic"+timeNodeStr;
	}
	data = {"carrierId":carrierId,"channelId":channelId,"statType":statType,"is_warn":is_warn,"countryId":countryId};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (type=='table') {
				$("#viewTable").html(rtn.data);
				if (statType!='todayWarnPer') trackStat('pic');
			} else {
				var obj = eval(rtn.data);
			}
		} else {
			alertify.error(rtn.errMsg);
		}
	},"jsonp");	
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
					var val		= $("#channelList").html('<select id="channelListItem"><option value="0">=全部运输渠道节点=</option></select>');
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

//页面后加载
$(function(){
	select_default_inti("countryId");	
});