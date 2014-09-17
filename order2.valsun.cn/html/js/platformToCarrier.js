$(function(){
	//POST数据验证
	$("#CarrierAddForm").validationEngine({autoHidePrompt:true});
	$("#CarrierupdateForm").validationEngine({autoHidePrompt:true});
	select0();
	select1();
	//返回
	$("#back").click(function(){
		window.location.href    = "index.php?mod=PlatformToCarrier&act=index&rc=reset";
	});
	//查重
	$("#carrierName").blur(function(){
		var platformId      = $("#platformId").val();
		var carrierName     = $("#carrierName").val();
		if(platformId=="" || carrierName=="" ){
			return false;
		}
		$.ajax({
			type	: "POST",
			dataType: "json",
			url		: 'index.php?mod=PlatformToCarrier&act=checkExit',
			data	: {platformId:platformId,carrierName:carrierName},
			success	: function (msg){
				if(msg.errCode==200){
					
				}else{
					alertify.alert(msg.errMsg);
					$("#carrierName").val("");
					$("#carrierName").focus();
				}
			}	
	   });
	});
	//新增运输方式
	$("#addCarrier").click(function(){
		window.location.href    = "index.php?mod=platformToCarrier&act=add";
		return false;
	});
	$(".list0").click(function(){
		var tag   = 0;
		$(".list0").each(function(){
			tal    = $(this).attr("checked");
			if(tal!=undefined){
				tag   += 1;
			}		
		});
		if(tag){
			$("#ckall0").prop('checked',true);
			$("#ckall1").prop({disabled:true});
			$(".list1").prop({disabled:true});
		}else{
			$("#ckall0").prop('checked',false);
			$("#ckall1").prop({disabled:false});
			$(".list1").prop({disabled:false});
		}
		
	});
	
	
	$(".list1").click(function(){
		var tag   = false;
		$(".list1").each(function(){
			tal    = $(this).attr("checked");
			if(tal){
				tag   = tal;
			}		
		});
		if(tag){
			$("#ckall1").prop('checked',true);
			$("#ckall0").prop({disabled:true});
			$(".list0").prop({disabled:true});
		}else{
			$("#ckall1").prop('checked',false);
			$("#ckall0").prop({disabled:false});
			$(".list0").prop({disabled:false});
		}
		
	});
	//全反选非快递
	$("#ckall0").click(function(){
		var ck    = $("#ckall0").attr('checked');
		if(ck){
			$(".list0").prop('checked',true);
			$("#ckall1").prop({disabled:true});
			$(".list1").prop({disabled:true});
		}else{
			
			$(".list0").prop('checked',false);
			$("#ckall1").prop({disabled:false});
			$(".list1").prop({disabled:false});
		}
	});
	
	//全反选快递
	$("#ckall1").click(function(){
		var ck    = $("#ckall1").attr('checked');
		if(ck){
			$(".list1").prop('checked',true);
			$("#ckall0").prop({disabled:true});
			$(".list0").prop({disabled:true});
		}else{
			$(".list1").prop('checked',false);
			$("#ckall0").prop({disabled:false});
			$(".list0").prop({disabled:false});
		}
	});
	//修改运输方式
	$('.edite').click(function(){
		id = $(this).attr('tid');
		window.location.href    = "index.php?mod=platformToCarrier&act=edit&id="+id;
	});
	
	//
	$('.delete').click(function(){
		id = $(this).attr('tid');
		window.location.href    = "index.php?mod=platformToCarrier&act=delete&id="+id;
	});
	
});

function select0(){
	var tag   = 0;
	$(".list0").each(function(){
		tal    = $(this).attr("checked");
		if(tal!=undefined){
			tag   += 1;
		}		
	});
	if(tag){
		$("#ckall0").prop('checked',true);
		$("#ckall1").prop({disabled:true});
		$(".list1").prop({disabled:true});
	}else{
		$("#ckall0").prop('checked',false);
		$("#ckall1").prop({disabled:false});
		$(".list1").prop({disabled:false});
	}
}

function select1(){
	var tag   = false;
	$(".list1").each(function(){
		tal    = $(this).attr("checked");
		if(tal){
			tag   = tal;
		}		
	});
	if(tag){
		$("#ckall1").prop('checked',true);
		$("#ckall0").prop({disabled:true});
		$(".list0").prop({disabled:true});
	}else{
		$("#ckall1").prop('checked',false);
		$("#ckall0").prop({disabled:false});
		$(".list0").prop({disabled:false});
	}
}

function selectAll0(){
	var ck    = $("#ckall0").attr('checked');
	if(ck){
		$(".list0").prop('checked',true);
		$("#ckall1").prop({disabled:true});
		$(".list1").prop({disabled:true});
	}else{
		
		$(".list0").prop('checked',false);
		$("#ckall1").prop({disabled:false});
		$(".list1").prop({disabled:false});
	}
}

function selectAll1(){
	var tag   = false;
	$(".list1").each(function(){
		tal    = $(this).attr("checked");
		if(tal){
			tag   = tal;
		}		
	});
	if(tag){
		$("#ckall1").prop('checked',true);
		$("#ckall0").prop({disabled:true});
		$(".list0").prop({disabled:true});
	}else{
		$("#ckall1").prop('checked',false);
		$("#ckall0").prop({disabled:false});
		$(".list0").prop({disabled:false});
	}
}