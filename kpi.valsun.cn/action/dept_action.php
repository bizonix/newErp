<?php
require_once('../lib/user.php');
require_once('../lib/dept.php');
require_once('../lib/jobpower.php');

$user=new User();
echo '您上次登录系统时间：'.User::login('陈文辉','1',2).'<br/>';

$Jobpower=new Jobpower();
$Jobpower->showJobpower();

//$dept=new Dept();
//print_r(Dept::showDept());
//Dept::updateDept(array('dept_name' => '总经办','dept_principal'=>'陈文辉'),'dept_name = "总经办"'
//Dept::addDept(array('dept_name' => 'eBay销售部','dept_principal'=>'陈小霞'));
//print_r(Dept::showDept());
//Dept::deleteDept('dept_id=3');
//print_r(Dept::showDept());
?>