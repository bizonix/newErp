$(function(){
	var counter=0;
	var div_outer=document.createElement('div');
	var div_inner=document.createElement('div');
	div_outer.className='reg_bg';
	div_inner.className='reg_box';
	$('#amazonaccount').click(function(){
		if(counter++>1){
			return;
		}
		
		div_inner.innerHTML="<p><span>Amazon账号:</span><input type='text' id='account'>" +
				"<p><span>Amazon站点:</span><select  name='site' id='site'>" +
				"<option value='US'>US</option><option value='Other'>Other</option>" +
				"</select><br />"+
				"<p><span>Gmail邮箱:</span><input type='text' id='gmail'><span>@gmail.com</span>" +
				"<p><span>Gmail密码:</span><input type='password' id='password' maxlength='50' ><br />" +
				"<p><span>Gmail密码确认:</span><input type='password' id='repassword' maxlength='16'><br />"+ 
				"<a class='btn_add'>添加</a>"+
				"<a class='btn_cancle'>取消</a>"
		
		document.body.appendChild(div_outer);
		document.body.appendChild(div_inner);
		$('.reg_bg').fadeIn();
		$('.reg_box').fadeIn();
		$('.btn_cancle').click(function(){
			$('.reg_bg').fadeOut();
			$('.reg_box').fadeOut();
			counter=0;
		})
		$('.btn_add').click(function(){
			var senddata={account:$('#account').val(),site:$('#site').val(),gmail:$('#gmail').val()+'@gmail.com',password:$('#password').val()};
			if(!validate()){
				return ;
			}
			
			$.post('index.php?mod=localPowerAmazon&act=addAmazonAccount',senddata,function(data){
				if(data.match('添加成功')){
					alertify.success('添加成功');
					setTimeout(function(){
						$('.reg_bg').fadeOut();
						$('.reg_box').fadeOut();
						counter=0;
					},1000);
				} else if(data.match('该邮箱已被使用')) {
					$(this).attr('disabled','');
					alertify.error('该邮箱已被使用');
				}
			})
		})
	});
	
	
	
	
	
	
	function validate(){
		
			if($("#account").val()==""){
				alertify.error('账号不能为空');
				$("#account").focus();
				return false;
			}
			if($("#gmail").val()==""){
				alertify.error('邮箱不能为空');
				$("#gmail").focus();
				return false;
			}

			if($("#gmail").val().search(/^[a-zA-Z0-9]+$/)<0){
				alertify.error('Gmail邮箱格式不正确');
				$("#gmail").focus();
				return false;
			}
			if($("#password").val()==""){
				alertify.error('密码不能为空');
				$("#password").focus();
				return false;
			}
			
			if($("#repassword").val()!=$("#password").val()){
				alertify.error('密码不一致');
				$("#repassword").focus();
				return false;
			}
			
			return true;
		}
	
})

