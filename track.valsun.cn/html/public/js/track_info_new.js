/*********跟踪号信息JS*******
auth : guanyongjun
date : 2014-01-18
*/

//增加运德物流查询时跳转URL页面功能
function track_open(){
	var data,carrier,tracknum,tracklan,carrierEN;
	carrier 	= $.trim($("#carrierEn").val());
	carrierEN 	= $.trim($("#carrier").val());
	tracknum	= $.trim($("#tracknum").val());
	if(carrier == '') {
		alertify.error('Please select the mode of transport!');
		return false;
	}
	if(tracknum == '' || tracknum == $("#tracknum").attr('data')) {
		if(carrier == '运德物流') {
			alertify.error('Please enter WeDo tracking number!');
		} else {
			alertify.error('Please enter '+carrierEN+' tracking number!');
		}
		$("#tracknum").focus();
		$("#tracknum").addClass('orange-border');
		if(tracknum == $("#tracknum").attr('data')) {
			$("#tracknum").val("");
		}
		return false;
	}
	if(carrier == '运德物流') {
		if(!isWodeNum(tracknum)) {
			alertify.error('Please enter WeDo tracking number!');
			$("#tracknum").focus();
			$("#tracknum").addClass('orange-border');
			// $("#tracknum").val("");
			return false;
		}
	}
	window.location.href = "rest?carrier=" + carrier + "&tracknum=" + tracknum;
	return false;
}

