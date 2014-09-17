/*
包装扫描
*/
$(document).ready(function(){
	$("#orderid").focus();
	$("#orderid").keypress(function(){
		var e = event||e;
		if(e.keyCode==13||e.keyCode==10){
			var orderid = $.trim(this.value);
			var p_realebayid=/^\d+$/;
			var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|EE|EC|LN|LM|RG)\d{9}(CN|HK|DE200|SG|DE)$/;
			if(p_realebayid.test(orderid) || p_eub_trackno.test(orderid)){//compablity with EUB
				$.ajax({
					type : "POST",
					dataType:'json',
					url : 'json.php?mod=packingOrder&act=packingOrder&jsonp=1',
					data : {'orderid':orderid},
					success: function(result){
						//console.log(result);return false;
						if(result.errCode==0){
							scanProcessTip(result.data.res,true);
							$('#showmes').show();
							var obj = document.getElementById("record");
							if(obj.rows.length >=6){
								obj.deleteRow(5);
							}
							$("#record > tbody").prepend("<tr class='odd'><td align='left'>"+orderid+"</td><td align='left'>"+result.data.carrier+"</td><td align='left'></td></tr>");
							$("#orderid").val('');
							$("#orderid").focus();
						}else if(result.errCode==222){
							scanProcessTip(result.errMsg,true);
							$("#in_tracknumber").show();
							gotoScanStep('tracknumber');
						}else{
							scanProcessTip(result.errMsg,false);
							gotoScanStep('orderid');
						}
					}
				});
			}else{
				scanProcessTip('订单号录入失败,格式有误',false);
				gotoScanStep('orderid');
				//scanend = true;
				return false;
			}
			
		}
	});
	$("#tracknumber").keydown(function(){
		var e = event||e;
		if(e.keyCode==13||e.keyCode==10){
			var tracknumber = $.trim($("#tracknumber").val());
			var orderid = $("#orderid").val();
			var p_realebayid=/^\d+$/;
			var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|EE|EC|LN|LM|RG|RX)\d{9}(CN|HK|DE200|SG|DE)$/;
			if(p_realebayid.test(orderid) || p_eub_trackno.test(orderid)){//compablity with EUB	
			}else{
				scanProcessTip('订单号录入失败,格式有误',false);
				gotoScanStep('orderid');
				return false;
			}
			if(p_eub_trackno.test(tracknumber)){//挂号条码格式匹配
				$.ajax({
					type : "post",
					dataType:'json',
					url : 'json.php?mod=packingOrder&act=packingTracknumber&jsonp=1',
					data : {'tracknumber':tracknumber,'orderid':orderid},
					success: function(result){
						if(result.errCode==0){
						//console.log(result);return false;
							scanProcessTip(result.data.res,true);
							$('#showmes').show();
							var obj = document.getElementById("record");
							if(obj.rows.length >=6){
								obj.deleteRow(5);
							}
							$("#record > tbody").prepend("<tr class='odd'><td align='left'>"+orderid+"</td><td align='left'>"+result.data.carrier+"</td><td align='left'>"+tracknumber+"</td></tr>");
							$("#orderid").val('');
							$("#orderid").focus();
							$("#in_tracknumber").hide();
							$('#tracknumber').val('');
						}else if(result.errCode==111){
							scanProcessTip(result.errMsg,true);
							$("#in_tracknumber").show();
							gotoScanStep('tracknumber');
						}else{
							scanProcessTip(result.errMsg,false);
							gotoScanStep('orderid');
						}
					}
				});			
			}else{	
				gotoScanStep('tracknumber');
				scanProcessTip('挂号条码录入失败,格式有误',false);
			}
			
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

function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}