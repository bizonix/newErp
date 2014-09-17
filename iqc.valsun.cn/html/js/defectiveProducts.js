$(function(){
    jQuery("#moveDefective").validationEngine();
    
    $('.audit').click(function(){
        var defectiveId = $(this).attr('defective_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("是否对其审核？")){
            window.location.href = "index.php?mod=DefectiveProducts&act=auditDefectiveProducts&audit=audit&infoId="+infoId+"&defectiveId="+defectiveId;
        }
	});

    $('.scrapped').click(function(){
        var defectiveId = $(this).attr('defective_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 1){
            return;
        }
        if(confirm("要进行报废处理？")){
            window.location.href = "index.php?mod=DefectiveProducts&act=moveDefectiveProducts&type=scrapped&infoId="+infoId+"&defectiveId="+defectiveId;
        }
	});

	$('.inter').click(function(){
	    var defectiveId = $(this).attr('defective_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 1){
            return;
        }
        if(confirm("要进行内部处理？")){
            window.location.href = "index.php?mod=DefectiveProducts&act=moveDefectiveProducts&type=inter&infoId="+infoId+"&defectiveId="+defectiveId;
        }
	});

    $('.return').click(function(){
	    var defectiveId = $(this).attr('defective_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 1){
            return;
        }
        if(confirm("要进行待退回处理？")){
            window.location.href = "index.php?mod=DefectiveProducts&act=moveDefectiveProducts&type=return&infoId="+infoId+"&defectiveId="+defectiveId;
        }
	});

});

function submitUpdate(){
    var num = $("#num").val();
    var leftNum = $("#leftNum").val();
    if(!$.trim(num))
	{
		$("#numSpan").text('数量不能为空');
		$("#num").focus();
		return false;
	}else{
	    $("#numSpan").text('*');
	}

    if(isNaN(num)){
		$("#numSpan").text('数量只能为数字');
		$("#num").focus();
		return false;
	}else{
	    $("#numSpan").text('*');
	}

    if(num <= 0){
		$("#numSpan").text('数量必须大于0');
		$("#num").focus();
		return false;
	}else{
	    $("#numSpan").text('*');
	}

    if(parseInt(num) > parseInt(leftNum)){
		$("#numSpan").text('数量不能超过未处理量');
		$("#num").focus();
		return false;
	}else{
	    $("#numSpan").text('*');
	}
}
