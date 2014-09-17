$(function(){
	//POST数据验证
	$("#ruleAddForm").validationEngine({autoHidePrompt:true});

	$('#floors').click(function(){
		location.href = "index.php?mod=locationPrint&act=floorRule";
	});
	
	//修改楼层
	$('.rule_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=locationPrint&act=editRule&id="+id;
		return false;
	});
	
	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增楼层
	$("#addRule").click(function(){
		window.location.href = "index.php?mod=locationPrint&act=addRule";
		return false;
	});
	
	//区域打印
	$("#area_print").click(function(){
		var a_number = /^\d+$/;
		var store 	  = $('#store').val();
		var floor	  = $('#floor').val();
		var area 	  = $('#area').val();
		
		if(store==''){
			$('#store').focus();
			alertify.error("仓库编码不能为空");
			return false;
		}
		if(floor==''){
			$('#floor').focus();
			alertify.error("楼层不能为空");
			return false;
		}
		var url = "&store="+store+"&floor="+floor+"&area="+area;
		//window.location.href = "index.php?mod=locationPrint&act=printFloor"+url;
		window.open("index.php?mod=locationPrint&act=printArea"+url,"_blank");
	});
	
	//打印
	$("#print").click(function(){
		var a_number = /^\d+$/;
		var store 	  = $('#store').val();
		var floor	  = $('#floor').val();
		var area 	  = $('#area').val();
		var Shelf1	  = $('#Shelf1').val();
		var Shelf2 	  = $('#Shelf2').val();
		var layer1	  = $('#layer1').val();
		var layer2 	  = $('#layer2').val();
		var location1 = $('#location1').val();
		var location2 = $('#location2').val();
		if(store==''){
			$('#store').focus();
			alertify.error("仓库编码不能为空");
			return false;
		}
		if(floor==''){
			$('#floor').focus();
			alertify.error("楼层不能为空");
			return false;
		}
		if(!a_number.test(Shelf1)){
			$('#Shelf1').focus();
			alertify.error("货架个数只能为数字");
			return false;
		}
		if(!a_number.test(Shelf2)){
			$('#Shelf2').focus();
			alertify.error("货架个数只能为数字");
			return false;
		}
		if(Shelf2<Shelf1){
			$('#Shelf2').focus();
			alertify.error("货架个数不能小于开始数");
			return false;
		}
		if(!a_number.test(layer1)){
			$('#layer1').focus();
			alertify.error("货架层数只能为数字");
			return false;
		}
		if(!a_number.test(layer2)){
			$('#layer2').focus();
			alertify.error("货架层数只能为数字");
			return false;
		}
		if(layer2<layer1){
			$('#layer2').focus();
			alertify.error("货架层数不能小于开始数");
			return false;
		}
		if(!a_number.test(location1)){
			$('#location1').focus();
			alertify.error("货位号只能为数字");
			return false;
		}
		if(!a_number.test(location2)){
			$('#location2').focus();
			alertify.error("货位号只能为数字");
			return false;
		}
		if(location2<location1){
			$('#location2').focus();
			alertify.error("货位号不能小于开始数");
			return false;
		}
		var url = "&store="+store+"&floor="+floor+"&area="+area+"&Shelf1="+Shelf1+"&Shelf2="+Shelf2+"&layer1="+layer1+"&layer2="+layer2+"&location1="+location1+"&location2="+location2;
		//window.location.href = "index.php?mod=locationPrint&act=printFloor"+url;
		window.open("index.php?mod=locationPrint&act=printFloor"+url,"_blank");
	});
});
