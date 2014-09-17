$(function(){
    $("#selectAll").click(function(){
        allselect('selectAll','selectSing');
    });
    
    $("#seachProductsComfirmList").click(function(){
        var sku = $("#sku").val();
        var isExsitWebMaker = $("#isExsitWebMaker").val();
        var webMakerId = $("#webMakerId").val();
        location.href = "index.php?mod=products&act=getProductsComfirmList&sku="+sku+"&isExsitWebMaker="+isExsitWebMaker+"&webMakerId="+webMakerId;
    });
    $("#seachProductsTakeList").click(function(){
        var sku = $("#sku").val();
        location.href = "index.php?mod=products&act=getProductsTakeList&sku="+sku;
    });
    $("#seachProductsCompleteList").click(function(){
        var sku = $("#sku").val();
        location.href = "index.php?mod=products&act=getProductsCompleteList&sku="+sku;
    });
    $("#seachProductsReturnList").click(function(){
        var sku = $("#sku").val();
        location.href = "index.php?mod=products&act=getProductsReturnList&sku="+sku;
    });
    
    $("#productsTake").click(function(){
        var sku = $("#sku").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsTake&id="+id+"&sku="+sku;
		}
    });
    $("#productsComplete").click(function(){
        var sku = $("#sku").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsComplete&id="+id+"&sku="+sku;
		}
    });
    $("#productsBack").click(function(){
        var sku = $("#sku").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsBack&id="+id+"&sku="+sku;
		}
    });
    
	$('button.add').click(function(){//button1
		var id = '';
		var productsStatus = $("#productsStatus").val();
        var productsType = $("#productsType").val();
		$('input[name="products"]:checked').each(function(){
	    id += $(this).val() + ",";
		});
		id = id.substring(0,id.length-1);
		if(id == ''){
			return;
		}else{
            if($(this).attr("id") == 'dc2'){
                productsStatus--;
            }
			else{
                productsStatus++;
			}
			location.href = "index.php?mod=Products&act=getProducts&id="+id+"&type=update&productsStatus="+productsStatus+"&productsType="+productsType;
		}
    });

    $('button#skuSearchButton').click(function(){
		var sku = $("#skuSearchText").val();
		var productsStatus = $("#productsStatus").val();
        var productsType = $("#productsType").val();
		//alert("index.php?mod=Products&act=getProducts&sku="+sku+"&type=search&productsStatus="+productsStatus);
		if(isNaN(productsStatus)){
			return;
		}
		location.href = "index.php?mod=Products&act=getProducts&sku="+sku+"&type=search&productsStatus="+productsStatus+"&productsType="+productsType;
    });
    
    $('#sessionSku').keydown(function(e){
        if(e.keyCode==13){
            var sku = $(this).val();
            location.href = "index.php?mod=products&act=addProInstore&sku="+sku;
        }
    }); 
    
    $("#bt6").click(function(){
        $("#export").toggle();
    });
    
    $("button#exp1").click(function(){
        var startdate1 = $("startdate1").val();
        var enddate1 = $("enddate1").val();
        if(startdate1 == '' || enddate1 == ''){
            alert('空');
            return;
        }
        window.open("index.php?mod=products&act=exportProductsFinished");
    });
    
    //虚拟料号制作流程
    $("#productsCombineSpuTake").click(function(){
        var combineSpu = $("#combineSpu").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsCombineSpuTake&id="+id+"&combineSpu="+combineSpu;
		}
    });
    $("#productsCombineSpuComplete").click(function(){
        var combineSpu = $("#combineSpu").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsCombineSpuComplete&id="+id+"&combineSpu="+combineSpu;
		}
    });
    $("#productsCombineSpuBack").click(function(){
        var combineSpu = $("#combineSpu").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=productsCombineSpuBack&id="+id+"&combineSpu="+combineSpu;
		}
    });
    
    //无效料号直接跳到制作完成
    $("#illSku").click(function(){
        var sku = $("#sku").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=illSkuToComplete&id="+id+"&sku="+sku;
		}
    });
    
    //完成制作直接跳转到签收列表
    $("#completeToComfirm").click(function(){
        var sku = $("#sku").val();
        var id = '';
        $('input[name="selectSing"]:checked').each(function(){
	       id += $(this).val() + ",";
		});
        id = id.substring(0,id.length-1);
		if(id == ''){
		    $("#error").html('请选择料号');
			return;
		}else{
			location.href = "index.php?mod=products&act=completeToComfirm&id="+id+"&sku="+sku;
		}
    });
    
    

});

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
