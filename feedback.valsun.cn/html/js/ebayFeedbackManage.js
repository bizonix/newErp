/*
 * ebayFeedback评价模块的js
 * ADD BY rdh
 */
$(function(){
	
	//全选/反选	
	$("#checkall").click(function(){
		var ckbs = $("input[name='fbkselect']"); 
		for(var i=0;i<ckbs.length;i++){
			if(ckbs[i].checked==false){
				ckbs[i].checked = true;
			}else{
				ckbs[i].checked = false;
			}
		}
	});

	$('#ebayChange_addItem_btn').click(function() {	
		var form = $('#ebayRquestChange_addItem_pop_form');
		form.dialog({
			width : 600,
			height : 300,
			modal : false,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确认添加' : function() {
					comfirmAdd();
					$(this).dialog('close');
				},

				'关闭' : function() {
					$(this).dialog('close');	
					
				}
			}
		});		
	});


	
	$('#ebayChange_mutildel_btn').click(function(){	
		var bill 			= new Array;
		$("input[name='fbkselect']").each(function(index, element) {
			if($(this).attr("checked") == "checked") {			
				bill.push($(this).val());
			}
		 });
		
		if(bill.length == 0){
			alertify.alert('你未选择任何项!');
			return false;
		}
		
		 alertify.confirm('确定要删除这些项吗?', function(e) {
	    	if(e) { 
	    		$.ajax({
	    			type	: "POST",
	    			dataType: "jsonp",
	    			url		: 'json.php?mod=EbayFeedback&act=requestChangeMutilDel&jsonp=1',
	    			data	: {'bill':bill},
	    			success	: function (msg){
	    				if(msg.errCode==0){
	    					alertify.success('rs批量删除成功');	
	    					window.location.reload();
	    				}else{			
	    					alertify.error(msg.errMsg);
	    				}				
	    			}    			
	    		});    		
	    	}
		 });
	});
	
	/**
	 * new 批量删除请求数据
	 */
	$('#ebayChange_mutildel_btn_new').click(function(){	
		var bill 			= new Array;
		$("input[name='fbkselect']").each(function(index, element) {
			if($(this).attr("checked") == "checked") {			
				bill.push($(this).val());
			}
		 });
		
		if(bill.length == 0){
			alertify.alert('你未选择任何项!');
			return false;
		}
		
		 alertify.confirm('确定要删除这些项吗?', function(e) {
	    	if(e) { 
	    		$.ajax({
	    			type	: "POST",
	    			dataType: "jsonp",
	    			url		: 'json.php?mod=EbayFeedback&act=changeMutilDel&jsonp=1',
	    			data	: {'bill':bill},
	    			success	: function (msg){
	    				if(msg.errCode==0){
	    					alertify.success('rs批量删除成功');	
	    					window.location.reload();
	    				}else{			
	    					alertify.error(msg.errMsg);
	    				}				
	    			}    			
	    		});    		
	    	}
		 });
	});
	
})



//批量删除
function ebayManageMutilDel() {
	alertify.confirm("你确定删除这些数据吗！",function(e){
		if(e){
			var bill = new Array;
			$("input[name='fbkselect']").each(function(index, element) {
				if($(this).attr("checked") == "checked") {			
					bill.push($(this).val());
				}
			 });
			if(bill == ""){
				alertify.alert("你没有选择任何选项!");
				//$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何选项-<span>');
				return false;
			}
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=EbayFeedback&act=orderMutilDel&jsonp=1',
				data	: {'bill':bill},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success('批量删除成功');	
						window.location.reload();
					}else{			
						alertify.error(msg.errMsg);
					}				
				}		
			});	
		}
	});
	
}

//搜索
function ebayManageSearch() {
     var userId,sku,latest_type,original_type,account,sort_type,ebay_start_time,ebay_end_time,feedbackReasonId;    
     userId      = $.trim($('#userId').val());             
     sku         = $.trim($('#sku').val());
     latest_type = $.trim($('#latest_type').val());               
     original_type = $.trim($('#original_type').val());       
     account     = $.trim($('#account').val());      
     sort_type   = $.trim($('#sort_type').val());  
     start_time = $.trim($('#start_time').val());
     end_time   = $.trim($('#end_time').val());   
     feedbackReasonId = $.trim($('#feedbackReasonId').val()); 
     var url = "index.php?mod=feedbackManage&act=ebayFeedbackManage&userId="+userId+"&sku="+sku+"&latest_type="+latest_type+"&original_type="+original_type+"&account="+account+"&sort_type="+sort_type+"&start_time="+start_time+"&end_time="+end_time+"&feedbackReasonId="+feedbackReasonId;
     window.location.href = url; 
}

