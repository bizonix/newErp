
$(function(){

    $("#goodsCode").focus();

    $('#goodsCode').keydown(function(e){
        if(e.keyCode==13){
        var goodsCode = $("#goodsCode").val();
        //if(isNaN(goodsCode) || goodsCode <= 0){
//            $("#error").html('条码有误');
//            $("#correct").html('');
//            $("#goodsCode").focus();
//            return;
//        }
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=isExistSku&jsonp=1',
				data	: {goodsCode:goodsCode},
				success	: function (msg){
					if(msg.data == false){
					    $("#error").html('SKU不存在，请检查');
                        $("#correct").html('');
						$("#goodsCode").val('');
                        $("#goodsLength").val('');
                        $("#goodsWidth").val('');
                        $("#goodsHeight").val('');
                        $("#pmId").val('');
                        $("#packageType").val('');
                        $("#pmCapacity").val('1');
					    $("#goodsCode").focus();
					}else{
					   //$("#goodsCode").val(msg.data);
                       $("#correct").html('输入的SKU为：'+msg.data);
					   $.ajax({
            				type	: "POST",
            				dataType: "jsonp",
            				url		: 'json.php?mod=goods&act=getVpInfoByGoodsCode&jsonp=1',
            				data	: {goodsCode:goodsCode},
            				success	: function (msg){
            					if(msg.data != false){
            						msg.data['goodsLength'] != 0 ?$('#goodsLength').val(msg.data['goodsLength']):$('#goodsLength').val('');
                                    msg.data['goodsWidth'] != 0 ?$('#goodsWidth').val(msg.data['goodsWidth']):$('#goodsWidth').val('');
                                    msg.data['goodsHeight'] != 0 ?$('#goodsHeight').val(msg.data['goodsHeight']):$('#goodsHeight').val('');
                                    $('#isPacking').val(msg.data['isPacking']);
                                    $('#packageType').val(msg.data['packageType']);
                                    $('#pmId').val(msg.data['pmName']);
                                    $('#pmCapacity').val(msg.data['pmCapacity']);
            					}
            				}
            			});
					   $("#error").html('');
					   $("#goodsLength").focus();
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
                $("#isPacking").focus();
                return;
            }
        }
    });

    $("#isPacking").keydown(function(e){
        if(e.keyCode==13){
           $("#pmId").focus();
        }
    });

    $("#packageType").keydown(function(e){
        if(e.keyCode==13){
           $('#submit').click();
        }
    });

    $('#pmId').keydown(function(e){
        if(e.keyCode==13){
            //var goodsCode = $('#goodsCode').val();
//            if(isNaN(goodsCode)){
//                $("#error").html('条码有误');
//                $("#correct").html('');
//                $('#goodsCode').focus();
//                return;
//            }
            var pmId = $('#pmId').val();
            if(pmId == ''){
                $("#error").html('包材为空');
                $("#correct").html('');
                $('#pmId').focus();
                return;
            }
            $("#pmCapacity").focus();
        }
    });

    $('#pmCapacity').keydown(function(e){
        if(e.keyCode==13){
            //var goodsCode = $('#goodsCode').val();
//            if(isNaN(goodsCode)){
//                $("#error").html('条码有误');
//                $("#correct").html('');
//                $('#goodsCode').focus();
//                return;
//            }
            var pmCapacity = $('#pmCapacity').val();
            if(isNaN(pmCapacity) || pmCapacity <= 0){
                $("#error").html('包材容量有误');
                $("#correct").html('');
                $('#pmCapacity').focus();
                return;
            }
            $('#submit').click();
        }
    });

    $('#submit').click(function(){
        var goodsCode = $('#goodsCode').val();
        var goodsLength = $('#goodsLength').val();
        var goodsWidth = $('#goodsWidth').val();
        var goodsHeight = $('#goodsHeight').val();
        var isPacking = $('#isPacking').val();
        var packageType = $('#packageType').val();
        //alert(isPacking);
//        return;
        var pmId = $('#pmId').val();
        var pmCapacity = $('#pmCapacity').val();
        //if(isNaN(goodsCode)){
//            $("#error").html('条码有误');
//            $("#correct").html('');
//            $('#goodsCode').val('');
//            $('#goodsCode').focus();
//            return;
//        }
        if(isNaN(goodsLength) || goodsLength <= 0){
            $("#error").html('长度输入有误');
            $("#correct").html('');
            $('#goodsLength').focus();
            return;
        }
        if(isNaN(goodsWidth) || goodsWidth <= 0){
            $("#error").html('宽度输入有误');
            $("#correct").html('');
            $('#goodsWidth').focus();
            return;
        }
        if(isNaN(goodsHeight) || goodsHeight <= 0){
            $("#error").html('高度输入有误');
            $("#correct").html('');
            $('#goodsHeight').focus();
            return;
        }
        if(pmId == ''){
            $("#error").html('包材为空');
            $("#correct").html('');
            $('#pmId').focus();
            return;
        }
        if(isNaN(pmCapacity) || pmCapacity <= 0){
            $("#error").html('包材容量有误');
            $("#correct").html('');
            $('#pmCapacity').focus();
            return;
        }
        //alert(goodsLength+'  '+goodsWidth + "  "+goodsHeight);
        //return;
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=skuVP&jsonp=1',
				data	: {goodsCode:goodsCode,goodsLength:goodsLength,goodsWidth:goodsWidth,goodsHeight:goodsHeight,isPacking:isPacking,packageType:packageType,pmId:pmId,pmCapacity:pmCapacity},
				success	: function (msg){
					if(msg.data == false){
					    if(msg.errCode == 101 || msg.errCode == 107){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
    						$('#goodsCode').val('');
                            $('#goodsCode').focus();
                            return;
					    }else if(msg.errCode == 102){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsLength').focus();
                            return;
					    }else if(msg.errCode == 103){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsWidth').focus();
                            return;
					    }else if(msg.errCode == 104){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsHeight').focus();
                            return;
					    }else if(msg.errCode == 105 || msg.errCode == 106){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
    						$('#pmId').val('');
                            $('#pmId').focus();
                            return;
					    }else if(msg.errCode == 107){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#pmId').focus();
                            return;
					    }
					}else{
					   $("#error").html('');
                       $("#correct").html(msg.data.sku + "<br/>" + goodsLength+'*'+goodsWidth+'*'+goodsHeight+'<br/>'+msg.data.pName+' - '+pmCapacity+'<br/>'+'添加成功');
                       $("#goodsCode").val('');
                       $("#goodsLength").val('');
                       $("#goodsWidth").val('');
                       $("#goodsHeight").val('');
                       $("#pmId").val('');
                       $("#packageType").val('');
                       $("#pmCapacity").val('1');
					   $("#goodsCode").focus();
                       return;
					}
				}
			});

    });

    $('#submit2').click(function(){
        var goodsCode = $('#goodsCode').val();
        var goodsLength = $('#goodsLength').val();
        var goodsWidth = $('#goodsWidth').val();
        var goodsHeight = $('#goodsHeight').val();
        var isPacking = $('#isPacking').val();
        var packageType = $('#packageType').val();
        //alert(isPacking);
//        return;
        var pmId = $('#pmId').val();
        var pmCapacity = $('#pmCapacity').val();
        //if(isNaN(goodsCode)){
//            $("#error").html('条码有误');
//            $("#correct").html('');
//            $('#goodsCode').val('');
//            $('#goodsCode').focus();
//            return;
//        }
        if(isNaN(goodsLength) || goodsLength <= 0){
            $("#error").html('长度输入有误');
            $("#correct").html('');
            $('#goodsLength').focus();
            return;
        }
        if(isNaN(goodsWidth) || goodsWidth <= 0){
            $("#error").html('宽度输入有误');
            $("#correct").html('');
            $('#goodsWidth').focus();
            return;
        }
        if(isNaN(goodsHeight) || goodsHeight <= 0){
            $("#error").html('高度输入有误');
            $("#correct").html('');
            $('#goodsHeight').focus();
            return;
        }
        if(pmId == ''){
            $("#error").html('包材为空');
            $("#correct").html('');
            $('#pmId').focus();
            return;
        }
        if(isNaN(pmCapacity) || pmCapacity <= 0){
            $("#error").html('包材容量有误');
            $("#correct").html('');
            $('#pmCapacity').focus();
            return;
        }
        //alert(goodsLength+'  '+goodsWidth + "  "+goodsHeight);
        //return;
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=skuVP2&jsonp=1',
				data	: {goodsCode:goodsCode,goodsLength:goodsLength,goodsWidth:goodsWidth,goodsHeight:goodsHeight,isPacking:isPacking,packageType:packageType,pmId:pmId,pmCapacity:pmCapacity},
				success	: function (msg){
					if(msg.data == false){
					    if(msg.errCode == 101 || msg.errCode == 107){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
    						$('#goodsCode').val('');
                            $('#goodsCode').focus();
                            return;
					    }else if(msg.errCode == 102){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsLength').focus();
                            return;
					    }else if(msg.errCode == 103){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsWidth').focus();
                            return;
					    }else if(msg.errCode == 104){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#goodsHeight').focus();
                            return;
					    }else if(msg.errCode == 105 || msg.errCode == 106){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
    						$('#pmId').val('');
                            $('#pmId').focus();
                            return;
					    }else if(msg.errCode == 107){
					        $("#error").html(msg.errMsg);
                            $("#correct").html('');
                            $('#pmId').focus();
                            return;
					    }
					}else{
					   $("#error").html('');
                       $("#correct").html(msg.data.sku + "<br/>" + goodsLength+'*'+goodsWidth+'*'+goodsHeight+'<br/>'+msg.data.pName+' - '+pmCapacity+'<br/>'+'添加成功');
                       $("#goodsCode").val('');
                       $("#goodsLength").val('');
                       $("#goodsWidth").val('');
                       $("#goodsHeight").val('');
                       $("#pmId").val('');
                       $("#packageType").val('');
                       $("#pmCapacity").val('1');
					   $("#goodsCode").focus();
                       return;
					}
				}
			});

    });


});

