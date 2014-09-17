function fuc_onload(){
	document.getElementById('orderid').focus();
}
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}
function checkinput(){
	var e = e||event;
	if(e.keyCode==13||e.keyCode==10){
		var orderid = document.getElementById("orderid").value;
		//alert(orderid);
		if(orderid==""){
			document.getElementById("errorLog").innerHTML = "发货单号输入错误！";
			return false;
		}
		createXMLHttpRequest();
		var url = 'json.php?act=orderPartion&mod=orderPartion&jsonp=1';
		var param = 'orderid='+orderid;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = checkinputResponse;
		xmlHttpRequest.send(param);
	}
}
function checkinputResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			res = eval("("+res+")");
			//console.log(res);return;
			//alert(typeof(res));
			if(res.errCode==0){
				window.location.href = "index.php?mod=pda_orderPartion&act=pda_orderPartion&type=scan&partion="+res.data.partion+"&orderid="+res.data.orderid;
			}else{
				document.getElementById("errorLog").innerHTML = res.errMsg;
				document.getElementById("successLog").innerHTML = "";
				gotoScanStep('orderid');
				//$("#successLog").html("");
			}
		}
	}
}
function pack(){
	var scan_package = document.getElementById("packdiv");
	scan_package.style.display="block";
    document.getElementById("packageid").focus();
}
function scan_packageid(){
	var e = e||event;
	if(e.keyCode==13||e.keyCode==10){
		var partion = document.getElementById("partionuser").value;
		var packageid = document.getElementById("packageid").value;
		createXMLHttpRequest();
		var url = 'json.php?act=orderPartionPack&mod=orderPartion&jsonp=1';
		var param = 'packageid='+packageid+"&partion="+partion;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = scan_packageidResponse;
		xmlHttpRequest.send(param);
	}

}
function scan_packageidResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			res = eval("("+res+")");
			if(res.errCode==0){
				gotoScanStep('orderid');
				document.getElementById("errorLog").innerHTML = "";
				document.getElementById("successLog").innerHTML = "打包成功！";
				var scan_package = document.getElementById("packdiv");
				scan_package.style.display="none";
				document.getElementById("packageid").value="";
			}else{
				document.getElementById("errorLog").innerHTML = res.errMsg;
				document.getElementById("successLog").innerHTML = "";
				document.getElementById("packageid").select();
			}
		}
	}
}
/*$(document).ready(function(){
	$("#orderid").focus();
	$("#print").click(function(){
		var print_partion = $("#partions").value;
		var nums = $("#nums").value;
		if(print_partion==""||nums==""){
			return false;
		}
	});
	$("#PartionPrintForm").validationEngine({autoHidePrompt:true});
	$("#orderid").keydown(function(){
		var e = e||event;
		if(e.keyCode==13||e.keyCode==10){
			var orderid = this.value;
			//alert(orderid);
			if(orderid==""){
				
				$("#errorLog").html("发货单号输入错误！");
				return false;
			}
			$.ajax({
				type:"POST",
				url:"json.php?act=orderPartion&mod=orderPartion&jsonp=1",
				data:{"orderid":orderid},
				dataType:"json",
				success:function(msg){
					//alert(msg);
					if(msg.errorCode==0){
						window.location.href = "index.php?act=orderPartion&mod=orderPartion&type=scan&partionId="+msg.data.partionId+"&carrierName="+msg.data.carrierName;
					}else{
						$("#errorLog").html(msg.errMsg);
						$("#successLog").html("");
					}
				}
			});
		}
	});
	$("input[name=packthis]").click(function(){
		$("#packdiv").css("display","none");
		var psrtionId = this.id;
		var objs = $("#partionuser").options;
		for(var i=0;i<objs.length;i++){
			if(objs[i].value==partionId){
				objs[i].selected==true;
			}
		}
	});
	$("#pack").click(function(){
		var objs = document.getElementById("partionuser").options;
		
		for(var i=0;i<objs.length;i++){
			if(objs[i].selected==true){
				partionId = objs[i].value;
			}
		}
		var packageId = $("#packageid").value;
		if(isNaN(packageId)){
			$("#errorLog").html("口袋编号输入错误！");
			$("#successLog").html("");
		}
		$.ajax({
			type:"POST",
			url:"json.php?act=orderPartion&mod=orderPartion&jsonp=1",
			data:{"pationId":partionId,"packageId":packageId},
			dataType:"json",
			success:function(msg){
				if(msg.errorCode==0){
					window.location.href = "index.php?act=orderPartion&mod=orderPartion&type=pack";
				}else{
					$("#errorLog").html(msg.errMsg);
					$("#successLog").html("");
				}
			}
		});
	});
});*/