//勾选全部sku
var checkSkuflag   = false;
function checkAllSku(){
    if(!checkSkuflag) {
        $("input[name='checkbox-list']").attr("checked","true");
        checkSkuflag = true;
    } else {
        $("input[name='checkbox-list']").removeAttr("checked");
        checkSkuflag = false;
    }
}

//隐藏edit显示save and cancel
function edit_click(id){		
	var edit 	= document.getElementById('edit_btn_'+id);
	var save 	= document.getElementById('save_btn_'+id);
	var cancel 	= document.getElementById('cancel_btn_'+id);
	var note    = document.getElementById('note_txt_'+id);
	edit.style.display 		= "none"
	save.style.display 		= "";
	cancel.style.display	= "";
	note.style.display 		= "";
}

//保存评价原因
function save_click(id){
	var edit 	= document.getElementById('edit_btn_'+id);
	var save 	= document.getElementById('save_btn_'+id);
	var cancel 	= document.getElementById('cancel_btn_'+id);
	var note 	= document.getElementById('note_txt_'+id);
	var reasonId = $.trim($('#note_txt_'+id).val()); 
	var	content	=$('#note_txt_'+id).find("option:selected").text() ;
	if(reasonId == ''){
		alertify.alert('请先选择原因!');
		return false;
	}	
	edit.style.display = "";
	save.style.display = "none";
	cancel.style.display = "none";
	note.style.display = "none";	
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=EbayFeedback&act=ebayReasonSave&jsonp=1',
		data	: {'id':id,'reasonId':reasonId},
		success	: function (msg){
			console.log(msg);
			if(msg.errCode==0){
				document.getElementById('reason'+id).innerHTML  = content;
				alertify.success('保存成功');	
				
			}else{			
				alertify.error(msg.errMsg);
			}			
		}		
	});	
}

//显示edit 隐藏 cancel and save
function cancel_click(id){
	var edit = document.getElementById('edit_btn_'+id);
	var save = document.getElementById('save_btn_'+id);
	var cancel = document.getElementById('cancel_btn_'+id);
	var note = document.getElementById('note_txt_'+id);
	note.value = "";	
	edit.style.display = "";
	save.style.display = "none";
	cancel.style.display = "none";
	note.style.display = "none";
}

/**
 * 手动修改评价
 * @returns {Boolean}
 */
