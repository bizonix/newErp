/*
 * 客户关系管理 crmSystemList.js
 * ADD BY chenwei 2013.09.25
 */

$(function(){			
	//导出到EXCLS
	$("button[id='exportExcelButton']").click(function(){
		var thisForm = $("form[id='crmFrom']");
		var actionName = thisForm.attr("action");
		thisForm.attr("action", "index.php?mod=crmSystem&act=crmSystemExportExcel");
		thisForm.submit();
		thisForm.attr("action", actionName);
	});

});

/*
 * 选中或不选中表格中的全部checkbox
 */
function chooseornot(selfobj) {
    var ischecked = selfobj.checked
    var list = $('.checkclass');
    for (i in list) {
        list[i].checked = ischecked;
    }
}
