<?php 
/*
 * message公用函数
 */

function str_rep($str){
    $str  = str_replace("'","&acute;",$str);
    $str  = str_replace("\"","&quot;",$str);
    return $str;
}