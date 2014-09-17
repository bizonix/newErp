$(function(){
    $('#returnSku').focus();
    
    $('#returnSku').keydown(function(e){
        if(e.keyCode==13){
            var sku = $(this).val();
            if($.trim(sku)){
                location.href = "index.php?mod=products&act=tmpReturnPros&type=add&sku="+sku;
            }
        }
    });
    
    //生成新品退料单
    $("#createReturnBill").click(function(){
        if(confirm('确认提交生成新品退料单？')){
            var whId = $("#whId").val();
            location.href = "index.php?mod=products&act=createReturnBill&whId="+whId;
        }       
    });
    
    $("#clear").click(function(){
        if(confirm('确认要清空扫描表吗？')){
            location.href = "index.php?mod=products&act=clearReturnBill";
        }  
    });
    
    //修改领料单
    $('#modGetSku').focus();
    
    $('#modGetSku').keydown(function(e){
        if(e.keyCode==13){
            var sku = $(this).val();
            if($.trim(sku)){
                location.href = "index.php?mod=products&act=tmpModGetPros&type=add&sku="+sku;
            }
        }
    });
    
    //生成产品修改领料单
    $("#createModGetBill").click(function(){
        if(confirm('确认提交生成修改领料单？')){
            var whId = $("#whId").val();
            location.href = "index.php?mod=products&act=createModGetBill&whId="+whId;
        }       
    });
    
    $("#clearModGet").click(function(){
        if(confirm('确认要清空扫描表吗？')){
            location.href = "index.php?mod=products&act=clearModGetBill";
        }  
    });
    
    //修改退料单
    
    $('#modReturnSku').focus();
    
    $('#modReturnSku').keydown(function(e){
        if(e.keyCode==13){
            var sku = $(this).val();
            if($.trim(sku)){
                location.href = "index.php?mod=products&act=tmpModReturnPros&type=add&sku="+sku;
            }
        }
    });
    
    ////生成产品修改退料单
    $("#createModReturnBill").click(function(){
        if(confirm('确认提交生成修改退料单？')){
            var whId = $("#whId").val();
            location.href = "index.php?mod=products&act=createModReturnBill&whId="+whId;
        }      
    });
    
    $("#clearModReturn").click(function(){
        if(confirm('确认要清空扫描表吗？')){
            location.href = "index.php?mod=products&act=clearModReturnBill";
        }  
    });
    
    //扫描缓存表中异步删除单个sku
    $(".deleteTmpPros").click(function(){
        if(confirm('确定删除？')){
            var id = $(this).attr('tmpId');
            if(isNaN(id)){
                $("#error").html('无效记录，删除失败');
                return;
            }
            
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=tmpReturnPros&act=deleteTmpProsById&jsonp=1',
				data	: {id:id},
				success	: function (msg){				        
					if(msg.errCode == '200'){
                        $("#tr"+id).hide();
                        $("#error").html('删除成功');
					}else{
					   $("#error").html('删除失败');
					}
				}
			});
        }  
    });
    
});