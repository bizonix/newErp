/*********运费对比查询JS*******
auth : guanyongjun
date : 2014-02-19
*/

//提交运费查询对比
function check(){
	var ship_add,ship_carrier,ship_country,ship_weight,ship_postcode,ship_tid;
	ship_add 	 	= $.trim($("#ship_add").val());
	ship_carrier 	= $.trim($("#ship_carrier").val());
	ship_country 	= $.trim($("#ship_country").val());
	ship_weight  	= $.trim($("#ship_weight").val());
	ship_postcode	= $.trim($("#ship_postcode").val());
	ship_tid		= $.trim($("#ship_tid").val());
	if (ship_add == "") {
		alertify.error("发货地址不能不选！");
		$("#ship_add").focus();
		return false;
	}
	if (ship_country == "" && ship_add == "1") {
		alertify.error("发往国家不能不选！");
		$("#ship_country").focus();
		return false;
	}
	if (ship_weight == "") {
		alertify.error("重量不能不填写！");
		$("#ship_weight").focus();
		return false;
	}
	if (ship_postcode == "" && ship_add == "2") {
		alertify.error("邮编不能不填写！");
		$("#ship_postcode").focus();
		return false;
	}
	return true;
}

//获取某个发货地址下所有的运输方式信息
function show_channel_list(addId){
	if (addId==0) {
		$("#channelList").html("");
		return false;
	}
	if (addId==2) {
		$("#postcodes").show();
		$("#ship_tids").hide();
	} else if(addId==5) {
		$("#postcodes").hide();
		$("#ship_tids").hide();
	} else {
		$("#postcodes").show();
		$("#ship_tids").show();
	}
	if (addId==5) {
		show_china_list();
	} else {
		show_english_list();
	}
	var url  = web_url + "json.php?mod=transOpenApi&act=getCarrierByAdd";
	var data = {"addId":addId}
	var seled = channelId = "";
	channelId = $('#ship_carrier_id').val();
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (rtn.data!="") {
				var obj		= eval(rtn.data);
				if (obj.length>0) {
					var val		= $("#channelList").html('<select id="ship_carrier" name="ship_carrier"><option value="0">=请选择发货运输方式=</option></select>');
					for (var i=0;i<obj.length;i++) {
						if (channelId==rtn.data[i]['id']) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}					
						$('#ship_carrier').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['carrierNameCn']+"</option>");
						select_default_inti("ship_country");
					}
				} else {
					$("#channelList").html("");
				}
			} else {
				$("#channelList").html("");
			}
		}else {
				alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}

//获取发货地址为中国的全部地区
function show_china_list(){
	$("#countrys").html("");
	var url  = web_url + "json.php?mod=transOpenApi&act=getCountriesChina";
	var data = {"areaId":"all"}
	var seled = channelId = "";
	channelId = $('#ship_country').val();
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (rtn.data!="") {
				var obj		= eval(rtn.data);
				if (obj.length>0) {
					var val		= $("#countrys").html('<select id="ship_country" name="ship_country" class="flexselect"><option value="0">=请选择收货地区=</option></select>');
					for (var i=0;i<obj.length;i++) {
						if (channelId==rtn.data[i]['id']) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}					
						$('#ship_country').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['countryName']+"</option>");
					}
					$("select[class*=flexselect]").flexselect();
					select_default_inti("ship_country");
				} else {
					$("#countrys").html("");
				}
			} else {
				$("#countrys").html("");
			}
		}else {
				alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}

//获取发货地址为国外的全部地区
function show_english_list(){
	$("#countrys").html("");
	var url  = web_url + "json.php?mod=transOpenApi&act=getCountriesStandard";
	var data = {"type":"ALL"}
	var seled = channelId = "";
	channelId = $('#ship_country').val();
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			if (rtn.data!="") {
				var obj		= eval(rtn.data);
				if (obj.length>0) {
					var val		= $("#countrys").html('<select id="ship_country" name="ship_country" class="flexselect"><option value="0">=请选择收货国家=</option></select>');
					for (var i=0;i<obj.length;i++) {
						if (channelId==rtn.data[i]['id']) {
							seled 	= 'selected="selected"';
						} else {
							seled	= '';
						}					
						$('#ship_country').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['countryNameEn']+"--"+rtn.data[i]['countryNameCn']+"</option>");
					}
					$("select[class*=flexselect]").flexselect();
				} else {
					$("#countrys").html("");
				}
			} else {
				$("#countrys").html("");
			}
		}else {
				alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}

//批量上传文件入口
function file_upload(){
	//待定
}

//页面后加载
$(function(){
	select_default_inti("ship_country");
	//$("#sortTable").tablesorter();
	$("#sortTable").tablesorter( {sortList: [[3,0], [4,0]]} ); 
});