$(function(){
	//更新索引
	$('#positionIndex').click(function(){
		$('#mess').html('更新中请稍等...');
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=position&act=updatePositionIndex&jsonp=1",
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					window.location.href = "index.php?mod=position&act=positionList&state=更新成功";
				}else{
					window.location.href = "index.php?mod=position&act=positionList&state=更新失败，请重试！";
				}				
			}

		});
	});
	
	$('.position').click(function(){
		var storeid = $(this).attr('store');
		var coordinate = $(this).siblings('span').attr('id');
		var elem = "#"+$(this).attr('id');
		var axis_x = $(this).parent().attr('axis_x');
		var axis_y = $(this).parent().attr('axis_y');
		var floor  = $(this).parent().attr('floor');
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=position&act=getCurrentPosition&jsonp=1",
			data	: {axis_x:axis_x,axis_y:axis_y,floor:floor,storeid:storeid},
			success	: function (msg){
				//console.log(msg.data[1]);return false;
				if(msg.errCode==0){
					var name0 = '';
					var name1 = '';
					var name2 = '';
					var name3 = '';
					var name4 = '';
					var name5 = '';
					var name6 = '';
					var name7 = '';
					var name8 = '';
					var name9 = '';
					var enable0 = '';
					var type0 = '';
					var enable1 = '';
					var type1 = '';
					var enable2 = '';
					var type2 = '';
					var enable3 = '';
					var type3 = '';
					var enable4 = '';
					var type4 = '';
					var enable5 = '';
					var type5 = '';
					var enable6 = '';
					var type6 = '';
					var enable7 = '';
					var type7 = '';
					var enable8 = '';
					var type8 = '';
					var enable9 = '';
					var type9 = '';
					if(msg.data[0]){
						name0   = msg.data[0].name;
						enable0 = msg.data[0].enable;
						type0   = msg.data[0].type;
					}
					if(enable0 && enable0==1){enable0 = "checked";}
					if(type0 && type0==1){type0 = "checked";}
					
					if(msg.data[1]){
						name1   = msg.data[1].name;
						enable1 = msg.data[1].enable;
						type1   = msg.data[1].type;
					}
					if(enable1 && enable1==1){enable1 = "checked";}
					if(type1 && type1==1){type1 = "checked";}
					
					if(msg.data[2]){
						name2   = msg.data[2].name;
						enable2 = msg.data[2].enable;
						type2   = msg.data[2].type;
					}
					if(enable2 && enable2==1){enable2 = "checked";}
					if(type2 && type2==1){type2 = "checked";}
					
					if(msg.data[3]){
						name3   = msg.data[3].name;
						enable3 = msg.data[3].enable;
						type3   = msg.data[3].type;
					}
					if(enable3 && enable3==1){enable3 = "checked";}
					if(type3 && type3==1){type3 = "checked";}
					
					if(msg.data[4]){
						name4   = msg.data[4].name;
						enable4 = msg.data[4].enable;
						type4   = msg.data[4].type;
					}
					if(enable4 && enable4==1){enable4 = "checked";}
					if(type4 && type4==1){type4 = "checked";}
					
					if(msg.data[5]){
						name5   = msg.data[5].name;
						enable5 = msg.data[5].enable;
						type5   = msg.data[5].type;
					}
					if(enable5 && enable5==1){enable5 = "checked";}
					if(type5 && type5==1){type5 = "checked";}
					
					if(msg.data[6]){
						name6   = msg.data[6].name;
						enable6 = msg.data[6].enable;
						type6   = msg.data[6].type;
					}
					if(enable6 && enable6==1){enable6 = "checked";}
					if(type6 && type6==1){type6 = "checked";}
					
					if(msg.data[7]){
						name7   = msg.data[7].name;
						enable7 = msg.data[7].enable;
						type7   = msg.data[7].type;
					}
					if(enable7 && enable7==1){enable7 = "checked";}
					if(type7 && type7==1){type7 = "checked";}
					
					if(msg.data[8]){
						name8   = msg.data[8].name;
						enable8 = msg.data[8].enable;
						type8   = msg.data[8].type;
					}
					if(enable8 && enable8==1){enable8 = "checked";}
					if(type8 && type8==1){type8 = "checked";}
					
					if(msg.data[9]){
						name9   = msg.data[9].name;
						enable9 = msg.data[9].enable;
						type9   = msg.data[9].type;
					}
					if(enable9 && enable9==1){enable9 = "checked";}
					if(type9 && type9==1){type9 = "checked";}

					var html = '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">'
					+'<tr><td align="center">十层<input axis_z="9" name="pname" id="f10" value="'+name9+'"></td><td>是否已使用：<input type="Checkbox" '+enable9+' axis_z="4" id="fb10" value=""></td><td>是否可配货：<input type="Checkbox" '+type9+' axis_z="4" id="ft10" value=""></td></tr>'
					+'<tr><td align="center">九层<input axis_z="8" name="pname" id="f9" value="'+name8+'"></td><td>是否已使用：<input type="Checkbox" '+enable8+' axis_z="4" id="fb9" value=""></td><td>是否可配货：<input type="Checkbox" '+type8+' axis_z="4" id="ft9" value=""></td></tr>'
					+'<tr><td align="center">八层<input axis_z="7" name="pname" id="f8" value="'+name7+'"></td><td>是否已使用：<input type="Checkbox" '+enable7+' axis_z="4" id="fb8" value=""></td><td>是否可配货：<input type="Checkbox" '+type7+' axis_z="4" id="ft8" value=""></td></tr>'
					+'<tr><td align="center">七层<input axis_z="6" name="pname" id="f7" value="'+name6+'"></td><td>是否已使用：<input type="Checkbox" '+enable6+' axis_z="4" id="fb7" value=""></td><td>是否可配货：<input type="Checkbox" '+type6+' axis_z="4" id="ft7" value=""></td></tr>'
					+'<tr><td align="center">六层<input axis_z="5" name="pname" id="f6" value="'+name5+'"></td><td>是否已使用：<input type="Checkbox" '+enable5+' axis_z="4" id="fb6" value=""></td><td>是否可配货：<input type="Checkbox" '+type5+' axis_z="4" id="ft6" value=""></td></tr>'
					+'<tr><td align="center">五层<input axis_z="4" name="pname" id="f5" value="'+name4+'"></td><td>是否已使用：<input type="Checkbox" '+enable4+' axis_z="4" id="fb5" value=""></td><td>是否可配货：<input type="Checkbox" '+type4+' axis_z="4" id="ft5" value=""></td></tr>'
					+'<tr><td align="center">四层<input axis_z="3" name="pname" id="f4" value="'+name3+'"></td><td>是否已使用：<input type="Checkbox" '+enable3+' axis_z="4" id="fb4" value=""></td><td>是否可配货：<input type="Checkbox" '+type3+' axis_z="4" id="ft4" value=""></td></tr>'
					+'<tr><td align="center">三层<input axis_z="2" name="pname" id="f3" value="'+name2+'"></td><td>是否已使用：<input type="Checkbox" '+enable2+' axis_z="4" id="fb3" value=""></td><td>是否可配货：<input type="Checkbox" '+type2+' axis_z="4" id="ft3" value=""></td></tr>'
					+'<tr><td align="center">二层<input axis_z="1" name="pname" id="f2" value="'+name1+'"></td><td>是否已使用：<input type="Checkbox" '+enable1+' axis_z="4" id="fb2" value=""></td><td>是否可配货：<input type="Checkbox" '+type1+' axis_z="4" id="ft2" value=""></td></tr>'
					+'<tr><td align="center">一层<input axis_z="0" name="pname" id="f1" value="'+name0+'"></td><td>是否已使用：<input type="Checkbox" '+enable0+' axis_z="4" id="fb1" value=""></td><td>是否可配货：<input type="Checkbox" '+type0+' axis_z="4" id="ft1" value=""></td></tr>'
					+'<tr><td colspan="3" ><button align="bottom" axis_x="'+axis_x+'" axis_y="'+axis_y+'" floor="'+floor+'" class="adds" storeid="'+storeid+'" coordinate="'+coordinate+'" value="确定">确定</button>   <button class="closes" value="取消">取消</button><td></tr>'
					+'</table>';
					showLoading(html,elem);
				}else{
					alert(msg.errMsg);
				}				
			}

		});

		return false;
		$(this).css('display','none');
		$(this).parent().siblings().css('display','block');
		return false;
	});
	
	$('.closes').live('click', function(){
		hideTip();
	});
	
	$('.adds').live('click', function(){
		var storeid = $(this).attr('storeid');
		var id_obj = $(this).attr('coordinate');
		var all_position = new Array;		
		var axis_x = $(this).attr('axis_x');
		var axis_y = $(this).attr('axis_y');
		var floor  = $(this).attr('floor');
		
		var name1  	 = $('#f1').val();
		var axis_z1  = $('#f1').attr('axis_z');
		var enable1	 = $('#fb1').attr('checked');
		if(enable1==true){enable1=1;}else{enable1=0;}
		var type1 	 = $('#ft1').attr('checked');
		if(type1==true){type1=1;}else{type1=2;}
		var position1 = name1+","+axis_z1+","+enable1+","+type1;
		all_position.push(position1);
	
		var name2  	 = $('#f2').val();
		var axis_z2  = $('#f2').attr('axis_z');
		var enable2	 = $('#fb2').attr('checked');
		if(enable2==true){enable2=1;}else{enable2=0;}
		var type2 	 = $('#ft2').attr('checked');
		if(type2==true){type2=1;}else{type2=2;}
		var position2 = name2+","+axis_z2+","+enable2+","+type2;
		all_position.push(position2);
		
		var name3  	 = $('#f3').val();
		var axis_z3  = $('#f3').attr('axis_z');
		var enable3	 = $('#fb3').attr('checked');
		if(enable3==true){enable3=1;}else{enable3=0;}
		var type3 	 = $('#ft3').attr('checked');
		if(type3==true){type3=1;}else{type3=2;}
		var position3 = name3+","+axis_z3+","+enable3+","+type3;
		all_position.push(position3);
		
		var name4  	 = $('#f4').val();
		var axis_z4  = $('#f4').attr('axis_z');
		var enable4	 = $('#fb4').attr('checked');
		if(enable4==true){enable4=1;}else{enable4=0;}
		var type4 	 = $('#ft4').attr('checked');
		if(type4==true){type4=1;}else{type4=2;}
		var position4 = name4+","+axis_z4+","+enable4+","+type4;
		all_position.push(position4);
		
		var name5  	 = $('#f5').val();
		var axis_z5  = $('#f5').attr('axis_z');
		var enable5	 = $('#fb5').attr('checked');
		if(enable5==true){enable5=1;}else{enable5=0;}
		var type5 	 = $('#ft5').attr('checked');
		if(type5==true){type5=1;}else{type5=2;}
		var position5 = name5+","+axis_z5+","+enable5+","+type5;		
		all_position.push(position5);
		
		var name6  	 = $('#f6').val();
		var axis_z6  = $('#f6').attr('axis_z');
		var enable6	 = $('#fb6').attr('checked');
		if(enable6==true){enable6=1;}else{enable6=0;}
		var type6 	 = $('#ft6').attr('checked');
		if(type6==true){type6=1;}else{type6=2;}
		var position6 = name6+","+axis_z6+","+enable6+","+type6;		
		all_position.push(position6);
		
		var name7  	 = $('#f7').val();
		var axis_z7  = $('#f7').attr('axis_z');
		var enable7	 = $('#fb7').attr('checked');
		if(enable7==true){enable7=1;}else{enable7=0;}
		var type7 	 = $('#ft7').attr('checked');
		if(type7==true){type7=1;}else{type7=2;}
		var position7 = name7+","+axis_z7+","+enable7+","+type7;		
		all_position.push(position7);
		
		var name8  	 = $('#f8').val();
		var axis_z8  = $('#f8').attr('axis_z');
		var enable8	 = $('#fb8').attr('checked');
		if(enable8==true){enable8=1;}else{enable8=0;}
		var type8 	 = $('#ft8').attr('checked');
		if(type8==true){type8=1;}else{type8=2;}
		var position8 = name8+","+axis_z8+","+enable8+","+type8;		
		all_position.push(position8);
		
		var name9  	 = $('#f9').val();
		var axis_z9  = $('#f9').attr('axis_z');
		var enable9	 = $('#fb9').attr('checked');
		if(enable9==true){enable9=1;}else{enable9=0;}
		var type9 	 = $('#ft9').attr('checked');
		if(type9==true){type9=1;}else{type9=2;}
		var position9 = name9+","+axis_z9+","+enable9+","+type9;		
		all_position.push(position9);
		
		var name10   = $('#f10').val();
		var axis_z10 = $('#f10').attr('axis_z');
		var enable10 = $('#fb10').attr('checked');
		if(enable10==true){enable10=1;}else{enable10=0;}
		var type10 	 = $('#ft10').attr('checked');
		if(type10==true){type10=1;}else{type10=2;}
		var position10 = name10+","+axis_z10+","+enable10+","+type10;		
		all_position.push(position10);
		var position_str = all_position.join('|');
		//console.log(position_str);return false;
		
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=position&act=opisitionManage&jsonp=1",
			data	: {position:position_str,axis_x:axis_x,axis_y:axis_y,floor:floor,storeid:storeid},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					hideTip();
					$('#'+id_obj).html(name1);
				}else{
					alert(msg.errMsg);
				}				
			}

		});
	});
	
	$('.save').click(function(){
		var position = $.trim($(this).siblings('input').val());
		var coordinate = $(this).siblings('input').attr('coordinate');
		var obj = $(this).parent();
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=position&act=opisitionManage&jsonp=1",
			data	: {coordinate:coordinate,position:position},
			success	: function (msg){
				//console.log(msg.errCode);return false;
				if(msg.errCode==0){
					obj.css('display','none');
					obj.siblings().children('button').css('display','block');
					obj.siblings().children('span').html(position);
				}else{
					alert(msg.errMsg);
				}				
			}

		});
		
		return false;
	});
	
	$('.cancel').click(function(){
		$(this).parent().css('display','none');
		$(this).parent().siblings().children('button').css('display','block');
		return false;
	});
});
