/*********转运中心美国邮编分区JS*******
auth : guanyongjun
date : 2014-05-28
*/

//搜索入口
$("#search").click(function() {
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=countriesUsazone&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check() {
	var ow_zip_code = ow_zone = transitId = "";
	ow_zip_code = $.trim($("#ow_zip_code").val());
	ow_zone		= $.trim($("#ow_zone").val());
	transitId	= $.trim($("#transitId").val());
	if(transitId == "") {
		alertify.error("转运中心不能不选！");
		$("#transitId").focus();
		return false;
	}
	if(ow_zip_code == "") {
		alertify.error("邮编不能为空！");
		$("#ow_zip_code").focus();
		return false;
	}
	if(ow_zone == "") {
		alertify.error("分区名称不能为空！");
		$("#ow_zone").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=countriesUsazone&act=addCountriesUsazone";
	var data = {"ow_zip_code":ow_zip_code,"ow_zone":ow_zone,"transitId":transitId};
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

//编辑入口
function edit_check() {
	var ow_zip_code = ow_zone = transitId = "";
	ow_zip_code = $.trim($("#ow_zip_code").val());
	ow_zone		= $.trim($("#ow_zone").val());
	transitId	= $.trim($("#transitId").val());
	id			= $("#act-id").val();
	if(transitId == "") {
		alertify.error("转运中心不能不选！");
		$("#transitId").focus();
		return false;
	}	
	if(ow_zip_code == "") {
		alertify.error("邮编不能为空！");
		$("#ow_zip_code").focus();
		return false;
	}
	if(ow_zone == "") {
		alertify.error("分区名称不能为空！");
		$("#ow_zone").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=countriesUsazone&act=updateCountriesUsazone";
	var data = {"id":id,"ow_zip_code":ow_zip_code,"ow_zone":ow_zone,"transitId":transitId};
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
function del_info(id) {
	var url  = web_url + "json.php?mod=countriesUsazone&act=delCountriesUsazone";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
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