function submitdata(){
	$('#categoryform').submit();
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