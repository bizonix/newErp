/*********小语种国家管理JS*******
auth : guanyongjun
date : 2014-07-08
*/

//搜索入口
$("#search").click(function(){
	var type  = $.trim($("#type").val());
	var key   = encodeURIComponent($.trim($("#key").val()));
	if (type!='0' && key!=''){
		window.location.href = "index.php?mod=countriesSmall&act=index&type="+type+"&key="+key;
	} else {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
});

//添加小语种
function add_check(){
	var small_name	= $.trim($("#small_name").val());
	var en_name		= $.trim($("#en_name").val());
	var code_name	= $.trim($("#code_name").val());
	if (small_name == "") {
		alertify.error("小语种国家名称不能为空！");
		$("#small_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("标准国家英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=countriesSmall&act=addCountriesSmall";
	var data = {"small_name":small_name,"en_name":en_name,"code_name":code_name};
	$.post(url,data,function(res){
		if (res.errCode == 0) {
			alertify.alert("添加成功！",function(){
				window.location.reload();
			});
		}else {
			alertify.error(res.errMsg);
		}
	}, "jsonp");
	return false;
}

//修改小语种
function edit_check(){
	var small_name	= $.trim($("#small_name").val());
	var en_name		= $.trim($("#en_name").val());
	var code_name	= $.trim($("#code_name").val());
	var id			= $("#act-id").val();
	if (small_name == "") {
		alertify.error("小语种国家名称不能为空！");
		$("#small_name").focus();
		return false;
	}
	if (en_name == "") {
		alertify.error("标准国家英文名称不能为空！");
		$("#en_name").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=countriesSmall&act=updateCountriesSmall";
	var data = {"id":id,"small_name":small_name,"en_name":en_name,"code_name":code_name};
	$.post(url,data,function(res){
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
	var url  = web_url + "json.php?mod=countriesSmall&act=delCountriesSmall";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function (e) {
		if (e) {
			$.post(url,data,function(res){
				if (res.errCode == 0) {
					alertify.alert("删除成功！",function(){
						window.location.reload();
					});
				} else {
					alertify.error(res.errMsg);
				}
			}, "jsonp");
		}
	});
}

//批量上传文件入口
function file_upload(){
	//待定
}

//页面后加载
$(function(){
	select_default_inti("en_name");	
});