function doChangeUpdate(id,type){
	alertify.confirm("确认修改吗？",function(e){
		if(e){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=EbayFeedback&act=doChangeUpdate&jsonp=1',
				data	: {'id':id,'type':type},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success("修改成功");
					}else{
						alertify.error(msg.errMsg);
				   }
				}
				});
		}
	});
}
//批量提交请求
function ebayManageMutilRequest(){
	var id,feedbackID,commentinguser,transactionID,itemID,commentText,sku,account,content;	
	var bill 	 = new Array;
	var obj		 = {};	
	var num      = 0;
	var strck    = "账号------------买家-------------交易ID<br/>"; 
	$("input[name='fbkselect']").each(function(index, element) {
		if($(this).attr("checked") == "checked") {
			num++;
			id 				   = $(this).val();
			obj		 		   = {};	
			obj.feedbackID 	   = $.trim($('#hd_feedbackID'+id).val());
			obj.commentinguser = $.trim($('#hd_commentingUser'+id).val());
			obj.transactionID  = $.trim($('#hd_transactionID'+id).val());
			obj.itemID 	       = $.trim($('#hd_itemID'+id).val());		
			obj.account 	   = $.trim($('#hd_account'+id).val());	
			obj.CommentType    = $.trim($('#hd_commentType'+id).val());
			strck              += obj.account+"_"+obj.commentinguser+"_"+obj.transactionID+"<br/>";
			bill.push(obj);
		}
	});
	if(bill == ""){
		alertify.alert("你没有选择任何项!");	
		return false;
	}
	alertify.confirm("你选择了"+num+"条数据<br/>"+strck,function(e){
		if(e){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=EbayFeedback&act=feedbackMutilRequest&jsonp=1',
				data	: {'bill':bill},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success("添加成功");
					}else{
						alertify.error(msg.errMsg);
				   }
				}
				});
		}
	});
	console.log(bill);
}
//批量回复
function ebayManageMutilReply() {	
	var id,feedbackID,commentinguser,transactionID,itemID,commentText,sku,account,content;	
	var bill 	 = new Array;
	var obj		 = {};		
	$("input[name='fbkselect']").each(function(index, element) {
		if($(this).attr("checked") == "checked") {
			id 				   = $(this).val();
			obj		 		   = {};	
			obj.feedbackID 	   = $.trim($('#hd_feedbackID'+id).val());
			obj.commentinguser = $.trim($('#hd_commentingUser'+id).val());
			obj.transactionID  = $.trim($('#hd_transactionID'+id).val());
			obj.itemID 	       = $.trim($('#hd_itemID'+id).val());		
			obj.account 	   = $.trim($('#hd_account'+id).val());				
			bill.push(obj);
		}
	});
	if(bill == ""){
		alertify.alert("你没有选择任何项!");	
		return false;
	}		
	console.log(bill);
	
	var form = $('#ebayMutilReply_pop_form');
	form.dialog({
		width : 600,
		height : 400,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确认添加' : function() {
				
				var content  = $.trim($('#ebayMutilReply_content').val());
				if(content == ""){
					alertify.alert("回复内容不能为空!");	
					return false;
				}
				if(content.length > 80) {
					alertify.alert('回复内容不能超过80字符!');
					return false;
				}
				
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=EbayFeedback&act=feedbackMutilReply&jsonp=1',
					data	: {'bill':bill,'content':content},
					success	: function (msg){
						console.log(msg);
						if(msg.errCode==0){
							alertify.confirm(msg.errMsg,function(e){
								if(e){
									window.location.reload();
								}else{
									window.location.reload();
								}
							});	
							//window.location.reload();
						}else{			
							alertify.confirm(msg.errMsg,function(e){
								if(e){
									window.location.reload();
								}else{
									window.location.reload();
								}
							});	
						}				
					}		
				});
				$(this).dialog('close');
			},

			'关闭' : function() {
				$(this).dialog('close');								
			}
		}
	});	
}




//批量Message 
function ebayManageMutilmessage() {	
	var id,feedbackID,commentinguser,transactionID,itemID,commentText,sku,account,content;	
	var bill 	 = new Array;
	var obj		 = {};		
	$("input[name='fbkselect']").each(function(index, element) {
		if($(this).attr("checked") == "checked") {
			id 				   = $(this).val();
			obj		 		   = {};	
			obj.feedbackID 	   = $.trim($('#hd_feedbackID'+id).val());
			obj.commentinguser = $.trim($('#hd_commentingUser'+id).val());
			obj.transactionID  = $.trim($('#hd_transactionID'+id).val());
			obj.itemID 	       = $.trim($('#hd_itemID'+id).val());		
			obj.account 	   = $.trim($('#hd_account'+id).val());				
			bill.push(obj);
		}
	});
	if(bill == ""){
		alertify.alert("你没有选择任何项!");	
		return false;
	}		
	//console.log(bill);
	
	var form = $('#ebayMutilMessage_pop_form');
	form.dialog({
		width : 600,
		height : 400,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确认添加' : function() {
				
				var content  = $.trim($('#ebayMutilMessage_content').val());
				if(content == ""){
					alertify.alert("回复内容不能为空!");	
					return false;
				}
				
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=EbayFeedback&act=feedbackMutilMessage&jsonp=1',
					data	: {'bill':bill,'content':content},
					success	: function (msg){
						if(msg.errCode==0){
							console.log(msg);
							alertify.success('批量Message成功');
							window.location.reload();
						}else{			
							alertify.error(msg.errMsg);
						}				
					}		
				});
				$(this).dialog('close');
			},

			'关闭' : function() {
				$(this).dialog('close');								
			}
		}
	});	
}

//触发回复邮件框
function feedback_message(id) {	
	var commentinguser = $.trim($('#hd_commentingUser'+id).val());
	var sku 	       = $.trim($('#hd_sku'+id).val());
	var commentText    = $.trim($('#hd_commentText'+id).val());	
	var itemId		   = $.trim($('#hd_itemID'+id).val());		
	$('#message_pop_commentingUser').html(commentinguser);
	$('#message_pop_sku').html(sku);
	$('#message_pop_commentText').html(commentText);
	$('#message_pop_itemID').html(itemId);	
	var form = $('#ebayMessage_pop_form');
	form.dialog({
		width : 600,
		height : 450,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确认添加' : function() {
				comfirmMessage(id);
				$(this).dialog('close');
			},
			'关闭' : function() {
				$(this).dialog('close');								
			}
		}
	});	
}

