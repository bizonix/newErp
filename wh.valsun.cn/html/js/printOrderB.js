$(function(){
	$('#order_group').click(function(){
		var print_list = $("#print_list").val();
		if(print_list==''){
			alert("请选择打印列表");
			return false;
		}else{
			var operate = $("#operate");
			operate.html("");
			operate.html("<font color='#33CC33'>正在生成配货清单，请稍等!</font>");
			$.ajax({
				type    : "POST",
				dataType: "jsonp",
				url     : "json.php?mod=groupRouteB&act=groupGenerate&jsonp=1",
				data	: {print_list:print_list},
				success: function(msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						$("#print_group").val('1');
						operate.html("");
						alert(msg.errMsg);
					}else{
						operate.html("");
						alert(msg.errMsg);
					}					
				}
			});
		}
		return false;
	});
	
	$('#print_order_group').click(function(){
		var order_group = $.trim($("#select_order_group").val());
		var print_group = $("#print_group").val();
		if(print_group==0 && order_group==''){
			alert("请先生成订单配货分组，或输入已生成的配货清单号");return false;
		}
		//alert(order_group);return false;
		var url = "template/v1/printlabel_phA4B.php?order_group="+order_group;
		window.open(url,'_blank');
		return false;
	});
	
	
	
});
