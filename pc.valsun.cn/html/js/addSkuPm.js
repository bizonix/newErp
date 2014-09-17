$(function(){
	$("#goodsCode").focus();
    
    $('#goodsCode').keydown(function(e){
        if(e.keyCode==13){
            var goodsCode = $('#goodsCode').val();
            if(isNaN(goodsCode)){
                $("#error").html('条码有误');
                $("#correct").html('');
                $('#goodsCode').focus();
                return;
            }
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=isExistSku&jsonp=1',
				data	: {goodsCode:goodsCode},
				success	: function (msg){
					if(msg.data == false){
					    $("#error").html('SKU不存在，请检查');
                        $("#correct").html('');
						$('#goodsCode').val('');
                        $('#pmId').val('');
                        $('#goodsCode').focus();
                        return;
					}else{
					   $("#error").html('');
					   $("#pmId").focus();
                       return;
					}
				}
			});
        }
    });
    
    $('#pmId').keydown(function(e){
        if(e.keyCode==13){
            var goodsCode = $('#goodsCode').val();
            if(isNaN(goodsCode)){
                $("#error").html('条码有误');
                $("#correct").html('');
                $('#goodsCode').focus();
                return;
            }
            var pmId = $('#pmId').val();
            if(pmId == ''){
                $("#error").html('包材为空');
                $("#correct").html('');
                $('#pmId').focus();
                return;
            }
            
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=addSkuPm&jsonp=1',
				data	: {goodsCode:goodsCode,pmId:pmId},
				success	: function (msg){
					if(msg.data.state == true){
					    $("#correct").html(msg.data.sku+'<br/>'+msg.data.pName+'<br/>'+'添加成功');
                        $("#error").html('');
						$('#goodsCode').val('');
                        $('#pmId').val('');
                        $('#goodsCode').focus();
					}else{
					   $("#error").html('错误，请重新输入');
                       $("#correct").html('');
                       $('#goodsCode').val('');
                       $('#pmId').val('');
                       $('#goodsCode').focus();
                       return;
					}
				}
			});
        }
    });
    


});

