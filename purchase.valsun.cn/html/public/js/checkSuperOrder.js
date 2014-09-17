var web_api = "http://order.valsun.cn/openapi.php?jsonp=1&";
function audit(dom){
   var self = $(dom);
   var nextButton = self.next().eq(0);//拦截按钮
   var omOrderdetailId = self.data("omorderdetailid");
   var omOrderId = self.data("omorderid");
   var sku = self.data("sku");
   var data = {};
   data.omOrderdetailId = omOrderdetailId;
   data.omOrderId = omOrderId;
   data.sku = sku;
   data.type = "audit";
   var  url = web_api+"mod=superOrder&act=auditOrder"; 
   $.get(url,{ "data":data },function(rtn){
	   console.log(rtn);
	   if(rtn.errCode == "001"){
		   alertify.success(rtn.errMsg);
		   self.hide();
		   nextButton.show();
	   }else if(rtn.errCode == "111"){
		   alertify.success(rtn.errMsg);
		   //modify by wxy 2013/09/16
		   var table = self.closest("tbody");
		   	table.find("button").each(function(index){
		   		var self = $(this);
		   		self.hide();
		   	});	

//		   setTimeout("window.location.reload();",3000);
	   }else{
		   alertify.error(rtn.errMsg);
	   }
   },"jsonp");
}
function back_audit(dom){
	   var self = $(dom);
	   var befButton = self.prev().eq(0);
	   var omOrderdetailId = self.data("omorderdetailid");
	   var omOrderId = self.data("omorderid");
	   var  url = web_api+"mod=superOrder&act=auditOrder"; 
	   var data = {};
	   data.omOrderdetailId = omOrderdetailId;
	   data.omOrderId = omOrderId;
	   data.type = "back_audit";
	   $.get(url,{ "data":data },function(rtn){
		   console.log(rtn);
		   if(rtn.errCode == "001"){
			   alertify.success(rtn.errMsg);
			   self.hide();
			   befButton.show();
		   }else if(rtn.errCode == "111"){
			   alertify.success(rtn.errMsg);
			   //modify by wxy 2013/09/16
			   var table = self.closest("tbody");
			   	table.find("button").each(function(index){
			   		var self = $(this);
			   		self.hide();
			   	});	
			   
//			   setTimeout("window.location.reload();",3000);
		   }else{
			   alertify.error(rtn.errMsg);
		   }
	   },"jsonp");
}
$("#search").click(function(){
	var ser_sku = $(".ser_sku").val();
	var recordNumber = $.trim($("#recordNumber").val());
	var ser_timetype = $.trim($(".ser_timetype").val());
	var startTime = $.trim($(".startTime").val());
	var endTime = $.trim( $(".endTime").val());
	if(ser_timetype != '' && startTime == ""){
		alertify.error("起始时间不应为空！");
		return;
	}
	if(ser_timetype != '' && endTime == ""){
		alertify.error("结束时间不应为空！");
		return;
	}
	if(ser_timetype != '' && endTime != "" && startTime != '' ){
	var	start = new Date(startTime).getTime();
	var	end = new Date(endTime).getTime();
		if(start > end){
			alertify.error("开始时间不应大于结束时间！");
			return;
		}
	}
	var url = "http://purchase.valsun.cn/index.php?mod=purchaseOrder&act=checkSuperOrder";
	url +="&ser_sku="+ser_sku+"&recordNumber="+recordNumber+"&ser_timetype="+ser_timetype;
	url +="&startTime="+startTime+"&endTime="+endTime;
	window.location.href = url;
});
$(".servar").keydown(function(e){
	if(e.keyCode == 13){
		$("#search").trigger("click");
	}
});

/**
 *审核 通过
 */
function skuPass(ebayid, detailid, sku, type, passornot, pcontent)
{
	$.ajax({
		url	: "index.php?mod=purchaseOrder&act=passSku",
		type: "POST",
		dataType : 'json',
		data : "ebayid="+ebayid+"&detailid="+detailid+"&sku="+
			sku+"&type="+type+"&passornot="+passornot+"&pcontent="+pcontent,
		success : function (data){
			if(data.code=='200'){
						alertify.success(data.data.msg);
					} else 
					{
						alertify.error(data.data.msg);
			}
		}
	});
}
/**
 *审核 不通过
 */
function skuNotPass(ebayid, detailid, sku, type, passornot, pcontent)
{
	// prompt dialog
	alertify.prompt("请输入拦截理由!", function (e, str) {
		// str is the input text
		if (e) {
			// user clicked "ok"
			pcontent	= str;
			$.ajax({
				url	: "index.php?mod=purchaseOrder&act=passSku",
				type: "POST",
				dataType : 'json',
				data : "ebayid="+ebayid+"&detailid="+detailid+"&sku="+
					sku+"&type="+type+"&passornot="+passornot+"&pcontent="+pcontent,
				success : function (data){
					if(data.code=='200'){
						alertify.success(data.data.msg);
					} else 
					{
						alertify.error(data.data.msg);
					}
				}
			});
		} else {
		}
	}, "");
	
}






