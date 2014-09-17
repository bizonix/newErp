$(function(){
    jQuery("#addIoStoreDetailForm").validationEngine();//添加详细
    
    $("#selectAll").click(function(){
        allselect('selectAll','selectSing');
    });
    
    $("#seachOutStoreList").click(function(){
	    var spu = $("#spu").val();
		var iostoreStatus = $("#iostoreStatus").val();
		var useTypeId = $("#useTypeId").val();
        var isAudit = $("#isAudit").val();
        var whId = $("#whId").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
		location.href = "index.php?mod=products&act=getOutStoreList&iostoreStatus="+iostoreStatus+"&spu="+spu+"&useTypeId="+useTypeId+"&isAudit="+isAudit+"&whId="+whId+"&startdate="+startdate+"&enddate="+enddate;
	});
    
	$("#seachInStoreList").click(function(){
	    var spu = $("#spu").val();
		var iostoreStatus = $("#iostoreStatus").val();
		var useTypeId = $("#useTypeId").val();
        var isAudit = $("#isAudit").val();
        var whId = $("#whId").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
		location.href = "index.php?mod=products&act=getInStoreList&iostoreStatus="+iostoreStatus+"&spu="+spu+"&useTypeId="+useTypeId+"&isAudit="+isAudit+"&whId="+whId+"&startdate="+startdate+"&enddate="+enddate;
	});
    
    $("#seachIsNotBackSkuList").click(function(){
		var useTypeId = $("#useTypeId").val();
        var whId = $("#whId").val();
		location.href = "index.php?mod=products&act=getIsNotBackSkuList&useTypeId="+useTypeId+"&whId="+whId;
	});
    
    //领料/退料单中异步删除单个sku
    $(".deleteIoStoreDetailSku").click(function(){
        if(confirm('确定删除？')){
            var id = $(this).attr('ioStoreDetailId');
            if(isNaN(id)){
                $("#error").html('无效记录，删除失败');
                return;
            }         
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=ioStore&act=deleteIoStoreDetailById&jsonp=1',
				data	: {id:id},
				success	: function (msg){				        
					if(msg.errCode == '200'){
                        $("#tr"+id).hide();
                        //$("#error").html('删除成功');
					}else{
					   //$("#error").html('删除失败');
					}
                    alert(msg.errMsg);
				}
			});
        }  
    });
    
    //产品部确认收货
    $("#confirmIoStore").click(function(){
        ioStoreId = $("#iostoreId").val();
        if(!confirm('确认收货？')){
            return false;
        }
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=ioStore&act=confirmReceivingByMFG&jsonp=1',
				data	: {ioStoreId:ioStoreId},
				success	: function (msg){				        
					alert(msg.errMsg);
                    if(msg.errCode == 200){
                        window.location.reload();
                    }
				}
			});
    });
    
    
    
    
	
});

function createBill(){
    var id = '';
    var flag = 0;
	$('input[name="selectSing"]:checked').each(function(){
    //var goodsCount = parseInt($("#goodsCount"+$(this).val()).val());
    //id += $(this).val() + "*" + goodsCount + ",";
    id += $(this).val() + ",";
	});
	id = id.substring(0,id.length-1);
	if(!$.trim(id)){
		$("#error").html('请选择新品');
		return false;
	}
    var wh = $("#whId").val();
    //alert(wh);
//    return false;
    if(isNaN(wh)){
        $("#error").html('请选择仓库');
		return false;
    }
    
    $("#bill").val(id);
    $("#wh").val(wh);
    return confirm('确定要生成领料单？');
    //alert(id);
//    return false;
}

function allselect(id,name){
	var allselect = document.getElementById(id);
	var objs = document.getElementsByName(name);
	if(allselect.checked==true){
		for(var i=0;i<objs.length;i++){
			objs[i].checked=true;
		}
	}else{
		for(var i=0;i<objs.length;i++){
			objs[i].checked=false;
		}
	}
}