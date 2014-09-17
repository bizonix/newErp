$(document).ready(function(){
	//alert("dg");
});

function submitback(){
    //alert("gffd"); 
	var transnamec = $("#transnamec").val();
	var transnamee = $("#transnamee").val();
	//alert(transnamee);
	if(transnamec==""){
		$("#transnamec_err").html("运输方式中文名称不能为空！");
		//alert(transnamec);
		return false;
	}
	if(transnamee==""){
		$("#transnamee_err").html("运输方式英文名称不能为空！");
		return false;
	}
    var obj_arr = $("input[name='carrierToPlatform']");
	//alert(obj_arr.length);
	var value_arr = new Array();
	var len = obj_arr.length;
	for(var i=0;i<len;i++){
		var transname = obj_arr[i].value;
		
		var platform = obj_arr[i].parentNode.lastChild;
		platform = platform.innerHTML;
		
		value_arr.push(transname+"*"+platform);
		
	}
    
	var weightmin = $("#weightmin").val();
	var weightmax = $("#weightmax").val();
	var timecount = $("#timeCount").val();


	$.ajax({
		type:"POST",
		url:"http://trans.valsun.cn/json.php?mod=addNewCarrier&act=addNewCarrier",
		dataType:"jsonp",
		data:{"transnamec":transnamec,"transnamee":transnamee,"carrierToPlatform":value_arr,"weightmin":weightmin,"weightmax":weightmax,"timecount":timecount},
		//data:"start="+start+"end="+end;
		jsonp:"jsonpcallback",
		success:function(msg){
			alert(msg);
			var data = $.parseJSON(msg);
			if(data['errMsg']==""){
			    window.location.href = "http://trans.valsun.cn/index.php?mod=transportmanage&act=list";
			}
		}
	});
}