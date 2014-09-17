
function check_number(obj){
	//alert(obj.value);
	obj.value = obj.value.replace(/\D/g,'');
}

function showAlert(id,content){
	$("#tip-content").html(content);
	$("#"+id).popwin({"left":"center","top":"center"}).show();
}


function changevcode(){
	$('#vcodeimg').attr({"src":"../../lib/captcha.php?time="+Math.random(1,10)});
}

function IsEmail(str){
    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return reg.test(str);
}

function isMobile(str) {
    var patrn =  /^0*(13|15|18)\d{9}$/ ;
	if(patrn.test(str)){
		return true;
	}else{
		return false;
	}
}

function isTel(str) {
	var pattern=/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)/;
    if(pattern.test(str)) {
        return true;
    } else {
        return false;
    }
}

function IsTelephone(str) {
	if (isMobile(str) || isTel(str)) {
       return true;
   }
   else {
       return false;
   }
}

function isQQ(str) {
	var pattern=/^\d{6,11}$/;
    if(pattern.test(str)) {
        return true;
    } else {
        return false;
    }
}

$(document).ready(function(){
    $("#close-btn").click(function(){
         $("#alert-tip").hide();
    });
});
