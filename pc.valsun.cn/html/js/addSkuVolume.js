
$(function(){
    
    $("#goodsCode").focus();
    
    $('#goodsCode').keydown(function(e){
        if(e.keyCode==13){
        var goodsCode = $("#goodsCode").val();
        if(isNaN(goodsCode) || goodsCode <= 0){
            $("#error").html('条码有误');
            $("#correct").html('');
            $("#goodsCode").focus();
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
                        $('#goodsCode').focus();
                        return;
					}else{
					   $("#error").html('');
					   $("#goodsLength").focus();
                       return;
					}
				}
			});
       } 
    });
    
    $("#goodsLength").keydown(function(e){
        if(e.keyCode==13){
            var goodsLength = $("#goodsLength").val();
            if(isNaN(goodsLength) || goodsLength <= 0){
                $("#error").html('长度有误');
                $("#correct").html('');
                $("#goodsLength").focus();
                return;
            }else{
                $("#goodsWidth").focus();
                return;
            }
        }
    });
    
    $("#goodsWidth").keydown(function(e){
        if(e.keyCode==13){
            var goodsWidth = $("#goodsWidth").val();
            if(isNaN(goodsWidth) || goodsWidth <= 0){
                $("#error").html('宽度有误');
                $("#correct").html('');
                $("#goodsWidth").focus();
                return;
            }else{
                $("#goodsHeight").focus();
                return;
            }
        }
    });
    
    $("#goodsHeight").keydown(function(e){
        if(e.keyCode==13){
            var goodsHeight = $("#goodsHeight").val();
            if(isNaN(goodsHeight) || goodsHeight <= 0){
                $("#error").html('高度有误');
                $("#correct").html('');
                $("#goodsHeight").focus();
                return;
            }else{
                $("#submit").click();
                return;
            }
        }
    });
    
    $('#submit').click(function(){
        var goodsCode = $('#goodsCode').val();
        var goodsLength = $('#goodsLength').val();
        var goodsWidth = $('#goodsWidth').val();
        var goodsHeight = $('#goodsHeight').val();
        if(isNaN(goodsCode)){
            $("#error").html('条码有误');
            $("#correct").html('');
            $('#goodsCode').val('');
            $('#goodsCode').focus();
        }
        if(isNaN(goodsLength) || goodsLength <= 0){
            $("#error").html('长度输入有误');
            $("#correct").html('');
            $('#goodsLength').focus();
        }
        if(isNaN(goodsWidth) || goodsWidth <= 0){
            $("#error").html('宽度输入有误');
            $("#correct").html('');
            $('#goodsWidth').focus();
        }
        if(isNaN(goodsHeight) || goodsHeight <= 0){
            $("#error").html('高度输入有误');
            $("#correct").html('');
            $('#goodsHeight').focus();
        }
        //alert(goodsLength+'  '+goodsWidth + "  "+goodsHeight);
        //return;
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=addSkuVolume&jsonp=1',
				data	: {goodsCode:goodsCode,goodsLength:goodsLength,goodsWidth:goodsWidth,goodsHeight:goodsHeight},
				success	: function (msg){
					if(msg.data == false){
					    $("#error").html('错误，请重新输入');
                        $("#correct").html('');
						$('#goodsCode').val('');
                        $('#goodsCode').focus();
                        return;
					}else{
					   $("#error").html('');
                       $("#correct").html(msg.data.sku + "<br/>" + goodsLength+'*'+goodsWidth+'*'+goodsHeight+'<br/>'+'添加成功');
                       $("#goodsCode").val('');
                       $("#goodsLength").val('');
                       $("#goodsWidth").val('');
                       $("#goodsHeight").val('');
					   $("#goodsCode").focus();
                       return;
					}
				}
			});
        
    });


});

