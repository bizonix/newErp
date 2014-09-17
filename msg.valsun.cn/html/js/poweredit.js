function choseall(){
	box = $('.checkclass');
	var len = box.length;
	for(var i=0; i<len ; i++){
		box[i].checked='checked';
	}
}

function rechose(){
	box = $('.checkclass');
	var len = box.length;
	for(var i=0; i<len ; i++){
		if(box[i].checked==true){
			box[i].checked=false;
		} else {
			box[i].checked=true;
		}
	}
}