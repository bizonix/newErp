/*
 * 申请箱号
 */
function applyNum(){
	var number 	= $('#boxnum').val();
	window.open('index.php?mod=OwBoxManage&act=printBox&number='+number,'_blank');
}

function chooseCheckBox(){
	var checkList	= $('.chkbox');
	var len	= checkList.length;
	for(var i=0; i<len; i++){
		if(checkList[i].checked == true){
			checkList[i].checked = false;
		} else {
			checkList[i].checked = true;
		}
	}
}

function getChoosedBox(){
	var boxArr		= new Array();
	var checkList	= $('.chkbox');
	var len	= checkList.length;
	for(var i=0; i<len; i++){
		if(checkList[i].checked == true){
			boxArr.push(checkList[i].value); 
		}
	}
	return boxArr;
}
function printBox(){
	var boxObj	= getChoosedBox();
	if(boxObj.length ==0){
		alert('请选择要打印的箱号!');
		return false;
	}
	window.open('index.php?mod=OwBoxManage&act=rePrintBox&boxArr='+boxObj,'_blank');
}