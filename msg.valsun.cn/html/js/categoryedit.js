function submitdata(){
	var name	= $('#category_name').val();
	name	= $.trim(name);
	if(name.length == 0){
		alertify.error('名称不能为空!');
		return false;
	}
	var acount	= $('#countslector').val();
	if(acount == -1){
		alertify.error('请分配账号!');
		return false;
	}
	$('#categoryform').submit();
}

function selectmenu(){
	var checklist	= $('.rules');
	for(var i=0; i<checklist.length; i++){
		if(checklist[i].checked == true){
			checklist[i].checked	= false;
		} else {
			checklist[i].checked	= true;
		}
	}
}
