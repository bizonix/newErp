<?php /* Smarty version Smarty-3.1.12, created on 2014-01-10 21:18:55
         compiled from "/data/web/purchase.valsun.cn/html/template/analyze.htm" */ ?>
<?php /*%%SmartyHeaderCode:61222552452b92387ecef20-27558319%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a882131f76194736410bca4e5b8a10e7d8ff302c' => 
    array (
      0 => '/data/web/purchase.valsun.cn/html/template/analyze.htm',
      1 => 1389359926,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '61222552452b92387ecef20-27558319',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52b9238817e343_27919694',
  'variables' => 
  array (
    'title' => 0,
    'pageStr' => 0,
    'key' => 0,
    'type' => 0,
    'status' => 0,
    'partnerList' => 0,
    'list' => 0,
    'pid' => 0,
    'purchaseList' => 0,
    'pcid' => 0,
    'is_warn' => 0,
    'skuInfo' => 0,
    'availableStockCount' => 0,
    'averageDailyCount' => 0,
    'ptlist' => 0,
    'pt' => 0,
    '_userid' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52b9238817e343_27919694')) {function content_52b9238817e343_27919694($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=productStockalarm&act=index">预警管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	</div>
	<div class="pagination">
		<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

	</div>
</div>
<div class="servar">
	<span>关键字：<input id="key" type="text" <?php if ($_smarty_tpl->tpl_vars['key']->value){?> value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php }?>/>
	<span>搜索类型：<select id="type">
			<option value='-1'>请选择类型</option>
			<option value='sku'  <?php if ($_smarty_tpl->tpl_vars['type']->value=="sku"){?>selected="selected"<?php }?>>料号子SKU</option>
			<option value='spu' selected="selected">主料号</option>
			<option value= "goodsName" <?php if ($_smarty_tpl->tpl_vars['type']->value=="goodsName"){?>selected="selected"<?php }?>>产品名称</option>
	</select>
	</span>
	</span>
		 <span>产品状态：<select id="flag">
			<option value='-1'>选择状态</option> 
			<option value='1' <?php if ($_smarty_tpl->tpl_vars['status']->value=="1"){?> selected<?php }?>>在线</option> 
			<option value='2' <?php if ($_smarty_tpl->tpl_vars['status']->value=="2"){?> selected<?php }?>>下线</option> 
			<option value='3' <?php if ($_smarty_tpl->tpl_vars['status']->value=="3"){?> selected<?php }?>>零库存</option> 
			<option value='4' <?php if ($_smarty_tpl->tpl_vars['status']->value=="4"){?> selected<?php }?>>停售</option> 
			<option value='5' <?php if ($_smarty_tpl->tpl_vars['status']->value=="5"){?> selected<?php }?>>部分平台在线</option> 
	</select>
	</span>
		 <span>供应商：<select id="pid">
			<option value='-1'>请选择供应商</option>
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['partnerList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?> 
			<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['pid']->value==$_smarty_tpl->tpl_vars['list']->value['id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value['company_name'];?>
</option>
			<?php } ?> 
	</select>
	</span>	 
	<span>采购员：
			<select id="pcid">
			<option value="-1">请选择采购员</option> 
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['purchaseList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['userId'];?>
" <?php if ($_smarty_tpl->tpl_vars['pcid']->value==$_smarty_tpl->tpl_vars['list']->value['userId']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value["userName"];?>
</option>
			<?php } ?>
			</select> 
	</span>
	<div style="border:2px red ;height:10px;"></div>
	 <span>
		 预警状态：
		<select id="is_warn">
		<option value='-1' <?php if ($_smarty_tpl->tpl_vars['is_warn']->value=='-1'){?> selected<?php }?>>全部货品信息</option> 
		<option value='1'  <?php if ($_smarty_tpl->tpl_vars['is_warn']->value=='1'){?> selected<?php }?>>预警货品信息</option> 
		</select>
	</span>
	<span>每天均量排序：
		<select id="dailyNum">
			<option value='-1' <?php if ($_GET['dailyNum']=='-1'){?> selected<?php }?>>--请选择顺序排列--</option> 
			<option value='1' <?php if ($_GET['dailyNum']=='1'){?> selected<?php }?>>--销量从高到低--</option> 
			<option value='2' <?php if ($_GET['dailyNum']=='2'){?> selected<?php }?>>--销量从低到高--</option> 
		</select>
	</span>

	<span>已订购筛选：
		<select id="bookNum">
			<option value='-1' <?php if ($_GET['bookNum']=='-1'){?> selected<?php }?>>--请选择--</option> 
			<option value='1' <?php if ($_GET['bookNum']=='1'){?> selected<?php }?>>已订购</option> 
		</select>
	</span>

	 <span> <a href="javascript:void(0)" id="search-btn">搜 索</a>
	</span>
	 <span> <a href="javascript:void(0)" id="createPur">生成采购订单</a>
	</span>
	<span> <a href="javascript:void(0)" id="pl-partner">批量更新供应商</a>
	</span>
	<span> <a href="javascript:void(0)" id="stop-sale">暂时停售</a></span>
	<span> <a href="javascript:void(0)" id="forever-stop-sale">永久停售</a></span>
	<span> <a href="javascript:void(0)" id="begin-sale">开始销售</a></span>
	
</div>
<div class="main products-main reply-main warning-main pagemargin-main">
	<table cellspacing="0" width="100%">
		<tbody>
			<tr class="purchase-title title">
				<td>
					<input type="checkbox" name="inverse-check" id="inverse-check" />
				</td>
				<td>image</td>
				<td>SKU</td>
				<td>SPU</td>
				<td>成本</td>
				<td>实际库存</td>
				<td>待发货</td>
				<td>虚拟库存</td>
				<td>被拦截</td>
				<td>自动拦截</td>
				<td>待审核</td>
				<td>可用天数</td>
				<td>每天均量</td>
				<td>预警天数</td>
				<td>采购天数</td>
				<td>采购数量</td>
				<td>已订购</td>
				<td>在途数量</td>
				<td>建议采购数量</td>
				<td>下月预测</td>
				<td>重量</td>
				<td>在线状态</td>
				<td>采购</td>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['skuInfo']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['iteration']++;
?>
			<tr <?php if (($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['iteration']%2)==0){?>style="background-color:#f2f2f2;"<?php }?>>
				<td class="table-line" rowspan="2" style="border-bottom:1px solid #ccc;">
					<input type="checkbox" name="inverse" value="<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
" data-rec="<?php echo ceil($_smarty_tpl->tpl_vars['list']->value['purchaseDays']*$_smarty_tpl->tpl_vars['list']->value['everyday_sale']);?>
"/>
				</td>
				<td rowspan="2" class="table-line" style="border-bottom:1px solid #ccc;">
					<a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
" class="fancybox">
						<img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
"  width="60" height="60" data-spu="<?php echo $_smarty_tpl->tpl_vars['list']->value['spu'];?>
" data-sku="<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
">
			   		</a>
				</td>
		
				<td align="left" class="table-line" >
					<a href="javascript:void(0)" class="<?php if ($_smarty_tpl->tpl_vars['list']->value['is_warning']){?>openwarning<?php }else{ ?>unwarning<?php }?>"></a>
					<span class="font-16"><?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
</span>
				</td>
				<td><span class="font-16"><?php echo $_smarty_tpl->tpl_vars['list']->value['spu'];?>
</span></td>
				<td class="table-line" >
					<?php echo $_smarty_tpl->tpl_vars['list']->value['goodsCost'];?>

				</td>
				<td class="table-line" >
					<?php echo $_smarty_tpl->tpl_vars['list']->value['actualStockCount'];?>
		
				</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['waitingSendCount'];?>
</td>
				<td class="table-line" >
					<?php echo $_smarty_tpl->tpl_vars['list']->value['availableStockCount'];?>

				</td>
				<td class="table-line" >
					<?php echo $_smarty_tpl->tpl_vars['list']->value['interceptSendCount'];?>

				</td>
				<td class="table-line" >
					<?php echo $_smarty_tpl->tpl_vars['list']->value['shortageSendCount'];?>

				</td>
				<td class="table-line">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['waitingAuditCount'];?>

				</td>
				<td >
					<?php echo $_smarty_tpl->tpl_vars['availableStockCount']->value/$_smarty_tpl->tpl_vars['averageDailyCount']->value;?>

				</td>
				<td>
					<?php echo $_smarty_tpl->tpl_vars['list']->value['averageDailyCount'];?>

				</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['goodsdays'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['purchasedays'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['purchasedays'];?>
</td>
				<td><?php echo checkSkuOnWayNum($_smarty_tpl->tpl_vars['list']->value['sku']);?>
</td>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo $_smarty_tpl->tpl_vars['list']->value['goodsWeight'];?>
</td>
				<td><?php if ($_smarty_tpl->tpl_vars['list']->value['status']==1){?>在线<?php }elseif($_smarty_tpl->tpl_vars['list']->value['status']==2){?>暂时停售<?php }elseif($_smarty_tpl->tpl_vars['list']->value['status']==3){?>永久性停售<?php }else{ ?>在线<?php }?></td>
				<td><?php echo getUserNameById($_smarty_tpl->tpl_vars['list']->value['purchaseId']);?>
</td>
			</tr>
			<tr <?php if (($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['iteration']%2)==0){?>style="background-color:#f2f2f2;"<?php }?>>
				<td  colspan="7" style="border-bottom:1px solid #ccc;">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['goodsName'];?>

				</td>
				<td colspan="2"  style="border-bottom:1px solid #ccc;">

				</td>
				<?php $_smarty_tpl->tpl_vars['ptlist'] = new Smarty_variable(CommonModel::getSkuPartner($_smarty_tpl->tpl_vars['list']->value['sku']), null, 0);?>
				<td  colspan="7" style="border-bottom:1px solid #ccc;">
					
				</td>
				<td  style="border-bottom:1px solid #ccc;">
				</td>
				<td colspan="7" style="border-bottom:1px solid #ccc;">
					<?php  $_smarty_tpl->tpl_vars['pt'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pt']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ptlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pt']->key => $_smarty_tpl->tpl_vars['pt']->value){
$_smarty_tpl->tpl_vars['pt']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['pt']->value['company_name'];?>
<?php } ?>
				</td>
			</tr>
			<?php } ?>

		</tbody>
	</table>
<div>
<div class="bottomvar">
<div class="pagination">
	<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

</div>
</div>

<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['_userid']->value;?>
" id="userid" />
<?php echo $_smarty_tpl->getSubTemplate ("footer.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript">


//搜索入口	
function search(){
	var type,key,pid,pcid,is_warn,flag,url,dailyNum;
	url		= "index.php?mod=productStockalarm&act=analyze";
	key   	= encodeURIComponent($.trim($("#key").val()));
	type	= $.trim($("#type").val());
	pid		= $.trim($("#pid").val());
	pcid  	= $.trim($("#pcid").val());
	flag  	= $.trim($("#flag").val());
	is_warn = $.trim($("#is_warn").val());
	dailyNum = $.trim($("#dailyNum").val());
	bookNum = $.trim($("#bookNum").val());
	if(type != -1 && key != ''){
		url += "&type="+type+"&key="+key;
	}
	if(flag != -1){
		url += "&status="+flag;
	}
	if(pid != -1){
		url += "&pid="+pid;
	}
	if(is_warn != -1){
		url += "&is_warn="+is_warn;
	}
	if(pcid !=-1){
		url += "&pcid="+pcid;
	}
	if(dailyNum != -1){
		url += "&dailyNum="+dailyNum;
	}
	if(bookNum != -1){
		url += "&bookNum="+bookNum;
	}

	if(type != -1 && key == ''){
		alertify.error("请输入要查找的关键词！");
		$("#key").focus();
		return false;
	}
	window.location.href = url;
}
$("#search-btn").click(function() {
	search();
});
//全选入口
$("#inverse-check").click(function(){
	select_all("inverse-check","input[name='inverse']",0);
});
//生成采购订单
$("#createPur").click(function(){
	var skuArr , url,skulist = [];
	var skuArr 	= $('input[name="inverse"]:checked');
	if(skuArr.length == 0){
		alertify.alert('请选择需下订单的料号');
		return false;
	}else{
		$.each(skuArr ,function(i,item){
			var skuObj = {};
			skuObj.sku = $(item).val();
			skuObj.rec = $(item).data("rec"); //建议采购数量
			skulist.push(skuObj);
		});
	}
	var url  = "json.php?mod=purchaseOrder&act=createOrder";
	$.post(url, {"skulist":skulist}, function(rtn){
		console.log(rtn);
		var data = rtn.msg;
		if(data == 'noPower'){
			alertify.alert('您不是采购员,没有权限生成采购订单');
		}else if(data == 'success'){
			alertify.alert('生成采购订单成功',function(){
				window.location.reload();
			});
		}else{
			alertify.alert('生成采购订单失败'+data.msg);
		}
	},'jsonp');
});

$("#pl-partner").click(function(){
		var skulist,partner,url; 
		skulist = getSkuList();
		partnerId = $("#pid").val();
	    url  = "json.php?mod=purchaseOrder&act=updatePartner";
		if(skulist.length == 0 || partnerId == -1){
			alertify.alert('请选择需要更新供应商的料号 and 供应商。。。');
		}else{
			$.post(url,{"skulist":skulist,"partnerId":partnerId},function(rtn){
				//console.log(rtn);
				alertify.alert('批量更新供应商成功。。。',function(){
					window.location.reload();
				});
			});
		}
});

//新更新缓存
$("#updateCache").click(function(){
	var url  	= "json.php?mod=productStockalarm&act=updateWarnNew";
	var skuArr 	= $('input[name="inverse"]:checked'), sku = "", tips = "", errmsg = "";
	if (skuArr.length == 0) {
		alertify.alert('请选择需要更新缓存的料号');
		return false;
	}
	tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;'></span>";
	alertify.alert(tips);
	$("#aOK").hide();
	var curid = isok = iserr = 0;
	$.each (skuArr,function(i,item) {
		sku = $(item).val();
		$("#label-tips").html("正在批更新料号缓存,请稍候...<br/>处理期间，请不要关闭或刷新当前页面，谢谢配合！");
		$.post (url, {"sku":sku}, function(rtn) {
			if (rtn.errCode=='0') {
				$("#label-tips").html(rtn.data);
			} else {
				$("#label-tips").html(rtn.errMsg);
				iserr++
			}
			if (curid==(skuArr.length-1)) {
				$("#aOK").show().click(function(){
					window.location.reload();
				});
			}
			if (iserr>0) {
				errmsg	= "   一共失败: "+iserr+" 个料号";
			}
			$("#label-tips").html($("#label-tips").html()+"<br/>处理进度："+ ((curid+1) +" / "+skuArr.length)+errmsg);
			curid++
		},'jsonp');
	});	
});

function getSkuList(){
	var skuArr , skulist = [];
	var skuArr 	= $('input[name="inverse"]:checked');
	if(skuArr.length != 0){
		$.each(skuArr ,function(i,item){
			var sku = $(item).val();
			skulist.push(sku);
		});
	}
	return skulist;
}

$("#stop-sale").click(function(){//暂时停售
		var skuArr = getSkuList();
		if(skuArr.length == 0){
			alertify.alert('请选择需要暂时停售的料号，这个操作日均量将不会更新');
			return;
		}
		changeSkuStatus(skuArr,2);
});

$("#forever-stop-sale").click(function(){//永久停售
		var skuArr = getSkuList();
		if(skuArr.length == 0){
			alertify.alert('请选择需要永久停售的料号，这个操作日均量将不会更新');
			return;
		}
		changeSkuStatus(skuArr,3);
});

$("#begin-sale").click(function(){//开始上线销售
		var skuArr = getSkuList();
		if(skuArr.length == 0){
			alertify.alert('请选择需要上线销售的料号');
			return;
		}
		changeSkuStatus(skuArr,1);
});

function changeSkuStatus(skuArr,status){ //记录sku 在采购系统中的状态
	var url = "json.php?mod=sku&act=changeSkuStatus";
	$.post(url,{"skuArr":skuArr,"status":status},function(rtn){
			console.log(rtn);
			if($.inArray(0,rtn) == -1){
				alertify.alert("操作成功。。。。",function(){
					window.location.reload();
				});
			}else{
			alertify.alert("操作failed。。。。。",function(){
				window.location.reload();
			});
			}
	},"json");
}

window.onkeyup = function(e) {
	if(e.keyCode == 13) {
		search();
	}
}


</script>
<?php }} ?>