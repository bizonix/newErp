/*********转运中心管理JS*******
auth : guanyongjun
date : 2014-05-28
*/

//搜索入口
$("#search").click(function(){
	type  = $.trim($("#type").val());
	key   = encodeURIComponent($.trim($("#key").val()));
	if(type!='0' && key!=''){
		window.location.href = "index.php?mod=transitCenter&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加入口
function add_check(){
	var cn_name = en_name = "";
	cn_name = $.trim($("#cn_name").val());
	en_name = $.trim($("#en_name").val());
	if (cn_name == "") {
		alertify.error("中文名称不能为空！");
		$("#cn_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=transitCenter&act=addTransitCenter";
	var data = {"cn_name":cn_name,"en_name":en_name};
	$.post(url,data,function(res){
		if(res.errCode == 0){
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
	var cn_name = en_name = "";
	id		= $("#act-id").val();
	cn_name = $.trim($("#cn_name").val());
	en_name = $.trim($("#en_name").val());
	if (cn_name == "") {
		alertify.error("中文名称不能为空！");
		$("#cn_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=transitCenter&act=updateTransitCenter";
	var data = {"id":id,"cn_name":cn_name,"en_name":en_name};
	$.post(url,data,function(res){
		if(res.errCode == 0){
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
	var url  = web_url + "json.php?mod=transitCenter&act=delTransitCenter";
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