//获取某个跟踪号信息
function track_info(){
	var url,data,carrier,tracknum,tracklan,carrierEN;
	carrier 	= $.trim($("#carrierEn").val());
	carrierEN 	= $.trim($("#carrier").val());
	tracknum	= $.trim($("#tracknum").val());

	if(carrier == '') {
		alertify.error('Please select the mode of transport from the left menu!');
		return false;
	}
	if(tracknum == '' || tracknum == $("#tracknum").attr('data')) {
		if(carrier=='运德物流') {
			alertify.error('Please enter WeDo tracking number!');
		} else {
			alertify.error('Please enter '+carrierEN+' tracking number!');
		}
		$("#tracknum").focus();
		$("#tracknum").val("");
		$("#tracknum").addClass('orange-border');
		return false;
	}
	if(carrier == '运德物流') {
		if(!isWodeNum(tracknum)) {
			alertify.error('Please enter WeDo tracking number!');
			$("#tracknum").focus();
			$("#tracknum").addClass('orange-border');
			return false;
		}
	}
	tracklan	= 1;
	url  		= web_url + "json.php?mod=trackInquiry&act=trackInfo";
	$("#trackLoadTip").html("<img src='./public/img/load.gif' border='0' />&nbsp;&nbsp;Information Loading, please wait...")
	data 		= {"carrier":carrier,"tracknum":tracknum,"tracklan":tracklan};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			var obj	   = rtn['data']['trackInfo'];
			var ext	   = rtn['data']['extInfo'];
			var stat   = 'status';
			var events = '';
			var toDes  = '';
			var exts   = '';
			var sTime  = '';
			var eTime  = '';
			var tTime  = '';
			var day	   = '';
			var hour   = '';
			var val	   = $("#trackInfoList").html("");
			if(ext['fromCounty']) exts += '<p>Origin Service Area：<span>' + ext['fromCounty'] + '</span><p/>';
			if(ext['toCity'] && ext['toCity'] != 'NULL') toDes += ext['toCity'] + ' , ';
			if(ext['toCounty'] && ext['toCounty'] != 'NULL') toDes += ext['toCounty'];
			if(toDes != '') exts += "<p>Destination Service Area：<span>" + toDes + "</span></p>";
			if(ext['realWeight'] && ext['realWeight'] != '0.0000') exts += '<p>Package Weight：<span>' + ext['realWeight'] + 'kg</span></p>';
			if(exts) $("#track_order").html(exts);
			if(ext['toCounty']) $("#toTrackCountry").html('To the country：'+ ext['toCounty']);
			if(ext['userName'] && ext['userEmail']) $("#server-info").html('From：'+ ext['platForm'] +'<span>Customer service：'+ ext['userName'] +' <a href="mailto:' + ext['userEmail'] + '">(' + ext['userEmail'] + ')</a></span>');
			if(typeof(ext['addCode']) != 'undefined' && ext['addCode'] != '') {
				$("#fromTrackCountry").html('From the country：'+ ext['addCode']);
			} else {
				$("#fromTrackCountry").html('From the country：China');
			}
			var trackEn	= ext['trackEn'];
			for (var i=(obj.length-1); i>=0; i--) {
				if (i==(obj.length-1)) {
					sTime		= obj[i]['trackTime'].toString();
					sTime		= sTime.replace(/-/g,"/");
					sTime		= new Date(sTime);
					sTime		= sTime.getTime();
					stat 		= 'status-first';
				} else if(i==0) {
					events 		= obj[i]['event'].toLowerCase();
					if(obj[i]['stat']=='3' || events.indexOf('deliver')!=-1) {
						stat 	= 'status-third';
					} else {
						stat 	= 'status-third';
					}
				} else {
					stat 		= 'status-second';
				}
                val 	= val + "<tr><td width='25%'>"+obj[i]['trackTime']+"</td><td width='10%' class='"+stat+"'></td><td width='25%'>" +decodeURIComponent(obj[i]['postion'])+"</td><td width='40'>"+decodeURIComponent(obj[i]['event'])+"</td></tr>";
				val	   	= val.replace(/(\[object Object])/i,'');
				if (i==0 && (ext['file_cz']!='' && typeof(ext['file_cz'])!='undefined')) {
					$("#pic1").attr('src',ext['file_cz']);
					$("#track_pic1").show();
				}
				if (i==0 && (ext['file_fh']!='' && typeof(ext['file_fh'])!='undefined')) {
					$("#pic2").attr('src',ext['file_fh']);
					$("#track_pic2").show();
				}
				if (i==0) {
					eTime		= obj[i]['trackTime'].toString();
					eTime		= eTime.replace(/-/g,"/");
					eTime		= new Date(eTime);
					eTime		= eTime.getTime();
					tTime		= (sTime-eTime)/(1000*86400);
					day			= parseInt(tTime);
					hour		= Math.ceil((tTime - day)*24);
					if (isNaN(day) || isNaN(hour)) {
						day		= 0;
						hour	= 0;
					}
					$("#consTime").html(ext['addCode'] + ' Time consuming：<span>'+day+' days '+hour+' hours</span>');
				}				
            }
			$("#trackInfoList").html(val);	
		} else {
			alertify.error(rtn.errMsg);
		}
		$("#trackLoadTip").hide();
		if(exts!='') $("#track_orders").show();
		$("#trackInfoLists").show();
		if(typeof(trackEn)!='undefined' && trackEn!='') track_info_en(trackEn,tracknum,tracklan,ext['toCounty']);
	}, "jsonp");
}