//回复邮件
function comfirmMessage(id) {	
	var feedbackID 	   = $.trim($('#hd_feedbackID'+id).val());
	var commentinguser = $.trim($('#hd_commentingUser'+id).val());
	var transactionID  = $.trim($('#hd_transactionID'+id).val());
	var itemID 	       = $.trim($('#hd_itemID'+id).val());
	var commentText    = $.trim($('#hd_commentText'+id).val());
	var sku 	       = $.trim($('#hd_sku'+id).val());
	var account 	   = $.trim($('#hd_account'+id).val());	
	var content  	   = $.trim($('#message_content').val());	
	if(content == '') {
		alertify.alert('回复内容不能为空!');
		return false;
	}	
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=EbayFeedback&act=feedbackMessage&jsonp=1',
		data	: {'feedbackID':feedbackID,'commentinguser':commentinguser,'transactionID':transactionID,'itemID':itemID,'commentText':commentText,'sku':sku,'account':account,'content':content},
		success	: function (msg){
			if(msg.errCode==0){
				console.log(msg);
				alertify.success('Message成功');	
				window.location.reload();
			}else{			
				alertify.error(msg.errMsg);
			}				
		}		
	});
}

//触发feedback replay
function feedback_reply(id) { 
	
	var commentinguser = $.trim($('#hd_commentingUser'+id).val());
	var sku 	       = $.trim($('#hd_sku'+id).val());
	var commentText    = $.trim($('#hd_commentText'+id).val());
	
	$('#reply_pop_commentingUser').html(commentinguser);
	$('#reply_pop_sku').html(sku);
	$('#reply_pop_commentText').html(commentText);
	
	var form = $('#ebayReply_pop_form');
	console.log(id);
	form.dialog({
		width : 600,
		height : 400,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确认回复' : function() {
				comfirmReply(id,'1');
				$(this).dialog('close');
			},

			'关闭' : function() {
				$(this).dialog('close');								
			}
		}
	});	
}

//回复评价
function comfirmReply(id) {	
	var feedbackID 	   = $.trim($('#hd_feedbackID'+id).val());
	var commentinguser = $.trim($('#hd_commentingUser'+id).val());
	var transactionID  = $.trim($('#hd_transactionID'+id).val());
	var itemID 	       = $.trim($('#hd_itemID'+id).val());
	var commentText    = $.trim($('#hd_commentText'+id).val());
	var sku 	       = $.trim($('#hd_sku'+id).val());
	var account 	   = $.trim($('#hd_account'+id).val());	
	var content  	   = $.trim($('#reply_content').val());	
	if(content == '') {
		alertify.alert('回复内容不能为空!');
		return false;
	}
	if(content.length > 80) {
		alertify.alert('回复内容不能超过80字符!');
		return false;
	}	
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=EbayFeedback&act=feedbackReply&jsonp=1',
		data	: {'feedbackID':feedbackID,'commentinguser':commentinguser,'transactionID':transactionID,'itemID':itemID,'commentText':commentText,'sku':sku,'account':account,'content':content},
		success	: function (msg){
			if(msg.errCode==0){
				console.log(msg);
				alertify.success('replay成功！');	
				window.location.reload();
			}else{			
				alertify.error(msg.errMsg);
			}				
		}		
	});
}
/*  
 * feedback change js
 * */
 



//评论
function ebayRequestChangeMutilMessage() {
	
	var id = 0;	
	var bill 	 = new Array;
	var obj		 = {};	
	$("input[name='fbkselect']").each(function(index, element) {
		if($(this).attr("checked") == "checked") {			
			id 				= $(this).val();		
			obj		 		= {};
			obj.ebayUserId 	= $.trim($('#hd_ebayUserId'+id).val());		
			obj.account 	= $.trim($('#hd_account'+id).val());				
			bill.push(obj);
		}
	 });
	if(bill == ""){
		alertify.alert("你没有选择任何项!");	
		return false;
	}
	
	var form = $('#RQMutilMessage_pop_form');
	form.dialog({
		width : 600,
		height : 300,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确认发送' : function() {
				
				var content  = $.trim($('#RQMutilMessage_content').val());
				if(content == ""){
					alertify.alert("回复内容不能为空!");	
					return false;
				}
				
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=EbayFeedback&act=feedbackChangeMutilMessage&jsonp=1',
					data	: {'bill':bill,'content':content},
					success	: function (msg){
						if(msg.errCode==0){
							alertify.success('批量发送成功');	
							$(this).dialog('close');
						}else{			
							alertify.error(msg.errMsg);
						}				
					}		
				});
				
			},

			'关闭' : function() {
				$(this).dialog('close');								
			}
		}
	});	
}

