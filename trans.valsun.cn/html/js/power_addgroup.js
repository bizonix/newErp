/*
 * 检测权限组名是否是系统唯一
 */
function checkgroupname(){
    showOkmsg('正在验证...');
    var groupname = $('#groupnameinput').val();
    if(groupname.length == 0){
        showerrmsg('不能为空值!');
        return false;
    }
    var D = new Date();
    $.getJSON(
            'json.php?mod=powergroup&act=groupValidateUnique&jsonp=1&groupname='+groupname+'&date='+D.getSeconds(),
            function (data){
                if(data['errCode'] == 1){   //数据验证正确
                    showOkmsg(data['errMsg']);
                    return true;
                } else {    //数据返回错误
                    showerrmsg(data['errMsg']);
                    return false;
                }
            }
        )
}

/*
 * 显示组名错误消息
 */
function showerrmsg(msg){
    $('#showmessage').html("<span style='color:red'>"+msg+"</span>");
}

/*
 * 显示组名正确消息
 */
function showOkmsg(msg){
    $('#showmessage').html("<span style='color:green'>"+msg+"</span>");
}

/*
 * 验证权限名是否唯一
 */
function checkpowername(){
    var selectval = $('#groupselect').val();
    //alert(selectval);
    if(selectval == 0){
        showerrmsg('请先选择所属组');
        return false;
    }
    showOkmsg('正在验证...');
    var powername = $('#groupnameinput').val();
    if(powername.length == 0){
        showerrmsg('不能为空值!');
        return false;
    }else if(powername.length >30){
        showerrmsg('名称不能超过30个字符!');
        return false;
    }
    
    var D = new Date();
    $.getJSON(
            'json.php?mod=power&act=powerValidateUnique&jsonp=1&gid='+selectval+'&code='+powername,
        function (data){
                if(data['errCode'] == 1){   //数据验证正确
                    showOkmsg(data['errMsg']);
                    return true;
                } else {    //数据返回错误
                    showerrmsg(data['errMsg']);
                    return false;
                }
            }
        )
}