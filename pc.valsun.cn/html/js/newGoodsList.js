$(function(){
    $("#selectAll").click(function(){
        allselect('selectAll','selectSing');
    });
    
	$("#seachNewGoods").click(function(){
	   $("input:checkbox").attr("disabled", true);
		var sku = $("#sku").val();
		var whId = $("#whId").val();
        var purchaseId = $("#purchaseId").val();
		location.href = "index.php?mod=products&act=getNewGoodsList&sku="+sku+"&whId="+whId+"&purchaseId="+purchaseId;
	});
    
    $("#whId").change(function(){        
        $("#seachNewGoods").click();
    });
	
});

function createBill(){
    var id = '';
    var flag = 0;
	$('input[name="selectSing"]:checked').each(function(){
    //var goodsCount = parseInt($("#goodsCount"+$(this).val()).val());
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