//获取某个跟踪号目的地信息
function track_info_en(carrier,tracknum,tracklan,countryEn){
	var url  		= web_url + "json.php?mod=trackInquiry&act=trackInfoEn";
	var data 		= {"carrier":carrier,"tracknum":tracknum,"tracklan":tracklan};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			var obj	   = rtn['data']['trackInfoEn'];
			var stat   = 'status';
			var events = '';
			var toDes  = '';
			var exts   = '';
			var sTime  = '';
			var eTime  = '';
			var tTime  = '';
			var day	   = '';
			var hour   = '';
			var val	   = $("#trackInfoListEn").html("");
			for (var i=(obj.length-1); i>=0; i--) {
				if (i==(obj.length-1)) {
					sTime		= obj[i]['trackTime'].toString();
					sTime		= sTime.replace(/-/g,"/");
					sTime		= new Date(sTime);
					sTime		= sTime.getTime();
					stat 		= 'status-first';
				} else if(i==0) {
					events 		= obj[i]['event'].toLowerCase();
					if(obj[i]['stat']=='3' || events.indexOf('deliver')!=-1) {
						stat 	= 'status-third';
					} else {
						stat 	= 'status-third';
					}
				} else {
					stat 		= 'status-second';
				}
                val 	= val + "<tr><td width='25%'>"+obj[i]['trackTime']+"</td><td width='10%' class='"+stat+"'></td><td width='25%'>" +decodeURIComponent(obj[i]['postion'])+"</td><td width='40'>"+decodeURIComponent(obj[i]['event'])+"</td></tr>";
				val	   	= val.replace(/(\[object Object])/i,'');
				if (i==0) {
					eTime		= obj[i]['trackTime'].toString();
					eTime		= eTime.replace(/-/g,"/");
					eTime		= new Date(eTime);
					eTime		= eTime.getTime();
					tTime		= (sTime-eTime)/(1000*86400);
					day			= parseInt(tTime);
					hour		= Math.ceil((tTime - day)*24);
					if (isNaN(day) || isNaN(hour)) {
						day		= 0;
						hour	= 0;
					}
					if(parseInt(obj[0]['stat']) > 0) $("#consTimeEn").html('Time consuming：<span>'+day+' days '+hour+' hours</span>');
				}				
            }
			if (parseInt(obj[0]['stat']) > 0) {
				$("#trackInfoListEn").html(val);
				$("#trackInfoEn").show();
			}
		} else {
			alertify.error(rtn.errMsg);
		}			
	}, "jsonp");
}

//获取track GET传值
function track_wedo(){
    var carrier,tracknum;
    carrier     = get_url_para('carrier');
    tracknum    = get_url_para('tracknum');
    if(carrier == 'wedo' && tracknum != '') {
        $("#carrier").val("WeDo");
        $("#carrierEN").val("运德物流");
        $("#tracknum").val(tracknum);        
    } else {
        $("#tracknum").val(tracknum);  
	}
	track_info();	
}
	
//运输方式选择
function selCarrier(id){
	var data 		= $("#cid_"+id).attr('data');
	var dataValue	= $("#cid_"+id).html();
	var carrier 	= new Array();
	carrier			= data.split("|");
	$("#carrier").val(carrier[1]);
	$("#carrierEn").val(carrier[0]);
	var objs 		= document.getElementById('carrierLists').getElementsByTagName("a");
	var nums		= objs.length;
	for (var i=0; i<nums; i++) {
		if(objs[i].id=="cid_"+id) {
			objs[i].className = "radio-selected";
		} else {
			objs[i].className = "";
		}
	}
	$("#carrierList").hide();
}

//图层鼠标移出后自动隐藏
$("#carrierList").mouseout(function(){
  $("#carrierList").hide();
});
$("#carrierList").mouseover(function(){
  $("#carrierList").show();
});

//默认值选择
function setDefault(id){
	var def_value,new_value;
	def_value	= $("#"+id).attr('data');
	new_value	= $("#"+id).val();
	if(new_value == '') {
		$("#"+id).val(def_value);
		$("#"+id).removeClass('orange-border')
		$("#"+id).addClass('font-color-c')
	}
	if(new_value == def_value) {
		$("#"+id).val('');
		$("#"+id).addClass('orange-border')
		$("#"+id).removeClass('font-color-c')
	}
}

