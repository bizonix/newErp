<?php

//获取变量配置信息
function C($name=null, $value=null) {
    static $_config = array();
    if(empty($name)) {
    	return $_config;
    }
    if(is_string($name)) {
        if(!strpos($name, '.')) {
            $name = strtolower($name);
            if(is_null($value)) {
                return isset($_config[$name]) ? $_config[$name] : null;
            }
            $_config[$name] = $value;
            return;
        }
        // 二维数组设置和获取支持
        $name 		= explode('.', $name);
        $name[0]   	= strtolower($name[0]);
        if(is_null($value)) {
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        }
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if(is_array($name)) {
        return $_config = array_merge($_config, array_change_key_case($name));
    }
    return null; 
}

