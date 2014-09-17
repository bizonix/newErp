$(function(){
    $('.add').click(function(){
		window.location.href = "index.php?mod=packingMaterials&act=addPm";
	});
    
    $('.mod').click(function(){
		var id = $(this).attr('tid');
		window.location.href = "index.php?mod=packingMaterials&act=updatePm&id="+id;
	});
	
	$('.del').click(function(){
		if(confirm("确定要删除该记录吗？")){
            var id = $(this).attr('tid');
			window.location.href = "index.php?mod=packingMaterials&act=deletePmOn&id="+id;
		}		
	});
	
	$("#back").click(function(){
		history.back();
	});


});

function submitUpdate(){
    var pmAlias = $("#pmAlias").val();
    var pmName = $("#pmName").val();
    var pmLength = $("#pmLength").val();
    var pmWidth = $("#pmWidth").val();
    var pmHeight = $("#pmHeight").val();
    var pmWeight = $("#pmWeight").val();
    var pmWidth = $("#pmWidth").val();
    var pmCost = $("#pmCost").val();
    var pmDimension = $("#pmDimension").val();
    var pmDivider = $("#pmDivider").val();
    var pmRatio = $("#pmRatio").val();
    
    if(!$.trim(pmName))
	{
		$("#pmNameSpan").text('包材类名不能为空');
		$("#pmName").focus();
		return false;
	}else{
	    $("#pmNameSpan").text('*');
	}
    
	if(!$.trim(pmAlias))
	{
		$("#pmAliasSpan").text('包材别名不能为空');
		$("#pmAlias").focus();
		return false;
	}else{
	    $("#pmAliasSpan").text('*');
	}
    
    if(!$.trim(pmCost))
    {
		$("#pmCostSpan").text('成本不能为空');
		$("#pmCost").focus();
		return false;
	}else{
	    $("#pmCostSpan").text('*');
	}
    	
    if(!$.trim(pmLength))
	{
		$("#pmLengthSpan").text('长度不能为空');
		$("#pmLength").focus();
		return false;
	}else{
	    $("#pmLengthSpan").text('*');
	}
    
    if(!$.trim(pmWidth))
	{
		$("#pmWidthSpan").text('宽度不能为空');
		$("#pmWidth").focus();
		return false;
	}else{
	    $("#pmWidthSpan").text('*');
	}
    
    if(!$.trim(pmHeight))
	{
		$("#pmHeightSpan").text('高度不能为空');
		$("#pmHeight").focus();
		return false;
	}else{
	    $("#pmHeightSpan").text('*');
	}
    
    if(!$.trim(pmWeight))
	{
		$("#pmWeightSpan").text('重量不能为空');
		$("#pmWeight").focus();
		return false;
	}else{
	    $("#pmWeightSpan").text('*');
	}
    
    if(!$.trim(pmDimension))
	{
		$("#pmDimensionSpan").text('容积不能为空');
		$("#pmDimension").focus();
		return false;
	}else{
	    $("#pmDimensionSpan").text('*');
	}  
    
    if(isNaN(pmCost))
    {
		$("#pmCostSpan").text('成本必须为数字');
		$("#pmCost").focus();
		return false;
	}else{
	    $("#pmCostSpan").text('*');
	}
    
	if(isNaN(pmLength))
    {
	   $("#pmLengthSpan").text('长度必须为数字');
	   $("#pmLength").focus();
       return false;
	}else{
	    $("#pmLengthSpan").text('*');
	}

    if(isNaN(pmWidth))
    {
		$("#pmWidthSpan").text('宽度必须为数字');
		$("#pmWidth").focus();
		return false;
	}else{
	    $("#pmWidthSpan").text('*');
	}
    
    if(isNaN(pmHeight))
    {
		$("#pmHeightSpan").text('高度必须为数字');
		$("#pmHeight").focus();
		return false;
	}else{
	    $("#pmHeightSpan").text('*');
	}
    
    if(isNaN(pmWeight))
    {
		$("#pmWeightSpan").text('重量必须为数字');
		$("#pmWeight").focus();
		return false;
	}else{
	    $("#pmWeightSpan").text('*');
	}
   
    if(isNaN(pmDimension))
    {
		$("#pmDimensionSpan").text('容积必须为数字');
		$("#pmDimension").focus();
		return false;
	}else{
	    $("#pmDimensionSpan").text('*');
	}
    
    if($.trim(pmDivider))//除数不为空时
    {
	   if(!$.trim(pmRatio)){//如果比值为空
           $("#pmDividerSpan").text('');
	       $("#pmRatioSpan").text("比值不能为空");
           return false;
	   }else{
	    $("#pmRatioSpan").text('');
	   }
       
       if(parseInt(pmRatio) != pmRatio){//比值不为整数时
           $("#pmDividerSpan").text('');
           $("#pmRatioSpan").text("比值必须为整数");
           return false;
       }else{
	    $("#pmRatioSpan").text('');
	   }	
	}else{
	   $("#pmRatioSpan").text('');
	}
    
    if(!$.trim(pmDivider))//除数为空时
    {
	   if($.trim(pmRatio)){//比值不为空
	       $("#pmDividerSpan").text("比值除数不能为空");
           return false;
	   }else{
	    $("#pmDividerSpan").text('');
	   }
	}else{
	   $("#pmDividerSpan").text('');
	}
   
    	
}