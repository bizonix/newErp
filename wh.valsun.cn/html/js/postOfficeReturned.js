/*
包装扫描
*/
$(document).ready(function(){
	$("#orderid").focus();
	$("#orderid").keypress(function(){
		var e = event||e;
		if(e.keyCode==13||e.keyCode==10){
			var orderid = $.trim(this.value);
			$("#orderid").prop("disabled",true);
			var p_realebayid=/^[1-9]\d+$/;
			var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|EE|EC|LN|LM|RG)\d{9}(CN|HK|DE200|SG|DE)$/;
			if(p_realebayid.test(orderid) || p_eub_trackno.test(orderid)){//compablity with EUB
				$.ajax({
					type : "POST",
					dataType:'json',
					url : 'json.php?mod=postOfficeReturned&act=returnToerp&jsonp=1',
					data : {'orderid':orderid},
					success: function(result){
						$("#orderid").prop("disabled",false);
						console.log(result.data.errMsg);
						//var	result		=	eval("("+result+")");
						if(result.data.errCode==200){
							scanProcessTip(result.data.errMsg,true);
							$("#orderid").val('');
							$("#orderid").focus();
						}else{
							scanProcessTip(result.data.errMsg,false);
							gotoScanStep('orderid');
						}
					}
				});
			}else{
				$("#orderid").prop("disabled",false);
				scanProcessTip('订单号录入失败,格式有误',false);
				gotoScanStep('orderid');
				//scanend = true;
				return false;
			}
			
		}
	});
	
	$(".backinstore").click(function(){
			var orderid = $.trim($(this).attr("ebayid"));
			$(this).prop("disabled",true);
			var p_realebayid=/^[1-9]\d+$/;
			var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|EE|EC|LN|LM|RG)\d{9}(CN|HK|DE200|SG|DE)$/;
			if(p_realebayid.test(orderid) || p_eub_trackno.test(orderid)){//compablity with EUB
				$.ajax({
					type : "POST",
					dataType:'json',
					url : 'json.php?mod=postOfficeReturned&act=returnToerp&jsonp=1',
					data : {'orderid':orderid},
					success: function(result){
						console.log(result.data.errMsg);
						//var	result		=	eval("("+result+")");
						if(result.data.errCode==200){
							scanProcessTip(result.data.errMsg,true);
							$('#mstatus').focus();
						}else{
							scanProcessTip(result.data.errMsg,false);
							$('#mstatus').focus();
						}
					}
				});
			}else{
				$("#backinstore").prop("disabled",false);
				scanProcessTip('订单号录入失败,格式有误',false);
				gotoScanStep('orderid');
				//scanend = true;
				return false;
			}
	});
	
});

function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus').html(str);
	}catch(e){}
}
function checkform(){
	var buyer_userid		=	$("#buyer_userid").val();
	var	recordnum			=	$("#recordnum").val();
	if(recordnum==""&&buyer_userid==""){
		alert("搜索条件不能全为空");
		$("#buyer_userid").focus();
		return false;
	}
}
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}