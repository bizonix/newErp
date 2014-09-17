/*********网站广告管理JS*******
auth : guanyongjun
date : 2014-07-18
*/

//搜索入口
$("#search").click(function(){
	//待定
});

//(获取/失去)焦点样式调整
$("#longs").focus(function(){
	$("#longs").addClass('orange-border');
});
$("#longs").blur(function(){
	$("#longs").removeClass('orange-border');
});
$("#widths").focus(function(){
	$("#widths").addClass('orange-border');
});
$("#widths").blur(function(){
	$("#widths").removeClass('orange-border');
});
$("#heights").focus(function(){
	$("#heights").addClass('orange-border');
});
$("#heights").blur(function(){
	$("#heights").removeClass('orange-border');
});
$("#weights").focus(function(){
	$("#weights").addClass('orange-border');
});
$("#weights").blur(function(){
	$("#weights").removeClass('orange-border');
});
// $("#addStr").focus(function(){
	// $("#addStr").addClass('orange-border');
// });
// $("#addStr").blur(function(){
	// $("#addStr").removeClass('orange-border');
// });

//获取标准国家信息
function track_ship_fee(){
	var url,data,addId,country,longs,widths,heights,unit,unitW,weights;
	addId 		= $.trim($("#addId").val());
	country 	= encodeURIComponent($.trim($("#country_flexselect").val()));
	longs 		= $.trim($("#longs").val());
	widths 		= $.trim($("#widths").val());
	heights 	= $.trim($("#heights").val());
	unit 		= $.trim($("#unit").val());
	unitW 		= $.trim($("#unitW").val());
	weights 	= $.trim($("#weights").val());
	if (addId != '1') {
		alertify.error("address parameter error!");
		$("#addId").focus();
		return false;
	}
	if (country == '') {
		alertify.error("countries parameter error!");
		$("#country_flexselect").focus();
		return false;
	}
	$("#shipFeeList").html('');
	url  		= web_url + "json.php?mod=trackShipFee&act=getShipFee";
	data 		= {"addId":addId,"country":country,"longs":longs,"widths":widths,"heights":heights,"unit":unit,"unitW":unitW,"weight":weights};
	$("#trackLoadTip").html("<img src='./public/img/load.gif' border='0' />&nbsp;&nbsp;Information Loading, please wait...");
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			$("#trackLoadTip").hide();
			$("#shipFees").show();
			var obj	   = rtn.data;
			var tr	   = '';
			var num	   = 5;
			var top    = '';
			var cssStr = '';
			for (var i=0; i<obj.length; i++) {
				top    = 'No'+(i+1);
				if (i>=num) {
					cssStr = 'class="dis_none"';
				} else {
					cssStr = '';
				}
				if (i>2) top = '';
				tr     += '<tr '+cssStr+'><td class="'+top+'"></td><td>'+obj[i]['enName']+'</td><td>'+Math.ceil(obj[i]['totalFee'])+'</td><td>'+obj[i]['aging']+'</td><td>'+obj[i]['note']+'</td></tr>';
			}
			if (obj.length>num) {
				$("#loadMore").show();
				$("#loadMore a").html('load '+(obj.length-num)+' items more');
			} else {
				$("#loadMore").hide();
				$("#loadMore a").html('');
			}
			
			$("#sortTable tbody").html(tr);
			$("#sortTable").trigger("update");
			// show_table_sort();
			reset_top_css();
			return false;
		} else {
			alertify.error(rtn.errMsg);
			$("#trackLoadTip").html('');
			$("#shipFeeList").html('');
			$("#shipFees").hide();
		}
	}, "jsonp");
	return false;
}

//显示更多的运费计算结果
function show_ship_fee_more(){
	$("#shipFeeList").find("tr").removeClass();
	$("#loadMore").hide();
}

//table重新排序
function show_table_sort(){
	$("#sortTable thead tr th.header").click();
}

//排序样式重置
function reset_top_css(){
	$("#sortTable thead tr th.header").addClass("headerSortDown");
}

//页面后加载
$(function(){
	getWebAd(4,"shipFeeAd2");
	getWebAd(8,"shipFeeAd1");
	getWebAd(10,"shipFeeAd3");
	$("#sortTable").tablesorter({
		sortList : [[2,0]],
		headers : {0:{sorter:false},1:{sorter:false},3:{sorter:false},4:{sorter:false}}
	});
	$(".flexselect").flexselect({
	});
	$("#country_flexselect").val("");
	$("#country_flexselect").focus(function(){
		$("#country_flexselect").addClass('orange-border');
		$("#country_flexselect").keyup();
	});
	$("#country_flexselect").blur(function(){
		$("#country_flexselect").removeClass('orange-border');
	});
	$("#country_flexselect").keyup(function(){
		if ($.trim($("#country_flexselect").val())=="") {
			$(".flexselect_dropdown").hide();
		} else {
			$(".flexselect_dropdown").show();
		}
	});
	// select_default_inti("country");
});