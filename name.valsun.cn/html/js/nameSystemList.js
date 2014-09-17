/*
 * 名称系统管理 nameSystemList.js
 * ADD BY chenwei 2013.09.25
 */

$(function(){		
	//POST数据验证
	$("#borrow-write").validationEngine({autoHidePrompt:true});
	
	//JQ时间	
	$("input#startTime, input#endTime").datetimepicker({
		beforeShow: customRange,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
	});
	function customRange(input) {
		return {minDate: (input.id == "endTime" ? jQuery("#startTime").datepicker("getDate") : null),
			maxDate: (input.id == "startTime" ? jQuery("#endTime").datepicker("getDate") : null)};
	}
	
	//导出到EXCLS
	$("button[id='exportExcelButton']").click(function(){
		var thisForm = $("form[id='crmFrom']");
		var actionName = thisForm.attr("action");
		thisForm.attr("action", "index.php?mod=crmSystem&act=crmSystemExportExcel");
		thisForm.submit();
		thisForm.attr("action", actionName);
	});
	
	//添加名称
	$('#addName').click(	
		function() {
			$('#addNewName').val('');
			$('#chooseSystem').val('');
			$('#chooseNameType').val('');
			$('#addFunctionNote').val('');
			$('#addNewNameVerifyList').html('');
			$('#form-borrow-dialog').dialog({
				width: 550,
				height: 450,
				modal: true,
				autoOpen: true,
				show: 'drop',
				hide: 'drop',
				buttons: {
					'取消': function() {
						
						$(this).dialog('close');
					},
					'提交': function() {
						/*
						 * 申请新名称验证
						 */
						 var addNewName = $.trim($('#addNewName').val()); 
						// alert(addNewName);return false;
						 if(addNewName == ''){
							 $("#borrow-write").submit();
						 }else{
							 $.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=nameSystem&act=nameSystemVerify&jsonp=1',
								data	: {addNewName:addNewName},
								success	: function (msg){
									if(msg.errCode=='200'){
										scanProcessTip(msg.errMsg,true);
										$("#borrow-write").submit();										
									}else{
										$('#addNewName').focus();
										scanProcessTip(msg.errMsg,false);
									}				
								}
							});
						 }
						 
					}
				}
			});
		}
	);
	
	//删除
	$('#delName').click(
		function (){		
			var checkboxs = document.getElementsByName("nameSystemId");
			var bill      = '';		
			
			for(var i=0;i<checkboxs.length;i++){		
				if(checkboxs[i].checked == true){
					bill = bill + ","+checkboxs[i].value;
				}
			}
		
			if(bill == ""){
				alert("你没有选择任何料号!");
				return false;	
			}else{
				if(confirm("确定要删除这些名称吗？")){
					$.ajax({
						type	: "POST",
						dataType: "jsonp",
						url		: 'json.php?mod=nameSystem&act=delName&jsonp=1',
						data	: {bill:bill},
						success	: function (msg){
							if(msg.errCode=='200'){
								alert(msg.errMsg);
								location.href='index.php?mod=nameSystem&act=nameSystemList';										
							}else{
								alert(msg.errMsg);
								return false;
							}				
						}
					});
				}	
			}
		}
	);

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
	var b	= 0;
	var checkboxs = document.getElementsByName("nameSystemId");
	for(var i=0;i<checkboxs.length;i++){
		if(checkboxs[i].checked == true){
			b++;
		}

	}

	document.getElementById('tipList').innerHTML="您已经选择 <font color=red>"+b+"</font> 条记录 ^_^";
}

function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
			$('#addNewNameVerifyList').html(str);
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
			$('#addNewNameVerifyList').html(str);
		}
		
	}catch(e){}
}

function displayselect(){

	var b	= 0;
	var checkboxs = document.getElementsByName("nameSystemId");
	for(var i=0;i<checkboxs.length;i++){
		if(checkboxs[i].checked == true){
			b++;
		}

	}

		document.getElementById('tipList').innerHTML="您已经选择 <font color=red>"+b+"</font> 条记录 ^_^";
}