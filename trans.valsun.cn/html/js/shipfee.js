$(document).ready(function(){
	$("#back").click(function(){
		history.go(-1);
	});
	
		// binds form submission and fields to the validation engine
		jQuery("#formID").validationEngine();
	
	$("#modify_cpsf_fujian").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var unitPrice = $("#unitPrice").val();
		var handlefee = $("#handlefee").val();
		var id = $("#hidden").val();
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_cpsf_fujian",
			dataType:"jsonp",
			data:{"groupName":groupName,"channelName":channelName,"countries":countries,"unitPrice":unitPrice,"handlefee":handlefee,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
			$.post('json.php?mod=shipfee&act=modify_cpsf_fujian&jsonp=1',
			   {"groupName":groupName,"channelName":channelName,"countries":countries,"unitPrice":unitPrice,"handlefee":handlefee,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=cpsf_fujian&channelName="+channelName;
				   }
			   }
			);
	});
	$("#modify_cpsf_shenzhen").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		
		var countries = $("#countries").val();
		var id = $("#hidden").val();
		var firstweight = $("#firstweight").val();
		
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_cpsf_shenzhen",
			dataType:"jsonp",
			data:{"groupName":groupName,"countries":countries,"firstweight":firstweight,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
			$.post('json.php?mod=shipfee&act=modify_cpsf_shenzhen&jsonp=1',
			   {"groupName":groupName,"countries":countries,"firstweight":firstweight,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=cpsf_shenzheng&channelName="+channelName;
				   }
			   }
			);
	});
	
	$("#modify_cprg_fujian").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var unitPrice = $("#unitPrice").val();
		var handlefee = $("#handlefee").val();
		var id = $("#hidden").val();
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_cprg_fujian",
			dataType:"jsonp",
			data:{"groupName":groupName,"channelName":channelName,"countries":countries,"unitPrice":unitPrice,"handlefee":handlefee,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
			$.post('json.php?mod=shipfee&act=modify_cprg_fujian&jsonp=1',
			   {"groupName":groupName,"countries":countries,"unitPrice":unitPrice,"handlefee":handlefee,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=cprg_fujian&channelName="+channelName;
				   }
			   }
			);
		
	});
	
	
	$("#modify_ems_shenzhen").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var firstweight = $("#firstweight").val();
		var firstweight0 = $("#firstweight0").val();
		var nextweight = $("#nextweight").val();
		var files = $("#files").val();
		var declared_value = $("#declared_value").val();
		
		var id = $("#hidden").val();
		$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_ems_shenzhen",
			dataType:"jsonp",
			data:{"groupName":groupName,"countries":countries,"firstweight":firstweight,"firstweight0":firstweight0,"nextweight":nextweight,"files":files,"declared_value":declared_value,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });
	});
	$("#modify_eub_shenzhen").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var unitprice = $("#unitprice").val();
		var handlefee = $("#handlefee").val();
	

		
		var id = $("#hidden").val();
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_eub_shenzhen",
			dataType:"jsonp",
			data:{"groupName":groupName,"countries":countries,"unitprice":unitprice,"handlefee":handlefee,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
			$.post('json.php?mod=shipfee&act=modify_eub_shenzhen&jsonp=1',
			   {"groupName":groupName,"countries":countries,"unitprice":unitprice,"handlefee":handlefee,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=eub_shenzheng&channelName="+channelName;
				   }
			   }
			);
	});
	$("#modify_hkpostsf_hk").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var firstweight = $("#firstweight").val();
		var nextweight = $("#nextweight").val();
		var handlefee = $("#handlefee").val();
	

		
		var id = $("#hidden").val();
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_hkpostrg_hk",
			dataType:"jsonp",
			data:{"groupName":groupName,"countries":countries,"firstweight":firstweight,"nextweight":nextweight,"handlefee":handlefee,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
			$.post('json.php?mod=shipfee&act=modify_hkpostsf_hk&jsonp=1',
			   {"groupName":groupName,"countries":countries,"firstweight":firstweight,"nextweight":nextweight,"handlefee":handlefee,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=hkpostsf_hk&channelName="+channelName;
				   }
			   }
			);
	});
	$("#modify_hkpostrg_hk").click(function(){
		var groupName = $("#groupName").val();
		var channelName = $("#channelName").val();
		var countries = $("#countries").val();
		var firstweight = $("#firstweight").val();
		var nextweight = $("#nextweight").val();
		var handlefee = $("#handlefee").val();
	

		
		var id = $("#hidden").val();
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=shipfee&act=modify_hkpostsf_hk",
			dataType:"jsonp",
			data:{"groupName":groupName,"countries":countries,"firstweight":firstweight,"firstweight0":firstweight0,"handlefee":handlefee,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					//window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
				}
			}
	    });*/
		$.post('json.php?mod=shipfee&act=modify_hkpostrg_hk&jsonp=1',
			   {"groupName":groupName,"countries":countries,"firstweight":firstweight,"nextweight":nextweight,"handlefee":handlefee,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=shipfee&act=hkpostrg_hk&channelName="+channelName;
				   }
			   }
			);
	});
	$("#modify_globalmail_shenzhen").click(function(){
	    //alert("come");
		$("#importexcel").css("display","block");
		
	});
	$("#modify_fedex_shenzhen").click(function(){
	    //alert("come");
		$("#importexcel").css("display","block");
		
	});
});