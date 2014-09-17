/*********目的地国家预警管理JS*******
auth : guanyongjun
date : 2014-05-23
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=trackWarnCountry&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var carrier_name = ship_country = ship_id = "";
	carrier_name = $.trim($("#carrier_name").val());
	ship_country = $.trim($("#ship_country").val());
	ship_id		 = $.trim($("#ship_id").val());
	if (ship_id == "") {
		alertify.error("运输方式名不能不选");
		$("#ship_id").focus();
		return false;
	}
	if (ship_country == "") {
		alertify.error("目的地国家名不能为空！");
		$("#ship_country").focus();
		return false;
	}
	if (carrier_name == "") {
		alertify.error("跟踪系统运输方式名不能为空！");
		$("#carrier_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackWarnCountry&act=addTrackWarnCountry";
	var data = {"carrier_name":carrier_name,"ship_country":ship_country,"ship_id":ship_id};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("添加成功！",function(){
				window.location.reload();
			});
		}else {
			 alertify.error(res.errMsg);
		   }
	}, "jsonp");
	return false;
}

//修改入口
function edit_check(){
	var carrier_name = ship_country = ship_id = id = "";
	carrier_name = $.trim($("#carrier_name").val());
	ship_country = $.trim($("#ship_country").val());
	ship_id		 = $.trim($("#ship_id").val());
	id		 	 = $.trim($("#act-id").val());
	if (ship_id == "") {
		alertify.error("运输方式名不能不选");
		$("#ship_id").focus();
		return false;
	}
	if (ship_country == "") {
		alertify.error("目的地国家名不能不选！");
		$("#ship_country").focus();
		return false;
	}
	if (carrier_name == "") {
		alertify.error("跟踪系统运输方式名不能为空！");
		$("#carrier_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=trackWarnCountry&act=updateTrackWarnCountry";
	var data = {"id":id,"carrier_name":carrier_name,"ship_country":ship_country,"ship_id":ship_id};
	$.post(url,data,function(res){
		if(res.errCode == 0){
			alertify.alert("修改成功！",function(){
				window.location.reload();
			});
		}else {
			 alertify.error(res.errMsg);
		   }
	}, "jsonp");
	return false;
}

//删除入口
function del_info(id){
	var url  = web_url + "json.php?mod=trackWarnCountry&act=delTrackWarnCountry";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if(e) {
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

//页面后加载
$(function(){
	select_default_inti("carrier_name");	
});