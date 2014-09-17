function waveBoxColor(){
	var sameZoneColor    = $("#sameZoneColor").val();
	var crossZoneColor   = $("#crossZoneColor").val();
	var crossStoreyColor = $("#crossStoreyColor").val();
	if(sameZoneColor == crossZoneColor){
		alertify.error("不同类别不能设置相同颜色");
		return false;
	}else if(sameZoneColor == crossStoreyColor){
		alertify.error("不同类别不能设置相同颜色");	
		return false;
	}else if(crossZoneColor == crossStoreyColor){
		alertify.error("不同类别不能设置相同颜色");	
		return false;
	}
	var waveBoxColorForm = $('Form[name="waveBoxColorForm"]');
	waveBoxColorForm.submit();
	//alertify.error(sameZoneColor+"====="+crossZoneColor+"========"+crossStoreyColor);
	//alert("===============");
}