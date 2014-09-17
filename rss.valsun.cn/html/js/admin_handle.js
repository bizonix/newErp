var paramCompany = 1;
var paramDept	 = 1;
var paramJob	 = 1;
var paramJob1	 = 1;
var paramJobList = 1;
var paramid1	 = 1;
var paramid2	 = 1;
var deletePara	 = 1;
function showDept(obj) {
	/*
	 * 根据公司显示部门
	 */
	var company_id  = obj.value;
	var e_id	    = obj.id;		
	var dept		= e_id.split("_");
	var dept_id		= dept[1];
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=showDept&company_id=" + company_id,
		  dataType: "json",
	      success: function(data){
	    	var listDept = "";
	    	listDept += "<select>";
	    	listDept += "<option value='default'>-----请选择-----</option>";
			$(data).each(function(i,item){
				listDept += "<option value='" + item.company_id + "_" + item.dept_id + "'>" + item.dept_name + "</option>";
			});
			listDept += "</select>";
			$('#dept_' + dept_id).html(listDept);
	      }
		});
}
function showJob(obj) {
	/*
	 * 根据公司、部门显示岗位
	 */
	var company		= obj.id;		// 获取公司下拉菜单ID
	var dept		= company.split("_");
	var job			= dept[1];
	var company_id	= $('#company_' + job).val();
	var deptArr	 	= obj.value;
	var deptArrSplit = deptArr.split("_");
	var dept_id		= deptArrSplit[1];
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=showJob&company_id=" + company_id + "&dept_id=" + dept_id,
		  dataType: "json",
	      success: function(data){
	    	var listJob = "";
			$(data).each(function(i,item){
				if((i+1)%5 == 0){
					listJob += "<br />";
				}
				listJob += "<label>";
				listJob += "<input type='checkbox' class='checkJob' name='jobs[]' value=" + item.company_id + "_" + item.dept_id + "_" + item.job_id + " /><font>" + item.job_name + "</font>";
				listJob += "</label>";
			});
			$('#job_' + job).html(listJob);
	      }
		});
}
function addJobPower() {
	/*
	 * 新增岗位权限列表
	 */
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=showCompany",
		  dataType: "json",
	      success: function(data){
	    	var appendList = "";
	    	appendList += "<tr id='id_" + paramid1++ + "'>";
	    	appendList += "<td>";
	    	appendList += "<select name='company[]' id='company_" + paramCompany++ + "' onChange='showDept(this);'>";
	    	appendList += "<option value='default'>-----请选择-----</option>";
			$(data).each(function(i,item){
				appendList += "<option value=" + item.company_id + ">" + item.company_name + "</option>";
			});
			appendList += "</select>";
			appendList += "<select name='dept[]' id='dept_" + paramDept++ + "' onChange='showJob(this);checkPowerRepeat(this);' class='dept'>";
			appendList += "<option value='default'>-----请选择-----</option>";
			appendList += "</select>";
			appendList += "<td id='delete_" + deletePara++ + "'><a href='#' onclick='removejob("+ paramid2++ +");'>删除</a></td>";
			appendList += "</td>";
			appendList += "</tr>";
			appendList += "<tr id='showJob_" + paramJob++ + "'>";
			appendList += "<td id='job_" + paramJobList++ + "'>";
			appendList += "</td>";
			appendList += "</tr>";
			$('#show tr:last').after(appendList);  //在table最后一行新增权限
	      }
		});
}
function removejob(obj) {
	/*
	 * 未提交之前移除岗位列表
	 */
	var com_id		= obj;
	var removeid 	= $('#id_' + com_id );
	var removejob 	= $('#showJob_' + com_id )
	removeid.remove();
	removejob.remove();
}
function modifyPower(id) {
	/*
	 * 修改邮件权限
	 */
	var url			= "index.php?mod=MailManage&act=modifyPower&list_id=" + id;
	window.location	= url;
}
function addMailList() {
	/*
	 * 跳转到新增邮件页面
	 */
	var url				= "index.php?mod=MailManage&act=showMailList";
	window.location		= url;
}
function deleteMail(id) {
	/*
	 * 删除邮件分类
	 */
	jConfirm('亲，确定要删除该邮件吗？', '请确定',function(res){
	if(res == true) {	//点击确定
		$.ajax({
			type: "GET",
			url: "index.php?mod=MailManage&act=deleteMail&list_id=" + id,
			dataType: "html",
			success: function(data){
				var url				= "index.php?mod=MailManage&act=showMailPower";
				window.location		= url;
			}
		});
	} else {	//点击取消
				
	}
	});
}
function deleteUser(id, name) {
	/*
	 * 删除已订阅邮件用户
	 */
	jConfirm('亲，确定要删除该订阅人吗？', '请确定',function(res){
	if(res == true) {	//点击确定
		$.ajax({
			type: "GET",
			url: "index.php?mod=MailManage&act=deleteUser&list_id=" + id +"&user_id=" + name,
			dataType: "html",
			success: function(data){
				setTimeout('window.location.reload()');
			}
		});
	} else {	//点击取消
				
	}
	});
}
function addMailUser(id) {
	//跳转到新增订阅用户页面
	var url				= "index.php?mod=MailManage&act=addUser&list_id=" + id;
	window.location		= url;
}
function showUserJob(obj) {
	/*
	 * 新增订阅用户显示岗位
	 */
	var company		= obj.id;		// 获取公司下拉菜单ID
	var dept		= company.split("_");
	var job			= dept[1];
	var company_id	= $('#company_' + job).val();
	var deptArr	 	= obj.value;
	var deptArrSplit = deptArr.split("_");
	var dept_id		= deptArrSplit[1];
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=showJob&company_id=" + company_id + "&dept_id=" + dept_id,
		  dataType: "json",
	      success: function(data){
	    	var listJob = "";
			listJob += "<select name='jobs[]' id='job_0' onChange='checkUserPower({$list_id}, this);showUser(this);' class='job'>";
	    	listJob	+= "<option value='default'>-----请选择-----</option>";
			$(data).each(function(i,item){
				listJob += "<option value=" + item.company_id + "_" + item.dept_id + "_" + item.job_id + " >" + item.job_name + "</option>";
			});
			listJob += "</select>";
			$('#job_0').html(listJob);
	     }
	});
}
function showUser(name) {
	//显示每个岗位的成员名称
	var user		= name.value;
	var info		= user.split("_");
	var company		= info[0];
	var dept		= info[1];
	var job			= info[2];
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=showUsers&company=" + company + "&dept=" + dept + "&job=" + job,
		  dataType: "json",
	      success: function(data){
	    	  var listUser = "";
			  $(data).each(function(i,item){
				  if((i+1)%10 == 0){
					  listUser += "<br />";
				  }
				  listUser += "<label>";
				  listUser += "<input type='checkbox' class='checkUser' name='users[]' value='" + company + "_" + dept + "_" + job + "_" + item.global_user_id + "' /><font>" + item.global_user_name + "</font>";
				  listUser += "</label>";
			  });
			  $('#user_0').html(listUser);
	       }
	 });
}
function checkUserPower(id, obj) {
	/*
	 * 新增订阅人判断是否有权限，若没有则询问是否新增邮件岗位权限
	 */
	var jobdata		= obj.value;
	var jobinfo		= jobdata.split("_");
	var company		= jobinfo[0];
	var dept		= jobinfo[1];
	var job			= jobinfo[2];
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=checkUserPower&company=" + company + "&dept=" + dept + "&job=" + job + "&list_id=" + id,
		  dataType: "json",
	      success: function(data) {
	    	  if(data.status == 0) {
	    		  jConfirm('该岗位尚未设置权限，确定增加该权限吗？', '请确定',function(res){
	    		  if(res == true) {	//点击确定
	    			  $.ajax({
	    				  type: "GET",
	    				  url: "index.php",
	    				  data: "mod=MailManage&act=addJobPower&company=" + company + "&dept=" + dept + "&job=" + job + "&list_id=" + id,
	    				  dataType: "json",
	    			      success: function(info){
	    			    	  if(info.status == 0) {
	    			    		  $.alerts.okButton="确定";
	    		    		      jAlert("添加失败！",'提示');
	    		    		      return true;
	    			    	  } else {
	    			    		  $.alerts.okButton="确定";
	    		    		      jAlert("添加成功！",'提示');
	    		    		      return true;
	    			    	  }
	    			      }
	    			  });
	    			} else {	//点击取消
		    			  
		    		}
	    		 });
	    	  } else {
	    		  return false;
	    	  }
	     }
	 });
}