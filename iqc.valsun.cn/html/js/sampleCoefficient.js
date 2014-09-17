$(function(){
    jQuery("#updateSampleCoff").validationEngine();
    
    $('#on').click(function(){
        var sampleTypeOnId = $("#sampleTypeOnId").val();
        var cNameOn = $("#cNameOn").val();
        if(sampleTypeOnId == 0 || cNameOn == 0){
            var status = '请选择要启动的类型和系数';
            //alert(sampleTypeOnId+'  '+cNameOn);
            window.location.href = "index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status="+status;
            return;
        }
        if(confirm("是否要启动该类型系数同时禁用同类型的其他系数？")){
            window.location.href = "index.php?mod=SampleCoefficient&act=onSampleCoefficient&sampleTypeOnId="+sampleTypeOnId+"&cNameOn="+cNameOn;
        }
	});

    $('.update').click(function(){
		var id = $(this).attr('name');
		window.location.href = "index.php?mod=SampleCoefficient&act=updateScanSampleCoefficient&id="+id;
	});

	$('#search').click(function(){
            var cName = $("#cName").val();
            var sampleTypeId = $("#sampleTypeId").val();
			window.location.href = "index.php?mod=sampleCoefficient&act=getSampleCoefficientList&type=search&cName="+cName+"&sampleTypeId="+sampleTypeId;
	});

	$("#back").click(function(){
		history.back();
	});
});

function submitUpdate(){
    var Ac = $("#Ac").val();
    var Re = $("#Re").val();
    var Al = $("#Al").val();
    var Rt = $("#Rt").val();

    if(!$.trim(Ac))
	{
		$("#AcSpan").text('允收数目不能为空');
		$("#Ac").focus();
		return false;
	}else{
	    $("#AcSpan").text('*');
	}
    
	if(!$.trim(Re))
	{
		$("#ReSpan").text('拒收数目不能为空');
		$("#Re").focus();
		return false;
	}else{
	    $("#ReSpan").text('*');
	}
    
    if(!$.trim(Al))
	{
		$("#AlSpan").text('追加数目不能为空');
		$("#Al").focus();
		return false;
	}else{
	    $("#AlSpan").text('*');
	}
    
	if(!$.trim(Rt))
	{
		$("#RtSpan").text('检测退回百分比不能为空');
		$("#Rt").focus();
		return false;
	}else{
	    $("#RtSpan").text('*');
	}
    
    if(isNaN(Ac))
    {
		$("#AcSpan").text('允收数目必须为数字');
		$("#Ac").focus();
		return false;
	}else{
	    $("#AcSpan").text('*');
	}
    
    if(isNaN(Re))
    {
		$("#ReSpan").text('拒收数目必须为数字');
		$("#Re").focus();
		return false;
	}else{
	    $("#ReSpan").text('*');
	}
    
    if(isNaN(Al))
    {
		$("#AlSpan").text('追加数目必须为数字');
		$("#Al").focus();
		return false;
	}else{
	    $("#AlSpan").text('*');
	}
    
    if(isNaN(Rt))
    {
		$("#RtSpan").text('检测退回百分比必须为数字');
		$("#Rt").focus();
		return false;
	}else{
	    $("#RtSpan").text('*');
	}
    
    if(parseInt(Ac) < parseInt(0))
    {
		$("#AcSpan").text('允收数目必须大于或等于0');
		$("#Ac").focus();
		return false;
	}else{
	    $("#AcSpan").text('*');
	}
    
    if(parseInt(Re) < parseInt(0))
    {
		$("#ReSpan").text('拒收数目必须大于或等于0');
		$("#Re").focus();
		return false;
	}else{
	    $("#ReSpan").text('*');
	}
    
    if(parseInt(Al) < parseInt(0))
    {
		$("#AlSpan").text('追加数目必须大于或等于0');
		$("#Al").focus();
		return false;
	}else{
	    $("#AlSpan").text('*');
	}
    
    if(parseInt(Rt) < parseInt(0) || parseInt(Rt) > parseInt(100))
    {
		$("#RtSpan").text('检测退回百分比必须介于0~100');
		$("#Rt").focus();
		return false;
	}else{
	    $("#RtSpan").text('*');
	}

}