function ebayRequestChangeUpdate(user_id,ebay_account){
	var url		=	"index.php?mod=feedbackManage&act=ebayFeedbackRequestChange";
	alertify.confirm('确定修改?', function(e) {
		if(e){
			//alert(user_id+ebay_account);
			 $.ajax({
				type	:	"POST",
				dataType:	"jsonp",
				url		:	"json.php?mod=EbayFeedback&act=ebayRequestUpdate&jsonp=1",
				data	:  {"user_id":user_id,"ebay_account":ebay_account},
				success	:	function(msg){
					console.log(msg);
					if(msg.errCode==0){
						alertify.success('修改成功');	
						window.location.reload();
					}else{			
						alertify.error(msg.errMsg);
					}	
				}
			}) 
		}
	});
	
}

function ebayRequestChangeSerch() {
     var account,ebayUserId,modify_status,add_start_time,add_end_time;    
     account     	  = $.trim($('#account').val()); 
     ebayUserId       = $.trim($('#ebayUserId').val());             
     modify_status    = $.trim($('#modify_status').val());  
     add_start_time   = $.trim($('#start_time').val());
     add_end_time     = $.trim($('#end_time').val());  
     var url = "index.php?mod=feedbackManage&act=ebayFeedbackRequestChange&account="
     +account+"&ebayUserId="+ebayUserId+"&modify_status="
     +modify_status+"&add_start_time="+add_start_time+"&add_end_time="
     +add_end_time;
     window.location.href = url; 
}

/**
 * 新请求流程搜索
 * 
 */
function RequestChangeSerch() {
    var account,ebayUserId,modify_status,add_start_time,add_end_time;    
    account     	  = $.trim($('#account').val()); 
    ebayUserId        = $.trim($('#ebayUserId').val());             
    modify_status    = $.trim($('#modify_status').val());  
    add_start_time   = $.trim($('#start_time').val());
    add_end_time     = $.trim($('#end_time').val());  
    feedID           = $.trim($('#feedID').val());
    var url = "index.php?mod=feedbackManage&act=requestChangeManage&account="
    +account+"&ebayUserId="+ebayUserId+"&modify_status="
    +modify_status+"&add_start_time="+add_start_time+"&add_end_time="
    +add_end_time+"&feedbackID="+feedID;
    window.location.href = url; 
}

function comfirmAdd() {	
	var account = $.trim($('#request_add_account').val()); 
	var userId  = $.trim($('#request_add_userId').val());		
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=EbayFeedback&act=requestChangeAdd&jsonp=1',
		data	: {'account':account,'userId':userId},
		success	: function (msg){
			if(msg.errCode==0){
				alertify.success('添加成功!');
				window.location.reload();				
			}else{
				var msg = '<span style="color:red;font-size:20px">'+msg.errMsg+'<span>';
				$('#add_status').html(msg);
				alertify.error(msg.errMsg);
			}				
		}
	});
}

function ebayRequestDel(id) {
	
	if (id == '') {
		return false;
	}
	
	 alertify.confirm('确定要删除这些项吗?', function(e) {
    	if(e) { 	    		
    		$.ajax({
    			type	: "POST",
    			dataType: "jsonp",
    			url		: 'json.php?mod=EbayFeedback&act=requestChangeDel&jsonp=1',
    			data	: {'id':id},
    			success	: function (msg){
    				if(msg.errCode==0){
    					alertify.success('删除成功!');			
    				}else{
    					alertify.error(msg.errMsg);
    				}			
    				window.location.reload();
    			}
    		});    		
    	 }
	 });	 

}

