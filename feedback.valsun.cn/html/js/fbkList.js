$(function(){	
	//搜索
	$('#serch_btn').click(function(){	
		var valuatestatus 	= $.trim($("#valuatestatus").val());
		var accountId 		= $.trim($("#accountId").val());		
		location.href = "index.php?mod=feedbackManage&act=fbkList&valuatestatus="+valuatestatus+"&accountId="+accountId;
	});
	
	//全选/反选	
	$("#checkall").click(function(){
		var ckbs = $("input[name='iqcselect']"); 
		for(var i=0;i<ckbs.length;i++){
			if(ckbs[i].checked==false){
				ckbs[i].checked = true;
			}else{
				ckbs[i].checked = false;
			}
		}
	});
	
	//评论
	$('#feedbk_btn').click(function(){
		var valuatescore 	= $.trim($("#valuatescore").val());
		var valuatecontent	= $.trim($("#valuatecontent").val());
		var bill 			= new Array;
		var billItem		= {};
		$("input[name=fbkselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				//bill.push($(this).val());
				billItem.account = $(this).attr("account");
				billItem.recordnumber = $(this).attr("recordnumber");
				bill.push(billItem);
			}
		 });
		if(bill == ""){
			//alert("你没有选择任何料号!");
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何选项-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		if(confirm("确定要评价这些订单吗?")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=feedbackManage&act=setEvaluation&jsonp=1',
				data	: {bill:bill,valuatescore:valuatescore,valuatecontent:valuatecontent},
				success	: function (msg){
					if(msg.errCode==0){
						//alert('评价成功');
						window.location.href = "index.php?mod=feedbackManage&act=fbkList&state=评价成功";
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
		}
		
	});

})
