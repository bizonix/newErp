$(function(){

	$("[name='choiceSampleType']").change(function(){
		//alert($(this).attr('id')); return;
		var thisId  = $(this).attr('id');
		var tempArr = thisId.split("_");
		var id      = tempArr[1];
		var thisVal = $(this).val();
		if(confirm("请确认是否修改该分类的检测方式！")){
			$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=qcCategoryList&act=modifySampleTypeId&jsonp=1',
			data	: {"id":id,"thisVal":thisVal},
			success	: function (msg){
				   if(msg.data){
						alert("修改成功");   
				   }else{
						alert("修改失败");   
				   }
			}
			});
		}
    });
	
	/*$("#back").click(function(){
		history.go(-1);
	});*/
	
	// binds form submission and fields to the validation engine
	jQuery("#formID").validationEngine();
		
});
function changeCategory(){
	var category1 = $("#category1").val();
	var category2 = $("#category2").val();
	var category3 = $("#category3").val();
	var category4 = $("#category4").val();
	var category = $("#categorymod").val();
	if(category==""){
		return false
	}
	if(confirm("请确认是否修改该分类的检测方式！")){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=qcCategoryList&act=changeCategory&jsonp=1',
			data	: {"category1":category1,"category2":category2,"category3":category3,"category4":category4,"category":category},
			success	: function (msg){
				if(msg.errCode==0){
					alertify.success("修改成功！");
					window.location.href = "index.php?mod=sampleStandard&act=skuCategoryList&condition="+msg.data;
				}else{
					alertify.error("修改失败！");
				}
			}
		});
	}
	
}
function changeCategory1(){
	var category1 = $("#category1").val();
	$("#category2").html("");
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=qcCategoryList&act=getCategory2&jsonp=1',
		data	: {"category1":category1},
		success	: function (msg){
			if(msg.errCode==0){
				var html = "<option value=''>请选择</option>";
				//alert()
				for(var i=0;i<msg.data.length;i++){
					html += "<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
				}
				//$("#category2").css("display","block");
				$("#category2").html(html);
				$("#category2").show();
				$("#category3").hide();
				$("#category4").hide();
			}else{
				alertify.error("未找到二级分类！");
			}
		}
	});
}

function changeCategory2(){
	var category1 = $("#category1").val();
	var category2 = $("#category2").val();
	$("#category3").html("");
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=qcCategoryList&act=getCategory3&jsonp=1',
		data	: {"category1":category1,"category2":category2},
		success	: function (msg){
			if(msg.errCode==0){
				var html = "<option value=''>请选择</option>";
				//alert()
				for(var i=0;i<msg.data.length;i++){
					html += "<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
				}
				//$("#category2").css("display","block");
				$("#category3").html(html);
				$("#category3").show();
				$("#category4").hide();
			}else{
				//alertify.error("未找到三级分类！");
				$("#category3").hide();
			}
		}
	});
}
function changeCategory3(){
	var category1 = $("#category1").val();
	var category2 = $("#category2").val();
	var category3 = $("#category3").val();
	$("#category4").html("");
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=qcCategoryList&act=getCategory4&jsonp=1',
		data	: {"category1":category1,"category2":category2,"category3":category3},
		success	: function (msg){
			if(msg.errCode==0){
				var html = "<option value=''>请选择</option>";
				//alert()
				for(var i=0;i<msg.data.length;i++){
					html += "<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
				}
				//$("#category2").css("display","block");
				$("#category4").html(html);
				$("#category4").show();
			}else{
				//alertify.error("未找到四级分类！");
				$("#category4").hide();
			}
		}
	});
}