//显示wedo跟踪号图片
function show_wedo_pic(url){
	$("#dialog-content").html("<img src='./public/img/load.gif' border='0' />&nbsp;&nbsp;Information Loading, please wait...");
	$("#dialog-menu").dialog("option", "title", "Photo Information");
	//$("#dialog-menu").dialog("option", "buttons", [ { text: "Narrow", click: function() { imgToSize(-50); } },{ text: "Enlarge", click: function() { imgToSize(50); } },{ text: "Rotate Right", click: function() { $('#imgBox').rotateRight(-90); } },{ text: "Rotate Left", click: function() { $('#imgBox').rotateRight(90); } } ] );
	// $("#dialog-menu").dialog("option","width",624);
	// $("#dialog-menu").dialog("option","height",460);
	$("#dialog-menu").dialog({position:{my: "center", at: "center", of: window}});
	$("#dialog-menu").dialog("open");
	$("#dialog-content").html("<div class=\"trackicon\"><a class=\"narrow\" href=\"javascript:void(0)\" onclick=\"imgToSize(-50);\"></a><a class=\"enlarge\" href=\"javascript:void(0)\" onclick=\"imgToSize(50);\"></a><a class=\"left\" href=\"javascript:void(0)\" onclick=\"$('#imgBox').rotateRight(90);\"></a><a class=\"right\" href=\"javascript:void(0)\" onclick=\"$('#imgBox').rotateRight(-90);\"></a><a class=\"original\" href=\"javascript:void(0)\" onclick=\"show_wedo_pic('"+url+"');\"></a></div><img id='imgBox' src='"+url+"' border=0'>");
}

//放大显示wedo跟踪号图片
function show_wedo_pics(picId){
	show_wedo_pic($("#"+picId).attr("src"));
}

//删除wedo跟踪号图片
function del_wedo_pic(id){
	// $("#"+id).attr("src","./public/img/nopic.jpg");
	// $("#"+id).attr("onclick","");
	$("#"+id).hide();
}

//显示wedo跟踪号图片
function display_wedo_pic(obj){
	if(obj.className=='Unfold') {
		$("#wedo_pics").show();
		obj.className = 'Collapse';
	} else {
		$("#wedo_pics").hide();
		obj.className = 'Unfold';
	}
}

//放大缩小图片
function imgToSize(size) {
	var img = $("#imgBox");
	var oWidth	= img.width(); //取得图片的实际宽度
	var oHeight	= img.height(); //取得图片的实际高度
	var dWidth	= $("#dialog-menu").dialog("option","width");
	var dHeight	= $("#dialog-menu").dialog("option","height");
	var dWidth	= $("#dialog-menu").dialog("option","width");
	var dHeight	= $("#dialog-menu").dialog("option","height");
	img.width(oWidth + size);
	img.height(oHeight + size/oWidth*oHeight);
	$("#dialog-menu").dialog("option","width","auto");
	$("#dialog-menu").dialog("option","height","auto");
	$("#dialog-menu").dialog({position:{my: "center", at: "center", of: window}});
}

//翻转图片
function imgReverse(arg){
	var img = $("#imgBox");
	if(arg == 'h') {
		img.css({'filter' : 'fliph','-moz-transform': 'matrix(-1, 0, 0, 1, 0, 0)','-webkit-transform': 'matrix(-1, 0, 0, 1, 0, 0)'});
	} else {
		img.css({'filter' : 'flipv','-moz-transform': 'matrix(1, 0, 0, -1, 0, 0)','-webkit-transform': 'matrix(1, 0, 0, -1, 0, 0)'});
	}
}

//(显示/隐藏)运输方式
function showCarrier(){
	var displays = $("#carrierList").css('display');
	if (displays=='none') {
		$("#carrierList").show();
	} else {
		$("#carrierList").hide();
	}
}

//下一页广告滚屏
function nextScreen(num,cid){
	$("#upBtn"+cid).addClass('prv').removeClass('no-prv');
	$("#nextBtn"+cid).addClass('no-next').removeClass('next');
	$("#tradings"+cid).animate({right: '+610px'}, "300");

}

//上一页广告滚屏
function upScreen(num,cid){
	$("#upBtn"+cid).addClass('no-prv').removeClass('prv');
	$("#nextBtn"+cid).addClass('next').removeClass('no-next');
	$("#tradings"+cid).animate({right: '0px'}, "300");
}