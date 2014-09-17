/*********开放运输方式管理JS*******
auth : guanyongjun
date : 2014-07-08
*/

//搜索入口
$("#search").click(function(){
	var type = key = carrierId = hurl = surl = "";
	carrierId 	= $.trim($("#carrierId").val());
	type  		= $.trim($("#type").val());
	key   		= encodeURIComponent($.trim($("#key").val()));
	hurl  		= "index.php?mod=carrierOpen&act=index";
	if(carrierId != '0') {
		surl += "&carrierId="+carrierId;
	}
	if(type != '0' && key != ''){
		surl += "&type="+type+"&key="+key;
	}
	if(surl == '') {
		alertify.error("搜索条件没选或搜索关键词不能为空！");
		return false;
	}
	window.location.href = hurl+surl;
});

//添加入口
function add_check(){
	var carrierId = carrierAbb = carrierEn = carrierIndex = carrierAging = carrierNote = carrierDiscount = "";
	carrierAbb		= $.trim($("#carrierAbb").val());
	carrierEn		= $.trim($("#carrierEn").val());
	carrierIndex	= $.trim($("#carrierIndex").val());
	carrierAging	= $.trim($("#carrierAging").val());
	carrierNote		= $.trim($("#carrierNote").val());
	carrierDiscount	= $.trim($("#carrierDiscount").val());
	carrierId		= $.trim($("#carrierId").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(carrierAbb == "" || !(/^[A-Z_]{1,20}$/.test(carrierAbb))) {
		alertify.error("运输方式简称不能为空且填写要正确！");
		$("#carrierAbb").focus();
		return false;
	}
	if(carrierEn == "" || !(/^[A-Za-z]{1,50}$/.test(carrierEn))) {
		alertify.error("运输方式英文名称不能为空且填写要正确！");
		$("#carrierEn").focus();
		return false;
	}
	if(carrierIndex == "" || !(/^[A-Z]{1}$/.test(carrierIndex))) {
		alertify.error("字母索引不能为空且填写要正确！");
		$("#carrierIndex").focus();
		return false;
	}
	if(carrierDiscount == "") {
		alertify.error("开放折扣不能为空！");
		$("#carrierDiscount").focus();
		return false;
	}	
	var url  = web_url + "json.php?mod=carrierOpen&act=addCarrierOpen";
	var data = {"carrierId":carrierId,"carrierAbb":carrierAbb,"carrierEn":carrierEn,"carrierIndex":carrierIndex,"carrierDiscount":carrierDiscount,"carrierAging":carrierAging,"carrierNote":carrierNote};
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

//修改入口
function edit_check(){
	var carrierId = carrierAbb = carrierEn = carrierIndex = carrierAging = carrierNote = carrierDiscount = id = "";
	carrierAbb		= $.trim($("#carrierAbb").val());
	carrierEn		= $.trim($("#carrierEn").val());
	carrierIndex	= $.trim($("#carrierIndex").val());
	carrierAging	= $.trim($("#carrierAging").val());
	carrierNote		= $.trim($("#carrierNote").val());
	carrierDiscount	= $.trim($("#carrierDiscount").val());
	carrierId		= $.trim($("#carrierId").val());
	id		 		= $.trim($("#act-id").val());
	if(carrierId == "") {
		alertify.error("运输方式不能不选");
		$("#carrierId").focus();
		return false;
	}
	if(carrierAbb == "" || !(/^[A-Z_]{1,20}$/.test(carrierAbb))) {
		alertify.error("运输方式简称不能为空且填写要正确！");
		$("#carrierAbb").focus();
		return false;
	}
	if(carrierEn == "" || !(/^[A-Za-z]{1,50}$/.test(carrierEn))) {
		alertify.error("运输方式英文名称不能为空且填写要正确！");
		$("#carrierEn").focus();
		return false;
	}
	if(carrierIndex == "" || !(/^[A-Z]{1}$/.test(carrierIndex))) {
		alertify.error("字母索引不能为空且填写要正确！");
		$("#carrierIndex").focus();
		return false;
	}
	if(carrierDiscount == "") {
		alertify.error("开放折扣不能为空！");
		$("#carrierDiscount").focus();
		return false;
	}
	var url  = web_url + "json.php?mod=carrierOpen&act=updateCarrierOpen";
	var data = {"id":id,"carrierId":carrierId,"carrierAbb":carrierAbb,"carrierEn":carrierEn,"carrierIndex":carrierIndex,"carrierDiscount":carrierDiscount,"carrierAging":carrierAging,"carrierNote":carrierNote};
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
function del_info(id){
	var url  = web_url + "json.php?mod=carrierOpen&act=delCarrierOpen";
	var data = {"id":id};
	alertify.confirm("真的要删除吗？", function(e){
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

//页面后加载
$(function(){
	select_default_inti("carrierId");	
});