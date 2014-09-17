$(function(){
	$('.checkall').click(function(){
		$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
	});

	//批量通过
	$('#allsure').click(function(){
		var bill = new Array;
		$("input[name=invselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			alertify.alert("你没有选择任何订单");
			//$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何订单-<span>');
			return false;
		}
		var new_bill = bill.join(',');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=whShelf&act=allSure&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.alert("确认成功");
						window.location.reload(); 
					}else{
						alertify.alert(msg.errMsg);
					}				
				}
			});
	});

});
