$(function(){
    jQuery("#addSampleCoff").validationEngine();
    
    $("#cName").blur(function(){
        var cName = $("#cName").val();
        var sampleTypeId = $("#sampleTypeId").val();
        var sizeCodeId = $("#sizeCodeId").val();
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=SampleCoefficient&act=checkAddCoeff&jsonp=1',
        		data	: {cName:cName,sampleTypeId:sampleTypeId,sizeCodeId:sizeCodeId},
        		success	: function (ret){
        			if(ret.errCode == '1111' && cName != ''){
        				$("#cNameSpan").text('√');
                        $("#check").text('');
        			}else{
                        $("#cNameSpan").text('×');
                        $("#check").text('已有记录，该系数不可用');
                        if(!$.trim(cName)){
                            $("#check").text('系数名称不能为空');
                        }
        			}			
        		}    
        	});        
    });
    
    $("#sampleTypeId").change(function(){
        var cName = $("#cName").val();
        var sampleTypeId = $("#sampleTypeId").val();
        var sizeCodeId = $("#sizeCodeId").val();
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=SampleCoefficient&act=checkAddCoeff&jsonp=1',
        		data	: {cName:cName,sampleTypeId:sampleTypeId,sizeCodeId:sizeCodeId},
        		success	: function (ret){
        			if(ret.errCode == '1111' && cName != ''){
        				$("#cNameSpan").text('√');
                        $("#check").text('');
        			}else{
                        $("#cNameSpan").text('×');
                        $("#check").text('已有记录，该系数不可用');
                        if(!$.trim(cName)){
                            $("#check").text('系数名称不能为空');
                        }
                        $("#cName").focus();
        			}			
        		}           
        	});        
    });
    
    $("#sizeCodeId").change(function(){
        var cName = $("#cName").val();
        var sampleTypeId = $("#sampleTypeId").val();
        var sizeCodeId = $("#sizeCodeId").val();
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=SampleCoefficient&act=checkAddCoeff&jsonp=1',
        		data	: {cName:cName,sampleTypeId:sampleTypeId,sizeCodeId:sizeCodeId},
        		success	: function (ret){
        			if(ret.errCode == '1111' && cName != ''){
        				$("#cNameSpan").text('√');
                        $("#check").text('');                  
        			}else{
                        $("#cNameSpan").text('×');
                        $("#check").text('已有记录，该系数不可用');
                        if(!$.trim(cName)){
                            $("#check").text('系数名称不能为空');
                        }
                        $("#cName").focus();
        			}			
        		}           
        	});        
    });
    
});

function check(){
    if(!$.trim($("#check").text())){
        return true;
    }else{
        return false;
    }
}

