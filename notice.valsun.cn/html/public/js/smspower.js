/**
 * 功能：修改用户短信权限
 * 作者：张志强
 * 时间：2014/08/20
 */

$(function() {
    var sms_dialog;
    var nameList = "";                                           //初始化存储要修改的用户名的全局变量
    $("#button-sms").on( "click", function() {
        nameList = "";                                           //点击前清空变量
        $("input[name=checkbox-list]").each(function() {  
            if($(this).attr("checked")) {                        //获取被选中名单，“，”分隔
               nameList += $(this).val();
               nameList += ",";
            }
        });
        if(nameList != "") {
            sms_dialog.dialog( "open" );
        } else {
            alertify.error('请选择要修改的用户!');
        }
    });
        
    sms_dialog = $("#sms-dialog").dialog({                         //设置弹窗
                    autoOpen:   false,
                    height  :   300,
                    width   :   500,
                    modal   :   true,
                    buttons : {
                             "提交": function() {
                                        var smsnum = $("#sms-num").val();
                                        $.ajax({
                                            url     :   "http://notice.valsun.cn/jsonNew.php?mod=userCompetence&act=updateUserCompetence&jsonp=1",
                                            type    :   "get",
                                            dataType:   "jsonp",
                                            data    :   {"smsnum":smsnum, "nameList":nameList},
                                            timeout :   60000,
                                            jsonp   :   "callback",
                                            error   :   function(XMLHttpRequest, textStatus, errorThrown) {
                                                            if(textStatus=="timeout") {
                                                                alertify.error('连接超时!');
                                                                xhr.abort();
                                                            }
                                                         },
                                            success :   function(data) {
                                                        if(data.ret === "ok")  {
                                                            var nameSuccess = "";
                                                            for(var i=0 ;i<data.success.length; i++) {
                                                                nameSuccess += data.success[i];
                                                            }
                                                            alertify.success( nameList + '修改成功！');
                                                        } else if(data.ret === "no") {
                                                            var nameError = "";
                                                            for(var i=0 ;i<data.error.length; i++) {
                                                                nameError += data.error[i];
                                                            }
                                                            alertify.error(nameError + '修改失败！');
                                                            var nameSuccess = ""
                                                            for(var i=0 ;i<data.success.length; i++) {
                                                                nameSuccess += data.success[i];
                                                            }
                                                            alertify.success( nameSuccess + '修改成功！');
                                                        }
                                                        $("#sms-num").val("");
                                                        sms_dialog.dialog( "close" );
                                            }
                                        });
                             },
                            "取消": function() {
                                        sms_dialog.dialog( "close" );
                            }
                 }
    });
});