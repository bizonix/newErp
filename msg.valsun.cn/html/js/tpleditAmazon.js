function submitdata(){
	if($('#title').val()==''){
		alertify.error('标题不能为空!');
		$('#title').focus();
		return false;
	}
	if($('#content').val().length==0){
		alertify.error('模板内容不能为空!');
		$('#content').focus();
		return false;
	}
	if($('#content').val().length>20000){
		alertify.error('模板内容不能超过20000个字符!');
		$('#content').focus();
		return false;
	}
	//$('#categoryform').submit();
}

function changeSet(obj,id){
	var checkList	= $('.country_'+id);
	var len	= checkList.length;
	for(var i=0; i<len; i++){
		if(checkList[i].checked==true){
			checkList[i].checked = false;
		} else {
			checkList[i].checked=true;
		}
	}
}
$(function(){
	$('#categoryform').submit(function(event){
		alert(11111);
	});
})