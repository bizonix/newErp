/*********API接口调用统计JS*******
auth : guanyongjun
date : 2014-07-11
*/

//搜索入口
$("#search").click(function(){
	var apiId = hurl = surl = timeNodeStr = "";
	apiId 		= $.trim($("#apiId").val());
	timeNodeStr = timeStr();
	hurl  		= "index.php?mod=apiVisitStat&act=index";
	if(apiId != '0') {
		surl += "&apiId="+apiId;
	}
	if(timeNodeStr) {
		surl += timeNodeStr;
	}
	if(timeNodeStr !== false && timeNodeStr == "" && surl == "") {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	if(surl != "") {
		window.location.href = hurl + surl;
	}
});

//页面后加载
$(function(){
	select_default_inti("apiId");
});