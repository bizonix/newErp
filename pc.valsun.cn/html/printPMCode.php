<?php
/**
* @name printlabelForEB_LT.php
* by zqt
* 20131119
* 标签打印-50*24
**/

$data = $_GET['pmName'];
if(empty($data)){
    echo '条码有误';
    exit;
}
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="./js/jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="./js/jquery/jquery-barcode.min.js"></script>
<style media="print">
.noprint { display: none; }
</style>

<style type="text/css">
<!--
body {
margin-top: 0px;
}
.STYLE5 {font-size: 10px}
-->
</style>


<table border="0" cellpadding="0" cellspacing="0" style="border:1px dashed #999999; height:25mm; width:50mm;overflow: hidden;">
    <tr>
        <td valign="top">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
            	<tr>
                	<td align="center" style="font-size: 18px; font-weight: bold;padding-top: 5px;"><?php echo $data; ?></td>
                </tr>
                <tr>
                	<td><img src="barcode128.class.php?data=<?php echo $data; ?>" alt="" width="180" height="50"/></td>
                </tr>
			</table>
		</td>
	</tr>
</table>