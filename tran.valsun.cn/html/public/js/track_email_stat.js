/*********跟踪邮件管理JS*******
auth : guanyongjun
date : 2014-07-11
*/

//搜索入口
$("#search").click(function(){
	var type = key = hurl = surl = timeNodeStr = "";
	type  		= $.trim($("#type").val());
	key   		= encodeURIComponent($.trim($("#key").val()));
	timeNodeStr = timeStr();
	hurl  		= "index.php?mod=trackEmailStat&act=index";
	if(type != '0' && key != '') {
		surl += "&type="+type+"&key="+key;
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