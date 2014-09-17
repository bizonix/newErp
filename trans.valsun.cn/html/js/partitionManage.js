$("#addNewPartition").click(function(){
	/*var objs = $("input[name='ckbs']"); 
	var value_arr = new Array();
	for(var i=0;i<objs.length;i++){
		if(objs[i].checked==true){
			value_arr.push(objs[i].value);
		}
	}
*/
	window.location.href = "http://trans.valsun.cn/index.php?mod=partitionManage&act=addNewPartition";
});
$(document).ready(function(){
	var obj = $("#enable");
	if(isNaN(obj.name)==false){
		obj.selectedIndex = obj.name;
	}
	$("#back").click(function(){
		history.go(-1);
	});
	
		// binds form submission and fields to the validation engine
		jQuery("#formID").validationEngine();
	
});
function checksubmit(){
	var partitionName = $("#partitionName").val();
	var channelName = $("#channelname").val();
	var countries = $("#countries").val();
	var returnAddress = $("#returnAddress").val();
	var enable = $("#enable").val();
	var channelId = $("#channelId").val();
	var operate = $("h1").html();

	if(operate=="新增分区"){
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=partitionManage&act=addNewPartition",
			dataType:"jsonp",
			data:{"partitionName":partitionName,"channelName":channelName,"countries":countries,"returnAddress":returnAddress,"enable":enable},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					window.location.href = "http://trans.valsun.cn/index.php?mod=partitionManage&act=partition";
				}
			}
		});*/
		$.post('json.php?mod=partitionManage&act=addNewPartition&jsonp=1',
			   {"partitionName":partitionName,"channelId":channelId,"countries":countries,"returnAddress":returnAddress,"enable":enable},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=partitionManage&act=partition&channelId="+channelId;
				   }
			   }
		);
	}else if(operate=="修改分区"){
	    var id = $("#hidden").val();
		
		/*$.ajax({
			type:"POST",
			url:"http://trans.valsun.cn/json.php?mod=partitionManage&act=modifyPartition",
			dataType:"jsonp",
			data:{"partitionName":partitionName,"channelName":channelName,"countries":countries,"returnAddress":returnAddress,"enable":enable,"id":id},
			//data:"start="+start+"end="+end;
			jsonp:"jsonpcallback",
			success:function(msg){
				alert(msg);
				var data = $.parseJSON(msg);
				if(data['errMsg']==""){
					window.location.href = "http://trans.valsun.cn/index.php?mod=partitionManage&act=partition";
				}
			}
		});*/
		$.post('json.php?mod=partitionManage&act=modifyPartition&jsonp=1',
			   {"partitionName":partitionName,"channelId":channelId,"countries":countries,"returnAddress":returnAddress,"enable":enable,"id":id},
			   function(data){
				   
				   var result = $.parseJSON(data);
				   
				   
				   if(result.errMsg==""){
				   //alert("come");
						window.location.href = "index.php?mod=partitionManage&act=partition&channelId="+channelId;
				   }
			   }
		);
	}
}