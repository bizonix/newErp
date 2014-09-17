
function login(){
    var username = $('#username').val();
    var password = $('#password').val();
    //alert(password)
    $.post('json.php?mod=login&act=login&jsonp=1',
           {"username":username,"password":password},
           function(data){
                //����json
                var result = $.parseJSON(data);
               if(result['errCode'] == 1){  
                   showErrMsg(result['errMsg']);
               }else if(result['errCode'] == 2){   
                   window.location='index.php?mod=query&act=showform';
               }
           }
        )
}


function showErrMsg(msg){
    $('#messagespan').html(msg);
}
