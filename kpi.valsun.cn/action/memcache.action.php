<?php
/********************************************
memcache 用例示范


// 设置值
$mc->set('word', 'hello world', 900);
 
// 取得值
echo $mc->get('word');
 
// 删除值
$mc->delete('word');
echo $mc->get('word');
 
$mc->set('counter', 1, 290000);
echo $mc->get('counter');
 
// 增加值
$mc->incr('counter');
$mc->incr('counter');
echo $mc->get('counter');
 
// 减少值
$mc->decr('counter');
echo $mc->get('counter');
 
// 按组删
$mc->flush(); 
 
// 减少值
$mc->decr('counter');
echo $mc->get('counter');
 
// 按组删
$mc->flush(); 
 */

class MemcacheAct extends Auth{
	
	//set(域名设置vhost到WEB_PATH/api -> api.xxx.com)
	//http://api.xxx.com/json.php?act=set&mod=memcache&jsonp=1&value=333333333
	function act_set(){
		$value	=	$_GET['value'];
		$cache	=	new Cache("group11");
		$cache->set("key1",$value,"60");
		return array();
	}
	
	//get
	////http://api.xxx.com/json.php?act=get&mod=memcache&jsonp=1
	function act_get(){
		$cache	=	new Cache("group11");
		$xx	=	$cache->get("key1");
		return array($xx);
	}	

}