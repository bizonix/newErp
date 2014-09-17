<?php /* Smarty version Smarty-3.1.12, created on 2014-02-22 09:36:20
         compiled from "/data/web/purchase.valsun.cn/html/template/productStockalarm.htm" */ ?>
<?php /*%%SmartyHeaderCode:49630222252b79c2c78b8c1-29825698%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5c6063eaa1db2aaaf06749fec2c221f96866f45e' => 
    array (
      0 => '/data/web/purchase.valsun.cn/html/template/productStockalarm.htm',
      1 => 1393032970,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49630222252b79c2c78b8c1-29825698',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52b79c2c958ff0_76857627',
  'variables' => 
  array (
    'title' => 0,
    'pageStr' => 0,
    'key' => 0,
    'status' => 0,
    'partnerList' => 0,
    'list' => 0,
    'pid' => 0,
    'purchaseList' => 0,
    'is_warn' => 0,
    'lists' => 0,
    'ptlist' => 0,
    'pt' => 0,
    '_userid' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52b79c2c958ff0_76857627')) {function content_52b79c2c958ff0_76857627($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="fourvar">
	<div class="pathvar">
		您的位置：<a href="index.php?mod=productStockalarm&act=index">预警管理</a>&nbsp;>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['title']->value;?>

	</div>
	<div class="pagination">
		<?php echo $_smarty_tpl->tpl_vars['pageStr']->value;?>

	</div>
</div>
<div class="servar">
	<span>关键字：<input id="key" type="text" style="width:300px;height:30px" <?php if ($_smarty_tpl->tpl_vars['key']->value){?> value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php }?>/>
	<span>搜索类型：<select id="type">
			<option value='sku'  <?php if ($_GET['type']=="sku"){?>selected="selected"<?php }?>>料号子SKU</option>
			<option value='spu' <?php if ($_GET['type']=="sku"){?>selected="selected"<?php }?> >主料号</option>
			<option value= "goodsName" <?php if ($_GET['type']=="goodsName"){?>selected="selected"<?php }?>>产品名称</option>
			<option value= "partner" <?php if ($_GET['type']=="partner"){?>selected="selected"<?php }?>>供应商</option>
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
			<option value="<?php echo $_smarty_tpl->tpl_vars['list']->value['global_user_id'];?>
" <?php if ($_GET['pcid']==$_smarty_tpl->tpl_vars['list']->value['global_user_id']){?> selected="selected"<?php }elseif($_SESSION['sysUserId']==$_smarty_tpl->tpl_vars['list']->value['global_user_id']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['list']->value["global_user_name"];?>
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
		<option value='2'  <?php if ($_smarty_tpl->tpl_vars['is_warn']->value=='2'){?> selected<?php }?>>缺货已订购</option> 
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
	<span> <a href="javascript:void(0)" id="getSkuData">获取旧ERP数据</a></span>
	<span style="display:none"> <a href="javascript:void(0)" id="updateCache">新系统更新缓存</a></span>
	<span> <a href="javascript:void(0)" id="checkAlert">判断预警</a></span>

	<!--
	<span> <a href="javascript:void(0)" id="stop-sale">暂时停售</a></span>
	<span> <a href="javascript:void(0)" id="forever-stop-sale">永久停售</a></span>
	<span> <a href="javascript:void(0)" id="begin-sale">开始销售</a></span>
	-->
	
</div>
<div class="main products-main reply-main warning-main pagemargin-main">
	<table cellspacing="0" width="100%">
		<tbody>
			<tr class="purchase-title title">
				<td>
					<input type="checkbox" name="inverse-check" id="inverse-check" />
				</td>
				<td></td>
				<td>
					是否<br>预警
				</td>
				<td>
					产品
					<br />
					编号
				</td>
				<td>
					产品
					<br />
					成本
				</td>
				<td>
					海外
					<br />
					库存
				</td>
				<td>
					实际
					<br />
					库存
				</td>
				<td>
					待发
					<br />
					货
				</td>
				<td>
					被拦
					<br />
					截
				</td>
				<td>
					自动
					<br />
					拦截
				</td>
				<td>
					待审
					<br />
					核
				</td>
				<td>
					虚拟
					<br />
					库存
				</td>
				<td>
					缺货
					<br />
					库存
				</td>
				<td>
					可用
					<br />
					天数
				</td>
				<td>
					每天
					<br />
					均量
				</td>
				<td>
					预警
					<br />
					天数
				</td>
				<td>
					采购
					<br />
					天数
				</td>
				<td>
					已订
					<br />
					购
				</td>
				<td>
					在途
					<br />
					数量
				</td>
				<td>
					建议采	
					<br />
					购数量
				</td>
				<td>
					下月
					<br />
					预测
				</td>
				<td>
					重量
				</td>
				<td>
					产品
					<br />
					状态
				</td>
				<td>
					采购
				</td>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['foo']['iteration']++;
?>
			<tr <?php if (($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['iteration']%2)==0){?>style="background-color:#f2f2f2;"<?php }?>>
				<td class="bor-top" rowspan="2">
					<input type="checkbox" name="inverse" value="<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
" data-rec="<?php echo ceil($_smarty_tpl->tpl_vars['list']->value['purchaseDays']*$_smarty_tpl->tpl_vars['list']->value['everyday_sale']);?>
"/>
				</td>
				<td class="bor-top" rowspan="2">
					<a href="javascript:void(0)" id="imgb-<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
" class="fancybox">
						<img src="" class="skuimg" id="imgs-<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
"  width="60" height="60" data-spu="<?php echo $_smarty_tpl->tpl_vars['list']->value['spu'];?>
" data-sku="<?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
">
			   		</a>
				</td>
		
				<td class="bor-top" align="left">
					<?php if ($_smarty_tpl->tpl_vars['list']->value['is_warning']){?><span style="color:red">是</span><?php }else{ ?><span style="color:green">否</span><?php }?>
				</td>
				<td class="bor-top" align="left">
					<span class="font-16"><?php echo $_smarty_tpl->tpl_vars['list']->value['sku'];?>
</span>
				</td>
				<td class="bor-top">
					￥<?php echo $_smarty_tpl->tpl_vars['list']->value['goodsCost'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['ow_stock'];?>
		
				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['stock_qty'];?>
		
				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['salensend'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['interceptnums'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['autointerceptnums'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['auditingnums'];?>

				</td>
				<td class="bor-top">
					<?php if (($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend'])>0){?> <?php echo ($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend']);?>
 <?php }else{ ?> <font color='red'><?php echo ($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend']);?>
</font><?php }?>
				</td>
				<td class="bor-top">
					<?php if (($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend']-$_smarty_tpl->tpl_vars['list']->value['autointerceptnums']>0)){?> <?php echo ($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend']-$_smarty_tpl->tpl_vars['list']->value['autointerceptnums']);?>
 <?php }else{ ?> <font color='red'><?php echo $_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend']-$_smarty_tpl->tpl_vars['list']->value['autointerceptnums'];?>
</font><?php }?>
				</td>
				<td class="bor-top">
					<?php if ($_smarty_tpl->tpl_vars['list']->value['first_sale']==0){?>从未卖<?php }elseif(($_smarty_tpl->tpl_vars['list']->value['last_sale']<time()-3600*24*30)){?>月未卖<?php }elseif($_smarty_tpl->tpl_vars['list']->value['stock_qty']==$_smarty_tpl->tpl_vars['list']->value['salensend']){?>0 <?php }else{ ?> <?php echo str_replace(".00",'',round_num((($_smarty_tpl->tpl_vars['list']->value['stock_qty']-$_smarty_tpl->tpl_vars['list']->value['salensend'])/$_smarty_tpl->tpl_vars['list']->value['everyday_sale']),2));?>
<?php }?>
				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['everyday_sale'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['alertDays'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['purchaseDays'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['booknums'];?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['it_stock'];?>

				</td>
				<td class="bor-top" style="color:green">
					<?php echo ceil($_smarty_tpl->tpl_vars['list']->value['purchaseDays']*$_smarty_tpl->tpl_vars['list']->value['everyday_sale']);?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['everyday_sale']*30;?>

				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['goodsWeight'];?>

				</td>
				<td class="bor-top">
					<?php if ($_smarty_tpl->tpl_vars['list']->value['goodsStatus']==1){?>在线<?php }elseif($_smarty_tpl->tpl_vars['list']->value['goodsStatus']==2){?>下线<?php }elseif($_smarty_tpl->tpl_vars['list']->value['goodsStatus']==3){?>零库存<?php }elseif($_smarty_tpl->tpl_vars['list']->value['goodsStatus']==4){?>停售<?php }elseif($_smarty_tpl->tpl_vars['list']->value['goodsStatus']==5){?>部分平台下线<?php }?>
				</td>
				<td class="bor-top">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['global_user_name'];?>

				</td>
			</tr>
			<tr <?php if (($_smarty_tpl->getVariable('smarty')->value['foreach']['foo']['iteration']%2)==0){?>style="background-color:#f2f2f2;"<?php }?>>
				<td  colspan="7">
					<?php echo $_smarty_tpl->tpl_vars['list']->value['goodsName'];?>

				</td>
				<td colspan="2">

				</td>
				<?php $_smarty_tpl->tpl_vars['ptlist'] = new Smarty_variable(CommonModel::getSkuPartner($_smarty_tpl->tpl_vars['list']->value['sku']), null, 0);?>
				<td colspan="7">
					
				</td>
				<td>
				</td>
				<td colspan="5">
					供应商：<?php  $_smarty_tpl->tpl_vars['pt'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pt']->_loop = false;
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
	url		= "index.php?mod=productStockalarm&act=index";
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
		alertify.alert('生成采购订单成功',function(){
			window.location.reload();
		});
		/*
		if(data == 'noPower'){
			alertify.alert('您不是采购员,没有权限生成采购订单');
		}else if(data == 'success'){
			alertify.alert('生成采购订单成功',function(){
				window.location.reload();
			});
		}else{
			alertify.alert('生成采购订单失败'+data.msg);
		}
		*/
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
$("#updateCache1").click(function(){
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


$("#updateCache").click(function(){
	var data,url = "json.php?mod=Common&act=updateCache";
	$(this).html("更新中。。。");
	$(this).attr("disabled","disabled");
	data = getSkuList();
	$.post(url,{"data":data},function(rtn){
		console.log(rtn);
		window.location.reload();
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

$("#getSkuData").click(function(){
	$(this).html("更新中。。。");
	$(this).attr("disabled","disabled");
	var data,url = "json.php?mod=Common&act=getSkuData",purchaseUser;
	data = getSkuList();
	purchaseUser = $("#pcid  option:selected").text();
	$.post(url,{"data":data,"purchaseUser":purchaseUser},function(rtn){
		console.log(rtn);
		if(rtn == 1){
			window.location.reload();
		}
	});
});

$("#checkAlert").click(function(){
	var data,url = "json.php?mod=Common&act=calcAlert";
	$(this).html("更新中。。。");
	$(this).attr("disabled","disabled");
	data = getSkuList();
	$.post(url,{"data":data},function(rtn){
		console.log(rtn);
		window.location.reload();
	});
});

window.onkeyup = function(e) {
	if(e.keyCode == 13) {
		search();
	}
}


</script>
<?php }} ?>