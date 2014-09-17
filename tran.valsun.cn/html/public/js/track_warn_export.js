/*********跟踪号信息导出JS*******
auth : guanyongjun
date : 2014-01-03
*/

//信息导出
$("#export-xls").click(function(){
	var carrierId  = $.trim($("#carrierId").val());
	var status     = $.trim($("#flag").val());
	if (status=='') status     = -1;
	var timeNodeStr= timeStr();
	var url  = web_url + "json.php?mod=trackWarnExport&act=exportTrackInfo";
	var data = {"carrierId":carrierId,"status":status}
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			location.href = rtn.data;
			//$("#file_url").src = rtn.data;
		} else {
			alertify.error(rtn.errMsg);
		}
	},"jsonp");
});