/*********网站广告统计管理JS*******
auth : guanyongjun
date : 2014-07-21
*/

//搜索入口
$("#search").click(function(){
	var type  		= $.trim($("#type").val());
	var adId  		= $.trim($("#adId").val());
	var key   		= encodeURIComponent($.trim($("#key").val()));
	var timeNodeStr = timeStr();
	var hurl  		= "index.php?mod=webAdStat&act=index";
	var surl 		= "";
	if (adId) {
		surl += "&adId="+adId;
	}
	if (type != '0' && key != '') {
		surl += "&type="+type+"&key="+key;
	}
	if (timeNodeStr) {
		surl += timeNodeStr;
	}
	if (timeNodeStr !== false && timeNodeStr == "" && surl == "") {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	if (surl != "" && timeNodeStr !== false) {
		window.location.href = hurl + surl;
	}
});

//页面后加载
$(function(){
	select_default_inti("adId");
});