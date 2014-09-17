<?php
/*
 * message统计
 */
class MessageStatisticsView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * messages统计页面
     */
    public function view_messageStatistics(){
//         echo 'system error!';exit;
        $starttime  = isset($_GET['starttime']) ? trim($_GET['starttime']) : '';
        $endtime    = isset($_GET['endtime']) ? trim($_GET['endtime']) : '';
        $this->smarty->assign('starttime', $starttime);
        $this->smarty->assign('endtime', $endtime);
        $wheresql = '';
        
        if (!empty($starttime)) {
        	$start_stamp = strtotime($starttime) ;
        } else {
            $start_stamp    = strtotime(date('Y-m-d'));
        }
        
        if (!empty($endtime)) {
            $end_stamp = strtotime($endtime)+86399 ;
            $wheresql .= ' and replytime < '.$end_stamp;
        } else {
            $end_stamp  = $start_stamp + 86399;
        }
        
        //$wheresql .= ' and replytime > '.$start_stamp;
        //$wheresql .= ' and replytime < '.$end_stamp;
        $wheresql .= ' AND replytime BETWEEN '.$start_stamp.' AND '.$end_stamp;
        /*获得文件夹列表*/
        $cat_obj    = new messagecategoryModel();
        $list       = $cat_obj->getAllCategoryInfoList('', 1);
        // print_r($list);exit;
        $catgroup   = array();
        foreach($list as $v){
            $catname    = $v['category_name'];
            $breakar    = explode('-', $catname);
            if(isset($breakar[1])){
               if(array_key_exists($breakar[1], $catgroup)){
                   $catgroup[$breakar[1]][] = $v['id'];
               } else {
                   $catgroup[$breakar[1]]   = array($v['id']);
               }
            }
        }
        
        $userlist = array (                                                             //客服部用户登陆名
                '程双双' 	=> 'chengshuangshua@sailvan.com',
                '陈君玉' 	=> 'chenjunyu@sailvan.com',
                '陈丽珍' 	=> 'chenlizhen@sailvan.com',
                '陈小婷' 	=> 'chenxiaoting@sailvan.com',
                '邓丽婷' 	=> 'dengliting@sailvan.com',
                '丁微越' 	=> 'dingweiyue@sailvan.com',
                '贺清'  		=> 'heqing@sailvan.com',
                '雷贤容' 	=> 'leixianrong@sailvan.com',
                '林秋娟' 	=> 'linqiujuan@sailvan.com',
                '刘娟'  		=> 'liujuan@sailvan.com',
                '罗小梨' 	=> 'luoxiaoli@sailvan.com',
                '裴野'  		=> 'peiye@sailvan.com',
                '孙冬华' 	=> 'sundonghua@sailvan.com',
                '韦江'  		=> 'weijiang@sailvan.com',
                '吴玉兰' 	=> 'wuyulan@sailvan.com',
                '张秀君' 	=> 'zhangxiujun@sailvan.com',
                '钟翠萍' 	=> 'zhongcuiping@sailvan.com',
                '何燕珊' 	=> 'heyanshan@sailvan.com',
                '赵志国' 	=> 'zhaozhiguo@sailvan.com' ,
                '曾盼'  		=>  'zengpan@sailvan.com',
                '林彬灵' 	=>'linbinling@sailvan.com',
                '刘艳菲' 	=> 'liuyanfei@sailvan.com',
                '毛诺莎' 	=> 'maonuosha@sailvan.com',
                '王成'  		=> 'wangcheng@sailvan.com',
                '张耿'  		=> 'zhanggeng@sailvan.com',
                '谢泽荣' 	=> 'xiezerong@sailvan.com',
                '祝筠楠' 	=> 'zhujunnan@sailvan.com',
                '李发刚' 	=> 'lifagang@sailvan.com',
                '刘程'  		=> 'liucheng@sailvan.com',
                '朱凤娟' 	=> 'zhufengjuan@sailvan.com',
                '谭兰芳' 	=> 'tanlanfang@sailvan.com',
                '张节'  		=> 'zhangjie@sailvan.com',
                '杨芸'  		=> 'yangyun@sailvan.com',
                '田宇'  		=> 'tianyu@sailvan.com',
                '陈燕玲' 	=> 'chenyanling@sailvan.com',
                '胡佳斌' 	=> 'hujiabin@sailvan.com',
                '刘飞云' 	=> 'liufeiyun@sailvan.com',
                '黄海珊' 	=> 'huanghaishan@sailvan.com',
                '黄婉仪' 	=> 'huangwanyi@sailvan.com',
				'肖小媚' 	=> 'xiaoxiaomei@sailvan.com',
				'黄桃花' 	=> 'huangtaohua@sailvan.com',
                '刘振婷' 	=> 'liuzhenting@sailvan.com',
                '潘贵朋' 	=> 'panguipeng@sailvan.com',
                '陈春漫' 	=> 'chenchunman@sailvan.com',
        		'陈鹿'   	=> 'chenlu@sailvan.com',
        		'李娜'   	=> 'lina2@sailvan.com',
        		'杨超'   	=> 'yangchao@sailvan.com',
        		'卢艳'   	=> 'luyan@sailvan.com', 
        		'胡莉娜' 	=> 'hulina@sailvan.com',
        		'朱芬' 		=> 'zhufen@sailvan.com',
        		'姚晓雯' 	=> 'yaoxiaowen@sailvan.com',
        		'李丰' 		=> 'lifeng@sailvan.com',
        		'王源' 		=> 'wangyuan@sailvan.com',
        		'朱小利' 	=> 'zhuxiaoli@sailvan.com',
        		'刘秋兰'  	=> 'liuqiulan@sailvan.com',
        		'陈秋香'  	=> 'chenqiuxiang@sailvan.com',
        	    '韩丹'   	=> 'handan@sailvan.com',
        		'邹波'   	=> 'zoubo@sailvan.com',
        		'陈阳'   	=> 'chenyang@sailvan.com',
        		'卿益'   	=> 'qingyi@sailvan.com',
        		'刘锦明'  	=> 'liujinming@sailvan.com',
        		'吴佳平'  	=> 'wujiaping@sailvan.com',
        		'李桂花'  	=> 'liguihua@sailvan.com',
        		'王剑华'  	=> 'wangjianhua@sailvan.com',
        		'向红梅'  	=> 'xianghongmei@sailvan.com',
        		'孟江南'  	=> 'mengjiangnan@sailvan.com',
        		'畅梦'   	=> 'changmeng@sailvan.com',
        		'段孝峰'  	=> 'duanxiaofeng@sailvan.com',
        		'肖小媚'  => 'xiaoxiaomei@sailvan.com',
        		'潘婷'   => 'panting@sailvan.com',
        		'朱红丹'  => 'zhuhongdan@sailvan.com',
        		'向红梅'  => 'xianghongmei@sailvan.com',
        		'李皖'   => 'lihuan@sailvan.com',
        		
        );
        
        $localUser_obj  = new GetLoacalUserModel();
        $name2id        = $localUser_obj->getUserId($userlist);
		//print_r($name2id);
        $idsql      = implode(',', $name2id);
        $sql_user   = "select replyuser_id,count(replyuser_id) as num from msg_message where replyuser_id in ($idsql) and ( classid !=415 $wheresql and status=2) 
                        or ( replytime is null and classid != 415 ) group by replyuser_id";
        $query_re   = mysql_query($sql_user);
        $userReply  = array();
        while($urow = mysql_fetch_assoc($query_re)){
            $userReply[$urow['replyuser_id']]    = $urow['num'];    
        }
        /*----- 生成统计信息 -----*/
        $sql    = "select classid, status, count(status) as num from msg_message where ( classid != 415 $wheresql ) or 
        ( replytime is null and classid != 415 )  group by classid , status";
        $result = array();
        $qre    = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qre)) {
            if (array_key_exists($row['classid'], $result)) {
                if (!array_key_exists($row['status'], $result[$row['classid']])) {
                    $result[$row['classid']][$row['status']]   = $row['num'];
                }
            } else {
                $result[$row['classid']]   = array($row['status']=>$row['num']);
            }
        }
        // print_r($result);exit;
        $finalresult    = array();
        foreach($catgroup as $cname=>$cat){
            $finalresult[$cname]    = array(0=>0, 1=>0, 2=>0, 3=>0);
            foreach($cat as $id){
                if (array_key_exists($id, $result)) {
                    $finalresult[$cname][0]    += $result[$id][0];
                    $finalresult[$cname][1]    += $result[$id][1];
                    $finalresult[$cname][2]    += $result[$id][2];
                    $finalresult[$cname][3]    += $result[$id][3];
                } 
            }
        }
		//print_r($finalresult);
        foreach ($finalresult as $k=>&$vx) {
            $total  = 0;
            foreach ($vx as $vy) {
                $total  += $vy;
            }
            $vx['4']    = $total;
            $vx['user'] = array_key_exists($k, $userlist) ? $userReply[$name2id[$k]] : 0;
            $vx['name'] = $k;
        }

        /*----- 处理排序 -----*/
        $noreply    = array();         //未回复
        $replyed    = array();         //已回复
        $replying   = array();         //回复中
        $markreply  = array();         //标记回复
        $total      = array();         //总数
        foreach ($finalresult as $key=>$item){
            $noreply[$key]      = isset($item[0]) ? $item[0] : 0;
            $replyed[$key]      = isset($item['user']) ? $item['user'] : 0;
            $replying[$key]     = isset($item[1]) ? $item[1] : 0;
            $markreply[$key]    = isset($item[3]) ? $item[3] : 0;
            $total[$key]        = isset($item[4]) ? $item[4] : 0;
        }
