$(document).ready(function(){
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
	
	 	var LODOP, aport; 
		LODOP = getLodop();
		$("#shipOrderId").focus();
		$("#shipOrderId").blur(function(){
			//$(this).focus();
		}).live("keydown",function(event){
	        if(event.keyCode==13){        	
	        	var orderid = "";
	        	orderid = $("#shipOrderId").val();
	        	type	= $("input[name='partitionType']:checked").val();
	        	if(type == undefined){
	        		$('.show_result').html('请先选择分渠道或者分区');
	        		return false;
	        	}
	        	if(orderid == ''){
	        		$(".show_result").html('请输入发货单号');
	        		return false;
	        	}
	        	var act = '';
	        	if(type=='channel'){
	        		act = 'checkChannel';
	        	}else{
	        		act = 'checkPartion';
	        	}
	        	LODOP.WRITE_PORT_DATA("COM"+aport, 'A99');
	        	$.ajax({
					type: "POST",
					dataType: "json",
					url: "json.php?mod=orderPartion&act="+act+"&jsonp=1",
					data: { "shipOrderId":orderid },
					success: function(response){
						LODOP.WRITE_PORT_DATA("COM"+aport, response.status);
						var partition = '';
						if(response.partition != '' && response.partition != undefined){
							partition = '->桶号：'+response.partition;
						}
						$(".show_result").html(orderid+partition+'<br/>'+response.msg);
						if(response.status == 'A00'){
							//报警 延时1延关闭
							var wtime = $('.warnning').val();
							setTimeout('sendOffFlag()', wtime);	
						}
						$("#shipOrderId").val('');
						$("#shipOrderId").focus();
					}
				});
	        }
	    });
	    //自检
	    $(".selfchecking").click(function(){
	    	LODOP.WRITE_PORT_DATA("COM"+aport, 'A98');
	    	$("#shipOrderId").focus();
	    });
	    //全灭
	    $(".offall").click(function(){
	    	LODOP.WRITE_PORT_DATA("COM"+aport, 'A99');
	    	$("#shipOrderId").focus();
	    });
	    var porthtml = '';
	    for(var i=1; i<=40; i++){
	    	porthtml += '<option value="'+i+'">COM'+i+'</option>';
	    }
	    
	    $(".portlist").html(porthtml).change(function(){
	    	var comv = $(this).val();
	    	var connectConf="mode com"+comv+":9600,n,8,1";
	    	if(!LODOP.WRITE_PORT_DATA("COM"+comv,connectConf)){
	    		$(".port_status").html('端口COM'+comv+'通讯失败！请选择正确的端口');
	    	}else{
	    		$(".port_status").html('');
	    		aport = comv;
	    	}
	    	Browser.setCookie('PARTION_COM', comv);
	    	$("#shipOrderId").focus();
	    });
	    
	    aport = $(".portlist").val();
	    var com1 = Browser.getCookie('PARTION_COM');
	    if(com1)aport = com1;
	    $(".portlist").val(aport);
	    var connectConf="mode com"+aport+":9600,n,8,1";
	    if(!LODOP.WRITE_PORT_DATA("COM"+aport,connectConf)){
	    	$(".port_status").html('端口COM'+aport+'通讯失败！请选择正确的端口');
	    }
	    
	    $(".packet").fancybox({
			'height': 30,
			'minHeight': 30,
			'autoScale': false,
			'transitionIn': 'none',
			'transitionOut': 'none',
			'href': $('#data').html(),
			'type': 'html',
			afterShow: function () {
				$(".packageid").focus();
			},
			afterClose: function () {
				$("#shipOrderId").focus();
			}
		});
		
		$(".packageid").blur(function(){
			//$(this).focus();
		}).live("keydown",function(event){
	        if(event.keyCode==13){
	        	var packageid = $(this).val();
	            if(!packageid){
	                alertify.error('请扫描口袋编号!');
	                return false;
	            }
	        	$.ajax({
					type : "post",
					dataType:'json',
					url : 'index.php?mod=orderPartion&act=savepacket',
					data: { "packageid":packageid },
					success: function(response){
						if (response.status == 1) {
							$.fancybox.close();
	                        $('#show_total').css('display', 'inline');
	                        $('#total_order').html(response.totalNum);
	                        $('#total_weight').html(response.totalWeight);
	                        $('.show_result').html('');
							alertify.success("口袋打包成功！");
						} else {
							alertify.error(response.msg);
	                        $('.packageid').val('');
						}
					}
				});
	        }
	    });	
	   
});

function sendOffFlag(){
   	$(".offall").click();
}	