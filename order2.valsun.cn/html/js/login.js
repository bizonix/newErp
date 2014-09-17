$(function(){
	$('#username').focus();
});
function login(){
    var username = $('#username').val();
    var password = $('#password').val();
    $.post('json.php?mod=login&act=login&jsonp=1',
           {"username":username,"password":password},
           function(data){
                var result = $.parseJSON(data);
               if(result['errCode'] == 1){  
                   showErrMsg(result['errMsg']);
               }else if(result['errCode'] == 2){
                   window.location='index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=101';
               }
           }
        )
}

function showErrMsg(msg){
    $('#showerror').html(msg);
}

/*
 * 重置登陆表单
 */
function formreset()
{
    document.getElementById('loginform').reset();
}