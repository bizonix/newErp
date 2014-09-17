<?php /* Smarty version Smarty-3.1.12, created on 2014-03-07 21:38:50
         compiled from "E:\erpNew\order.valsun.cn\html\template\v1\editField.htm" */ ?>
<?php /*%%SmartyHeaderCode:101555319cbeaa332b2-96869330%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5ddd54d7871810b7cba341349616447a463876b' => 
    array (
      0 => 'E:\\erpNew\\order.valsun.cn\\html\\template\\v1\\editField.htm',
      1 => 1393658410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '101555319cbeaa332b2-96869330',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'value' => 0,
    'orderData' => 0,
    'ostatus' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5319cbeab016f6_63337252',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5319cbeab016f6_63337252')) {function content_5319cbeab016f6_63337252($_smarty_tpl) {?><span>
<a style="margin:0;">操&nbsp;&nbsp;作</a>
<div id="u220" class="sub_menu" style=" z-index: 1009;">
<!-- Unnamed (表格) -->
<div id="u221" class="menuitem">

    <!-- Unnamed (菜单项) -->
    <!--<div id="u222" class="ax_cell">-->
    <!-- Unnamed () -->
    <!--<div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="applyexception(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
)">
            发货拦截
        </span>
        </p>
    </div>
    </div>-->
    
    <!-- Unnamed (菜单项) -->
    <!--<div id="u222" class="ax_cell">
    <!-- Unnamed ()
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="showPage(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,2)">
            查看
        </span>
        </p>
    </div>
    </div>-->
    
    <?php if ($_smarty_tpl->tpl_vars['ostatus']->value!=2){?>
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="editPage(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,1)">
            编辑
        </span>
        </p>
    </div>
    </div>
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="bestTransport(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
);">
            最优运费
        </span>
        </p>
    </div>
    </div>
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="transportFee(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
);">
            运费计算
        </span>
        </p>
    </div>
    </div>
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4">
        <span id="cache5" style="" onclick="expressDescription(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['orderData']->value['transportId'];?>
);">
            快递描述
        </span>
        </p>
    </div>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['ostatus']->value==900){?>
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="canceldeal(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,2)">
            废弃订单
        </span>
        </p>
    </div>
    </div>
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="canceldeal(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,3)">
            暂不寄
        </span>
        </p>
    </div>
    </div>
    <?php }?>
    
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['orderData']->value['platformId']==2||$_smarty_tpl->tpl_vars['orderData']->value['platformId']==3||$_smarty_tpl->tpl_vars['orderData']->value['platformId']==4||$_smarty_tpl->tpl_vars['orderData']->value['platformId']==9){?>
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="negativeFeedback(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
)">
            中差评
        </span>
        </p>
    </div>
    </div>
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="">
            退款登记
        </span>
        </p>
    </div>
    </div>
    <?php }?>
    <!-- Unnamed (菜单项) -->
    <!--<div id="u222" class="ax_cell">
    <!-- Unnamed ()
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="copyorder('<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
')">
            复制订单
        </span>
        </p>
    </div>
    </div>-->
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="resendorder(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
)">
            复制订单
        </span>
        </p>
    </div>
    </div>
    
    <!-- Unnamed (菜单项) -->
    <div id="u222" class="ax_cell">
    <!-- Unnamed () -->
    <div id="u223" class="text" style="top: 12px; ">
      <p id="cache4"  >
        <span id="cache5" style="" onclick="canceldeal(<?php echo $_smarty_tpl->tpl_vars['orderData']->value['id'];?>
,1)">
            取消交易
        </span>
        </p>
    </div>
    </div>

</div>
</div>
</span><?php }} ?>