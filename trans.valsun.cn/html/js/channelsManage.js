$(document).ready(function(){

	$("#addNewChannel").click(function(){
	
		var objs = $("input[name='ckbs']"); 
		var value_arr = new Array();
		for(var i=0;i<objs.length;i++){
			if(objs[i].checked==true){
				value_arr.push(objs[i].value);
			}
		}
		var channelname = $("#channelname").val();
		var transname = $("#transname").val();
		var channelAlias = $("#channelAlias").val();
		var discount = $("#discount").val();
		var enable = $("#enable").val();
        var id = $("#hidden").val();;
		//enable = enable.options[enable.selectedIndex].value;
		var h1 = $("h1").html();
		alert(h1);
        if(h1 =="修改渠道"){
        // alert("xiugai");		
			/*$.ajax({
				type:"POST",
				url:"http://trans.valsun.cn/json.php?mod=channelsManage&act=modifyChannels",
				dataType:"jsonp",
				data:{"channelname":channelname,"transname":transname,"channelAlias":channelAlias,"discount":discount,"enable":enable,"id":id},
				//data:"start="+start+"end="+end;
				jsonp:"jsonpcallback",
				success:function(msg){
					alert(msg);
					var data = $.parseJSON(msg);
					if(data.errCode==""){
						window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
					}
				}
			});*/
			$.post('json.php?mod=channelsManage&act=modifyChannels&jsonp=1',
			   {"channelname":channelname,"transname":transname,"channelAlias":channelAlias,"discount":discount,"enable":enable,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=channelsManage&act=channels&carrierName="+transname;
				   }
			   }
			);
		}else{
			/*$.ajax({
				type:"POST",
				url:"http://trans.valsun.cn/json.php?mod=channelsManage&act=addNewChannels",
				dataType:"jsonp",
				data:{"channelname":channelname,"transname":transname,"channelAlias":channelAlias,"discount":discount,"enable":enable},
				//data:"start="+start+"end="+end;
				jsonp:"jsonpcallback",
				success:function(msg){
					alert(msg);
					var data = $.parseJSON(msg);
					if(data['errMsg']==""){
						window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
					}
				}
			});*/
			$.post('json.php?mod=channelsManage&act=addNewChannels&jsonp=1',
			   {"channelname":channelname,"transname":transname,"channelAlias":channelAlias,"discount":discount,"enable":enable},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   alert(result.errMsg);
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=channelsManage&act=channels&carrierName="+transname;
				   }
			   }
			);
		
		}
	//window.location.href = "http://trans.valsun.cn/index.php?mod=channelsManage&act=addNewChannel";
    });
	$("#back").click(function(){
		history.go(-1);
	});
	
			// binds form submission and fields to the validation engine
			jQuery("#formID").validationEngine();
		
});