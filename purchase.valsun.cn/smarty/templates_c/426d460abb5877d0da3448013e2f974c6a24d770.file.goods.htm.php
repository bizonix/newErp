<?php /* Smarty version Smarty-3.1.12, created on 2013-11-14 21:03:03
         compiled from "/data/web/purchase.valsun.cn/html/template/goods.htm" */ ?>
<?php /*%%SmartyHeaderCode:7883393695284ca075d0520-88234068%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '426d460abb5877d0da3448013e2f974c6a24d770' => 
    array (
      0 => '/data/web/purchase.valsun.cn/html/template/goods.htm',
      1 => 1384344441,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7883393695284ca075d0520-88234068',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'fpage' => 0,
    'type' => 0,
    'warehousList' => 0,
    'list' => 0,
    'categoryList' => 0,
    'cate_two' => 0,
    'cate_three' => 0,
    'cate_four' => 0,
    'goodsList' => 0,
    'value' => 0,
    'categoryName' => 0,
    'warehous' => 0,
    'purchasers' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5284ca07706e10_89868177',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5284ca07706e10_89868177')) {function content_5284ca07706e10_89868177($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			<div  class="fourvar">
            	<div class="pathvar">
                	您的位置：<a href="index.php?mod=goods&act=goods_search_index">货品资料管理</a>&nbsp;>>&nbsp;货品搜索
                </div>
				<div class="pagination">
                	<?php echo $_smarty_tpl->tpl_vars['fpage']->value;?>

            	</div>
            </div>
            <div class="servar wh-servar">
	<span>
		<span>
			<input name="searchContent" id="searchContent" value="<?php echo $_GET['searchContent'];?>
" size="50">
			<input type="button" id="search" value="搜索">
            <label>
				<input name="searchtype" type="radio" value="1" <?php if ($_GET['searchtype']==1){?>checked="checked"<?php }?> <?php if (empty($_smarty_tpl->tpl_vars['type']->value)){?>checked="checked"<?php }?> />SKU
			</label>
			<label>
				<input name="searchtype" type="radio" value="2" <?php if ($_GET['searchtype']==2){?>checked="checked"<?php }?>/>仓位
			</label>
			<label>
				<input name="searchtype" type="radio" value="3" <?php if ($_GET['searchtype']==3){?>checked="checked"<?php }?>/>货品名称
			</label>
			<label>
            	<input name="searchtype" type="radio" value="5" <?php if ($_GET['searchtype']==5){?>checked="checked"<?php }?>/>供应商
            </label>
			<label>
				<input name="searchtype" type="radio" value="4" <?php if ($_GET['searchtype']==4){?>checked="checked"<?php }?>/>采购负责人
			</label>
		</span>
            &nbsp;&nbsp;
    <span style="color: red;"></span>
    </span>
	<div style="margin-top:10px;">
					<span style="width:60px;">产品状态：</span>
					<select  name="online" id="online" style="width:100px">
						<option value="">请选择</option>
						<option value="0" <?php if ($_GET['online']==="0"){?>selected<?php }?>>在线</option>
						<option value="1" <?php if ($_GET['online']==1){?>selected<?php }?>>下线</option>
						<option value="2" <?php if ($_GET['online']==2){?>selected<?php }?>>零库存</option>
						<option value="3" <?php if ($_GET['online']==2){?>selected<?php }?>>停售</option>
						<option value="4" <?php if ($_GET['online']==2){?>selected<?php }?>>部分停售</option>
						<option value="5" <?php if ($_GET['online']==2){?>selected<?php }?>>部分下线</option>
						<option value="6" <?php if ($_GET['online']==2){?>selected<?php }?>>缺货</option>
						<option value="7" <?php if ($_GET['online']==2){?>selected<?php }?>>无采购人</option>
					</select>
					<span style="width:60px;">仓库：</span>
					<select  name="warehouse" id="warehouse" style="width:100px">
						<option value="">请选择</option>
						<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehousList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_GET['warehouse']==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['whName'];?>
</option>
						<?php } ?>
					</select>
					<span style="width:60px;">新/老品：</span>
					<select  name="isnew" id="isnew" style="width:100px">
						<option value="">请选择</option>
						<option value="1" <?php if ($_GET['isnew']==1){?>selected<?php }?>>新品</option>
						<option value="0" <?php if ($_GET['isnew']==="0"){?>selected<?php }?>>老品</option>
					</select>
					<span style="width:60px;">类别：</span>
					<select id="pid_one" style="width:100px" onchange="select_one1();">
						<option value="">请选择</option>
						<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoryList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_GET['pid_one']==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['name'];?>
</option>
						<?php } ?>
					</select>
					<span id="div_two">
						<?php if (!empty($_GET['pid_one2'])){?>
						<select name='pid_one2' id='pid_one2' style='width:100px' onchange='select_one2();'>
							<option value="">请选择</option>
							<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cate_two']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_GET['pid_one2']==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['name'];?>
</option>
							<?php } ?>
						</select>
						<?php }?>
					</span>
					<span id="div_three">
						<?php if (!empty($_GET['pid_one3'])){?>
							<select name='pid_one3' id='pid_one3' style='width:100px' onchange='select_one3();'>
							<option value="">请选择</option>
							<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cate_three']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_GET['pid_one3']==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['name'];?>
</option>
							<?php } ?>
							</select>
						<?php }?>
					</span>
					<span id="div_four">
						<?php if (!empty($_GET['pid_one4'])){?>
							<select name='pid_one4' id='pid_one4' style='width:100px'>
							<option value="">请选择</option>
							<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cate_four']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_GET['pid_one4']==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['name'];?>
</option>
							<?php } ?>
							</select>
						<?php }?>
					</span>
				</div>
</div>
<div class="main underline-main">
	<table cellspacing="0" width="100%">
		<tbody>
        	<tr class="title purchase-title">
				<td width="10%">图片</td>
				<td align="left">sku</td>
				<td>仓位</td>
				<td>重量Kg</td>
				<td>成本RMB</td>
				<td>实际库存</td>
				<td>到货库存</td>
				<td>虚拟库存</td>
				<td>缺货库存</td>
				<td>产品类别</td>
				<td>产品状态</td>
				<td>新/老品</td>
				<td>仓库</td>
			</tr>	
			<?php if (!empty($_smarty_tpl->tpl_vars['goodsList']->value)){?>
			<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['goodsList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
			<tr >
                <td  rowspan="3"> 
	               	<a href="javascript:void(0)" id="imga-<?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
" class="fancybox">
						<img src="./public/img/ajax-loader.gif" name="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
" spu="<?php echo $_smarty_tpl->tpl_vars['value']->value['spu'];?>
">
			   		</a>
                 </td>
                <td align="left"><?php echo $_smarty_tpl->tpl_vars['value']->value['sku'];?>
</td>
                <td name="api_goodsCategory" data-goodscategory="1"></td>
                <td><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsWeight'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsCost'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['value']->value['stock_qty'];?>
</td>
                <td>到货库存</td>
                <td><?php echo $_smarty_tpl->tpl_vars['value']->value['stock_qty']-$_smarty_tpl->tpl_vars['value']->value['waiting_send'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['value']->value['stock_qty']-$_smarty_tpl->tpl_vars['value']->value['waiting_send']-$_smarty_tpl->tpl_vars['value']->value['autointerceptnums'];?>
</td>
				<?php $_smarty_tpl->tpl_vars['categoryName'] = new Smarty_variable(CommandAct::act_categoryName($_smarty_tpl->tpl_vars['value']->value['goodsCategory']), null, 0);?>
				<td><?php echo $_smarty_tpl->tpl_vars['categoryName']->value;?>
</td>
				<td><?php if ($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==="0"){?>在线<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==1){?>下线<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==2){?>零库存<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==3){?>停售<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==4){?>部分停售<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==5){?>部分下线<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==6){?>缺货<?php }elseif($_smarty_tpl->tpl_vars['value']->value['goodsStatus']==7){?>无采购人<?php }?></td>
				<td><?php if ($_smarty_tpl->tpl_vars['value']->value['isNew']==0){?>老品<?php }else{ ?>新品<?php }?></td>
				<td>
				<?php  $_smarty_tpl->tpl_vars['warehous'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['warehous']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehousList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['warehous']->key => $_smarty_tpl->tpl_vars['warehous']->value){
$_smarty_tpl->tpl_vars['warehous']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['value']->value['warehouseid']==$_smarty_tpl->tpl_vars['warehous']->value['id']){?>
						<?php echo $_smarty_tpl->tpl_vars['warehous']->value['whName'];?>
	
					<?php }?>
				<?php } ?>
				</td>
            </tr>
            <tr >                    	     	              	
                <td colspan="13" align="left"><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsName'];?>
</td>
            </tr>
            <tr >   	              	
                <td colspan="4" align="left">采购员：<?php echo GoodsAct::purchaseNameById($_smarty_tpl->tpl_vars['value']->value['purchaseId']);?>
</td>
  				<td colspan="10" align="left">供应商：<?php echo GoodsAct::partnerNameBySku($_smarty_tpl->tpl_vars['value']->value['sku']);?>
</td>
  			</tr>
		<?php } ?>			
		<?php }else{ ?>
			<tr >
					<td colspan="14">没有搜索值！</td>
			</tr>
		<?php }?>
  		</tbody>
    </table>
</div>
            <div class="bottomvar">
            	<div class="pagination">
                	<?php echo $_smarty_tpl->tpl_vars['fpage']->value;?>

            	</div>
            </div>
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript">
var web_api = "<?php echo @WEB_API;?>
";
var purchaser = "<?php echo $_smarty_tpl->tpl_vars['purchasers']->value;?>
";

//审核料号
$('#audit-btn').click(function(){
    var skuArr = $('input[name="checkbox"]:checked'),idArr=[];
	if(skuArr.length == 0){
		alertify.alert( '亲,您没有选择要审核的料号呢!', function (){
		return false;
	});
	}else {
		$.each(skuArr,function(i,item){
			idArr.push($(item).val());
		});
        var url  = web_api + "json.php?mod=goods&act=auditSku";
        var data = {"idArr":idArr};
		alertify.confirm("亲,真的要批量审核料号吗？", function (e) {
        if (e) {
			$.post(url,data,function(rtn){
				if(rtn.errCode == 0){              
					alertify.success("亲,批量审核料号成功!");
					window.location.reload();
				}else {
					 alertify.error(rtn.errMsg);
			   }
			},"jsonp");
		}});
	}
});

//为ID move-sku 附加一个事件处理函数
$("#move-sku").live("click", function(){
	$("select[class*=flexselect]").flexselect();
});
//移交料号
$('#move-sku').click(function(){
    var skuArr = $('input[name="checkbox"]:checked'),idArr=[];
	if(skuArr.length == 0){
		alertify.alert( '亲,您没有选择要移交的料号呢!', function (){
		return false;
	});
	}else {
		$.each(skuArr,function(i,item){
			idArr.push($(item).val());
		});
        var url  = web_api + "json.php?mod=goods&act=moveSku";
        var data = {"idArr":idArr};
		alertify.confirm("亲,真的要批量移交料号给以下采购吗？<p><br/>采购员:" + purchaser + "</p>", function (e) {
			if (e) {
				var purchase	= $('#purchasers').val();
				var data = {"idArr":idArr,"purchase":purchase};
				if(purchase == '0'){
					alertify.error('亲,您没有选择要移交给那个采购呢?');
					return false;
				}else {
					$.post(url,data,function(rtn){
						if(rtn.errCode == 0){              
							alertify.success("亲,批量移交料号成功!");
							window.location.reload();
						}else {
							 alertify.error(rtn.errMsg);
					   }
					},"jsonp");
				}
			}
		});
	}
});

function getCategoryPid()
{
    var pid1,pid2,pid3,pid4;
	pid1 = $("#pid_one").val();     
    if(pid1 == '0') {       
        return '0';
    } else {
        pid2 = $("#pid_one2").val();       
        if(pid2 == '0') {
            return pid1;
        } else {
            pid3 = $("#pid_one3").val();           
            if(pid3 == '0') {
                return pid2;
            } else {
                pid4 = $("#pid_one4").val();               
                if(pid4 == '0') {
                    return pid3;
                } else {
                    return pid4;
                }
            }
        }
    } 	
}



//搜索货品资料入口 mod by wxb 2013/11/7
$("#search").click(function(){
	var searchContent,searchtype,online,warehouse,isnew,pid_one;
	searchContent = $.trim($("#searchContent").val());
	searchtype = $.trim($("input[name='searchtype']:checked").val());
	
	online = $.trim($("#online").val());
	warehouse = $.trim($("#warehouse").val());
	isnew = $.trim($("#isnew").val());
	pid_one =  $.trim($("#pid_one").val());
	pid_one2 =  $.trim($("#pid_one2").val());
	pid_one3 =  $.trim($("#pid_one3").val());
	pid_one4 =  $.trim($("#pid_one4").val());
	
	var selFlag = true;
	if(online=='' && warehouse ==''  && isnew==''  && pid_one =='' ){
		selFlag = false;
	}
	var textFlag = true;
	if(searchContent==''){
		textFlag = false;
	}
	if(textFlag==false && selFlag==false){
		alertify.error('请选择搜索内容');
		return;
	}
	var url = "index.php?mod=goods&act=index";
	var param = "&pid_one2="+pid_one2+"&pid_one3="+pid_one3+"&pid_one4="+pid_one4;
	window.location.href = url+param+"&searchContent="+searchContent+"&searchtype="+searchtype+"&online="+online+"&warehouse="+warehouse+"&isnew="+isnew+"&pid_one="+pid_one;
});

//全选反选入口
$('#inverse-check').click(function(){
  select_all('inverse-check','input[name="checkbox"]',0);
});



function select_one1(){     //拉取二级目录  
	   
	var pid         = document.getElementById('pid_one').value;
	var div_two     = document.getElementById('div_two');
	var div_three   = document.getElementById('div_three');
	var div_four    = document.getElementById('div_four');
	if(pid=="")
	{
		div_two.style.display = "none";
		div_three.style.display = "none";
		div_four.style.display  = "none";
		$('#pid_one2').remove();
		$('#pid_one3').remove();
		$('#pid_one4').remove();    
		return false;
	}
    var url  = web_api + "json.php?mod=command&act=getCategoryInfo";
    var data = { "pid":pid  };	
//     console.log(data);
	$.post(url,data,function(rtn){
	   //console.log(rtn);
		if(rtn.errCode == 200){              
			show_second_cate(rtn.data);
		}else {
			 console.log(rtn.errMsg);
	    }
	},"jsonp");
}
                        
function show_second_cate(data) //展示二级目录  
{   
	var datas = data;
	var showdetail = document.getElementById("div_two");
	showdetail.innerHTML = "";
	if(datas.length!=0)
	{
		showdetail.style.display = "";
	}
	else
	{
		showdetail.style.display = "none";
	}
	var newtab = '';  
	newtab +="<select name='pid_one2' id='pid_one2' onchange='select_one2()'>";
	newtab +="<option value=''>请选择子类别</option>";   
	for(var i=0; i<datas.length; i++)
	{
		newtab +="<option value='"+datas[i].id+"'>"+datas[i].name+"</option>";
	}
	newtab +="</select>";
	showdetail.innerHTML = newtab;
}

function select_one2(){     //拉取三级目录  

	var path        = document.getElementById('pid_one2').value;    
    var pathArr;
    pathArr = path.split('-');
    pid =  pathArr[pathArr.length - 1];         
	var div_three  = document.getElementById('div_three');
	var div_four   = document.getElementById('div_four');
	if(pid=="")
	{
		div_three.style.display = 'none';
		$('#pid_one3').remove();
		$('#pid_one4').remove();
		return false;
	}
    var url  = web_api + "json.php?mod=command&act=getCategoryInfo";
    var data = {"pid":pid};	
//     console.log(data);
	$.post(url,data,function(rtn){
		if(rtn.errCode == 200){              
			show_third_cate(rtn.data);
		}else {
			 console.log(rtn.errMsg);
	    }
	},"jsonp");  
}

function show_third_cate(data) //展示二级目录  
{
	var datas = data;
	var showdetail = document.getElementById("div_three");
	showdetail.innerHTML = "";
	if(datas.length!=0)
	{
		showdetail.style.display = "";
	}
	else
	{
		showdetail.style.display = "none";
	}
	var newtab = '';
	newtab +="<select name='pid_one3' id='pid_one3' onchange='select_one3()'>";
	newtab +="<option value=''>请选择子类别</option>";
	for(var i=0; i<datas.length; i++)
	{
		newtab +="<option value='"+datas[i].id+"'>"+datas[i].name+"</option>";	
	}
	newtab +="</select>";
	showdetail.innerHTML = newtab;
}


function select_one3(){  //拉取四级目录  
	var path = document.getElementById('pid_one3').value;    
    var pathArr;
    pathArr = path.split('-');   
    pid =  pathArr[pathArr.length - 1];
	var div_four   = document.getElementById('div_four');
	if(pid=="")
	{
		div_four.style.display = 'none';
		$('#pid_one4').remove();
		return false;
	}
    var url  = web_api + "json.php?mod=command&act=getCategoryInfo";
    //var url  = web_api + "json.php?mod=goods&act=getCategoryPidMap";
    var data = {"pid":pid};	
	$.post(url,data,function(rtn){
	   //console.log(rtn);
		if(rtn.errCode == 200){              
			show_fourth_cate(rtn.data);
		}else {
			 console.log(rtn.errMsg);
	    }
	},"jsonp");
}

function show_fourth_cate(data)  //展示级目录  
{
	var datas = data;
	var showdetail = document.getElementById("div_four");
	showdetail.innerHTML = "";
	if(datas.length!=0)
	{
		showdetail.style.display = "";
	}
	else
	{
		showdetail.style.display = "none";
	}
	var newtab = '';
	newtab +="<select name='pid_one4' id='pid_one4'>";
	newtab +="<option value=''>请选择子类别</option>";  
	for(var i=0; i<datas.length; i++)
	{
		newtab +="<option value='"+datas[i].id+"'>"+datas[i].name+"</option>";		  
	}
	newtab +="</select>";
	showdetail.innerHTML = newtab;
}
//回车搜索
$(".servar.wh-servar").keydown(function(event){
	if(event.keyCode==13){
		$("#search").trigger("click");
	}
});
//页面加载完成后加载图片
$(function(){
	var url  = web_api + "json.php?mod=command&act=getSkuImg";
	var skuArr	= $('img[name="skuimg"]'), imgurl="", spu="", sku="";
	$.each(skuArr,function(i,item){
		spu	= $(item).attr('spu');
		$.ajax({
			url: url,
			type: "POST",
			async: true,
			timeout: 10000,
			dataType: "jsonp",
// 		jsonpCallback:"replay",
			success: function(rtn){
							sku	= $(item).attr('id').substring(5);
							if ($.trim(rtn.data)) {
								$("#imgs-"+sku).attr({"src":rtn.data,"width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href",rtn.data);
							} else {
								$("#imgs-"+sku).attr({"src":"./public/img/no_image.gif","width":"60px","height":"60px"});
							    $("#imga-"+sku).attr("href","./public/img/no_image.gif");
							}
				}	
			});
	});
});



</script><?php }} ?>