function ebayDel(id) {
	
	if (id == '') {
		return false;
	}
	
	 alertify.confirm('确定要删除这些项吗?', function(e) {
    	if(e) { 	    		
    		$.ajax({
    			type	: "POST",
    			dataType: "jsonp",
    			url		: 'json.php?mod=EbayFeedback&act=changeDel&jsonp=1',
    			data	: {'id':id},
    			success	: function (msg){
    				if(msg.errCode==0){
    					alertify.success('删除成功!');			
    				}else{
    					alertify.error(msg.errMsg);
    				}			
    				window.location.reload();
    			}
    		});    		
    	 }
	 });	 

}

//重试获取Sku
function get_sku(CommentingUser,itemId,TransactionID,FeedbackID,CommentType){
	alert("aaa");
	/*$.ajax({
		type	 : "POST",
		dataType : "jsonp",
		url		 : 'json.php?mod=EbayFeedback&act=getsku&jsonp=1',
		data	 : {'TransactionID':TransactionID,'CommentingUser':CommentingUser,'itemId':itemId,'FeedbackID':FeedbackID,'CommentType':CommentType},
		success	 : function (msg){
			alert("ddddd");
			if(msg.errCode==0){
				window.location.reload();		
			}else{
				alertify.error(msg.errMsg);
			}			
		}
	});*/
	$.ajax({
		type	 : "POST",
		dataType : "jsonp",
		url		 : 'json.php?mod=EbayFeedback&act=getsku&jsonp=1',
		data	 : {'TransactionID':TransactionID,'CommentingUser':CommentingUser,'itemId':itemId,'FeedbackID':FeedbackID,'CommentType':CommentType},
		success	 : function (msg){
			if(msg.errCode==0){
						
			}else{
				alertify.error(msg.errMsg);
			}			
			window.location.reload();
		}
	});
}


/***补充料号 Start****/
function edit_patch(id){
	var btnedit 	= document.getElementById('patch_edit'+id);
	var btnsave 	= document.getElementById('patch_save'+id);
	var btncancel 	= document.getElementById('patch_cancel'+id);
	var show 	    = document.getElementById('patch'+id);
	
	btnedit.style.display 		= "none";
	show.style.display 			= "";
	btnsave.style.display 		= "";
	btncancel.style.display 	= "";
	
	document.getElementById('patch_sku'+id).value 		= '';
	document.getElementById('patch_amount'+id).value 	= '';
}
function cancel_patch(id){
	var btnedit 	= document.getElementById('patch_edit'+id);
	var btnsave 	= document.getElementById('patch_save'+id);
	var btncancel 	= document.getElementById('patch_cancel'+id);
	var show 	    = document.getElementById('patch'+id);
	btnedit.style.display 		= ""
	show.style.display 			= "none";
	btnsave.style.display 		= "none";
	btncancel.style.display 	= "none";
}
function save_patch(id){
	var btnedit 	= document.getElementById('patch_edit'+id);
	var btnsave 	= document.getElementById('patch_save'+id);
	var btncancel 	= document.getElementById('patch_cancel'+id);
	var show 	    = document.getElementById('patch'+id);	
	btnedit.style.display 		= "none"
	show.style.display 			= "none";
	btnsave.style.display 		= "none";
	btncancel.style.display 	= "none";	
	var sku 	    = document.getElementById('patch_sku'+id).value;
	var amount 	    = document.getElementById('patch_amount'+id).value;	
	
	$.ajax({
		type	 : "POST",
		dataType : "jsonp",
		url		 : 'json.php?mod=EbayFeedback&act=addsku&jsonp=1',
		data	 : {'id':id,'sku':sku,'amount':amount},
		success	 : function (msg){
			if(msg.errCode==0){
				document.getElementById('showsku'+id).innerHTML  = sku;
				document.getElementById('showqty'+id).innerHTML  = amount;			
			}else{
				alertify.error(msg.errMsg);
			}			
			window.location.reload();
		}
	});
	
	/*var url = 'update_feedback_sku.php';
	
	var param = 'id='+id+'&sku='+sku+'&amount='+amount;
	createXMLHttpRequest();	
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = updateSku; 
	xmlHttpRequest.send(param); */
	
}


/*function updateSku(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res 		= eval('('+xmlHttpRequest.responseText+')');
			var id 			= res.id;
			var sku      	= res.sku;
			var amount      = res.amount;
			var msg 		= res.msg;
			document.getElementById('showsku'+id).innerHTML  = sku;
			document.getElementById('showqty'+id).innerHTML  = amount;
			if(msg == '0'){
				alert('保存失败');
			}
		}
	}
}*/	
/***补充料号 End****/