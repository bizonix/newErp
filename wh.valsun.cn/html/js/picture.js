
$(function(){
	$("img.lazy").lazyload({ threshold : 200 });
	$("a.fancy").fancybox();
	/*$(".lazy").click(function(){
		var src1=$(this).attr('src');
		showDialog();
		$(".showpics").attr("src",src1);
		$(".showpics").show();
	});*/
	$(":radio[audit='ispass']").click(function(){
		var flag		=	$(this).val();
		var idft		=	$(this).attr('id');
		var ordersn		=	idft.substring(0,idft.length-1);
		var audituser	=   $(":hidden[name='userName']").val();
		var scanuser	=	$("#"+ordersn+"scanuser").text();
		var scantime	=	$(this).attr('scantime');
		var stype	=	$(this).attr('stype');
		//alert(ordersn+"-"+audituser+"-"+flag+"-"+scanuser+scantime+stype);
		$.ajax({
			type		: "POST",
			url			: 'json.php?mod=pictureAudit&act=updatePictureAudit',
			dataType	: "json",
			data		: {'stype':stype,'status':flag,'ordersn':ordersn,'audituser':audituser,'scanuser':scanuser,'scantime':scantime},
			success		: function	(msg){
				
				var date=("("+msg+")");
				alert(date);
				alert(date.errCode);
			}
		});
	});
	$("#excelOutPut").click(function(){
		
		var startdate	=	$("#startdate").val();
		var enddate		= 	$("#enddate").val();
		var scanuser	=	$("#scanuser").val();
		var pic_type	=	$("#pic_type").val();
		var pic_status	=	$("#pic_status").val();
		location.href="index.php?mod=pictureAudit&act=exceloutput&startdate="+startdate+"&enddate="+enddate+"&scanuser="+scanuser+"&pic_type="+pic_type+"&pic_status="+pic_status;
		
	});
	$("#testexcel").click(function(){
		location.href="index.php?mod=pictureAudit&act=exceltest";
	});
	
	
		/*$("#ordersn").click(function(){
		var ordersn		=	$("#ordersn").val();
		var startdate	=	$("#startdate").val();
		var enddate		= 	$("#enddate").val();
		var scanuser	=	$("#scanuser").val();
		var pic_type	=	$("#pic_type").val();
		alert(ordersn+"dd"+startdate);
		if(ordersn!=""){
			return true;
		}else{
			if(startdate==""){
				alert("开始时间不能为空！");
				return false;
			}else if(enddate==""){
				alert("结束时间不能为空！");
				return false;
			}else if(scanuser==""){
				alert("扫描人为空！");
				return false;
			}else if(pic_type==""){
				alert("请选择拍照类型");
				return false;
			}else{
				return true;
			}
		}
		});
			*/
	
	
	/*function transdate(endTime){
		var date=new Date();
		date.setFullYear(endTime.substring(0,4));
		date.setMonth(endTime.substring(5,7)-1);
		date.setDate(endTime.substring(8,10));
		date.setHours(endTime.substring(11,13));
		date.setMinutes(endTime.substring(14,16));
		date.setSeconds(endTime.substring(17,19));
		return Date.parse(date)/1000;
		}*/
});


function checkForm(){
	var ordersn		=	$("#ordersn").val();
	var startdate	=	$("#startdate").val();
	var enddate		= 	$("#enddate").val();
	var scanuser	=	$("#scanuser").val();
	var pic_type	=	$("#pic_type").val();
	if(ordersn!=""){
		return true;
	}else{
		if(startdate==""){
			alert("开始时间不能为空！");
			return false;
		}else if(enddate==""){
			alert("结束时间不能为空！");
			return false;
		}else if(scanuser==""){
			alert("扫描人为空！");
			return false;
		}else if(pic_type==""){
			alert("请选择拍照类型");
			return false;
		}else{
			return true;
		}
	}
}

function showDialog() {
    var objW = $(window); //当前窗口
    var objC = $(".showpics"); //对话框
    var brsW = objW.width();
    var brsH = objW.height();
    var sclL = objW.scrollLeft();
    
    var sclT = objW.scrollTop();
    var curW = objC.width();
    var curH = objC.height();
    //计算对话框居中时的左边距
    var left = sclL + (brsW - curW) / 2;
    //计算对话框居中时的上边距
    var top = sclT + (brsH - curH) / 2;
    //设置对话框在页面中的位置
    objC.css({ "left": left, "top": top });
  }//设置框的位置