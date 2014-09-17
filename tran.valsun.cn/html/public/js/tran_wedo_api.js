/*********开放业务管理JS*******
auth : guanyongjun
date : 2014-03-19
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=wedoApi&act=wedoSn&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//上传文件入口
function file_upload(){
	var upfile = "";
	upfile	= $.trim($("#upfile").val());
	if (upfile == "") {
		alertify.error("上传文件不能为空！");
		$("#upfile").focus();
		return false;
	}	
	return true;
}

//添加入口
function add_check(){
	var gid = wedo_sn = "";
	gid		= $.trim($("#gid").val());
	wedo_sn	= $.trim($("#wedo_sn").val());
	if (gid == "") {
		alertify.error("授权用户不能为空！");
		$("#gid").focus();
		return false;
	}
	if (!/^(([A-Z]{1}[A-Z0-9]{1,2}))$/.test(wedo_sn)) {
		alertify.error("生成规则不能为空或生成规则不对！");
		$("#wedo_sn").focus();
		return false;
	}	
	var url  = web_url + "json.php?mod=wedoApi&act=addWedoSn";
	var data = {"gid":gid,"wedo_sn":wedo_sn};
	$.post (url,data,function(res) {
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
	var gid = wedo_sn = "";
	gid		= $.trim($("#gid").val());
	wedo_sn	= $.trim($("#wedo_sn").val());
	if (gid == "") {
		alertify.error("授权用户不能为空！");
		$("#gid").focus();
		return false;
	}
	if (!/^(([A-Z]{1}[A-Z0-9]{1,2}))$/.test(wedo_sn)) {
		alertify.error("生成规则不能为空或生成规则不对！");
		$("#wedo_sn").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=wedoApi&act=updateWedoSn";
	var data = {"gid":gid,"wedo_sn":wedo_sn};
	$.post (url,data,function(res) {
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
	var url  = web_url + "json.php?mod=wedoApi&act=delWedoSn";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if(res.errCode == 0){
					alertify.alert("删除成功！",function(){
						window.location.reload();
					});
				}else {
					 alertify.error(res.errMsg);
				   }
			}, "jsonp");
		}
	});
}

//导出报表入口
$("#export-info").click(function(){
	var data = new Array();
	var surl = condition = timeNodeStr = "";
	surl	 = "json.php?mod=wedoApi&act=orderExport";
	timeNodeStr = timeStr();
	if (timeNodeStr) condition += timeNodeStr;
	surl	+= condition;
	if (condition!="") {
		$( "#dialog-content" ).html('<tr><td><img src="./public/img/load.gif" border=0 />&nbsp;&nbsp;正在努力为您导出运德物流跟踪号信息...</td></tr>');
		$( "#dialog-menu" ).dialog( "option", "title", "导出运德物流订单跟踪号信息！" );
		$( "#dialog-menu" ).dialog( "open" );
		var xhr	= $.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: surl,
			timeout	: 300000,
			success	: function (rtn){
				if(rtn.data != 'fail'){
					$( "#dialog-content" ).html("<tr><td><a href='"+ rtn.data +"' target='_blank'><font class='font-blue'>亲，数据导出成功，点我下载！</font></a></td></tr>");
				} else {
					alertify.error(rtn.errMsg);
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

//页面后加载
$(function(){
	select_default_inti("gid");
});