//         print_r($total);exit;
        $total_noreply          = array_sum($noreply);
        $total_replyed          = array_sum($replyed);
        $total_replying         = array_sum($replying);
        $total_markreply        = array_sum($markreply);
        $total_total            = array_sum($total);
        $this->smarty->assign('total_noreply', $total_noreply);
        $this->smarty->assign('total_replyed', $total_replyed);
        $this->smarty->assign('total_markreply', $total_markreply);
        $this->smarty->assign('total_replying', $total_replying);
        $this->smarty->assign('total_total', $total_total);
        
        $sort_no    = isset($_GET['srtn']) ? ($_GET['srtn'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //未回复
        $sort_h     = isset($_GET['srth']) ? ($_GET['srth'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //已回复
        $sort_i     = isset($_GET['srti']) ? ($_GET['srti'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //回复中
        $sort_m     = isset($_GET['srtm']) ? ($_GET['srtm'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //标记回复
        $sort_t     = isset($_GET['srtt']) ? ($_GET['srtt'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //总数
        if ($sort_no !== FALSE) {               //未回复排序
        	if ($sort_no == 'asc') {            //升序
        		asort($noreply);
        	} else {                            //降序
        	    arsort($noreply);
        	}
        	$this->smarty->assign('condition', 'sort_no');
        	$this->smarty->assign('sortindex', $noreply);
        } elseif ($sort_h !== FALSE){           //已回复
            if ($sort_h == 'asc') {
            	asort($replyed);
            } else {
                arsort($replyed);
            }
            $this->smarty->assign('condition', 'sort_h');
            $this->smarty->assign('sortindex', $replyed);
        } elseif ($sort_i !== FALSE){            //回复中
            if ($sort_i == 'asc') {
            	asort($replying);
            } else {
                arsort($replying);
            }
            $this->smarty->assign('condition', 'sort_i');
            $this->smarty->assign('sortindex', $replying);
        } elseif ($sort_m !== FALSE) {           //标记回复
            if ($sort_m == 'asc') {
            	asort($markreply);
            } else {
                arsort($markreply);
            }
            $this->smarty->assign('condition', 'sort_m');
            $this->smarty->assign('sortindex', $markreply);
        } elseif ($sort_t !== FALSE){            //总数量
            if ($sort_t == 'asc') {
            	asort($total);
            } else {
                arsort($total);
            }
            $this->smarty->assign('condition', 'sort_t');
            $this->smarty->assign('sortindex', $total);
        } else {                                //默认按未回复数量降序排列
            if ($sort_no == 'asc') {            //升序
                asort($noreply);
            } else {                            //降序
                arsort($noreply);
            }
            $this->smarty->assign('condition', 'sort_no');
            $this->smarty->assign('sortindex', $noreply);
        }
        //print_r($noreply);exit;
        $this->smarty->assign('sorttype', $sortype);
        /*----- 处理排序 -----*/
        
        $this->smarty->assign('sec_menue', 7);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=msgCategory&act=categoryList', 'title' => 'message系统'),
            array('url' => '', 'title' => 'ebay message统计'),
        );
        $this->smarty->assign('starttime', date('Y-m-d', $start_stamp));
        $this->smarty->assign('endtime', date('Y-m-d', $end_stamp));
        $this->smarty->assign('toplevel', 3);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('statistics', $finalresult);
        $this->smarty->assign('sec_menue', 1);
        $this->smarty->display('msgstatistics.htm');
    }

    /*
     * messages统计页面
     */
    public function view_messageStatisticsAli(){
        $starttime  = isset($_GET['starttime']) ? trim($_GET['starttime'])  : '';
        $endtime    = isset($_GET['endtime'])   ? trim($_GET['endtime'])    : '';
        $type       = isset($_GET['type'])        ? trim($_GET['type'])     : 'order';
        $wheresql = '';
        date_default_timezone_set('America/Los_Angeles');                           //转换成洛杉矶时间
        $start_stamp = strtotime($starttime) ;
        $end_stamp   = strtotime($endtime)+84600 ;
        date_default_timezone_set('Asia/Shanghai');
        if (!empty($starttime)) {
            $wheresql .= ' and createtimestamp > '.$start_stamp;
        }
        if (!empty($endtime)) {
            $wheresql .= ' and createtimestamp < '.$end_stamp;
        }
        $localuser      = new GetLoacalUserModel();
        $mysql_obj      = new MysqlModel();
        
        $cat_obj    = new messagecategoryModel();
        $list       = $cat_obj->getAllCategoryInfoList('', 2);
//         print_r($list);
        $userGroup  = array();
        foreach ($list as $lival){
            $cat_name   = trim($lival['category_name']);
            $nameBreak  = explode("--", $cat_name);
            $userName   = array_pop($nameBreak);
            if (array_key_exists($userName, $userGroup)) {
            	$userGroup[$userName][]    = $lival['id'];
            } else {
                $userGroup[$userName]       = array($lival['id']);
            }
        }
        $userName2loginName = array (
                '韩庆新' => 'hanqingxin@sailvan.com',
                '王丽娟' => 'wanglijuan@sailvan.com',
                '王玲'  => 'wangling@sailvan.com',
                '王铭'  => 'wangming@sailvan.com',
                '喻情'  => 'yuqing@sailvan.com',
                '黄海娣' =>'huanghaidi@sailvan.com',
                '袁岸'  => 'yuanan@sailvan.com',
                '潘小红' =>'panxiaohong@sailvan.com',
                '陈小燕' =>'chenxiaoyan@sailvan.com',
                '肖奇生' =>'xiaoqisheng@sailvan.com',
                '戴霞'  =>'daixia@sailvan.com',
                '温健玲' =>'wenjianling@sailvan.com',
                '肖庚莲'=>'xiaojinhua@sailvan.com',
        );
        
        $name2id        = $localuser->getUserId($userName2loginName);                                   //中文名称到系统用户id的映射关系
//         print_r($name2id);
        
        if ($type== 'order') {                                                                          //处理订单留言
            $sql    = "select fieldId , hasread,count(*) as num from msg_aliordermessage where role=1 and hasread in (0,1)
                       $wheresql group by fieldId, hasread
                    ";
//             echo $sql;exit;
            $resultList = $mysql_obj->getQueryResult($sql);
            $statistics = array();                                                                      //统计数据
            foreach ($userGroup as $username=>$ids){
                if (!array_key_exists($username, $statistics)){
                    $statistics[$username]   = array(0=>0, 1=>0);
                }
                foreach ($resultList as $row){
                    if (in_array($row['fieldId'], $ids)) {
                        if ($row['hasread']==0) {                                                       //累加未回复留言
                            $statistics[$username][0]  += $row['num'];
                        } elseif ($row['hasread']==1){                                                  //累加已读留言
                            $statistics[$username][1]  += $row['num'];
                        }
                    }
                }
            }
            
            foreach ($statistics as $key=>$item){
                $total  = array_sum($item);
                $statistics[$key]['total']  = $total;
            }
//             print_r($statistics);
            
            $replyedStatistics  = array();                                                              //已回复留言统计
            $userIdSql          = implode(', ', $name2id);
            $sqlre              = "select replyer, count(*) as num from msg_aliordermessage where role=1 and replyer in ($userIdSql) 
                                    and hasread=2 $wheresql  group by replyer";
//             echo $sqlre;exit;
            $replyResult        = $mysql_obj->getQueryResult($sqlre);
            $replyList          = array();
            foreach ($replyResult as $reRow){
                $replyList[$reRow['replyer']]   = $reRow['num'];
            }
            $totalNohandle      = 0;                                                                    //未处理的留言总数
            $totalRead          = 0;                                                                    //已读留言总数
            $totalReply         = 0;                                                                    //已回复留言总数
            foreach ($statistics as $r){
                $totalNohandle += $r[0];
                $totalRead     += $r[1];
                $totalReply    += $r[2];
            }
            
            global $dbConn;
            $accountGroup   = $this->getAliAccountGroup();
            $gropAmount     = array();
            foreach ($accountGroup as $key=>$group){
                $sql_str    = implode("', '", $group);
                $sql        = "select count(1) as num from msg_aliordermessage where receiverid in ('$sql_str') $wheresql";
                $row        = $dbConn->fetch_first($sql);
                $gropAmount[$key]   = $row['num'];
            }
            $filpNameId         = array_flip($name2id);
            foreach ($statistics as $user=>$rows){
                $userId = isset($name2id[$user]) ? $name2id[$user] : -1;
                if (isset($replyList[$userId])){
                    $statistics[$user][2]   = $replyList[$userId];
                } else {
                    $statistics[$user][2]   = 0;
                }
            }
            $thirdmenue = 1;
        } else {                                                                                        //处理站内信
            $sql = "select fieldId , hasread, count(*) as num from msg_alisitemessage where role=1 and hasread in (0,1)
                      $wheresql  group by fieldId, hasread
                    ";
            $resultList = $mysql_obj->getQueryResult ( $sql );
            $statistics = array ();                                                                     // 统计数据
            foreach ( $userGroup as $username => $ids ) {
                if (! array_key_exists ( $username, $statistics )) {
                    $statistics [$username] = array (0 => 0,1 => 0);
                }
                foreach ( $resultList as $row ) {
                    if (in_array ( $row ['fieldId'], $ids )) {
                        if ($row ['hasread'] == 0) {                                                        // 累加未回复留言
                            $statistics [$username] [0]     += $row ['num'];
                        } elseif ($row ['hasread'] == 1) {                                                  // 累加已读留言
                            $statistics [$username] [1]     += $row ['num'];
                        }
                    }
                }
            }
            
            foreach ($statistics as $key=>$item){
                $total  = array_sum($item);
                $statistics[$key]['total']  = $total;
            }
//             print_r($statistics);
            $replyedStatistics  = array ();                                                                 // 已回复留言统计
            $userIdSql          = implode ( ', ', $name2id );
            $sqlre              = "select replyer, count(*) as num from msg_alisitemessage where role=1 and replyer in ($userIdSql) 
                                  and hasread=2 $wheresql group by replyer";
//             echo $sqlre;exit;
            $replyResult        = $mysql_obj->getQueryResult ( $sqlre );
            $replyList          = array ();
            foreach ( $replyResult as $reRow ) {
                $replyList [$reRow ['replyer']] = $reRow ['num'];
            }
            $totalNohandle  = 0;                                                                            // 未处理的留言总数
            $totalRead      = 0;                                                                            // 已读留言总数
            $totalReply     = 0;                                                                            // 已回复留言总数
            foreach ( $statistics as $r ) {
                $totalNohandle  += $r [0];
                $totalRead      += $r [1];
                $totalReply     += $r [2];
            }
            global $dbConn;
            $accountGroup   = $this->getAliAccountGroup();
            $gropAmount     = array();    
            foreach ($accountGroup as $key=>$group){
                $sql_str    = implode("', '", $group);
                $sql        = "select count(1) as num from msg_alisitemessage where receiverid in ('$sql_str') $wheresql";
                $row        = $dbConn->fetch_first($sql);
                $gropAmount[$key]   = $row['num'];
            }
            
//             print_r($gropAmount);exit;
            $filpNameId         = array_flip($name2id);
//             print_r($filpNameId);
            foreach ( $statistics as $user => $rows ) {
                $userId = isset ( $name2id [$user] ) ? $name2id [$user] : - 1;
                if (isset ( $replyList [$userId] )) {
                    $statistics [$user] [2] = $replyList [$userId];
                } else {
                    $statistics [$user] [2] = 0;
                }
            }
//             print_r($statistics);exit;
            $thirdmenue = 2;
        }
//         exit;
//         print_r($data);exit;
        $this->smarty->assign('third_menue', $thirdmenue);
        $this->smarty->assign('starttime', $starttime);
        $this->smarty->assign('endtime', $endtime);
        
        $this->smarty->assign('totalNhandle', $totalNohandle);
        $this->smarty->assign('totalread', $totalRead);
        $this->smarty->assign('totalreply', $totalReply);
        $this->smarty->assign('type', $type);
        
        $this->smarty->assign('toplevel', 3);
//         $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('statistics', $statistics);
        $this->smarty->assign('statistics_2', $gropAmount);
        $this->smarty->assign('sec_menue', 2);
        $this->smarty->display('msgstatisticsAliorder.htm');
    }
    
    /*
     * 速卖通账号分组
     */
    public function getAliAccountGroup(){
        return array(
        	'3acyber'      => array('3acyber', 'cn1000268236'),
            '5-Season'     => array('cn1001377688', 'cn1001424576'),
            'acitylife'    => array('cn1510513243', 'cn1510515579'),
            'Babyhouse'    => array('cn1500053754', 'cn1500053764'),
            'bagfashion'   => array('bagfashion789', 'cn1000983826'),
            'beauty365'    => array('beauty365', 'cn1000960806'),
            'caracc88'     => array('caracc88', 'cn1000983412'),
            'centermall'   => array('cn1510509744', 'cn1510509429'),
            'championacc'  => array('cn1501578269', 'cn1501578304'),
            'citymiss'     => array('cn1510309914', 'cn1510304665'),
            'digitallife'  => array('cn1501595496', 'cn1501595926'),
            'E-Global'     => array('cn1000616054'),
            'Etime'        => array('cn1501637888', 'cn1501638006'),
            'etrademart'   => array('cn1510506505', 'cn1510509503'),
            'fashion deal' => array('cn1001379555', 'cn1001656836'),
            'fashion queen'=> array('cn1001718385', 'cn1001718610'),
            'fashionzone'  => array('cn1500152269', 'cn1500152370'),
            'finejo'       => array('szfinejo', 'cn1001392417'),
            'homestyle'    => array('cn1501534536', 'cn1501540493'),
            'istore'       => array('cn1500439632', 'cn1500439756'),
            'King&Qeen'    => array('cn1500688658', 'cn1500688776'),
            'ladyzone'     => array('cn1500514393', 'cn1500514645'),
            'lovely baby'  => array('cn1001315312', 'cn1001428059'),
            'Myzone'       => array('cn1501287406', 'cn1501287427'),
            'Pretty hair'  => array('cn1000999030'),
            'shingstar'    => array('cn1001739214', 'cn1001739224'),
            'shoesacc'     => array('cn1500225927', 'cn1500226033'),
            'sunshine'     => array('cn1001711552', 'cn1001711574'),
            'Sunweb'       => array('szsunweb', 'cn1000421358'),
            'superdeal'    => array('cn1500293372', 'cn1500293467'),
            'viphouse'     => array('cn1510517588', 'cn1510514024'),
            'womenworld'   => array('cn1501288484', 'cn1501288533'),
            'zeagoo360'    => array('cn1500439946', 'cn1500440054'),
            'citywheel'    => array('cn1510886356', 'cn1510891016'),
            'mangocart'    => array('cn1510893085', 'cn1510895038'),
            'sailvan hour' => array('cn1510930486', 'cn1510890054'),
            'Angel city'   => array('cn1510893199', 'cn1510893515'),
        	'MVP'          => array('cn1501655651', 'cn1501655558'),
        	'HappyMall365' => array('cn1501657451', 'cn1501657572'),
        	'sailvanspace' => array('cn1511272624', 'cn1511324723'),
        	'TheTown'      => array('cn1511324726', 'cn1511256103'),
        	'fashionmall'  => array('cn1501686262', 'cn1501686293'),
        	'ThePowerMall' => array('cn1501638127', 'cn1501642501'),
        	'bestshopping' => array('cn1501654562', 'cn1501654678'),
        	'One-Power'    => array('cn1501656181', 'cn1501656206'),
        	'bestoffer365' => array('cn1501656498', 'cn1501656494'),
        	'AliexpressVK' => array('cn1501657123', 'cn1501657160'),
        	'svbest'       => array('cn1511448887'),
            'E-town'       => array('cn1501654797', 'cn1501654763'),
            'fivestar'     => array('cn1511399758'),
            'fancyland'     => array('cn1511397003'),
            'U-LikeStation' => array('cn1511399648'),
            'HappyGo'       => array('cn1511387623'),
            'dreamhouse'    => array('cn1511445991'),
        	'bestoffer'    		=> array('cn1511389571'),
        	'newfashion'    	=> array('cn1511396627'),
            'sweethouse'    	=> array('cn1511400702'),
        	'FashionShow'    	=> array('cn1511387707'),
        	'ShoppingHeaven'    => array('cn1511434615'),
        	'8AngelStreet'    	=> array('cn1511445674'),
        	'SweetTown'     	=> array('cn1511422672'),
        );
    }
}
